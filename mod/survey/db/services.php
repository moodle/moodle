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
 * Survey external functions and service definitions.
 *
 * @package    mod_survey
 * @category   external
 * @copyright  2015 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.0
 */

defined('MOODLE_INTERNAL') || die;

$functions = array(

    'mod_survey_get_surveys_by_courses' => array(
        'classname'     => 'mod_survey_external',
        'methodname'    => 'get_surveys_by_courses',
        'description'   => 'Returns a list of survey instances in a provided set of courses,
                            if no courses are provided then all the survey instances the user has access to will be returned.',
        'type'          => 'read',
        'capabilities'  => '',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),

    'mod_survey_view_survey' => array(
        'classname'     => 'mod_survey_external',
        'methodname'    => 'view_survey',
        'description'   => 'Trigger the course module viewed event and update the module completion status.',
        'type'          => 'write',
        'capabilities'  => 'mod/survey:participate',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),

    'mod_survey_get_questions' => array(
        'classname'     => 'mod_survey_external',
        'methodname'    => 'get_questions',
        'description'   => 'Get the complete list of questions for the survey, including subquestions.',
        'type'          => 'read',
        'capabilities'  => 'mod/survey:participate',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),

    'mod_survey_submit_answers' => array(
        'classname'     => 'mod_survey_external',
        'methodname'    => 'submit_answers',
        'description'   => 'Submit the answers for a given survey.',
        'type'          => 'write',
        'capabilities'  => 'mod/survey:participate',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),

);
