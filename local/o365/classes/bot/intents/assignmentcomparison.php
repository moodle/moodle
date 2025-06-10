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
 * Class assignmentcomparison implements bot intent interface for student-assignment-comparison-results intent.
 *
 * @package local_o365
 * @author  Enovation Solutions
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2016 onwards Microsoft Open Technologies, Inc. (http://msopentech.com/)
 */

namespace local_o365\bot\intents;

defined('MOODLE_INTERNAL') || die();

/**
 * Class assignmentcomparison implements bot intent interface for student-assignment-comparison-results intent.
 */
class assignmentcomparison implements \local_o365\bot\intents\intentinterface {

    /**
     * Gets a message with details about user assignments with grades compared to class average
     *
     * @param string $language - Message language
     * @param mixed $entities - Intent entities (optional and not used at the moment)
     * @return array|string - Bot message structure with data
     */
    public static function get_message($language, $entities = null) {
        global $USER, $DB, $OUTPUT;
        $listitems = [];
        $warnings = [];
        $listtitle = '';
        $message = '';

        $sql = "SELECT gi.iteminstance, g.itemid, g.finalgrade, g.timemodified
                  FROM {grade_grades} g
                  JOIN {grade_items} gi ON gi.id = g.itemid
                 WHERE g.userid = :userid AND gi.itemmodule LIKE :assignstr AND g.finalgrade IS NOT NULL
              ORDER BY g.timemodified DESC";

        $sqlparams = ['assignstr' => 'assign', 'userid' => $USER->id];
        $assignments = $DB->get_records_sql($sql, $sqlparams, 0, self::DEFAULT_LIMIT_NUMBER);

        if (empty($assignments)) {
            $message = get_string_manager()->get_string('no_assignments_found', 'local_o365', null, $language);
            $warnings[] = array(
                    'item' => 'grades',
                    'itemid' => 0,
                    'warningcode' => '1',
                    'message' => 'No assignments found'
            );
        } else {
            $message = get_string_manager()->get_string('list_of_assignments_grades_compared', 'local_o365', null, $language);
            foreach ($assignments as $assign) {
                $cm = get_coursemodule_from_instance('assign', $assign->iteminstance);
                $coursecontext = \context_course::instance($cm->course);
                $course = get_course($cm->course);
                $group = groups_get_course_group($course);
                $participants = get_enrolled_users($coursecontext, '', $group, 'u.id', null, 0, 0, false);
                $participants = join(',', array_keys($participants));
                $url = new \moodle_url("/mod/assign/view.php", ['id' => $cm->id]);
                $sql = "SELECT g.itemid, COUNT(*) AS amount, SUM(g.finalgrade) AS sum
                          FROM {grade_items} gi
                          JOIN {grade_grades} g ON g.itemid = gi.id
                          JOIN {user} u ON u.id = g.userid
                         WHERE gi.itemmodule LIKE :assignstr
                               AND gi.iteminstance = :assignmentid
                               AND u.deleted = 0
                               AND g.finalgrade IS NOT NULL
                               AND u.id IN ($participants)
                      GROUP BY g.itemid";
                $sqlparams = ['assignstr' => 'assign', 'assignmentid' => $assign->iteminstance];
                $average = $DB->get_record_sql($sql, $sqlparams);
                $subtitledata = new \stdClass();
                $subtitledata->usergrade = number_format((float)$assign->finalgrade, 1, '.', '');
                $subtitledata->classgrade = number_format((float)($average->sum / $average->amount), 1, '.', '');
                $assignment = array(
                        'title' => $cm->name,
                        'subtitle' => get_string_manager()->get_string('your_grade_class_grade', 'local_o365', $subtitledata,
                                $language),
                        'icon' => $OUTPUT->image_url('icon', 'assign')->out(),
                        'action' => $url->out(false),
                        'actionType' => 'openUrl'
                );
                $listitems[] = $assignment;
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
