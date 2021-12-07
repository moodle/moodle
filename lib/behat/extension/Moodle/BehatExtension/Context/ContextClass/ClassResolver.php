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
 * Moodle behat context class resolver.
 *
 * @package    behat
 * @copyright  2104 Rajesh Taneja <rajesh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace Moodle\BehatExtension\Context\ContextClass;

use Behat\Behat\Context\Environment\Handler\ContextEnvironmentHandler;
use Behat\Behat\Context\ContextClass\ClassResolver as Resolver;

/**
 * Resolves arbitrary context strings into a context classes.
 *
 * @see ContextEnvironmentHandler
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
final class ClassResolver implements Resolver {

    /**
     * @var array keep list of all behat contexts in moodle.
     */
    private $moodlebehatcontexts = null;

    /**
     * @param $parameters array list of params provided to moodle.
     */
    public function __construct($parameters) {
        $this->moodlebehatcontexts = $parameters['steps_definitions'];
    }
    /**
     * Checks if resolvers supports provided class.
     * Moodle behat context class starts with behat_
     *
     * @param string $contextString
     *
     * @return Boolean
     */
    public function supportsClass($contextString) {
        return (strpos($contextString, 'behat_') === 0);
    }

    /**
     * Resolves context class.
     *
     * @param string $contexclass
     *
     * @return string context class.
     */
    public function resolveClass($contextclass) {
        if (!is_array($this->moodlebehatcontexts)) {
            throw new \RuntimeException('There are no Moodle context with steps definitions');
        }

        // Using the key as context identifier load context class.
        if (!empty($this->moodlebehatcontexts[$contextclass]) &&
            (file_exists($this->moodlebehatcontexts[$contextclass]))) {
            require_once($this->moodlebehatcontexts[$contextclass]);
        } else {
            throw new \RuntimeException('Moodle behat context "'.$contextclass.'" not found');
        }
        return $contextclass;
    }
}
