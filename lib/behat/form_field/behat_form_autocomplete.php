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
 * Auto complete form field class.
 *
 * @package    core_form
 * @category   test
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__  . '/behat_form_text.php');

/**
 * Auto complete form field.
 *
 * @package    core_form
 * @category   test
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_form_autocomplete extends behat_form_text {

    /**
     * Sets the value to a field.
     *
     * @param string $value
     * @return void
     */
    public function set_value($value) {
        if (!$this->running_javascript()) {
            throw new coding_exception('Setting the valid of an autocomplete field requires javascript.');
        }

        // Set the value of the autocomplete's input.
        // If this autocomplete offers suggestions then these should be fetched by setting the value and waiting for the
        // JS to finish fetching those suggestions.

        $istagelement = $this->field->hasAttribute('data-tags') && $this->field->getAttribute('data-tags');

        if ($istagelement && false !== strpos($value, ',')) {
            // Commas have a special meaning as a value separator in 'tag' autocomplete elements.
            // To handle this we break the value up by comma, and enter it in chunks.
            $values = explode(',', $value);

            while ($value = array_shift($values)) {
                $this->set_value($value);
            }
        } else {
            $this->field->setValue($value);
            $this->wait_for_pending_js();

            // If the autocomplete found suggestions, then it will have:
            // 1) marked itself as expanded; and
            // 2) have an aria-selected suggestion in the list.
            $expanded = $this->field->getAttribute('aria-expanded');
            $suggestion = $this->field->getParent()->find('css', '.form-autocomplete-suggestions > [aria-selected="true"]');

            if ($expanded && null !== $suggestion) {
                // A suggestion was found.
                // Click on the first item in the list.
                $suggestion->click();
            } else {
                // Press the return key to create a new tag.
                // Note: We cannot use $this->key_press() because the keyPress action, in combination with the keyDown
                // submits the form.
                $this->field->keyDown(13);
                $this->field->keyUp(13);
            }

            $this->wait_for_pending_js();
            $this->key_press(27);
        }
    }
}
