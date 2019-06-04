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
 * Steps definitions related to the SCORM activity module.
 *
 * @package    mod_scorm
 * @category   test
 * @copyright  2019 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../../../lib/behat/behat_base.php');

use Behat\Behat\Hook\Scope\AfterScenarioScope;

/**
 * Steps definitions related to the SCORM activity module.
 *
 * @package    mod_scorm
 * @category   test
 * @copyright  2019 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_mod_scorm extends behat_base {

    /**
     * Restart the Seleium Session after each mod_scorm Scenario.
     *
     * This prevents issues with the scorm player's onbeforeunload event, and cached SCORM content being served to the
     * browser in subsequent tests.
     *
     * @AfterScenario @mod_scorm
     * @param AfterScenarioScope $scope The scenario scope
     */
    public function reset_after_scorm(AfterScenarioScope $scope) {
        $this->getSession()->stop();
    }
}
