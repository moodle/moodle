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
 * Step definition to generate database fixtures for learning plan report.
 *
 * @package    report_lpmonitoring
 * @category   test
 * @author     Issam Taboubi <issam.taboubi@umontreal.ca>
 * @copyright  2016 Université de Montréal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../../lib/behat/behat_base.php');

use Behat\Mink\Exception\ElementNotFoundException as ElementNotFoundException;
use Behat\Mink\Exception\ExpectationException as ExpectationException;
use report_lpmonitoring\api;

/**
 * Step definition for learning plan report.
 *
 * @package    report_lpmonitoring
 * @category   test
 * @author     Issam Taboubi <issam.taboubi@umontreal.ca>
 * @copyright  2016 Université de Montréal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_report_lpmonitoring extends behat_base {

    /**
     * Checks, that the specified element contains the specified text in the competency detail rating.
     *
     * @Then /^I should see "(?P<nb>[^"]*)" for "(?P<txt>[^"]*)" in the row "(?P<r>[^"]*)" of "(?P<c>[^"]*)" "(?P<t>[^"]*)" rating$/
     * @throws ElementNotFoundException
     * @throws ExpectationException
     * @param int $numberrating
     * @param string $scalevalue
     * @param int $rownumber
     * @param string $competencyname
     * @param string $type data-type value, for example 'incourse', 'incm'
     */
    public function i_see_nbrating_of_the_scalevalue_in_the_competency($numberrating, $scalevalue, $rownumber, $competencyname,
            $type) {
        // Building xpath.
        $xpath = "//table[contains(@class, 'tile_info') and contains(@data-type, '$type') and "
                . "ancestor-or-self::div[contains(., '$competencyname')]]/"
                . "tbody/tr[$rownumber]/td[contains(., '$scalevalue')]/following-sibling::td[1]";
        $this->execute("behat_general::assert_element_contains_text",
            [$numberrating, $xpath, "xpath_element"]
        );
    }

    /**
     * Checks, that the specified element contains the specified text in the competency detail block.
     *
     * @Then /^I should see "(?P<text>[^"]*)" in "(?P<class>[^"]*)" of the competency "(?P<competency>[^"]*)"$/
     * @throws ElementNotFoundException
     * @throws ExpectationException
     * @param string $texttoverify
     * @param string $targetclass
     * @param string $compname
     */
    public function i_see_text_in_element_of_the_competency_detail($texttoverify, $targetclass, $compname) {

        // Building xpath.
        $xpath = '';
        switch ($targetclass) {
            case 'level-proficiency':
                $xpath = "//div[contains(., '$compname')]/div/div/div/div/div[contains(@class, '$targetclass')]";
                break;
            case 'finalrate':
                $xpath = "//div[contains(., '$compname')]/div/div/div/div/div/span[contains(@class, 'badge')]";
                break;
            case 'level':
                $xpath = "//span[contains(@class, '$targetclass') and ancestor-or-self::div/h4/a[contains(., '$compname')]]";
                break;
            case 'no-data-available':
                 $xpath = "//div[contains(., '$compname')]/div/div/div/div/div/"
                    . "table/tbody/tr/td/div[contains(@class, '$targetclass')]";
                break;
            case 'incourse':
                $xpath = "//div[contains(@class, '$targetclass') and ancestor-or-self::div/div/h4/a[contains(., '$compname')]]";
                break;
            default:
                $xpath = "//a[contains(@class, '$targetclass') and ancestor-or-self::div[contains(., '$compname')]]";
                break;
        }

        $this->execute("behat_general::assert_element_contains_text",
            [$texttoverify, $xpath, "xpath_element"]
        );
    }

    /**
     * Click on the specified element contains the specified text in the competency detail block.
     *
     * @Then /^I click on "(?P<class>[^"]*)" of the competency "(?P<competency>[^"]*)"$/
     * @throws ElementNotFoundException
     * @throws ExpectationException
     * @param string $targetclass
     * @param string $competencyname
     */
    public function i_click_on_element_of_the_competency_detail($targetclass, $competencyname) {

        // Building xpath.
        $xpath = '';
        switch ($targetclass) {
            case 'rate-competency':
                $xpath = "//div[contains(., '$competencyname')]/div/div/div/div/button[contains(@class, '$targetclass')]";
                break;
            default:
                $xpath = "//a[contains(@class, '$targetclass') and ancestor-or-self::div[contains(., '$competencyname')]]";
                break;
        }

        $this->execute("behat_general::i_click_on", [$xpath, "xpath_element"]);
    }

    /**
     * Click on the specified element contains the specified text in the competency detail rating.
     *
     * @Then /^I click on "(?P<nb>[^"]*)" for "(?P<txt>[^"]*)" in the row "(?P<r>[^"]*)" of "(?P<c>[^"]*)" "(?P<t>[^"]*)" rating$/
     * @throws ElementNotFoundException
     * @throws ExpectationException
     * @param int $numberrating
     * @param string $scalevalue
     * @param int $rownumber
     * @param string $competencyname
     * @param string $type data-type value, for example 'incourse', 'incm'
     */
    public function i_click_on_rating_of_the_scalevalue_in_the_competency($numberrating, $scalevalue, $rownumber,
            $competencyname, $type) {
        // Building xpath.
        $xpath = "//table[contains(@class, 'tile_info') and contains(@data-type, '$type') and "
                . "ancestor-or-self::div[contains(., '$competencyname')]]/tbody/"
                . "tr[$rownumber]/td[contains(., '$scalevalue')]/following-sibling::td[1]/a[contains(., '$numberrating')]";
        $this->execute('behat_general::i_click_on', [$xpath, "xpath_element"]);
    }

    /**
     * Open/close the competency detail block.
     *
     * @Then /^I toggle the "(?P<competency_string>(?:[^"]|\\")*)" detail$/
     * @throws ElementNotFoundException
     * @throws ExpectationException
     * @param int $competency
     */
    public function i_toggle_the_competency_detail_block($competency) {

        // Building xpath.
        $xpath = "//a[contains(@class, 'collapse-link') and ancestor-or-self::div/h4/a[contains(., '$competency')]]";
        $this->execute('behat_general::i_click_on', [$xpath, "xpath_element"]);
    }

    /**
     * Should see item from autocomplete list.
     *
     * @Given /^I should see "([^"]*)" item in the autocomplete list$/
     *
     * @param string $item
     */
    public function i_should_see_item_in_the_autocomplete_list($item) {
        $xpathtarget = "//ul[@class='form-autocomplete-suggestions']//li//span[contains(.,'" . $item . "')]";

        $this->execute('behat_general::should_exist', [$xpathtarget, 'xpath_element']);
    }

    /**
     * Should not see item from autocomplete list.
     *
     * @Given /^I should not see "([^"]*)" item in the autocomplete list$/
     *
     * @param string $item
     */
    public function i_should_not_see_item_in_the_autocomplete_list($item) {
        $xpathtarget = "//ul[@class='form-autocomplete-suggestions']//li//span//span[contains(.,'" . $item . "')]";

        $this->execute('behat_general::should_not_exist', [$xpathtarget, 'xpath_element']);
    }

    /**
     * Should see text value in specific row and column of table.
     *
     * @Then I should see :value in :row row :column column of :table table
     *
     * @param string $value
     * @param string $row
     * @param string $column
     * @param string $table
     */
    public function i_should_see_in_row_column_of_table($value, $row, $column, $table) {
        // Find the visible table (in case more than one table $table is found).
        list($selector, $locator) = $this->transform_selector('table', $table);
        $tablenodes = $this->find_all($selector, $locator);
        $visiblenode = null;
        foreach ($tablenodes as $node) {
            if ($node->isVisible()) {
                $visiblenode = $node;
                break;
            }
        }
        $tablexpath = $visiblenode->getXpath();

        $rowliteral = \behat_context_helper::escape($row);
        $valueliteral = \behat_context_helper::escape($value);
        $columnliteral = \behat_context_helper::escape($column);

        if (preg_match('/^-?(\d+)-?$/', $column, $columnasnumber)) {
            // Column indicated as a number, just use it as position of the column.
            $columnpositionxpath = "/child::*[position() = {$columnasnumber[1]}]";
        } else {
            // Header can be in thead or tbody (first row), following xpath should work.
            $theadheaderxpath = "thead/tr[1]/th[(normalize-space(.)=" . $columnliteral . " or a[normalize-space(text())=" .
                    $columnliteral . "] or div[normalize-space(text())=" .
                    $columnliteral . "])][not(contains(@class, 'switchsearchhidden'))]";
            $tbodyheaderxpath = "tbody/tr[1]/td[(normalize-space(.)=" . $columnliteral . " or a[normalize-space(text())=" .
                    $columnliteral . "] or div[normalize-space(text())=" .
                    $columnliteral . "])][not(contains(@class, 'switchsearchhidden'))]";

            // Check if column exists.
            $columnheaderxpath = $tablexpath . "[" . $theadheaderxpath . " | " . $tbodyheaderxpath . "]";
            $columnheader = $this->getSession()->getDriver()->find($columnheaderxpath);
            if (empty($columnheader)) {
                $columnexceptionmsg = $column . '" in table "' . $table . '"';
                throw new ElementNotFoundException($this->getSession(), "\n$columnheaderxpath\n\n".'Column', null,
                        $columnexceptionmsg);
            }
            // Following conditions were considered before finding column count.
            // 1. Table header can be in thead/tr/th or tbody/tr/td[1].
            // 2. First column can have th (Gradebook -> user report), so having lenient sibling check.
            $columnpositionxpath = "/child::*[position() = count(" . $tablexpath . "/" . $theadheaderxpath .
                "/preceding-sibling::*) + 1]";
        }

        // Check if value exists in specific row/column.
        // Get row xpath.
        // GoutteDriver uses DomCrawler\Crawler and it is making XPath relative to the current context, so use descendant.
        $rowxpath = $tablexpath."/tbody/tr[descendant::th[contains(., " . $rowliteral .
                    ")] | descendant::td[contains(., " . $rowliteral . ")]]";

        $columnvaluexpath = $rowxpath . $columnpositionxpath . "[contains(normalize-space(.)," . $valueliteral . ")]";

        // Looks for the requested node inside the container node.
        $columnnode = $this->getSession()->getDriver()->find($columnvaluexpath);
        if (empty($columnnode)) {
            $locatorexceptionmsg = $value . '" in "' . $row . '" row with column "' . $column;
            throw new ElementNotFoundException($this->getSession(), "\n$columnvaluexpath\n\n".'Column value', null,
                    $locatorexceptionmsg);
        }
    }

    /**
     * Should see value tagged by dt and dd.
     *
     * @Then I should see :dd dd in :dt dt
     * @param string $dd
     * @param string $dt
     */
    public function i_should_see_dd_in_dt($dd, $dt) {
        $ddliteral = \behat_context_helper::escape($dd);
        $dtliteral = \behat_context_helper::escape($dt);
        $xpathtarget = "//dt[contains(., $dtliteral)]/following-sibling::dd[contains(., $ddliteral)]";

        $this->execute('behat_general::should_exist', [$xpathtarget, 'xpath_element']);
    }

    /**
     * If course module competency grading is not enabled, skip the test.
     *
     * @Given /^course module competency grading is enabled$/
     */
    public function course_module_competency_grading_is_enabled() {
        if (!api::is_cm_comptency_grading_enabled()) {
            throw new \Moodle\BehatExtension\Exception\SkippedException;
        }
    }

    /**
     * If course module competency grading is enabled, skip the test.
     *
     * @Given /^course module competency grading is not enabled$/
     */
    public function course_module_competency_grading_is_not_enabled() {
        if (api::is_cm_comptency_grading_enabled()) {
            throw new \Moodle\BehatExtension\Exception\SkippedException;
        }
    }

    /**
     * If hide competency rating is not enabled, skip the test.
     *
     * @Given /^hide competency rating is enabled$/
     */
    public function hide_competency_rating_is_enabled() {
        if (!api::is_display_rating_enabled()) {
            throw new \Moodle\BehatExtension\Exception\SkippedException;
        }
    }

    /**
     * Convert page names to URLs for steps like 'When I am on the "[identifier]" "[page type]" page'.
     *
     * Recognised page names are:
     * | Page type                  | Identifier meaning        | description                          |
     * | Category                   | category idnumber         | List of courses in that category.    |
     * | Course                     | course shortname          | Main course home pag                 |
     * | Activity                   | activity idnumber         | Start page for that activity         |
     * | Activity editing           | activity idnumber         | Edit settings page for that activity |
     * | [modname] Activity         | activity name or idnumber | Start page for that activity         |
     * | [modname] Activity editing | activity name or idnumber | Edit settings page for that activity |
     *
     * Examples:
     *
     * When I am on the "Welcome to ECON101" "forum activity" page logged in as student1
     *
     * @param string $type identifies which type of page this is, e.g. 'Category page'.
     * @param string $identifier identifies the particular page, e.g. 'test-cat'.
     * @return moodle_url the corresponding URL.
     * @throws Exception with a meaningful error message if the specified page cannot be found.
     */
    protected function resolve_core_page_instance_url(string $type, string $identifier): moodle_url {
        global $DB;

        $type = strtolower($type);

        $categoryid = $this->get_category_id($identifier);
        if (!$categoryid) {
            throw new Exception('The specified category with idnumber "' . $identifier . '" does not exist');
        }
        return new moodle_url('/course/index.php', ['categoryid' => $categoryid]);

        throw new Exception('Unrecognised core page type "' . $type . '."');
    }

    /**
     * Go to the lpmonitoring page for category.
     *
     * @When I am on :category lpmonitoring page
     * @param string $category
     */
    public function i_on_category_lpmonitoring_page($category) {

        $categoryid = $this->get_category_id($category);
        $categorycontext = \context_coursecat::instance($categoryid);
        if (!$categoryid) {
            throw new Exception('The specified category with idnumber "' . $category . '" does not exist');
        }
        $url = new moodle_url('/report/lpmonitoring/index.php', ['pagecontextid' => $categorycontext->id]);
        $this->execute('behat_general::i_visit', [$url]);
    }

}
