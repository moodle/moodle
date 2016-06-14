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
 * Web cron
 *
 * This script looks through all the module directories for cron.php files
 * and runs them.  These files can contain cleanup functions, email functions
 * or anything that needs to be run on a regular basis.
 *
 * This file is best run from cron on the host system (ie outside PHP).
 * It is strongly recommended to add password protection via admin settings.
 *
 * eg   wget -q -O /dev/null 'http: *moodle.somewhere.edu/admin/cron.php?password=SeCreT666'
 *
 * It is also possible to use CLI script admin/cli/cron.php instead,
 * you can not call this script from command line any more.
 *
 * @package    core
 * @subpackage admin
 * @copyright  1999 onwards Martin Dougiamas  http://dougiamas.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


if (defined('STDIN')) {
    fwrite(STDERR, "ERROR: This script no longer supports CLI, please use admin/cli/cron.php instead\n");
    exit(1);
}

// This is a fake CLI script, it is a really ugly hack which emulates
// CLI via web interface, please do not use this hack elsewhere
define('CLI_SCRIPT', true);
define('WEB_CRON_EMULATED_CLI', 'defined'); // ugly ugly hack, do not use elsewhere please
define('NO_OUTPUT_BUFFERING', true);

require('../config.php');
require_once($CFG->libdir.'/clilib.php');
require_once($CFG->libdir.'/cronlib.php');

// extra safety
\core\session\manager::write_close();

// check if execution allowed
if (!empty($CFG->cronclionly)) {
    // This script can only be run via the cli.
    print_error('cronerrorclionly', 'admin');
    exit;
}
// This script is being called via the web, so check the password if there is one.
if (!empty($CFG->cronremotepassword)) {
    $pass = optional_param('password', '', PARAM_RAW);
    if ($pass != $CFG->cronremotepassword) {
        // wrong password.
        print_error('cronerrorpassword', 'admin');
        exit;
    }
}

// send mime type and encoding
@header('Content-Type: text/plain; charset=utf-8');

// we do not want html markup in emulated CLI
@ini_set('html_errors', 'off');

// execute the cron
cron_run();
