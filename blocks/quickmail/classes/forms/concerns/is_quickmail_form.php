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
 * @package    block_quickmail
 * @copyright  2008 onwards Louisiana State University
 * @copyright  2008 onwards Chad Mazilly, Robert Russo, Jason Peak, Dave Elliott, Adam Zapletal, Philip Cali
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_quickmail\forms\concerns;

defined('MOODLE_INTERNAL') || die();

trait is_quickmail_form {

    /**
     * Returns a constructed query string including the given parameters
     *
     * @param  array  $params
     * @return string
     */
    public static function generate_target_url($params = []) {
        $target = '?' . http_build_query($params, '', '&');

        return $target;
    }

    // Error Handling / Rendering.
    /**
     * Sets errors on this form's error stack received from the given exception
     *
     * @param array $errors
     * @return void
     */
    public function set_error_exception($exception) {
        // If no errors set yet, create stack container.
        $this->errors = ! is_null($this->errors) ? $this->errors : [];

        // Handle persistent exceptions.
        if (get_class($exception) == 'core\invalid_persistent_exception') {
            $this->errors[] = $exception->a;
        } else if (get_class($exception) == 'block_quickmail\exceptions\validation_exception') {
            // If the "errors" is not an array, make it an array.
            if (!is_array($exception->errors)) {
                $this->errors = array_merge($this->errors, [$exception->errors]);
            } else {
                $this->errors = array_merge($this->errors, $exception->errors);
            }
        }
    }

    /**
     * Renders a moodle error notification if there are any errors
     *
     * @return string
     */
    public function render_error_notification() {
        if (!empty($this->errors) && count($this->errors)) {
            $html = '<ul style="margin-bottom: 0px;">';

            foreach ($this->errors as $error) {
                $html .= '<li>' . $error . '</li>';
            }

            $html .= '</ul>';

            \core\notification::error($html);
        }
    }

}
