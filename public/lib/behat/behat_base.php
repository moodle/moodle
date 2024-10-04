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
 * Base class of all steps definitions.
 *
 * This script is only called from Behat as part of it's integration
 * in Moodle.
 *
 * @package   core
 * @category  test
 * @copyright 2012 David Monllaó
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/classes/behat_session_interface.php');
require_once(__DIR__ . '/classes/behat_session_trait.php');

/**
 * Steps definitions base class.
 *
 * To extend by the steps definitions of the different Moodle components.
 *
 * It can not contain steps definitions to avoid duplicates, only utility
 * methods shared between steps.
 *
 * @method NodeElement find_field(string $locator) Finds a form element
 * @method NodeElement find_button(string $locator) Finds a form input submit element or a button
 * @method NodeElement find_link(string $locator) Finds a link on a page
 * @method NodeElement find_file(string $locator) Finds a forum input file element
 *
 * @package   core
 * @category  test
 * @copyright 2012 David Monllaó
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_base extends Behat\MinkExtension\Context\RawMinkContext implements behat_session_interface {

    // All of the functionality of behat_base is shared with form fields via the behat_session_trait trait.
    use behat_session_trait;
}
