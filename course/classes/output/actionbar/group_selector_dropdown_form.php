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

namespace core_course\output\actionbar;

use core\output\named_templatable;
use core\output\renderable;
use core\output\renderer_base;

/**
 * Renderable class for the group selection dropdown form.
 *
 * This form is the content for the group_selector renderable, which itself is an extension of the comboboxsearch component.
 * {@see group_selector}.
 *
 * @package    core_course
 * @copyright  2024 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class group_selector_dropdown_form implements renderable, named_templatable {

    /**
     * The class constructor.
     *
     * @param \context $context The context instance.
     */
    public function __construct(
        protected \context $context
    ) {
    }

    public function export_for_template(renderer_base $output) {
        return [
            'courseid' => $this->context->get_course_context()->instanceid,
            'currentvalue' => optional_param('groupsearchvalue', '', PARAM_NOTAGS),
            'instance' => rand(),
        ];
    }

    public function get_template_name(renderer_base $renderer): string {
        return 'core_group/comboboxsearch/searchbody';
    }
}
