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
 * Moodle editor field.
 *
 * @package    core_form
 * @category   test
 * @copyright  2012 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

use Behat\Mink\Element\NodeElement as NodeElement;

require_once(__DIR__ . '/behat_form_textarea.php');

/**
 * Moodle editor field.
 *
 * @package   core_form
 * @category  test
 * @copyright 2012 David Monllaó
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_form_editor extends behat_form_textarea {

    /**
     * Sets the value to a field.
     *
     * @param string $value
     */
    public function set_value($value): void {
        $editorid = $this->field->getAttribute('id');
        if ($this->running_javascript()) {
            $value = addslashes($value);
            // This will be transported in JSON, which doesn't allow newlines in strings, so we must escape them.
            $value = str_replace("\n", "\\n", $value);
            behat_base::execute_in_matching_contexts('editor', 'set_editor_value', [
                $editorid,
                $value,
            ]);

        } else {
            parent::set_value($value);
        }
    }

    /**
     * Returns the current value of the select element.
     *
     * @return string
     */
    public function get_value(): string {
        if ($this->running_javascript()) {
            // Give any listening editors a chance to persist the value to the textarea.
            // Some editors only do this on form submission or similar events.
            behat_base::execute_in_matching_contexts('editor', 'store_current_value', [
                $this->field->getAttribute('id'),
            ]);
        }

        return parent::get_value();
    }

    /**
     * Select all the text in the form field.
     *
     */
    public function select_text() {
        // NodeElement.keyPress simply doesn't work.
        if (!$this->running_javascript()) {
            throw new coding_exception('Selecting text requires javascript.');
        }

        $editorid = $this->field->getAttribute('id');
        $js = ' (function() {
    var e = document.getElementById("'.$editorid.'editable"),
        r = rangy.createRange(),
        s = rangy.getSelection();

    while ((e.firstChild !== null) && (e.firstChild.nodeType != document.TEXT_NODE)) {
        e = e.firstChild;
    }
    e.focus();
    r.selectNodeContents(e);
    s.setSingleRange(r);
}()); ';
        behat_base::execute_script_in_session($this->session, $js);
    }

    /**
     * Matches the provided value against the current field value.
     *
     * @param string $expectedvalue
     * @return bool The provided value matches the field value?
     */
    public function matches($expectedvalue) {
        // Fetch the actual value to save fetching it multiple times.
        $actualvalue = $this->get_value();

        if ($this->text_matches($expectedvalue, $actualvalue)) {
            // The text is an exact match already.
            return true;
        }

        if ($this->text_matches("<p>{$expectedvalue}</p>", $actualvalue)) {
            // A text editor may silently wrap the content in p tags.
            return true;
        }

        // Standardise both the expected value and the actual field value.
        // We are likely dealing with HTML content, given this is an editor.
        $expectedvalue = $this->standardise_html($expectedvalue);
        $actualvalue = $this->standardise_html($actualvalue);

        // Note: We don't need to worry about the floats here that we care about in text_matches.
        // That condition isn't relevant to the content of an editor.
        if ($expectedvalue === $actualvalue) {
            return true;
        }

        return false;
    }

    /**
     * Standardises the HTML content for comparison.
     *
     * @param string $html The HTML content to standardise
     * @return string The standardised HTML content
     */
    protected function standardise_html(string $html): string {
        $document = new DOMDocument();
        $errorstate = libxml_use_internal_errors(true);

        // Format the whitespace nicely.
        $document->preserveWhiteSpace = false;
        $document->formatOutput = true;

        // Wrap the content in a DIV element so that it is not parsed weirdly.
        // Note: We must remove newlines too because DOMDocument does not do so, despite preserveWhiteSpace being false.
        // Unfortunately this is slightly limited in that it will also remove newlines from <pre> content and similar.
        $document->loadHTML(str_replace("\n", "", "<div>{$html}</div>"), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        $document->normalizeDocument();
        libxml_clear_errors();
        libxml_use_internal_errors($errorstate);

        // Save the content of the 'div' element, removing the <div> and </div> tags at the start and end.
        return trim(substr(
            $document->saveHTML($document->getElementsByTagName('div')->item(0)),
            5,
            -6
        ));
    }
}
