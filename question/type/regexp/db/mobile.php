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
 * Mobile plugin.
 *
 * @package qtype_regexp
 * @copyright 2018 Joseph Rézeau
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if (defined('CLI_SCRIPT') === false) {
    // To enable moodle mobile test site to upload my css files.
    header('Access-Control-Allow-Origin: *');
}

$addons = [
    "qtype_regexp" => [
        "handlers" => [
            'regexp' => [
                'displaydata' => [
                    'title' => 'Regular Expression Short Answer',
                    'icon' => '/question/type/regexp/pix/icon.gif',
                    'class' => '',
                ],
                'delegate' => 'CoreQuestionDelegate',
                'method' => 'regexp_view',
                'offlinefunctions' => [
                    'mobile_get_regexp' => [],
                ],
                'styles' => [
                    'url' => '/question/type/regexp/mobile/styles_app.css',
                    'version' => '1.00',
                ],
                'lang' => [
                    ['pluginname', 'qtype_regexp'],
                    ['buyword', 'qbehaviour_regexpadaptivewithhelp'],
                ],
            ],
        ],
    ],
];

