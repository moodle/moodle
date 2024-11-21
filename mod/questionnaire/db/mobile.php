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
 * Defines mobile handlers.
 *
 * @package   mod_questionnaire
 * @copyright 2018 Igor Sazonov <sovletig@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$addons = [
    'mod_questionnaire' => [
        'handlers' => [
            'questionsview' => [
                'displaydata' => [
                    'icon' => $CFG->wwwroot . '/mod/questionnaire/pix/icon.svg',
                    'class' => 'core-course-module-questionnaire-handler',
                ],
                'delegate' => 'CoreCourseModuleDelegate',
                'method' => 'mobile_view_activity',
                'styles' => [
                    'url' => $CFG->wwwroot . '/mod/questionnaire/styles_app.css',
                    'version' => '1.5'
                ]
            ]
        ],
        'lang' => [
            ['yourresponse', 'questionnaire'],
            ['submitted', 'questionnaire'],
            ['answerquestions', 'questionnaire'],
            ['areyousure', 'moodle'],
            ['resumesurvey', 'questionnaire'],
            ['success', 'moodle'],
            ['savechanges', 'moodle'],
            ['nextpage', 'questionnaire'],
            ['previouspage', 'questionnaire'],
            ['fullname', 'moodle'],
            ['required', 'moodle']
        ],
    ]
];
