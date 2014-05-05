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
    Behat\Behat\Context\Step\Given as Given,
    Behat\Behat\Context\Step\Then as Then,
    Behat\Gherkin\Node\TableNode as TableNode;

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
     * Click on the specified element inside a table row containing the specified text.
     *
     * @deprecated since Moodle 2.7 MDL-42627
     * @todo MDL-42862 This will be deleted in Moodle 2.9
     * @see behat_general::i_click_on_in_the()
     *
     * @Given /^I click on "(?P<element_string>(?:[^"]|\\")*)" "(?P<selector_string>(?:[^"]|\\")*)" in the "(?P<row_text_string>(?:[^"]|\\")*)" table row$/
     * @throws ElementNotFoundException
     * @param string $element Element we look for
     * @param string $selectortype The type of what we look for
     * @param string $tablerowtext The table row text
     */
    public function i_click_on_in_the_table_row($element, $selectortype, $tablerowtext) {

        // Throw an exception if deprecated methods are not allowed otherwise allow it's execution.
        $alternative = 'I click on "' . $this->escape($element) . '" "' . $this->escape($selectortype) .
            '" in the "' . $this->escape($tablerowtext) . '" "table_row"';
        $this->deprecated_message($alternative);

        return new Given($alternative);
    }

    /**
     * Goes to notification page ensuring site admin navigation is loaded.
     *
     * Step [I expand "Site administration" node] will ensure that administration menu
     * is opened in both javascript and non-javascript modes.
     *
     * @deprecated since 2.7
     * @todo MDL-42862 This will be deleted in Moodle 2.9
     *
     * @Given /^I go to notifications page$/
     * @return Given[]
     */
    public function i_go_to_notifications_page() {
        $alternative = array(
            'I expand "' . get_string('administrationsite') . '" node',
            'I click on "' . get_string('notifications') . '" "link" in the "'.get_string('administration').'" "block"'
        );
        $this->deprecated_message($alternative);
        return array(
            new Given($alternative[0]),
            new Given($alternative[1]),
        );
    }

    /**
     * Adds the specified file from the 'Recent files' repository to the specified filepicker of the current page.
     *
     * @deprecated since 2.7
     * @todo MDL-42862 This will be deleted in Moodle 2.9
     * @see behat_filepicker::i_add_file_from_repository_to_filemanager()
     *
     * @When /^I add "(?P<filename_string>(?:[^"]|\\")*)" file from recent files to "(?P<filepicker_field_string>(?:[^"]|\\")*)" filepicker$/
     * @param string $filename
     * @param string $filepickerelement
     */
    public function i_add_file_from_recent_files_to_filepicker($filename, $filepickerelement) {
        $reponame = get_string('pluginname', 'repository_recent');
        $alternative = 'I add "' . $this->escape($filename) . '" file from "' .
                $reponame . '" to "' . $this->escape($filepickerelement) . '" filemanager';
        $this->deprecated_message($alternative);
        return array(
            new Given($alternative)
        );
    }

    /**
     * Uploads a file to the specified filemanager leaving other fields in upload form default. The paths should be relative to moodle codebase.
     *
     * @deprecated since 2.7
     * @todo MDL-42862 This will be deleted in Moodle 2.9
     * @see behat_repository_upload::i_upload_file_to_filemanager()
     *
     * @When /^I upload "(?P<filepath_string>(?:[^"]|\\")*)" file to "(?P<filepicker_field_string>(?:[^"]|\\")*)" filepicker$/
     * @throws ExpectationException Thrown by behat_base::find
     * @param string $filepath
     * @param string $filepickerelement
     */
    public function i_upload_file_to_filepicker($filepath, $filepickerelement) {
        $alternative = 'I upload "' . $this->escape($filepath) . '" file to "' .
                $this->escape($filepickerelement) . '" filemanager';
        $this->deprecated_message($alternative);
        return array(
            new Given($alternative)
        );
    }

    /**
     * Creates a folder with specified name in the current folder and in the specified filepicker field.
     *
     * @deprecated since 2.7
     * @todo MDL-42862 This will be deleted in Moodle 2.9
     * @see behat_filepicker::i_create_folder_in_filemanager()
     *
     * @Given /^I create "(?P<foldername_string>(?:[^"]|\\")*)" folder in "(?P<filepicker_field_string>(?:[^"]|\\")*)" filepicker$/
     * @throws ExpectationException Thrown by behat_base::find
     * @param string $foldername
     * @param string $filepickerelement
     */
    public function i_create_folder_in_filepicker($foldername, $filepickerelement) {
        $alternative = 'I create "' . $this->escape($foldername) .
                '" folder in "' . $this->escape($filepickerelement) . '" filemanager';
        $this->deprecated_message($alternative);
        return array(new Given($alternative));
    }

    /**
     * Opens the contents of a filepicker folder. It looks for the folder in the current folder and in the path bar.
     *
     * @deprecated since 2.7
     * @todo MDL-42862 This will be deleted in Moodle 2.9
     * @see behat_filepicker::i_open_folder_from_filemanager()
     *
     * @Given /^I open "(?P<foldername_string>(?:[^"]|\\")*)" folder from "(?P<filepicker_field_string>(?:[^"]|\\")*)" filepicker$/
     * @throws ExpectationException Thrown by behat_base::find
     * @param string $foldername
     * @param string $filepickerelement
     */
    public function i_open_folder_from_filepicker($foldername, $filepickerelement) {
        $alternative = 'I open "' . $this->escape($foldername) . '" folder from "' .
                $this->escape($filepickerelement) . '" filemanager';
        $this->deprecated_message($alternative);
        return array(new Given($alternative));
    }

    /**
     * Unzips the specified file from the specified filepicker field. The zip file has to be visible in the current folder.
     *
     * @deprecated since 2.7
     * @todo MDL-42862 This will be deleted in Moodle 2.9
     * @see behat_filepicker::i_unzip_file_from_filemanager()
     *
     * @Given /^I unzip "(?P<filename_string>(?:[^"]|\\")*)" file from "(?P<filepicker_field_string>(?:[^"]|\\")*)" filepicker$/
     * @throws ExpectationException Thrown by behat_base::find
     * @param string $filename
     * @param string $filepickerelement
     */
    public function i_unzip_file_from_filepicker($filename, $filepickerelement) {
        $alternative = 'I unzip "' . $this->escape($filename) . '" file from "' .
                $this->escape($filepickerelement) . '" filemanager';
        $this->deprecated_message($alternative);
        return array(new Given($alternative));
    }

    /**
     * Zips the specified folder from the specified filepicker field. The folder has to be in the current folder.
     *
     * @deprecated since 2.7
     * @todo MDL-42862 This will be deleted in Moodle 2.9
     * @see behat_filepicker::i_zip_folder_from_filemanager()
     *
     * @Given /^I zip "(?P<filename_string>(?:[^"]|\\")*)" folder from "(?P<filepicker_field_string>(?:[^"]|\\")*)" filepicker$/
     * @throws ExpectationException Thrown by behat_base::find
     * @param string $foldername
     * @param string $filepickerelement
     */
    public function i_zip_folder_from_filepicker($foldername, $filepickerelement) {
        $alternative = 'I zip "' . $this->escape($foldername) . '" folder from "' .
                $this->escape($filepickerelement) . '" filemanager';
        $this->deprecated_message($alternative);
        return array(new Given($alternative));
    }

    /**
     * Deletes the specified file or folder from the specified filepicker field.
     *
     * @deprecated since 2.7
     * @todo MDL-42862 This will be deleted in Moodle 2.9
     * @see behat_filepicker::i_delete_file_from_filemanager()
     *
     * @Given /^I delete "(?P<file_or_folder_name_string>(?:[^"]|\\")*)" from "(?P<filepicker_field_string>(?:[^"]|\\")*)" filepicker$/
     * @throws ExpectationException Thrown by behat_base::find
     * @param string $name
     * @param string $filepickerelement
     */
    public function i_delete_file_from_filepicker($name, $filepickerelement) {
        $alternative = 'I delete "' . $this->escape($name) . '" from "' .
                $this->escape($filepickerelement) . '" filemanager';
        $this->deprecated_message($alternative);
        return array(new Given($alternative));
    }

    /**
     * Sends a message to the specified user from the logged user.
     *
     * @deprecated since 2.7
     * @todo MDL-42862 This will be deleted in Moodle 2.9
     * @see behat_message::i_send_message_to_user()
     *
     * @Given /^I send "(?P<message_contents_string>(?:[^"]|\\")*)" message to "(?P<username_string>(?:[^"]|\\")*)"$/
     * @throws ElementNotFoundException
     * @param string $messagecontent
     * @param string $tousername
     */
    public function i_send_message_to_user($messagecontent, $tousername) {

        global $DB;

        // Runs by CLI, same PHP process that created the user.
        $touser = $DB->get_record('user', array('username' => $tousername));
        if (!$touser) {
            throw new ElementNotFoundException($this->getSession(), '"' . $tousername . '" ');
        }
        $tofullname = fullname($touser);

        $alternative = 'I send "' . $this->escape($messagecontent) . '" message to "' . $tofullname . '" user';
        $this->deprecated_message($alternative);
        return new Given($alternative);
    }

    /**
     * Adds the user to the specified cohort.
     *
     * @deprecated since 2.7
     * @todo MDL-42862 This will be deleted in Moodle 2.9
     * @see behat_cohort::i_add_user_to_cohort_members()
     *
     * @Given /^I add "(?P<user_username_string>(?:[^"]|\\")*)" user to "(?P<cohort_idnumber_string>(?:[^"]|\\")*)" cohort$/
     * @param string $username
     * @param string $cohortidnumber
     */
    public function i_add_user_to_cohort($username, $cohortidnumber) {
        global $DB;

        // The user was created by the data generator, executed by the same PHP process that is
        // running this step, not by any Selenium action.
        $user = $DB->get_record('user', array('username' => $username));
        $userlocator = $user->firstname . ' ' . $user->lastname . ' (' . $user->email . ')';

        $alternative = 'I add "' . $this->escape($userlocator) .
            '" user to "' . $this->escape($cohortidnumber) . '" cohort members';
        $this->deprecated_message($alternative);

        return new Given($alternative);
    }

    /**
     * Add the specified user to the group. You should be in the groups page when running this step.
     *
     * @deprecated since 2.7
     * @todo MDL-42862 This will be deleted in Moodle 2.9
     * @see behat_groups::i_add_user_to_group_members()
     *
     * @Given /^I add "(?P<username_string>(?:[^"]|\\")*)" user to "(?P<group_name_string>(?:[^"]|\\")*)" group$/
     * @param string $username
     * @param string $groupname
     */
    public function i_add_user_to_group($username, $groupname) {
        global $DB;

        $user = $DB->get_record('user', array('username' => $username));
        $userfullname = fullname($user);

        $alternative = 'I add "' . $this->escape($userfullname) .
            '" user to "' . $this->escape($groupname) . '" group members';
        $this->deprecated_message($alternative);

        return new Given($alternative);
    }

    /**
     * Fills in form text field with specified id|name|label|value. It works with text-based fields.
     *
     * @deprecated since 2.7
     * @todo MDL-42862 This will be deleted in Moodle 2.9
     * @see behat_forms::i_set_the_field_to()
     *
     * @When /^I fill in "(?P<field_string>(?:[^"]|\\")*)" with "(?P<value_string>(?:[^"]|\\")*)"$/
     * @throws ElementNotFoundException Thrown by behat_base::find
     * @param string $field
     * @param string $value
     */
    public function fill_field($field, $value) {
        $alternative = 'I set the field "' . $this->escape($field) . '" to "' . $this->escape($value) . '"';
        $this->deprecated_message($alternative);

        return new Given($alternative);
    }

    /**
     * Selects option in select field with specified id|name|label|value.
     *
     * @deprecated since 2.7
     * @todo MDL-42862 This will be deleted in Moodle 2.9
     * @see behat_forms::i_set_the_field_to()
     *
     * @When /^I select "(?P<option_string>(?:[^"]|\\")*)" from "(?P<select_string>(?:[^"]|\\")*)"$/
     * @throws ElementNotFoundException Thrown by behat_base::find
     * @param string $option
     * @param string $select
     */
    public function select_option($option, $select) {
        $alternative = 'I set the field "' . $this->escape($select) . '" to "' . $this->escape($option) . '"';
        $this->deprecated_message($alternative);

        return new Given($alternative);
    }

    /**
     * Selects the specified id|name|label from the specified radio button.
     *
     * @deprecated since 2.7
     * @todo MDL-42862 This will be deleted in Moodle 2.9
     * @see behat_forms::i_set_the_field_to()
     *
     * @When /^I select "(?P<radio_button_string>(?:[^"]|\\")*)" radio button$/
     * @throws ElementNotFoundException Thrown by behat_base::find
     * @param string $radio The radio button id, name or label value
     */
    public function select_radio($radio) {
        $alternative = 'I set the field "' . $this->escape($radio) . '" to "1"';
        $this->deprecated_message($alternative);

        return new Given($alternative);
    }

    /**
     * Checks checkbox with specified id|name|label|value.
     *
     * @deprecated since 2.7
     * @todo MDL-42862 This will be deleted in Moodle 2.9
     * @see behat_forms::i_set_the_field_to()
     *
     * @When /^I check "(?P<option_string>(?:[^"]|\\")*)"$/
     * @throws ElementNotFoundException Thrown by behat_base::find
     * @param string $option
     */
    public function check_option($option) {
        $alternative = 'I set the field "' . $this->escape($option) . '" to "1"';
        $this->deprecated_message($alternative);

        return new Given($alternative);
    }

    /**
     * Unchecks checkbox with specified id|name|label|value.
     *
     * @deprecated since 2.7
     * @todo MDL-42862 This will be deleted in Moodle 2.9
     * @see behat_forms::i_set_the_field_to()
     *
     * @When /^I uncheck "(?P<option_string>(?:[^"]|\\")*)"$/
     * @throws ElementNotFoundException Thrown by behat_base::find
     * @param string $option
     */
    public function uncheck_option($option) {
        $alternative = 'I set the field "' . $this->escape($option) . '" to ""';
        $this->deprecated_message($alternative);

        return new Given($alternative);
    }

    /**
     * Checks that the field matches the specified value. When using multi-select fields use commas to separate selected options.
     *
     * @deprecated since 2.7
     * @todo MDL-42862 This will be deleted in Moodle 2.9
     * @see behat_forms::the_field_matches_value()
     *
     * @Then /^the "(?P<field_string>(?:[^"]|\\")*)" field should match "(?P<value_string>(?:[^"]|\\")*)" value$/
     * @throws ExpectationException
     * @throws ElementNotFoundException Thrown by behat_base::find
     * @param string $locator
     * @param string $value
     */
    public function the_field_should_match_value($locator, $value) {
        $alternative = 'the field "' . $this->escape($locator) . '" matches value "' . $this->escape($value) . '"';
        $this->deprecated_message($alternative);

        return new Then($alternative);
    }

    /**
     * Checks, that checkbox with specified in|name|label|value is checked.
     *
     * @deprecated since 2.7
     * @todo MDL-42862 This will be deleted in Moodle 2.9
     * @see behat_forms::the_field_matches_value()
     *
     * @Then /^the "(?P<checkbox_string>(?:[^"]|\\")*)" checkbox should be checked$/
     * @param string $checkbox
     */
    public function assert_checkbox_checked($checkbox) {
        $alternative = 'the field "' . $this->escape($checkbox) . '" matches value "1"';
        $this->deprecated_message($alternative);

        return new Then($alternative);
    }

    /**
     * Checks, that checkbox with specified in|name|label|value is unchecked.
     *
     * @deprecated since 2.7
     * @todo MDL-42862 This will be deleted in Moodle 2.9
     * @see behat_forms::the_field_matches_value()
     *
     * @Then /^the "(?P<checkbox_string>(?:[^"]|\\")*)" checkbox should not be checked$/
     * @param string $checkbox
     */
    public function assert_checkbox_not_checked($checkbox) {
        $alternative = 'the field "' . $this->escape($checkbox) . '" matches value ""';
        $this->deprecated_message($alternative);

        return new Then($alternative);
    }

    /**
     * Fills a moodle form with field/value data.
     *
     * @deprecated since 2.7
     * @todo MDL-42862 This will be deleted in Moodle 2.9
     * @see behat_forms::i_set_the_following_fields_to_these_values()
     *
     * @Given /^I fill the moodle form with:$/
     * @throws ElementNotFoundException Thrown by behat_base::find
     * @param TableNode $data
     */
    public function i_fill_the_moodle_form_with(TableNode $data) {
        $alternative = 'I set the following fields to these values:';
        $this->deprecated_message($alternative);

        return new Given($alternative, $data);
    }

    /**
     * Checks the provided element and selector type exists in the current page.
     *
     * This step is for advanced users, use it if you don't find anything else suitable for what you need.
     *
     * @Then /^"(?P<element_string>(?:[^"]|\\")*)" "(?P<selector_string>[^"]*)" should exists$/
     * @throws ElementNotFoundException Thrown by behat_base::find
     * @param string $element The locator of the specified selector
     * @param string $selectortype The selector type
     */
    public function should_exists($element, $selectortype) {
        $alternative = '"' . $this->escape($element) . '" "' . $this->escape($selectortype) . '" should exist';
        $this->deprecated_message($alternative);
        return new Then($alternative);
    }

    /**
     * Checks that the provided element and selector type not exists in the current page.
     *
     * This step is for advanced users, use it if you don't find anything else suitable for what you need.
     *
     * @Then /^"(?P<element_string>(?:[^"]|\\")*)" "(?P<selector_string>[^"]*)" should not exists$/
     * @throws ExpectationException
     * @param string $element The locator of the specified selector
     * @param string $selectortype The selector type
     */
    public function should_not_exists($element, $selectortype) {
        $alternative = '"' . $this->escape($element) . '" "' . $this->escape($selectortype) . '" should not exist';
        $this->deprecated_message($alternative);
        return new Then($alternative);
    }

    /**
     * Creates the specified element. More info about available elements in http://docs.moodle.org/dev/Acceptance_testing#Fixtures.
     *
     * @Given /^the following "(?P<element_string>(?:[^"]|\\")*)" exists:$/
     *
     * @throws Exception
     * @throws PendingException
     * @param string    $elementname The name of the entity to add
     * @param TableNode $data
     */
    public function the_following_exists($elementname, TableNode $data) {
        $alternative = 'the following "' . $this->escape($elementname) . '" exist:';
        $this->deprecated_message($alternative);
        return new Given($alternative, $data);
    }

    /**
     * Throws an exception if $CFG->behat_usedeprecated is not allowed.
     *
     * @throws Exception
     * @param string|array $alternatives Alternative/s to the requested step
     * @return void
     */
    protected function deprecated_message($alternatives) {
        global $CFG;

        // We do nothing if it is enabled.
        if (!empty($CFG->behat_usedeprecated)) {
            return;
        }

        if (is_scalar($alternatives)) {
            $alternatives = array($alternatives);
        }

        $message = 'Deprecated step, rather than using this step you can:';
        foreach ($alternatives as $alternative) {
            $message .= PHP_EOL . '- ' . $alternative;
        }
        $message .= PHP_EOL . '- Set $CFG->behat_usedeprecated in config.php to allow the use of deprecated steps if you don\'t have any other option';
        throw new Exception($message);
    }

}
