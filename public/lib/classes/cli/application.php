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

namespace core\cli;

use Symfony\Component\Console\Command\CompleteCommand;
use Symfony\Component\Console\Command\DumpCompletionCommand;

/**
 * Moodle CLI application.
 *
 * @package    core
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class application extends \Symfony\Component\Console\Application {
    /**
     * Constructor for the Moodle CLI application.
     *
     * @param \Symfony\Component\Console\CommandLoader\CommandLoaderInterface $commandloader The command loader
     */
    public function __construct(
        \Symfony\Component\Console\CommandLoader\CommandLoaderInterface $commandloader,
    ) {
        parent::__construct('Moodle CLI Application', self::get_moodle_version());
        $this->setCommandLoader($commandloader);

        $this->add(new CompleteCommand());
        $this->add(new DumpCompletionCommand());
    }

    /**
     * Get an input instance for the given command name and arguments.
     *
     * @param string $commandname
     * @param array $args
     * @return \Symfony\Component\Console\Input\InputInterface
     */
    public static function get_input_for_command(
        string $commandname,
        array $args = [],
    ): \Symfony\Component\Console\Input\InputInterface {
        $inputargs = array_merge(['command' => $commandname], $args);
        return new \Symfony\Component\Console\Input\ArrayInput($inputargs);
    }

    /**
     * Get the Moodle version number.
     *
     * @return int
     */
    private static function get_moodle_version(): int {
        require(dirname(__DIR__, 3) . '/version.php');

        return $version;
    }
}
