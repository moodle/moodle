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
 * Upgrade.
 *
 * @package    portfolio_boxnet
 * @copyright  2013 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Upgrade function.
 *
 * @param int $oldversion the version we are upgrading from.
 * @return bool result
 */
function xmldb_portfolio_boxnet_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2013050102) {
        require_once($CFG->libdir . '/portfoliolib.php');
        require_once($CFG->dirroot . '/portfolio/boxnet/db/upgradelib.php');

        $existing = $DB->get_record('portfolio_instance', array('plugin' => 'boxnet'), '*', IGNORE_MULTIPLE);
        if ($existing) {

            // Only disable or message the admins when the portfolio hasn't been set for APIv2.
            $instance = portfolio_instance($existing->id, $existing);
            if ($instance->get_config('clientid') === null && $instance->get_config('clientsecret') === null) {

                // Disable Box.net.
                $instance->set('visible', 0);
                $instance->save();

                // Message the admins.
                portfolio_boxnet_admin_upgrade_notification();
            }
        }

        upgrade_plugin_savepoint(true, 2013050102, 'portfolio', 'boxnet');
    }

    return true;
}
