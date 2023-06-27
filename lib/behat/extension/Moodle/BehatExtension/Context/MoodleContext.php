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

namespace Moodle\BehatExtension\Context;

use Behat\MinkExtension\Context\RawMinkContext;

// phpcs:disable moodle.NamingConventions.ValidFunctionName.LowercaseMethod

/**
 * Moodle contexts loader
 *
 * It gathers all the available steps definitions reading the
 * Moodle configuration file
 *
 * @package core
 * @copyright  2012 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class MoodleContext extends RawMinkContext {

    /** @var array Moodle features and steps definitions list */
    protected $moodleconfig;

    /**
     * Includes all the specified Moodle subcontexts.
     *
     * @param array $parameters
     */
    public function setMoodleConfig(array $parameters): void {
        $this->moodleconfig = $parameters;
    }
}
