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
 * Behat grade related steps definitions.
 *
 * @package    core_grades
 * @category   test
 * @copyright  2014 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../../lib/behat/behat_base.php');

use Behat\Gherkin\Node\TableNode as TableNode;

class behat_grade extends behat_base {

    /**
     * Enters a grade via the gradebook for a specific grade item and user when viewing the 'Grader report' with editing mode turned on.
     *
     * @Given /^I give the grade "(?P<grade_number>(?:[^"]|\\")*)" to the user "(?P<username_string>(?:[^"]|\\")*)" for the grade item "(?P<grade_activity_string>(?:[^"]|\\")*)"$/
     * @param int $grade
     * @param string $userfullname the user's fullname as returned by fullname()
     * @param string $itemname
     */
    public function i_give_the_grade($grade, $userfullname, $itemname) {
        $gradelabel = $userfullname . ' ' . $itemname;
        $fieldstr = get_string('useractivitygrade', 'gradereport_grader', $gradelabel);

        $this->execute('behat_forms::i_set_the_field_to', array($this->escape($fieldstr), $grade));
    }

    /**
     * Changes the settings of a grade item or category or the course.
     *
     * Teacher must be either on the grade setup page or on the Grader report page with editing mode turned on.
     *
     * @Given /^I set the following settings for grade item "(?P<grade_item_string>(?:[^"]|\\")*)" of type "([^"]*)" on "([^"]*)" page:$/
     * @param string $gradeitem
     * @param string $type
     * @param string $page
     * @param TableNode $data
     */
    public function i_set_the_following_settings_for_grade_item(string $gradeitem, string $type, string $page, TableNode $data) {

        if ($this->running_javascript()) {
            $this->execute("behat_grades::i_click_on_grade_item_menu", [$gradeitem, $type, $page]);
        }

        if ($type == 'gradeitem') {
            $linktext = get_string('itemsedit', 'grades');
        } else if ($type == 'category') {
            $linktext = get_string('categoryedit', 'grades');
        } else {
            $linktext = get_string('categoryedit', 'grades');
        }
        $this->execute("behat_action_menu::i_choose_in_the_open_action_menu", $linktext);

        $savechanges = get_string('savechanges', 'grades');
        $this->execute("behat_forms::i_set_the_following_fields_to_these_values", $data);
        $this->execute('behat_forms::press_button', $this->escape($savechanges));
    }

    /**
     * Hids a grade item or category on gradebook setup or grader page.
     *
     * Teacher must be on the grade setup page.
     *
     * @Given /^I hide the grade item "(?P<grade_item_string>(?:[^"]|\\")*)" of type "([^"]*)" on "([^"]*)" page$/
     * @param string $gradeitem
     * @param string $type
     * @param string $page
     */
    public function i_hide_the_grade_item(string $gradeitem, string $type, string $page) {

        $linktext = get_string('hide');

        if ($this->running_javascript()) {
            $this->execute("behat_grades::i_click_on_grade_item_menu", [$gradeitem, $type, $page]);
        }

        $this->execute("behat_action_menu::i_choose_in_the_open_action_menu", $linktext);
    }

    /**
     * Duplicates a grade item or category.
     *
     * Teacher must be on the grade setup page.
     *
     * @Given /^I duplicate the grade item "(?P<grade_item_string>(?:[^"]|\\")*)"$/
     * @param string $gradeitem
     */
    public function i_duplicate_the_grade_item(string $gradeitem) {
        $linktext = get_string('duplicate');

        if ($this->running_javascript()) {
            $this->execute("behat_grades::i_click_on_grade_item_menu", [$gradeitem, 'gradeitem', 'setup']);
        }

        $this->execute("behat_action_menu::i_choose_in_the_open_action_menu", $linktext);
    }

    /**
     * Sets a calculated manual grade item. Needs a table with item name - idnumber relation.
     * The step requires you to be in the 'Gradebook setup' page.
     *
     * @Given /^I set "(?P<calculation_string>(?:[^"]|\\")*)" calculation for grade item "(?P<grade_item_string>(?:[^"]|\\")*)" with idnumbers:$/
     * @param string $calculation The calculation.
     * @param string $gradeitem The grade item name.
     * @param TableNode $TableNode The grade item name - idnumbers relation.
     */
    public function i_set_calculation_for_grade_item_with_idnumbers($calculation, $gradeitem, TableNode $data) {

        $edit = get_string('editcalculation', 'grades');

        if ($this->running_javascript()) {
            $this->execute("behat_grades::i_click_on_grade_item_menu", [$gradeitem, 'gradeitem', 'setup']);
        }
        $this->execute("behat_action_menu::i_choose_in_the_open_action_menu", $edit);

        $savechanges = get_string('savechanges', 'grades');

        // Mapping names to idnumbers.
        $datahash = $data->getRowsHash();
        foreach ($datahash as $gradeitem => $idnumber) {
            // This xpath looks for course, categories and items with the provided name.
            // Grrr, we can't equal in categoryitem and courseitem because there is a line jump...
            $inputxpath = "//input[@class='idnumber'][" .
                    "parent::li[@class='item'][text()='" . $gradeitem . "']" .
                    " or " .
                    "parent::li[@class='categoryitem' or @class='courseitem']" .
                    "/parent::ul/parent::li[starts-with(text(),'" . $gradeitem . "')]" .
                    "]";
            $this->execute('behat_forms::i_set_the_field_with_xpath_to', [$inputxpath, $idnumber]);
        }

        $this->execute('behat_forms::press_button', get_string('addidnumbers', 'grades'));
        $this->execute('behat_forms::i_set_the_field_to', [get_string('calculation', 'grades'), $calculation]);
        $this->execute('behat_forms::press_button', $savechanges);

    }

    /**
     * Sets a calculated manual grade category total. Needs a table with item name - idnumber relation.
     * The step requires you to be in the 'Gradebook setup' page.
     *
     * @Given /^I set "(?P<calculation_string>(?:[^"]|\\")*)" calculation for grade category "(?P<grade_item_string>(?:[^"]|\\")*)" with idnumbers:$/
     * @param string $calculation The calculation.
     * @param string $gradeitem The grade item name.
     * @param TableNode $data The grade item name - idnumbers relation.
     */
    public function i_set_calculation_for_grade_category_with_idnumbers(string $calculation, string $gradeitem, TableNode $data) {

        $edit = get_string('editcalculation', 'grades');

        if ($this->running_javascript()) {
            $this->execute("behat_grades::i_click_on_grade_item_menu", [$gradeitem, 'gradeitem', 'setup']);
        }
        $this->execute("behat_action_menu::i_choose_in_the_open_action_menu", $edit);

        $savechanges = get_string('savechanges', 'grades');

        // Mapping names to idnumbers.
        $datahash = $data->getRowsHash();
        foreach ($datahash as $gradeitem => $idnumber) {
            // This xpath looks for course, categories and items with the provided name.
            // Grrr, we can't equal in categoryitem and courseitem because there is a line jump...
            $inputxpath = "//input[@class='idnumber'][" .
                    "parent::li[@class='item'][text()='" . $gradeitem . "']" .
                    " | " .
                    "parent::li[@class='categoryitem' or @class='courseitem']" .
                    "/parent::ul/parent::li[starts-with(text(),'" . $gradeitem . "')]" .
                    "]";
            $this->execute('behat_forms::i_set_the_field_with_xpath_to', array($inputxpath, $idnumber));
        }

        $this->execute('behat_forms::press_button', get_string('addidnumbers', 'grades'));

        $this->execute('behat_forms::i_set_the_field_to', [get_string('calculation', 'grades'), $calculation]);
        $this->execute('behat_forms::press_button', $savechanges);
    }

    /**
     * Resets the weights for the grade category
     *
     * Teacher must be on the grade setup page.
     *
     * @Given /^I reset weights for grade category "(?P<grade_item_string>(?:[^"]|\\")*)"$/
     * @param string $gradeitem
     */
    public function i_reset_weights_for_grade_category(string $gradeitem) {

        if ($this->running_javascript()) {
            $this->execute("behat_grades::i_click_on_grade_item_menu", [$gradeitem, 'category', 'setup']);
        }
        $linktext = get_string('resetweightsshort', 'grades');
        $this->execute("behat_action_menu::i_choose_in_the_open_action_menu", [$this->escape($linktext), "link"]);
    }

    /**
     * Step allowing to test before-the-fix behaviour of the gradebook
     *
     * @Given /^gradebook calculations for the course "(?P<coursename_string>(?:[^"]|\\")*)" are frozen at version "(?P<version_string>(?:[^"]|\\")*)"$/
     * @param string $coursename
     * @param string $version
     */
    public function gradebook_calculations_for_the_course_are_frozen_at_version($coursename, $version) {
        global $DB;
        $courseid = $DB->get_field('course', 'id', array('shortname' => $coursename), MUST_EXIST);
        set_config('gradebook_calculations_freeze_' . $courseid, $version);
    }

    /**
     * Select the tab in the gradebook. We must be on one of the gradebook pages already.
     *
     * @deprecated since 4.0 - use behat_forms::i_set_the_field_to() instead.
     * @param string $gradepath examples: "View > User report", "Letters > View", "Scales"
     */
    protected function select_in_gradebook_tabs($gradepath) {
        debugging('The function select_in_gradebook_tabs() is deprecated, please use ' .
            'behat_forms::i_set_the_field_to() instead.', DEBUG_DEVELOPER);

        $gradepath = preg_split('/\s*>\s*/', trim($gradepath));
        if (count($gradepath) > 2) {
            throw new coding_exception('Grade path is too long (must have no more than two items separated with ">")');
        }

        $xpath = '//div[contains(@class,\'grade-navigation\')]';

        // If the first row of the grade-navigation tabs does not have $gradepath[0] as active tab, click on it.
        $link = '\'' . $this->escape($gradepath[0]) . '\'';
        $xpathrow1 = $xpath . '//ul[1]//*[contains(@class,\'active\') and contains(normalize-space(.), ' . $link . ')]';
        if (!$this->getSession()->getPage()->findAll('xpath', $xpathrow1)) {
            $this->find('xpath', $xpath . '//ul[1]/li/a[text()=' . $link . ']')->click();
            $this->wait_for_pending_js();
        }

        if (isset($gradepath[1])) {
            // If the second row of the grade-navigation tabs does not have $gradepath[1] as active tab, click on it.
            $link = '\'' . $this->escape($gradepath[1]) . '\'';
            $xpathrow2 = $xpath . '//ul[2]//*[contains(@class,\'active\') and contains(normalize-space(.), ' . $link . ')]';
            if (!$this->getSession()->getPage()->findAll('xpath', $xpathrow2)) {
                $this->find('xpath', $xpath . '//ul[2]/li/a[text()=' . $link . ']')->click();
                $this->wait_for_pending_js();
            }
        }
    }

    /**
     * Navigates to the course gradebook and selects the specified item from the general grade navigation selector.
     *
     * Examples:
     * - I navigate to "Setup > Gradebook setup" in the course gradebook
     * - I navigate to "Scales" in the course gradebook
     * - I navigate to "More > Grade letters" in the course gradebook
     *
     * @Given /^I navigate to "(?P<gradepath_string>(?:[^"]|\\")*)" in the course gradebook$/
     * @param string $gradepath The path string. If the path has two items (ex. "More > Grade letters"), the first item
     *                          ("More") will be used to identify an option group in the navigation selector, while the
     *                          second ("Grade letters") will be used to identify an option within that option group.
     *                          Otherwise, a single item in a path (ex. "Scales") will be used to identify an option in
     *                          the navigation selector regardless of the option group.
     */
    public function i_navigate_to_in_the_course_gradebook($gradepath) {
        // If we are not on one of the gradebook pages already, follow "Grades" link in the navigation drawer.
        $xpath = '//div[contains(@class,\'grade-navigation\')]';
        if (!$this->getSession()->getPage()->findAll('xpath', $xpath)) {
            $this->execute('behat_navigation::i_select_from_secondary_navigation', get_string('grades'));
        }

        $this->execute('behat_forms::i_set_the_field_to', [get_string('gradebooknavigationmenu', 'grades'), $gradepath]);
    }

    /**
     * Navigates to the imports page in the course gradebook and selects the specified import type from the grade
     * imports navigation selector.
     *
     * Examples:
     * - I navigate to "CSV file" import page in the course gradebook
     *
     * @Given /^I navigate to "(?P<importoption_string>(?:[^"]|\\")*)" import page in the course gradebook$/
     * @param string $gradeimportoption The name of an existing grade import option.
     */
    public function i_navigate_to_import_page_in_the_course_gradebook($gradeimportoption) {
        $this->i_navigate_to_in_the_course_gradebook("More > Import");
        $this->execute('behat_forms::i_set_the_field_to', [get_string('importas', 'grades'), $gradeimportoption]);
    }

    /**
     * Navigates to the exports page in the course gradebook and selects the specified export type from the grade
     * exports navigation selector.
     *
     * Examples:
     * - I navigate to "XML file" export page in the course gradebook
     *
     * @Given /^I navigate to "(?P<exportoption_string>(?:[^"]|\\")*)" export page in the course gradebook$/
     * @param string $gradeexportoption The name of an existing grade export option.
     */
    public function i_navigate_to_export_page_in_the_course_gradebook($gradeexportoption) {
        $this->i_navigate_to_in_the_course_gradebook("More > Export");
        $this->execute('behat_forms::i_set_the_field_to', [get_string('exportas', 'grades'), $gradeexportoption]);
    }

    /**
     * Select a given option from a navigation URL selector in the gradebook. We must be on one of the gradebook pages
     * already.
     *
     * @deprecated since 4.1 - use behat_forms::i_set_the_field_to() instead.
     * @param string $path The string path that is used to identify an item within the navigation selector. If the path
     *                     has two items (ex. "More > Grade letters"), the first item ("More") will be used to identify
     *                     an option group in the navigation selector, while the second ("Grade letters") will be used to
     *                     identify an option within that option group. Otherwise, a single item in a path (ex. "Scales")
     *                     will be used to identify an option in the navigation selector regardless of the option group.
     * @param string $formid The ID of the form element which contains the navigation URL selector element.
     */
    protected function select_in_gradebook_navigation_selector(string $path, string $formid) {
        debugging('The function select_in_gradebook_navigation_selector() is deprecated, please use ' .
            'behat_forms::i_set_the_field_to() instead.', DEBUG_DEVELOPER);

        // Split the path string by ">".
        $path = preg_split('/\s*>\s*/', trim($path));

        // Make sure that the path does not have more than two items separated with ">".
        if (count($path) > 2) {
            throw new coding_exception('The path is too long (must have no more than two items separated with ">")');
        }

        // Get the select element.
        $selectxpath = "//form[contains(@id,'{$formid}')]//select";
        $select = $this->find('xpath', $selectxpath);

        // Define the xpath to the option element depending on the provided path.
        // If two items are provided in the path, the first item will be considered as an identifier of an existing
        // option group in the select select element, while the second item will identify an existing option within
        // that option group.
        // If one item is provided in the path, this item will identify any existing option in the select element
        // regardless of the option group. Also, this is useful when option elements are not a part of an option group
        // which is possible.
        if (count($path) === 2) {
            $optionxpath = $selectxpath . '/optgroup[@label="' . $this->escape($path[0]) . '"]' .
                '/option[contains(.,"' . $this->escape($path[1]) . '")]';
        } else {
            $optionxpath = $selectxpath . '//option[contains(.,"' . $this->escape($path[0]) . '")]';
        }

        // Get the option element that we are looking to select.
        $option = $this->find('xpath', $optionxpath);

        // Select the given option in the select element.
        $field = behat_field_manager::get_field_instance('select', $select, $this->getSession());
        $field->set_value($this->escape($option->getValue()));

        if (!$this->running_javascript()) {
            $this->execute('behat_general::i_click_on_in_the', [get_string('go'), 'button',
                "#{$formid}", 'css_element']);
        }
    }

    /**
     * Confirm if a value is within the search widget within the gradebook.
     *
     * Examples:
     * - I confirm "User" in "user" search within the gradebook widget exists
     * - I confirm "Group" in "group" search within the gradebook widget exists
     * - I confirm "Grade item" in "grade" search within the gradebook widget exists
     *
     * @Given /^I confirm "(?P<needle>(?:[^"]|\\")*)" in "(?P<haystack>(?:[^"]|\\")*)" search within the gradebook widget exists$/
     * @param string $needle The value to search for.
     * @param string $haystack The type of the search widget.
     */
    public function i_confirm_in_search_within_the_gradebook_widget_exists($needle, $haystack) {
        $triggercssselector = ".search-widget[data-searchtype='{$haystack}']";

        // Make sure that the dropdown menu is visible.
        $node = $this->find("css_element", "{$triggercssselector} .dropdown-menu");
        if (!$node->isVisible()) {
            $this->execute("behat_general::i_click_on", [$triggercssselector, "css_element"]);
        }

        $this->execute("behat_general::wait_until_the_page_is_ready");
        $this->execute("behat_general::assert_element_contains_text",
            [$needle, "{$triggercssselector} .dropdown-menu", "css_element"]);
    }

    /**
     * Confirm if a value is not within the search widget within the gradebook.
     *
     * Examples:
     * - I confirm "User" in "user" search within the gradebook widget does not exist
     * - I confirm "Group" in "group" search within the gradebook widget does not exist
     * - I confirm "Grade item" in "grade" search within the gradebook widget does not exist
     *
     * @Given /^I confirm "(?P<needle>(?:[^"]|\\")*)" in "(?P<haystack>(?:[^"]|\\")*)" search within the gradebook widget does not exist$/
     * @param string $needle The value to search for.
     * @param string $haystack The type of the search widget.
     */
    public function i_confirm_in_search_within_the_gradebook_widget_does_not_exist($needle, $haystack) {
        $triggercssselector = ".search-widget[data-searchtype='{$haystack}']";

        // Make sure that the dropdown menu is visible.
        $node = $this->find("css_element", "{$triggercssselector} .dropdown-menu");
        if (!$node->isVisible()) {
            $this->execute("behat_general::i_click_on", [$triggercssselector, "css_element"]);
        }

        $this->execute("behat_general::wait_until_the_page_is_ready");
        $this->execute("behat_general::assert_element_not_contains_text",
            [$needle, "{$triggercssselector} .dropdown-menu", "css_element"]);
    }

    /**
     * Clicks on an option from the specified search widget in the current gradebook page.
     *
     * Examples:
     * - I click on "Student" in the "user" search widget
     * - I click on "Group" in the "group" search widget
     * - I click on "Grade item" in the "grade" search widget
     *
     * @Given /^I click on "(?P<needle>(?:[^"]|\\")*)" in the "(?P<haystack>(?:[^"]|\\")*)" search widget$/
     * @param string $needle The value to search for.
     * @param string $haystack The type of the search widget.
     */
    public function i_click_on_in_search_widget(string $needle, string $haystack) {
        $this->execute("behat_general::wait_until_the_page_is_ready");

        $triggercssselector = ".search-widget[data-searchtype='{$haystack}']";

        $this->execute("behat_general::i_click_on", [$triggercssselector, "css_element"]);
        $this->execute("behat_general::wait_until_the_page_is_ready");
        $this->execute('behat_general::i_click_on_in_the', [
            "//li[@role='option'][contains(., '" . $needle . "')] | //a[contains(., '" . $needle . "')]",
            "xpath_element",
            "{$triggercssselector} .dropdown-menu",
            "css_element"
        ]);
    }
}
