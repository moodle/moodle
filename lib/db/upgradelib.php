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
        // the system default is to show a column with letters (and the course uses the defaults).
        $lettercolumnsql = '(gss.value is NULL OR ' . $DB->sql_compare_text('gss.value') .  ' <> \'0\')';
    } else {
        // the course displays a column with letters.
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
 * Delete orphaned records in block_positions
 */
function upgrade_block_positions() {
    global $DB;
    $id = 'id';
    if ($DB->get_dbfamily() !== 'mysql') {
        // Field block_positions.subpage has type 'char', it can not be compared to int in db engines except for mysql.
        $id = $DB->sql_concat('?', 'id');
    }
    $sql = "DELETE FROM {block_positions}
    WHERE pagetype IN ('my-index', 'user-profile') AND subpage NOT IN (SELECT $id FROM {my_pages})";
    $DB->execute($sql, ['']);
}

/**
 * Fix configdata in block instances that are using the old object class that has been removed (deprecated).
 */
function upgrade_fix_block_instance_configuration() {
    global $DB;

    $sql = "SELECT *
              FROM {block_instances}
             WHERE " . $DB->sql_isnotempty('block_instances', 'configdata', true, true);
    $blockinstances = $DB->get_recordset_sql($sql);
    foreach ($blockinstances as $blockinstance) {
        $configdata = base64_decode($blockinstance->configdata);
        list($updated, $configdata) = upgrade_fix_serialized_objects($configdata);
        if ($updated) {
            $blockinstance->configdata = base64_encode($configdata);
            $DB->update_record('block_instances', $blockinstance);
        }
    }
    $blockinstances->close();
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
