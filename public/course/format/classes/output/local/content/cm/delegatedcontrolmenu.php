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

use cm_info;
use core\context\course as context_course;
use core\context\module as context_module;
use core\output\action_menu;
use core\output\action_menu\link;
use core\output\action_menu\link_secondary;
use core\output\renderer_base;
use core_courseformat\base as course_format;
use core_courseformat\output\local\content\basecontrolmenu;
use core\output\pix_icon;
use core\url;
use section_info;

/**
 * Base class to render delegated section controls.
 *
 * @package   core_courseformat
 * @copyright 2024 Amaia Anabitarte <amaia@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class delegatedcontrolmenu extends basecontrolmenu {
    /** @var context_module|null modcontext the module context if any */
    protected ?context_module $modcontext = null;

    /** @var bool $canmanageactivities Optimization to know if the user can manage activities */
    protected bool $canmanageactivities;

    /**
     * Constructor.
     *
     * @param course_format $format the course format
     * @param section_info $section the section info
     * @param cm_info $mod the module info
     */
    public function __construct(course_format $format, section_info $section, cm_info $mod) {
        parent::__construct($format, $section, $mod, $section->id);

        $this->modcontext = context_module::instance($mod->id);
        $this->canmanageactivities = has_capability('moodle/course:manageactivities', $this->modcontext);
    }

    /**
     * Generate the default delegated section action menu.
     *
     * This method is public in case some block needs to modify the menu before output it.
     *
     * @param renderer_base $output typically, the renderer that's calling this function
     * @return action_menu|null the action menu
     */
    public function get_default_action_menu(renderer_base $output): ?action_menu {
        $controls = $this->delegated_control_items();
        return $this->format_controls($controls);
    }

    /**
     * Generate the edit control items of a section.
     *
     * @return array of edit control items
     */
    public function delegated_control_items() {
        // TODO remove this if as part of MDL-83530.
        if (!$this->format->supports_components()) {
            return $this->delegated_control_items_legacy();
        }

        $controls = [];
        $controls['view'] = $this->get_section_view_item();
        $controls['edit'] = $this->get_section_edit_item();
        $controls['duplicate'] = $this->get_section_duplicate_item();
        $controls['visibility'] = $this->get_section_visibility_item();
        $controls['movesection'] = $this->get_cm_move_item();
        $controls['permalink'] = $this->get_section_permalink_item();
        $controls['delete'] = $this->get_cm_delete_item();

        return $controls;
    }

    /**
     * Retrieves the view item for the section control menu.
     *
     * @return link|null The menu item if applicable, otherwise null.
     */
    protected function get_section_view_item(): ?link {
        // Let third-party plugins decide if they want to show the view link overriding this method.
        return null;
    }

    /**
     * Retrieves the edit item for the section control menu.
     *
     * @return link|null The menu item if applicable, otherwise null.
     */
    protected function get_section_edit_item(): ?link {
        if (!has_capability('moodle/course:update', $this->coursecontext)) {
            return null;
        }

        $url = new url(
            '/course/editsection.php',
            [
                'id' => $this->section->id,
                'sr' => $this->section->sectionnum,
            ]
        );

        return new link_secondary(
                url: $url,
                icon: new pix_icon('i/settings', ''),
                text: get_string('editsection'),
                attributes: ['class' => 'edit'],
        );
    }



    /**
     * Generates the move item for a course module.
     *
     * @return link|null The menu item if applicable, otherwise null.
     */
    protected function get_cm_move_item(): ?link {
        // Only show the move link if we are not already in the section view page.
        if (
            !$this->canmanageactivities
            || $this->format->get_sectionid() == $this->section->id
        ) {
            return null;
        }

        // The move action uses visual elements on the course page.
        $url = new url('/course/mod.php', ['sesskey' => sesskey()]);

        $sectionnumreturn = $this->format->get_sectionnum();
        if ($sectionnumreturn !== null) {
            $url->param('sr', $sectionnumreturn);
        }

        return new link_secondary(
            url: $url,
            icon: new pix_icon('i/dragdrop', ''),
            text: get_string('move'),
            attributes: [
                // This tool requires ajax and will appear only when the frontend state is ready.
                'class' => 'editing_movecm waitstate',
                'data-action' => 'moveCm',
                'data-id' => $this->mod->id,
            ],
        );
    }

    /**
     * Retrieves the duplicate item for the section control menu.
     *
     * @return link|null The menu item if applicable, otherwise null.
     */
    protected function get_section_duplicate_item(): ?link {
        $capabilities = ['moodle/course:update', 'moodle/backup:backuptargetimport', 'moodle/restore:restoretargetimport'];
        if (!has_all_capabilities($capabilities, $this->coursecontext)) {
            return null;
        }
        if (!plugin_supports('mod', $this->mod->modname, FEATURE_BACKUP_MOODLE2)) {
            return null;
        }
        if (!course_allowed_module($this->mod->get_course(), $this->mod->modname)) {
            return null;
        }

        $url = $this->format->get_update_url(
            action: 'cm_duplicate',
            ids: [$this->mod->id],
            returnurl: $this->baseurl,
        );

        return new link_secondary(
            url: $url,
            icon: new pix_icon('t/copy', ''),
            text: get_string('duplicate'),
        );
    }

    /**
     * Retrieves the get_section_visibility_menu_item item for the section control menu.
     *
     * @return link|null The menu item if applicable, otherwise null.
     */
    protected function get_section_visibility_item(): ?link {
        // To avoid exponential complexity, we only allow subsection visibility actions
        // when the parent section is visible.
        $parentsection = $this->mod->get_section_info();
        if (
            $this->section->sectionnum == 0
            || !$parentsection->visible
            || !has_capability('moodle/course:sectionvisibility', $this->coursecontext)
            || !has_capability('moodle/course:activityvisibility', $this->modcontext)
        ) {
            return null;
        }

        $sectionreturn = $this->format->get_sectionnum();

        $strhide = get_string('hide');
        $strshow = get_string('show');

        if ($this->section->visible) {
            $action = 'section_hide';
            $icon = 'i/show';
            $name = $strhide;
            $attributes = [
                'class' => 'icon editing_showhide',
                'data-sectionreturn' => $sectionreturn,
                'data-action' => 'sectionHide',
                'data-id' => $this->section->id,
                'data-icon' => 'i/show',
                'data-swapname' => $strshow,
                'data-swapicon' => 'i/hide',
            ];
        } else {
            $action = 'section_show';
            $icon = 'i/hide';
            $name = $strshow;
            $attributes = [
                'class' => 'editing_showhide',
                'data-sectionreturn' => $sectionreturn,
                'data-action' => 'sectionShow',
                'data-id' => $this->section->id,
                'data-icon' => 'i/hide',
                'data-swapname' => $strhide,
                'data-swapicon' => 'i/show',
            ];
        }

        $url = $this->format->get_update_url(
            action: $action,
            ids: [$this->section->id],
            returnurl: $this->baseurl,
        );

        return new link_secondary(
            url: $url,
            icon: new pix_icon($icon, ''),
            text: $name,
            attributes: $attributes,
        );
    }

    /**
     * Retrieves the permalink item for the section control menu.
     *
     * @return link|null The menu item if applicable, otherwise null.
     */
    protected function get_section_permalink_item(): ?link {
        if (!has_any_capability(
                [
                    'moodle/course:movesections',
                    'moodle/course:update',
                    'moodle/course:sectionvisibility',
                ],
                $this->coursecontext
            )
        ) {
            return null;
        }

        $parentsection = $this->mod->get_section_info();
        $url = new url(
            '/course/section.php',
            ['id' => $parentsection->id],
            'section-' . $this->section->sectionnum,
        );
        return new link_secondary(
            url: $url,
            icon: new pix_icon('i/link', ''),
            text: get_string('sectionlink', 'course'),
            attributes: [
                'class' => 'permalink',
                'data-action' => 'permalink',
            ],
        );
    }

    /**
     * Generates the delete item for a course module.
     *
     * @return link|null The menu item if applicable, otherwise null.
     */
    protected function get_cm_delete_item(): ?link {
        if (!$this->canmanageactivities) {
            return null;
        }

        $url = $this->format->get_update_url(
            action: 'cm_delete',
            ids: [$this->mod->id],
            returnurl: $this->baseurl,
        );

        return new link_secondary(
            url: $url,
            icon: new pix_icon('t/delete', ''),
            text: get_string('delete'),
            attributes: [
                'class' => 'editing_delete text-danger',
                'data-action' => 'cmDelete',
                'data-sectionreturn' => $this->format->get_sectionnum(),
                'data-id' => $this->mod->id,
            ],
        );
    }

    /**
     * Generate the edit control items of a section.
     *
     * It is not clear this kind of controls are still available in 4.0 so, for now, this
     * method is almost a clone of the previous section_control_items from the course/renderer.php.
     *
     * @deprecated since Moodle 5.0
     * @todo Remove this method in Moodle 6.0 (MDL-83530).
     * @return array of edit control items
     */
    #[\core\attribute\deprecated(
        replacement: 'delegated_control_items',
        since: '5.0',
        mdl: 'MDL-83527',
    )]
    protected function delegated_control_items_legacy(): array {
        global $USER;
        \core\deprecation::emit_deprecation([self::class, __FUNCTION__]);

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

        $cmbaseurl = new url('/course/mod.php');
        $cmbaseurl->param('sesskey', sesskey());

        $hasmanageactivities = has_capability('moodle/course:manageactivities', $coursecontext);
        $isheadersection = $format->get_sectionid() == $section->id;

        $controls = [];

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
                'url'   => new url('/course/editsection.php', $params),
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
                'url'   => new url('/course/mod.php', ['copy' => $cm->id]),
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
            $sectionlink = new url(
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
