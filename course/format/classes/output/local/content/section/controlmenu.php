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

namespace core_courseformat\output\local\content\section;

use action_menu;
use context_course;
use core_courseformat\base as course_format;
use core_courseformat\output\local\content\basecontrolmenu;
use moodle_url;
use section_info;

/**
 * Base class to render section controls.
 *
 * @package   core_courseformat
 * @copyright 2020 Ferran Recio <ferran@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class controlmenu extends basecontrolmenu {

    /**
     * Constructor.
     *
     * @param course_format $format the course format
     * @param section_info $section the section info
     */
    public function __construct(course_format $format, section_info $section) {
        parent::__construct($format, $section, null, $section->id);
    }

    /**
     * Generate the action menu element depending on the section.
     *
     * Sections controlled by a plugin will delegate the control menu to the delegated section class.
     *
     * @param \renderer_base $output typically, the renderer that's calling this function
     * @return action_menu|null the section action menu or null if no action menu is available
     */
    public function get_action_menu(\renderer_base $output): ?action_menu {

        if (!empty($this->menu)) {
            return $this->menu;
        }

        $sectiondelegate = $this->section->get_component_instance();
        if ($sectiondelegate) {
            return $sectiondelegate->get_section_action_menu($this->format, $this, $output);
        }
        return $this->get_default_action_menu($output);
    }

    /**
     * Generate the default section action menu.
     *
     * This method is public in case some block needs to modify the menu before output it.
     *
     * @param \renderer_base $output typically, the renderer that's calling this function
     * @return action_menu|null the section action menu
     */
    public function get_default_action_menu(\renderer_base $output): ?action_menu {
        $controls = $this->section_control_items();
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
    public function section_control_items() {
        global $USER, $PAGE;

        $format = $this->format;
        $section = $this->section;
        $course = $format->get_course();
        $sectionreturn = !is_null($format->get_sectionid()) ? $format->get_sectionnum() : null;
        $user = $USER;

        $usecomponents = $format->supports_components();
        $coursecontext = context_course::instance($course->id);
        $numsections = $format->get_last_section_number();
        $isstealth = $section->is_orphan();

        $baseurl = course_get_url($course, $sectionreturn);
        $baseurl->param('sesskey', sesskey());

        $controls = [];

        // Only show the view link if we are not already in the section view page.
        if ($PAGE->pagetype !== 'course-view-section-' . $course->format) {
            $controls['view'] = [
                'url'   => new moodle_url('/course/section.php', ['id' => $section->id]),
                'icon' => 'i/viewsection',
                'name' => get_string('view'),
                'pixattr' => ['class' => ''],
                'attr' => ['class' => 'icon view'],
            ];
        }

        if (!$isstealth && has_capability('moodle/course:update', $coursecontext, $user)) {
            $params = ['id' => $section->id];
            $params['sr'] = $section->section;
            if (get_string_manager()->string_exists('editsection', 'format_'.$format->get_format())) {
                $streditsection = get_string('editsection', 'format_'.$format->get_format());
            } else {
                $streditsection = get_string('editsection');
            }

            $controls['edit'] = [
                'url'   => new moodle_url('/course/editsection.php', $params),
                'icon' => 'i/settings',
                'name' => $streditsection,
                'pixattr' => ['class' => ''],
                'attr' => ['class' => 'icon edit'],
            ];

            if ($section->section) {
                $duplicatesectionurl = clone($baseurl);
                $duplicatesectionurl->param('sectionid', $section->id);
                $duplicatesectionurl->param('duplicatesection', 1);
                if (!is_null($sectionreturn)) {
                    $duplicatesectionurl->param('sr', $sectionreturn);
                }
                $controls['duplicate'] = [
                    'url' => $duplicatesectionurl,
                    'icon' => 't/copy',
                    'name' => get_string('duplicate'),
                    'pixattr' => ['class' => ''],
                    'attr' => ['class' => 'icon duplicate'],
                ];
            }
        }

        if ($section->section) {
            $url = clone($baseurl);
            if (!is_null($sectionreturn)) {
                $url->param('sectionid', $format->get_sectionid());
            }
            if (!$isstealth) {
                if (has_capability('moodle/course:sectionvisibility', $coursecontext, $user)) {
                    $strhidefromothers = get_string('hidefromothers', 'format_' . $course->format);
                    $strshowfromothers = get_string('showfromothers', 'format_' . $course->format);
                    if ($section->visible) { // Show the hide/show eye.
                        $url->param('hide', $section->section);
                        $controls['visibility'] = [
                            'url' => $url,
                            'icon' => 'i/show',
                            'name' => $strhidefromothers,
                            'pixattr' => ['class' => ''],
                            'attr' => [
                                'class' => 'icon editing_showhide',
                                'data-sectionreturn' => $sectionreturn,
                                'data-action' => ($usecomponents) ? 'sectionHide' : 'hide',
                                'data-id' => $section->id,
                                'data-icon' => 'i/show',
                                'data-swapname' => $strshowfromothers,
                                'data-swapicon' => 'i/hide',
                            ],
                        ];
                    } else {
                        $url->param('show',  $section->section);
                        $controls['visibility'] = [
                            'url' => $url,
                            'icon' => 'i/hide',
                            'name' => $strshowfromothers,
                            'pixattr' => ['class' => ''],
                            'attr' => [
                                'class' => 'icon editing_showhide',
                                'data-sectionreturn' => $sectionreturn,
                                'data-action' => ($usecomponents) ? 'sectionShow' : 'show',
                                'data-id' => $section->id,
                                'data-icon' => 'i/hide',
                                'data-swapname' => $strhidefromothers,
                                'data-swapicon' => 'i/show',
                            ],
                        ];
                    }
                }

                if (!$sectionreturn && has_capability('moodle/course:movesections', $coursecontext, $user)) {
                    if ($usecomponents) {
                        // This tool will appear only when the state is ready.
                        $url = clone ($baseurl);
                        $url->param('movesection', $section->section);
                        $url->param('section', $section->section);
                        $controls['movesection'] = [
                            'url' => $url,
                            'icon' => 'i/dragdrop',
                            'name' => get_string('move', 'moodle'),
                            'pixattr' => ['class' => ''],
                            'attr' => [
                                'class' => 'icon move waitstate',
                                'data-action' => 'moveSection',
                                'data-id' => $section->id,
                            ],
                        ];
                    }
                    // Legacy move up and down links for non component-based formats.
                    $url = clone($baseurl);
                    if ($section->section > 1) { // Add a arrow to move section up.
                        $url->param('section', $section->section);
                        $url->param('move', -1);
                        $strmoveup = get_string('moveup');
                        $controls['moveup'] = [
                            'url' => $url,
                            'icon' => 'i/up',
                            'name' => $strmoveup,
                            'pixattr' => ['class' => ''],
                            'attr' => ['class' => 'icon moveup whilenostate'],
                        ];
                    }

                    $url = clone($baseurl);
                    if ($section->section < $numsections) { // Add a arrow to move section down.
                        $url->param('section', $section->section);
                        $url->param('move', 1);
                        $strmovedown = get_string('movedown');
                        $controls['movedown'] = [
                            'url' => $url,
                            'icon' => 'i/down',
                            'name' => $strmovedown,
                            'pixattr' => ['class' => ''],
                            'attr' => ['class' => 'icon movedown whilenostate'],
                        ];
                    }
                }
            }

            if (course_can_delete_section($course, $section)) {
                if (get_string_manager()->string_exists('deletesection', 'format_'.$course->format)) {
                    $strdelete = get_string('deletesection', 'format_'.$course->format);
                } else {
                    $strdelete = get_string('deletesection');
                }
                $params = [
                    'id' => $section->id,
                    'delete' => 1,
                    'sesskey' => sesskey(),
                ];
                if (!is_null($sectionreturn)) {
                    $params['sr'] = $sectionreturn;
                }
                $url = new moodle_url(
                    '/course/editsection.php',
                    $params,
                );
                $controls['delete'] = [
                    'url' => $url,
                    'icon' => 'i/delete',
                    'name' => $strdelete,
                    'pixattr' => ['class' => ''],
                    'attr' => [
                        'class' => 'icon editing_delete text-danger',
                        'data-action' => 'deleteSection',
                        'data-id' => $section->id,
                    ],
                ];
            }
        }
        if (
            !$isstealth &&
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
                    'class' => 'icon',
                    'data-action' => 'permalink',
                ],
            ];
        }

        return $controls;
    }
}
