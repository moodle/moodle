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
 * Theme Boost Union - Course management renderer
 *
 * @package    theme_moove
 * @copyright  2023 Alexander Bias <bias@alexanderbias.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_moove\output\core_course\management;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/course/classes/management_renderer.php');

use core_course_category;
use core_course_list_element;
use moodle_url;
use pix_icon;
use core\output\html_writer;

/**
 * Extending the core_course_management_renderer.
 *
 * @package    theme_moove
 * @copyright  2023 Alexander Bias <bias@alexanderbias.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class renderer extends \core_course_management_renderer {
    /**
     * Renderers actions for individual course actions.
     *
     * This renderer function is copied and modified from /course/classes/management_renderer.php.
     *
     * @param core_course_category $category The currently selected category.
     * @param core_course_list_element  $course The course to renderer actions for.
     * @return string
     */
    public function course_listitem_actions(core_course_category $category, core_course_list_element $course) {
        $actions = \core_course\management\helper::get_course_listitem_actions($category, $course);

        if ($course->can_access()) {
            // Prepend the 'view course' icon.
            $viewaction = [
                'url' => new \moodle_url('/course/view.php', ['id' => $course->id]),
                'icon' => new \pix_icon('i/course', \get_string('view')),
                'attributes' => ['class' => 'action-view'],
            ];
            array_unshift($actions, $viewaction);
        }

        $actionshtml = [];
        foreach ($actions as $action) {
            $action['attributes']['role'] = 'button';
            $actionshtml[] = $this->output->action_icon($action['url'], $action['icon'], null, $action['attributes']);
        }

        return html_writer::span(join('', $actionshtml), 'course-item-actions item-actions me-0');
    }

    /**
     * Renderers actions for individual course actions.
     *
     * This renderer function is copied and modified from /course/classes/management_renderer.php.
     *
     * @param core_course_list_element  $course The course to renderer actions for.
     * @return string
     */
    public function search_listitem_actions(core_course_list_element $course) {
        $baseurl = new moodle_url(
            '/course/managementsearch.php',
            ['courseid' => $course->id, 'categoryid' => $course->category, 'sesskey' => sesskey()]
        );
        $actions = [];
        if ($course->can_access()) {
            // View.
            $actions[] = $this->output->action_icon(
                new moodle_url('/course/view.php', ['id' => $course->id]),
                new pix_icon('i/course', \get_string('view')),
                null,
                ['class' => 'action-view']
            );
            // Edit.
            if ($course->can_edit()) {
                $actions[] = $this->output->action_icon(
                    new moodle_url('/course/edit.php', ['id' => $course->id]),
                    new pix_icon('t/edit', get_string('edit')),
                    null,
                    ['class' => 'action-edit']
                );
            }
            // Delete.
            if ($course->can_delete()) {
                $actions[] = $this->output->action_icon(
                    new moodle_url('/course/delete.php', ['id' => $course->id]),
                    new pix_icon('t/delete', get_string('delete')),
                    null,
                    ['class' => 'action-delete']
                );
            }
            // Show/Hide.
            if ($course->can_change_visibility()) {
                $actions[] = $this->output->action_icon(
                    new moodle_url($baseurl, ['action' => 'hidecourse']),
                    new pix_icon('t/hide', get_string('hide')),
                    null,
                    ['data-action' => 'hide', 'class' => 'action-hide']
                );
                $actions[] = $this->output->action_icon(
                    new moodle_url($baseurl, ['action' => 'showcourse']),
                    new pix_icon('t/show', get_string('show')),
                    null,
                    ['data-action' => 'show', 'class' => 'action-show']
                );
            }
        }
        if (empty($actions)) {
            return '';
        }
        return html_writer::span(join('', $actions), 'course-item-actions item-actions');
    }
}
