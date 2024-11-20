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
 * Version information
 *
 * @package    tool
 * @subpackage iomadmerge
 * @copyright  Derick Turner
 * @author     Derick Turner
 * @basedon    admin tool merge by:
 * @author     Nicolas Dunand <Nicolas.Dunand@unil.ch>
 * @author     Mike Holzer
 * @author     Forrest Gaston
 * @author     Juan Pablo Torres Herrera
 * @author     Jordi Pujol-Ahulló, SREd, Universitat Rovira i Virgili
 * @author     John Hoopes <hoopes@wisc.edu>, University of Wisconsin - Madison
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once __DIR__ . '/autoload.php';

class Merger {
    /**
     * @var IomadMergeTool instance of the tool.
     */
    protected $mut;

    /**
     * Initializes the IomadMergeTool to process any incoming merging action through
     * any Gathering instance.
     */
    public function __construct(IomadMergeTool $mut) {
        $this->mut = $mut;
        $this->logger = new tool_iomadmerge_logger();

        // to catch Ctrl+C interruptions, we need this stuff.
        declare(ticks = 1);

        if (extension_loaded('pcntl')) {
            pcntl_signal(SIGINT, array(
                $this,
                'aborting'
            ));
        }
    }

    /**
     * Called when aborting from command-line on Ctrl+C interruption.
     * @param int $signo only SIGINT.
     */
    public function aborting($signo) {
        if (defined("CLI_SCRIPT")) {
            echo "\n\n" . get_string('ok') . ", exit!\n\n";
        }
        exit(0); //quiting normally after all ;-)
    }

    /**
     * This iterates over all merging actions from the given Gathering instance and tries to
     * perform it. The result of every action is logged internally for future checking.
     * @param Gathering $gathering List of merging actions.
     */
    public function merge(Gathering $gathering) {
        foreach ($gathering as $action) {
            list($success, $log, $id) = $this->mut->merge($action->toid, $action->fromid);

            // only shows results on cli script
            if (defined("CLI_SCRIPT")) {
                echo (($success)?get_string("success"):get_string("error")) . ". Log id: " . $id . "\n\n";
            }
        }
        if (defined("CLI_SCRIPT")) {
            echo get_string('ok') .", exit!\n\n";
        }
    }
}
