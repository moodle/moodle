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
 * Tool models install function.
 *
 * @package    tool_models
 * @copyright  2017 David Monllao {@link http://www.davidmonllao.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Tool models install function.
 *
 * @return void
 */
function xmldb_tool_models_install() {

    // Students at risk of dropping out of courses.
    $target = \core_analytics\manager::get_target('\tool_models\analytics\target\course_dropout');
    // We need the model to be created in order to know all its potential indicators and set them.
    $model = \core_analytics\model::create($target, array());
    // TODO All of them for the moment, we will define a limited set of them once in core.
    $model->update(0, $model->get_potential_indicators());

    // Course without teachers.
    $target = \core_analytics\manager::get_target('\tool_models\analytics\target\no_teaching');
    $weekbeforestart = '\core_analytics\local\time_splitting\week_before_course_start';
    $noteacher = \core_analytics\manager::get_indicator('\core_course\analytics\indicator\no_teacher');
    \core_analytics\model::create($target, array($noteacher->get_id() => $noteacher), $weekbeforestart);
}
