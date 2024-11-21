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
 * Additional behat steps definition.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../../../lib/behat/behat_base.php');

/**
 * Additional steps definition.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_block_xp extends behat_base {

    /**
     * Go to the front page.
     *
     * There are no standard definitions available from 2.7 so we use our own.
     *
     * @Given /^I am on front page$/
     */
    public function i_am_on_front_page() {
        $this->getSession()->visit($this->locate_path('/?redirect=0'));
    }

    /**
     * Step to edit a student's points.
     *
     * @Given /^I follow edit for "(?P<student>(?:[^"]|\\")*)" in XP report$/
     * @param string $studentname
     */
    public function i_follow_edit_for_in_xp_report($studentname) {
        $rowxpath = "//tr[contains(normalize-space(.), '$studentname')]";

        $this->execute('behat_general::i_click_on_in_the', [
            "[data-toggle='dropdown']", "css_element",
            $rowxpath, "xpath_element",
        ]);

        $this->execute('behat_general::i_click_on_in_the', [
            "Edit", "link",
            $rowxpath, "xpath_element",
        ]);
    }

    /**
     * Step to follow a page menu link.
     *
     * @Given /^I follow "(?P<text>(?:[^"]|\\")*)" in the XP page menu$/
     * @param string $studentname
     */
    public function i_follow_foo_in_xp_page_menu($text) {
        $this->execute('behat_general::i_click_on', [
            "[data-region='block_xp-page_menu'] [data-toggle='dropdown']", "css_element",
        ]);

        $this->execute('behat_general::i_click_on_in_the', [
            "$text", "link",
            "[data-region='block_xp-page_menu'] .dropdown", "css_element",
        ]);
    }

}
