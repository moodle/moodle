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

use core\output\action_menu\link as action_menu_link;
use core\output\action_menu\link_secondary as action_menu_link_secondary;
use core\output\pix_icon;
use core_courseformat\output\local\content\section\controlmenu as controlmenu_base;
use core\url;

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
        $section = $this->section;
        $parentcontrols = parent::section_control_items();

        if ($section->is_orphan() || !$section->sectionnum) {
            return $parentcontrols;
        }

        if (!has_capability('moodle/course:setcurrentsection', $this->coursecontext)) {
            return $parentcontrols;
        }

        return $this->add_control_after($parentcontrols, 'edit', 'highlight', $this->get_section_highlight_item());
    }

    /**
     * Return the course url.
     *
     * @return url
     */
    #[\core\attribute\deprecated(
        since: '5.0',
        mdl: 'MDL-82767',
        reason: 'Not used anymore, use $this->format->get_update_url instead',
    )]
    protected function get_course_url(): url {
        \core\deprecation::emit_deprecation_if_present([self::class, __FUNCTION__]);
        $format = $this->format;
        $section = $this->section;
        $course = $format->get_course();
        $sectionreturn = $format->get_sectionnum();

        if ($sectionreturn) {
            $url = course_get_url($course, $section->sectionnum);
        } else {
            $url = course_get_url($course);
        }
        $url->param('sesskey', sesskey());
        return $url;
    }

    /**
     * Retrieves the view item for the section control menu.
     *
     * @return action_menu_link|null The menu item if applicable, otherwise null.
     */
    protected function get_section_highlight_item(): action_menu_link_secondary {
        $format = $this->format;
        $section = $this->section;
        $course = $format->get_course();
        $sectionreturn = $format->get_sectionnum();

        $highlightoff = get_string('highlightoff');
        $highlightofficon = 'i/marked';

        $highlighton = get_string('highlight');
        $highlightonicon = 'i/marker';

        if ($course->marker == $section->sectionnum) {  // Show the "light globe" on/off.
            $action = 'section_unhighlight';
            $icon = $highlightofficon;
            $name = $highlightoff;
            $attributes = [
                'class' => 'editing_highlight',
                'data-action' => 'sectionUnhighlight',
                'data-sectionreturn' => $sectionreturn,
                'data-id' => $section->id,
                'data-icon' => $highlightofficon,
                'data-swapname' => $highlighton,
                'data-swapicon' => $highlightonicon,
            ];
        } else {
            $action = 'section_highlight';
            $icon = $highlightonicon;
            $name = $highlighton;
            $attributes = [
                'class' => 'editing_highlight',
                'data-action' => 'sectionHighlight',
                'data-sectionreturn' => $sectionreturn,
                'data-id' => $section->id,
                'data-icon' => $highlightonicon,
                'data-swapname' => $highlightoff,
                'data-swapicon' => $highlightofficon,
            ];
        }

        $url = $this->format->get_update_url(
            action: $action,
            ids: [$section->id],
            returnurl: $this->baseurl,
        );

        return new action_menu_link_secondary(
                url: $url,
                icon: new pix_icon($icon, ''),
                text: $name,
                attributes: $attributes,
        );
    }

    /**
     * Return the specific section highlight action.
     *
     * @deprecated since Moodle 5.0
     * @todo Remove this method in Moodle 6.0 (MDL-83530).
     * @return array the action element.
     */
    #[\core\attribute\deprecated(
        replacement: 'get_section_highlight_item',
        since: '5.0',
        mdl: 'MDL-83527',
        reason: 'Wrong return type',
    )]
    protected function get_highlight_control(): array {
        \core\deprecation::emit_deprecation_if_present([self::class, __FUNCTION__]);
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
