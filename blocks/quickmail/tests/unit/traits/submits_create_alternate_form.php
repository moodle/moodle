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

defined('MOODLE_INTERNAL') || die();

// Create alternate form submission helpers.
trait submits_create_alternate_form {

    public function get_create_alternate_form_submission(array $overrideparams = []) {
        $params = $this->get_create_alternate_form_submission_params($overrideparams);

        $formdata = (object)[];

        $formdata->email = $params['email']; // Default: different@email.com.
        $formdata->firstname = $params['firstname']; // Default: Firsty.
        $formdata->lastname = $params['lastname']; // Default: Lasty.
        $formdata->availability = $params['availability']; // Default: only.

        return $formdata;
    }

    public function get_create_alternate_form_submission_params(array $overrideparams) {
        $params = [];

        $params['email'] = array_key_exists('email', $overrideparams) ? $overrideparams['email'] : 'different@email.com';
        $params['firstname'] = array_key_exists('firstname', $overrideparams) ? $overrideparams['firstname'] : 'Firsty';
        $params['lastname'] = array_key_exists('lastname', $overrideparams) ? $overrideparams['lastname'] : 'Lasty';
        $params['availability'] = array_key_exists('availability', $overrideparams) ? $overrideparams['availability'] : 'only';

        return $params;
    }

}
