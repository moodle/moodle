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
 * tool_models plugin uninstallation.
 *
 * @package    tool_models
 * @copyright  2017 David Monllao {@link http://www.davidmonllao.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function xmldb_tool_models_uninstall() {
    global $DB;

    // Remove the models that are using this tool targets.
    $targets = core_component::get_component_classes_in_namespace('tool_models', 'analytics\target');

    $options = array();
    foreach ($targets as $classname => $unused) {
        $target = \core_analytics\manager::get_target($classname);
        $options[] = '\\' . get_class($target);
    }
    list($sql, $params) = $DB->get_in_or_equal($options);
    $models = $DB->get_records_select('analytics_models', "target $sql", $params);
    foreach ($models as $modelobj) {
        $model = new \core_analytics\model($modelobj);
        $model->delete();
    }
}
