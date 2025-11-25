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

namespace core\local\cli;

/**
 * CLI script shutdown helper class.
 *
 * @package    core
 * @copyright  2019 Brendan Heywood <brendan@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class shutdown {
    /** @var bool Should we exit gracefully at the next opportunity? */
    protected static $cligracefulexit = false;

    /**
     * Declares that this CLI script can gracefully handle signals
     *
     * @return void
     */
    public static function script_supports_graceful_exit(): void {
        \core\shutdown_manager::register_signal_handler('\core\local\cli\shutdown::signal_handler');
    }

    /**
     * Should we gracefully exit?
     *
     * @return bool true if we should gracefully exit
     */
    public static function should_gracefully_exit(): bool {
        return self::$cligracefulexit;
    }

    /**
     * Handle the signal
     *
     * The first signal flags a graceful exit. If a second signal is received
     * then it immediately exits.
     *
     * @param int $signo The signal number
     * @return bool true if we should exit
     */
    public static function signal_handler(int $signo): bool {
        if (self::$cligracefulexit) {
            cli_heading(get_string('cliexitnow', 'admin'));
            return true;
        }

        cli_heading(get_string('cliexitgraceful', 'admin'));
        self::$cligracefulexit = true;
        return false;
    }

}
