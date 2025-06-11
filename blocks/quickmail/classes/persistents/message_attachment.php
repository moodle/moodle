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

namespace block_quickmail\persistents;

defined('MOODLE_INTERNAL') || die();

use block_quickmail\persistents\message;
use block_quickmail\persistents\concerns\enhanced_persistent;
use block_quickmail\persistents\concerns\belongs_to_a_message;

class message_attachment extends \block_quickmail\persistents\persistent {

    use enhanced_persistent,
        belongs_to_a_message;

    /** Table name for the persistent. */
    const TABLE = 'block_quickmail_msg_attach';

    /**
     * Return the definition of the properties of this model.
     *
     * @return array
     */
    protected static function define_properties() {
        return [
            'message_id' => [
                'type' => PARAM_INT,
            ],
            'path' => [
                'type' => PARAM_TEXT,
            ],
            'filename' => [
                'type' => PARAM_FILE,
            ],
        ];
    }

    // Custom Methods.
    /**
     * Returns this attachment's full file path (path + filename)
     *
     * @param  bool  $leadingslash  whether or not to include a leading slash
     * @return string
     */
    public function get_full_filepath($leadingslash = false) {
        $path = $this->get('path') . $this->get('filename');

        return !$leadingslash ? ltrim($path, '/') : $path;
    }

    // Custom Static Methods.
    /**
     * Deletes all attachment records for this message
     *
     * @param  message $message
     * @return void
     */
    public static function clear_all_for_message(message $message) {
        global $DB;

        $DB->delete_records('block_quickmail_msg_attach', ['message_id' => $message->get('id')]);
    }

}
