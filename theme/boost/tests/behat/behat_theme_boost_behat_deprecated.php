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
 * Deprecated steps overrides.
 *
 * @copyright  2018 Victor Deniz
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../../../lib/tests/behat/behat_deprecated.php');

/**
 * Deprecated behat step definitions.
 *
 * @package    theme_boost
 * @category   test
 * @copyright  2018 Victor Deniz
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_theme_boost_behat_deprecated extends behat_deprecated {

    /**
     * Click link in navigation tree that matches the text in parentnode/s (seperated using greater-than character if more than one)
     *
     * @throws ExpectationException
     * @param string $nodetext navigation node to click.
     * @param string $parentnodes comma seperated list of parent nodes.
     * @return void
     * @deprecated since Moodle 3.6 MDL-57281 - please do not use this definition step any more.
     * @todo MDL-63004 This will be deleted in Moodle 4.0.
     */
    public function i_navigate_to_node_in($nodetext, $parentnodes) {
        $alternative[] = 'I navigate to "PATH" in current page administration';
        $alternative[] = 'I navigate to "PATH" in site administration';
        $alternative[] = 'I navigate to "TAB1 > TAB2" in the course gradebook';
        $alternative[] = 'I navigate to course participants';
        $alternative[] = 'If some items are not available without Navigation block at all, one can use combination of:
                              I add the "Navigation" block if not present
                              I click on "LINK" "link" in the "Navigation" "block"';

        $this->deprecated_message($alternative);

        $parentnodes = array_map('trim', explode('>', $parentnodes));
        $nodelist = array_merge($parentnodes, [$nodetext]);
        $firstnode = array_shift($nodelist);

        if ($firstnode === get_string('administrationsite')) {
            $this->execute('behat_theme_boost_behat_navigation::i_select_from_flat_navigation_drawer',
                    array(get_string('administrationsite')));
            $this->execute('behat_theme_boost_behat_navigation::select_on_administration_page', array($nodelist));
            return;
        }

        if ($firstnode === get_string('sitepages')) {
            if ($nodetext === get_string('calendar', 'calendar')) {
                $this->execute('behat_theme_boost_behat_navigation::i_select_from_flat_navigation_drawer',
                        array(($nodetext)));
            } else {
                // TODO MDL-57120 other links under "Site pages" are not accessible without navigation block.
                $this->execute('behat_theme_boost_behat_navigation::select_node_in_navigation',
                        array($nodetext, $parentnodes));
            }
            return;
        }

        if ($firstnode === get_string('courseadministration')) {
            // Administration menu is available only on the main course page where settings in Administration
            // block (original purpose of the step) are available on every course page.
            $this->execute('behat_theme_boost_behat_navigation::go_to_main_course_page', array());
        }

        $this->execute('behat_theme_boost_behat_navigation::select_from_administration_menu', array($nodelist));
    }
}
