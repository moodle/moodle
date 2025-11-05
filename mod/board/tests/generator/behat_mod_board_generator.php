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
 * Board test generator.
 *
 * @package    mod_board
 * @copyright  2025 Brickfield Education Labs <https://www.brickfield.ie/>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_mod_board_generator extends behat_generator_base {
    #[\Override]
    protected function get_creatable_entities(): array {
        return [
            'columns' => [
                'singular' => 'column',
                'datagenerator' => 'column',
                'required' => ['name'],
                'switchids' => ['board' => 'boardid'],
            ],
            'notes' => [
                'singular' => 'note',
                'datagenerator' => 'note',
                'required' => ['board', 'column', 'heading', 'content', 'user'],
                'switchids' => ['board' => 'boardid', 'user' => 'userid', 'owner' => 'ownerid', 'group' => 'groupid'],
            ],
            'comments' => [
                'singular' => 'comment',
                'datagenerator' => 'comment',
                'required' => ['note', 'content', 'user'],
                'switchids' => ['note' => 'noteid', 'user' => 'userid'],
            ],
            'templates' => [
                'singular' => 'template',
                'datagenerator' => 'template',
                'required' => ['name'],
            ],
        ];
    }

    /**
     * Look up the id of a board from its name.
     *
     * @param string $boardname the board name, for example 'Test board'.
     * @return int corresponding id.
     */
    protected function get_board_id(string $boardname): int {
        $cm = $this->get_cm_by_activity_name('board', $boardname);
        return $cm->instance;
    }

    /**
     * Look up the id of a board owner from its username.
     *
     * @param string $username
     * @return int corresponding id.
     */
    protected function get_owner_id(string $username): int {
        return parent::get_user_id($username);
    }

    /**
     * Look up the id of a group from its idnumber.
     *
     * @param string $idnumber
     * @return int corresponding id or 0
     */
    protected function get_group_id($idnumber): int {
        if (!$idnumber) {
            return 0;
        }
        return parent::get_group_id($idnumber);
    }

    /**
     * Look up the id of a note from its heading.
     *
     * @param string $heading
     * @return int corresponding id
     */
    protected function get_note_id(string $heading): int {
        global $DB;
        $note = $DB->get_record('board_notes', ['heading' => $heading], '*', MUST_EXIST);
        return $note->id;
    }

    /**
     * If contextlevel and reference are specified for template, transform them to the contextid.
     *
     * @param array $data
     * @return array
     */
    protected function preprocess_template($data) {
        if (isset($data['contextlevel'])) {
            if (!isset($data['reference'])) {
                throw new Exception('If field contextlevel is specified, field reference must also be present');
            }
            $context = $this->get_context($data['contextlevel'], $data['reference']);
            unset($data['contextlevel']);
            unset($data['reference']);
            $data['contextid'] = $context->id;
        }
        return $data;
    }
}
