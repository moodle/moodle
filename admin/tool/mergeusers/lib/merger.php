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
 * @package tool
 * @subpackage mergeusers
 * @author Jordi Pujol-Ahulló <jordi.pujol@urv.cat>
 * @copyright 2013 Servei de Recursos Educatius (http://www.sre.urv.cat)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

global $CFG;
require_once($CFG->dirroot . '/lib/clilib.php');
require_once(__DIR__ . '/autoload.php');

class Merger {
    /**
     * @var MergeUserTool instance of the tool.
     */
    protected $mut;

    /**
     * Initializes the MergeUserTool to process any incoming merging action through
     * any Gathering instance.
     */
    public function __construct(MergeUserTool $mut) {
        $this->mut = $mut;
        $this->logger = new tool_mergeusers_logger();

        // To catch Ctrl+C interruptions, we need this stuff.
        declare(ticks = 1);

        if (extension_loaded('pcntl')) {
            pcntl_signal(SIGINT, [$this, 'aborting']);
        }
    }

    /**
     * Called when aborting from command-line on Ctrl+C interruption.
     * @param int $signo only SIGINT.
     */
    public function aborting($signo) {
        if (defined("CLI_SCRIPT")) {
            echo "\n\nAborting!\n\n";
        }
        exit(0); // Exiting without error.
    }

    /**
     * This iterates over all merging actions from the given Gathering instance and tries to
     * perform it. The result of every action is logged internally for future checking.
     * @param Gathering $gathering List of merging actions.
     */
    public function merge(Gathering $gathering) {
        $numberoperations = 0;
        foreach ($gathering as $action) {
            list($success, $log, $id) = $this->mut->merge($action->toid, $action->fromid);

            // Only shows results on cli script.
            if (defined("CLI_SCRIPT")) {
                $status = ($success) ? "Success" : "Error";

                cli_writeln('');
                cli_writeln("From {$action->fromid} to {$action->toid}: $status; Log id: $id");
                cli_writeln('');
            }
            $numberoperations++;
        }

        if (defined("CLI_SCRIPT")) {
            cli_writeln("${numberoperations} merge operations performed. Bye!");
        }
    }
}
