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
 * Silly behat_form_select extension.
 *
 * @package    core_form
 * @category   test
 * @copyright  2013 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__  . '/behat_form_text.php');

/**
 * Allows interaction with passwordunmask form fields.
 *
 * Plain behat_form_select extension as it is the same
 * kind of field.
 *
 * @package    core_form
 * @category   test
 * @copyright  2013 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_form_passwordunmask extends behat_form_text {
    /**
     * Sets the value to a field.
     *
     * @param string $value
     * @return void
     */
    public function set_value($value) {
        if ($this->running_javascript()) {
            $id = $this->field->getAttribute('id');
            $js = <<<JS
(function() {
    require(["jquery"], function($) {
        var wrapper = $(document.getElementById("{$id}")).closest('[data-passwordunmask="wrapper"]');
            wrapper.find('[data-passwordunmask="edit"]').trigger("click");
    });
})();
JS;
            behat_base::execute_script_in_session($this->session, $js);
        }

        $this->field->setValue($value);

        // Ensure all pending JS is finished.
        if ($this->running_javascript()) {
            // Press enter key after setting password, so we have a stable page.
            $this->field->keyDown(13);
            $this->field->keyPress(13);
            $this->field->keyUp(13);
            $this->session->wait(behat_base::get_timeout() * 1000, behat_base::PAGE_READY_JS);
        }
    }
}
