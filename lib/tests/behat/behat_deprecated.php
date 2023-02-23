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

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../../lib/behat/behat_deprecated_base.php');

use Behat\Mink\Exception\ElementNotFoundException as ElementNotFoundException;

/**
 * Steps definitions that are now deprecated and will be removed in the next releases.
 *
 * This file only contains the steps that previously were in the behat_*.php files in the SAME DIRECTORY.
 * When deprecating steps from other components or plugins, create a behat_COMPONENT_deprecated.php
 * file in the same directory where the steps were defined.
 *
 * @package    core
 * @category   test
 * @copyright  2013 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_deprecated extends behat_deprecated_base {

    /**
     * Clicks link with specified id|title|alt|text in the flat navigation drawer.
     *
     * @When /^I select "(?P<link_string>(?:[^"]|\\")*)" from flat navigation drawer$/
     * @param string $link
     * @deprecated Since Moodle 4.0
     */
    public function i_select_from_flat_navigation_drawer(string $link) {
        $this->deprecated_message(['i_select_from_primary_navigation', 'i_select_from_secondary_navigation']);

        $this->execute('behat_navigation::i_open_flat_navigation_drawer');
        $this->execute('behat_general::i_click_on_in_the', [$link, 'link', '#nav-drawer', 'css_element']);
    }
}
