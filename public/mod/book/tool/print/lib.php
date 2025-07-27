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

/**
 * Adds module specific settings to the settings block
 *
 * @param settings_navigation $settings The settings navigation object
 * @param navigation_node $node The node to add module settings to
 */
function booktool_print_extend_settings_navigation(settings_navigation $settings, navigation_node $node) {
    global $OUTPUT;

    $params = $settings->get_page()->url->params();
    if (empty($params['id']) || empty($params['chapterid'])) {
        return;
    }

    if (has_capability('booktool/print:print', $settings->get_page()->cm->context)) {
        $url1 = new moodle_url('/mod/book/tool/print/index.php', ['id' => $params['id']]);
        $url2 = new moodle_url('/mod/book/tool/print/index.php', ['id' => $params['id'], 'chapterid' => $params['chapterid']]);
        $action = new action_link($url1, get_string('printbook', 'booktool_print'), new popup_action('click', $url1));
        $newwindowtext = html_writer::span(' (' . get_string('opensinnewwindow') . ')', 'visually-hidden');
        $externalicon = $OUTPUT->pix_icon('i/externallink', '', attributes: [
            'title' => get_string('opensinnewwindow'),
            'class' => 'ms-1',
        ]);

        $booknode = $node->add(
            // Add both an icon and visualy hidden text to indicate link opens in a new window
            // because Clean theme strips out the icon html.
            get_string('printbook', 'booktool_print') . $externalicon . $newwindowtext,
            $action,
            navigation_node::TYPE_SETTING,
            null,
            'printbook',
            new pix_icon('book', '', 'booktool_print', ['class' => 'icon'])
        );
        $booknode->set_force_into_more_menu(true);
        $action = new action_link($url2, get_string('printchapter', 'booktool_print'), new popup_action('click', $url2));
        $chapternode = $node->add(
            // Add both an icon and visualy hidden text to indicate link opens in a new window
            // because Clean theme strips out the icon html.
            get_string('printchapter', 'booktool_print') . $externalicon . $newwindowtext,
            $action,
            navigation_node::TYPE_SETTING,
            null,
            'printchapter',
            new pix_icon('chapter', '', 'booktool_print', ['class' => 'icon'])
        );
        $chapternode->set_force_into_more_menu(true);
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
    return ['print'];
}
