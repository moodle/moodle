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
 * Steps definitions related to mod_feedback.
 *
 * @package   mod_feedback
 * @category  test
 * @copyright 2016 Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../../../lib/behat/behat_base.php');

use Behat\Gherkin\Node\TableNode as TableNode,
    Behat\Mink\Exception\ExpectationException as ExpectationException;

/**
 * Steps definitions related to mod_feedback.
 *
 * @copyright 2016 Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_mod_feedback extends behat_base {

    /**
     * Adds a question to the existing feedback with filling the form.
     *
     * The form for creating a question should be on one page.
     *
     * @When /^I add a "(?P<question_type_string>(?:[^"]|\\")*)" question to the feedback with:$/
     * @param string $questiontype
     * @param TableNode $questiondata with data for filling the add question form
     */
    public function i_add_question_to_the_feedback_with($questiontype, TableNode $questiondata) {

        $questiontype = $this->escape($questiontype);
        $this->execute('behat_forms::i_select_from_the_singleselect', array($questiontype, 'typ'));

        // Wait again, for page to reloaded.
        $this->execute('behat_general::i_wait_to_be_redirected');

        $rows = $questiondata->getRows();
        $modifiedrows = array();
        foreach ($rows as $row) {
            foreach ($row as $key => $value) {
                $row[$key] = preg_replace('|\\\\n|', "\n", $value);
            }
            $modifiedrows[] = $row;
        }
        $newdata = new TableNode($modifiedrows);

        $this->execute("behat_forms::i_set_the_following_fields_to_these_values", $newdata);

        $saveitem = $this->escape(get_string('save'));
        $this->execute("behat_forms::press_button", $saveitem);
    }

    /**
     * Adds a question to the existing feedback with filling the form.
     *
     * The form for creating a question should be on one page.
     *
     * @When /^I add a page break to the feedback$/
     */
    public function i_add_a_page_break_to_the_feedback() {

        $questiontype = $this->escape(get_string('add_pagebreak', 'feedback'));
        $this->execute('behat_forms::i_select_from_the_singleselect', array($questiontype, 'typ'));

        // Wait again, for page to reloaded.
        $this->execute('behat_general::i_wait_to_be_redirected');
    }

    /**
     * Quick way to generate answers to a one-page feedback.
     *
     * @When /^I log in as "(?P<user_name_string>(?:[^"]|\\")*)" and complete feedback "(?P<feedback_name_string>(?:[^"]|\\")*)" in course "(?P<course_name_string>(?:[^"]|\\")*)" with:$/
     * @param string $questiontype
     * @param TableNode $questiondata with data for filling the add question form
     */
    public function i_log_in_as_and_complete_feedback_in_course($username, $feedbackname, $coursename, TableNode $answers) {
        $username = $this->escape($username);
        $coursename = $this->escape($coursename);
        $feedbackname = $this->escape($feedbackname);
        $completeform = $this->escape(get_string('complete_the_form', 'feedback'));

        // Log in as user.
        $this->execute('behat_auth::i_log_in_as', $username);

        // Navigate to feedback complete form.
        $this->execute('behat_navigation::i_am_on_page_instance', [$feedbackname, 'feedback activity']);
        $this->execute('behat_general::click_link', $completeform);

        // Fill form and submit.
        $this->execute("behat_forms::i_set_the_following_fields_to_these_values", $answers);
        $this->execute("behat_forms::press_button", 'Submit your answers');

        // Log out.
        $this->execute('behat_auth::i_log_out');
    }

    /**
     * Exports feedback and makes sure the export file is the same as in the fixture
     *
     * @Then /^following "(?P<link_string>(?:[^"]|\\")*)" should export feedback identical to "(?P<filename_string>(?:[^"]|\\")*)"$/
     * @param string $link
     * @param string $filename
     */
    public function following_should_export_feedback_identical_to($link, $filename) {
        global $CFG;
        $exception = new ExpectationException('Error while downloading data from ' . $link, $this->getSession());

        // It will stop spinning once file is downloaded or time out.
        $behatgeneralcontext = behat_context_helper::get('behat_general');
        $result = $this->spin(
            function($context, $args) use ($behatgeneralcontext) {
                $link = $args['link'];
                return $behatgeneralcontext->download_file_from_link($link);
            },
            array('link' => $link),
            behat_base::get_extended_timeout(),
            $exception
        );

        $this->compare_exports(file_get_contents($CFG->dirroot . '/' . $filename), $result);
    }

    /**
     * Clicks on Show chart data to display chart data if not visible.
     *
     * @Then /^I show chart data for the "(?P<feedback_name_string>(?:[^"]|\\")*)" feedback$/
     * @param string $feedbackname name of the feedback for which chart data needs to be shown.
     */
    public function i_show_chart_data_for_the_feedback($feedbackname) {

        $feedbackxpath = "//th[contains(normalize-space(string(.)), \"" . $feedbackname . "\")]/ancestor::table//" .
            "div[contains(concat(' ', normalize-space(@class), ' '), ' chart-table ')]" .
            "//p[contains(concat(' ', normalize-space(@class), ' '), ' chart-table-expand ') and ".
            "//a[contains(normalize-space(string(.)), '".get_string('showchartdata')."')]]";

        $charttabledataxpath = $feedbackxpath .
            "/following-sibling::div[contains(concat(' ', normalize-space(@class), ' '), ' chart-table-data ')][1]";

        // If chart data is not visible then expand.
        $node = $this->get_selected_node("xpath_element", $charttabledataxpath);
        if ($node) {
            if ($node->getAttribute('aria-expanded') === 'false') {
                $this->execute('behat_general::i_click_on_in_the', array(
                    get_string('showchartdata'),
                    'link',
                    $feedbackxpath,
                    'xpath_element'
                ));
            }
        }
    }

    /**
     * Ensures two feedback export files are identical
     *
     * Maps the itemids and converts DEPENDITEM if necessary
     *
     * Throws ExpectationException if exports are different
     *
     * @param string $expected
     * @param string $actual
     * @throws ExpectationException
     */
    protected function compare_exports($expected, $actual) {
        $dataexpected = xmlize($expected, 1, 'UTF-8');
        $dataexpected = $dataexpected['FEEDBACK']['#']['ITEMS'][0]['#']['ITEM'];
        $dataactual = xmlize($actual, 1, 'UTF-8');
        $dataactual = $dataactual['FEEDBACK']['#']['ITEMS'][0]['#']['ITEM'];

        if (count($dataexpected) != count($dataactual)) {
            throw new ExpectationException('Expected ' . count($dataexpected) .
                    ' items in the export file, found ' . count($dataactual), $this->getSession());
        }

        $itemmapping = array();
        $itemactual = reset($dataactual);
        foreach ($dataexpected as $idx => $itemexpected) {
            // Map ITEMID and DEPENDITEM.
            $itemmapping[intval($itemactual['#']['ITEMID'][0]['#'])] = intval($itemexpected['#']['ITEMID'][0]['#']);
            $itemactual['#']['ITEMID'][0]['#'] = $itemexpected['#']['ITEMID'][0]['#'];
            $expecteddependitem = $actualdependitem = 0;
            if (isset($itemexpected['#']['DEPENDITEM'][0]['#'])) {
                $expecteddependitem = intval($itemexpected['#']['DEPENDITEM'][0]['#']);
            }
            if (isset($itemactual['#']['DEPENDITEM'][0]['#'])) {
                $actualdependitem = intval($itemactual['#']['DEPENDITEM'][0]['#']);
            }
            if ($expecteddependitem && !$actualdependitem) {
                throw new ExpectationException('Expected DEPENDITEM in ' . ($idx + 1) . 'th item', $this->getSession());
            }
            if (!$expecteddependitem && $actualdependitem) {
                throw new ExpectationException('Unexpected DEPENDITEM in ' . ($idx + 1) . 'th item', $this->getSession());
            }
            if ($expecteddependitem && $actualdependitem) {
                if (!isset($itemmapping[$actualdependitem]) || $itemmapping[$actualdependitem] != $expecteddependitem) {
                    throw new ExpectationException('Unknown DEPENDITEM in ' . ($idx + 1) . 'th item', $this->getSession());
                }
                $itemactual['#']['DEPENDITEM'][0]['#'] = $itemexpected['#']['DEPENDITEM'][0]['#'];
            }
            // Now, after mapping, $itemexpected should be exactly the same as $itemactual.
            if (json_encode($itemexpected) !== json_encode($itemactual)) {
                throw new ExpectationException('Actual ' . ($idx + 1) . 'th item does not match expected', $this->getSession());
            }
            // Get the next itemactual.
            $itemactual = next($dataactual);
        }
    }
}
