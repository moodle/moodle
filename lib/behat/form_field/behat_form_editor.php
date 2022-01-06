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
 * @todo Support for multiple editors
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
     * @return void
     */
    public function set_value($value) {

        $editorid = $this->field->getAttribute('id');
        if ($this->running_javascript()) {
            $value = addslashes($value);
            // This will be transported in JSON, which doesn't allow newlines in strings, so we must escape them.
            $value = str_replace("\n", "\\n", $value);
            $js = '
(function() {
    var editor = Y.one(document.getElementById("'.$editorid.'editable"));
    if (editor) {
        editor.setHTML("' . $value . '");
    }
    editor = Y.one(document.getElementById("'.$editorid.'"));
    editor.set("value", "' . $value . '");
})();
';
            behat_base::execute_script_in_session($this->session, $js);
        } else {
            parent::set_value($value);
        }
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
        // A text editor may silently wrap the content in p tags (or not). Neither is an error.
        return $this->text_matches($expectedvalue) || $this->text_matches('<p>' . $expectedvalue . '</p>');
    }
}

