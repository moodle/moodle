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
 * Step definition to generate database fixtures for learning plan system.
 *
 * @package    tool_lp
 * @category   test
 * @copyright  2016 Issam Taboubi <issam.taboubi@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../../../lib/behat/behat_base.php');

/**
 * Step definition for learning plan system.
 *
 * @package    tool_lp
 * @category   test
 * @copyright  2016 Issam Taboubi <issam.taboubi@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_tool_lp extends behat_base {

    /**
     * Click on an entry in the edit menu.
     *
     * @When /^I click on "([^"]*)" of edit menu in the "([^"]*)" row$/
     *
     * @param string $nodetext
     * @param string $rowname
     */
    public function click_on_edit_menu_of_the_row($nodetext, $rowname) {
        $xpathtarget = "//ul//li//ul//li[contains(concat(' ', @class, ' '), ' tool-lp-menu-item ')]//a[contains(.,'" . $nodetext . "')]";

        $this->execute('behat_general::i_click_on_in_the', [get_string('edit'), 'link', $this->escape($rowname), 'table_row']);
        $this->execute('behat_general::i_click_on_in_the', [$xpathtarget, 'xpath_element', $this->escape($rowname), 'table_row']);
    }

    /**
     * Click on competency in the tree.
     *
     * @Given /^I select "([^"]*)" of the competency tree$/
     *
     * @param string $competencyname
     */
    public function select_of_the_competency_tree($competencyname) {
        $xpathtarget = "//li[@role='tree-item']//span[contains(.,'" . $competencyname . "')]";

        $this->execute('behat_general::i_click_on', [$xpathtarget, 'xpath_element']);
    }

    /**
     * Select item from autocomplete list.
     *
     * @Given /^I click on "([^"]*)" item in the autocomplete list$/
     *
     * @param string $item
     */
    public function i_click_on_item_in_the_autocomplete_list($item) {
        $xpathtarget = "//ul[@class='form-autocomplete-suggestions']//li//span//span[contains(.,'" . $item . "')]";

        $this->execute('behat_general::i_click_on', [$xpathtarget, 'xpath_element']);
    }
}
