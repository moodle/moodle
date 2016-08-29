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
 * Steps definitions that will be deprecated in the next releases.
 *
 * @package    core
 * @category   test
 * @copyright  2013 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../../lib/behat/behat_base.php');

use Behat\Mink\Exception\ElementNotFoundException as ElementNotFoundException,
    Behat\Gherkin\Node\TableNode as TableNode,
    Behat\Gherkin\Node\PyStringNode as PyStringNode;

/**
 * Deprecated behat step definitions.
 *
 * @package    core
 * @category   test
 * @copyright  2013 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_deprecated extends behat_base {

    /**
     * @Given /^I click on "(?P<element_string>(?:[^"]|\\")*)" "(?P<selector_string>(?:[^"]|\\")*)" in the "(?P<row_text_string>(?:[^"]|\\")*)" table row$/
     * @deprecated since Moodle 2.7 MDL-42627 - please do not use this step any more.
     */
    public function i_click_on_in_the_table_row($element, $selectortype, $tablerowtext) {
        $alternative = 'I click on "' . $this->escape($element) . '" "' . $this->escape($selectortype) .
            '" in the "' . $this->escape($tablerowtext) . '" "table_row"';
        $this->deprecated_message($alternative, true);
    }

    /**
     * @Given /^I go to notifications page$/
     * @deprecated since Moodle 2.7 MDL-42731 - please do not use this step any more.
     */
    public function i_go_to_notifications_page() {
        $alternative = array(
            'I expand "' . get_string('administrationsite') . '" node',
            'I click on "' . get_string('notifications') . '" "link" in the "'.get_string('administration').'" "block"'
        );
        $this->deprecated_message($alternative, true);
    }

    /**
     * @When /^I add "(?P<filename_string>(?:[^"]|\\")*)" file from recent files to "(?P<filepicker_field_string>(?:[^"]|\\")*)" filepicker$/
     * @deprecated since Moodle 2.7 MDL-42174 - please do not use this step any more.
     */
    public function i_add_file_from_recent_files_to_filepicker($filename, $filepickerelement) {
        $reponame = get_string('pluginname', 'repository_recent');
        $alternative = 'I add "' . $this->escape($filename) . '" file from "' .
                $reponame . '" to "' . $this->escape($filepickerelement) . '" filemanager';
        $this->deprecated_message($alternative, true);
    }

    /**
     * @When /^I upload "(?P<filepath_string>(?:[^"]|\\")*)" file to "(?P<filepicker_field_string>(?:[^"]|\\")*)" filepicker$/
     * @deprecated since Moodle 2.7 MDL-42174 - please do not use this step any more.
     */
    public function i_upload_file_to_filepicker($filepath, $filepickerelement) {
        $alternative = 'I upload "' . $this->escape($filepath) . '" file to "' .
                $this->escape($filepickerelement) . '" filemanager';
        $this->deprecated_message($alternative, true);
    }

    /**
     * @Given /^I create "(?P<foldername_string>(?:[^"]|\\")*)" folder in "(?P<filepicker_field_string>(?:[^"]|\\")*)" filepicker$/
     * @deprecated since Moodle 2.7 MDL-42174 - please do not use this step any more.
     */
    public function i_create_folder_in_filepicker($foldername, $filepickerelement) {
        $alternative = 'I create "' . $this->escape($foldername) .
                '" folder in "' . $this->escape($filepickerelement) . '" filemanager';
        $this->deprecated_message($alternative, true);
    }

    /**
     * @Given /^I open "(?P<foldername_string>(?:[^"]|\\")*)" folder from "(?P<filepicker_field_string>(?:[^"]|\\")*)" filepicker$/
     * @deprecated since Moodle 2.7 MDL-42174 - please do not use this step any more.
     */
    public function i_open_folder_from_filepicker($foldername, $filepickerelement) {
        $alternative = 'I open "' . $this->escape($foldername) . '" folder from "' .
                $this->escape($filepickerelement) . '" filemanager';
        $this->deprecated_message($alternative, true);
    }

    /**
     * @Given /^I unzip "(?P<filename_string>(?:[^"]|\\")*)" file from "(?P<filepicker_field_string>(?:[^"]|\\")*)" filepicker$/
     * @deprecated since Moodle 2.7 MDL-42174 - please do not use this step any more.
     */
    public function i_unzip_file_from_filepicker($filename, $filepickerelement) {
        $alternative = 'I unzip "' . $this->escape($filename) . '" file from "' .
                $this->escape($filepickerelement) . '" filemanager';
        $this->deprecated_message($alternative, true);
    }

    /**
     * @Given /^I zip "(?P<filename_string>(?:[^"]|\\")*)" folder from "(?P<filepicker_field_string>(?:[^"]|\\")*)" filepicker$/
     * @deprecated since Moodle 2.7 MDL-42174 - please do not use this step any more.
     */
    public function i_zip_folder_from_filepicker($foldername, $filepickerelement) {
        $alternative = 'I zip "' . $this->escape($foldername) . '" folder from "' .
                $this->escape($filepickerelement) . '" filemanager';
        $this->deprecated_message($alternative, true);
    }

    /**
     * @Given /^I delete "(?P<file_or_folder_name_string>(?:[^"]|\\")*)" from "(?P<filepicker_field_string>(?:[^"]|\\")*)" filepicker$/
     * @deprecated since Moodle 2.7 MDL-42174 - please do not use this step any more.
     */
    public function i_delete_file_from_filepicker($name, $filepickerelement) {
        $alternative = 'I delete "' . $this->escape($name) . '" from "' .
                $this->escape($filepickerelement) . '" filemanager';
        $this->deprecated_message($alternative, true);
    }

    /**
     * @Given /^I send "(?P<message_contents_string>(?:[^"]|\\")*)" message to "(?P<username_string>(?:[^"]|\\")*)"$/
     * @deprecated since Moodle 2.7 MDL-43584 - please do not use this step any more.
     */
    public function i_send_message_to_user($messagecontent, $tousername) {
        $alternative = 'I send "' . $this->escape($messagecontent) . '" message to "USER_FULL_NAME" user';
        $this->deprecated_message($alternative, true);
    }

    /**
     * @Given /^I add "(?P<user_username_string>(?:[^"]|\\")*)" user to "(?P<cohort_idnumber_string>(?:[^"]|\\")*)" cohort$/
     * @deprecated since Moodle 2.7 MDL-43584 - please do not use this step any more.
     */
    public function i_add_user_to_cohort($username, $cohortidnumber) {
        $alternative = 'I add "USER_FIRST_NAME USER_LAST_NAME (USER_EMAIL)" user to "'
                . $this->escape($cohortidnumber) . '" cohort members';
        $this->deprecated_message($alternative, true);
    }

    /**
     * @Given /^I add "(?P<username_string>(?:[^"]|\\")*)" user to "(?P<group_name_string>(?:[^"]|\\")*)" group$/
     * @deprecated since Moodle 2.7 MDL-43584 - please do not use this step any more.
     */
    public function i_add_user_to_group($username, $groupname) {
        $alternative = 'I add "USER_FULL_NAME" user to "' . $this->escape($groupname) . '" group members';
        $this->deprecated_message($alternative, true);
    }

    /**
     * @When /^I fill in "(?P<field_string>(?:[^"]|\\")*)" with "(?P<value_string>(?:[^"]|\\")*)"$/
     * @deprecated since Moodle 2.7 MDL-43738 - please do not use this step any more.
     */
    public function fill_field($field, $value) {
        $alternative = 'I set the field "' . $this->escape($field) . '" to "' . $this->escape($value) . '"';
        $this->deprecated_message($alternative, true);
    }

    /**
     * @When /^I select "(?P<option_string>(?:[^"]|\\")*)" from "(?P<select_string>(?:[^"]|\\")*)"$/
     * @deprecated since Moodle 2.7 MDL-43738 - please do not use this step any more.
     */
    public function select_option($option, $select) {
        $alternative = 'I set the field "' . $this->escape($select) . '" to "' . $this->escape($option) . '"';
        $this->deprecated_message($alternative, true);
    }

    /**
     * @When /^I select "(?P<radio_button_string>(?:[^"]|\\")*)" radio button$/
     * @deprecated since Moodle 2.7 MDL-43738 - please do not use this step any more.
     */
    public function select_radio($radio) {
        $alternative = 'I set the field "' . $this->escape($radio) . '" to "1"';
        $this->deprecated_message($alternative, true);
    }

    /**
     * @When /^I check "(?P<option_string>(?:[^"]|\\")*)"$/
     * @deprecated since Moodle 2.7 MDL-43738 - please do not use this step any more.
     */
    public function check_option($option) {
        $alternative = 'I set the field "' . $this->escape($option) . '" to "1"';
        $this->deprecated_message($alternative, true);
    }

    /**
     * @When /^I uncheck "(?P<option_string>(?:[^"]|\\")*)"$/
     * @deprecated since Moodle 2.7 MDL-43738 - please do not use this step any more.
     */
    public function uncheck_option($option) {
        $alternative = 'I set the field "' . $this->escape($option) . '" to ""';
        $this->deprecated_message($alternative, true);
    }

    /**
     * @Then /^the "(?P<field_string>(?:[^"]|\\")*)" field should match "(?P<value_string>(?:[^"]|\\")*)" value$/
     * @deprecated since Moodle 2.7 MDL-43738 - please do not use this step any more.
     */
    public function the_field_should_match_value($locator, $value) {
        $alternative = 'the field "' . $this->escape($locator) . '" matches value "' . $this->escape($value) . '"';
        $this->deprecated_message($alternative, true);
    }

    /**
     * @Then /^the "(?P<checkbox_string>(?:[^"]|\\")*)" checkbox should be checked$/
     * @deprecated since Moodle 2.7 MDL-43738 - please do not use this step any more.
     */
    public function assert_checkbox_checked($checkbox) {
        $alternative = 'the field "' . $this->escape($checkbox) . '" matches value "1"';
        $this->deprecated_message($alternative, true);
    }

    /**
     * @Then /^the "(?P<checkbox_string>(?:[^"]|\\")*)" checkbox should not be checked$/
     * @deprecated since Moodle 2.7 MDL-43738 - please do not use this step any more.
     */
    public function assert_checkbox_not_checked($checkbox) {
        $alternative = 'the field "' . $this->escape($checkbox) . '" matches value ""';
        $this->deprecated_message($alternative, true);
    }

    /**
     * @Given /^I fill the moodle form with:$/
     * @deprecated since Moodle 2.7 MDL-43738 - please do not use this step any more.
     */
    public function i_fill_the_moodle_form_with(TableNode $data) {
        $alternative = 'I set the following fields to these values:';
        $this->deprecated_message($alternative, true);
    }

    /**
     * @Then /^"(?P<element_string>(?:[^"]|\\")*)" "(?P<selector_string>[^"]*)" should exists$/
     * @deprecated since Moodle 2.7 MDL-43236 - please do not use this step any more.
     */
    public function should_exists($element, $selectortype) {
        $alternative = '"' . $this->escape($element) . '" "' . $this->escape($selectortype) . '" should exist';
        $this->deprecated_message($alternative, true);
    }

    /**
     * @Then /^"(?P<element_string>(?:[^"]|\\")*)" "(?P<selector_string>[^"]*)" should not exists$/
     * @deprecated since Moodle 2.7 MDL-43236 - please do not use this step any more.
     */
    public function should_not_exists($element, $selectortype) {
        $alternative = '"' . $this->escape($element) . '" "' . $this->escape($selectortype) . '" should not exist';
        $this->deprecated_message($alternative, true);
    }

    /**
     * @Given /^the following "(?P<element_string>(?:[^"]|\\")*)" exists:$/
     * @deprecated since Moodle 2.7 MDL-43236 - please do not use this step any more.
     */
    public function the_following_exists($elementname, TableNode $data) {
        $alternative = 'the following "' . $this->escape($elementname) . '" exist:';
        $this->deprecated_message($alternative, true);
    }

    /**
     * Sets the specified value to the field.
     *
     * @Given /^I set the field "(?P<field_string>(?:[^"]|\\")*)" to multiline$/
     * @throws ElementNotFoundException Thrown by behat_base::find
     * @param string $field
     * @param PyStringNode $value
     * @deprecated since Moodle 3.2 MDL-55406 - please do not use this step any more.
     */
    public function i_set_the_field_to_multiline($field, PyStringNode $value) {

        $alternative = 'I set the field "' . $this->escape($field) . '"  to multiline:';
        $this->deprecated_message($alternative);

        $this->execute('behat_forms::i_set_the_field_to_multiline', array($field, $value));
    }

    /**
     * Click on a given link in the moodle-actionmenu that is currently open.
     * @Given /^I follow "(?P<link_string>(?:[^"]|\\")*)" in the open menu$/
     * @param string $linkstring the text (or id, etc.) of the link to click.
     */
    public function i_follow_in_the_open_menu($linkstring) {
        $alternative = 'I choose "' . $this->escape($linkstring) . '" from the open action menu';
        $this->deprecated_message($alternative, true);
    }

    /**
     * Throws an exception if $CFG->behat_usedeprecated is not allowed.
     *
     * @throws Exception
     * @param string|array $alternatives Alternative/s to the requested step
     * @param bool $throwexception If set to true we always throw exception, irrespective of behat_usedeprecated setting.
     * @return void
     */
    protected function deprecated_message($alternatives, $throwexception = false) {
        global $CFG;

        // We do nothing if it is enabled.
        if (!empty($CFG->behat_usedeprecated) && !$throwexception) {
            return;
        }

        if (is_scalar($alternatives)) {
            $alternatives = array($alternatives);
        }

        // Show an appropriate message based on the throwexception flag.
        if ($throwexception) {
            $message = 'This step has been removed. Rather than using this step you can:';
        } else {
            $message = 'Deprecated step, rather than using this step you can:';
        }

        // Add all alternatives to the message.
        foreach ($alternatives as $alternative) {
            $message .= PHP_EOL . '- ' . $alternative;
        }

        if (!$throwexception) {
            $message .= PHP_EOL . '- Set $CFG->behat_usedeprecated in config.php to allow the use of deprecated steps
                    if you don\'t have any other option';
        }

        throw new Exception($message);
    }

}
