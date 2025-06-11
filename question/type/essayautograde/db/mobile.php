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
 * essayautograde question type  capability definition for Mobile
 *
 * @package    qtype_essayautograde
 * @copyright  2019 Gordon Bateson with grateful thanks to Marcus Green
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

$addons = [
    'qtype_essayautograde' => [
        'handlers' => [
            // The handler name can be anything, but let's use "view"
            // so that it matches the templates/mobile_view.mustache
            'view' => [
                'displaydata' => [
                    'title' => 'Essay (auto-grade) question',
                    'icon' => '/question/type/essayautograde/pix/icon.gif',
                    'class' => '',
                ],
                'delegate' => 'CoreQuestionDelegate',
                'method' => 'mobile_get_essayautograde', // in classes/output/mobile.php
                'offlinefunctions' => [
                    'mobile_view_essayautograde' => [],
                ],
                'styles' => [
                    'url' => '/question/type/essayautograde/mobile/styles_app.css',
                    'version' => '1.00'
                ]
            ]
        ],
        // These lang strings can be inserted into the template as follows:
        // {{ 'plugin.qtype_essayautograde.STRINGNAME' | translate }}
        'lang' => [
            ['maxwordswarning', 'qtype_essayautograde'],
            ['minwordswarning', 'qtype_essayautograde'],
        ],
    ]
];