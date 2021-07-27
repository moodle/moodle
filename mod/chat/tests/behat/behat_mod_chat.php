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
 * Steps definitions related to mod_chat.
 *
 * @package   mod_chat
 * @category  test
 * @copyright 2021 Dongsheng Cai
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../../lib/behat/behat_base.php');

/**
 * Steps definitions related to mod_chat.
 *
 */
class behat_mod_chat extends behat_base {
    /**
     * Convert page names to URLs for steps like 'When I am on the "[identifier]" "[page type]" page'.
     *
     * Recognised page names are:
     * | pagetype          | name meaning | description                   |
     * | View              | Chat name    | The chat info page (view.php) |
     *
     * @param string $type identifies which type of page this is, e.g. 'View'.
     * @param string $name chat instance name
     * @return moodle_url the corresponding URL.
     * @throws Exception with a meaningful error message if the specified page cannot be found.
     */
    protected function resolve_page_instance_url(string $type, string $name): moodle_url {
        switch (strtolower($type)) {
            case 'view':
                $cm = $this->get_cm_by_chat_name($name);
                return new moodle_url('/mod/chat/view.php', ['id' => $cm->id]);
            default:
                throw new Exception('Unrecognised chat page type "' . $type . '."');
        }
    }

    /**
     * Get a chat by name.
     *
     * @param string $name chat name.
     * @return stdClass the corresponding DB row.
     */
    protected function get_chat_by_name(string $name): stdClass {
        global $DB;
        return $DB->get_record('chat', ['name' => $name], '*', MUST_EXIST);
    }

    /**
     * Get a chat coursemodule object from the name.
     *
     * @param string $name chat name.
     * @return stdClass cm from get_coursemodule_from_instance.
     */
    protected function get_cm_by_chat_name(string $name): stdClass {
        $chat = $this->get_chat_by_name($name);
        return get_coursemodule_from_instance('chat', $chat->id, $chat->course);
    }
}
