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
 * TinyMCE custom steps definitions.
 *
 * @package    editor_tiny
 * @category   test
 * @copyright  2022 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Mink\Exception\DriverException;
use Behat\Mink\Exception\ExpectationException;

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.
require_once(__DIR__ . '/../../../../behat/behat_base.php');
require_once(__DIR__ . '/editor_tiny_helpers.php');

/**
 * TinyMCE custom behat step definitions.
 *
 * @package    editor_tiny
 * @category   test
 * @copyright  2022 Andrew Lyons <andrew@nicols.co.uk>
 */
class behat_editor_tiny extends behat_base implements \core_behat\settable_editor {
    use editor_tiny_helpers;

    /**
     * Set Tiny as default editor before executing Tiny tests.
     *
     * This step is required to ensure that TinyMCE is set as the current default editor as it may
     * not always be the default editor.
     *
     * Any Scenario, or Feature, which has the `editor_tiny` tag, or any `tiny_*` tag will have
     * this step executed before the Scenario.
     *
     * @BeforeScenario
     * @param BeforeScenarioScope $scope The Behat Scope
     */
    public function set_default_editor_flag(BeforeScenarioScope $scope): void {
        // This only applies to a scenario which matches the editor_tiny, or an tiny subplugin.
        $callback = function (string $tag): bool {
            return $tag === 'editor_tiny' || substr($tag, 0, 5) === 'tiny_';
        };

        if (!self::scope_tags_match($scope, $callback)) {
            // This scope does not require TinyMCE. Exit now.
            return;
        }

        // TinyMCE is a JavaScript editor so require JS here.
        $this->require_javascript();

        $this->execute('behat_general::the_default_editor_is_set_to', ['tiny']);
    }

    /**
     * Click on a button for the specified TinyMCE editor.
     *
     * @When /^I click on the "(?P<button_string>(?:[^"]|\\")*)" button for the "(?P<locator_string>(?:[^"]|\\")*)" TinyMCE editor$/
     *
     * @param string $button The label of the button
     * @param string $locator The locator for the editor
     */
    public function i_click_on_button(string $button, string $locator): void {
        $this->require_tiny_tags();
        $container = $this->get_editor_container_for_locator($locator);

        $this->execute('behat_general::i_click_on_in_the', [$button, 'button', $container, 'NodeElement']);
    }

    /**
     * Checks that a button exists in specified TinyMCE editor.
     *
     * @When /^"(?P<button_string>(?:[^"]|\\")*)" button should exist in the "(?P<locator_string>(?:[^"]|\\")*)" TinyMCE editor$/
     *
     * @param string $button The label of the button
     * @param string $locator The locator for the editor
     */
    public function button_should_exist(string $button, string $locator): void {
        $this->require_tiny_tags();
        $container = $this->get_editor_container_for_locator($locator);

        $this->execute('behat_general::should_exist_in_the', [$button, 'button', $container, 'NodeElement']);
    }

    /**
     * Checks that a button does not exist in specified TinyMCE editor.
     *
     * @When /^"(?P<button_string>(?:[^"]|\\")*)" button should not exist in the "(?P<locator_string>(?:[^"]|\\")*)" TinyMCE editor$/
     *
     * @param string $button The label of the button
     * @param string $locator The locator for the editor
     */
    public function button_should_not_exist(string $button, string $locator): void {
        $this->require_tiny_tags();
        $container = $this->get_editor_container_for_locator($locator);

        $this->execute('behat_general::should_not_exist_in_the', [$button, 'button', $container, 'NodeElement']);
    }

    /**
     * Confirm that the button state of the specified button/editor combination matches the expectation.
     *
     * @Then /^the "(?P<button_string>(?:[^"]|\\")*)" button of the "(?P<locator_string>(?:[^"]|\\")*)" TinyMCE editor has state "(?P<state_string>(?:[^"]|\\")*)"$/
     *
     * @param string $button The text name of the button
     * @param string $locator The locator string for the editor
     * @param string $state The state of the button
     * @throws ExpectationException Thrown if the button state is not correct
     */
    public function button_state_is(string $button, string $locator, string $state): void {
        $this->require_tiny_tags();
        $container = $this->get_editor_container_for_locator($locator);

        $button = $this->find_button($button, false, $container);
        $buttonstate = $button->getAttribute('aria-pressed');

        if ($buttonstate !== $state) {
            throw new ExpectationException("Button '{$button}' is in state '{$buttonstate}' not '{$state}'", $this->getSession());
        }
    }

    /**
     * Click on a button for the specified TinyMCE editor.
     *
     * @When /^I click on the "(?P<menuitem_string>(?:[^"]|\\")*)" menu item for the "(?P<locator_string>(?:[^"]|\\")*)" TinyMCE editor$/
     *
     * @param string $menuitem The label of the menu item
     * @param string $locator The locator for the editor
     */
    public function i_click_on_menuitem_in_menu(string $menuitem, string $locator): void {
        $this->require_tiny_tags();
        $container = $this->get_editor_container_for_locator($locator);

        $menubar = $container->find('css', '[role="menubar"]');

        $menus = array_map(function(string $value): string {
            return trim($value);
        }, explode('>', $menuitem));

        // Open the menu bar.
        $mainmenu = array_shift($menus);
        $this->execute('behat_general::i_click_on_in_the', [$mainmenu, 'button', $menubar, 'NodeElement']);

        foreach ($menus as $menuitem) {
            // Find the menu that was opened.
            $openmenu = $this->find('css', '.tox-selected-menu');

            // Move the mouse to the first item in the list.
            // This is required because WebDriver takes the shortest path to the next click location,
            // which will mean crossing across other menu items.
            $firstlink = $openmenu->find('css', "[role^='menuitem'] .tox-collection__item-icon");
            $firstlink->mouseover();

            // Now match by title where the role matches any menuitem, or menuitemcheckbox, or menuitem*.
            $link = $openmenu->find('css', "[aria-label='{$menuitem}'][role^='menuitem']");
            $this->execute('behat_general::i_click_on', [$link, 'NodeElement']);
        }
    }

    /**
     * Select the element type/index for the specified TinyMCE editor.
     *
     * @When /^I select the "(?P<textlocator_string>(?:[^"]|\\")*)" element in position "(?P<position_int>(?:[^"]|\\")*)" of the "(?P<locator_string>(?:[^"]|\\")*)" TinyMCE editor$/
     * @param string $textlocator The type of element to select (for example `p` or `span`)
     * @param int $position The zero-indexed position
     * @param string $locator The editor to select within
     */
    public function select_text(string $textlocator, int $position, string $locator): void {
        $this->require_tiny_tags();

        $editor = $this->get_textarea_for_locator($locator);
        $editorid = $editor->getAttribute('id');

        // Ensure that a name is set on the iframe relating to the editorid.
        $js = <<<EOF
            const element = instance.dom.select("{$textlocator}")[{$position}];
            instance.selection.select(element);
        EOF;

        $this->execute_javascript_for_editor($editorid, $js);
    }

    /**
     * Upload a file in the file picker using the repository_upload plugin.
     *
     * Note: This step assumes we are already in the file picker.
     * Note: This step is for use by TinyMCE and will be removed once an appropriate step is added to core.
     * See MDL-76001 for details.
     *
     * @Given /^I upload "(?P<filepath_string>(?:[^"]|\\")*)" to the file picker for TinyMCE$/
     */
    public function i_upload_a_file_in_the_filepicker(string $filepath): void {
        if (!$this->has_tag('javascript')) {
            throw new DriverException('The file picker is only available with javascript enabled');
        }

        if (!$this->has_tag('_file_upload')) {
            throw new DriverException('File upload tests must have the @_file_upload tag on either the scenario or feature.');
        }

        if (!$this->has_tag('editor_tiny')) {
            throw new DriverException('This step is intended for use in TinyMCE. Please vote for MDL-76001');
        }

        $filepicker = $this->find('dialogue', get_string('filepicker', 'core_repository'));

        $this->execute('behat_general::i_click_on_in_the', [
            get_string('pluginname', 'repository_upload'), 'link',
            $filepicker, 'NodeElement',
        ]);

        $reporegion = $filepicker->find('css', '.fp-repo-items');
        $fileinput = $this->find('field', get_string('attachment', 'core_repository'), false, $reporegion);

        $filepath = $this->normalise_fixture_filepath($filepath);

        $fileinput->attachFile($filepath);
        $this->execute('behat_general::i_click_on_in_the', [
            get_string('upload', 'repository'), 'button',
            $reporegion, 'NodeElement',
        ]);
    }

    /**
     * Select in the editor.
     *
     * @param string $locator
     * @param string $type
     * @param string $editorlocator
     *
     * @Given /^I select the "(?P<locator_string>(?:[^"]|\\")*)" "(?P<type_string>(?:[^"]|\\")*)" in the "(?P<editorlocator_string>(?:[^"]|\\")*)" TinyMCE editor$/
     */
    public function select_in_editor(string $locator, string $type, string $editorlocator): void {
        $this->require_tiny_tags();

        $editor = $this->get_textarea_for_locator($editorlocator);
        $editorid = $editor->getAttribute('id');

        // Get the iframe name for this editor.
        $iframename = $this->get_editor_iframe_name($editorlocator);

        // Switch to it.
        $this->execute('behat_general::switch_to_iframe', [$iframename]);

        // Find the element.
        $element = $this->find($type, $locator);
        $xpath = $element->getXpath();

        // Switch back to the main window.
        $this->execute('behat_general::switch_to_the_main_frame', []);

        // Select the Node using the xpath.
        $js = <<<EOF
            const editorDocument = instance.getDoc();
            const element = editorDocument.evaluate(
                "{$xpath}",
                editorDocument,
                null,
                XPathResult.FIRST_ORDERED_NODE_TYPE,
                null
            ).singleNodeValue;

            instance.selection.select(element);
        EOF;
        $this->execute_javascript_for_editor($editorid, $js);
    }

    /**
     * Expand all of the TinyMCE toolbars.
     *
     * @Given /^I expand all toolbars for the "(?P<editorlocator_string>(?:[^"]|\\")*)" TinyMCE editor$/
     *
     * @param string $locator
     */
    public function expand_all_toolbars(string $editorlocator): void {
        $this->require_tiny_tags();

        $editor = $this->get_editor_container_for_locator($editorlocator);
        try {
            $button = $this->find('button', get_string('tiny:reveal_or_hide_additional_toolbar_items', 'editor_tiny'), false, $editor);
        } catch (ExpectationException $e) {
            // No more button, so no need to expand.
            return;
        }

        if ($button->getAttribute(('aria-expanded')) === 'false') {
            $this->execute('behat_general::i_click_on', [$button, 'NodeElement']);
        }
    }

    /**
     * Switch to the TinyMCE iframe using a selector.
     *
     * @param string $editorlocator
     *
     * @When /^I switch to the "(?P<editorlocator_string>(?:[^"]|\\")*)" TinyMCE editor iframe$/
     */
    public function switch_to_tiny_iframe(string $editorlocator): void {
        $this->require_tiny_tags();

        // Get the iframe name for this editor.
        $iframename = $this->get_editor_iframe_name($editorlocator);

        // Switch to it.
        $this->execute('behat_general::switch_to_iframe', [$iframename]);
    }
}
