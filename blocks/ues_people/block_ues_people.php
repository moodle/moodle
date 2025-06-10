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
 * The main block file.
 *
 * @package    block_ues_people
 * @copyright  2014 Louisiana State University
 * @copyright  2014 Philip Cali, Jason Peak, Robert Russo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Block ues_people class definition.
 *
 * This block can be added to a UES enrolled course page to display of list of
 * students enrolled in the course with appropriate metadata.
 *
 * @package    block_ues_people
 * @copyright  2014 Louisiana State University
 * @copyright  2014 Philip Cali, Jason Peak, Chad Mazilly, Robert Russo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class block_ues_people extends block_list {

    /**
     * Core function used to initialize the block.
     */
    public function init() {
        $this->title = get_string('pluginname', 'block_ues_people');
    }

    /**
     * Core function, specifies where the block can be used.
     *
     * @return array
     */
    public function applicable_formats() {
        return array('course' => true, 'site' => false, 'my' => false);
    }

    /**
     * Allow the block to have a configuration page
     *
     * @return boolean
     */
    public function has_config() {
        return true;
    }

    /**
     * Used to generate the content for the block.
     *
     * @return string
     */
    public function get_content() {
        if ($this->content !== null) {
            return $this->content;
        }

        // Set up the global variables we'll need.
        global $PAGE, $COURSE, $OUTPUT, $CFG;

        // Set the context for later.
        $context = context_course::instance($COURSE->id);

        // Set permissions required for later.
        $permission = (
            has_capability('moodle/site:accessallgroups', $context) or
            has_capability('block/ues_people:viewmeta', $context)
        );

        if (!$permission) {
            // Set up the prerequisites.
            require_once($CFG->dirroot . '/enrol/ues/publiclib.php');
            ues::require_daos();

            // Grab the UES sections in the course.
            $sections = ues_section::from_course($COURSE);

            // Only set permissions if we have UES sections in the course.
            if (empty($sections)) {
                $permission = false;
            } else {
                $permission = ues_user::is_teacher_in($sections);
            }
        }

        // Set up the course content.
        $content = new stdClass;
        $content->icons = array();
        $content->footer = '';
        $this->content = $content;

        if ($permission) {
            // Set up the string.
            $str = get_string('canonicalname', 'block_ues_people');

            // Actually build the content and add it.
            $this->add_item_to_content([
                'lang_key' => $str,
                'icon_key' => 'i/users',
                'page' => 'index',
                'query_string' => ['id' => $COURSE->id]
            ]);
        }
        // Return the content.
        return $this->content;
    }

    /**
     * Builds and adds an item to the content container for the given params
     *
     * @param  array $params  [lang_key, icon_key, page, query_string]
     * @return void
     */
    private function add_item_to_content($params) {
        if ( ! array_key_exists('query_string', $params)) {
            $params['query_string'] = [];
        }
        $item = $this->build_item($params);
        $this->content->items[] = $item;
    }

    /**
     * Builds a content item (link) for the given params
     *
     * @param  array $params  [lang_key, icon_key, page, query_string]
     * @return string
     */
    private function build_item($params) {
        global $OUTPUT;

        $label = $params['lang_key'];

        $icon = $OUTPUT->pix_icon($params['icon_key'], $label, 'moodle', ['class' => 'icon']);

        return html_writer::link(
            new moodle_url('/blocks/ues_people/' . $params['page'] . '.php', $params['query_string']),
            $icon . $label
        );
    }
}
