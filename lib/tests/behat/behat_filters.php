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
 * Steps definitions related to filters.
 *
 * @package    core
 * @category   test
 * @copyright  2018 the Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Note: You cannot use MOODLE_INTERNAL test here, or include files which do so.
// This file is required by behat before including /config.php.

require_once(__DIR__ . '/../../behat/behat_base.php');

/**
 * Steps definitions related to filters.
 *
 * @package    core
 * @category   test
 * @copyright  2018 the Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_filters extends behat_base {

    /**
     * Set the global filter configuration.
     *
     * @Given /^the "(?P<filter_name>(?:[^"]|\\")*)" filter is "(on|off|disabled)"$/
     *
     * @param string $filtername the name of a filter, e.g. 'glossary'.
     * @param string $statename 'on', 'off' or 'disabled'.
     */
    public function the_filter_is($filtername, $statename) {
        require_once(__DIR__ . '/../../filterlib.php');

        switch ($statename) {
            case 'on':
                $state = TEXTFILTER_ON;
                break;
            case 'off':
                $state = TEXTFILTER_OFF;
                break;
            case 'disabled':
                $state = TEXTFILTER_DISABLED;
                break;
            default:
                throw new coding_exception('Unknown filter state: ' . $statename);
        }
        filter_set_global_state($filtername, $state);
    }
}
