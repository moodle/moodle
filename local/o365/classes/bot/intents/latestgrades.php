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
 * Class latestgrades implements bot intent interface for student-latest-grades intent.
 *
 * @package local_o365
 * @author  Enovation Solutions
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2016 onwards Microsoft Open Technologies, Inc. (http://msopentech.com/)
 */

namespace local_o365\bot\intents;

defined('MOODLE_INTERNAL') || die();

/**
 * Class latestgrades implements bot intent interface for student-latest-grades intent.
 */
class latestgrades implements \local_o365\bot\intents\intentinterface {

    /**
     * Gets a message for student with their latest grades.
     *
     * @param string $language - Message language
     * @param mixed $entities - Intent entities. Gives student name.
     * @return array|string - Bot message structure with data
     */
    public static function get_message($language, $entities = null) {
        global $USER, $DB, $OUTPUT;
        $listitems = [];
        $warnings = [];
        $listtitle = '';
        $message = '';

        $activities = [
                'assign',
                'quiz'
        ];
        list($activitiessql, $activitiesparams) = $DB->get_in_or_equal($activities, SQL_PARAMS_NAMED);
        $sql = "SELECT g.id, gi.itemmodule, gi.iteminstance, g.finalgrade, g.timemodified
                  FROM {grade_grades} g
                  JOIN {grade_items} gi ON gi.id = g.itemid
                 WHERE g.userid = :userid AND gi.itemmodule $activitiessql AND g.finalgrade IS NOT NULL
              ORDER BY g.timemodified DESC";
        $sqlparams = array_merge(['userid' => $USER->id], $activitiesparams);
        $grades = $DB->get_records_sql($sql, $sqlparams, 0, self::DEFAULT_LIMIT_NUMBER);
        if (empty($grades)) {
            $message = get_string_manager()->get_string('no_grades_found', 'local_o365', null, $language);
            $warnings[] = array(
                    'item' => 'grades',
                    'itemid' => 0,
                    'warningcode' => '1',
                    'message' => 'No grades found'
            );
        } else {
            $message = get_string_manager()->get_string('list_of_recent_grades', 'local_o365', null, $language);
            foreach ($grades as $grade) {
                $cm = get_coursemodule_from_instance($grade->itemmodule, $grade->iteminstance);
                $url = new \moodle_url("/mod/{$grade->itemmodule}/view.php", ['id' => $cm->id]);
                $subtitledata = number_format((float)$grade->finalgrade, 1, '.', '');
                $grade = array(
                        'title' => $cm->name,
                        'subtitle' => get_string_manager()->get_string('your_grade', 'local_o365', $subtitledata, $language),
                        'icon' => $OUTPUT->image_url('icon', $grade->itemmodule)->out(),
                        'action' => $url->out(false),
                        'actionType' => 'openUrl'
                );
                $listitems[] = $grade;
            }
        }
        return array(
                'message' => $message,
                'listTitle' => $listtitle,
                'listItems' => $listitems,
                'warnings' => $warnings
        );
    }
}
