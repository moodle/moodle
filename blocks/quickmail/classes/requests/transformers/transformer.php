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

namespace block_quickmail\requests\transformers;

defined('MOODLE_INTERNAL') || die();

class transformer {

    public $form_data;
    public $transformed_data;

    /**
     * Construct the transformer
     *
     * @param object  $formdata  the submitted mform data object
     */
    public function __construct($formdata) {
        $this->form_data = $formdata;
        $this->transformed_data = (object)[];
    }

    public function if_exists($prop, $default = 0) {
        return property_exists($this->form_data, $prop)
            ? $this->form_data->$prop
            : $default;
    }

    public function transform() {
        $this->transform_form_data();

        return $this->transformed_data;
    }

}
