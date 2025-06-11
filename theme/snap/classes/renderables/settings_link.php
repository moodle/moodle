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
 * Settings link renderable.
 * @author    gthomas2
 * @copyright Copyright (c) 2015 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_snap\renderables;
use theme_snap\local;

class settings_link implements \renderable {

    /**
     * @var int $instanceid
     */
    public $instanceid;

    /**
     * @var bool $output - are we ok to output the settings block.
     */
    public $output = false;

    /**
     * @throws coding_exception
     */
    public function __construct() {
        global $PAGE, $COURSE;

        // Are we on the main course page?
        $oncoursepage = strpos($PAGE->pagetype, 'course-view') === 0;

        // For any format other than topics, weeks, or singleactivity, always output admin menu on main
        // course page.
        $formats = ['topics', 'weeks', 'singleactivity'];
        if ($oncoursepage && !empty($COURSE->format) && !in_array($COURSE->format, $formats)) {
            $this->set_admin_menu_instance();
            return;
        }

        // Page path blacklist for admin menu.
        $adminblockblacklist = ['/user/profile.php'];
        if (in_array(local::current_url_path(), $adminblockblacklist)) {
            return;
        }

        // Admin users always see the admin menu with the exception of blacklisted pages.
        // The admin menu shows up for other users if they are a teacher in the current course.
        if (!is_siteadmin()) {
            // We don't want students to see the admin menu ever.
            // Editing teachers are identified as people who can manage activities and non editing teachers as those who
            // can view the gradebook. As editing teachers are almost certain to also be able to view the gradebook, the
            // grader:view capability is checked first.
            $caps = ['gradereport/grader:view', 'moodle/course:manageactivities', 'moodle/site:configview'];
            $canmanageacts = has_any_capability($caps, $PAGE->context);
            $isstudent = !$canmanageacts && !is_role_switched($COURSE->id);
            if ($isstudent) {
                return;
            }
        }

        if (!$PAGE->blocks->is_block_present('settings')) {
            return;
        }

        $this->set_admin_menu_instance();

    }

    /**
     * Set admin menu instance, if required capability satisfied.
     *
     * @throws \coding_exception
     */
    private function set_admin_menu_instance() {
        global $PAGE;

        // Core Moodle API appears to be missing a 'get block by name' function.
        // Cycle through all regions and block instances until we find settings.
        foreach ($PAGE->blocks->get_regions() as $region) {
            foreach ($PAGE->blocks->get_blocks_for_region($region) as $block) {
                if (isset($block->instance) && $block->instance->blockname == 'settings') {
                    $this->instanceid = $block->instance->id;
                    break 2;
                }
            }
        }

        if (empty($this->instanceid)) {
            return;
        }

        if (!has_capability('moodle/block:view', \context_block::instance($this->instanceid))) {
            return;
        }

        $this->output = true;
    }
}
