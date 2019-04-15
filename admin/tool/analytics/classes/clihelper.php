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
 * Helper class that contains helper functions for cli scripts.
 *
 * @package   tool_analytics
 * @copyright 2017 onwards Ankit Agarwal
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_analytics;

defined('MOODLE_INTERNAL') || die();

/**
 * Helper class that contains helper functions for cli scripts.
 *
 * @package   tool_analytics
 * @copyright 2017 onwards Ankit Agarwal
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class clihelper {

    /**
     * List all models in the system. To be used from cli scripts.
     *
     * @return void
     */
    public static function list_models() {
        cli_heading("List of models");
        echo str_pad(get_string('modelid', 'tool_analytics'), 15, ' ') . ' ' . str_pad(get_string('name'), 50, ' ') .
            ' ' . str_pad(get_string('status'), 15, ' ') . "\n";
        $models = \core_analytics\manager::get_all_models();
        foreach ($models as $model) {
            $modelid = $model->get_id();
            $isenabled = $model->is_enabled() ? get_string('enabled', 'tool_analytics') : get_string('disabled', 'tool_analytics');
            $name = $model->get_name();
            echo str_pad($modelid, 15, ' ') . ' ' . str_pad($name, 50, ' ') . ' ' . str_pad($isenabled, 15, ' ') . "\n";
        }
    }
}