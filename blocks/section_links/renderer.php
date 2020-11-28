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
 * Renderer for the section links block.
 *
 * @since     Moodle 2.5
 * @package   block_section_links
 * @copyright 2013 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Renderer for the section links block.
 *
 * @package   block_section_links
 * @copyright 2013 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_section_links_renderer extends plugin_renderer_base {

    /**
     * Render a series of section links.
     *
     * @param stdClass $course The course we are rendering for.
     * @param array $sections An array of section objects to render.
     * @param bool|int The section to provide a jump to link for.
     * @param bool $showsectionname Whether or not section name should be displayed.
     * @return string The HTML to display.
     */
    public function render_section_links(stdClass $course, array $sections, $jumptosection = false, $showsectionname = false) {
        $olparams = $showsectionname ? ['class' => 'unlist'] : ['class' => 'inline-list'];
        $html = html_writer::start_tag('ol', $olparams);
        foreach ($sections as $section) {
            $attributes = array();
            if (!$section->visible) {
                $attributes['class'] = 'dimmed';
            }
            $html .= html_writer::start_tag('li');
            $sectiontext = $section->section;
            if ($showsectionname) {
                $sectiontext .= ': ' . $section->name;
            }
            if ($section->highlight) {
                $sectiontext = html_writer::tag('strong', $sectiontext);
            }
            $html .= html_writer::link(course_get_url($course, $section->section), $sectiontext, $attributes);
            $html .= html_writer::end_tag('li').' ';
        }
        $html .= html_writer::end_tag('ol');
        if ($jumptosection && isset($sections[$jumptosection])) {

            if ($course->format == 'weeks') {
                $linktext = new lang_string('jumptocurrentweek', 'block_section_links');
            } else if ($course->format == 'topics') {
                $linktext = new lang_string('jumptocurrenttopic', 'block_section_links');
            }

            $attributes = array();
            if (!$sections[$jumptosection]->visible) {
                $attributes['class'] = 'dimmed';
            }
            $html .= html_writer::link(course_get_url($course, $jumptosection), $linktext, $attributes);
        }

        return $html;
    }
}