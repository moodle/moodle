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

require_once(__DIR__ . '/behat_form_field.php');

/**
 * Moodle editor field.
 *
 * @todo Support for multiple editors
 * @package   core_form
 * @category  test
 * @copyright 2012 David Monllaó
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_form_editor extends behat_form_field {

    /**
     * Sets the value to a field.
     *
     * @param string $value
     * @return void
     */
    public function set_value($value) {

        // If tinyMCE var exists means that we are using that editor.
        if ($this->is_editor_available()) {

            // Set the value to the iframe and save it to the textarea.
            $editorid = $this->field->getAttribute('id');

            $this->session->executeScript('
                tinyMCE.get("'.$editorid.'").setContent("' . $value . '");
                tinyMCE.get("'.$editorid.'").save();
            ');

        } else {
            // Set the value to a textarea otherwise.
            parent::set_value($value);
        }
    }

    /**
     * Returns the editor value.
     *
     * @return string
     */
    public function get_value() {

        // If tinyMCE var exists means that we are using that editor.
        if ($this->is_editor_available()) {

            // Save the current iframe value in case default value has been edited.
            $editorid = $this->field->getAttribute('id');
            $this->session->executeScript('tinyMCE.get("'.$editorid.'").save();');
        }

        return $this->field->getValue();
    }

    /**
     * Returns if the HTML editor is available.
     *
     * The editor availability depends on the driver running the tests; Goutte
     * can not execute Javascript, also some Moodle settings disables the HTML
     * editor.
     *
     * @return bool
     */
    protected function is_editor_available() {

        // Non-JS drivers throws exceptions when running JS.
        try {
            $available = $this->session->evaluateScript('return (typeof tinyMCE != "undefined")');
        } catch (Exception $e) {
            $available = false;
        }

        return $available;
    }
}

