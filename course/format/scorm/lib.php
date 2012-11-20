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
 * This file contains main class for the course format SCORM
 *
 * @since     2.0
 * @package   format_scorm
 * @copyright 2009 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot. '/course/format/lib.php');

/**
 * Main class for the Scorm course format
 *
 * @package    format_scorm
 * @copyright  2012 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class format_scorm extends format_base {

    /**
     * The URL to use for the specified course
     *
     * @param int|stdClass $section Section object from database or just field course_sections.section
     *     if null the course view page is returned
     * @param array $options options for view URL. At the moment core uses:
     *     'navigation' (bool) if true and section has no separate page, the function returns null
     *     'sr' (int) used by multipage formats to specify to which section to return
     * @return null|moodle_url
     */
    public function get_view_url($section, $options = array()) {
        if (!empty($options['navigation']) && $section !== null) {
            return null;
        }
        return new moodle_url('/course/view.php', array('id' => $this->courseid));
    }

    /**
     * Loads all of the course sections into the navigation
     *
     * @param global_navigation $navigation
     * @param navigation_node $node The course node within the navigation
     */
    public function extend_course_navigation($navigation, navigation_node $node) {
        // Scorm course format does not extend course navigation
    }

    /**
     * Returns the list of blocks to be automatically added for the newly created course
     *
     * @return array of default blocks, must contain two keys BLOCK_POS_LEFT and BLOCK_POS_RIGHT
     *     each of values is an array of block names (for left and right side columns)
     */
    public function get_default_blocks() {
        return array(
            BLOCK_POS_LEFT => array(),
            BLOCK_POS_RIGHT => array('news_items', 'recent_activity', 'calendar_upcoming')
        );
    }

    /**
     * Allows course format to execute code on moodle_page::set_course()
     *
     * If user is on course view page and there is no scorm module added to the course
     * and the user has 'moodle/course:update' capability, redirect to create module
     * form. This function is executed before the output starts
     *
     * @param moodle_page $page instance of page calling set_course
     */
    public function page_set_course(moodle_page $page) {
        global $PAGE;
        if ($PAGE == $page && $page->has_set_url() &&
                $page->url->compare(new moodle_url('/course/view.php'), URL_MATCH_BASE)) {
            $modinfo = get_fast_modinfo($this->courseid);
            if (empty($modinfo->instances['scorm'])
                    && has_capability('moodle/course:update', context_course::instance($this->courseid))) {
                // Redirect to create a new activity
                $url = new moodle_url('/course/modedit.php',
                        array('course' => $this->courseid, 'section' => 0, 'add' => 'scorm'));
                redirect($url);
            }
        }
    }
}
