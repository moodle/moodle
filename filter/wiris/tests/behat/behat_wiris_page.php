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

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/behat_wiris_base.php');

use Behat\Mink\Exception\ExpectationException;

/**
 * Methods related to the interaction with the MathType.
 * @package    filter_wiris
 * @subpackage wiris
 * @copyright  WIRIS Europe (Maths for more S.L)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_wiris_page extends behat_wiris_base {
    /**
     * Check the size of the formula in full screen mode
     *
     * @Given I press :button in full screen mode
     * @param  string $button button to press
     * @throws ExpectationException If the button is not found, it will throw an exception.
     */
    public function i_press_mathtype_in_full_screen_mode($button) {
        $session = $this->getSession();
        $buttonarray = [
            "MathType" => "mce_fullscreen_tiny_mce_wiris_formulaEditor",
            "ChemType" => "mce_fullscreen_tiny_mce_wiris_formulaEditorChemistry",
            "Full screen" => "mce_fullscreen_fullscreen",
        ];
        if (empty($buttonarray[$button])) {
            throw new ExpectationException($button . " button not registered.", $this->getSession());
        }
        $component = $session->getPage()->find('xpath', '//*[@id="' . $buttonarray[$button] . '"]');
        if (empty($component)) {
            throw new ExpectationException('"' . $button . '" button not found in full screen mode', $this->getSession());
        }
        $component->click();
    }

    /**
     * Check input element 'value' equals an expected result
     *
     * @Then :field input value is equal to :value
     * @param  field input element which value is wanted
     * @param  value value expected to receive
     * @throws ExpectationException If the element's true 'value' is not equal to the expected one, it will throw an exception.
     */
    public function input_value_is_equal_to($field, $value) {
        $fieldarray = [
            "Service host" => "id_s_filter_wiris_imageservicehost",
        ];
        $valuearray = [
            "wiris.net" => "www.wiris.net",
            "wipis.net" => "www.wipis.net",
        ];
        if (empty($fieldarray[$field])) {
            throw new ExpectationException($field . " field not registered.", $this->getSession());
        }
        $session = $this->getSession();
        $component = $session->getPage()->find('xpath', '//input[@id="' . $fieldarray[$field] . '" and @value="' . $valuearray[$value] . '"]');
        if (empty($component)) {
            throw new ExpectationException('"' . $field . '" input value attribute is not equal to "' . $valuearray[$value] . '"', $this->getSession());
        }
    }

    /**
     * Click on a certain field
     *
     * @Given I click on :field field
     * @param  string $field field to click on
     * @throws ExpectationException If the field is not found, it will throw an exception.
     */
    public function i_click_on_field($field) {
        $fieldarray = [
            "Page content" => "id_pageeditable",
            "Question text" => "id_questiontexteditable",
            "General feedback" => "id_generalfeedbackeditable",
            "Feedback" => "id_feedback_0editable",
        ];
        if (empty($fieldarray[$field])) {
            throw new ExpectationException($field . " field not registered.");
        }
        $session = $this->getSession();
        $component = $session->getPage()->find('xpath', '//div[@id="' . $fieldarray[$field] . '"]');
        if (empty($component)) {
            throw new ExpectationException($field . " field not correctly recognized.", $this->getSession());
        }
        $component->click();
    }

    /**
     * Click on a certain field in TinyMCE 6
     *
     * @Given I click on :field field in TinyMCE 6
     * @param  string $field field to click on
     * @throws ExpectationException If the field is not found, it will throw an exception.
     */
    public function i_click_on_field_in_tinymce_6($field) {
        $fieldarray = [
            "Page content" => "id_page",
        ];
        if (empty($fieldarray[$field])) {
            throw new ExpectationException($field . " field not registered.");
        }
        $session = $this->getSession();
        $component = $session->getPage()->find('xpath', '//body[@data-id="' . $fieldarray[$field] . '"]');
        if (empty($component)) {
            throw new ExpectationException($field . " field not correctly recognized.", $this->getSession());
        }
        $component->click();
    }

    /**
     * DbClick on a certain image with specific alternative text.
     *
     * @Given I dbClick on image with alt equals to :alt
     * @param  string $alt image alternative text
     * @throws ExpectationException If the image is not found, it will throw an exception.
     */
    public function i_dbclick_on_image_with_alt_text($alt) {
        $session = $this->getSession();
        $component = $session->getPage()->find('xpath', '//img[contains(@alt, "' . $alt . '")]');
        if (empty($component)) {
            throw new ExpectationException("Image with alternative text" .
                $alt . " is not correctly recognized.", $this->getSession());
        }
        $component->doubleClick();
    }

    /**
     * Follows the page redirection. Use this step after any action that shows a message and waits for a redirection
     *
     * @Then modal window is opened
     * @param  string $seconds time to wait
     */
    public function modal_window_is_opened() {
        $session = $this->getSession();
        $component = $session->getPage()->find('xpath', '//div[contains(@class, "wrs_modal_dialogContainer")]');
        if (empty($component) || !$component->isVisible()) {
            throw new ExpectationException("Modal window is not opened.", $this->getSession());
        }
    }

    /**
     * Place caret in a certain position in a certain field
     *
     * @Given I place caret at position :position in :field field
     * @param  integer $position position to which the caret is placed
     * @param  string $field field to check
     * @throws ExpectationException If the field is not found, it will throw an exception.
     */
    public function i_place_caret_at_position_in_field($position, $field) {
        $fieldarray = [
            "Page content" => "id_pageeditable",
            "Question text" => "id_questiontexteditable",
            "General feedback" => "id_generalfeedbackeditable",
            "Feedback" => "id_feedback_0editable",
        ];
        if (empty($fieldarray[$field])) {
            throw new ExpectationException($field . " field not registered.", $this->getSession());
        }
        $session = $this->getSession();
        $component = $session->getPage()->find('xpath', '//div[@id="' . $fieldarray[$field] . '"]');
        if (empty($component)) {
            throw new ExpectationException($field . " field not correctly recognized.", $this->getSession());
        }
        $session = $this->getSession();
        $script = 'range = window.parent.document.getSelection().getRangeAt(0);'
            . 'node = document.getElementById(\'' . $fieldarray[$field] . '\').firstChild;'
            . 'window.parent.document.getSelection().removeAllRanges();'
            . 'range.setStart(node,' . $position . ');'
            . 'range.setEnd(node,' . $position . ');'
            . 'window.parent.document.getSelection().addRange(range);'
            . 'window.parent.document.body.focus();';
        $session->executeScript($script);
    }

    /**
     * Place caret in a certain position in a certain field
     *
     * @Given I place caret at position :position in :field field in TinyMCE 6
     * @param  integer $position position to which the caret is placed
     * @param  string $field field to check
     * @throws ExpectationException If the field is not found, it will throw an exception.
     */
    public function i_place_caret_at_position_in_field_in_tinymce_6($position, $field) {
        $fieldarray = [
            "Page Content" => "id_page",
        ];
        $session = $this->getSession();

        $script = 'var editor = tinymce.get(\'' . $fieldarray[$field] . '\');'
            . 'editor.focus();'
            . 'var body = editor.getBody();'
            . 'var textNode = body.querySelector(\'p\').firstChild;'
            . 'var offset = ' . $position . ';'
            . 'editor.selection.setCursorLocation(textNode, offset);';

        $session->executeScript($script);
    }

    /**
     * Press certain button in certain field in Atto
     *
     * @Given I press :button in :field field in Atto editor
     * @param  string $button button to press
     * @param  string $field field to check
     * @throws ExpectationException If the field is not found, it will throw an exception.
     */
    public function i_press_in_field_in_atto_editor($button, $field) {
        global $CFG;

        $sectionarray = [
            "Page content" => "fitem_id_page",
            "Question text" => "fitem_id_questiontext",
            "General feedback" => "fitem_id_generalfeedback",
            "Feedback" => "fitem_id_feedback_0",
            "Description" => "fitem_id_introeditor",
            "Message" => "fitem_id_message",
        ];
        if (empty($sectionarray[$field])) {
            throw new ExpectationException($field . " field not registered.", $this->getSession());
        }
        $buttonarray = [
            "MathType" => "atto_wiris_button_wiris_editor",
            "ChemType" => "atto_wiris_button_wiris_chem_editor",
            "Collapse" => "atto_collapse_button",
            "HTML" => "atto_html_button",
            "HTML pressed" => "atto_html_button",
        ];
        if (empty($buttonarray[$button])) {
            throw new ExpectationException($button . " button not registered.", $this->getSession());
        }

        if ($CFG->version >= 2018051700 && $CFG->version < 2018120300) {
            $buttonarray["HTML pressed"] = "atto_html_button highlight";
        }

        $session = $this->getSession();
        $component = $session->getPage()->find('xpath', '//div[@id="' . $sectionarray[$field] . '"]
        //button[@class="' . $buttonarray[$button] . '"]');
        if (empty($component)) {
            throw new ExpectationException('"' . $button . '" button not found in "' . $field . '" field', $this->getSession());
        }
        $component->click();
    }

    /**
     * Press certain button in certain field in Tiny
     *
     * @Given I press :button in :field field in TinyMCE editor
     * @param  string $button button to press
     * @param  string $field field to check
     * @throws ExpectationException If the field is not found, it will throw an exception.
     */
    public function i_press_in_field_in_tinymce_editor($button, $field) {
        $sectionarray = [
            "Page content" => "fitem_id_page",
            "Question text" => "fitem_id_questiontext",
            "General feedback" => "fitem_id_generalfeedback",
            "Feedback" => "fitem_id_feedback_0",
        ];
        if (empty($sectionarray[$field])) {
            throw new ExpectationException($field . " field not registered.", $this->getSession());
        }
        $buttonarray = [
            "MathType" => "Insert a math equation - MathType",
            "ChemType" => "Insert a chemistry formula - ChemType",
            "Toggle" => "pdw_toggle",
            "Full screen" => "fullscreen",
        ];
        if (empty($buttonarray[$button])) {
            throw new ExpectationException($button . " button not registered.", $this->getSession());
        }
        $session = $this->getSession();
        $component = $session->getPage()->find('xpath', '//div[@id="' . $sectionarray[$field] . '"]
        //*[contains(@title,\'' . $buttonarray[$button] . '\')]');
        if (empty($component)) {
            throw new ExpectationException('"' . $button . '" button not found in "' . $field . '" field', $this->getSession());
        }
        if ($button == 'Toggle') {
            // Clicking only if toggle button is not pressed yet.
            $component = $session->getPage()->find('xpath', '//div[@id="' . $sectionarray[$field] . '"]
            //*[contains(@class,\'mceButtonActive\')]');
            if (!empty($component)) {
                $component->click();
            }
        } else {
            $component->click();
        }
    }

    /**
     * Press certain button in certain field in TinyMCE 6
     *
     * @Given I press :button in :field field in TinyMCE 6 editor
     * @param  string $button button to press
     * @param  string $field field to check
     * @throws ExpectationException If the field is not found, it will throw an exception.
     */
    public function i_press_in_field_in_tiny_editor($button, $field) {
        global $CFG;
        $sectionarray = [
            "Page content" => "fitem_id_page",
            "Question text" => "fitem_id_questiontext",
            "General feedback" => "fitem_id_generalfeedback",
            "Feedback" => "fitem_id_feedback_0",
            "Message" => "fitem_id_message",
        ];
        if (empty($sectionarray[$field])) {
            throw new ExpectationException($field . " field not registered.", $this->getSession());
        }
        $buttonarray = [
            "MathType" => "MathType",
            "ChemType" => "ChemType",
            "Toggle" => "More...",
        ];

        if (empty($buttonarray[$button])) {
            throw new ExpectationException($button . " button not registered.", $this->getSession());
        }
        $session = $this->getSession();
        $component = $session->getPage()->find('xpath', '//div[@id="' . $sectionarray[$field] . '"]
        //*[contains(@title,\'' . $buttonarray[$button] . '\')]');

        // In Moodle 4.4 the button has change from "More.." to "Reveal or hide..."
        if ($button == 'Toggle' && $CFG->version >= 2024042202.02) {
            $component = $session->getPage()->find('xpath', '//div[@id="' . $sectionarray[$field] . '"]
            //*[contains(@aria-label,"Reveal or hide")]');
        }

        // From 4.5, the button is not found via id but via data-mce-name //TODO verify if 4.x may always work like this
        if ($button != 'Toggle' && $CFG->version >= 2024092400) {
            $component = $session->getPage()->find('xpath', '//div[@id="' . $sectionarray[$field] . '"]
            //*[contains(@aria-label,"' . $buttonarray[$button] . '")]');
        }

        if (empty($component)) {
            throw new ExpectationException('"' . $button . '" button not found in "' . $field . '" field', $this->getSession());
        }
        $component->click();
    }

    /**
     * Enables saveMode to XML
     *
     * @Given I enable saveMode
     */
    public function i_enable_save_mode() {
        $script = 'WirisPlugin.Configuration.set("saveMode", "xml")';
        $this->getSession()->executeScript($script);
    }

    /**
     * Follow a specific url
     *
     * @Given I go to link :url
     */
    public function i_go_to_link($url) {
        $this->getSession()->visit($this->locate_path($url));
    }


    /**
     * Check if MathType formula has certain value for the src property
     *
     * @Given I check if MathType formula src is equals to :link
     */
    public function i_check_if_mathtype_formula_src_is_equals_to($link) {
        $session = $this->getSession();
        $script = 'return document.getElementsByClassName(\'Wirisformula\')[0].src == \'' . $link . '\'';
        $session->evaluateScript($script);
    }

    /**
     * Go back on the browser
     *
     * @Given I go back
     */
    public function i_go_back() {
        $this->getSession()->back();
    }

    /**
     * Svg element is correctly displayed in the current page
     *
     * @Then an svg image is correctly displayed
     */
    public function an_svg_image_is_correclty_displayed() {
        // We do not use xpath because in this page the svg element acts as root node instead of being an element inside an html.
        $script = 'return document.children[0].nodeName';
        $node = $this->getSession()->evaluateScript($script);
        return $node == 'svg';
    }

    /**
     * Png element is correctly displayed in the current page
     *
     * @Then an png image is correctly displayed
     * @throws ExpectationException If the png image is not found, it will throw an exception.
     */
    public function an_png_image_is_correctly_displayed() {
        $session = $this->getSession();
        $image = $session->getPage()->find('xpath', '//img');
        if (empty($image)) {
            throw new ExpectationException('Image not found.', $this->getSession());
        }
    }

    /**
     * MathType images are correctly displayed when the chosen format is svg
     *
     * @Given MathType formula in svg format is correctly displayed
     * @throws ExpectationException If the MathType formula is not found, it will throw an exception.
     */
    public function mathtype_image_in_svg_format_is_correctly_displayed() {
        $session = $this->getSession();
        $image = $session->getPage()->find('xpath', '//img[contains(@src,\'data:image/svg+xml\')]');
        if (empty($image)) {
            throw new ExpectationException('MathType formula not found.', $this->getSession());
        }
    }

    /**
     * MathType images are correctly displayed when the chosen format is png
     *
     * @Given MathType formula in png format is correctly displayed
     * @throws ExpectationException If the MathType formula is not found, it will throw an exception.
     */
    public function mathtype_image_in_png_format_is_correctly_displayed() {
        $session = $this->getSession();
        $image = $session->getPage()->find('xpath', '//img[contains(@src,\'data:image/png;\')]');
        if (empty($image)) {
            throw new ExpectationException('MathType formula not found.', $this->getSession());
        }
    }

    /**
     * Select language option as spanish
     *
     * @Given I select spanish
     * @throws ExpectationException If spanish option is not found, it will throw an exception.
     */
    public function i_select_spanish() {
        $session = $this->getSession();
        $component = $session->getPage()->find('xpath', '//select');
        if (empty($component)) {
            throw new ExpectationException('Spanish option not found.', $this->getSession());
        }
        $component->selectOption("EspaÃ±ol - Internacional â€Ž(es)â€Ž");
    }

    /**
     * Select 100% in grade option of Answer 1 field
     *
     * @Given I select 100% option in Answer1
     * @throws ExpectationException If grade option is not found, it will throw an exception.
     */
    public function i_select_100_option_in_answer1() {
        $session = $this->getSession();
        $component = $session->getPage()->find('xpath', '//select[@id="id_fraction_0"]');
        if (empty($component)) {
            throw new ExpectationException('Grade option in Answer 1 field not found.', $this->getSession());
        }
        $component->selectOption("100%");
    }

    /**
     * Check enable trusted content on site security settings page
     *
     * @Given I check enable trusted content
     * @throws ExpectationException If enable trusted content checkbox is not found, it will throw an exception.
     */
    public function i_check_enable_trusted_content() {
        $session = $this->getSession();
        $component = $session->getPage()->find('xpath', '//input[@id="id_s__enabletrusttext"]');
        if (empty($component)) {
            throw new ExpectationException('Enable trusted content checkbox not found.', $this->getSession());
        }
        $component->check();
    }

    /**
     * Select seconds in autosave frequency option on Atto toolbar settings page
     *
     * @Given I select seconds in autosave frequency option
     * @throws ExpectationException If autosave frequency option is not fonud, it will throw an exception.
     */
    public function i_select_seconds_in_autosave_frequency_option() {
        $session = $this->getSession();
        $component = $session->getPage()->find('xpath', '//select[@id="id_s_editor_atto_autosavefrequencyu"]');
        if (empty($component)) {
            throw new ExpectationException('Autosave frequency option in Answer 1 field not found.', $this->getSession());
        }
        $component->selectOption("seconds");
    }

    /**
     * Choose Short answoer in Choose a questoin type to add dialog
     *
     * @Given I choose Short answer
     * @throws ExpectationException If Short answer radio button is not found, it will throw an exception.
     */
    public function i_choose_short_answer() {
        $session = $this->getSession();
        $component = $session->getPage()->find('xpath', '//input[@id="item_qtype_shortanswer"]');
        if (empty($component)) {
            throw new ExpectationException('Short answer radio button in Answer 1 field not found.', $this->getSession());
        }
        $component->click();
    }

    /**
     * Checks the existance or non existance of a certain button in certain field in Atto editor
     *
     * @Given I check :button in :field field :exist exist in Atto editor
     * @param  string $button button to check
     * @param  string $field field to check
     * @param  string $exist existance or not existance. Values: does| does not
     * @throws ExpectationException If the field is not found, it will throw an exception.
     */
    public function i_check_in_field_in_atto_editor($button, $field, $exist) {
        global $CFG;

        $sectionarray = [
            "Page content" => "fitem_id_page",
        ];
        if (empty($sectionarray[$field])) {
            throw new ExpectationException($field . " field not registered.", $this->getSession());
        }
        $buttonarray = [
            "MathType" => "atto_wiris_button_wiris_editor",
            "ChemType" => "atto_wiris_button_wiris_chem_editor",
        ];
        if (empty($buttonarray[$button])) {
            throw new ExpectationException($button . " button not registered.", $this->getSession());
        }

        $session = $this->getSession();
        $component = $session->getPage()->find('xpath', '//div[@id="' . $sectionarray[$field] . '"]
        //button[@class="' . $buttonarray[$button] . '"]');
        if ($exist === "does" && empty($component)) {
            throw new ExpectationException('"' . $button . '" button not found in "' . $field . '" field', $this->getSession());
        } else if ($exist === "does not" && !empty($component)) {
            throw new ExpectationException('"' . $button . '" button found in "' . $field . '" field', $this->getSession());
        }
    }

    /**
     * Checks the existance or non existance
     * of a certain button in certain field in Atto editor
     *
     * @Given I check :button in :field field :exist exist in TinyMCE editor
     * @param  string $button button to press
     * @param  string $field field to check
     * @param  string $exist existance or not existance. Values: does|does not
     * @throws ExpectationException If the field is not found, it will throw an exception.
     */
    public function i_check_in_field_in_tinymce_editor($button, $field, $exist) {
        $sectionarray = [
            "Page content" => "fitem_id_page",
        ];
        if (empty($sectionarray[$field])) {
            throw new ExpectationException($field . " field not registered.", $this->getSession());
        }
        $buttonarray = [
            "MathType" => "Insert a math equation - MathType",
            "ChemType" => "Insert a chemistry formula - ChemType",
        ];
        if (empty($buttonarray[$button])) {
            throw new ExpectationException($button . " button not registered.", $this->getSession());
        }
        $session = $this->getSession();
        $component = $session->getPage()->find('xpath', '//button[@title="' . $buttonarray[$button] . '"]');
        // From 4.5, the button is not found via id but via data-mce-name //TODO verify if 4.x may always work like this
        global $CFG;
        if ($button != 'Toggle' && $CFG->version >= 2024092400) {
            $component = $session->getPage()->find('xpath', '//div[@id="' . $sectionarray[$field] . '"]
            //*[contains(@aria-label,"' . $button . '")]');
        }

        if ($exist === "does" && empty($component)) {
            throw new ExpectationException('"' . $button . '" button not found in "' . $field . '" field', $this->getSession());
        } else if ($exist === "does not" && $component === '') {
            echo "a is " . $component . "<br>";
            throw new ExpectationException('"' . $button . '" button found in "' . $field . '" field', $this->getSession());
        }
    }

    /**
     * Click on a certain field in TinyMCE 6
     *
     * @Given I click on :button in TinyMCE 6 editor toolbar
     * @param  string $button button to click on
     * @throws ExpectationException If the button is not found, it will throw an exception.
     */
    public function i_click_on_in_tinymce_6_editor_toolbar($button) {
        global $CFG;
        $buttonarray = [
            "More options" => "More...",
        ];
        if (empty($buttonarray[$button])) {
            throw new ExpectationException($button . " button not registered.");
        }
        $session = $this->getSession();
        $component = $session->getPage()->find('xpath', '//button[@title="' . $buttonarray[$button] . '"]');
        if ($CFG->version >= 2024042202.02) {
            $component = $session->getPage()->find('xpath', '//*[contains(@aria-label,"Reveal or hide")]');
        }

        if (empty($component)) {
            throw new ExpectationException($button . " button not correctly recognized.", $this->getSession());
        }
        $component->click();
    }
}
