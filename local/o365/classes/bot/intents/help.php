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
 * Class help implements bot intent interface for get-help intent.
 *
 * @package local_o365
 * @author  Enovation Solutions
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2016 onwards Microsoft Open Technologies, Inc. (http://msopentech.com/)
 */

namespace local_o365\bot\intents;

defined('MOODLE_INTERNAL') || die();

/**
 * Class help implements bot intent interface for get-help intent.
 */
class help implements \local_o365\bot\intents\intentinterface {
    /**
     * Gets a message with the welcome text and available questions.
     *
     * @param string $language - Message language
     * @param mixed $entities - Intent entities (optional and not used at the moment)
     * @return array|string - Bot message structure with data
     */
    public static function get_message($language, $entities = null) {
        global $CFG;
        $listitems = [];
        $warnings = [];
        $listtitle = '';

        $message = get_string_manager()->get_string('help_message', 'local_o365', null, $language);
        foreach ($entities as $intent) {
            if (!empty($intent['permission']) && \local_o365\bot\botintent::check_permission($intent['permission'])) {
                    $text = get_string_manager()->get_string($intent['text'], 'local_o365', null, $language);
                    $action = ($intent['clickable'] ? $text : null);
                    $actiontype = ($intent['clickable'] ? 'imBack' : null);
                    $listitems[] = [
                            'title' => $text,
                            'subtitle' => null,
                            'icon' => $CFG->wwwroot . '/local/o365/pix/moodle.png',
                            'action' => $action,
                            'actionType' => $actiontype
                    ];
            }
        }
        return array(
                'message' => $message,
                'listTitle' => $listtitle,
                'listItems' => $listitems,
                'warnings' => $warnings
        );
    }
}
