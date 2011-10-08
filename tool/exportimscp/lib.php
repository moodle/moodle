<?php
// This file is part of Book plugin for Moodle - http://moodle.org/
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
 * IMSCP export lib
 *
 * @package    booktool
 * @subpackage print
 * @copyright  2011 Petr Skoda  {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

function booktool_exportimscp_extend_settings_navigation(settings_navigation $settingsnav, navigation_node $booknode) {
    global $USER, $PAGE, $CFG, $DB, $OUTPUT;

    if ($PAGE->cm->modname !== 'book') {
        return;
    }

    if (empty($PAGE->cm->context)) {
        $PAGE->cm->context = get_context_instance(CONTEXT_MODULE, $PAGE->cm->instance);
    }


    $params = $PAGE->url->params();

    if (empty($params['id']) or empty($params['chapterid'])) {
        return;
    }

    if (has_capability('booktool/exportimscp:export', $PAGE->cm->context)) {
        //TODO
        /// Enable the IMS CP button
        //$generateimscp = ($allowexport) ? '<a title="'.get_string('generateimscp', 'booktool_exportimscp').'" href="generateimscp.php?id='.$cm->id.'"><img class="bigicon" src="'.$OUTPUT->pix_url('generateimscp', 'mod_book').'" alt="'.get_string('generateimscp', 'book').'"></img></a>' : '';
    }
}