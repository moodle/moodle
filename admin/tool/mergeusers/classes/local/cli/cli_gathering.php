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
 * Implements a gathering class as an interactive script for manual merging from CLI.
 *
 * @package   tool_mergeusers
 * @author    Jordi Pujol-Ahulló <jordi.pujol@urv.cat>
 * @copyright 2013 onwards to Universitat Rovira i Virgili (https://www.urvc.cat)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_mergeusers\local\cli;

defined('MOODLE_INTERNAL') || die();
defined('CLI_SCRIPT') || die();

use coding_exception;
use stdClass;

global $CFG;
require_once($CFG->dirroot . '/lib/clilib.php');

/**
 * Implements a gathering class as an interactive script for manual merging from CLI.
 *
 * @package   tool_mergeusers
 * @author    Jordi Pujol-Ahulló <jordi.pujol@urv.cat>
 * @copyright 2013 onwards to Universitat Rovira i Virgili (https://www.urvc.cat)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cli_gathering implements gathering {
    /** @var int value to use for quitting the CLI script. */
    public const END_USER_ID = -1;
    /**
     * @var merge_request relation of user.ids to merge.
     */
    protected merge_request $current;
    /**
     * @var bool true if user chose to conclude with merging users; false if we are still merging.
     */
    protected bool $end;
    /**
     * @var int zero-based index of the number of asked merging operations.
     */
    protected int $index;

    /**
     * Initialization.
     */
    public function __construct() {
        $this->index = -1;
        $this->end = false;
    }

    /**
     * Asks by command line both users to merge, with a header telling what to do.
     */
    public function rewind(): void {
        cli_heading(get_string('pluginname', 'tool_mergeusers'));
        echo get_string('cligathering:description', 'tool_mergeusers') . PHP_EOL . PHP_EOL;
        echo get_string('cligathering:stopping', 'tool_mergeusers') . PHP_EOL . PHP_EOL;
        $this->next();
    }

    /**
     * Asks for the next pair of users' id to merge.
     * It also detects when anything but a number is introduced, to re-ask for any user id.
     *
     * @throws coding_exception
     */
    public function next(): void {
        $request = new merge_request();

        // Asks for the source user id.
        $request->fromid = $this->get_next_id(get_string('cligathering:fromid', 'tool_mergeusers'));

        // If we have to exit, do it just now.
        if ($request->fromid == self::END_USER_ID) {
            $this->end = true;
            return;
        }

        // Otherwise, ask for the target user id.
        $request->toid = $this->get_next_id(get_string('cligathering:toid', 'tool_mergeusers'));

        // Updates related iterator fields.
        $this->end = $request->toid == self::END_USER_ID;
        $this->current = $request;
        $this->index++;
    }

    /**
     * Gets the next user.id from CLI.
     *
     * @param string $climessage message to show to the user when asking another user.id.
     * @return int user.id to use on the merge; or -1 for quitting the process.
     */
    private function get_next_id(string $climessage): int {
        $userid = 0;
        while ($userid <= 0 && $userid != -1) {
            $userid = (int) cli_input($climessage);
        }
        return $userid;
    }

    /**
     * Tells whether to conclude iteration.
     *
     * @return bool true if to go on with the iteration (we have a pair of users to merge).
     * false to conclude.
     */
    public function valid(): bool {
        return !$this->end;
    }

    /**
     * Gets the current pair of users to merge.
     *
     * @return stdClass object with fromid and toid fields
     */
    public function current(): mixed {
        return $this->current;
    }

    /**
     * Gets current int zero-based index.
     *
     * @return int zero-based index value
     */
    public function key(): mixed {
        return $this->index;
    }
}
