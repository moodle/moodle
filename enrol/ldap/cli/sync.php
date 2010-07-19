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
 * CLI sync for full LDAP synchronisation.
 *
 * @package   enrol_ldap
 * @author    Iñaki Arenaza - based on code by Martin Dougiamas, Martin Langhoff and others
 * @copyright 1999 onwards Martin Dougiamas {@link http://moodle.com}
 * @copyright 2010 Iñaki Arenaza <iarenaza@eps.mondragon.edu>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 *
 * This script is meant to be called from a cronjob to sync moodle with the LDAP
 * backend in those setups where the LDAP backend acts as 'master' for enrolment.
 *
 * Example cron entry:
 * # 5 minutes past 4am
 * 5 4 * * * /usr/bin/php5 -c /etc/php5/cli/php.ini /var/www/moodle/enrol/ldap/cli/sync.php
 *
 * Notes:
 *   - If you have a large number of users, you may want to raise the memory limits
 *     by passing -d momory_limit=256M
 *   - For debugging & better logging, you are encouraged to use in the command line:
 *     -d log_errors=1 -d error_reporting=E_ALL -d display_errors=0 -d html_errors=0
 *
 */

if(isset($_SERVER['REMOTE_ADDR'])) {
    error_log("enrol/ldap/cli/sync.php can not be called from web server!");
    echo "enrol/ldap/cli/sync.php can not be called from web server!";
    exit;
}

require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/config.php');

// Ensure errors are well explained
$CFG->debug = DEBUG_NORMAL;

if (!enrol_is_enabled('ldap')) {
    error_log('[ENROL LDAP] '.get_string('pluginnotenabled', 'enrol_ldap'));
    die;
}

// Update enrolments -- these handlers should autocreate courses if required
$enrol = enrol_get_plugin('ldap');
$enrol->sync_enrolments();

