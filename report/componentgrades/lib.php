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
 *
 * Spreadsheet export report for assignments marked with advanced grading methods
 *
 * @package    report_componentgrades
 * @copyright  2014 Paul Nicholls
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * This function extends the module navigation with the report items
 *
 * @param navigation_node $navigation The navigation node to extend
 * @param cm_info $cm
 */
function report_componentgrades_extend_navigation_module(navigation_node $navigation, cm_info $cm) {
    $context = context_module::instance($cm->id);
    if ($cm->modname == 'assign' && has_capability('moodle/grade:edit', $context)) {
        $gradingmanager = get_grading_manager($context, 'mod_assign', 'submissions');
        $gradinginstance = null;
        switch ($gradingmanager->get_active_method()) {
            case 'rubric':
                $url = new moodle_url('/report/componentgrades/rubric.php', array('id' => $cm->course, 'modid' => $cm->id));
                $navigation->add(get_string('rubricgrades', 'report_componentgrades'), $url, navigation_node::TYPE_SETTING, null,
                        'rubricgrades');
                break;
            case 'guide':
                $url = new moodle_url('/report/componentgrades/guide.php', array('id' => $cm->course, 'modid' => $cm->id));
                $navigation->add(get_string('guidegrades', 'report_componentgrades'), $url, navigation_node::TYPE_SETTING, null,
                        'guidegrades');
                break;
            case 'btec':
                $url = new moodle_url('/report/componentgrades/btec.php', array('id' => $cm->course, 'modid' => $cm->id));
                $navigation->add(get_string('btecgrades', 'report_componentgrades'), $url,
                        navigation_node::TYPE_SETTING, null, 'btecgrades');
            break;
        }
    }
}
