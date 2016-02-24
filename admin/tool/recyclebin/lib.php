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
 * Local lib code
 *
 * @package    local_recyclebin
 * @copyright  2015 Skylar Kelty <S.Kelty@kent.ac.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * Adds a recycle bin link to the course admin menu.
 *
 * @param  settings_navigation $nav     Nav menu
 * @param  context             $context Context of the menu
 * @return navigation_node              A new navigation mode to insert.
 */
function local_recyclebin_extend_settings_navigation(settings_navigation $nav, context $context) {
    global $PAGE;

    $url = null;
    $bin = null;
    $settingnode = null;

    // What context are we in?
    if ($context->contextlevel == \CONTEXT_COURSECAT) {
        // Check we can view the recycle bin.
        if (!has_capability('local/recyclebin:view_course', $context) || !\local_recyclebin\category::is_enabled()) {
            return null;
        }

        // Add a link to the category recyclebin.
        $bin = new \local_recyclebin\category($context->instanceid);
        $url = new moodle_url('/local/recyclebin/index.php', array(
            'contextid' => $context->id
        ));

        $settingnode = $nav->find('categorysettings', null);
    } else {
        // Only add this settings item on non-site course pages.
        if (!$PAGE->course || $PAGE->course->id == SITEID || !\local_recyclebin\course::is_enabled()) {
            return null;
        }

        // We might be in a mod page, etc.
        $coursectx = \context_course::instance($PAGE->course->id);

        // Check we can view the recycle bin.
        if (!has_capability('local/recyclebin:view_item', $coursectx)) {
            return null;
        }

        $bin = new \local_recyclebin\course($coursectx->instanceid);
        $url = new moodle_url('/local/recyclebin/index.php', array(
            'contextid' => $coursectx->id
        ));

        $settingnode = $nav->find('courseadmin', navigation_node::TYPE_COURSE);
    }

    if ($settingnode == null) {
        return;
    }

    // If we are set to auto-hide, check the number of items.
    $autohide = get_config('local_recyclebin', 'autohide');
    if ($autohide) {
        $items = $bin->get_items();
        if (empty($items)) {
            return null;
        }
    }

    // Add the recyclebin link.
    $pluginname = get_string('pluginname', 'local_recyclebin');

    $node = navigation_node::create(
        $pluginname,
        $url,
        navigation_node::NODETYPE_LEAF,
        'local_recyclebin',
        'local_recyclebin',
        new pix_icon('e/cleanup_messy_code', $pluginname)
    );

    if ($PAGE->url->compare($url, URL_MATCH_BASE)) {
        $node->make_active();
    }

    $settingnode->add_node($node);

    return $node;
}

/**
 * For pre-2.9 installations.
 *
 * @param settings_navigation $nav
 * @param context $context
 */
function local_recyclebin_extends_settings_navigation(settings_navigation $nav, context $context) {
    local_recyclebin_extend_settings_navigation($nav, $context);
}
