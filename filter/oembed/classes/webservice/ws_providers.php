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

namespace filter_oembed\webservice;

use filter_oembed\output\managementpage;
use filter_oembed\service\oembed;
use filter_oembed\service\util;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../../../../lib/externallib.php');

/**
 * Web service for getting array of provider models.
 * @author    Guy Thomas
 * @copyright Copyright (c) 2016 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class ws_providers extends \external_api {
    /**
     * @return \external_function_parameters
     */
    public static function service_parameters() {
        $parameters = [
            'scope' => new \external_value(PARAM_ALPHA, 'Providers scope - all, enabled, disabled', VALUE_REQUIRED, 'all')
        ];
        return new \external_function_parameters($parameters);
    }

    /**
     * @return \external_single_structure
     */
    public static function service_returns() {
        $keys = [
            'downloadrows' => new \external_multiple_structure(
                new \external_single_structure(
                    util::define_class_for_webservice('filter_oembed\output\providermodel'),
                    'Provider renderable',
                    VALUE_REQUIRED
                ), 'Array of downloaded providers', VALUE_REQUIRED
            ),
            'pluginrows' => new \external_multiple_structure(
                new \external_single_structure(
                    util::define_class_for_webservice('filter_oembed\output\providermodel'),
                    'Provider renderable',
                    VALUE_REQUIRED
                ), 'Array of plugin providers', VALUE_REQUIRED
            ),
            'localrows' => new \external_multiple_structure(
                new \external_single_structure(
                    util::define_class_for_webservice('filter_oembed\output\providermodel'),
                    'Provider renderable',
                    VALUE_REQUIRED
                ), 'Array of local providers', VALUE_REQUIRED
            )

        ];

        return new \external_single_structure($keys, 'Providers array.');
    }

    /**
     * @param int $pid
     * @param string $action
     * @return array
     */
    public static function service($scope) {
        global $PAGE;
        $PAGE->set_context(\context_system::instance());
        $output = $PAGE->get_renderer('core', '', RENDERER_TARGET_GENERAL);

        $oembed = oembed::get_instance($scope);

        $providerrows = $oembed->providers;

        $page = new managementpage($providerrows);
        return $page->export_for_template($output);
    }
}
