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
 * Library code used by the roles administration interfaces.
 *
 * @package    core_role
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Get the potential assignees selector for a given context.
 *
 * If this context is a course context, or inside a course context (module or
 * some blocks) then return a core_role_potential_assignees_below_course object. Otherwise
 * return a core_role_potential_assignees_course_and_above.
 *
 * @param context $context a context.
 * @param string $name passed to user selector constructor.
 * @param array $options to user selector constructor.
 * @return user_selector_base an appropriate user selector.
 */
function core_role_get_potential_user_selector(context $context, $name, $options) {
    $blockinsidecourse = false;
    if ($context->contextlevel == CONTEXT_BLOCK) {
        $parentcontext = $context->get_parent_context();
        $blockinsidecourse = in_array($parentcontext->contextlevel, array(CONTEXT_MODULE, CONTEXT_COURSE));
    }

    if (($context->contextlevel == CONTEXT_MODULE || $blockinsidecourse) &&
            !is_inside_frontpage($context)) {
        $potentialuserselector = new core_role_potential_assignees_below_course('addselect', $options);
    } else {
        $potentialuserselector = new core_role_potential_assignees_course_and_above('addselect', $options);
    }

    return $potentialuserselector;
}
