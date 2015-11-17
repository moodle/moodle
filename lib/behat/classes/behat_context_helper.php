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
 * Helper to get behat contexts from other contexts.
 *
 * @package    core
 * @category   test
 * @copyright  2014 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

use \Behat\Behat\Context\BehatContext;

/**
 * Helper to get behat contexts.
 *
 * @package    core
 * @category   test
 * @copyright  2014 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_context_helper {

    /**
     * @var BehatContext main behat context.
     */
    protected static $maincontext = false;

    /**
     * Save main behat context reference to be used for finding sub-contexts.
     *
     * @param BehatContext $maincontext
     * @return void
     */
    public static function set_main_context(BehatContext $maincontext) {
        self::$maincontext = $maincontext;
    }

    /**
     * Gets the required context.
     *
     * Getting a context you get access to all the steps
     * that uses direct API calls; steps returning step chains
     * can not be executed like this.
     *
     * @throws coding_exception
     * @param string $classname Context identifier (the class name).
     * @return behat_base
     */
    public static function get($classname) {

        if (!$subcontext = self::$maincontext->getSubcontextByClassName($classname)) {
            throw coding_exception('The required "' . $classname . '" class does not exist');
        }

        return $subcontext;
    }
}
