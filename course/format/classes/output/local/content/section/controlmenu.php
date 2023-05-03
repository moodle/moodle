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
 * @package   core_courseformat
 * @copyright 2020 Ferran Recio <ferran@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_courseformat\output\local\content\section;

use action_menu;
use action_menu_link_secondary;
use context_course;
use core\output\named_templatable;
use core_courseformat\base as course_format;
use core_courseformat\output\local\courseformat_named_templatable;
use moodle_url;
use pix_icon;
use renderable;
use section_info;
use stdClass;

/**
 * Base class to render section controls.
 *
 * @package   core_courseformat
 * @copyright 2020 Ferran Recio <ferran@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class controlmenu implements named_templatable, renderable {

    use courseformat_named_templatable;

    /** @var course_format the course format class */
    protected $format;

    /** @var section_info the course section class */
    protected $section;

    /**
     * Constructor.
     *
     * @param course_format $format the course format
     * @param section_info $section the section info
     */
    public function __construct(course_format $format, section_info $section) {
        $this->format = $format;
        $this->section = $section;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param \renderer_base $output typically, the renderer that's calling this function
     * @return array data context for a mustache template
     */
    public function export_for_template(\renderer_base $output): stdClass {

        $section = $this->section;

        $controls = $this->section_control_items();

        if (empty($controls)) {
            return new stdClass();
        }

        // Convert control array into an action_menu.
        $menu = new action_menu();
        $menu->set_kebab_trigger(get_string('edit'));
        $menu->attributes['class'] .= ' section-actions';
        foreach ($controls as $value) {
            $url = empty($value['url']) ? '' : $value['url'];
            $icon = empty($value['icon']) ? '' : $value['icon'];
            $name = empty($value['name']) ? '' : $value['name'];
            $attr = empty($value['attr']) ? [] : $value['attr'];
            $class = empty($value['pixattr']['class']) ? '' : $value['pixattr']['class'];
            $al = new action_menu_link_secondary(
                new moodle_url($url),
                new pix_icon($icon, '', null, ['class' => "smallicon " . $class]),
                $name,
                $attr
            );
            $menu->add($al);
        }

        $data = (object)[
            'menu' => $output->render($menu),
            'hasmenu' => true,
            'id' => $section->id,
        ];

        return $data;
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
        global $USER;

        $format = $this->format;
        $section = $this->section;
        $course = $format->get_course();
        $sectionreturn = $format->get_section_number();
        $user = $USER;

        $usecomponents = $format->supports_components();
        $coursecontext = context_course::instance($course->id);
        $numsections = $format->get_last_section_number();
        $isstealth = $section->section > $numsections;

        $baseurl = course_get_url($course, $sectionreturn);
        $baseurl->param('sesskey', sesskey());

        $controls = [];

        if (!$isstealth && has_capability('moodle/course:update', $coursecontext, $user)) {
            if ($section->section > 0
                && get_string_manager()->string_exists('editsection', 'format_'.$format->get_format())) {
                $streditsection = get_string('editsection', 'format_'.$format->get_format());
            } else {
                $streditsection = get_string('editsection');
            }

            $controls['edit'] = [
                'url'   => new moodle_url('/course/editsection.php', ['id' => $section->id, 'sr' => $sectionreturn]),
                'icon' => 'i/settings',
                'name' => $streditsection,
                'pixattr' => ['class' => ''],
                'attr' => ['class' => 'icon edit'],
            ];

            $duplicatesectionurl = clone($baseurl);
            $duplicatesectionurl->param('section', $section->section);
            $duplicatesectionurl->param('duplicatesection', $section->section);
            $controls['duplicate'] = [
                'url' => $duplicatesectionurl,
                'icon' => 't/copy',
                'name' => get_string('duplicate'),
                'pixattr' => ['class' => ''],
                'attr' => ['class' => 'icon duplicate'],
            ];
        }

        if ($section->section) {
            $url = clone($baseurl);
            if (!$isstealth) {
                if (has_capability('moodle/course:sectionvisibility', $coursecontext, $user)) {
                    $strhidefromothers = get_string('hidefromothers', 'format_' . $course->format);
                    $strshowfromothers = get_string('showfromothers', 'format_' . $course->format);
                    if ($section->visible) { // Show the hide/show eye.
                        $url->param('hide', $section->section);
                        $controls['visiblity'] = [
                            'url' => $url,
                            'icon' => 'i/hide',
                            'name' => $strhidefromothers,
                            'pixattr' => ['class' => ''],
                            'attr' => [
                                'class' => 'icon editing_showhide',
                                'data-sectionreturn' => $sectionreturn,
                                'data-action' => ($usecomponents) ? 'sectionHide' : 'hide',
                                'data-id' => $section->id,
                                'data-swapname' => $strshowfromothers,
                                'data-swapicon' => 'i/show',
                            ],
                        ];
                    } else {
                        $url->param('show',  $section->section);
                        $controls['visiblity'] = [
                            'url' => $url,
                            'icon' => 'i/show',
                            'name' => $strshowfromothers,
                            'pixattr' => ['class' => ''],
                            'attr' => [
                                'class' => 'icon editing_showhide',
                                'data-sectionreturn' => $sectionreturn,
                                'data-action' => ($usecomponents) ? 'sectionShow' : 'show',
                                'data-id' => $section->id,
                                'data-swapname' => $strhidefromothers,
                                'data-swapicon' => 'i/hide',
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
                $url = new moodle_url(
                    '/course/editsection.php',
                    [
                        'id' => $section->id,
                        'sr' => $sectionreturn,
                        'delete' => 1,
                        'sesskey' => sesskey(),
                    ]
                );
                $controls['delete'] = [
                    'url' => $url,
                    'icon' => 'i/delete',
                    'name' => $strdelete,
                    'pixattr' => ['class' => ''],
                    'attr' => [
                        'class' => 'icon editing_delete',
                        'data-action' => 'deleteSection',
                        'data-id' => $section->id,
                    ],
                ];
            }
        }
        if (
            has_any_capability([
                'moodle/course:movesections',
                'moodle/course:update',
                'moodle/course:sectionvisibility',
            ], $coursecontext)
        ) {
            $sectionlink = new moodle_url(
                '/course/view.php',
                ['id' => $course->id],
                "sectionid-{$section->id}-title"
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
