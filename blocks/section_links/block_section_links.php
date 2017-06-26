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
 * This file contains the main class for the section links block.
 *
 * @package    block_section_links
 * @copyright  Jason Hardin
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Section links block class.
 *
 * @package    block_section_links
 * @copyright  Jason Hardin
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_section_links extends block_base {

    /**
     * Initialises the block instance.
     */
    public function init() {
        $this->title = get_string('pluginname', 'block_section_links');
    }

    /**
     * Returns an array of formats for which this block can be used.
     *
     * @return array
     */
    public function applicable_formats() {
        return array(
            'course-view-weeks' => true,
            'course-view-topics' => true
        );
    }

    /**
     * Generates the content of the block and returns it.
     *
     * If the content has already been generated then the previously generated content is returned.
     *
     * @return stdClass
     */
    public function get_content() {

        // The config should be loaded by now.
        // If its empty then we will use the global config for the section links block.
        if (isset($this->config)){
            $config = $this->config;
        } else{
            $config = get_config('block_section_links');
        }

        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->footer = '';
        $this->content->text   = '';

        if (empty($this->instance)) {
            return $this->content;
        }

        $course = $this->page->course;
        $courseformat = course_get_format($course);
        $numsections = $courseformat->get_last_section_number();
        $context = context_course::instance($course->id);

        // Course format options 'numsections' is required to display the block.
        if (empty($numsections)) {
            return $this->content;
        }

        // Prepare the increment value.
        if (!empty($config->numsections1) and ($numsections > $config->numsections1)) {
            $inc = $config->incby1;
        } else if ($numsections > 22) {
            $inc = 2;
        } else {
            $inc = 1;
        }
        if (!empty($config->numsections2) and ($numsections > $config->numsections2)) {
            $inc = $config->incby2;
        } else {
            if ($numsections > 40) {
                $inc = 5;
            }
        }

        // Prepare an array of sections to create links for.
        $sections = array();
        $canviewhidden = has_capability('moodle/course:update', $context);
        $coursesections = $courseformat->get_sections();
        $coursesectionscount = count($coursesections);
        $sectiontojumpto = false;
        for ($i = $inc; $i <= $coursesectionscount; $i += $inc) {
            if ($i > $numsections || !isset($coursesections[$i])) {
                continue;
            }
            $section = $coursesections[$i];
            if ($section->section && ($section->visible || $canviewhidden)) {
                $sections[$i] = (object)array(
                    'section' => $section->section,
                    'visible' => $section->visible,
                    'highlight' => false
                );
                if ($courseformat->is_section_current($section)) {
                    $sections[$i]->highlight = true;
                    $sectiontojumpto = $section->section;
                }
            }
        }

        if (!empty($sections)) {
            // Render the sections.
            $renderer = $this->page->get_renderer('block_section_links');
            $this->content->text = $renderer->render_section_links($this->page->course, $sections, $sectiontojumpto);
        }

        return $this->content;
    }
    /**
     * Returns true if this block has instance config.
     *
     * @return bool
     **/
    public function instance_allow_config() {
        return true;
    }

    /**
     * Returns true if this block has global config.
     *
     * @return bool
     */
    public function has_config() {
        return true;
    }
}


