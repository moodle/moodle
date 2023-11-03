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
 * LearnerScript Tile block plugin installation.
 *
 * @package    block_reporttiles
 * @author     Arun Kumar Mukka
 * @copyright  2018 eAbyas Info Solutions
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function xmldb_block_reporttiles_install() {
    global $CFG, $DB;
    $pluginman = core_plugin_manager::instance();
    $reportdashboardpluginfo = $pluginman->get_plugin_info('block_reportdashboard');
    if (is_null($reportdashboardpluginfo)) {
        $DB->set_field('block', 'visible', 0, array('name' => 'learnerscript'));
    } else {
        $DB->set_field('block', 'visible', 1, array('name' => 'learnerscript'));
    }
}
