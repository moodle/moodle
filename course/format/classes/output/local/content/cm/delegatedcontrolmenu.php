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

namespace core_courseformat\output\local\content\cm;

use action_menu;
use context_course;
use core_courseformat\base as course_format;
use core_courseformat\output\local\content\basecontrolmenu;
use moodle_url;
use section_info;
use cm_info;

/**
 * Base class to render delegated section controls.
 *
 * @package   core_courseformat
 * @copyright 2024 Amaia Anabitarte <amaia@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class delegatedcontrolmenu extends basecontrolmenu {

    /**
     * Constructor.
     *
     * @param course_format $format the course format
     * @param section_info $section the section info
     * @param cm_info $mod the module info
     */
    public function __construct(course_format $format, section_info $section, cm_info $mod) {
        parent::__construct($format, $section, $mod, $section->id);
    }

    /**
     * Generate the default delegated section action menu.
     *
     * This method is public in case some block needs to modify the menu before output it.
     *
     * @param \renderer_base $output typically, the renderer that's calling this function
     * @return action_menu|null the action menu
     */
    public function get_default_action_menu(\renderer_base $output): ?action_menu {
        $controls = $this->delegated_control_items();
        return $this->format_controls($controls);
    }

    /**
     * Generate the edit control items of a section.
     *
     * It is not clear this kind of controls are still available in 4.0 so, for now, this
     * method is almost a clone of the previous section_control_items from the course/renderer.php.
     *
     * This method must remain public until the final deprecation of section_edit_control_items.
     *
     * @return array of edit control items
     */
    public function delegated_control_items() {
        global $USER;

        $format = $this->format;
        $section = $this->section;
        $cm = $this->mod;
        $course = $format->get_course();
        $sectionreturn = !is_null($format->get_sectionid()) ? $format->get_sectionnum() : null;
        $user = $USER;

        $usecomponents = $format->supports_components();
        $coursecontext = context_course::instance($course->id);

        $baseurl = course_get_url($course, $sectionreturn);
        $baseurl->param('sesskey', sesskey());

        $cmbaseurl = new moodle_url('/course/mod.php');
        $cmbaseurl->param('sesskey', sesskey());

        $hasmanageactivities = has_capability('moodle/course:manageactivities', $coursecontext);
        $isheadersection = $format->get_sectionid() == $section->id;

        $controls = [];

        // Only show the view link if we are not already in the section view page.
        if (!$isheadersection) {
            $controls['view'] = [
                'url'   => new moodle_url('/course/section.php', ['id' => $section->id]),
                'icon' => 'i/viewsection',
                'name' => get_string('view'),
                'pixattr' => ['class' => ''],
                'attr' => ['class' => 'view'],
            ];
        }

        if (has_capability('moodle/course:update', $coursecontext, $user)) {
            $params = ['id' => $section->id];
            $params['sr'] = $section->section;
            if (get_string_manager()->string_exists('editsection', 'format_'.$format->get_format())) {
                $streditsection = get_string('editsection', 'format_'.$format->get_format());
            } else {
                $streditsection = get_string('editsection');
            }

            // Edit settings goes to section settings form.
            $controls['edit'] = [
                'url'   => new moodle_url('/course/editsection.php', $params),
                'icon' => 'i/settings',
                'name' => $streditsection,
                'pixattr' => ['class' => ''],
                'attr' => ['class' => 'edit'],
            ];
        }

        // Hide/Show uses module functionality.
        // Hide/Show options will be available for subsections inside visible sections only.
        $parentsection = $cm->get_section_info();
        $availablevisibility = has_capability('moodle/course:sectionvisibility', $coursecontext, $user) && $parentsection->visible;
        if ($availablevisibility) {
            $url = clone($baseurl);
            if (!is_null($sectionreturn)) {
                $url->param('sr', $format->get_sectionid());
            }
            $strhidefromothers = get_string('hidefromothers', 'format_' . $course->format);
            $strshowfromothers = get_string('showfromothers', 'format_' . $course->format);
            if ($section->visible) { // Show the hide/show eye.
                $url->param('hide', $section->section);
                $controls['visiblity'] = [
                    'url' => $url,
                    'icon' => 'i/show',
                    'name' => $strhidefromothers,
                    'pixattr' => ['class' => ''],
                    'attr' => [
                        'class' => 'editing_showhide',
                        'data-sectionreturn' => $sectionreturn,
                        'data-action' => ($usecomponents) ? 'sectionHide' : 'hide',
                        'data-id' => $section->id,
                        'data-swapname' => $strshowfromothers,
                        'data-swapicon' => 'i/show',
                    ],
                ];
            } else {
                $url->param('show', $section->section);
                $controls['visiblity'] = [
                    'url' => $url,
                    'icon' => 'i/hide',
                    'name' => $strshowfromothers,
                    'pixattr' => ['class' => ''],
                    'attr' => [
                        'class' => 'editing_showhide',
                        'data-sectionreturn' => $sectionreturn,
                        'data-action' => ($usecomponents) ? 'sectionShow' : 'show',
                        'data-id' => $section->id,
                        'data-swapname' => $strhidefromothers,
                        'data-swapicon' => 'i/hide',
                    ],
                ];
            }
        }

        // Only show the move link if we are not already in the section view page.
        // Move (only for component compatible formats).
        if (!$isheadersection && $hasmanageactivities && $usecomponents) {
            $controls['move'] = [
                'url'   => new moodle_url('/course/mod.php', ['copy' => $cm->id]),
                'icon' => 'i/dragdrop',
                'name' => get_string('move'),
                'pixattr' => ['class' => ''],
                'attr' => [
                    'class' => 'editing_movecm ',
                    'data-action' => 'moveCm',
                    'data-id' => $cm->id,
                ],
            ];
        }

        // Delete deletes the module.
        if ($hasmanageactivities) {
            $url = clone($cmbaseurl);
            $url->param('delete', $cm->id);
            $url->param('sr', $cm->sectionnum);

            $controls['delete'] = [
                'url' => $url,
                'icon' => 't/delete',
                'name' => get_string('delete'),
                'pixattr' => ['class' => ''],
                'attr' => [
                    'class' => 'editing_delete text-danger',
                    'data-action' => ($usecomponents) ? 'cmDelete' : 'delete',
                    'data-sectionreturn' => $sectionreturn,
                    'data-id' => $cm->id,
                ],
            ];
        }

        // Add section page permalink.
        if (
            has_any_capability([
                'moodle/course:movesections',
                'moodle/course:update',
                'moodle/course:sectionvisibility',
            ], $coursecontext)
        ) {
            $sectionlink = new moodle_url(
                '/course/section.php',
                ['id' => $section->id]
            );
            $controls['permalink'] = [
                'url' => $sectionlink,
                'icon' => 'i/link',
                'name' => get_string('sectionlink', 'course'),
                'pixattr' => ['class' => ''],
                'attr' => [
                    'data-action' => 'permalink',
                ],
            ];
        }

        return $controls;
    }
}
