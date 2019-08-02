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
 * Settings for the myoverview block
 *
 * @package    block_myoverview
 * @copyright  2019 Tom Dickman <tomdickman@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    require_once($CFG->dirroot . '/blocks/myoverview/lib.php');

    // Display Course Categories on Dashboard course items (cards, lists, summary items).
    $settings->add(new admin_setting_configcheckbox(
        'block_myoverview/displaycategories',
        get_string('displaycategories', 'block_myoverview'),
        get_string('displaycategories_help', 'block_myoverview'),
        1));

       $choices = array(BLOCK_MYOVERVIEW_VIEW_CARD => get_string('card', 'block_myoverview'),
        BLOCK_MYOVERVIEW_VIEW_LIST => get_string('list', 'block_myoverview'),
        BLOCK_MYOVERVIEW_VIEW_SUMMARY => get_string('summary', 'block_myoverview'));

    $settings->add(new admin_setting_configmulticheckbox(
        'block_myoverview/layouts',
        get_string('layouts', 'block_myoverview'),
        get_string('layouts_help', 'block_myoverview'),
        $choices,
        $choices));

}
