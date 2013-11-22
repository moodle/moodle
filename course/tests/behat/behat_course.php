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
 * Behat course-related steps definitions.
 *
 * @package    core_course
 * @category   test
 * @copyright  2012 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../../lib/behat/behat_base.php');

use Behat\Behat\Context\Step\Given as Given,
    Behat\Gherkin\Node\TableNode as TableNode,
    Behat\Mink\Exception\ExpectationException as ExpectationException,
    Behat\Mink\Exception\DriverException as DriverException,
    Behat\Mink\Exception\ElementNotFoundException as ElementNotFoundException;

/**
 * Course-related steps definitions.
 *
 * @package    core_course
 * @category   test
 * @copyright  2012 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_course extends behat_base {

    /**
     * Turns editing mode on.
     * @Given /^I turn editing mode on$/
     */
    public function i_turn_editing_mode_on() {
        return new Given('I press "' . get_string('turneditingon') . '"');
    }

    /**
     * Turns editing mode off.
     * @Given /^I turn editing mode off$/
     */
    public function i_turn_editing_mode_off() {
        return new Given('I press "' . get_string('turneditingoff') . '"');
    }

    /**
     * Creates a new course with the provided table data matching course settings names with the desired values.
     *
     * @Given /^I create a course with:$/
     * @param TableNode $table The course data
     */
    public function i_create_a_course_with(TableNode $table) {
        return array(
            new Given('I go to the courses management page'),
            new Given('I press "' . get_string('addnewcourse') . '"'),
            new Given('I fill the moodle form with:', $table),
            new Given('I press "' . get_string('savechanges') . '"')
        );
    }

    /**
     * Goes to the system courses/categories management page.
     *
     * @Given /^I go to the courses management page$/
     */
    public function i_go_to_the_courses_management_page() {

        return array(
            new Given('I am on homepage'),
            new Given('I expand "' . get_string('administrationsite') . '" node'),
            new Given('I expand "' . get_string('courses', 'admin') . '" node'),
            new Given('I follow "' . get_string('coursemgmt', 'admin') . '"'),
        );
    }

    /**
     * Adds the selected activity/resource filling the form data with the specified field/value pairs. Sections 0 and 1 are also allowed on frontpage.
     *
     * @When /^I add a "(?P<activity_or_resource_name_string>(?:[^"]|\\")*)" to section "(?P<section_number>\d+)" and I fill the form with:$/
     * @param string $activity The activity name
     * @param int $section The section number
     * @param TableNode $data The activity field/value data
     */
    public function i_add_to_section_and_i_fill_the_form_with($activity, $section, TableNode $data) {

        return array(
            new Given('I add a "' . $this->escape($activity) . '" to section "' . $this->escape($section) . '"'),
            new Given('I fill the moodle form with:', $data),
            new Given('I press "' . get_string('savechangesandreturntocourse') . '"')
        );
    }

    /**
     * Opens the activity chooser and opens the activity/resource form page. Sections 0 and 1 are also allowed on frontpage.
     *
     * @Given /^I add a "(?P<activity_or_resource_name_string>(?:[^"]|\\")*)" to section "(?P<section_number>\d+)"$/
     * @throws ElementNotFoundException Thrown by behat_base::find
     * @param string $activity
     * @param int $section
     */
    public function i_add_to_section($activity, $section) {

        if ($this->getSession()->getPage()->find('css', 'body#page-site-index') && (int)$section <= 1) {
            // We are on the frontpage.
            if ($section) {
                // Section 1 represents the contents on the frontpage.
                $sectionxpath = "//body[@id='page-site-index']/descendant::div[contains(concat(' ',normalize-space(@class),' '),' sitetopic ')]";
            } else {
                // Section 0 represents "Site main menu" block.
                $sectionxpath = "//div[contains(concat(' ',normalize-space(@class),' '),' block_site_main_menu ')]";
            }
        } else {
            // We are inside the course.
            $sectionxpath = "//li[@id='section-" . $section . "']";
        }

        $activityliteral = $this->getSession()->getSelectorsHandler()->xpathLiteral(ucfirst($activity));

        if ($this->running_javascript()) {

            // Clicks add activity or resource section link.
            $sectionxpath = $sectionxpath . "/descendant::div[@class='section-modchooser']/span/a";
            $sectionnode = $this->find('xpath', $sectionxpath);
            $sectionnode->click();

            // Clicks the selected activity if it exists.
            $activityxpath = "//div[@id='chooseform']/descendant::label" .
                "/descendant::span[contains(concat(' ', normalize-space(@class), ' '), ' typename ')]" .
                "[contains(., $activityliteral)]" .
                "/parent::label/child::input";
            $activitynode = $this->find('xpath', $activityxpath);
            $activitynode->doubleClick();

        } else {
            // Without Javascript.

            // Selecting the option from the select box which contains the option.
            $selectxpath = $sectionxpath . "/descendant::div[contains(concat(' ', normalize-space(@class), ' '), ' section_add_menus ')]" .
                "/descendant::select[contains(., $activityliteral)]";
            $selectnode = $this->find('xpath', $selectxpath);
            $selectnode->selectOption($activity);

            // Go button.
            $gobuttonxpath = $selectxpath . "/ancestor::form/descendant::input[@type='submit']";
            $gobutton = $this->find('xpath', $gobuttonxpath);
            $gobutton->click();
        }

    }

    /**
     * Turns course section highlighting on.
     *
     * @Given /^I turn section "(?P<section_number>\d+)" highlighting on$/
     * @param int $sectionnumber The section number
     */
    public function i_turn_section_highlighting_on($sectionnumber) {

        // Ensures the section exists.
        $xpath = $this->section_exists($sectionnumber);

        return array(
            new Given('I click on "' . get_string('markthistopic') . '" "link" in the "' . $this->escape($xpath) . '" "xpath_element"'),
            new Given('I wait "2" seconds')
        );
    }

    /**
     * Turns course section highlighting off.
     *
     * @Given /^I turn section "(?P<section_number>\d+)" highlighting off$/
     * @param int $sectionnumber The section number
     */
    public function i_turn_section_highlighting_off($sectionnumber) {

        // Ensures the section exists.
        $xpath = $this->section_exists($sectionnumber);

        return array(
            new Given('I click on "' . get_string('markedthistopic') . '" "link" in the "' . $this->escape($xpath) . '" "xpath_element"'),
            new Given('I wait "2" seconds')
        );
    }

    /**
     * Shows the specified hidden section. You need to be in the course page and on editing mode.
     *
     * @Given /^I show section "(?P<section_number>\d+)"$/
     * @param int $sectionnumber
     */
    public function i_show_section($sectionnumber) {
        $showlink = $this->show_section_icon_exists($sectionnumber);
        $showlink->click();

        // It requires time.
        if ($this->running_javascript()) {
            $this->getSession()->wait(5000, false);
        }
    }

    /**
     * Hides the specified visible section. You need to be in the course page and on editing mode.
     *
     * @Given /^I hide section "(?P<section_number>\d+)"$/
     * @param int $sectionnumber
     */
    public function i_hide_section($sectionnumber) {
        $hidelink = $this->hide_section_icon_exists($sectionnumber);
        $hidelink->click();

        // It requires time.
        if ($this->running_javascript()) {
            $this->getSession()->wait(5000, false);
        }
    }

    /**
     * Go to editing section page for specified section number. You need to be in the course page and on editing mode.
     *
     * @Given /^I edit the section "(?P<section_number>\d+)"$/
     * @param int $sectionnumber
     */
    public function i_edit_the_section($sectionnumber) {
        return new Given('I click on "' . get_string('editsummary') . '" "link" in the "#section-' . $sectionnumber . '" "css_element"');
    }

    /**
     * Edit specified section and fill the form data with the specified field/value pairs.
     *
     * @When /^I edit the section "(?P<section_number>\d+)" and I fill the form with:$/
     * @param int $sectionnumber The section number
     * @param TableNode $data The activity field/value data
     * @return Given[]
     */
    public function i_edit_the_section_and_i_fill_the_form_with($sectionnumber, TableNode $data) {

        return array(
            new Given('I edit the section "' . $sectionnumber . '"'),
            new Given('I fill the moodle form with:', $data),
            new Given('I press "' . get_string('savechanges') . '"')
        );
    }

    /**
     * Checks if the specified course section hightlighting is turned on. You need to be in the course page on editing mode.
     *
     * @Then /^section "(?P<section_number>\d+)" should be highlighted$/
     * @throws ExpectationException
     * @param int $sectionnumber The section number
     */
    public function section_should_be_highlighted($sectionnumber) {

        // Ensures the section exists.
        $xpath = $this->section_exists($sectionnumber);

        // The important checking, we can not check the img.
        $xpath = $xpath . "/descendant::img[@alt='" . get_string('markedthistopic') . "'][contains(@src, 'marked')]";
        $exception = new ExpectationException('The "' . $sectionnumber . '" section is not highlighted', $this->getSession());
        $this->find('xpath', $xpath, $exception);
    }

    /**
     * Checks if the specified course section highlighting is turned off. You need to be in the course page on editing mode.
     *
     * @Then /^section "(?P<section_number>\d+)" should not be highlighted$/
     * @throws ExpectationException
     * @param int $sectionnumber The section number
     */
    public function section_should_not_be_highlighted($sectionnumber) {

        // We only catch ExpectationException, ElementNotFoundException should be thrown if the specified section does not exist.
        try {
            $this->section_should_be_highlighted($sectionnumber);
        } catch (ExpectationException $e) {
            // ExpectedException means that it is not highlighted.
            return;
        }

        throw new ExpectationException('The "' . $sectionnumber . '" section is highlighted', $this->getSession());
    }

    /**
     * Checks that the specified section is visible. You need to be in the course page. It can be used being logged as a student and as a teacher on editing mode.
     *
     * @Then /^section "(?P<section_number>\d+)" should be hidden$/
     * @throws ExpectationException
     * @throws ElementNotFoundException Thrown by behat_base::find
     * @param int $sectionnumber
     */
    public function section_should_be_hidden($sectionnumber) {

        $sectionxpath = $this->section_exists($sectionnumber);

        // Section should be hidden.
        $exception = new ExpectationException('The section is not hidden', $this->getSession());
        $this->find('xpath', $sectionxpath . "[contains(concat(' ', normalize-space(@class), ' '), ' hidden ')]", $exception);

        // The checking are different depending on user permissions.
        if ($this->is_course_editor()) {

            // The section must be hidden.
            $this->show_section_icon_exists($sectionnumber);

            // If there are activities they should be hidden and the visibility icon should not be available.
            if ($activities = $this->get_section_activities($sectionxpath)) {

                $dimmedexception = new ExpectationException('There are activities that are not dimmed', $this->getSession());
                $visibilityexception = new ExpectationException('There are activities which visibility icons are clickable', $this->getSession());
                foreach ($activities as $activity) {

                    // Dimmed.
                    $this->find('xpath', "//div[contains(concat(' ', normalize-space(@class), ' '), ' activityinstance ')]" .
                        "/a[contains(concat(' ', normalize-space(@class), ' '), ' dimmed ')]", $dimmedexception, $activity);

                    // Non-JS browsers can not click on img elements.
                    if ($this->running_javascript()) {
                        // To check that the visibility is not clickable we check the funcionality rather than the applied style.
                        $visibilityiconnode = $this->find('css', 'a.editing_show img', false, $activity);
                        $visibilityiconnode->click();
                    }

                    // We ensure that we still see the show icon.
                    $visibilityiconnode = $this->find('css', 'a.editing_show img', $visibilityexception, $activity);
                }
            }

        } else {
            // There shouldn't be activities.
            if ($this->get_section_activities($sectionxpath)) {
                throw new ExpectationException('There are activities in the section and they should be hidden', $this->getSession());
            }
        }
    }

    /**
     * Checks that the specified section is visible. You need to be in the course page. It can be used being logged as a student and as a teacher on editing mode.
     *
     * @Then /^section "(?P<section_number>\d+)" should be visible$/
     * @throws ExpectationException
     * @param int $sectionnumber
     */
    public function section_should_be_visible($sectionnumber) {

        $sectionxpath = $this->section_exists($sectionnumber);

        // Section should not be hidden.
        $xpath = $sectionxpath . "[not(contains(concat(' ', normalize-space(@class), ' '), ' hidden '))]";
        if (!$this->getSession()->getPage()->find('xpath', $xpath)) {
            throw new ExpectationException('The section is hidden', $this->getSession());
        }

        // Hide section button should be visible.
        if ($this->is_course_editor()) {
            $this->hide_section_icon_exists($sectionnumber);
        }
    }

    /**
     * Moves up the specified section, this step only works with Javascript disabled. Editing mode should be on.
     *
     * @Given /^I move up section "(?P<section_number>\d+)"$/
     * @throws DriverException Step not available when Javascript is enabled
     * @param int $sectionnumber
     */
    public function i_move_up_section($sectionnumber) {

        if ($this->running_javascript()) {
            throw new DriverException('Move a section up step is not available with Javascript enabled');
        }

        // Ensures the section exists.
        $sectionxpath = $this->section_exists($sectionnumber);

        // Follows the link
        $moveuplink = $this->get_node_in_container('link', get_string('moveup'), 'xpath_element', $sectionxpath);
        $moveuplink->click();
    }

    /**
     * Moves down the specified section, this step only works with Javascript disabled. Editing mode should be on.
     *
     * @Given /^I move down section "(?P<section_number>\d+)"$/
     * @throws DriverException Step not available when Javascript is enabled
     * @param int $sectionnumber
     */
    public function i_move_down_section($sectionnumber) {

        if ($this->running_javascript()) {
            throw new DriverException('Move a section down step is not available with Javascript enabled');
        }

        // Ensures the section exists.
        $sectionxpath = $this->section_exists($sectionnumber);

        // Follows the link
        $movedownlink = $this->get_node_in_container('link', get_string('movedown'), 'xpath_element', $sectionxpath);
        $movedownlink->click();
    }

    /**
     * Checks that the specified activity is visible. You need to be in the course page. It can be used being logged as a student and as a teacher on editing mode.
     *
     * @Then /^"(?P<activity_or_resource_string>(?:[^"]|\\")*)" activity should be visible$/
     * @param string $activityname
     */
    public function activity_should_be_visible($activityname) {

        // The activity must exists and be visible.
        $activitynode = $this->get_activity_node($activityname);

        if ($this->is_course_editor()) {

            // The activity should not be dimmed.
            try {
                $this->find('css', 'a.dimmed', false, $activitynode);
                throw new ExpectationException('"' . $activityname . '" is hidden', $this->getSession());
            } catch (ElementNotFoundException $e) {
                // All ok.
            }

            // The 'Hide' button should be available.
            $nohideexception = new ExpectationException('"' . $activityname . '" don\'t have a "' . get_string('hide') . '" icon', $this->getSession());
            $this->find('named', array('link', get_string('hide')), $nohideexception, $activitynode);
        }
    }

    /**
     * Checks that the specified activity is hidden. You need to be in the course page. It can be used being logged as a student and as a teacher on editing mode.
     *
     * @Then /^"(?P<activity_or_resource_string>(?:[^"]|\\")*)" activity should be hidden$/
     * @param string $activityname
     */
    public function activity_should_be_hidden($activityname) {

        if ($this->is_course_editor()) {

            // The activity should exists.
            $activitynode = $this->get_activity_node($activityname);

            // Should be hidden.
            $exception = new ExpectationException('"' . $activityname . '" is not dimmed', $this->getSession());
            $this->find('css', 'a.dimmed', $exception, $activitynode);

            // Also 'Show' icon.
            $noshowexception = new ExpectationException('"' . $activityname . '" don\'t have a "' . get_string('show') . '" icon', $this->getSession());
            $this->find('named', array('link', get_string('show')), $noshowexception, $activitynode);

        } else {

            // It should not exists at all.
            try {
                $this->find_link($activityname);
                throw new ExpectationException('The "' . $activityname . '" should not appear');
            } catch (ElementNotFoundException $e) {
                // This is good, the activity should not be there.
            }
        }

    }

    /**
     * Moves the specified activity to the first slot of a section. This step is experimental when using it in Javascript tests. Editing mode should be on.
     *
     * @Given /^I move "(?P<activity_name_string>(?:[^"]|\\")*)" activity to section "(?P<section_number>\d+)"$/
     * @param string $activityname The activity name
     * @param int $sectionnumber The number of section
     */
    public function i_move_activity_to_section($activityname, $sectionnumber) {

        // Ensure the destination is valid.
        $sectionxpath = $this->section_exists($sectionnumber);

        $activitynode = $this->get_activity_element('.editing_move img', 'css_element', $activityname);

        // JS enabled.
        if ($this->running_javascript()) {

            $destinationxpath = $sectionxpath . "/descendant::ul[contains(concat(' ', normalize-space(@class), ' '), ' yui3-dd-drop ')]";

            return array(
                new Given('I drag "' . $this->escape($activitynode->getXpath()) . '" "xpath_element" ' .
                    'and I drop it in "' . $this->escape($destinationxpath) . '" "xpath_element"'),
            );

        } else {
            // Following links with no-JS.

            // Moving to the fist spot of the section (before all other section's activities).
            return array(
                new Given('I click on "a.editing_move" "css_element" in the "' . $this->escape($activityname) . '" activity'),
                new Given('I click on "li.movehere a" "css_element" in the "' . $this->escape($sectionxpath) . '" "xpath_element"'),
            );
        }
    }

    /**
     * Edits the activity name through the edit activity; this step only works with Javascript enabled. Editing mode should be on.
     *
     * @Given /^I change "(?P<activity_name_string>(?:[^"]|\\")*)" activity name to "(?P<new_name_string>(?:[^"]|\\")*)"$/
     * @throws DriverException Step not available when Javascript is disabled
     * @param string $activityname
     * @param string $newactivityname
     */
    public function i_change_activity_name_to($activityname, $newactivityname) {

        if (!$this->running_javascript()) {
            throw new DriverException('Change activity name step is not available with Javascript disabled');
        }

        // Adding chr(10) to save changes.
        return array(
            new Given('I click on "' . get_string('edittitle') . '" "link" in the "' . $this->escape($activityname) .'" activity'),
            new Given('I fill in "title" with "' . $this->escape($newactivityname) . chr(10) . '"'),
            new Given('I wait "2" seconds')
        );
    }

    /**
     * Indents to the right the activity or resource specified by it's name. Editing mode should be on.
     *
     * @Given /^I indent right "(?P<activity_name_string>(?:[^"]|\\")*)" activity$/
     * @param string $activityname
     */
    public function i_indent_right_activity($activityname) {

        $steps = array(
            new Given('I click on "' . get_string('moveright') . '" "link" in the "' . $this->escape($activityname) . '" activity')
        );

        if ($this->running_javascript()) {
            $steps[] = new Given('I wait "2" seconds');
        }

        return $steps;
    }

    /**
     * Indents to the left the activity or resource specified by it's name. Editing mode should be on.
     *
     * @Given /^I indent left "(?P<activity_name_string>(?:[^"]|\\")*)" activity$/
     * @param string $activityname
     */
    public function i_indent_left_activity($activityname) {

        $steps = array(
            new Given('I click on "' . get_string('moveleft') . '" "link" in the "' . $this->escape($activityname) . '" activity')
        );

        if ($this->running_javascript()) {
            $steps[] = new Given('I wait "2" seconds');
        }

        return $steps;

    }

    /**
     * Deletes the activity or resource specified by it's name. This step is experimental when using it in Javascript tests. You should be in the course page with editing mode on.
     *
     * @Given /^I delete "(?P<activity_name_string>(?:[^"]|\\")*)" activity$/
     * @param string $activityname
     */
    public function i_delete_activity($activityname) {

        $deletestring = get_string('delete');

        // JS enabled.
        // Not using chain steps here because the exceptions catcher have problems detecting
        // JS modal windows and avoiding interacting them at the same time.
        if ($this->running_javascript()) {

            $element = $this->get_activity_element($deletestring, 'link', $activityname);
            $element->click();

            $this->getSession()->getDriver()->getWebDriverSession()->accept_alert();

            $this->getSession()->wait(2 * 1000, false);

        } else {

            // With JS disabled.
            $steps = array(
                new Given('I click on "' . $this->escape($deletestring) . '" "link" in the "' . $this->escape($activityname) . '" activity'),
                new Given('I press "' . get_string('yes') . '"')
            );

            return $steps;
        }
    }

    /**
     * Duplicates the activity or resource specified by it's name. You should be in the course page with editing mode on.
     *
     * @Given /^I duplicate "(?P<activity_name_string>(?:[^"]|\\")*)" activity$/
     * @param string $activityname
     */
    public function i_duplicate_activity($activityname) {
        return array(
            new Given('I click on "' . get_string('duplicate') . '" "link" in the "' . $this->escape($activityname) . '" activity'),
            new Given('I press "' . get_string('continue') .'"'),
            new Given('I press "' . get_string('duplicatecontcourse') .'"')
        );
    }

    /**
     * Duplicates the activity or resource and modifies the new activity with the provided data. You should be in the course page with editing mode on.
     *
     * @Given /^I duplicate "(?P<activity_name_string>(?:[^"]|\\")*)" activity editing the new copy with:$/
     * @param string $activityname
     * @param TableNode $data
     */
    public function i_duplicate_activity_editing_the_new_copy_with($activityname, TableNode $data) {
        return array(
            new Given('I click on "' . get_string('duplicate') . '" "link" in the "' . $this->escape($activityname) . '" activity'),
            new Given('I press "' . get_string('continue') .'"'),
            new Given('I press "' . get_string('duplicatecontedit') . '"'),
            new Given('I fill the moodle form with:', $data),
            new Given('I press "' . get_string('savechangesandreturntocourse') . '"')
        );
    }

    /**
     * Clicks on the specified element of the activity. You should be in the course page with editing mode turned on.
     *
     * @Given /^I click on "(?P<element_string>(?:[^"]|\\")*)" "(?P<selector_string>[^"]*)" in the "(?P<activity_name_string>[^"]*)" activity$/
     * @param string $element
     * @param string $selectortype
     * @param string $activityname
     */
    public function i_click_on_in_the_activity($element, $selectortype, $activityname) {
        $element = $this->get_activity_element($element, $selectortype, $activityname);
        $element->click();
    }

    /**
     * Clicks on the specified element inside the activity container.
     *
     * @throws ElementNotFoundException
     * @param string $element
     * @param string $selectortype
     * @param string $activityname
     * @return NodeElement
     */
    protected function get_activity_element($element, $selectortype, $activityname) {
        $activitynode = $this->get_activity_node($activityname);

        // Transforming to Behat selector/locator.
        list($selector, $locator) = $this->transform_selector($selectortype, $element);
        $exception = new ElementNotFoundException($this->getSession(), '"' . $element . '" "' . $selectortype . '" in "' . $activityname . '" ');

        return $this->find($selector, $locator, $exception, $activitynode);
    }

    /**
     * Checks if the course section exists.
     *
     * @throws ElementNotFoundException Thrown by behat_base::find
     * @param int $sectionnumber
     * @return string The xpath of the section.
     */
    protected function section_exists($sectionnumber) {

        // Just to give more info in case it does not exist.
        $xpath = "//li[@id='section-" . $sectionnumber . "']";
        $exception = new ElementNotFoundException($this->getSession(), "Section $sectionnumber ");
        $this->find('xpath', $xpath, $exception);

        return $xpath;
    }

    /**
     * Returns the show section icon or throws an exception.
     *
     * @throws ElementNotFoundException Thrown by behat_base::find
     * @param int $sectionnumber
     * @return NodeElement
     */
    protected function show_section_icon_exists($sectionnumber) {

        // Gets the section xpath and ensure it exists.
        $xpath = $this->section_exists($sectionnumber);

        // We need to know the course format as the text strings depends on them.
        $courseformat = $this->get_course_format();

        // Checking the show button alt text and show icon.
        $showtext = $this->getSession()->getSelectorsHandler()->xpathLiteral(get_string('showfromothers', $courseformat));
        $linkxpath = $xpath . "/descendant::a[@title=$showtext]";
        $imgxpath = $linkxpath . "/descendant::img[@alt=$showtext][contains(@src, 'show')]";

        $exception = new ElementNotFoundException($this->getSession(), 'Show section icon ');
        $this->find('xpath', $imgxpath, $exception);

        // Returing the link so both Non-JS and JS browsers can interact with it.
        return $this->find('xpath', $linkxpath, $exception);
    }

    /**
     * Returns the hide section icon link if it exists or throws exception.
     *
     * @throws ElementNotFoundException Thrown by behat_base::find
     * @param int $sectionnumber
     * @return NodeElement
     */
    protected function hide_section_icon_exists($sectionnumber) {

        // Gets the section xpath and ensure it exists.
        $xpath = $this->section_exists($sectionnumber);

        // We need to know the course format as the text strings depends on them.
        $courseformat = $this->get_course_format();

        // Checking the hide button alt text and hide icon.
        $hidetext = $this->getSession()->getSelectorsHandler()->xpathLiteral(get_string('hidefromothers', $courseformat));
        $linkxpath = $xpath . "/descendant::a[@title=$hidetext]";
        $imgxpath = $linkxpath . "/descendant::img[@alt=$hidetext][contains(@src, 'hide')]";

        $exception = new ElementNotFoundException($this->getSession(), 'Hide section icon ');
        $this->find('xpath', $imgxpath, $exception);

        // Returing the link so both Non-JS and JS browsers can interact with it.
        return $this->find('xpath', $linkxpath, $exception);
    }

    /**
     * Gets the current course format.
     *
     * @throws ExpectationException If we are not in the course view page.
     * @return string The course format in a frankenstyled name.
     */
    protected function get_course_format() {

        $exception = new ExpectationException('You are not in a course page', $this->getSession());

        // The moodle body's id attribute contains the course format.
        $node = $this->getSession()->getPage()->find('css', 'body');
        if (!$node) {
            throw $exception;
        }

        if (!$bodyid = $node->getAttribute('id')) {
            throw $exception;
        }

        if (strstr($bodyid, 'page-course-view-') === false) {
            throw $exception;
        }

        return 'format_' . str_replace('page-course-view-', '', $bodyid);
    }

    /**
     * Gets the section's activites DOM nodes.
     *
     * @param string $sectionxpath
     * @return array NodeElement instances
     */
    protected function get_section_activities($sectionxpath) {

        $xpath = $sectionxpath . "/descendant::li[contains(concat(' ', normalize-space(@class), ' '), ' activity ')]";

        // We spin here, as activities usually require a lot of time to load.
        try {
            $activities = $this->find_all('xpath', $xpath);
        } catch (ElementNotFoundException $e) {
            return false;
        }

        return $activities;
    }

    /**
     * Returns the DOM node of the activity from <li>.
     *
     * @throws ElementNotFoundException Thrown by behat_base::find
     * @param string $activityname The activity name
     * @return NodeElement
     */
    protected function get_activity_node($activityname) {

        $activityname = $this->getSession()->getSelectorsHandler()->xpathLiteral($activityname);
        $xpath = "//li[contains(concat(' ', normalize-space(@class), ' '), ' activity ')][contains(., $activityname)]";

        return $this->find('xpath', $xpath);
    }

    /**
     * Returns whether the user can edit the course contents or not.
     *
     * @return bool
     */
    protected function is_course_editor() {

        // We don't need to behat_base::spin() here as all is already loaded.
        if (!$this->getSession()->getPage()->findButton(get_string('turneditingoff')) &&
                !$this->getSession()->getPage()->findButton(get_string('turneditingon'))) {
            return false;
        }

        return true;
    }

    /**
     * Clicks to expand or collapse a category displayed on the frontpage
     *
     * @Given /^I toggle "(?P<categoryname_string>(?:[^"]|\\")*)" category children visibility in frontpage$/
     * @throws ExpectationException
     * @param string $categoryname
     */
    public function i_toggle_category_children_visibility_in_frontpage($categoryname) {

        $headingtags = array();
        for ($i = 1; $i <= 6; $i++) {
            $headingtags[] = 'self::h' . $i;
        }

        $exception = new ExpectationException('"' . $categoryname . '" category can not be found', $this->getSession());
        $categoryliteral = $this->getSession()->getSelectorsHandler()->xpathLiteral($categoryname);
        $xpath = "//div[@class='info']/descendant::*[" . implode(' or ', $headingtags) . "][@class='name'][./descendant::a[.=$categoryliteral]]";
        $node = $this->find('xpath', $xpath, $exception);
        $node->click();

        // Smooth expansion.
        $this->getSession()->wait(1000, false);
    }

}
