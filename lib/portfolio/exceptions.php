<?php
/**
 * Moodle - Modular Object-Oriented Dynamic Learning Environment
 *          http://moodle.org
 * Copyright (C) 1999 onwards Martin Dougiamas  http://dougiamas.com
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package    core
 * @subpackage portfolio
 * @author     Penny Leach <penny@catalyst.net.nz>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 * @copyright  (C) 1999 onwards Martin Dougiamas  http://dougiamas.com
 *
 * This file contains all the portfolio exception classes.
 */

defined('MOODLE_INTERNAL') || die();

/**
* top level portfolio exception.
* sometimes caught and rethrown as {@see portfolio_export_exception}
*/
class portfolio_exception extends moodle_exception {}

/**
* exception to throw during an export - will clean up session and tempdata
*/
class portfolio_export_exception extends portfolio_exception {

    /**
    * constructor.
    * @param object $exporter instance of portfolio_exporter (will handle null case)
    * @param string $errorcode language string key
    * @param string $module language string module (optional, defaults to moodle)
    * @param string $continue url to continue to (optional, defaults to wwwroot)
    * @param mixed $a language string data (optional, defaults to  null)
    */
    public function __construct($exporter, $errorcode, $module=null, $continue=null, $a=null) {
        global $CFG;
        // This static variable is necessary because sometimes the code below
        // which tries to obtain a continue link can cause one of these
        // exceptions to be thrown. This would create an infinite loop (until
        // PHP hits its stack limit). Using this static lets us make the
        // nested constructor finish immediately without attempting to call
        // methods that might fail.
        static $inconstructor = false;

        if (!$inconstructor && !empty($exporter) &&
                $exporter instanceof portfolio_exporter) {
            $inconstructor = true;
            try {
                if (empty($continue)) {
                    $caller = $exporter->get('caller');
                    if (!empty($caller) && $caller instanceof portfolio_caller_base) {
                        $continue = $exporter->get('caller')->get_return_url();
                    }
                }
                // this was previously only called if we were in cron,
                // but I think if there's always an exception, we should clean up
                // rather than force the user to resolve the export later.
                $exporter->process_stage_cleanup();
            } catch(Exception $e) {
                // Ooops, we had an exception trying to get caller
                // information. Ignore it.
            }
            $inconstructor = false;
        }
        parent::__construct($errorcode, $module, $continue, $a);
    }
}

/**
* exception for callers to throw when they have a problem.
* usually caught and rethrown as {@see portfolio_export_exception}
*/
class portfolio_caller_exception extends portfolio_exception {}

/**
* exception for portfolio plugins to throw when they have a problem.
* usually caught and rethrown as {@see portfolio_export_exception}
*/
class portfolio_plugin_exception extends portfolio_exception {}

/**
* exception for interacting with the button class
*/
class portfolio_button_exception extends portfolio_exception {}

/**
 * leap2a exception - for invalid api calls
 */
class portfolio_format_leap2a_exception extends portfolio_exception {}
