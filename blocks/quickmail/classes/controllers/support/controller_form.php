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

namespace block_quickmail\controllers\support;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

class controller_form extends \moodleform {

    public $errors;

    public function __construct(
        $subaction = null,
        $customdata = null,
        $method = 'post',
        $target = '',
        $attributes = null,
        $editable = true) {
            parent::__construct($subaction, $customdata, $method, $target, $attributes, $editable);
    }

    public function definition() {
        $this->definition();
    }

    /**
     * Reports whether or not this form was submitted and validated with the "next" subaction
     *
     * @return bool
     */
    public function is_validated_next() {
        return $this->is_validated() && $this->is_subaction('next');
    }

    /**
     * Reports whether or not this form was submitted and with the "back" subaction
     *
     * @return bool
     */
    public function is_submitted_back() {
        return $this->is_submitted_subaction('back');
    }

    /**
     * Reports whether or not this form was submitted and with the given subaction
     *
     * @param  string  $subaction
     * @param  array   $subactions            optional array of additional subactions to listen for
     * @param  bool    $validationcheck   optional, if true will check validity
     * @return bool
     */
    public function is_submitted_subaction($subaction, $subactions = [], $validationcheck = false) {
        if ($validationcheck && ! $this->is_validated()) {
            return false;
        }

        return $this->is_submitted() && $this->is_subaction($subaction, $subactions);
    }

    /**
     * Reports whether or not this form was submitted with the given subaction
     *
     * @param  string   $type  back|next
     * @param  array   $subactions  optional array of additional subactions to listen for
     * @return bool
     */
    private function is_subaction($type, $subactions = []) {
        return $this->get_subaction($subactions) == $type;
    }

    /**
     * Returns which subaction was submitted in this form
     *
     * @param  array   $subactions  optional array of additional subactions to listen for
     * @return mixed  string|null
     */
    private function get_subaction($subactions = []) {
        $data = $this->get_submitted_data();

        $subactions = array_merge(['next', 'back'], $subactions);

        foreach ($subactions as $subaction) {
            if (property_exists($data, $subaction)) {
                return $subaction;
            }
        }

        return null;
    }

    /**
     * Returns this form's custom data by key
     *
     * @param  string  $key
     * @return mixed
     */
    public function get_custom_data($key) {
        return $this->_customdata[$key];
    }

    /**
     * Returns this form's "view name"
     *
     * @return string
     */
    public function get_view_form_name() {
        return $this->get_custom_data('view_form_name');
    }

    /**
     * Returns the current session store data, or a given key's value
     *
     * @param  string  $key  optional
     * @return mixed
     */
    public function get_session_stored($key = null) {
        $stored = $this->get_custom_data('stored');

        if (empty($key)) {
            return $stored;
        }

        return array_key_exists($key, $stored) ? $stored[$key] : '';
    }

    /**
     * Reports whether or not data of a given key exists in the controller session store
     *
     * @param  string  $key
     * @return mixed
     */
    public function has_session_stored($key) {
        $stored = $this->get_custom_data('stored');

        return array_key_exists($key, $stored);
    }

}
