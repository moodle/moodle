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
 * so there's something in the database.  The locations of
 * strings and files is a bit odd, but this is because we
 * need to maintain backward compatibility with many different
 * existing language translations and older sites.
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
    $defaultscale->scale = get_string('postrating1', 'forum').','.
                           get_string('postrating2', 'forum').','.
                           get_string('postrating3', 'forum');
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
 * Updates the existing prediction actions in the database according to the new suggested actions.
 * @return null
 */
function upgrade_rename_prediction_actions_useful_incorrectly_flagged() {
    global $DB;

    // The update depends on the analyser class used by each model so we need to iterate through the models in the system.
    $modelids = $DB->get_records_sql("SELECT DISTINCT am.id, am.target
                                        FROM {analytics_models} am
                                        JOIN {analytics_predictions} ap ON ap.modelid = am.id
                                        JOIN {analytics_prediction_actions} apa ON ap.id = apa.predictionid");
    foreach ($modelids as $model) {
        $targetname = $model->target;
        if (!class_exists($targetname)) {
            // The plugin may not be available.
            continue;
        }
        $target = new $targetname();

        $analyserclass = $target->get_analyser_class();
        if (!class_exists($analyserclass)) {
            // The plugin may not be available.
            continue;
        }

        if ($analyserclass::one_sample_per_analysable()) {
            // From 'fixed' to 'useful'.
            $params = ['oldaction' => 'fixed', 'newaction' => 'useful'];
        } else {
            // From 'notuseful' to 'incorrectlyflagged'.
            $params = ['oldaction' => 'notuseful', 'newaction' => 'incorrectlyflagged'];
        }

        $subsql = "SELECT id FROM {analytics_predictions} WHERE modelid = :modelid";
        $updatesql = "UPDATE {analytics_prediction_actions}
                         SET actionname = :newaction
                       WHERE predictionid IN ($subsql) AND actionname = :oldaction";

        $DB->execute($updatesql, $params + ['modelid' => $model->id]);
    }
}

/**
 * Convert the site settings for the 'hub' component in the config_plugins table.
 *
 * @param stdClass $hubconfig Settings loaded for the 'hub' component.
 * @param string $huburl The URL of the hub to use as the valid one in case of conflict.
 * @return stdClass List of new settings to be applied (including null values to be unset).
 */
function upgrade_convert_hub_config_site_param_names(stdClass $hubconfig, string $huburl): stdClass {

    $cleanhuburl = clean_param($huburl, PARAM_ALPHANUMEXT);
    $converted = [];

    foreach ($hubconfig as $oldname => $value) {
        if (preg_match('/^site_([a-z]+)([A-Za-z0-9_-]*)/', $oldname, $matches)) {
            $newname = 'site_'.$matches[1];

            if ($oldname === $newname) {
                // There is an existing value with the new naming convention already.
                $converted[$newname] = $value;

            } else if (!array_key_exists($newname, $converted)) {
                // Add the value under a new name and mark the original to be unset.
                $converted[$newname] = $value;
                $converted[$oldname] = null;

            } else if ($matches[2] === '_'.$cleanhuburl) {
                // The new name already exists, overwrite only if coming from the valid hub.
                $converted[$newname] = $value;
                $converted[$oldname] = null;

            } else {
                // Just unset the old value.
                $converted[$oldname] = null;
            }

        } else {
            // Not a hub-specific site setting, just keep it.
            $converted[$oldname] = $value;
        }
    }

    return (object) $converted;
}

/**
 * Fix the incorrect default values inserted into analytics contextids field.
 */
function upgrade_analytics_fix_contextids_defaults() {
    global $DB;

    $select = $DB->sql_compare_text('contextids') . ' = :zero OR ' . $DB->sql_compare_text('contextids') . ' = :null';
    $params = ['zero' => '0', 'null' => 'null'];
    $DB->execute("UPDATE {analytics_models} set contextids = null WHERE " . $select, $params);
}

/**
 * Upgrade core licenses shipped with Moodle.
 */
function upgrade_core_licenses() {
    global $CFG, $DB;

    $corelicenses = [];

    $license = new stdClass();
    $license->shortname = 'unknown';
    $license->fullname = 'Licence not specified';
    $license->source = '';
    $license->enabled = 1;
    $license->version = '2010033100';
    $license->custom = 0;
    $corelicenses[] = $license;

    $license = new stdClass();
    $license->shortname = 'allrightsreserved';
    $license->fullname = 'All rights reserved';
    $license->source = 'https://en.wikipedia.org/wiki/All_rights_reserved';
    $license->enabled = 1;
    $license->version = '2010033100';
    $license->custom = 0;
    $corelicenses[] = $license;

    $license = new stdClass();
    $license->shortname = 'public';
    $license->fullname = 'Public domain';
    $license->source = 'https://en.wikipedia.org/wiki/Public_domain';
    $license->enabled = 1;
    $license->version = '2010033100';
    $license->custom = 0;
    $corelicenses[] = $license;

    $license = new stdClass();
    $license->shortname = 'cc';
    $license->fullname = 'Creative Commons';
    $license->source = 'https://creativecommons.org/licenses/by/3.0/';
    $license->enabled = 1;
    $license->version = '2010033100';
    $license->custom = 0;
    $corelicenses[] = $license;

    $license = new stdClass();
    $license->shortname = 'cc-nd';
    $license->fullname = 'Creative Commons - NoDerivs';
    $license->source = 'https://creativecommons.org/licenses/by-nd/3.0/';
    $license->enabled = 1;
    $license->version = '2010033100';
    $license->custom = 0;
    $corelicenses[] = $license;

    $license = new stdClass();
    $license->shortname = 'cc-nc-nd';
    $license->fullname = 'Creative Commons - No Commercial NoDerivs';
    $license->source = 'https://creativecommons.org/licenses/by-nc-nd/3.0/';
    $license->enabled = 1;
    $license->version = '2010033100';
    $license->custom = 0;
    $corelicenses[] = $license;

    $license = new stdClass();
    $license->shortname = 'cc-nc';
    $license->fullname = 'Creative Commons - No Commercial';
    $license->source = 'https://creativecommons.org/licenses/by-nc/3.0/';
    $license->enabled = 1;
    $license->version = '2010033100';
    $license->custom = 0;
    $corelicenses[] = $license;

    $license = new stdClass();
    $license->shortname = 'cc-nc-sa';
    $license->fullname = 'Creative Commons - No Commercial ShareAlike';
    $license->source = 'https://creativecommons.org/licenses/by-nc-sa/3.0/';
    $license->enabled = 1;
    $license->version = '2010033100';
    $license->custom = 0;
    $corelicenses[] = $license;

    $license = new stdClass();
    $license->shortname = 'cc-sa';
    $license->fullname = 'Creative Commons - ShareAlike';
    $license->source = 'https://creativecommons.org/licenses/by-sa/3.0/';
    $license->enabled = 1;
    $license->version = '2010033100';
    $license->custom = 0;
    $corelicenses[] = $license;

    foreach ($corelicenses as $corelicense) {
        // Check for current license to maintain idempotence.
        $currentlicense = $DB->get_record('license', ['shortname' => $corelicense->shortname]);
        if (!empty($currentlicense)) {
            $corelicense->id = $currentlicense->id;
            // Remember if the license was enabled before upgrade.
            $corelicense->enabled = $currentlicense->enabled;
            $DB->update_record('license', $corelicense);
        } else if (!isset($CFG->upgraderunning) || during_initial_install()) {
            // Only install missing core licenses if not upgrading or during initial install.
            $DB->insert_record('license', $corelicense);
        }
    }

    // Add sortorder to all licenses.
    $licenses = $DB->get_records('license');
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
