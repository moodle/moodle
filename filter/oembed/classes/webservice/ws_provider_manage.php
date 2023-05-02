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

use filter_oembed\output\providermodel;
use filter_oembed\service\util;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../../../../lib/externallib.php');

/**
 * Web service for managing provider visibility.
 * @author    Guy Thomas
 * @copyright Copyright (c) 2016 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class ws_provider_manage extends \external_api {
    /**
     * @return \external_function_parameters
     */
    public static function service_parameters() {
        $parameters = [
            'pid' => new \external_value(PARAM_INT, 'Provider id', VALUE_REQUIRED),
            'action' => new \external_value(PARAM_ALPHA, 'Action: enable / disable / reload / delete', VALUE_REQUIRED)
        ];
        return new \external_function_parameters($parameters);
    }

    /**
     * @return \external_single_structure
     */
    public static function service_returns() {
        $keys = [
            'visible' => new \external_value(PARAM_INT, 'Provider visibility', VALUE_REQUIRED),
            'providermodel' => new \external_single_structure(
                util::define_class_for_webservice('filter_oembed\output\providermodel'),
                'Provider renderable',
                VALUE_OPTIONAL
            )
        ];

        return new \external_single_structure($keys, 'provider');
    }

    /**
     * @param int $pid
     * @param string $action
     * @return array
     */
    public static function service($pid, $action) {
        $oembed = \filter_oembed\service\oembed::get_instance('all');

        if ($action === 'enable' || $action === 'disable') {
            if ($action === 'enable') {
                $oembed->enable_provider($pid);
            } else {
                $oembed->disable_provider($pid);
            }
        }

        if ($action === 'delete') {
            $oembed->delete_provider($pid);
            return [
                'visible' => 0
            ];
        } else {
            $providerrow = $oembed->get_provider_row($pid);
            $providermodel = new providermodel($providerrow);
            $visible = intval($providerrow->enabled);
            if ($action === 'enable' || $action === 'disable') {
                $visible = $action === 'enable' ? 1 : 0;
            }
            return [
                'visible' => $visible,
                'providermodel' => $providermodel
            ];
        }

        throw new coding_exception('Invalid action - '.$action);
    }
}
