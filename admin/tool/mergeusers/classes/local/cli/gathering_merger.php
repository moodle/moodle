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
 * Merger tool to use to iterate through several pair of users.
 *
 * @package   tool_mergeusers
 * @author    Jordi Pujol-Ahulló <jordi.pujol@urv.cat>
 * @copyright 2013 onwards to Universitat Rovira i Virgili (https://www.urvc.cat)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_mergeusers\local\cli;

defined('MOODLE_INTERNAL') || die;

global $CFG;
require_once($CFG->libdir . '/clilib.php');

use coding_exception;
use dml_exception;
use moodle_exception;
use tool_mergeusers\local\logger;
use tool_mergeusers\local\user_merger;


/**
 * Merger tool to use to iterate through several pair of users.
 *
 * @package   tool_mergeusers
 * @author    Jordi Pujol-Ahulló <jordi.pujol@urv.cat>
 * @copyright 2013 onwards to Universitat Rovira i Virgili (https://www.urvc.cat)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class gathering_merger {
    /** @var user_merger user merger. */
    protected user_merger $usermerger;
    /** @var logger logger instance. */
    protected logger $logger;

    /**
     * Initializes the to process any incoming merging action through
     * any Gathering instance.
     */
    public function __construct(user_merger $mut) {
        $this->usermerger = $mut;
        $this->logger = new logger();
    }

    /**
     * This iterates over all merging actions from the given Gathering instance and tries to
     * perform it. The result of every action is logged internally for future checking.
     *
     * @param gathering $gathering List of merging actions.
     * @throws coding_exception
     * @throws dml_exception
     * @throws moodle_exception
     */
    public function merge(gathering $gathering): void {
        $numberoperations = 0;
        foreach ($gathering as $action) {
            [$success, $log, $id] = $this->usermerger->merge($action->toid, $action->fromid);

            // Only shows results on cli script.
            if (defined("CLI_SCRIPT")) {
                $status = ($success) ? get_string("success") : get_string("error");

                cli_writeln('');
                cli_writeln("From {$action->fromid} to {$action->toid}: $status; Log id: $id");
                cli_writeln('');
            }
            $numberoperations++;
        }

        if (defined("CLI_SCRIPT")) {
            cli_writeln("{$numberoperations} merge operations performed. Bye!");
        }
    }
}
