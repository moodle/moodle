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
 * Get help card for user.
 *
 * @package local_o365
 * @author  2018 Enovation
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2014 onwards Microsoft, Inc. (http://microsoft.com/)
 */

namespace local_o365\webservices;

defined('MOODLE_INTERNAL') || die();

/**
 * Get help card for user.
 */
class read_bot_message extends \external_api {

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     */
    public static function bot_message_read_parameters() {
        return new \external_function_parameters([
            'intent' => new \external_value(
                PARAM_TEXT,
                'Bot intent',
                VALUE_DEFAULT,
                'en'
            ),
            'entities' => new \external_value(
                PARAM_RAW,
                'Intent entities',
                VALUE_DEFAULT,
                null
            )
        ]);
    }

    /**
     * Returns the students with the searched name and last login time.
     *
     * @param string $intent
     * @param string $entities
     * @return An array of students and warnings.
     */
    public static function bot_message_read($intent = null, $entities = null) {
        $params = self::validate_parameters(
            self::bot_message_read_parameters(),
            array(
                'intent' => $intent,
                'entities' => $entities
            )
        );
        $botintent = new \local_o365\bot\botintent($params);
        return $botintent->get_message();
    }


    /**
     * Creates list card item external_single_structure
     *
     * @return external_single_structure
     */
    private static function get_cards_structure() {
        return new \external_single_structure(
            array(
                'title' => new \external_value(PARAM_TEXT, 'list item title'),
                'subtitle' => new \external_value(PARAM_RAW, 'list item subtitle'),
                'icon' => new \external_value(PARAM_URL, 'list item icon url'),
                'action' => new \external_value(PARAM_TEXT, 'list item action url or text'),
                'actionType' => new \external_value(PARAM_TEXT, 'list item action type'),
            ), 'listcard list item data'
        );
    }

    /**
     * Describes the return value for get_last_logged_students
     *
     * @return external_single_structure
     */
    public static function bot_message_read_returns() {
        return new \external_single_structure(
            array(
                'message' => new \external_value(PARAM_TEXT, 'message to be returned', VALUE_DEFAULT, ''),
                'listTitle' => new \external_value(PARAM_TEXT, 'title that is showed below the message and above
                    the list cards', VALUE_DEFAULT, ''),
                'listItems' => new \external_multiple_structure(self::get_cards_structure(), 'list of cards to beshowed
                    with message', VALUE_DEFAULT, []),
                'warnings'  => new \external_warnings('warning messages that occured when getting data',
                    'Item id is mod or user id and if not possible to define 0 by default',
                    ''),
                'language' => new \external_value(PARAM_TEXT, 'message language', VALUE_DEFAULT, 'en'),
            )
        );
    }

}
