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

require_once $CFG->libdir.'/gradelib.php';
require_once($CFG->libdir.'/xmlize.php');
require_once $CFG->dirroot.'/grade/lib.php';
require_once $CFG->dirroot.'/grade/import/lib.php';

function import_xml_grades($text, $course, &$error) {
    global $USER, $DB;

    $importcode = get_new_importcode();

    $status = true;

    $content = xmlize($text);

    if (!empty($content['results']['#']['result'])) {
        $results = $content['results']['#']['result'];

        foreach ($results as $i => $result) {
            $gradeidnumber = $result['#']['assignment'][0]['#'];
            if (!$grade_items = grade_item::fetch_all(array('idnumber'=>$gradeidnumber, 'courseid'=>$course->id))) {
                // gradeitem does not exist
                // no data in temp table so far, abort
                $status = false;
                $error  = get_string('errincorrectgradeidnumber', 'gradeimport_xml', $gradeidnumber);
                break;
            } else if (count($grade_items) != 1) {
                $status = false;
                $error  = get_string('errduplicategradeidnumber', 'gradeimport_xml', $gradeidnumber);
                break;
            } else {
                $grade_item = reset($grade_items);
            }

            // grade item locked, abort
            if ($grade_item->is_locked()) {
                $status = false;
                $error  = get_string('gradeitemlocked', 'grades');
                break;
            }

            // check if user exist and convert idnumber to user id
            $useridnumber = $result['#']['student'][0]['#'];
            if (!$user = $DB->get_record('user', array('idnumber' =>$useridnumber))) {
                // no user found, abort
                $status = false;
                $error = get_string('errincorrectuseridnumber', 'gradeimport_xml', $useridnumber);
                break;
            }

            // check if grade_grade is locked and if so, abort
            if ($grade_grade = new grade_grade(array('itemid'=>$grade_item->id, 'userid'=>$user->id))) {
                $grade_grade->grade_item =& $grade_item;
                if ($grade_grade->is_locked()) {
                    // individual grade locked, abort
                    $status = false;
                    $error  = get_string('gradelocked', 'grades');
                    break;
                }
            }

            $newgrade = new stdClass();
            $newgrade->itemid     = $grade_item->id;
            $newgrade->userid     = $user->id;
            $newgrade->importcode = $importcode;
            $newgrade->importer   = $USER->id;

            // check grade value exists and is a numeric grade
            if (isset($result['#']['score'][0]['#']) && $result['#']['score'][0]['#'] !== '-') {
                if (is_numeric($result['#']['score'][0]['#'])) {
                    $newgrade->finalgrade = $result['#']['score'][0]['#'];
                } else {
                    $status = false;
                    $error = get_string('badgrade', 'grades');
                    break;
                }
            } else {
                $newgrade->finalgrade = NULL;
            }

            // check grade feedback exists
            if (isset($result['#']['feedback'][0]['#'])) {
                $newgrade->feedback = $result['#']['feedback'][0]['#'];
            } else {
                $newgrade->feedback = NULL;
            }

            // insert this grade into a temp table
            $DB->insert_record('grade_import_values', $newgrade);
        }

    } else {
        // no results section found in xml,
        // assuming bad format, abort import
        $status = false;
        $error = get_string('errbadxmlformat', 'gradeimport_xml');
    }

    if ($status) {
        return $importcode;

    } else {
        import_cleanup($importcode);
        return false;
    }
}

/**
 * Download an XML grades file from a URL and import it into a course.
 *
 * @param stdClass $course target course
 * @param string $url source URL of the XML grades file
 * @param bool $feedback import feedback alongside grades
 * @param bool $verbose whether grade_import_commit prints progress
 * @return bool true if the grades were committed
 */
function gradeimport_xml_fetch_and_commit(stdClass $course, string $url, bool $feedback, bool $verbose): bool {
    global $CFG;
    require_once($CFG->libdir.'/filelib.php');

    // Large files are likely to take their time and memory. Let PHP know that we'll
    // take longer, and that the process should be recycled soon to free up memory.
    core_php_time_limit::raise();
    raise_memory_limit(MEMORY_EXTRA);

    $text = download_file_content($url);
    if ($text === false) {
        throw new \moodle_exception(
            'cannotreadfile',
            'error',
            $CFG->wwwroot . '/grade/import/xml/index.php?id=' . $course->id,
            $url
        );
    }

    $error = '';
    $importcode = import_xml_grades($text, $course, $error);
    if ($importcode === false) {
        throw new \moodle_exception(
            'errorduringimport',
            'gradeimport_xml',
            $CFG->wwwroot . '/grade/import/xml/index.php?id=' . $course->id,
            $error
        );
    }

    return grade_import_commit($course->id, $importcode, $feedback, $verbose);
}
