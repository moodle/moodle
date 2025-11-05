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
 * The stepslib class.
 * @package     mod_board
 * @author      Karen Holland <karen@brickfieldlabs.ie>
 * @copyright   2021 Brickfield Education Labs <https://www.brickfield.ie/>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backup_board_activity_structure_step extends backup_activity_structure_step {
    /**
     * Define the structure.
     * @return backup_nested_element
     */
    protected function define_structure() {

        $userinfo = $this->get_setting_value('userinfo');

        $board = new backup_nested_element('board', ['id'], [
            'course', 'name', 'timemodified', 'intro', 'introformat', 'historyid',
            'background_color', 'addrating', 'hideheaders', 'sortby', 'postby', 'userscanedit', 'singleusermode',
            'completionnotes', 'embed']);

        $columns = new backup_nested_element('columns');
        $column = new backup_nested_element('column', ['id'], ['boardid', 'name', 'sortorder']);

        $notes = new backup_nested_element('notes');
        $note = new backup_nested_element('note', ['id'], [
            'columnid', 'ownerid', 'userid', 'groupid', 'content', 'heading', 'type', 'info', 'url', 'filename', 'timecreated',
            'sortorder', 'deleted']);

        $ratings = new backup_nested_element('ratings');
        $rating = new backup_nested_element('rating', ['id'], [
            'noteid', 'userid', 'timecreated']);

        $comments = new backup_nested_element('comments');
        $comment = new backup_nested_element('comment', ['id'], [
            'noteid', 'userid', 'content', 'timecreated', 'timemodified', 'deleted']);

        $comments->add_child($comment);
        $note->add_child($comments);

        $ratings->add_child($rating);
        $note->add_child($ratings);

        $notes->add_child($note);
        $column->add_child($notes);

        $columns->add_child($column);
        $board->add_child($columns);

        $board->set_source_table('board', ['id' => backup::VAR_ACTIVITYID]);
        $column->set_source_table('board_columns', ['boardid' => backup::VAR_PARENTID], 'id ASC');

        if ($userinfo) {
            $note->set_source_table('board_notes', ['columnid' => backup::VAR_PARENTID], 'id ASC');
            $rating->set_source_table('board_note_ratings', ['noteid' => backup::VAR_PARENTID], 'id ASC');
            $comment->set_source_table('board_comments', ['noteid' => backup::VAR_PARENTID], 'id ASC');
        }

        $comment->annotate_ids('user', 'userid');
        $note->annotate_ids('user', 'userid');
        $note->annotate_ids('group', 'groupid');
        $rating->annotate_ids('user', 'userid');

        $board->annotate_files('mod_board', 'background', null);
        $board->annotate_files('mod_board', 'intro', null);
        $note->annotate_files('mod_board', 'images', 'id');
        $note->annotate_files('mod_board', 'files', 'id');

        return $this->prepare_activity_structure($board);
    }
}
