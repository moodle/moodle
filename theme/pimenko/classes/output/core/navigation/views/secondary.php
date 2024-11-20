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

namespace theme_pimenko\output\core\navigation\views;

use navigation_node;
use format_horizontaltabs;
use theme_config;
use context_course;

/**
 * custom secondary menu
 *
 * @package     theme_pimenko
 * @category    navigation
 * @copyright   2021 onwards Adrian Greeve | Pimenko
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class secondary extends \core\navigation\views\secondary {

    /**
     * Load the course secondary navigation. Since we are sourcing all the info from existing objects that already do
     * the relevant checks, we don't do it again here.
     *
     * @param navigation_node|null $rootnode The node where the course navigation nodes should be added into as children.
     *                                       If not explicitly defined, the nodes will be added to the secondary root
     *                                       node by default.
     */
    protected function load_course_navigation(?navigation_node $rootnode = null): void {
        global $SITE, $OUTPUT, $USER, $DB;

        $rootnode = $rootnode ?? $this;
        $course = $this->page->course;
        // Initialise the main navigation and settings nav.
        // It is important that this is done before we try anything.
        $settingsnav = $this->page->settingsnav;
        $navigation = $this->page->navigation;

        $url = new \moodle_url('/course/view.php', ['id' => $course->id]);
        $pix = null;
        if ($course->format == 'horizontaltabs') {
            $firstnodeidentifier = get_string('learn', 'format_horizontaltabs');
            $pix = new \pix_icon('t/grades', $firstnodeidentifier);
            $system = \core\output\icon_system::instance(\core\output\icon_system::STANDARD);
            $firstnodeidentifier = $system->render_pix_icon($OUTPUT, $pix) . $firstnodeidentifier;
        } else if ($course->format == 'digidagotabs') {
            $firstnodeidentifier = get_string('learn', 'format_digidagotabs');
            $pix = new \pix_icon('t/grades', $firstnodeidentifier);
            $system = \core\output\icon_system::instance(\core\output\icon_system::STANDARD);
            $firstnodeidentifier = $system->render_pix_icon($OUTPUT, $pix) . $firstnodeidentifier;
        } else {
            $firstnodeidentifier = get_string('course');
        }

        $issitecourse = $course->id == $SITE->id;
        if ($issitecourse) {
            $firstnodeidentifier = get_string('home');
            if ($frontpage = $settingsnav->get('frontpage')) {
                $settingsnav = $frontpage;
            }
        }
        $rootnode->add($firstnodeidentifier, $url, self::TYPE_COURSE, null, 'coursehome');

        // Add custom secondary menu for digidagotabs and horizontaltabs course format.
        if ($course->format == 'digidagotabs' || $course->format == 'horizontaltabs') {
            $courseformat = course_get_format($course->id);
            $tabs = $courseformat->get_tabs();
            foreach ($tabs as $tab) {
                if ($tab->icon) {
                    $text = $OUTPUT->render_custom_pix($OUTPUT, $tab->icon) . $tab->name;
                } else {
                    $text = $tab->name;
                }
                $rootnode->add($text, $tab->url, self::TYPE_SECTION, null, $tab->name);
                $sectiontabid = (int) filter_input(INPUT_GET, 'sectiontab', FILTER_SANITIZE_URL);
                if ($sectiontabid == $tab->url->get_param('sectiontab') && $tab->url->get_param('sectiontab') != null) {
                    navigation_node::override_active_url($tab->url);
                }
            }
        }

        $nodes = $this->get_default_course_mapping();
        $nodesordered = $this->get_leaf_nodes($settingsnav, $nodes['settings'] ?? []);
        $nodesordered += $this->get_leaf_nodes($navigation, $nodes['navigation'] ?? []);
        $this->add_ordered_nodes($nodesordered, $rootnode);

        // Hide participants node with theme settings ask for it.
        $theme = theme_config::load('pimenko');

        $allowedtosee = false;

        if ($theme->settings->showparticipantscourse) {

            if (is_siteadmin($USER) && !is_role_switched($course->id)) {
                $allowedtosee = true;
            } else if (is_role_switched($course->id)) {
                $roleswitched = $DB->get_record('role', ['id' => $USER->access['rsw'][$this->context->path]]);
                if (strpos($theme->settings->listuserrole, $roleswitched->shortname) !== false) {
                    $allowedtosee = true;
                }
            } else {
                foreach (get_user_roles($this->context, $USER->id) as $role) {
                    if (strpos($theme->settings->listuserrole, $role->shortname) !== false) {
                        $allowedtosee = true;
                    }
                }
            }
        } else if (is_siteadmin($USER)) {
            if (!is_role_switched($course->id)) {
                $allowedtosee = true;
            }
        }

        if (!$allowedtosee) {
            $rootnode->children->remove('participants');
        }

        // Try to get any custom nodes defined by a user which may include containers.
        $expectedcourseadmin = $this->get_expected_course_admin_nodes();
        $courseadminnode = $settingsnav;
        if (!$issitecourse) {
            $courseadminnode = $settingsnav->get('courseadmin');
        }

        if ($courseadminnode) {
            foreach ($courseadminnode->children as $other) {
                if (array_search($other->key, $expectedcourseadmin) === false) {
                    $othernode = $this->get_first_action_for_node($other);
                    $recursivenode = $othernode && !$rootnode->get($othernode->key) ? $othernode : $other;
                    // Get the first node and check whether it's been added already.
                    // Also check if the first node is an external link. If it is, add all children.
                    $this->add_external_nodes_to_secondary($recursivenode, $recursivenode, $rootnode);
                }
            }
        }

        $coursecontext = \context_course::instance($course->id);
        if (has_capability('moodle/course:update', $coursecontext)) {
            $overflownode = $this->get_course_overflow_nodes($rootnode);
            if (is_null($overflownode)) {
                return;
            }
            $actionnode = $this->get_first_action_for_node($overflownode);
            // All additional nodes will be available under the 'Course reuse' page.
            $text = get_string('coursereuse');
            $rootnode->add($text, $actionnode->action, navigation_node::TYPE_COURSE, null, 'coursereuse',
                new \pix_icon('t/edit', $text));
        }
    }
}
