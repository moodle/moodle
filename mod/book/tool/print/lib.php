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
 * Print lib
 *
 * @package    booktool_print
 * @copyright  2004-2011 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * Adds module specific settings to the settings block
 *
 * @param settings_navigation $settings The settings navigation object
 * @param navigation_node $node The node to add module settings to
 */
function booktool_print_extend_settings_navigation(settings_navigation $settings, navigation_node $node) {
    global $USER, $PAGE, $CFG, $DB, $OUTPUT;

    $params = $PAGE->url->params();
    if (empty($params['id']) or empty($params['chapterid'])) {
        return;
    }

    if (has_capability('booktool/print:print', $PAGE->cm->context)) {
        $url1 = new moodle_url('/mod/book/tool/print/index.php', array('id'=>$params['id']));
        $url2 = new moodle_url('/mod/book/tool/print/index.php', array('id'=>$params['id'], 'chapterid'=>$params['chapterid']));
        $action = new action_link($url1, get_string('printbook', 'booktool_print'), new popup_action('click', $url1));
        $node->add(get_string('printbook', 'booktool_print'), $action, navigation_node::TYPE_SETTING, null, null,
                new pix_icon('book', '', 'booktool_print', array('class'=>'icon')));
        $action = new action_link($url2, get_string('printchapter', 'booktool_print'), new popup_action('click', $url2));
        $node->add(get_string('printchapter', 'booktool_print'), $action, navigation_node::TYPE_SETTING, null, null,
                new pix_icon('chapter', '', 'booktool_print', array('class'=>'icon')));
    }
}

/**
 * Return read actions.
 *
 * Note: This is not used by new logging system. Event with
 *       crud = 'r' and edulevel = LEVEL_PARTICIPATING will
 *       be considered as view action.
 *
 * @return array
 */
function booktool_print_get_view_actions() {
    return array('print');
}
