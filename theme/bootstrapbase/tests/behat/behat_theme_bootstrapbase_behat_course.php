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
 * Behat course-related steps definitions overrides.
 *
 * @package    theme_bootstrapbase
 * @category   test
 * @copyright  2016 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../../../course/tests/behat/behat_course.php');

use Behat\Gherkin\Node\TableNode as TableNode,
    Behat\Mink\Exception\ExpectationException as ExpectationException,
    Behat\Mink\Exception\DriverException as DriverException,
    Behat\Mink\Exception\ElementNotFoundException as ElementNotFoundException;

/**
 * Course-related steps definitions overrides.
 *
 * @package    theme_bootstrapbase
 * @category   test
 * @copyright  2016 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_theme_bootstrapbase_behat_course extends behat_course {

    public function i_open_actions_menu($activityname) {

        if (!$this->running_javascript()) {
            throw new DriverException('Activities actions menu not available when Javascript is disabled');
        }

        // If it is already opened we do nothing.
        $activitynode = $this->get_activity_node($activityname);
        $classes = array_flip(explode(' ', $activitynode->getAttribute('class')));
        if (!empty($classes['action-menu-shown'])) {
            return;
        }

        $this->execute('behat_course::i_click_on_in_the_activity',
                array("a[role='menuitem']", "css_element", $this->escape($activityname))
        );

    }

    public function i_close_actions_menu($activityname) {

        if (!$this->running_javascript()) {
            throw new DriverException('Activities actions menu not available when Javascript is disabled');
        }

        // If it is already closed we do nothing.
        $activitynode = $this->get_activity_node($activityname);
        $classes = array_flip(explode(' ', $activitynode->getAttribute('class')));
        if (empty($classes['action-menu-shown'])) {
            return;
        }

        $this->execute('behat_course::i_click_on_in_the_activity',
                array("a[role='menuitem']", "css_element", $this->escape($activityname))
        );
    }

    public function actions_menu_should_be_open($activityname) {

        if (!$this->running_javascript()) {
            throw new DriverException('Activities actions menu not available when Javascript is disabled');
        }

        // If it is already closed we do nothing.
        $activitynode = $this->get_activity_node($activityname);
        $classes = array_flip(explode(' ', $activitynode->getAttribute('class')));
        if (empty($classes['action-menu-shown'])) {
            throw new ExpectationException(
                    sprintf("The action menu for '%s' is not open", $activityname), $this->getSession());
        }
    }

    public function i_add_to_section($activity, $section) {

        if ($this->getSession()->getPage()->find('css', 'body#page-site-index') && (int)$section <= 1) {
            // We are on the frontpage.
            if ($section) {
                // Section 1 represents the contents on the frontpage.
                $sectionxpath = "//body[@id='page-site-index']/descendant::div[contains(concat(' ',normalize-space(@class),' ')," .
                        "' sitetopic ')]";
            } else {
                // Section 0 represents "Site main menu" block.
                $sectionxpath = "//div[contains(concat(' ',normalize-space(@class),' '),' block_site_main_menu ')]";
            }
        } else {
            // We are inside the course.
            $sectionxpath = "//li[@id='section-" . $section . "']";
        }

        $activityliteral = behat_context_helper::escape(ucfirst($activity));

        if ($this->running_javascript()) {

            // Clicks add activity or resource section link.
            $sectionxpath = $sectionxpath . "/descendant::div[@class='section-modchooser']/span/a";
            $sectionnode = $this->find('xpath', $sectionxpath);
            $sectionnode->click();

            // Clicks the selected activity if it exists.
            $activityxpath = "//div[@id='chooseform']/descendant::label" .
                    "/descendant::span[contains(concat(' ', normalize-space(@class), ' '), ' typename ')]" .
                    "[normalize-space(.)=$activityliteral]" .
                    "/parent::label/child::input";
            $activitynode = $this->find('xpath', $activityxpath);
            $activitynode->doubleClick();

        } else {
            // Without Javascript.

            // Selecting the option from the select box which contains the option.
            $selectxpath = $sectionxpath . "/descendant::div[contains(concat(' ', normalize-space(@class), ' '), " .
                    "' section_add_menus ')]/descendant::select[option[normalize-space(.)=$activityliteral]]";
            $selectnode = $this->find('xpath', $selectxpath);
            $selectnode->selectOption($activity);

            // Go button.
            $gobuttonxpath = $selectxpath . "/ancestor::form/descendant::input[@type='submit']";
            $gobutton = $this->find('xpath', $gobuttonxpath);
            $gobutton->click();
        }

    }

    public function i_duplicate_activity_editing_the_new_copy_with($activityname, TableNode $data) {

        $activity = $this->escape($activityname);
        $activityliteral = behat_context_helper::escape($activityname);

        $this->execute("behat_course::i_duplicate_activity", $activity);

        // Determine the future new activity xpath from the former one.
        $duplicatedxpath = "//li[contains(concat(' ', normalize-space(@class), ' '), ' activity ')]" .
                "[contains(., $activityliteral)]/following-sibling::li";
        $duplicatedactionsmenuxpath = $duplicatedxpath . "/descendant::a[@role='menuitem']";

        if ($this->running_javascript()) {
            // We wait until the AJAX request finishes and the section is visible again.
            $hiddenlightboxxpath = "//li[contains(concat(' ', normalize-space(@class), ' '), ' activity ')]" .
                    "[contains(., $activityliteral)]" .
                    "/ancestor::li[contains(concat(' ', normalize-space(@class), ' '), ' section ')]" .
                    "/descendant::div[contains(concat(' ', @class, ' '), ' lightbox ')][contains(@style, 'display: none')]";

            $this->execute("behat_general::wait_until_exists",
                    array($this->escape($hiddenlightboxxpath), "xpath_element")
            );

            // Close the original activity actions menu.
            $this->i_close_actions_menu($activity);

            // The next sibling of the former activity will be the duplicated one, so we click on it from it's xpath as, at
            // this point, it don't even exists in the DOM (the steps are executed when we return them).
            $this->execute('behat_general::i_click_on',
                    array($this->escape($duplicatedactionsmenuxpath), "xpath_element")
            );
        }

        // We force the xpath as otherwise mink tries to interact with the former one.
        $this->execute('behat_general::i_click_on_in_the',
                array(get_string('editsettings'), "link", $this->escape($duplicatedxpath), "xpath_element")
        );

        $this->execute("behat_forms::i_set_the_following_fields_to_these_values", $data);
        $this->execute("behat_forms::press_button", get_string('savechangesandreturntocourse'));

    }

    public function i_open_section_edit_menu($sectionnumber) {
        if (!$this->running_javascript()) {
            throw new DriverException('Section edit menu not available when Javascript is disabled');
        }

        // Wait for section to be available, before clicking on the menu.
        $this->i_wait_until_section_is_available($sectionnumber);

        // If it is already opened we do nothing.
        $xpath = $this->section_exists($sectionnumber);
        $xpath .= "/descendant::div[contains(@class, 'section-actions')]/descendant::a[contains(@class, 'textmenu')]";

        $exception = new ExpectationException('Section "' . $sectionnumber . '" was not found', $this->getSession());
        $menu = $this->find('xpath', $xpath, $exception);
        $menu->click();
        $this->i_wait_until_section_is_available($sectionnumber);
    }

    public function section_should_be_visible($sectionnumber) {

        $sectionxpath = $this->section_exists($sectionnumber);

        // Section should not be hidden.
        $xpath = $sectionxpath . "[not(contains(concat(' ', normalize-space(@class), ' '), ' hidden '))]";
        if (!$this->getSession()->getPage()->find('xpath', $xpath)) {
            throw new ExpectationException('The section is hidden', $this->getSession());
        }

        // Edit menu should be visible.
        if ($this->is_course_editor()) {
            $xpath = $sectionxpath .
                    "/descendant::div[contains(@class, 'section-actions')]" .
                    "/descendant::a[contains(@class, 'textmenu')]";
            if (!$this->getSession()->getPage()->find('xpath', $xpath)) {
                throw new ExpectationException('The section edit menu is not available', $this->getSession());
            }
        }
    }

    protected function user_clicks_on_management_listing_action($listingtype, $listingnode, $action) {
        $actionsnode = $listingnode->find('xpath',
                "//*[contains(concat(' ', normalize-space(@class), ' '), '{$listingtype}-item-actions')]");
        if (!$actionsnode) {
            throw new ExpectationException("Could not find the actions for $listingtype", $this->getSession());
        }
        $actionnode = $actionsnode->find('css', '.action-'.$action);
        if (!$actionnode) {
            throw new ExpectationException("Expected action was not available or not found ($action)", $this->getSession());
        }
        if ($this->running_javascript() && !$actionnode->isVisible()) {
            $actionsnode->find('css', 'a.toggle-display')->click();
            $actionnode = $actionsnode->find('css', '.action-'.$action);
        }
        $actionnode->click();
    }

    protected function is_course_editor() {

        // We don't need to behat_base::spin() here as all is already loaded.
        if (!$this->getSession()->getPage()->findButton(get_string('turneditingoff')) &&
                !$this->getSession()->getPage()->findButton(get_string('turneditingon'))) {
            return false;
        }

        return true;
    }

    public function i_navigate_to_course_participants() {
        $coursestr = behat_context_helper::escape(get_string('courses'));
        $mycoursestr = behat_context_helper::escape(get_string('mycourses'));
        $xpath = "//div[contains(@class,'block')]//li[p/*[string(.)=$coursestr or string(.)=$mycoursestr]]";
        $this->execute('behat_general::i_click_on_in_the', [get_string('participants'), 'link', $xpath, 'xpath_element']);
    }
}
