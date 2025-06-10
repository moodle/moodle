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
 * @package    block_ues_reprocess
 * @copyright  2014 Louisiana State University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

abstract class ues_event_handler {
    public static function helpdesk_course($help) {

        if (!has_capability('block/ues_reprocess:canreprocess', $help->context)) {
            return true;
        }

        $pluginname = get_string('pluginname', 'block_ues_reprocess');

        $params = array('id' => $help->courseid, 'type' => 'course');
        $url = new moodle_url('/blocks/ues_reprocess/reprocess.php', $params);

        $help->links[] = html_writer::link($url, $pluginname);

        return true;
    }

    public static function ues_course_settings_navigation($params) {
        global $OUTPUT;

        $nodes = $params[0];
        $instance = $params[1];

        $context = context_course::instance($instance->courseid);

        if (!has_capability('block/ues_reprocess:canreprocess', $context)) {
            return true;
        }

        $pluginname = get_string('reprocess', 'block_ues_reprocess');
        $params = array('id' => $instance->courseid, 'type' => 'course');

        $reprocesslink = new navigation_node(array(
            'text' => $pluginname,
            'shorttext' => $pluginname,
            'icon' => new pix_icon('i/users', $pluginname),
            'key' => 'block_ues_reprocess',
            'action' => new moodle_url('/blocks/ues_reprocess/reprocess.php', $params)
        ));

        $nodes->parent->add_node($reprocesslink, 'manageinstances');
        return true;
    }
}
