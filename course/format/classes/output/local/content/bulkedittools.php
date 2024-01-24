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

namespace core_courseformat\output\local\content;

use core\moodlenet\utilities;
use core\output\named_templatable;
use core_courseformat\base as course_format;
use core_courseformat\output\local\courseformat_named_templatable;
use renderable;
use stdClass;

/**
 * Contains the bulk editor tools bar.
 *
 * @package   core_courseformat
 * @copyright 2023 Ferran Recio <ferran@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class bulkedittools implements named_templatable, renderable {
    use courseformat_named_templatable;

    /** @var core_courseformat\base the course format class */
    protected $format;

    /**
     * Constructor.
     *
     * @param course_format $format the course format
     */
    public function __construct(course_format $format) {
        $this->format = $format;
    }

    /**
     * Export this data so it can be used as the context for a mustache template (core/inplace_editable).
     *
     * @param renderer_base $output typically, the renderer that's calling this function
     * @return stdClass data context for a mustache template
     */
    public function export_for_template(\renderer_base $output): stdClass {
        $format = $this->format;
        $course = $format->get_course();

        $data = (object)[
            'id' => $course->id,
            'actions' => $this->get_toolbar_actions(),
        ];
        $data->hasactions = !empty($data->actions);
        return $data;
    }

    /**
     * Get the toolbar actions.
     * @return array the array of buttons
     */
    protected function get_toolbar_actions(): array {
        return array_merge(
            array_values($this->section_control_items()),
            array_values($this->cm_control_items()),
        );
    }

    /**
     * Generate the bulk edit control items of a course module.
     *
     * Format plugins can override the method to add or remove elements
     * from the toolbar.
     *
     * @return array of edit control items
     */
    protected function cm_control_items(): array {
        global $CFG, $USER;
        $format = $this->format;
        $context = $format->get_context();
        $user = $USER;

        $controls = [];

        if (has_capability('moodle/course:activityvisibility', $context, $user)) {
            $controls['availability'] = [
                'icon' => 't/show',
                'action' => 'cmAvailability',
                'name' => get_string('availability'),
                'title' => get_string('cmavailability', 'core_courseformat'),
                'bulk' => 'cm',
            ];
        }


        $duplicatecapabilities = ['moodle/backup:backuptargetimport', 'moodle/restore:restoretargetimport'];
        if (has_all_capabilities($duplicatecapabilities, $context, $user)) {
            $controls['duplicate'] = [
                'icon' => 't/copy',
                'action' => 'cmDuplicate',
                'name' => get_string('duplicate'),
                'title' => get_string('cmsduplicate', 'core_courseformat'),
                'bulk' => 'cm',
            ];
        }


        $hasmanageactivities = has_capability('moodle/course:manageactivities', $context, $user);
        if ($hasmanageactivities) {
            $controls['move'] = [
                'icon' => 'i/dragdrop',
                'action' => 'moveCm',
                'name' => get_string('move'),
                'title' => get_string('cmsmove', 'core_courseformat'),
                'bulk' => 'cm',
            ];

            $controls['delete'] = [
                'icon' => 'i/delete',
                'action' => 'cmDelete',
                'name' => get_string('delete'),
                'title' => get_string('cmsdelete', 'core_courseformat'),
                'bulk' => 'cm',
            ];
        }

        $usercanshare = utilities::can_user_share($context, $user->id, 'course');
        if ($CFG->enablesharingtomoodlenet && $usercanshare) {
            $controls['sharetomoodlenet'] = [
                'id' => 'cmShareToMoodleNet',
                'icon' => 'i/share',
                'action' => 'cmShareToMoodleNet',
                'name' => get_string('moodlenet:sharetomoodlenet'),
                'title' => get_string('moodlenet:sharetomoodlenet'),
                'bulk' => 'cm',
            ];
        }

        return $controls;
    }

    /**
     * Generate the bulk edit control items of a section.
     *
     * Format plugins can override the method to add or remove elements
     * from the toolbar.
     *
     * @return array of edit control items
     */
    protected function section_control_items(): array {
        global $USER;
        $format = $this->format;
        $context = $format->get_context();
        $sectionreturn = $format->get_section_number();
        $user = $USER;

        $controls = [];

        if (has_capability('moodle/course:sectionvisibility', $context, $user)) {
            $controls['availability'] = [
                'icon' => 't/show',
                'action' => 'sectionAvailability',
                'name' => get_string('availability'),
                'title' => $this->format->get_format_string('sectionsavailability'),
                'bulk' => 'section',
            ];
        }

        if (!$sectionreturn && has_capability('moodle/course:movesections', $context, $user)) {
            $controls['move'] = [
                'icon' => 'i/dragdrop',
                'action' => 'moveSection',
                'name' => get_string('move', 'moodle'),
                'title' => $this->format->get_format_string('sectionsmove'),
                'bulk' => 'section',
            ];
        }

        $deletecapabilities = ['moodle/course:movesections', 'moodle/course:update'];
        if (!$sectionreturn && has_all_capabilities($deletecapabilities, $context, $user)) {
            $controls['delete'] = [
                'icon' => 'i/delete',
                'action' => 'deleteSection',
                'name' => get_string('delete'),
                'title' => $this->format->get_format_string('sectionsdelete'),
                'bulk' => 'section',
            ];
        }

        return $controls;
    }
}
