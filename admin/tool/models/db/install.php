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

    // Community of inquiry indicators.
    $indicators = array(
        '\mod_assign\analytics\indicator\cognitive_depth',
        '\mod_assign\analytics\indicator\social_breadth',
        '\mod_book\analytics\indicator\cognitive_depth',
        '\mod_book\analytics\indicator\social_breadth',
        '\mod_chat\analytics\indicator\cognitive_depth',
        '\mod_chat\analytics\indicator\social_breadth',
        '\mod_choice\analytics\indicator\cognitive_depth',
        '\mod_choice\analytics\indicator\social_breadth',
        '\mod_data\analytics\indicator\cognitive_depth',
        '\mod_data\analytics\indicator\social_breadth',
        '\mod_feedback\analytics\indicator\cognitive_depth',
        '\mod_feedback\analytics\indicator\social_breadth',
        '\mod_folder\analytics\indicator\cognitive_depth',
        '\mod_folder\analytics\indicator\social_breadth',
        '\mod_forum\analytics\indicator\cognitive_depth',
        '\mod_forum\analytics\indicator\social_breadth',
        '\mod_glossary\analytics\indicator\cognitive_depth',
        '\mod_glossary\analytics\indicator\social_breadth',
        '\mod_imscp\analytics\indicator\cognitive_depth',
        '\mod_imscp\analytics\indicator\social_breadth',
        '\mod_label\analytics\indicator\cognitive_depth',
        '\mod_label\analytics\indicator\social_breadth',
        '\mod_lesson\analytics\indicator\cognitive_depth',
        '\mod_lesson\analytics\indicator\social_breadth',
        '\mod_lti\analytics\indicator\cognitive_depth',
        '\mod_lti\analytics\indicator\social_breadth',
        '\mod_page\analytics\indicator\cognitive_depth',
        '\mod_page\analytics\indicator\social_breadth',
        '\mod_quiz\analytics\indicator\cognitive_depth',
        '\mod_quiz\analytics\indicator\social_breadth',
        '\mod_resource\analytics\indicator\cognitive_depth',
        '\mod_resource\analytics\indicator\social_breadth',
        '\mod_scorm\analytics\indicator\cognitive_depth',
        '\mod_scorm\analytics\indicator\social_breadth',
        '\mod_survey\analytics\indicator\cognitive_depth',
        '\mod_survey\analytics\indicator\social_breadth',
        '\mod_url\analytics\indicator\cognitive_depth',
        '\mod_url\analytics\indicator\social_breadth',
        '\mod_wiki\analytics\indicator\cognitive_depth',
        '\mod_wiki\analytics\indicator\social_breadth',
        '\mod_workshop\analytics\indicator\cognitive_depth',
        '\mod_workshop\analytics\indicator\social_breadth',
    );
    array_walk($indicators, function(&$indicator) {
        $indicator = \core_analytics\manager::get_indicator($indicator);
    });

    // We need the model to be created in order to know all its potential indicators and set them.
    $model = \core_analytics\model::create($target, array());

    // Course without teachers.
    $target = \core_analytics\manager::get_target('\tool_models\analytics\target\no_teaching');
    $timesplittingmethod = '\core_analytics\local\time_splitting\single_range';
    $noteacher = \core_analytics\manager::get_indicator('\core_course\analytics\indicator\no_teacher');
    \core_analytics\model::create($target, array($noteacher->get_id() => $noteacher), $timesplittingmethod);
}
