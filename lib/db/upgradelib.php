<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Upgrade helper functions
 *
 * This file is used for special upgrade functions - for example groups and gradebook.
 * These functions must use SQL and database related functions only- no other Moodle API,
 * because it might depend on db structures that are not yet present during upgrade.
 * (Do not use functions from accesslib.php, grades classes or group functions at all!)
 *
 * @package   core_install
 * @category  upgrade
 * @copyright 2007 Petr Skoda (http://skodak.org)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Returns all non-view and non-temp tables with sane names.
 * Prints list of non-supported tables using $OUTPUT->notification()
 *
 * @return array
 */
function upgrade_mysql_get_supported_tables() {
    global $OUTPUT, $DB;

    $tables = array();
    $patprefix = str_replace('_', '\\_', $DB->get_prefix());
    $pregprefix = preg_quote($DB->get_prefix(), '/');

    $sql = "SHOW FULL TABLES LIKE '$patprefix%'";
    $rs = $DB->get_recordset_sql($sql);
    foreach ($rs as $record) {
        $record = array_change_key_case((array)$record, CASE_LOWER);
        $type = $record['table_type'];
        unset($record['table_type']);
        $fullname = array_shift($record);

        if ($pregprefix === '') {
            $name = $fullname;
        } else {
            $count = null;
            $name = preg_replace("/^$pregprefix/", '', $fullname, -1, $count);
            if ($count !== 1) {
                continue;
            }
        }

        if (!preg_match("/^[a-z][a-z0-9_]*$/", $name)) {
            echo $OUTPUT->notification("Database table with invalid name '$fullname' detected, skipping.", 'notifyproblem');
            continue;
        }
        if ($type === 'VIEW') {
            echo $OUTPUT->notification("Unsupported database table view '$fullname' detected, skipping.", 'notifyproblem');
            continue;
        }
        $tables[$name] = $name;
    }
    $rs->close();

    return $tables;
}

/**
 * Using data for a single course-module that has groupmembersonly enabled,
 * returns the new availability value that incorporates the correct
 * groupmembersonly option.
 *
 * Included as a function so that it can be shared between upgrade and restore,
 * and unit-tested.
 *
 * @param int $groupingid Grouping id for the course-module (0 if none)
 * @param string $availability Availability JSON data for the module (null if none)
 * @return string New value for availability for the module
 */
function upgrade_group_members_only($groupingid, $availability) {
    // Work out the new JSON object representing this option.
    if ($groupingid) {
        // Require specific grouping.
        $condition = (object)array('type' => 'grouping', 'id' => (int)$groupingid);
    } else {
        // No grouping specified, so require membership of any group.
        $condition = (object)array('type' => 'group');
    }

    if (is_null($availability)) {
        // If there are no conditions using the new API then just set it.
        $tree = (object)array('op' => '&', 'c' => array($condition), 'showc' => array(false));
    } else {
        // There are existing conditions.
        $tree = json_decode($availability);
        switch ($tree->op) {
            case '&' :
                // For & conditions we can just add this one.
                $tree->c[] = $condition;
                $tree->showc[] = false;
                break;
            case '!|' :
                // For 'not or' conditions we can add this one
                // but negated.
                $tree->c[] = (object)array('op' => '!&', 'c' => array($condition));
                $tree->showc[] = false;
                break;
            default:
                // For the other two (OR and NOT AND) we have to add
                // an extra level to the tree.
                $tree = (object)array('op' => '&', 'c' => array($tree, $condition),
                        'showc' => array($tree->show, false));
                // Inner trees do not have a show option, so remove it.
                unset($tree->c[0]->show);
                break;
        }
    }

    return json_encode($tree);
}

/**
 * Marks all courses with changes in extra credit weight calculation
 *
 * Used during upgrade and in course restore process
 *
 * This upgrade script is needed because we changed the algorithm for calculating the automatic weights of extra
 * credit items and want to prevent changes in the existing student grades.
 *
 * @param int $onlycourseid
 */
function upgrade_extra_credit_weightoverride($onlycourseid = 0) {
    global $DB;

    // Find all courses that have categories in Natural aggregation method where there is at least one extra credit
    // item and at least one item with overridden weight.
    $courses = $DB->get_fieldset_sql(
        "SELECT DISTINCT gc.courseid
          FROM {grade_categories} gc
          INNER JOIN {grade_items} gi ON gc.id = gi.categoryid AND gi.weightoverride = :weightoverriden
          INNER JOIN {grade_items} gie ON gc.id = gie.categoryid AND gie.aggregationcoef = :extracredit
          WHERE gc.aggregation = :naturalaggmethod" . ($onlycourseid ? " AND gc.courseid = :onlycourseid" : ''),
        array('naturalaggmethod' => 13,
            'weightoverriden' => 1,
            'extracredit' => 1,
            'onlycourseid' => $onlycourseid,
        )
    );
    foreach ($courses as $courseid) {
        $gradebookfreeze = get_config('core', 'gradebook_calculations_freeze_' . $courseid);
        if (!$gradebookfreeze) {
            set_config('gradebook_calculations_freeze_' . $courseid, 20150619);
        }
    }
}

/**
 * Marks all courses that require calculated grade items be updated.
 *
 * Used during upgrade and in course restore process.
 *
 * This upgrade script is needed because the calculated grade items were stuck with a maximum of 100 and could be changed.
 * This flags the courses that are affected and the grade book is frozen to retain grade integrity.
 *
 * @param int $courseid Specify a course ID to run this script on just one course.
 */
function upgrade_calculated_grade_items($courseid = null) {
    global $DB, $CFG;

    $affectedcourses = array();
    $possiblecourseids = array();
    $params = array();
    $singlecoursesql = '';
    if (isset($courseid)) {
        $singlecoursesql = "AND ns.id = :courseid";
        $params['courseid'] = $courseid;
    }
    $siteminmaxtouse = 1;
    if (isset($CFG->grade_minmaxtouse)) {
        $siteminmaxtouse = $CFG->grade_minmaxtouse;
    }
    $courseidsql = "SELECT ns.id
                      FROM (
                        SELECT c.id, coalesce(" . $DB->sql_compare_text('gs.value') . ", :siteminmax) AS gradevalue
                          FROM {course} c
                          LEFT JOIN {grade_settings} gs
                            ON c.id = gs.courseid
                           AND ((gs.name = 'minmaxtouse' AND " . $DB->sql_compare_text('gs.value') . " = '2'))
                        ) ns
                    WHERE " . $DB->sql_compare_text('ns.gradevalue') . " = '2' $singlecoursesql";
    $params['siteminmax'] = $siteminmaxtouse;
    $courses = $DB->get_records_sql($courseidsql, $params);
    foreach ($courses as $course) {
        $possiblecourseids[$course->id] = $course->id;
    }

    if (!empty($possiblecourseids)) {
        list($sql, $params) = $DB->get_in_or_equal($possiblecourseids);
        // A calculated grade item grade min != 0 and grade max != 100 and the course setting is set to
        // "Initial min and max grades".
        $coursesql = "SELECT DISTINCT courseid
                        FROM {grade_items}
                       WHERE calculation IS NOT NULL
                         AND itemtype = 'manual'
                         AND (grademax <> 100 OR grademin <> 0)
                         AND courseid $sql";
        $affectedcourses = $DB->get_records_sql($coursesql, $params);
    }

    // Check for second type of affected courses.
    // If we already have the courseid parameter set in the affectedcourses then there is no need to run through this section.
    if (!isset($courseid) || !in_array($courseid, $affectedcourses)) {
        $singlecoursesql = '';
        $params = array();
        if (isset($courseid)) {
            $singlecoursesql = "AND courseid = :courseid";
            $params['courseid'] = $courseid;
        }
        $nestedsql = "SELECT id
                        FROM {grade_items}
                       WHERE itemtype = 'category'
                         AND calculation IS NOT NULL $singlecoursesql";
        $calculatedgradecategories = $DB->get_records_sql($nestedsql, $params);
        $categoryids = array();
        foreach ($calculatedgradecategories as $key => $gradecategory) {
            $categoryids[$key] = $gradecategory->id;
        }

        if (!empty($categoryids)) {
            list($sql, $params) = $DB->get_in_or_equal($categoryids);
            // A category with a calculation where the raw grade min and the raw grade max don't match the grade min and grade max
            // for the category.
            $coursesql = "SELECT DISTINCT gi.courseid
                            FROM {grade_grades} gg, {grade_items} gi
                           WHERE gi.id = gg.itemid
                             AND (gg.rawgrademax <> gi.grademax OR gg.rawgrademin <> gi.grademin)
                             AND gi.id $sql";
            $additionalcourses = $DB->get_records_sql($coursesql, $params);
            foreach ($additionalcourses as $key => $additionalcourse) {
                if (!array_key_exists($key, $affectedcourses)) {
                    $affectedcourses[$key] = $additionalcourse;
                }
            }
        }
    }

    foreach ($affectedcourses as $affectedcourseid) {
        if (isset($CFG->upgrade_calculatedgradeitemsonlyregrade) && !($courseid)) {
            $DB->set_field('grade_items', 'needsupdate', 1, array('courseid' => $affectedcourseid->courseid));
        } else {
            // Check to see if the gradebook freeze is already in affect.
            $gradebookfreeze = get_config('core', 'gradebook_calculations_freeze_' . $affectedcourseid->courseid);
            if (!$gradebookfreeze) {
                set_config('gradebook_calculations_freeze_' . $affectedcourseid->courseid, 20150627);
            }
        }
    }
}

/**
 * This function creates a default separated/connected scale
 * so there's something in the database.
 *
 * @global object
 * @return void
 */
function make_default_scale() {
    global $DB;

    $defaultscale = new stdClass();
    $defaultscale->courseid = 0;
    $defaultscale->userid = 0;
    $defaultscale->name  = get_string('separateandconnected');
    $defaultscale->description = get_string('separateandconnectedinfo');
    $defaultscale->scale = get_string('separateandconnected1') . ',' .
        get_string('separateandconnected2') . ',' .
        get_string('separateandconnected3');
    $defaultscale->timemodified = time();

    $defaultscale->id = $DB->insert_record('scale', $defaultscale);
    return $defaultscale;
}


/**
 * Create another default scale.
 *
 * @param int $oldversion
 * @return bool always true
 */
function make_competence_scale() {
    global $DB;

    $defaultscale = new stdClass();
    $defaultscale->courseid = 0;
    $defaultscale->userid = 0;
    $defaultscale->name  = get_string('defaultcompetencescale');
    $defaultscale->description = get_string('defaultcompetencescaledesc');
    $defaultscale->scale = get_string('defaultcompetencescalenotproficient').','.
                           get_string('defaultcompetencescaleproficient');
    $defaultscale->timemodified = time();

    $defaultscale->id = $DB->insert_record('scale', $defaultscale);
    return $defaultscale;
}

/**
 * Marks all courses that require rounded grade items be updated.
 *
 * Used during upgrade and in course restore process.
 *
 * This upgrade script is needed because it has been decided that if a grade is rounded up, and it will changed a letter
 * grade or satisfy a course completion grade criteria, then it should be set as so, and the letter will be awarded and or
 * the course completion grade will be awarded.
 *
 * @param int $courseid Specify a course ID to run this script on just one course.
 */
function upgrade_course_letter_boundary($courseid = null) {
    global $DB, $CFG;

    $coursesql = '';
    $params = array('contextlevel' => CONTEXT_COURSE);
    if (!empty($courseid)) {
        $coursesql = 'AND c.id = :courseid';
        $params['courseid'] = $courseid;
    }

    // Check to see if the system letter boundaries are borked.
    $systemcontext = context_system::instance();
    $systemneedsfreeze = upgrade_letter_boundary_needs_freeze($systemcontext);

    // Check the setting for showing the letter grade in a column (default is false).
    $usergradelettercolumnsetting = 0;
    if (isset($CFG->grade_report_user_showlettergrade)) {
        $usergradelettercolumnsetting = (int)$CFG->grade_report_user_showlettergrade;
    }
    $lettercolumnsql = '';
    if ($usergradelettercolumnsetting) {
        // The system default is to show a column with letters (and the course uses the defaults).
        $lettercolumnsql = '(gss.value is NULL OR ' . $DB->sql_compare_text('gss.value') .  ' <> \'0\')';
    } else {
        // The course displays a column with letters.
        $lettercolumnsql = $DB->sql_compare_text('gss.value') .  ' = \'1\'';
    }

    // 3, 13, 23, 31, and 32 are the grade display types that incorporate showing letters. See lib/grade/constants/php.
    $systemusesletters = (int) (isset($CFG->grade_displaytype) && in_array($CFG->grade_displaytype, array(3, 13, 23, 31, 32)));
    $systemletters = $systemusesletters || $usergradelettercolumnsetting;

    $contextselect = context_helper::get_preload_record_columns_sql('ctx');

    if ($systemletters && $systemneedsfreeze) {
        // Select courses with no grade setting for display and a grade item that is using the default display,
        // but have not altered the course letter boundary configuration. These courses are definitely affected.

        $sql = "SELECT DISTINCT c.id AS courseid
                  FROM {course} c
                  JOIN {grade_items} gi ON c.id = gi.courseid
                  JOIN {context} ctx ON ctx.instanceid = c.id AND ctx.contextlevel = :contextlevel
             LEFT JOIN {grade_settings} gs ON gs.courseid = c.id AND gs.name = 'displaytype'
             LEFT JOIN {grade_settings} gss ON gss.courseid = c.id AND gss.name = 'report_user_showlettergrade'
             LEFT JOIN {grade_letters} gl ON gl.contextid = ctx.id
                 WHERE gi.display = 0
                 AND ((gs.value is NULL)
                      AND ($lettercolumnsql))
                 AND gl.id is NULL $coursesql";
        $affectedcourseids = $DB->get_recordset_sql($sql, $params);
        foreach ($affectedcourseids as $courseid) {
            set_config('gradebook_calculations_freeze_' . $courseid->courseid, 20160518);
        }
        $affectedcourseids->close();
    }

    // If the system letter boundary is okay proceed to check grade item and course grade display settings.
    $sql = "SELECT DISTINCT c.id AS courseid, $contextselect
              FROM {course} c
              JOIN {context} ctx ON ctx.instanceid = c.id AND ctx.contextlevel = :contextlevel
              JOIN {grade_items} gi ON c.id = gi.courseid
         LEFT JOIN {grade_settings} gs ON c.id = gs.courseid AND gs.name = 'displaytype'
         LEFT JOIN {grade_settings} gss ON gss.courseid = c.id AND gss.name = 'report_user_showlettergrade'
             WHERE
                (
                    -- A grade item is using letters
                    (gi.display IN (3, 13, 23, 31, 32))
                    -- OR the course is using letters
                    OR (" . $DB->sql_compare_text('gs.value') . " IN ('3', '13', '23', '31', '32')
                        -- OR the course using the system default which is letters
                        OR (gs.value IS NULL AND $systemusesletters = 1)
                    )
                    OR ($lettercolumnsql)
                )
                -- AND the course matches
                $coursesql";

    $potentialcourses = $DB->get_recordset_sql($sql, $params);

    foreach ($potentialcourses as $value) {
        $gradebookfreeze = 'gradebook_calculations_freeze_' . $value->courseid;

        // Check also if this course id has already been frozen.
        // If we already have this course ID then move on to the next record.
        if (!property_exists($CFG, $gradebookfreeze)) {
            // Check for 57 letter grade issue.
            context_helper::preload_from_record($value);
            $coursecontext = context_course::instance($value->courseid);
            if (upgrade_letter_boundary_needs_freeze($coursecontext)) {
                // We have a course with a possible score standardisation problem. Flag for freeze.
                // Flag this course as being frozen.
                set_config('gradebook_calculations_freeze_' . $value->courseid, 20160518);
            }
        }
    }
    $potentialcourses->close();
}

/**
 * Checks the letter boundary of the provided context to see if it needs freezing.
 * Each letter boundary is tested to see if receiving that boundary number will
 * result in achieving the cosponsoring letter.
 *
 * @param object $context Context object
 * @return bool if the letter boundary for this context should be frozen.
 */
function upgrade_letter_boundary_needs_freeze($context) {
    global $DB;

    $contexts = $context->get_parent_context_ids();
    array_unshift($contexts, $context->id);

    foreach ($contexts as $ctxid) {

        $letters = $DB->get_records_menu('grade_letters', array('contextid' => $ctxid), 'lowerboundary DESC',
                'lowerboundary, letter');

        if (!empty($letters)) {
            foreach ($letters as $boundary => $notused) {
                $standardisedboundary = upgrade_standardise_score($boundary, 0, 100, 0, 100);
                if ($standardisedboundary < $boundary) {
                    return true;
                }
            }
            // We found letters but we have no boundary problem.
            return false;
        }
    }
    return false;
}

/**
 * Given a float value situated between a source minimum and a source maximum, converts it to the
 * corresponding value situated between a target minimum and a target maximum. Thanks to Darlene
 * for the formula :-)
 *
 * @param float $rawgrade
 * @param float $sourcemin
 * @param float $sourcemax
 * @param float $targetmin
 * @param float $targetmax
 * @return float Converted value
 */
function upgrade_standardise_score($rawgrade, $sourcemin, $sourcemax, $targetmin, $targetmax) {
    if (is_null($rawgrade)) {
        return null;
    }

    if ($sourcemax == $sourcemin or $targetmin == $targetmax) {
        // Prevent division by 0.
        return $targetmax;
    }

    $factor = ($rawgrade - $sourcemin) / ($sourcemax - $sourcemin);
    $diff = $targetmax - $targetmin;
    $standardisedvalue = $factor * $diff + $targetmin;
    return $standardisedvalue;
}

/**
 * Provides a way to check and update a serialized string that uses the deprecated object class.
 *
 * @param  string $serializeddata Serialized string which may contain the now deprecated object.
 * @return array Returns an array where the first variable is a bool with a status of whether the initial data was changed
 * or not. The second variable is the said data.
 */
function upgrade_fix_serialized_objects($serializeddata) {
    $updated = false;
    if (strpos($serializeddata, ":6:\"object") !== false) {
        $serializeddata = str_replace(":6:\"object", ":8:\"stdClass", $serializeddata);
        $updated = true;
    }
    return [$updated, $serializeddata];
}

/**
 * Deletes file records which have their repository deleted.
 *
 */
function upgrade_delete_orphaned_file_records() {
    global $DB;

    $sql = "SELECT f.id, f.contextid, f.component, f.filearea, f.itemid, fr.id AS referencefileid
              FROM {files} f
              JOIN {files_reference} fr ON f.referencefileid = fr.id
         LEFT JOIN {repository_instances} ri ON fr.repositoryid = ri.id
             WHERE ri.id IS NULL";

    $deletedfiles = $DB->get_recordset_sql($sql);

    $deletedfileids = array();

    $fs = get_file_storage();
    foreach ($deletedfiles as $deletedfile) {
        $fs->delete_area_files($deletedfile->contextid, $deletedfile->component, $deletedfile->filearea, $deletedfile->itemid);
        $deletedfileids[] = $deletedfile->referencefileid;
    }
    $deletedfiles->close();

    $DB->delete_records_list('files_reference', 'id', $deletedfileids);
}

/**
 * Upgrade core licenses shipped with Moodle.
 */
function upgrade_core_licenses() {
    global $CFG, $DB;

    $expectedlicenses = json_decode(file_get_contents($CFG->dirroot . '/lib/licenses.json'))->licenses;
    if (!is_array($expectedlicenses)) {
        $expectedlicenses = [];
    }
    $corelicenses = $DB->get_records('license', ['custom' => 0]);

    // Disable core licenses which are no longer current.
    $todisable = array_diff(
        array_map(fn ($license) => $license->shortname, $corelicenses),
        array_map(fn ($license) => $license->shortname, $expectedlicenses),
    );

    // Disable any old *core* license that does not exist in the licenses.json file.
    if (count($todisable)) {
        [$where, $params] = $DB->get_in_or_equal($todisable, SQL_PARAMS_NAMED);
        $DB->set_field_select(
            'license',
            'enabled',
            0,
            "shortname {$where}",
            $params
        );
    }

    // Add any new licenses.
    foreach ($expectedlicenses as $expectedlicense) {
        if (!$expectedlicense->enabled) {
            // Skip any license which is no longer enabled.
            continue;
        }
        if (!$DB->record_exists('license', ['shortname' => $expectedlicense->shortname])) {
            // If the license replaces an older one, check whether this old license was enabled or not.
            $isreplacement = false;
            foreach (array_reverse($expectedlicense->replaces ?? []) as $item) {
                foreach ($corelicenses as $corelicense) {
                    if ($corelicense->shortname === $item) {
                        $expectedlicense->enabled = $corelicense->enabled;
                        // Also, keep the old sort order.
                        $expectedlicense->sortorder = $corelicense->sortorder * 100;
                        $isreplacement = true;
                        break 2;
                    }
                }
            }
            if (!isset($CFG->upgraderunning) || during_initial_install() || $isreplacement) {
                // Only install missing core licenses if not upgrading or during initial installation.
                $DB->insert_record('license', $expectedlicense);
            }
        }
    }

    // Add/renumber sortorder to all licenses.
    $licenses = $DB->get_records('license', null, 'sortorder');
    $sortorder = 1;
    foreach ($licenses as $license) {
        $license->sortorder = $sortorder++;
        $DB->update_record('license', $license);
    }

    // Set the license config values, used by file repository for rendering licenses at front end.
    $activelicenses = $DB->get_records_menu('license', ['enabled' => 1], 'id', 'id, shortname');
    set_config('licenses', implode(',', $activelicenses));

    $sitedefaultlicense = get_config('', 'sitedefaultlicense');
    if (empty($sitedefaultlicense) || !in_array($sitedefaultlicense, $activelicenses)) {
        set_config('sitedefaultlicense', reset($activelicenses));
    }
}

/**
 * Detects if the site may need to get the calendar events fixed or no. With optional output.
 *
 * @param bool $output true if the function must output information, false if not.
 * @return bool true if the site needs to run the fixes, false if not.
 */
function upgrade_calendar_site_status(bool $output = true): bool {
    global $DB;

    // List of upgrade steps where the bug happened.
    $badsteps = [
        '3.9.5'   => '2020061504.08',
        '3.10.2'  => '2020110901.09',
        '3.11dev' => '2021022600.02',
        '4.0dev'  => '2021052500.65',
    ];

    // List of upgrade steps that ran the fixer.
    $fixsteps = [
        '3.9.6+'  => '2020061506.05',
        '3.10.3+' => '2020110903.05',
        '3.11dev' => '2021042100.02',
        '4.0dev'  => '2021052500.85',
    ];

    $targetsteps = array_merge(array_values($badsteps), array_values( $fixsteps));
    list($insql, $inparams) = $DB->get_in_or_equal($targetsteps);
    $foundsteps = $DB->get_fieldset_sql("
        SELECT DISTINCT version
          FROM {upgrade_log}
         WHERE plugin = 'core'
           AND version " . $insql . "
      ORDER BY version", $inparams);

    // Analyse the found steps, to decide if the site needs upgrading or no.
    $badfound = false;
    $fixfound = false;
    foreach ($foundsteps as $foundstep) {
        $badfound = $badfound ?: array_search($foundstep, $badsteps, true);
        $fixfound = $fixfound ?: array_search($foundstep, $fixsteps, true);
    }
    $needsfix = $badfound && !$fixfound;

    // Let's output some textual information if required to.
    if ($output) {
        mtrace("");
        if ($badfound) {
            mtrace("This site has executed the problematic upgrade step {$badsteps[$badfound]} present in {$badfound}.");
        } else {
            mtrace("Problematic upgrade steps were NOT found, site should be safe.");
        }
        if ($fixfound) {
            mtrace("This site has executed the fix upgrade step {$fixsteps[$fixfound]} present in {$fixfound}.");
        } else {
            mtrace("Fix upgrade steps were NOT found.");
        }
        mtrace("");
        if ($needsfix) {
            mtrace("This site NEEDS to run the calendar events fix!");
            mtrace('');
            mtrace("You can use this CLI tool or upgrade to a version of Moodle that includes");
            mtrace("the fix and will be executed as part of the normal upgrade procedure.");
            mtrace("The following versions or up are known candidates to upgrade to:");
            foreach ($fixsteps as $key => $value) {
                mtrace("  - {$key}: {$value}");
            }
            mtrace("");
        }
    }
    return $needsfix;
}

/**
 * Detects the calendar events needing to be fixed. With optional output.
 *
 * @param bool $output true if the function must output information, false if not.
 * @return stdClass[] an array of event types (as keys) with total and bad counters, plus sql to retrieve them.
 */
function upgrade_calendar_events_status(bool $output = true): array {
    global $DB;

    // Calculate the list of standard (core) activity plugins.
    $plugins = core_plugin_manager::standard_plugins_list('mod');
    $coremodules = "modulename IN ('" . implode("', '", $plugins) . "')";

    // Some query parts go here.
    $brokenevents = "(userid = 0 AND (eventtype <> 'user' OR priority <> 0))"; // From the original bad upgrade step.
    $standardevents = "(eventtype IN ('site', 'category', 'course', 'group', 'user') AND subscriptionid IS NULL)";
    $subscriptionevents = "(subscriptionid IS NOT NULL)";
    $overrideevents = "({$coremodules} AND priority IS NOT NULL)";
    $actionevents = "({$coremodules} AND instance > 0 and priority IS NULL)";
    $otherevents = "(NOT ({$standardevents} OR {$subscriptionevents} OR {$overrideevents} OR {$actionevents}))";

    // Detailed query template.
    $detailstemplate = "
        SELECT ##group## AS groupname, COUNT(1) AS count
          FROM {event}
         WHERE ##groupconditions##
      GROUP BY ##group##";

    // Count total and potentially broken events.
    $total = $DB->count_records_select('event', '');
    $totalbadsql = $brokenevents;
    $totalbad = $DB->count_records_select('event', $totalbadsql);

    // Standard events.
    $standard = $DB->count_records_select('event', $standardevents);
    $standardbadsql = "{$brokenevents} AND {$standardevents}";
    $standardbad = $DB->count_records_select('event', $standardbadsql);
    $standarddetails = $DB->get_records_sql(
        str_replace(
            ['##group##', '##groupconditions##'],
            ['eventtype', $standardbadsql],
            $detailstemplate
        )
    );
    array_walk($standarddetails, function (&$rec) {
        $rec = $rec->groupname . ': ' . $rec->count;
    });
    $standarddetails = $standarddetails ? '(' . implode(', ', $standarddetails) . ')' : '- all good!';

    // Subscription events.
    $subscription = $DB->count_records_select('event', $subscriptionevents);
    $subscriptionbadsql = "{$brokenevents} AND {$subscriptionevents}";
    $subscriptionbad = $DB->count_records_select('event', $subscriptionbadsql);
    $subscriptiondetails = $DB->get_records_sql(
        str_replace(
            ['##group##', '##groupconditions##'],
            ['eventtype', $subscriptionbadsql],
            $detailstemplate
        )
    );
    array_walk($subscriptiondetails, function (&$rec) {
        $rec = $rec->groupname . ': ' . $rec->count;
    });
    $subscriptiondetails = $subscriptiondetails ? '(' . implode(', ', $subscriptiondetails) . ')' : '- all good!';

    // Override events.
    $override = $DB->count_records_select('event', $overrideevents);
    $overridebadsql = "{$brokenevents} AND {$overrideevents}";
    $overridebad = $DB->count_records_select('event', $overridebadsql);
    $overridedetails = $DB->get_records_sql(
        str_replace(
            ['##group##', '##groupconditions##'],
            ['modulename', $overridebadsql],
            $detailstemplate
        )
    );
    array_walk($overridedetails, function (&$rec) {
        $rec = $rec->groupname . ': ' . $rec->count;
    });
    $overridedetails = $overridedetails ? '(' . implode(', ', $overridedetails) . ')' : '- all good!';

    // Action events.
    $action = $DB->count_records_select('event', $actionevents);
    $actionbadsql = "{$brokenevents} AND {$actionevents}";
    $actionbad = $DB->count_records_select('event', $actionbadsql);
    $actiondetails = $DB->get_records_sql(
        str_replace(
            ['##group##', '##groupconditions##'],
            ['modulename', $actionbadsql],
            $detailstemplate
        )
    );
    array_walk($actiondetails, function (&$rec) {
        $rec = $rec->groupname . ': ' . $rec->count;
    });
    $actiondetails = $actiondetails ? '(' . implode(', ', $actiondetails) . ')' : '- all good!';

    // Other events.
    $other = $DB->count_records_select('event', $otherevents);
    $otherbadsql = "{$brokenevents} AND {$otherevents}";
    $otherbad = $DB->count_records_select('event', $otherbadsql);
    $otherdetails = $DB->get_records_sql(
        str_replace(
            ['##group##', '##groupconditions##'],
            ['COALESCE(component, modulename)', $otherbadsql],
            $detailstemplate
        )
    );
    array_walk($otherdetails, function (&$rec) {
        $rec = ($rec->groupname ?: 'unknown') . ': ' . $rec->count;
    });
    $otherdetails = $otherdetails ? '(' . implode(', ', $otherdetails) . ')' : '- all good!';

    // Let's output some textual information if required to.
    if ($output) {
        mtrace("");
        mtrace("Totals: {$total} / {$totalbad} (total / wrong)");
        mtrace("  - standards events: {$standard} / {$standardbad} {$standarddetails}");
        mtrace("  - subscription events: {$subscription} / {$subscriptionbad} {$subscriptiondetails}");
        mtrace("  - override events: {$override} / {$overridebad} {$overridedetails}");
        mtrace("  - action events: {$action} / {$actionbad} {$actiondetails}");
        mtrace("  - other events: {$other} / {$otherbad} {$otherdetails}");
        mtrace("");
    }

    return [
        'total' => (object)['count' => $total, 'bad' => $totalbad, 'sql' => $totalbadsql],
        'standard' => (object)['count' => $standard, 'bad' => $standardbad, 'sql' => $standardbadsql],
        'subscription' => (object)['count' => $subscription, 'bad' => $subscriptionbad, 'sql' => $subscriptionbadsql],
        'override' => (object)['count' => $override, 'bad' => $overridebad, 'sql' => $overridebadsql],
        'action' => (object)['count' => $action, 'bad' => $actionbad, 'sql' => $actionbadsql],
        'other' => (object)['count' => $other, 'bad' => $otherbad, 'sql' => $otherbadsql],
    ];
}

/**
 * Detects the calendar events needing to be fixed. With optional output.
 *
 * @param stdClass[] an array of event types (as keys) with total and bad counters, plus sql to retrieve them.
 * @param bool $output true if the function must output information, false if not.
 * @param int $maxseconds Number of seconds the function will run as max, with zero meaning no limit.
 * @return bool true if the function has not finished fixing everything, false if it has finished.
 */
function upgrade_calendar_events_fix_remaining(array $info, bool $output = true, int $maxseconds = 0): bool {
    global $DB;

    upgrade_calendar_events_mtrace('', $output);

    // Initial preparations.
    $starttime = time();
    $endtime = $maxseconds ? ($starttime + $maxseconds) : 0;

    // No bad events, or all bad events are "other" events, finished.
    if ($info['total']->bad == 0 || $info['total']->bad == $info['other']->bad) {
        return false;
    }

    // Let's fix overriden events first (they are the ones performing worse with the missing userid).
    if ($info['override']->bad != 0) {
        if (upgrade_calendar_override_events_fix($info['override'], $output, $endtime)) {
            return true; // Not finished yet.
        }
    }

    // Let's fix the subscription events (like standard ones, but with the event_subscriptions table).
    if ($info['subscription']->bad != 0) {
        if (upgrade_calendar_subscription_events_fix($info['subscription'], $output, $endtime)) {
            return true; // Not finished yet.
        }
    }

    // Let's fix the standard events (site, category, course, group).
    if ($info['standard']->bad != 0) {
        if (upgrade_calendar_standard_events_fix($info['standard'], $output, $endtime)) {
            return true; // Not finished yet.
        }
    }

    // Let's fix the action events (all them are "general" ones, not user-specific in core).
    if ($info['action']->bad != 0) {
        if (upgrade_calendar_action_events_fix($info['action'], $output, $endtime)) {
            return true; // Not finished yet.
        }
    }

    // Have arrived here, finished!
    return false;
}

/**
 * Wrapper over mtrace() to allow a few more things to be specified.
 *
 * @param string $string string to output.
 * @param bool $output true to perform the output, false to avoid it.
 */
function upgrade_calendar_events_mtrace(string $string, bool $output): void {
    static $cols = 0;

    // No output, do nothing.
    if (!$output) {
        return;
    }

    // Printing dots... let's output them slightly nicer.
    if ($string === '.') {
        $cols++;
        // Up to 60 cols.
        if ($cols < 60) {
            mtrace($string, '');
        } else {
            mtrace($string);
            $cols = 0;
        }
        return;
    }

    // Reset cols, have ended printing dots.
    if ($cols) {
        $cols = 0;
        mtrace('');
    }

    // Normal output.
    mtrace($string);
}

/**
 * Get a valid editing teacher for a given courseid
 *
 * @param int $courseid The course to look for editing teachers.
 * @return int A user id of an editing teacher or, if missing, the admin userid.
 */
function upgrade_calendar_events_get_teacherid(int $courseid): int {

    if ($context = context_course::instance($courseid, IGNORE_MISSING)) {
        if ($havemanage = get_users_by_capability($context, 'moodle/course:manageactivities', 'u.id')) {
            return array_keys($havemanage)[0];
        }
    }
    return get_admin()->id; // Could not find a teacher, default to admin.
}

/**
 * Detects the calendar standard events needing to be fixed. With optional output.
 *
 * @param stdClass $info an object with total and bad counters, plus sql to retrieve them.
 * @param bool $output true if the function must output information, false if not.
 * @param int $endtime cutoff time when the process must stop (0 means no cutoff).
 * @return bool true if the function has not finished fixing everything, false if it has finished.
 */
function upgrade_calendar_standard_events_fix(stdClass $info, bool $output = true, int $endtime = 0): bool {
    global $DB;

    $return = false; // Let's assume the function is going to finish by default.
    $status = "Finished!"; // To decide the message to be presented on return.

    upgrade_calendar_events_mtrace('Processing standard events', $output);

    $rs = $DB->get_recordset_sql("
        SELECT DISTINCT eventtype, courseid
          FROM {event}
         WHERE {$info->sql}");

    foreach ($rs as $record) {
        switch ($record->eventtype) {
            case 'site':
            case 'category':
                // These are created by admin.
                $DB->set_field('event', 'userid', get_admin()->id, ['eventtype' => $record->eventtype]);
                break;
            case 'course':
            case 'group':
                // These are created by course teacher.
                $DB->set_field('event', 'userid', upgrade_calendar_events_get_teacherid($record->courseid),
                    ['eventtype' => $record->eventtype, 'courseid' => $record->courseid]);
                break;
        }

        // Cutoff time, let's exit.
        if ($endtime && $endtime <= time()) {
            $status = 'Remaining standard events pending';
            $return = true; // Not finished yet.
            break;
        }
        upgrade_calendar_events_mtrace('.', $output);
    }
    $rs->close();
    upgrade_calendar_events_mtrace($status, $output);
    upgrade_calendar_events_mtrace('', $output);
    return $return;
}

/**
 * Detects the calendar subscription events needing to be fixed. With optional output.
 *
 * @param stdClass $info an object with total and bad counters, plus sql to retrieve them.
 * @param bool $output true if the function must output information, false if not.
 * @param int $endtime cutoff time when the process must stop (0 means no cutoff).
 * @return bool true if the function has not finished fixing everything, false if it has finished.
 */
function upgrade_calendar_subscription_events_fix(stdClass $info, bool $output = true, int $endtime = 0): bool {
    global $DB;

    $return = false; // Let's assume the function is going to finish by default.
    $status = "Finished!"; // To decide the message to be presented on return.

    upgrade_calendar_events_mtrace('Processing subscription events', $output);

    $rs = $DB->get_recordset_sql("
        SELECT DISTINCT subscriptionid AS id
          FROM {event}
         WHERE {$info->sql}");

    foreach ($rs as $subscription) {
        // Subscriptions can be site or category level, let's put the admin as userid.
        // (note that "user" subscription weren't deleted so there is nothing to recover with them.
        $DB->set_field('event_subscriptions', 'userid', get_admin()->id, ['id' => $subscription->id]);
        $DB->set_field('event', 'userid', get_admin()->id, ['subscriptionid' => $subscription->id]);

        // Cutoff time, let's exit.
        if ($endtime && $endtime <= time()) {
            $status = 'Remaining subscription events pending';
            $return = true; // Not finished yet.
            break;
        }
        upgrade_calendar_events_mtrace('.', $output);
    }
    $rs->close();
    upgrade_calendar_events_mtrace($status, $output);
    upgrade_calendar_events_mtrace('', $output);
    return $return;
}

/**
 * Detects the calendar action events needing to be fixed. With optional output.
 *
 * @param stdClass $info an object with total and bad counters, plus sql to retrieve them.
 * @param bool $output true if the function must output information, false if not.
 * @param int $endtime cutoff time when the process must stop (0 means no cutoff).
 * @return bool true if the function has not finished fixing everything, false if it has finished.
 */
function upgrade_calendar_action_events_fix(stdClass $info, bool $output = true, int $endtime = 0): bool {
    global $DB;

    $return = false; // Let's assume the function is going to finish by default.
    $status = "Finished!"; // To decide the message to be presented on return.

    upgrade_calendar_events_mtrace('Processing action events', $output);

    $rs = $DB->get_recordset_sql("
        SELECT DISTINCT modulename, instance, courseid
          FROM {event}
         WHERE {$info->sql}");

    foreach ($rs as $record) {
        // These are created by course teacher.
        $DB->set_field('event', 'userid', upgrade_calendar_events_get_teacherid($record->courseid),
            ['modulename' => $record->modulename, 'instance' => $record->instance, 'courseid' => $record->courseid]);

        // Cutoff time, let's exit.
        if ($endtime && $endtime <= time()) {
            $status = 'Remaining action events pending';
            $return = true; // Not finished yet.
            break;
        }
        upgrade_calendar_events_mtrace('.', $output);
    }
    $rs->close();
    upgrade_calendar_events_mtrace($status, $output);
    upgrade_calendar_events_mtrace('', $output);
    return $return;
}

/**
 * Detects the calendar override events needing to be fixed. With optional output.
 *
 * @param stdClass $info an object with total and bad counters, plus sql to retrieve them.
 * @param bool $output true if the function must output information, false if not.
 * @param int $endtime cutoff time when the process must stop (0 means no cutoff).
 * @return bool true if the function has not finished fixing everything, false if it has finished.
 */
function upgrade_calendar_override_events_fix(stdClass $info, bool $output = true, int $endtime = 0): bool {
    global $CFG, $DB;

    include_once($CFG->dirroot. '/course/lib.php');
    include_once($CFG->dirroot. '/mod/assign/lib.php');
    include_once($CFG->dirroot. '/mod/assign/locallib.php');
    include_once($CFG->dirroot. '/mod/lesson/lib.php');
    include_once($CFG->dirroot. '/mod/lesson/locallib.php');
    include_once($CFG->dirroot. '/mod/quiz/lib.php');
    include_once($CFG->dirroot. '/mod/quiz/locallib.php');

    $return = false; // Let's assume the function is going to finish by default.
    $status = "Finished!"; // To decide the message to be presented on return.

    upgrade_calendar_events_mtrace('Processing override events', $output);

    $rs = $DB->get_recordset_sql("
        SELECT DISTINCT modulename, instance
          FROM {event}
         WHERE {$info->sql}");

    foreach ($rs as $module) {
        // Remove all the records from the events table for the module.
        $DB->delete_records('event', ['modulename' => $module->modulename, 'instance' => $module->instance]);

        // Get the activity record.
        if (!$activityrecord = $DB->get_record($module->modulename, ['id' => $module->instance])) {
            // Orphaned calendar event (activity doesn't exists), skip.
            continue;
        }

        // Let's rebuild it by calling to each module API.
        switch ($module->modulename) {
            case 'assign';
                if (function_exists('assign_prepare_update_events')) {
                    assign_prepare_update_events($activityrecord);
                }
                break;
            case 'lesson':
                if (function_exists('lesson_update_events')) {
                    lesson_update_events($activityrecord);
                }
                break;
            case 'quiz':
                if (function_exists('quiz_update_events')) {
                    quiz_update_events($activityrecord);
                }
                break;
        }

        // Sometimes, some (group) overrides are created without userid, when that happens, they deserve
        // some user (teacher or admin). This doesn't affect to groups calendar events behaviour,
        // but allows counters to detect already processed group overrides and makes things
        // consistent.
        $DB->set_field_select('event', 'userid', upgrade_calendar_events_get_teacherid($activityrecord->course),
            'modulename = ? AND instance = ? and priority != 0 and userid = 0',
            ['modulename' => $module->modulename, 'instance' => $module->instance]);

        // Cutoff time, let's exit.
        if ($endtime && $endtime <= time()) {
            $status = 'Remaining override events pending';
            $return = true; // Not finished yet.
            break;
        }
        upgrade_calendar_events_mtrace('.', $output);
    }
    $rs->close();
    upgrade_calendar_events_mtrace($status, $output);
    upgrade_calendar_events_mtrace('', $output);
    return $return;
}

/**
 * Add a new item at the end of the usermenu.
 *
 * @param string $menuitem
 */
function upgrade_add_item_to_usermenu(string $menuitem): void {
    global $CFG;

    // Get current configuration data.
    $currentcustomusermenuitems = str_replace(["\r\n", "\r"], "\n", $CFG->customusermenuitems);
    $lines = preg_split('/\n/', $currentcustomusermenuitems, -1, PREG_SPLIT_NO_EMPTY);
    $lines = array_map('trim', $lines);

    if (!in_array($menuitem, $lines)) {
        // Add the item to the menu.
        $lines[] = $menuitem;
        set_config('customusermenuitems', implode("\n", $lines));
    }
}

/**
 * Update all instances of a block shown on a pagetype to a new default region, adding missing block instances where
 * none is found.
 *
 * Note: This is intended as a helper to add blocks to all instances of the standard my-page. It will only work where
 * the subpagepattern is a string representation of an integer. If there are any string values this will not work.
 *
 * @param string $blockname The block name, without the block_ frankenstyle component
 * @param string $pagename The type of my-page to match
 * @param string $pagetypepattern The page type pattern to match for the block
 * @param string $newdefaultregion The new region to set
 */
function upgrade_block_set_defaultregion(
    string $blockname,
    string $pagename,
    string $pagetypepattern,
    string $newdefaultregion
): void {
    global $DB;

    // The subpagepattern is a string.
    // In all core blocks it contains a string represnetation of an integer, but it is theoretically possible for a
    // community block to do something different.
    // This function is not suited to those cases.
    $subpagepattern = $DB->sql_cast_char2int('bi.subpagepattern');
    $subpageempty = $DB->sql_isnotempty('block_instances', 'bi.subpagepattern', true, false);

    // If a subquery returns any NULL then the NOT IN returns no results at all.
    // By adding a join in the inner select on my_pages we remove any possible nulls and prevent any need for
    // additional casting to filter out the nulls.
    $sql = <<<EOF
        INSERT INTO {block_instances} (
            blockname,
            parentcontextid,
            showinsubcontexts,
            pagetypepattern,
            subpagepattern,
            defaultregion,
            defaultweight,
            timecreated,
            timemodified
        ) SELECT
            :selectblockname AS blockname,
            c.id AS parentcontextid,
            0 AS showinsubcontexts,
            :selectpagetypepattern AS pagetypepattern,
            mp.id AS subpagepattern,
            :selectdefaultregion AS defaultregion,
            0 AS defaultweight,
            :selecttimecreated AS timecreated,
            :selecttimemodified AS timemodified
          FROM {my_pages} mp
          JOIN {context} c ON c.instanceid = mp.userid AND c.contextlevel = :contextuser
         WHERE mp.id NOT IN (
            SELECT mpi.id FROM {my_pages} mpi
              JOIN {block_instances} bi
                    ON bi.blockname = :blockname
                   AND bi.subpagepattern IS NOT NULL AND {$subpageempty}
                   AND bi.pagetypepattern = :pagetypepattern
                   AND {$subpagepattern} = mpi.id
         )
         AND mp.private = 1
         AND mp.name = :pagename
    EOF;

    $DB->execute($sql, [
        'selectblockname' => $blockname,
        'contextuser' => CONTEXT_USER,
        'selectpagetypepattern' => $pagetypepattern,
        'selectdefaultregion' => $newdefaultregion,
        'selecttimecreated' => time(),
        'selecttimemodified' => time(),
        'pagetypepattern' => $pagetypepattern,
        'blockname' => $blockname,
        'pagename' => $pagename,
    ]);

    // Update the existing instances.
    $sql = <<<EOF
        UPDATE {block_instances}
           SET defaultregion = :newdefaultregion
         WHERE id IN (
            SELECT * FROM (
                SELECT bi.id
                  FROM {my_pages} mp
                  JOIN {block_instances} bi
                        ON bi.blockname = :blockname
                       AND bi.subpagepattern IS NOT NULL AND {$subpageempty}
                       AND bi.pagetypepattern = :pagetypepattern
                       AND {$subpagepattern} = mp.id
                 WHERE mp.private = 1
                   AND mp.name = :pagename
                   AND bi.defaultregion <> :existingnewdefaultregion
            ) bid
         )
    EOF;

    $DB->execute($sql, [
        'newdefaultregion' => $newdefaultregion,
        'pagetypepattern' => $pagetypepattern,
        'blockname' => $blockname,
        'existingnewdefaultregion' => $newdefaultregion,
        'pagename' => $pagename,
    ]);

    // Note: This can be time consuming!
    \context_helper::create_instances(CONTEXT_BLOCK);
}

/**
 * Remove all instances of a block on pages of the specified pagetypepattern.
 *
 * Note: This is intended as a helper to add blocks to all instances of the standard my-page. It will only work where
 * the subpagepattern is a string representation of an integer. If there are any string values this will not work.
 *
 * @param string $blockname The block name, without the block_ frankenstyle component
 * @param string $pagename The type of my-page to match
 * @param string $pagetypepattern This is typically used on the 'my-index'
 */
function upgrade_block_delete_instances(
    string $blockname,
    string $pagename,
    string $pagetypepattern
): void {
    global $DB;

    $deleteblockinstances = function (string $instanceselect, array $instanceparams) use ($DB) {
        $deletesql = <<<EOF
            SELECT c.id AS cid
              FROM {context} c
              JOIN {block_instances} bi ON bi.id = c.instanceid AND c.contextlevel = :contextlevel
             WHERE {$instanceselect}
        EOF;
        $DB->delete_records_subquery('context', 'id', 'cid', $deletesql, array_merge($instanceparams, [
            'contextlevel' => CONTEXT_BLOCK,
        ]));

        $deletesql = <<<EOF
            SELECT bp.id AS bpid
              FROM {block_positions} bp
              JOIN {block_instances} bi ON bi.id = bp.blockinstanceid
             WHERE {$instanceselect}
        EOF;
        $DB->delete_records_subquery('block_positions', 'id', 'bpid', $deletesql, $instanceparams);

        $blockhidden = $DB->sql_concat("'block'", 'bi.id', "'hidden'");
        $blockdocked = $DB->sql_concat("'docked_block_instance_'", 'bi.id');
        $deletesql = <<<EOF
            SELECT p.id AS pid
              FROM {user_preferences} p
              JOIN {block_instances} bi ON p.name IN ({$blockhidden}, {$blockdocked})
             WHERE {$instanceselect}
        EOF;
        $DB->delete_records_subquery('user_preferences', 'id', 'pid', $deletesql, $instanceparams);

        $deletesql = <<<EOF
            SELECT bi.id AS bid
              FROM {block_instances} bi
             WHERE {$instanceselect}
        EOF;
        $DB->delete_records_subquery('block_instances', 'id', 'bid', $deletesql, $instanceparams);
    };

    // Delete the default indexsys version of the block.
    $subpagepattern = $DB->get_record('my_pages', [
        'userid' => null,
        'name' => $pagename,
        'private' => MY_PAGE_PRIVATE,
    ], 'id', IGNORE_MULTIPLE)->id;

    $instanceselect = <<<EOF
            blockname = :blockname
        AND pagetypepattern = :pagetypepattern
        AND subpagepattern = :subpagepattern
    EOF;

    $params = [
        'blockname' => $blockname,
        'pagetypepattern' => $pagetypepattern,
        'subpagepattern' => $subpagepattern,
    ];
    $deleteblockinstances($instanceselect, $params);

    // The subpagepattern is a string.
    // In all core blocks it contains a string represnetation of an integer, but it is theoretically possible for a
    // community block to do something different.
    // This function is not suited to those cases.
    $subpagepattern = $DB->sql_cast_char2int('bi.subpagepattern');

    // Look for any and all instances of the block in customised /my pages.
    $subpageempty = $DB->sql_isnotempty('block_instances', 'bi.subpagepattern', true, false);
    $instanceselect = <<<EOF
         bi.id IN (
            SELECT * FROM (
                SELECT bi.id
                  FROM {my_pages} mp
                  JOIN {block_instances} bi
                        ON bi.blockname = :blockname
                       AND bi.subpagepattern IS NOT NULL AND {$subpageempty}
                       AND bi.pagetypepattern = :pagetypepattern
                       AND {$subpagepattern} = mp.id
                 WHERE mp.private = :private
                   AND mp.name = :pagename
            ) bid
         )
    EOF;

    $params = [
        'blockname' => $blockname,
        'pagetypepattern' => $pagetypepattern,
        'pagename' => $pagename,
        'private' => MY_PAGE_PRIVATE,
    ];

    $deleteblockinstances($instanceselect, $params);
}

/**
 * Update the block instance parentcontext to point to the correct user context id for the specified block on a my page.
 *
 * @param string $blockname
 * @param string $pagename
 * @param string $pagetypepattern
 */
function upgrade_block_set_my_user_parent_context(
    string $blockname,
    string $pagename,
    string $pagetypepattern
): void {
    global $DB;

    $subpagepattern = $DB->sql_cast_char2int('bi.subpagepattern');
    // Look for any and all instances of the block in customised /my pages.
    $subpageempty = $DB->sql_isnotempty('block_instances', 'bi.subpagepattern', true, false);

    $dbman = $DB->get_manager();
    $temptablename = 'block_instance_context';
    $xmldbtable = new \xmldb_table($temptablename);
    $xmldbtable->add_field('instanceid', XMLDB_TYPE_INTEGER, 10, null, XMLDB_NOTNULL, null, null);
    $xmldbtable->add_field('contextid', XMLDB_TYPE_INTEGER, 10, null, XMLDB_NOTNULL, null, null);
    $xmldbtable->add_key('primary', XMLDB_KEY_PRIMARY, ['instanceid']);
    $dbman->create_temp_table($xmldbtable);

    $sql = <<<EOF
        INSERT INTO {block_instance_context} (
            instanceid,
            contextid
        ) SELECT
            bi.id as instanceid,
            c.id as contextid
           FROM {my_pages} mp
           JOIN {context} c ON c.instanceid = mp.userid AND c.contextlevel = :contextuser
           JOIN {block_instances} bi
                ON bi.blockname = :blockname
               AND bi.subpagepattern IS NOT NULL AND {$subpageempty}
               AND bi.pagetypepattern = :pagetypepattern
               AND {$subpagepattern} = mp.id
          WHERE mp.name = :pagename AND bi.parentcontextid <> c.id
    EOF;

    $DB->execute($sql, [
        'blockname' => $blockname,
        'pagetypepattern' => $pagetypepattern,
        'contextuser' => CONTEXT_USER,
        'pagename' => $pagename,
    ]);

    $dbfamily = $DB->get_dbfamily();
    if ($dbfamily === 'mysql') {
        // MariaDB and MySQL.
        $sql = <<<EOF
            UPDATE {block_instances} bi, {block_instance_context} bic
               SET bi.parentcontextid = bic.contextid
             WHERE bi.id = bic.instanceid
        EOF;
    } else if ($dbfamily === 'oracle') {
        $sql = <<<EOF
            UPDATE {block_instances} bi
            SET (bi.parentcontextid) = (
                SELECT bic.contextid
                  FROM {block_instance_context} bic
                 WHERE bic.instanceid = bi.id
            ) WHERE EXISTS (
                SELECT 'x'
                  FROM {block_instance_context} bic
                 WHERE bic.instanceid = bi.id
            )
        EOF;
    } else {
        // Postgres and sqlsrv.
        $sql = <<<EOF
            UPDATE {block_instances}
            SET parentcontextid = bic.contextid
            FROM {block_instance_context} bic
            WHERE {block_instances}.id = bic.instanceid
        EOF;
    }

    $DB->execute($sql);

    $dbman->drop_table($xmldbtable);
}

/**
 * Fix the timestamps for files where their timestamps are older
 * than the directory listing that they are contained in.
 */
function upgrade_fix_file_timestamps() {
    global $DB;

    // Due to incompatability in SQL syntax for updates with joins,
    // These will be updated in a select + separate update.
    $sql = "SELECT f.id, f2.timecreated
              FROM {files} f
              JOIN {files} f2
                    ON f2.contextid = f.contextid
                   AND f2.filepath = f.filepath
                   AND f2.component = f.component
                   AND f2.filearea = f.filearea
                   AND f2.itemid = f.itemid
                   AND f2.filename = '.'
             WHERE f2.timecreated > f.timecreated";

    $recordset = $DB->get_recordset_sql($sql);

    if (!$recordset->valid()) {
        $recordset->close();
        return;
    }

    foreach ($recordset as $record) {
        $record->timemodified = $record->timecreated;
        $DB->update_record('files', $record);
    }

    $recordset->close();
}

/**
 * Upgrade helper to add foreign keys and indexes for MDL-49795
 */
function upgrade_add_foreign_key_and_indexes() {
    global $DB;

    $dbman = $DB->get_manager();
    // Define key originalcourseid (foreign) to be added to course.
    $table = new xmldb_table('course');
    $key = new xmldb_key('originalcourseid', XMLDB_KEY_FOREIGN, ['originalcourseid'], 'course', ['id']);
    // Launch add key originalcourseid.
    $dbman->add_key($table, $key);

    // Define key roleid (foreign) to be added to enrol.
    $table = new xmldb_table('enrol');
    $key = new xmldb_key('roleid', XMLDB_KEY_FOREIGN, ['roleid'], 'role', ['id']);
    // Launch add key roleid.
    $dbman->add_key($table, $key);

    // Define key userid (foreign) to be added to scale.
    $table = new xmldb_table('scale');
    $key = new xmldb_key('userid', XMLDB_KEY_FOREIGN, ['userid'], 'user', ['id']);
    // Launch add key userid.
    $dbman->add_key($table, $key);

    // Define key userid (foreign) to be added to scale_history.
    $table = new xmldb_table('scale_history');
    $key = new xmldb_key('userid', XMLDB_KEY_FOREIGN, ['userid'], 'user', ['id']);
    // Launch add key userid.
    $dbman->add_key($table, $key);

    // Define key courseid (foreign) to be added to post.
    $table = new xmldb_table('post');
    $key = new xmldb_key('courseid', XMLDB_KEY_FOREIGN, ['courseid'], 'course', ['id']);
    // Launch add key courseid.
    $dbman->add_key($table, $key);

    // Define key coursemoduleid (foreign) to be added to post.
    $table = new xmldb_table('post');
    $key = new xmldb_key('coursemoduleid', XMLDB_KEY_FOREIGN, ['coursemoduleid'], 'course_modules', ['id']);
    // Launch add key coursemoduleid.
    $dbman->add_key($table, $key);

    // Define key questionid (foreign) to be added to question_statistics.
    $table = new xmldb_table('question_statistics');
    $key = new xmldb_key('questionid', XMLDB_KEY_FOREIGN, ['questionid'], 'question', ['id']);
    // Launch add key questionid.
    $dbman->add_key($table, $key);

    // Define key questionid (foreign) to be added to question_response_analysis.
    $table = new xmldb_table('question_response_analysis');
    $key = new xmldb_key('questionid', XMLDB_KEY_FOREIGN, ['questionid'], 'question', ['id']);
    // Launch add key questionid.
    $dbman->add_key($table, $key);

    // Define index last_log_id (not unique) to be added to mnet_host.
    $table = new xmldb_table('mnet_host');
    $index = new xmldb_index('last_log_id', XMLDB_INDEX_NOTUNIQUE, ['last_log_id']);
    // Conditionally launch add index last_log_id.
    if (!$dbman->index_exists($table, $index)) {
        $dbman->add_index($table, $index);
    }

    // Define key userid (foreign) to be added to mnet_session.
    $table = new xmldb_table('mnet_session');
    $key = new xmldb_key('userid', XMLDB_KEY_FOREIGN, ['userid'], 'user', ['id']);
    // Launch add key userid.
    $dbman->add_key($table, $key);

    // Define key mnethostid (foreign) to be added to mnet_session.
    $table = new xmldb_table('mnet_session');
    $key = new xmldb_key('mnethostid', XMLDB_KEY_FOREIGN, ['mnethostid'], 'mnet_host', ['id']);
    // Launch add key mnethostid.
    $dbman->add_key($table, $key);

    // Define key userid (foreign) to be added to grade_import_values.
    $table = new xmldb_table('grade_import_values');
    $key = new xmldb_key('userid', XMLDB_KEY_FOREIGN, ['userid'], 'user', ['id']);
    // Launch add key userid.
    $dbman->add_key($table, $key);

    // Define key tempdataid (foreign) to be added to portfolio_log.
    $table = new xmldb_table('portfolio_log');
    $key = new xmldb_key('tempdataid', XMLDB_KEY_FOREIGN, ['tempdataid'], 'portfolio_tempdata', ['id']);
    // Launch add key tempdataid.
    $dbman->add_key($table, $key);

    // Define key usermodified (foreign) to be added to file_conversion.
    $table = new xmldb_table('file_conversion');
    $key = new xmldb_key('usermodified', XMLDB_KEY_FOREIGN, ['usermodified'], 'user', ['id']);
    // Launch add key usermodified.
    $dbman->add_key($table, $key);

    // Define key userid (foreign) to be added to repository_instances.
    $table = new xmldb_table('repository_instances');
    $key = new xmldb_key('userid', XMLDB_KEY_FOREIGN, ['userid'], 'user', ['id']);
    // Launch add key userid.
    $dbman->add_key($table, $key);

    // Define key contextid (foreign) to be added to repository_instances.
    $table = new xmldb_table('repository_instances');
    $key = new xmldb_key('contextid', XMLDB_KEY_FOREIGN, ['contextid'], 'context', ['id']);
    // Launch add key contextid.
    $dbman->add_key($table, $key);

    // Define key scaleid (foreign) to be added to rating.
    $table = new xmldb_table('rating');
    $key = new xmldb_key('scaleid', XMLDB_KEY_FOREIGN, ['scaleid'], 'scale', ['id']);
    // Launch add key scaleid.
    $dbman->add_key($table, $key);

    // Define key courseid (foreign) to be added to course_published.
    $table = new xmldb_table('course_published');
    $key = new xmldb_key('courseid', XMLDB_KEY_FOREIGN, ['courseid'], 'course', ['id']);
    // Launch add key courseid.
    $dbman->add_key($table, $key);

    // Define index hubcourseid (not unique) to be added to course_published.
    $table = new xmldb_table('course_published');
    $index = new xmldb_index('hubcourseid', XMLDB_INDEX_NOTUNIQUE, ['hubcourseid']);
    // Conditionally launch add index hubcourseid.
    if (!$dbman->index_exists($table, $index)) {
        $dbman->add_index($table, $index);
    }

    // Define key courseid (foreign) to be added to event_subscriptions.
    $table = new xmldb_table('event_subscriptions');
    $key = new xmldb_key('courseid', XMLDB_KEY_FOREIGN, ['courseid'], 'course', ['id']);
    // Launch add key courseid.
    $dbman->add_key($table, $key);

    // Define key userid (foreign) to be added to event_subscriptions.
    $table = new xmldb_table('event_subscriptions');
    $key = new xmldb_key('userid', XMLDB_KEY_FOREIGN, ['userid'], 'user', ['id']);
    // Launch add key userid.
    $dbman->add_key($table, $key);

    // Define key userid (foreign) to be added to task_log.
    $table = new xmldb_table('task_log');
    $key = new xmldb_key('userid', XMLDB_KEY_FOREIGN, ['userid'], 'user', ['id']);
    // Launch add key userid.
    $dbman->add_key($table, $key);

    // Define key scaleid (foreign) to be added to competency.
    $table = new xmldb_table('competency');
    $key = new xmldb_key('scaleid', XMLDB_KEY_FOREIGN, ['scaleid'], 'scale', ['id']);
    // Launch add key scaleid.
    $dbman->add_key($table, $key);

    // Define key usermodified (foreign) to be added to competency.
    $table = new xmldb_table('competency');
    $key = new xmldb_key('usermodified', XMLDB_KEY_FOREIGN, ['usermodified'], 'user', ['id']);
    // Launch add key usermodified.
    $dbman->add_key($table, $key);

    // Define key usermodified (foreign) to be added to competency_coursecompsetting.
    $table = new xmldb_table('competency_coursecompsetting');
    $key = new xmldb_key('usermodified', XMLDB_KEY_FOREIGN, ['usermodified'], 'user', ['id']);
    // Launch add key usermodified.
    $dbman->add_key($table, $key);

    // Define key contextid (foreign) to be added to competency_framework.
    $table = new xmldb_table('competency_framework');
    $key = new xmldb_key('contextid', XMLDB_KEY_FOREIGN, ['contextid'], 'context', ['id']);
    // Launch add key contextid.
    $dbman->add_key($table, $key);

    // Define key scaleid (foreign) to be added to competency_framework.
    $table = new xmldb_table('competency_framework');
    $key = new xmldb_key('scaleid', XMLDB_KEY_FOREIGN, ['scaleid'], 'scale', ['id']);
    // Launch add key scaleid.
    $dbman->add_key($table, $key);

    // Define key usermodified (foreign) to be added to competency_framework.
    $table = new xmldb_table('competency_framework');
    $key = new xmldb_key('usermodified', XMLDB_KEY_FOREIGN, ['usermodified'], 'user', ['id']);
    // Launch add key usermodified.
    $dbman->add_key($table, $key);

    // Define key usermodified (foreign) to be added to competency_coursecomp.
    $table = new xmldb_table('competency_coursecomp');
    $key = new xmldb_key('usermodified', XMLDB_KEY_FOREIGN, ['usermodified'], 'user', ['id']);
    // Launch add key usermodified.
    $dbman->add_key($table, $key);

    // Define key actionuserid (foreign) to be added to competency_evidence.
    $table = new xmldb_table('competency_evidence');
    $key = new xmldb_key('actionuserid', XMLDB_KEY_FOREIGN, ['actionuserid'], 'user', ['id']);
    // Launch add key actionuserid.
    $dbman->add_key($table, $key);

    // Define key contextid (foreign) to be added to competency_evidence.
    $table = new xmldb_table('competency_evidence');
    $key = new xmldb_key('contextid', XMLDB_KEY_FOREIGN, ['contextid'], 'context', ['id']);
    // Launch add key contextid.
    $dbman->add_key($table, $key);

    // Define key usermodified (foreign) to be added to competency_evidence.
    $table = new xmldb_table('competency_evidence');
    $key = new xmldb_key('usermodified', XMLDB_KEY_FOREIGN, ['usermodified'], 'user', ['id']);
    // Launch add key usermodified.
    $dbman->add_key($table, $key);

    // Define key usermodified (foreign) to be added to competency_userevidence.
    $table = new xmldb_table('competency_userevidence');
    $key = new xmldb_key('usermodified', XMLDB_KEY_FOREIGN, ['usermodified'], 'user', ['id']);
    // Launch add key usermodified.
    $dbman->add_key($table, $key);

    // Define key usermodified (foreign) to be added to competency_plan.
    $table = new xmldb_table('competency_plan');
    $key = new xmldb_key('usermodified', XMLDB_KEY_FOREIGN, ['usermodified'], 'user', ['id']);
    // Launch add key usermodified.
    $dbman->add_key($table, $key);

    // Define key usermodified (foreign) to be added to competency_template.
    $table = new xmldb_table('competency_template');
    $key = new xmldb_key('usermodified', XMLDB_KEY_FOREIGN, ['usermodified'], 'user', ['id']);
    // Launch add key usermodified.
    $dbman->add_key($table, $key);

    // Define key contextid (foreign) to be added to competency_template.
    $table = new xmldb_table('competency_template');
    $key = new xmldb_key('contextid', XMLDB_KEY_FOREIGN, ['contextid'], 'context', ['id']);
    // Launch add key contextid.
    $dbman->add_key($table, $key);

    // Define key usermodified (foreign) to be added to competency_templatecomp.
    $table = new xmldb_table('competency_templatecomp');
    $key = new xmldb_key('usermodified', XMLDB_KEY_FOREIGN, ['usermodified'], 'user', ['id']);
    // Launch add key usermodified.
    $dbman->add_key($table, $key);

    // Define key usermodified (foreign) to be added to competency_templatecohort.
    $table = new xmldb_table('competency_templatecohort');
    $key = new xmldb_key('usermodified', XMLDB_KEY_FOREIGN, ['usermodified'], 'user', ['id']);
    // Launch add key usermodified.
    $dbman->add_key($table, $key);

    // Define key competencyid (foreign) to be added to competency_relatedcomp.
    $table = new xmldb_table('competency_relatedcomp');
    $key = new xmldb_key('competencyid', XMLDB_KEY_FOREIGN, ['competencyid'], 'competency', ['id']);
    // Launch add key competencyid.
    $dbman->add_key($table, $key);

    // Define key relatedcompetencyid (foreign) to be added to competency_relatedcomp.
    $table = new xmldb_table('competency_relatedcomp');
    $key = new xmldb_key('relatedcompetencyid', XMLDB_KEY_FOREIGN, ['relatedcompetencyid'], 'competency', ['id']);
    // Launch add key relatedcompetencyid.
    $dbman->add_key($table, $key);

    // Define key usermodified (foreign) to be added to competency_relatedcomp.
    $table = new xmldb_table('competency_relatedcomp');
    $key = new xmldb_key('usermodified', XMLDB_KEY_FOREIGN, ['usermodified'], 'user', ['id']);
    // Launch add key usermodified.
    $dbman->add_key($table, $key);

    // Define key usermodified (foreign) to be added to competency_usercomp.
    $table = new xmldb_table('competency_usercomp');
    $key = new xmldb_key('usermodified', XMLDB_KEY_FOREIGN, ['usermodified'], 'user', ['id']);
    // Launch add key usermodified.
    $dbman->add_key($table, $key);

    // Define key usermodified (foreign) to be added to competency_usercompcourse.
    $table = new xmldb_table('competency_usercompcourse');
    $key = new xmldb_key('usermodified', XMLDB_KEY_FOREIGN, ['usermodified'], 'user', ['id']);
    // Launch add key usermodified.
    $dbman->add_key($table, $key);

    // Define key usermodified (foreign) to be added to competency_usercompplan.
    $table = new xmldb_table('competency_usercompplan');
    $key = new xmldb_key('usermodified', XMLDB_KEY_FOREIGN, ['usermodified'], 'user', ['id']);
    // Launch add key usermodified.
    $dbman->add_key($table, $key);

    // Define key usermodified (foreign) to be added to competency_plancomp.
    $table = new xmldb_table('competency_plancomp');
    $key = new xmldb_key('usermodified', XMLDB_KEY_FOREIGN, ['usermodified'], 'user', ['id']);
    // Launch add key usermodified.
    $dbman->add_key($table, $key);

    // Define key usermodified (foreign) to be added to competency_userevidencecomp.
    $table = new xmldb_table('competency_userevidencecomp');
    $key = new xmldb_key('usermodified', XMLDB_KEY_FOREIGN, ['usermodified'], 'user', ['id']);
    // Launch add key usermodified.
    $dbman->add_key($table, $key);

    // Define key usermodified (foreign) to be added to competency_modulecomp.
    $table = new xmldb_table('competency_modulecomp');
    $key = new xmldb_key('usermodified', XMLDB_KEY_FOREIGN, ['usermodified'], 'user', ['id']);
    // Launch add key usermodified.
    $dbman->add_key($table, $key);

    // Define key usermodified (foreign) to be added to oauth2_endpoint.
    $table = new xmldb_table('oauth2_endpoint');
    $key = new xmldb_key('usermodified', XMLDB_KEY_FOREIGN, ['usermodified'], 'user', ['id']);
    // Launch add key usermodified.
    $dbman->add_key($table, $key);

    // Define key usermodified (foreign) to be added to oauth2_system_account.
    $table = new xmldb_table('oauth2_system_account');
    $key = new xmldb_key('usermodified', XMLDB_KEY_FOREIGN, ['usermodified'], 'user', ['id']);
    // Launch add key usermodified.
    $dbman->add_key($table, $key);

    // Define key usermodified (foreign) to be added to oauth2_user_field_mapping.
    $table = new xmldb_table('oauth2_user_field_mapping');
    $key = new xmldb_key('usermodified', XMLDB_KEY_FOREIGN, ['usermodified'], 'user', ['id']);
    // Launch add key usermodified.
    $dbman->add_key($table, $key);

    // Define key usermodified (foreign) to be added to analytics_models.
    $table = new xmldb_table('analytics_models');
    $key = new xmldb_key('usermodified', XMLDB_KEY_FOREIGN, ['usermodified'], 'user', ['id']);
    // Launch add key usermodified.
    $dbman->add_key($table, $key);

    // Define key usermodified (foreign) to be added to analytics_models_log.
    $table = new xmldb_table('analytics_models_log');
    $key = new xmldb_key('usermodified', XMLDB_KEY_FOREIGN, ['usermodified'], 'user', ['id']);
    // Launch add key usermodified.
    $dbman->add_key($table, $key);

    // Define key usermodified (foreign) to be added to oauth2_access_token.
    $table = new xmldb_table('oauth2_access_token');
    $key = new xmldb_key('usermodified', XMLDB_KEY_FOREIGN, ['usermodified'], 'user', ['id']);
    // Launch add key usermodified.
    $dbman->add_key($table, $key);

    // Define key contextid (foreign) to be added to payment_accounts.
    $table = new xmldb_table('payment_accounts');
    $key = new xmldb_key('contextid', XMLDB_KEY_FOREIGN, ['contextid'], 'context', ['id']);
    // Launch add key contextid.
    $dbman->add_key($table, $key);
}

/**
 * Upgrade helper to change a binary column to an integer column with a length of 1 in a consistent manner across databases.
 *
 * This function will
 * - rename the existing column to a temporary name,
 * - add a new column with the integer type,
 * - copy the values from the old column to the new column,
 * - and finally, drop the old column.
 *
 * This function will do nothing if the field is already an integer.
 *
 * The new column with the integer type will need to have a default value of 0.
 * This is to avoid breaking the not null constraint, if it's set, especially if there are existing records.
 * Please make sure that the column definition in install.xml also has the `DEFAULT` attribute value set to 0.
 *
 * @param string $tablename The name of the table.
 * @param string $fieldname The name of the field to be converted.
 * @param bool|null $notnull {@see XMLDB_NOTNULL} or null.
 * @param string|null $previous The name of the field that this field should come after.
 * @return bool
 */
function upgrade_change_binary_column_to_int(
    string $tablename,
    string $fieldname,
    ?bool $notnull = null,
    ?string $previous = null,
): bool {
    global $DB;

    // Get the information about the field to be converted.
    $columns = $DB->get_columns($tablename);
    $toconvert = $columns[$fieldname];

    // Check if the field to be converted is already an integer-type column (`meta_type` property of 'I').
    if ($toconvert->meta_type === 'I') {
        // Nothing to do if the field is already an integer-type.
        return false;
    } else if (!$toconvert->binary) {
        throw new \core\exception\coding_exception(
            'This function is only used to convert XMLDB_TYPE_BINARY fields to XMLDB_TYPE_INTEGER fields. '
            . 'For other field types, please check out \database_manager::change_field_type()'
        );
    }

    $dbman = $DB->get_manager();
    $table = new xmldb_table($tablename);
    // Temporary rename the field. We'll drop this later.
    $tmpfieldname = "tmp$fieldname";
    $field = new xmldb_field($fieldname, XMLDB_TYPE_BINARY);
    $dbman->rename_field($table, $field, $tmpfieldname);

    // Add the new field wih the integer type.
    $field = new xmldb_field($fieldname, XMLDB_TYPE_INTEGER, '1', null, $notnull, null, '0', $previous);
    $dbman->add_field($table, $field);

    // Copy the 'true' values from the old field to the new field.
    if ($DB->get_dbfamily() === 'oracle') {
        // It's tricky to use the binary column in the WHERE clause in Oracle DBs.
        // Let's go updating the records one by one. It's nasty, but it's only done for instances with Oracle DBs.
        // The normal SQL UPDATE statement will be used for other DBs.
        $columns = implode(', ', ['id', $tmpfieldname, $fieldname]);
        $records = $DB->get_recordset($tablename, null, '', $columns);
        if ($records->valid()) {
            foreach ($records as $record) {
                if (!$record->$tmpfieldname) {
                    continue;
                }
                $DB->set_field($tablename, $fieldname, 1, ['id' => $record->id]);
            }
        }
        $records->close();
    } else {
        $sql = 'UPDATE {' . $tablename . '}
                   SET ' . $fieldname . ' = 1
                 WHERE ' . $tmpfieldname . ' = ?';
        $DB->execute($sql, [1]);
    }

    // Drop the old field.
    $oldfield = new xmldb_field($tmpfieldname);
    $dbman->drop_field($table, $oldfield);

    return true;
}

/**
 * Upgrade script replacing absolute URLs in defaulthomepage setting with relative URLs
 */
function upgrade_store_relative_url_sitehomepage() {
    global $CFG, $DB;

    if (str_starts_with((string)$CFG->defaulthomepage, $CFG->wwwroot . '/')) {
        set_config('defaulthomepage', substr((string)$CFG->defaulthomepage, strlen($CFG->wwwroot)));
    }

    $records = $DB->get_records_select('user_preferences', "name = :name AND " . $DB->sql_like('value', ':pattern'),
        ['name' => 'user_home_page_preference', 'pattern' => 'http%']);
    foreach ($records as $record) {
        if (str_starts_with($record->value, $CFG->wwwroot . '/')) {
            $DB->update_record('user_preferences', [
                'id' => $record->id,
                'value' => substr($record->value, strlen($CFG->wwwroot)),
            ]);
        }
    }
}
