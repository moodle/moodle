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

namespace block_quickmail\requests;

defined('MOODLE_INTERNAL') || die();

use block_quickmail\requests\transformers\compose_transformer;

class compose_request extends \block_quickmail_request {

    /**
     * Reports whether or not this request was submitted with intent to send
     *
     * @return bool
     */
    public function to_send_message() {
        return $this->was_submitted('send');
    }

    /**
     * Reports whether or not this request was submitted with intent to save
     *
     * @return bool
     */
    public function to_save_draft() {
        return $this->was_submitted('save');
    }

    public static function get_transformed($formdata) {
        $transformer = new compose_transformer($formdata);

        return $transformer->transform();
    }

}
