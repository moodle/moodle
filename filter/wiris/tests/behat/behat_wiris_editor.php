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
 * @package    filter
 * @subpackage wiris
 * @copyright  WIRIS Europe (Maths for more S.L)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/behat_wiris_base.php');

use Behat\Mink\Exception\ExpectationException;

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
            function($context, $args) {
                return $context->getSession()->getPage()->find('xpath', '//div[contains(@class,\'wrs_editor\')]
                //span[@class=\'wrs_container\']');
            },
            array(),
            self::get_extended_timeout(),
            $exception,
            true
        );
        $session = $this->getSession(); // Get the mink session.
        if (strpos($value, 'math') == false) {
            $component = $session->getPage()->find('xpath', "//input[@class='wrs_focusElement']");
            if (empty($component)) {
                throw new \ElementNotFoundException($this->getSession(), get_string('wirisbehaterroreditornotfound'
                , 'filter_wiris'));
            }
            $component->setValue($value);
        } else {
            $script = 'return document.getElementById(\'wrs_content_container[0]\')';
            $container = $session->evaluateScript($script);
            if (empty($container)) {
                throw new \ElementNotFoundException($this->getSession(), get_string('wirisbehaterroreditornotfound'
                , 'filter_wiris'));
            }
            $script = 'const container = document.getElementById(\'wrs_content_container[0]\');'.
                'const editor = window.com.wiris.jsEditor.JsEditor.getInstance(container);'.
                'editor.setMathML(\''.$value.'\');';
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
            function($context) {
                $toolbar = $context->getSession()->getPage()->find('xpath', '//div[@id=\'wrs_modal_dialogContainer[0]\' and
                @class=\'wrs_modal_dialogContainer wrs_modal_desktop wrs_stack\']//div[@class=\'wrs_panelContainer\']');
                $container = $context->getSession()->getPage()->find('xpath', '//div[@id=\'wrs_modal_dialogContainer[0]\' and
                @class=\'wrs_modal_dialogContainer wrs_modal_desktop wrs_stack\']//span[@class=\'wrs_container\']');
                $button = $context->getSession()->getPage()->find('xpath', '//button[@id=\'wrs_modal_button_accept[0]\']');
                return !empty($toolbar) and !empty($container);
            },
            array(),
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
            function($context) {
                $toolbar = $context->getSession()->getPage()->find('xpath', '//div[@id=\'wrs_modal_dialogContainer[0]\' and
                @class=\'wrs_modal_dialogContainer wrs_modal_desktop wrs_stack\']//div[@class=\'wrs_panelContainer\']');
                $container = $context->getSession()->getPage()->find('xpath', '//div[@id=\'wrs_modal_dialogContainer[0]\' and
                @class=\'wrs_modal_dialogContainer wrs_modal_desktop wrs_stack\']//span[@class=\'wrs_container\']');
                $button = $context->getSession()->getPage()->find('xpath', '//button[@id=\'wrs_modal_button_accept[0]\']');
                return !empty($toolbar) and !empty($container);
            },
            array(),
            self::get_extended_timeout(),
            $exception,
            true
        );
        $session = $this->getSession();
        $component = $session->getPage()->find('xpath', '//button[@id=\'wrs_modal_button_accept[0]\']');
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

}
