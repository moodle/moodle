<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Plugin administration pages are defined here.
 *
 * @package     local_aiquestions
 * @category    admin
 * @copyright   2023 Ruthy Salomon <ruthy.salomon@gmail.com> , Yedidia Klein <yedidia@openapp.co.il>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();
/**
 * Add the AI Questions menu to the course administration menu.
 *
 * @param settings_navigation $settingsnav
 * @param context $context
 */
function local_aiquestions_extend_settings_navigation($settingsnav, $context) {
    global $CFG, $PAGE, $USER;

    // Add the AI Questions menu to the course administration menu only if the user has the permission to add questions.
    if (has_capability('moodle/question:add', $context)) {

        if ($settingnode = $settingsnav->find('courseadmin', navigation_node::TYPE_COURSE)) {
            $strfather = get_string('aiquestions', 'local_aiquestions');
            $fathernode = navigation_node::create(
                $strfather,
                null,
                navigation_node::NODETYPE_BRANCH,
                'local_aiquestions_father',
                'local_aiquestions_father'
            );

            $settingnode->add_node($fathernode);
            $strlist = get_string('story', 'local_aiquestions');
            $url = new moodle_url('/local/aiquestions/story.php', array('courseid' => $PAGE->course->id));
            $listnode = navigation_node::create(
                $strlist,
                $url,
                navigation_node::NODETYPE_LEAF,
                'local_aiquestions_story',
                'local_aiquestions_story',
                new pix_icon('f/avi-24', $strlist)
            );

            if ($PAGE->url->compare($url, URL_MATCH_BASE)) {
                $listnode->make_active();
            }
            $fathernode->add_node($listnode);
        }
    }
}
