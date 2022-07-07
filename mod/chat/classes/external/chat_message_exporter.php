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
 * Class for exporting a chat message.
 *
 * @package    mod_chat
 * @copyright  2017 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_chat\external;
defined('MOODLE_INTERNAL') || die();

use core\external\exporter;

/**
 * Class for exporting a chat message.
 *
 * @copyright  2017 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class chat_message_exporter extends exporter {

    /**
     * Defines exporter properties.
     *
     * @return array
     */
    protected static function define_properties() {
        return array(
            'id' => array(
                'type' => PARAM_INT,
                'description' => 'The message record id.',
            ),
            'chatid' => array(
                'type' => PARAM_INT,
                'description' => 'The chat id.',
                'default' => 0,
            ),
            'userid' => array(
                'type' => PARAM_INT,
                'description' => 'The user who wrote the message.',
                'default' => 0,
            ),
            'groupid' => array(
                'type' => PARAM_INT,
                'description' => 'The group this message belongs to.',
                'default' => 0,
            ),
            'issystem' => array(
                'type' => PARAM_BOOL,
                'description' => 'Whether is a system message or not.',
                'default' => false,
            ),
            'message' => array(
                'type' => PARAM_RAW,
                'description' => 'The message text.',
            ),
            'timestamp' => array(
                'type' => PARAM_INT,
                'description' => 'The message timestamp (indicates when the message was sent).',
                'default' => 0,
            ),
        );
    }

    /**
     * Defines related information.
     *
     * @return array
     */
    protected static function define_related() {
        return array(
            'context' => 'context',
        );
    }

    /**
     * Get the formatting parameters for the name.
     *
     * @return array
     */
    protected function get_format_parameters_for_message() {
        return [
            'component' => 'mod_chat',
        ];
    }
}
