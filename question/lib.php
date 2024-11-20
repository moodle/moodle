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
 * Question related functions.
 *
 * This file was created just because Fragment API expects callbacks to be defined on lib.php
 *
 * Please, do not add new functions to this file.
 *
 * @package   core_question
 * @copyright 2018 Simey Lameze <simey@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/editlib.php');

/**
 * Question data fragment to get the question html via ajax call.
 *
 * @param array $args Arguments for rendering the fragment. Expected keys:
 *  * view - the view class
 *  * cmid - if in an activity, the course module ID.
 *  * filterquery - the current filters encoded as a URL parameter.
 *  * lastchanged - the ID of the last edited question.
 *  * sortdata - Array of sorted columns.
 *  * filtercondition - the current filters encoded as an object.
 *  * extraparams - additional parameters required for a particular view class.
 *
 * @return array|string
 */
function core_question_output_fragment_question_data(array $args): string {
    if (empty($args)) {
        return '';
    }
    [$params, $extraparams] = \core_question\local\bank\filter_condition_manager::extract_parameters_from_fragment_args($args);
    [
        $thispageurl,
        $contexts,
        ,
        $cm,
        ,
        $pagevars
    ] = question_build_edit_resources('questions', '/question/edit.php', $params);

    if (is_null($cm)) {
        $course = get_course(clean_param($args['courseid'], PARAM_INT));
    } else {
        $course = get_course($cm->course);
    }

    $viewclass = empty($args['view']) ? \core_question\local\bank\view::class : clean_param($args['view'], PARAM_NOTAGS);

    if (!empty($args['lastchanged'])) {
        $thispageurl->param('lastchanged', clean_param($args['lastchanged'], PARAM_INT));
    }
    // This is highly suspicious, but it is the same approach taken in /question/edit.php. See MDL-79281.
    $thispageurl->param('deleteall', 1);
    $questionbank = new $viewclass($contexts, $thispageurl, $course, $cm, $pagevars, $extraparams);
    $questionbank->add_standard_search_conditions();
    ob_start();
    $questionbank->display_question_list();
    return ob_get_clean();
}
