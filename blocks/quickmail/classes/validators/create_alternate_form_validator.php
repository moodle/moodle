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

namespace block_quickmail\validators;

defined('MOODLE_INTERNAL') || die();

use block_quickmail\validators\validator;
use block_quickmail_string;

class create_alternate_form_validator extends validator {

    /**
     * Defines this specific validator's validation rules
     *
     * @return void
     */
    public function validator_rules() {
        $this->validate_email();

        $this->validate_firstname();

        $this->validate_lastname();

        $this->validate_availability();
    }

    /**
     * Checks that the data has a valid email, adding any errors to the stack
     *
     * @return void
     */
    private function validate_email() {
        if ($this->is_missing('email')) {
            $this->add_error(block_quickmail_string::get('missing_email'));
        }

        if (filter_var($this->form_data->email, FILTER_VALIDATE_EMAIL) == false) {
            $this->add_error(block_quickmail_string::get('invalid_email'));
        }
    }

    /**
     * Checks that the data has a valid firstname, adding any errors to the stack
     *
     * @return void
     */
    private function validate_firstname() {
        if ($this->is_missing('firstname')) {
            $this->add_error(block_quickmail_string::get('missing_firstname'));
        }
    }

    /**
     * Checks that the data has a valid lastname, adding any errors to the stack
     *
     * @return void
     */
    private function validate_lastname() {
        if ($this->is_missing('lastname')) {
            $this->add_error(block_quickmail_string::get('missing_lastname'));
        }
    }

    /**
     * Checks that the data has a valid availability, adding any errors to the stack
     *
     * @return void
     */
    private function validate_availability() {
        if (!in_array($this->form_data->availability, [
            'only',
            'user',
            'course'
        ])) {
            $this->add_error(block_quickmail_string::get('invalid_availability'));
        }
    }

}
