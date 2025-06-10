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
 * Methods related to the interaction with the MathType.
 * @package    filter_wiris
 * @subpackage wiris
 * @copyright  WIRIS Europe (Maths for more S.L)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/behat_wiris_base.php');

use Behat\Mink\Exception\ExpectationException;

/**
 * Class behat_wiris_page
 *
 * This class represents a Behat WIRIS page and is used for testing purposes.
 *
 * @package    filter_wiris
 * @subpackage wiris
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_wiris_editor extends behat_wiris_base {




    /**
     * Once the editor has been opened and focused, set the MathType formula to the specified value.
     *
     * @Given I set MathType formula to :value
     * @param  string $value value to which we want to set the field
     * @throws ElementNotFoundException If MathType editor does not exist, it will throw an invalid argument exception.
     */
    public function i_set_mathtype_formula_to($value) {
        $exception = new ExpectationException('MathType editor container not found.', $this->getSession());
        $this->spin(
            function ($context, $args) {
                return $context->getSession()->getPage()->find('xpath', '//div[contains(@class,\'wrs_editor\')]
                //span[@class=\'wrs_container\']');
            },
            [],
            self::get_extended_timeout(),
            $exception,
            true
        );
        $session = $this->getSession(); // Get the mink session.
        if (strpos($value, 'math') == false) {
            $component = $session->getPage()->find('xpath', "//input[@class='wrs_focusElement']");
            if (empty($component)) {
                throw new \ElementNotFoundException($this->getSession(), get_string(
                    'wirisbehaterroreditornotfound',
                    'filter_wiris'
                ));
            }
            $component->setValue($value);
        } else {
            $script = 'return document.getElementById(\'wrs_content_container[0]\')';
            $container = $session->evaluateScript($script);
            if (empty($container)) {
                throw new \ElementNotFoundException($this->getSession(), get_string(
                    'wirisbehaterroreditornotfound',
                    'filter_wiris'
                ));
            }
            $script = 'const container = document.getElementById(\'wrs_content_container[0]\');' .
                'const editor = window.com.wiris.jsEditor.JsEditor.getInstance(container);' .
                'editor.setMathML(\'' . $value . '\');';
            $session->executeScript($script);
        }
    }

    /**
     * Press on accept button in MathType Editor
     *
     * @Given I press accept button in MathType Editor
     * @throws ExpectationException If accept button is not found, it will throw an exception.
     */
    public function i_press_accept_button_in_mathtype_editor() {
        $exception = new ExpectationException('Accept button not found.', $this->getSession());
        $this->spin(
            function ($context) {
                $toolbar = $context->getSession()->getPage()->find('xpath', '//div[@id=\'wrs_modal_dialogContainer[0]\' and
                @class=\'wrs_modal_dialogContainer wrs_modal_desktop wrs_stack\']//div[@class=\'wrs_panelContainer\']');
                $container = $context->getSession()->getPage()->find('xpath', '//div[@id=\'wrs_modal_dialogContainer[0]\' and
                @class=\'wrs_modal_dialogContainer wrs_modal_desktop wrs_stack\']//span[@class=\'wrs_container\']');
                $button = $context->getSession()->getPage()->find('xpath', '//button[@id=\'wrs_modal_button_accept[0]\']');
                return !empty($toolbar) && !empty($container);
            },
            [],
            self::get_extended_timeout(),
            $exception,
            true
        );
        $session = $this->getSession();
        $component = $session->getPage()->find('xpath', '//button[@id=\'wrs_modal_button_accept[0]\']');
        $component->click();
    }

    /**
     * Press on cancel button in MathType Editor
     *
     * @Given I press cancel button in MathType Editor
     * @throws ExpectationException If Cancel button is not found, it will throw an exception.
     */
    public function i_press_cancel_button_in_mathtype_editor() {
        $exception = new ExpectationException('Cancel button not found.', $this->getSession());
        $this->spin(
            function ($context) {
                $toolbar = $context->getSession()->getPage()->find('xpath', '//div[@id=\'wrs_modal_dialogContainer[0]\' and
                @class=\'wrs_modal_dialogContainer wrs_modal_desktop wrs_stack\']//div[@class=\'wrs_panelContainer\']');
                $container = $context->getSession()->getPage()->find('xpath', '//div[@id=\'wrs_modal_dialogContainer[0]\' and
                @class=\'wrs_modal_dialogContainer wrs_modal_desktop wrs_stack\']//span[@class=\'wrs_container\']');
                $button = $context->getSession()->getPage()->find('xpath', '//button[@id=\'wrs_modal_button_cancel[0]\']');
                return !empty($toolbar) && !empty($container);
            },
            [],
            self::get_extended_timeout(),
            $exception,
            true
        );
        $session = $this->getSession();
        $component = $session->getPage()->find('xpath', '//button[@id=\'wrs_modal_button_cancel[0]\']');
        $component->click();
    }

    /**
     * Press on minimize button in MathType Editor
     *
     * @Given I press minimize button in MathType Editor
     * @throws ExpectationException If minimize button is not found, it will throw an exception.
     */
    public function i_press_minimize_button_in_mathtype_editor() {
        $exception = new ExpectationException('Minimize button not found.', $this->getSession());
        $this->spin(
            function ($context) {
                $button = $context->getSession()->getPage()->find('xpath', '//a[@id=\'wrs_modal_minimize_button[0]\']');
                return !empty($button);
            },
            [],
            self::get_extended_timeout(),
            $exception,
            true
        );
        $session = $this->getSession();
        $component = $session->getPage()->find('xpath', '//a[@id=\'wrs_modal_minimize_button[0]\']');
        $component->click();
    }

    /**
     * Click on MathType editor title bar
     *
     * @Given I click on MathType editor title bar
     * @throws ExpectationException If the editor title bar is not found, it will throw an exception.
     */
    public function i_click_on_mathtype_editor_title_bar() {
        $session = $this->getSession();
        $component = $session->getPage()->find('xpath', '//div[@class=\'wrs_modal_title\']');
        if (empty($component)) {
            throw new ExpectationException('Editor title bar not found.', $this->getSession());
        }
        $component->click();
    }

    /**
     * Move the MathType editor
     *
     * @Given I move the MathType editor
     * @throws ExpectationException If the editor title bar is not found, it will throw an exception.
     */
    public function i_move_mathtype_editor_window() {
        $session = $this->getSession();
        $component = $session->getPage()->find('xpath', '//div[@class=\'wrs_modal_title\']');
        if (empty($component)) {
            throw new ExpectationException('Editor title bar not found.', $this->getSession());
        }
        // JavaScript to simulate drag and drop
        $script = <<<JS
        var element = document.evaluate("//div[@class='wrs_modal_title']", document, null, XPathResult.FIRST_ORDERED_NODE_TYPE, null).singleNodeValue;
        var rect = element.getBoundingClientRect();
        var startX = rect.left + rect.width / 2;
        var startY = rect.top + rect.height / 2;
        var endX = startX - 200;  // Move 200px to the left
        var endY = startY - 100;   // Move 100px up
        var dataTransfer = new DataTransfer();
        element.dispatchEvent(new MouseEvent('mousedown', {
            clientX: startX,
            clientY: startY,
            bubbles: true
        }));
        document.dispatchEvent(new MouseEvent('mousemove', {
            clientX: endX,
            clientY: endY,
            bubbles: true
        }));
        document.dispatchEvent(new MouseEvent('mouseup', {
            clientX: endX,
            clientY: endY,
            bubbles: true
        }));
    JS;
        // Execute the script
        $session->executeScript($script);
    }

    /**
     * Check if Mathtype button is in full-screen mode
     *
     * @Then I check editor is in full-screen mode
     * @throws ExpectationException If the full screen button is not found, it will throw an exception.
     */
    public function i_check_editor_is_in_full_screen_mode() {
        $session = $this->getSession();
        $component = $session->getPage()->find('xpath', '//a[@title=\'Exit full-screen\']');
        if (empty($component)) {
            throw new ExpectationException('Exit full-screen button not found.', $this->getSession());
        }
        $component->click();
    }

    /**
     * Click on MathType right to left screen button
     *
     * @Given I click on MathType right to left button
     * @throws ExpectationException If the full screen button is not found, it will throw an exception.
     */
    public function i_click_on_mathtype_right_to_left_button() {
        $session = $this->getSession();
        $component = $session->getPage()->find('xpath', '//button[@title=\'Right to left editing\']');
        if (empty($component)) {
            throw new ExpectationException('Right to Left button not found.', $this->getSession());
        }
        $component->click();
    }

    /**
     * Follows the page redirection. Use this step after clicking the editor's maximize button
     *
     * @Then full screen modal window is opened
     * @param  string $seconds time to wait
     */
    public function full_screen_modal_window_is_opened() {
        $session = $this->getSession();
        $component = $session->getPage()->find('xpath', '//div[contains(@class, "wrs_modal_overlay wrs_modal_desktop wrs_maximized")]');
        if (empty($component) || !$component->isVisible()) {
            throw new ExpectationException("Full-screen modal window is opened.", $this->getSession());
        }
    }

    /**
     * Look whether MathType editor exist
     *
     * @Then MathType editor should exist
     * @throws ExpectationException If MathType editor not found, it will throw an exception.
     */
    public function mathtype_editor_should_exist() {
        $session = $this->getSession();
        $formula = $session->getPage()->find('xpath', '//div[contains(@id, \'wrs_modal_wrapper\')]');
        if (empty($formula)) {
            throw new ExpectationException('MathType editor not found.', $this->getSession());
        }
    }

    /**
     * Waits the excution until the MathType editor displays
     *
     * @Then i wait until MathType editor is displayed
     */
    public function i_wait_until_mathtype_editor_is_displayed() {
        // Looks for a the MT editor opened in the page.
        $editor = '//div[contains(@id, \'wrs_modal_wrapper\')]';
        $this->ensure_element_is_visible($editor, 'xpath_element');
    }

    /**
     * Waits the excution until the ChemType editor displays
     *
     * @Then i wait until ChemType editor is displayed
     */
    public function i_wait_until_chemtype_editor_is_displayed() {
        // Looks for a the CT editor opened in the page.
        $chemtab = '//button[contains(@title, \'Chemistry tab\')]';
        $this->ensure_element_is_visible($chemtab, 'xpath_element');
    }

    /**
     * Waits the excution until the MathType editor displays
     *
     * @Then text should exist
     */
    public function text_should_exist() {
        $session = $this->getSession();
        $text = $session->getPage()->find('xpath', '//div[@id="id_introeditoreditable"]/text()');
        if (empty($text)) {
            throw new ExpectationException('MathType editor not found.', $this->getSession());
        }
    }
    /**
     * Enters the div inside the specified editor
     * @Given I switch to div with locator :locator
     * @param String $locator
     */
    public function iswitchtodivwithlocator($locator) {

        $javascript = "(function(){
        var divs = document.getElementsByTagName('div');
        for (var i = 0; i < divs.length; i++) {
            divs[i].name = 'div_number_' + (i + 1) ;
        }
        })()";

        $this->getSession()->executeScript($javascript);
        $div = $this->getSession()->getPage()->find('xpath', '//div[@id="'.$locator.'"]');
        if (empty($div)) {
            throw new ExpectationException('div with locator \''.$locator.'\' not found', $this->getSession());
        }
        $divname = $div->getAttribute("name");
        $this->getSession()->getDriver()->switchToDiv($divname);
    }
    /**
     * Enters the inframe inside the specified tinymce editor
     * @Given I switch to iframe with locator :locator
     * @param String $locator
     */
    public function iswitchtoiframewithlocator($locator) {

        $javascript = "(function(){
        var iframes = document.getElementsByTagName('iframe');
        for (var i = 0; i < iframes.length; i++) {
            iframes[i].name = 'iframe_number_' + (i + 1) ;
        }
        })()";

        $this->getSession()->executeScript($javascript);
        $iframe = $this->getSession()->getPage()->find('xpath', '//iframe[@id="'.$locator.'"]');
        if (empty($iframe)) {
            throw new ExpectationException('Iframe with locator \''.$locator.'\' not found', $this->getSession());
        }
        $iframename = $iframe->getAttribute("name");
        $this->getSession()->getDriver()->switchToIFrame($iframename);
    }

    /**
     * Exits the current iframe and return to the default frame
     * @Given I return to default frame
     * @param String $locator
     */
    public function i_return_to_default_frame() {
        $this->getSession()->getDriver()->switchToIFrame(null);
    }
}
