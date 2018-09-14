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
 * Essential is a clean and customizable theme.
 *
 * @package     theme_essential
 * @copyright   2018 Gareth J Barnard
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function xmldb_theme_essential_upgrade($oldversion = 0) {

    global $DB;
    $dbman = $DB->get_manager();
    $result = true;

    if ($oldversion < 2017102903) {

        $table = new xmldb_table('config_plugins');
        if ($dbman->table_exists($table) == true) {
            $conditions = array('plugin' => 'theme_essential', 'name' => 'populateme');
            $settings = array('analyticsenabled', 'analytics', 'analyticssiteid', 'analyticsimagetrack', 'analyticssiteurl',
                'analyticsuseuserid', 'analyticstrackingid', 'analyticstrackadmin', 'analyticscleanurl');
            foreach ($settings as $setting) {
                $conditions['name'] = $setting;
                $DB->delete_records('config_plugins', $conditions);
            }
        }

        upgrade_plugin_savepoint(true, 2017102903, 'theme', 'essential');
    }

    // Automatic 'Purge all caches'....
    if ($oldversion < 2118041000) {
        purge_all_caches();
    }

    return $result;
}