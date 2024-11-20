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
 * Contains the default section controls output class.
 *
 * @package   format_topics
 * @copyright 2020 Ferran Recio <ferran@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace format_topics\output\courseformat\content\section;

use core_courseformat\output\local\content\section\controlmenu as controlmenu_base;
use moodle_url;

/**
 * Base class to render a course section menu.
 *
 * @package   format_topics
 * @copyright 2020 Ferran Recio <ferran@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class controlmenu extends controlmenu_base {

    /** @var \core_courseformat\base the course format class */
    protected $format;

    /** @var \section_info the course section class */
    protected $section;

    /**
     * Generate the edit control items of a section.
     *
     * This method must remain public until the final deprecation of section_edit_control_items.
     *
     * @return array of edit control items
     */
    public function section_control_items() {

        $format = $this->format;
        $section = $this->section;
        $coursecontext = $format->get_context();

        $parentcontrols = parent::section_control_items();

        if ($section->is_orphan() || !$section->section) {
            return $parentcontrols;
        }

        $controls = [];
        if (has_capability('moodle/course:setcurrentsection', $coursecontext)) {
            $controls['highlight'] = $this->get_highlight_control();
        }

        // If the edit key exists, we are going to insert our controls after it.
        if (array_key_exists("edit", $parentcontrols)) {
            $merged = [];
            // We can't use splice because we are using associative arrays.
            // Step through the array and merge the arrays.
            foreach ($parentcontrols as $key => $action) {
                $merged[$key] = $action;
                if ($key == "edit") {
                    // If we have come to the edit key, merge these controls here.
                    $merged = array_merge($merged, $controls);
                }
            }

            return $merged;
        } else {
            return array_merge($controls, $parentcontrols);
        }
    }

    /**
     * Return the course url.
     *
     * @return moodle_url
     */
    protected function get_course_url(): moodle_url {
        $format = $this->format;
        $section = $this->section;
        $course = $format->get_course();
        $sectionreturn = $format->get_sectionnum();

        if ($sectionreturn) {
            $url = course_get_url($course, $section->section);
        } else {
            $url = course_get_url($course);
        }
        $url->param('sesskey', sesskey());
        return $url;
    }

    /**
     * Return the specific section highlight action.
     *
     * @return array the action element.
     */
    protected function get_highlight_control(): array {
        $format = $this->format;
        $section = $this->section;
        $course = $format->get_course();
        $sectionreturn = $format->get_sectionnum();
        $url = $this->get_course_url();
        if (!is_null($sectionreturn)) {
            $url->param('sectionid', $format->get_sectionid());
        }

        $highlightoff = get_string('highlightoff');
        $highlightofficon = 'i/marked';

        $highlighton = get_string('highlight');
        $highlightonicon = 'i/marker';

        if ($course->marker == $section->section) {  // Show the "light globe" on/off.
            $url->param('marker', 0);
            $result = [
                'url' => $url,
                'icon' => $highlightofficon,
                'name' => $highlightoff,
                'pixattr' => ['class' => ''],
                'attr' => [
                    'class' => 'editing_highlight',
                    'data-action' => 'sectionUnhighlight',
                    'data-sectionreturn' => $sectionreturn,
                    'data-id' => $section->id,
                    'data-icon' => $highlightofficon,
                    'data-swapname' => $highlighton,
                    'data-swapicon' => $highlightonicon,
                ],
            ];
        } else {
            $url->param('marker', $section->section);
            $result = [
                'url' => $url,
                'icon' => $highlightonicon,
                'name' => $highlighton,
                'pixattr' => ['class' => ''],
                'attr' => [
                    'class' => 'editing_highlight',
                    'data-action' => 'sectionHighlight',
                    'data-sectionreturn' => $sectionreturn,
                    'data-id' => $section->id,
                    'data-icon' => $highlightonicon,
                    'data-swapname' => $highlightoff,
                    'data-swapicon' => $highlightofficon,
                ],
            ];
        }
        return $result;
    }
}
