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
            throw new coding_exception('Setting the value of an autocomplete field requires javascript.');
        }

        // Clear all current selections.
        $rootnode = $this->field->getParent()->getParent();
        $selections = $rootnode->findAll('css', '.form-autocomplete-selection > [role=listitem]');
        foreach (array_reverse($selections) as $selection) {
            $selection->click();
            $this->wait_for_pending_js();
        }

        $allowscreation = $this->field->hasAttribute('data-tags') && !empty($this->field->getAttribute('data-tags'));
        $hasmultiple = $this->field->hasAttribute('data-multiple') && !empty($this->field->getAttribute('data-multiple'));

        if ($hasmultiple && false !== strpos($value, ',')) {
            // Commas have a special meaning as a value separator in 'multiple' autocomplete elements.
            // To handle this we break the value up by comma, and enter it in chunks.
            $values = explode(',', $value);

            while ($value = array_shift($values)) {
                $this->add_value(trim($value), $allowscreation);
            }
        } else {
            $this->add_value(trim($value), $allowscreation);
        }
    }

    /**
     * Add a value to the autocomplete.
     *
     * @param   string $value
     * @param   bool $allowscreation
     */
    protected function add_value(string $value, bool $allowscreation): void {
        $value = trim($value);

        // Click into the field.
        $this->field->click();

        // Remove any existing text.
        do {
            behat_base::type_keys($this->session, [behat_keys::BACKSPACE, behat_keys::DELETE]);
        } while (strlen($this->field->getValue()) > 0);
        $this->wait_for_pending_js();

        // Type in the new value.
        behat_base::type_keys($this->session, str_split($value));
        $this->wait_for_pending_js();

        // If the autocomplete found suggestions, then it will have:
        // 1) marked itself as expanded; and
        // 2) have an aria-selected suggestion in the list.
        $expanded = $this->field->getAttribute('aria-expanded');
        $suggestion = $this->field->getParent()->getParent()->find('css', '.form-autocomplete-suggestions > [aria-selected="true"]');

        if ($expanded && null !== $suggestion) {
            // A suggestion was found.
            // Click on the first item in the list.
            $suggestion->click();
        } else if ($allowscreation) {
            // Press the return key to create a new entry.
            behat_base::type_keys($this->session, [behat_keys::ENTER]);
        } else {
            throw new \InvalidArgumentException(
                "Unable to find '{$value}' in the list of options, and unable to create a new option"
            );
        }

        $this->wait_for_pending_js();

        // Press the escape to close the autocomplete suggestions list.
        behat_base::type_keys($this->session, [behat_keys::ESCAPE]);
        $this->wait_for_pending_js();
    }
}
