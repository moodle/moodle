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
     * Convert page names to URLs for steps like 'When I am on the "[identifier]" "[page type]" page'.
     *
     * Recognised page names are:
     * | pagetype            | name meaning | description                  |
     * | Course competencies | Course name  | The course competencies page |
     *
     * @param string $page identifies which type of page this is, e.g. 'Course competencies'.
     * @param string $identifier identifies the particular page, e.g. 'C1'.
     * @return moodle_url the corresponding URL.
     * @throws Exception with a meaningful error message if the specified page cannot be found.
     */
    protected function resolve_page_instance_url(string $page, string $identifier): moodle_url {
        switch (strtolower($page)) {
            case 'course competencies':
                $courseid = $this->get_course_id($identifier);
                return new moodle_url('/admin/tool/lp/coursecompetencies.php', [
                    'courseid' => $courseid,
                ]);
            default:
                throw new Exception("Unrecognised page type '{$page}'");
        }
    }

    /**
     * Return a list of the exact named selectors for the component.
     *
     * @return behat_component_named_selector[]
     */
    public static function get_exact_named_selectors(): array {
        return [
            new behat_component_named_selector('competency', [
                "//*[@data-region='coursecompetencies']//table[contains(@class,'managecompetencies')]".
                    "//tr[contains(., //a[@title='View details'][contains(., %locator%)])]",
            ]),
            new behat_component_named_selector('learning plan', [
                "//*[@data-region='plan-competencies']//table[contains(@class,'managecompetencies')]".
                    "//tr[@data-node='user-competency'][contains(., //a[@data-usercompetency='true'][contains(., %locator%)])]",
            ]),
            new behat_component_named_selector('competency description', [
                "//td/p[contains(., %locator%)]",
            ]),
            new behat_component_named_selector('competency grade', [
                "//span[contains(concat(' ', normalize-space(@class), ' '), ' badge ')][contains(., %locator%)]",
            ]),
            new behat_component_named_selector('learning plan rating', [
                "//td[position()=2][contains(., %locator%)]",
            ]),
            new behat_component_named_selector('learning plan proficiency', [
                "//td[position()=3][contains(., %locator%)]",
            ]),
            new behat_component_named_selector('competency page proficiency', [
                "//dt[contains(., 'Proficient')]/following-sibling::dd[1][contains(., %locator%)]",
            ]),
            new behat_component_named_selector('competency page rating', [
                "//dt[contains(., 'Rating')]/following-sibling::dd[1][contains(., %locator%)]",
            ]),
            new behat_component_named_selector('competency page related competency', [
                "//*[@data-region='relatedcompetencies']//a[contains(., %locator%)]",
            ]),
        ];
    }

}
