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
 * Get version information.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2017 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_ally\webservice;

use tool_ally\version_information;

/**
 * Get version information.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2017 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class version_info extends loggable_external_api {
    /**
     * @return \external_function_parameters
     */
    public static function service_parameters() {
        return new \external_function_parameters([]);
    }

    /**
     * @return \external_single_structure
     */
    public static function service_returns() {
        return new \external_single_structure([
            'tool_ally' => new \external_single_structure([
                'version'    => new \external_value(PARAM_FLOAT, 'Ally admin tool version'),
                'requires'   => new \external_value(PARAM_FLOAT, 'Ally admin tool requires Moodle version'),
                'release'    => new \external_value(PARAM_TEXT,  'Ally admin tool release'),
                'installed'  => new \external_value(PARAM_BOOL,  'Ally admin tool installed', VALUE_REQUIRED)
            ]),
            'filter_ally' => new \external_single_structure([
                'version'    => new \external_value(PARAM_FLOAT, 'Ally filter version', VALUE_OPTIONAL),
                'requires'   => new \external_value(PARAM_FLOAT, 'Ally filter requires Moodle version', VALUE_OPTIONAL),
                'release'    => new \external_value(PARAM_TEXT,  'Ally filter release', VALUE_OPTIONAL),
                'active'     => new \external_value(PARAM_BOOL,  'Ally filter active at system level', VALUE_OPTIONAL),
                'installed'  => new \external_value(PARAM_BOOL,  'Ally filter installed', VALUE_REQUIRED)
            ]),
            'report_allylti' => new \external_single_structure([
                'version'    => new \external_value(PARAM_FLOAT, 'Ally LTI report version', VALUE_OPTIONAL),
                'requires'   => new \external_value(PARAM_FLOAT, 'Ally LTI report requires Moodle version',
                        VALUE_OPTIONAL),
                'release'    => new \external_value(PARAM_TEXT,  'Ally LTI report release', VALUE_OPTIONAL),
                'installed'  => new \external_value(PARAM_BOOL,  'Ally LTI report installed', VALUE_REQUIRED)
            ]),
            'moodle' => new \external_single_structure([
                'version'    => new \external_value(PARAM_FLOAT, 'Moodle version'),
                'release'    => new \external_value(PARAM_TEXT,  'Moodle release'),
                'branch'     => new \external_value(PARAM_FLOAT, 'Moodle branch')
            ]),
            'system' => new \external_single_structure([
                'os' => new \external_value(PARAM_TEXT,  'Server operating system info'),
                'phposbuild' => new \external_value(PARAM_TEXT,  'PHP operating system build info'),
                'phpversion' => new \external_value(PARAM_TEXT, 'PHP version'),
                'dbtype' => new \external_value(PARAM_TEXT, 'Databse type'),
                'dbversion' => new \external_value(PARAM_TEXT, 'Databse version')
            ])
        ]);
    }

    /**
     * @return array
     */
    public static function execute_service() {

        $versioninfo = new version_information();

        return [
            'tool_ally'       => $versioninfo->toolally,
            'filter_ally'     => $versioninfo->filterally,
            'report_allylti'  => $versioninfo->reportally,
            'moodle'          => $versioninfo->core,
            'system'          => $versioninfo->system
        ];
    }
}
