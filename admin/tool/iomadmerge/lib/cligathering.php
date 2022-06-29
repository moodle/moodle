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

(defined("MOODLE_INTERNAL") && defined("CLI_SCRIPT")) || die();

global $CFG;

require_once $CFG->dirroot . '/lib/clilib.php';

/**
 * Abstraction layer to use to get the list of mergin actions to perform asked from command line.
 *
 */
class CLIGathering implements Gathering {

    /**
     * @var stdClass object with fromid and toid user.id fields.
     */
    protected $current;
    /**
     * @var bool true if user chose to conclude with merging users. false if we are still merging.
     */
    protected $end;
    /**
     * @var int zero-based index of the number of asked merging operations.
     */
    protected $index;

    /**
     * Initialization, also for capturing Ctrl+C interruptions.
     */
    public function __construct()
    {
        $this->index = -1;
        $this->end = false;
    }

    /**
     * Asks by command line both users to merge, with a header telling what to do.
     */
    public function rewind()
    {
        cli_heading(get_string('pluginname', 'tool_iomadmerge'));
        echo get_string('cligathering:description', 'tool_iomadmerge') . "\n\n";
        echo get_string('cligathering:stopping', 'tool_iomadmerge') . "\n\n";
        $this->next();
    }

    /**
     * Asks for the next pair of users' id to merge.
     * It also detects when anything but a number is introduced, to re-ask for any user id.
     */
    public function next()
    {
        $record = new stdClass();

        //ask for the source user id.
        $record->fromid = 0;
        while ($record->fromid <= 0 && $record->fromid != -1) {
            $record->fromid = intval(cli_input(get_string('cligathering:fromid', 'tool_iomadmerge')));
        }

        //if we have to exit, do it just now ;-)
        if ($record->fromid == -1) {
            $this->end = true;
            return;
        }

        //otherwise, ask for the target user id.
        $record->toid = 0;
        while ($record->toid <= 0 && $record->toid != -1) {
            $record->toid = intval(cli_input(get_string('cligathering:toid', 'tool_iomadmerge')));
        }

        //updates related iterator fields.
        $this->end = $record->toid == -1;
        $this->current = $record;
        $this->index++;
    }

    /**
     * Tells whether to conclude iteration.
     * @return bool true if to go on with the iteration (we have a pair of users to merge).
     * false to conclude.
     */
    public function valid()
    {
        return !$this->end;
    }

    /**
     * Gets the current pair of users to merge.
     * @return stdClass object with fromid and toid fields
     */
    public function current()
    {
        return $this->current;
    }

    /**
     * Gets current int zero-based index.
     * @return int zero-based index value
     */
    public function key()
    {
        return $this->index;
    }
}
