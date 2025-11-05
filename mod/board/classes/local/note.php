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

namespace mod_board\local;

use mod_board\board;
use stdClass;

/**
 * Note helper class.
 *
 * @package    mod_board
 * @copyright  2021 Brickfield Education Labs <https://www.brickfield.ie/>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class note {
    /**
     * Add a note to the board.
     *
     * @param int $columnid
     * @param int $ownerid
     * @param int|null $groupid
     * @param string|null $heading
     * @param string $content
     * @param array $attachment
     * @param int|null $userid NULL means current user
     * @return stdClass note record with extra historyid property
     */
    public static function create(
        int $columnid,
        int $ownerid,
        ?int $groupid,
        ?string $heading,
        string $content,
        array $attachment,
        ?int $userid = null
    ): stdClass {
        global $DB, $USER;

        if ($userid === null) {
            $userid = $USER->id;
        }
        if (!$userid || !$DB->record_exists('user', ['id' => $userid, 'deleted' => 0])) {
            throw new \core\exception\invalid_parameter_exception('Invalid userid');
        }

        $column = board::get_column($columnid, MUST_EXIST);
        $board = board::get_board($column->boardid, MUST_EXIST);
        $context = board::context_for_board($board);

        $heading = \core_text::substr($heading ?? '', 0, board::LENGTH_HEADING);
        if (trim($heading) === '') {
            $heading = null;
        }
        // There is no technical reason to shorten the content before storage, let WS/frontend deal with restrictions.
        if (trim($content) === '') {
            $content = '';
        } else {
            // Normalise new-line characters to prevent problems with maxlength attribute later.
            $content = str_replace("\r\n", "\n", $content);
        }

        if (!$groupid) {
            $groupid = null;
        } else if ($board->singleusermode != board::SINGLEUSER_DISABLED) {
            throw new \core\exception\invalid_parameter_exception('groupid is not allowed in single user mode');
        } else if (!$DB->record_exists('groups', ['id' => $groupid])) {
            throw new \core\exception\invalid_parameter_exception('Invalid groupid');
        }

        if (!$ownerid) {
            throw new \core\exception\invalid_parameter_exception('ownerid is required');
        }
        if (!$DB->record_exists('user', ['id' => $ownerid, 'deleted' => 0])) {
            throw new \core\exception\invalid_parameter_exception('Invalid ownerid');
        }
        if ($board->singleusermode == board::SINGLEUSER_DISABLED && $userid != $ownerid) {
            throw new \core\exception\invalid_parameter_exception('ownerid must match userid if single user mode disabled');
        }

        $transaction = $DB->start_delegated_transaction();

        // Get the count of notes in the column to add to bottom of sort order.
        $countnotes = $DB->count_records('board_notes', ['columnid' => $columnid, 'deleted' => 0]);

        $noteid = $DB->insert_record('board_notes', [
            'groupid' => $groupid,
            'columnid' => $columnid,
            'ownerid' => $ownerid,
            'heading' => $heading,
            'content' => $content,
            'type' => board::MEDIATYPE_NONE,
            'info' => null,
            'url' => null,
            'userid' => $userid,
            'timecreated' => time(),
            'sortorder' => $countnotes,
            'deleted' => 0,
        ]);

        $note = self::update_attachment($noteid, $attachment, $context);
        $formatted = self::format_for_display($note, $column, $board, $context);

        $historyid = $DB->insert_record('board_history', ['boardid' => $board->id, 'groupid' => $groupid,
            'action' => 'add_note', 'ownerid' => $ownerid, 'userid' => $userid,
            'content' => json_encode([
                'id' => $note->id,
                'columnid' => $columnid,
                'identifier' => $formatted->identifier,
                'heading' => $formatted->heading,
                'content' => $formatted->content,
                'attachment' => ['type' => $formatted->type, 'info' => $formatted->info, 'url' => $formatted->url],
                'rating' => $formatted->rating,
                'timecreated' => $note->timecreated,
                'sortorder' => $countnotes,
            ]),
            'timecreated' => time()]);

        $DB->set_field('board', 'historyid', $historyid, ['id' => $board->id]);
        $board->historyid = (string)$historyid;

        $transaction->allow_commit();

        if ($board->completionnotes) {
            $cm = board::coursemodule_for_board($board);
            $course = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);
            $completion = new \completion_info($course);
            if ($completion->is_enabled($cm)) {
                $completion->update_state($cm);
            }
        }

        $event = \mod_board\event\add_note::create_from_note($note, $column, $board, $context);
        $event->trigger();

        $note->historyid = $historyid;

        board::clear_history();
        return $note;
    }

    /**
     * Update a note.
     *
     * @param int $id
     * @param string|null $heading
     * @param string $content
     * @param array $attachment
     * @return stdClass note record with extra historyid property
     */
    public static function update(int $id, ?string $heading, string $content, array $attachment): stdClass {
        global $DB, $USER;

        $heading = \core_text::substr($heading ?? '', 0, board::LENGTH_HEADING);
        if (trim($heading) === '') {
            $heading = null;
        }
        // There is no technical reason to shorten the content before storage, let WS/frontend deal with restrictions.
        if (trim($content) === '') {
            $content = '';
        } else {
            // Normalise new-line characters to prevent problems with maxlength attribute later.
            $content = str_replace("\r\n", "\n", $content);
        }

        $note = board::get_note($id, MUST_EXIST);
        $column = board::get_column($note->columnid, MUST_EXIST);
        $board = board::get_board($column->boardid, MUST_EXIST);
        $context = board::context_for_board($board);

        $transaction = $DB->start_delegated_transaction();

        $DB->update_record('board_notes', [
            'id' => $note->id,
            'heading' => $heading,
            'content' => $content,
        ]);
        $note = self::update_attachment($note->id, $attachment, $context);
        $formatted = self::format_for_display($note, $column, $board, $context);

        $historyid = $DB->insert_record('board_history', ['boardid' => $board->id, 'action' => 'update_note',
            'ownerid' => $note->ownerid, 'userid' => $USER->id, 'content' => json_encode([
                'id' => $id,
                'columnid' => $column->id,
                'identifier' => $formatted->identifier,
                'heading' => $formatted->heading,
                'content' => $formatted->content,
                'attachment' => ['type' => $formatted->type, 'info' => $formatted->info, 'url' => $formatted->url],
            ]),
            'timecreated' => time()]);

        $DB->set_field('board', 'historyid', $historyid, ['id' => $board->id]);
        $board->historyid = (string)$historyid;

        $transaction->allow_commit();

        $event = \mod_board\event\update_note::create_from_note($note, $column, $board, $context);
        $event->trigger();

        board::clear_history();

        $note->historyid = $historyid;

        return $note;
    }

    /**
     * Delete a note from the board.
     *
     * @param int $id
     * @return int history id
     */
    public static function delete(int $id): int {
        global $DB, $USER;

        $note = board::get_note($id, MUST_EXIST);
        $column = board::get_column($note->columnid, MUST_EXIST);
        $board = board::get_board($column->boardid, MUST_EXIST);
        $context = board::context_for_board($board);

        $sortorder = $note->sortorder;

        $transaction = $DB->start_delegated_transaction();

        $DB->delete_records('board_note_ratings', ['noteid' => $note->id]);
        self::delete_files($note, $context);

        // Delete all note comments.
        $commentrecords = $DB->get_records('board_comments', ['noteid' => $note->id]);
        foreach ($commentrecords as $commentrecord) {
            comment::delete($commentrecord->id);
        }

        $DB->update_record('board_notes', ['id' => $id, 'deleted' => 1]);
        $historyid = $DB->insert_record('board_history', ['boardid' => $board->id, 'action' => 'delete_note',
            'ownerid' => 0, 'content' => json_encode(['id' => $id, 'columnid' => $column->id]),
            'userid' => $USER->id, 'timecreated' => time()]);

        $sql = "UPDATE {board_notes}
                   SET sortorder = sortorder - 1
                 WHERE sortorder > :sortorder AND columnid = :columnid";
        $DB->execute($sql, ['sortorder' => $sortorder, 'columnid' => $column->id]);

        $DB->set_field('board', 'historyid', $historyid, ['id' => $board->id]);
        $board->historyid = (string)$historyid;

        $transaction->allow_commit();

        $event = \mod_board\event\delete_note::create_from_note($note, $column, $board, $context);
        $event->trigger();

        board::clear_history();

        return $historyid;
    }

    /**
     * Move a note to a different column or position in the same column.
     *
     * @param int $id
     * @param int $columnid
     * @param int $sortorder The order in the column the note was placed.
     * @return int history id
     */
    public static function move(int $id, int $columnid, int $sortorder): int {
        global $DB, $USER;

        $note = board::get_note($id, MUST_EXIST);
        $column = board::get_column($note->columnid, MUST_EXIST);
        $board = board::get_board($column->boardid, MUST_EXIST);
        $context = board::context_for_board($board);

        $newcolumn = $DB->get_record('board_columns', ['id' => $columnid], '*', MUST_EXIST);
        if ($newcolumn->boardid != $column->boardid) {
            throw new \invalid_parameter_exception('note cannot be moved to a different board');
        }

        $transaction = $DB->start_delegated_transaction();

        $formatted = self::format_for_display($note, $column, $board, $context);

        $DB->insert_record('board_history', ['boardid' => $board->id, 'action' => 'delete_note',
            'content' => json_encode(['id' => $note->id, 'columnid' => $note->columnid]),
            'ownerid' => $note->ownerid, 'userid' => $USER->id, 'timecreated' => time()]);
        $historyid = $DB->insert_record('board_history', ['boardid' => $board->id, 'groupid' => $note->groupid,
            'action' => 'add_note', 'userid' => $note->userid, 'ownerid' => $note->ownerid,
            'content' => json_encode([
                'id' => $note->id,
                'columnid' => $columnid,
                'identifier' => $formatted->identifier,
                'heading' => $formatted->heading,
                'content' => $formatted->content,
                'attachment' => ['type' => $formatted->type, 'info' => $formatted->info, 'url' => $formatted->url],
                'timecreated' => $note->timecreated,
                'rating' => $formatted->rating,
                'sortorder' => $sortorder,
            ]),
            'timecreated' => time()]);
        // Checking if we move the note up or down.
        $ismovingup = $note->sortorder < $sortorder;
        $ismovingdown = $note->sortorder > $sortorder;
        $issamecolumn = ($columnid == $note->columnid);
        // Check whether it is the same column and then increment or decrement notes above or below
        // the set sortorder according to whether the sortorder has moved up or down.
        if ($issamecolumn) {
            $params = ['newsort' => $sortorder, 'oldsort' => $note->sortorder, 'columnid' => $columnid];
            if ($ismovingup) {
                $sql = "UPDATE {board_notes}
                           SET sortorder = sortorder - 1
                         WHERE sortorder <= :newsort
                               AND sortorder >= :oldsort
                               AND columnid = :columnid";
                $DB->execute($sql, $params);
            } else if ($ismovingdown) {
                $sql = "UPDATE {board_notes}
                           SET sortorder = sortorder + 1
                         WHERE sortorder >= :newsort
                               AND sortorder <= :oldsort
                               AND columnid = :columnid";
                $DB->execute($sql, $params);
            }
        } else {
            // Increment the new column notes to fit the moved note.
            $sql = "UPDATE {board_notes}
                       SET sortorder = sortorder + 1
                     WHERE sortorder >= :newsort AND columnid = :columnid";
            $DB->execute($sql, ['newsort' => $sortorder, 'columnid' => $columnid]);
            // Decrement the old column notes above where the moved note left.
            $sql = "UPDATE {board_notes}
                       SET sortorder = sortorder - 1
                     WHERE sortorder > :oldsort AND columnid = :columnid";
            $DB->execute($sql, ['oldsort' => $note->sortorder, 'columnid' => $note->columnid]);
        }
        // Update the note record.
        $DB->update_record('board_notes', [
            'id' => $note->id,
            'columnid' => $columnid,
            'sortorder' => $sortorder,
        ]);
        $note = board::get_note($note->id, MUST_EXIST);

        $DB->set_field('board', 'historyid', $historyid, ['id' => $board->id]);
        $board->historyid = (string)$historyid;

        $transaction->allow_commit();

        $event = \mod_board\event\move_note::create_from_note($note, $column, $board, $context);
        $event->trigger();

        board::clear_history();

        return $historyid;
    }

    /**
     * Checks to see if the user can rate the note.
     *
     * @param int $noteid
     * @return bool
     */
    public static function can_rate(int $noteid): bool {
        global $USER;

        $note = board::get_note($noteid);
        if (!$note) {
            return false;
        }
        $column = board::get_column($note->columnid, MUST_EXIST);
        $board = board::get_board($column->boardid, MUST_EXIST);
        $context = board::context_for_board($board);

        if (!board::board_rating_enabled($board)) {
            return false;
        }

        if (board::board_readonly($board, $note->groupid)) {
            return false;
        }

        if (!has_capability('mod/board:post', $context)) {
            return false;
        }

        $iseditor = has_capability('mod/board:manageboard', $context);

        if ($board->addrating == board::RATINGBYSTUDENTS && $iseditor) {
            return false;
        }

        if ($board->addrating == board::RATINGBYTEACHERS && !$iseditor) {
            return false;
        }

        if (!$iseditor) {
            if ($board->singleusermode == board::SINGLEUSER_PRIVATE) {
                if ($note->userid != $USER->id && $note->ownerid != $USER->id) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Rate or unrate the note.
     *
     * @param int $noteid
     * @return int history id
     */
    public static function rate(int $noteid): int {
        global $DB, $USER;

        $note = board::get_note($noteid, MUST_EXIST);
        $column = board::get_column($note->columnid, MUST_EXIST);
        $board = board::get_board($column->boardid, MUST_EXIST);
        $context = board::context_for_board($board);

        $transaction = $DB->start_delegated_transaction();

        $hasrating = $DB->record_exists('board_note_ratings', ['userid' => $USER->id, 'noteid' => $noteid]);
        $action = $hasrating ? 'delete_note_rating' : 'add_note_rating';
        if ($hasrating) {
            $DB->delete_records('board_note_ratings', ['userid' => $USER->id, 'noteid' => $noteid]);
        } else {
            $DB->insert_record('board_note_ratings', [
                'userid' => $USER->id,
                'noteid' => $noteid,
                'timecreated' => time(),
            ]);
        }

        $rating = self::get_rating($noteid);
        $historyid = $DB->insert_record('board_history', ['boardid' => $board->id, 'action' => $action,
            'content' => json_encode(['id' => $note->id, 'rating' => $rating]),
            'userid' => $USER->id, 'timecreated' => time()]);

        $DB->set_field('board', 'historyid', $historyid, ['id' => $board->id]);
        $board->historyid = (string)$historyid;

        $transaction->allow_commit();

        $event = \mod_board\event\rate_note::create_from_note($note, $rating, $column, $board, $context);
        $event->trigger();

        board::clear_history();

        return $historyid;
    }

    /**
     * Retrieves a record of the rating for the selected note.
     *
     * @param int $noteid
     * @return int
     */
    public static function get_rating($noteid) {
        global $DB;
        return $DB->count_records('board_note_ratings', ['noteid' => $noteid]);
    }

    /**
     * Store the added image file.
     *
     * @param int $noteid
     * @param int $draftitemid
     * @param \context $context
     * @return string|null file name
     */
    protected static function store_image_file(int $noteid, int $draftitemid, \context $context): ?string {
        $options = self::get_image_picker_options();

        file_save_draft_area_files(
            $draftitemid,
            $context->id,
            'mod_board',
            'images',
            $noteid,
            $options
        );

        $fs = get_file_storage();
        /** @var \stored_file[] $files */
        $files = $fs->get_directory_files(
            $context->id,
            'mod_board',
            'images',
            $noteid,
            '/',
            false,
            false,
            'id DESC'
        );

        $storedfile = null;
        foreach ($files as $file) {
            if (!$storedfile) {
                $storedfile = $file;
                continue;
            }
            $file->delete();
        }

        if (!$storedfile) {
            // This means there is no file here.
            return null;
        }

        return $storedfile->get_filename();
    }

    /**
     * Store the added general file.
     *
     * @param int $noteid
     * @param int $draftitemid
     * @param \context $context
     * @return string|null file name
     */
    protected static function store_general_file(int $noteid, int $draftitemid, \context $context): ?string {
        $options = self::get_general_picker_options();
        if (!$options) {
            return null;
        }

        file_save_draft_area_files(
            $draftitemid,
            $context->id,
            'mod_board',
            'files',
            $noteid,
            $options
        );

        $fs = get_file_storage();
        /** @var \stored_file[] $files */
        $files = $fs->get_directory_files(
            $context->id,
            'mod_board',
            'files',
            $noteid,
            '/',
            false,
            false,
            'id DESC'
        );

        $storedfile = null;
        foreach ($files as $file) {
            if (!$storedfile) {
                $storedfile = $file;
                continue;
            }
            $file->delete();
        }

        if (!$storedfile) {
            // This means there is no file here.
            return null;
        }

        return $storedfile->get_filename();
    }

    /**
     * Delete the image file for to a note.
     *
     * @param int $noteid
     * @param \context $context
     */
    protected static function delete_image_file(int $noteid, \context $context): void {
        $fs = get_file_storage();
        $fs->delete_area_files($context->id, 'mod_board', 'images', $noteid);
    }

    /**
     * Delete the general file for to a note.
     *
     * @param int $noteid
     * @param \context $context
     */
    protected static function delete_general_file(int $noteid, \context $context): void {
        $fs = get_file_storage();
        $fs->delete_area_files($context->id, 'mod_board', 'files', $noteid);
    }

    /**
     * Update the attachment.
     *
     * NOTE: This must be used only from create and update methods, it is public to allow testing only!
     *
     * @param int $noteid
     * @param array $attachment
     * @param \context $context
     * @return stdClass
     */
    public static function update_attachment(int $noteid, array $attachment, \context $context): stdClass {
        global $DB;

        $note = board::get_note($noteid, MUST_EXIST);
        $update = [];

        if (!isset($attachment['type'])) {
            $attachment['type'] = board::MEDIATYPE_NONE;
        }
        if (isset($attachment['info'])) {
            $attachment['info'] = \core_text::substr($attachment['info'], 0, board::LENGTH_INFO);
        } else {
            $attachment['info'] = '';
        }

        if ($attachment['type'] == board::MEDIATYPE_IMAGE) {
            if (empty($attachment['draftitemid'])) {
                throw new \core\exception\invalid_parameter_exception('missing image filemanager draftitemid');
            }

            self::delete_general_file($noteid, $context);

            $filename = self::store_image_file($noteid, $attachment['draftitemid'], $context);
            if ($filename) {
                $update = [
                    'type' => board::MEDIATYPE_IMAGE,
                    'info' => $attachment['info'],
                    'url' => null,
                    'filename' => $filename,
                ];
            } else {
                self::delete_image_file($noteid, $context);
                $update = [
                    'type' => board::MEDIATYPE_NONE,
                    'info' => null,
                    'url' => null,
                    'filename' => null,
                ];
            }
        } else if ($attachment['type'] == board::MEDIATYPE_FILE) {
            if (empty($attachment['draftitemid'])) {
                throw new \core\exception\invalid_parameter_exception('missing general filemanager draftitemid');
            }

            self::delete_image_file($noteid, $context);

            if (self::get_accepted_general_file_extensions()) {
                $filename = self::store_general_file($noteid, $attachment['draftitemid'], $context);
            } else {
                $filename = null;
            }
            if ($filename) {
                $update = [
                    'type' => board::MEDIATYPE_FILE,
                    'info' => $attachment['info'],
                    'url' => null,
                    'filename' => $filename,
                ];
            } else {
                self::delete_general_file($noteid, $context);
                $update = [
                    'type' => board::MEDIATYPE_NONE,
                    'info' => null,
                    'url' => null,
                    'filename' => null,
                ];
            }
        } else if ($attachment['type'] == board::MEDIATYPE_YOUTUBE || $attachment['type'] == board::MEDIATYPE_URL) {
            self::delete_general_file($noteid, $context);
            self::delete_image_file($noteid, $context);

            if ($attachment['url']) {
                $update = [
                    'type' => $attachment['type'],
                    'info' => $attachment['info'],
                    'url' => \core_text::substr($attachment['url'], 0, board::LENGTH_URL),
                    'filename' => null,
                ];
            } else {
                $update = [
                    'type' => board::MEDIATYPE_NONE,
                    'info' => null,
                    'url' => null,
                    'filename' => null,
                ];
            }
        } else {
            self::delete_general_file($noteid, $context);
            self::delete_image_file($noteid, $context);

            $update = [
                'type' => board::MEDIATYPE_NONE,
                'info' => null,
                'url' => null,
                'filename' => null,
            ];
        }

        if ($update) {
            $update['id'] = $note->id;
            $DB->update_record('board_notes', $update);
            $note = board::get_note($note->id, MUST_EXIST);
        }

        return $note;
    }

    /**
     * Delete the stored images and general files related to the note.
     *
     * @param stdClass $note
     * @param \context $context
     */
    public static function delete_files(stdClass $note, \context $context): void {
        if ($note->type == board::MEDIATYPE_IMAGE) {
            self::delete_image_file($note->id, $context);
        }
        if ($note->type == board::MEDIATYPE_FILE) {
            self::delete_general_file($note->id, $context);
        }
    }

    /**
     * Get the supported filetype extensions for note images.
     *
     * @return array of strings of supported file extensions.
     */
    public static function get_accepted_image_file_extensions(): array {
        $config = get_config('mod_board');
        if (isset($config->acceptedfiletypeforcontent)) {
            $extensions = explode(',', $config->acceptedfiletypeforcontent);
        } else {
            $extensions = [];
        }
        return $extensions;
    }

    /**
     * Get the supported filetype extensions for note general files.
     *
     * @return array of strings of supported file extensions.
     */
    public static function get_accepted_general_file_extensions(): array {
        $list = get_config('mod_board', 'acceptedfiletypeforgeneral');
        if (!$list) {
            return [];
        }

        $extensions = explode(',', $list);
        $extensions = array_map('trim', $extensions);
        $extensions = array_filter($extensions);

        return array_values($extensions);
    }

    /**
     * Returns basic options for the image file picker.
     *
     * @return array
     */
    public static function get_image_picker_options(): array {
        $extensions = self::get_accepted_image_file_extensions();

        $extensions = array_map(function ($extension) {
            return '.' . $extension;
        }, $extensions);

        return [
            'accepted_types' => $extensions,
            'maxfiles' => 1,
            'subdirs' => 0,
            'maxbytes' => board::ACCEPTED_FILE_MAX_SIZE,
        ];
    }

    /**
     * Returns basic options for the general file picker.
     *
     * @return array
     */
    public static function get_general_picker_options(): array {
        $extensions = self::get_accepted_general_file_extensions();
        if (!$extensions) {
            return [];
        }

        $extensions = array_map(function ($extension) {
            return '.' . $extension;
        }, $extensions);

        return [
            'accepted_types' => $extensions,
            'maxfiles' => 1,
            'subdirs' => 0,
            'maxbytes' => board::ACCEPTED_FILE_MAX_SIZE,
        ];
    }

    /**
     * Is there any file in draft area?
     *
     * @param int $draftitemid
     * @return bool
     */
    public static function is_draft_file_present(int $draftitemid): bool {
        global $USER;

        $usercontext = \context_user::instance($USER->id);
        $fs = get_file_storage();
        $draftfiles = $fs->get_area_files($usercontext->id, 'user', 'draft', $draftitemid, 'id', false);

        return !empty($draftfiles);
    }

    /**
     * Does the URL look like valid YouTube url?
     *
     * @param string $url
     * @return bool
     */
    public static function is_youtube_url(string $url): bool {
        // NOTE: this has to match getEmbedUrl() in board.js file.
        $regex = '/(\/|%3D|v=)([0-9A-z-_]{11})([%#?&]|$)/';
        return preg_match($regex, $url);
    }

    /**
     * Similar to s(), but the entities are encoded only once.
     *
     * This is necessary because historically data was s()ed before saving
     * into database, which is not the case anymore.
     *
     * @param string|null $var
     * @return string|null
     */
    public static function format_plain_text(?string $var): ?string {
        if ($var === null) {
            return null;
        }
        if (trim($var) === '') {
            return '';
        }
        return htmlspecialchars($var, ENT_QUOTES | ENT_HTML5 | ENT_SUBSTITUTE, 'utf-8', false);
    }

    /**
     * Format note content using slimmed down markdown.
     *
     * @param string $content
     * @return string
     */
    public static function format_limited_markdown(string $content): string {
        if (trim($content) === '') {
            return '';
        }

        // HTML tags are not allowed, encode all entities to show them, keep existing entities as-is.
        $content = htmlspecialchars($content, ENT_QUOTES | ENT_HTML5 | ENT_SUBSTITUTE, 'utf-8', false);

        // Normalise Windows newlines.
        $content = str_replace("\r\n", "\n", $content);

        // Concat wrapped lines to simplify regex.
        $lines = explode("\n", $content);
        $content = '';
        while (true) {
            while (true) {
                $line = current($lines);
                next($lines);
                if ($line === false) {
                    break 2;
                }
                if (trim($line) !== '') {
                    break;
                }
            }
            while (true) {
                $nextline = current($lines);
                if ($nextline === false || $nextline === '') {
                    next($lines);
                    break;
                }
                if (preg_match('/^(# |- |\d+\. )[^ ]/', $nextline)) {
                    break;
                }
                next($lines);
                $line .= ' ' . $nextline;
            };

            $content .= $line . "\n";
        }

        // Add headings - note that Moodle usually uses visuals of lower headings.
        $content = preg_replace('/^# (.*)$/m', '<h4 class="h5">$1</h4>', $content);

        // Add lists.
        $content = preg_replace('/^- (.*)/m', '<ul><li>$1</li></ul>', $content);
        $content = preg_replace('/^\d+\. (.*)/m', '<ol><li>$1</li></ol>', $content);
        $content = str_replace("</ul>\n<ul>", "\n", $content);
        $content = str_replace("</ol>\n<ol>", "\n", $content);

        // Add paragraphs.
        $content = preg_replace('/^([^<].*)$/m', '<p>$1</p>', $content);

        // Finally add bold and italic.
        $content = preg_replace("/\*\*\*([^<>]+)\*\*\*/U", "<em><strong>$1</strong></em>", $content);
        $content = preg_replace("/\*\*([^<>]+)\*\*/U", "<strong>$1</strong>", $content);
        $content = preg_replace("/\*([^<>]+)\*/U", "<em>$1</em>", $content);

        return clean_text($content, FORMAT_HTML);
    }

    /**
     * Format note object for display, this must be used always before returning note data via WS.
     *
     * WARNING: this must be as fast as possible because it is used a lot!
     *
     * @param stdClass $note
     * @param stdClass $column
     * @param stdClass $board
     * @param \context $context
     * @return stdClass
     */
    public static function format_for_display(stdClass $note, stdClass $column, stdClass $board, \context $context): stdClass {
        $note = (object)(array)$note;
        unset($note->historyid);
        if ($note->columnid != $column->id || $column->boardid != $board->id) {
            throw new \core\exception\coding_exception('Invalid parameter mix');
        }

        $note->heading = self::format_plain_text($note->heading);
        $note->content = self::format_limited_markdown($note->content);

        if ($note->type == board::MEDIATYPE_IMAGE) {
            $note->url = \moodle_url::make_pluginfile_url(
                $context->id,
                'mod_board',
                'images',
                $note->id,
                '/',
                $note->filename
            )->out(false);
            if (trim($note->info ?? '') === '') {
                // NOTE: ideally title should be required in form validation.
                $note->info = $note->filename;
            }
        } else if ($note->type == board::MEDIATYPE_FILE) {
            if (self::get_accepted_general_file_extensions()) {
                $note->url = \moodle_url::make_pluginfile_url(
                    $context->id,
                    'mod_board',
                    'files',
                    $note->id,
                    '/',
                    $note->filename
                )->out(false);
                $note->info = $note->filename;
            } else {
                $note->type = (string)board::MEDIATYPE_NONE;
                $note->url = null;
                $note->info = null;
            }
        } else if ($note->type == board::MEDIATYPE_YOUTUBE) {
            if (!get_config('mod_board', 'allowyoutube')) {
                $note->type = (string)board::MEDIATYPE_NONE;
                $note->url = null;
                $note->info = null;
            } else if (trim($note->info ?? '') === '') {
                $note->info = $note->url;
            }
        } else if ($note->type == board::MEDIATYPE_URL) {
            if (empty($note->url)) {
                $note->type = (string)board::MEDIATYPE_NONE;
                $note->url = null;
                $note->info = null;
            } else if (trim($note->info ?? '') === '') {
                $note->info = $note->url;
                if (\core_text::strlen($note->info) > 100) {
                    // No point showing very long URLs here.
                    $note->info = \core_text::substr($note->info, 0, 100) . '...';
                }
            }
        }

        if (isset($note->info)) {
            $note->info = self::format_plain_text($note->info);
        }

        // Identifier of note.
        $note->identifier = null;
        if (trim($note->heading ?? '') !== '') {
            $note->identifier = $note->heading;
        } else if ($note->content !== '') {
            // The limited Markdown formatting normalises newlines,
            // so use just the first like/paragraph as note identifier.
            $lines = explode("\n", $note->content);
            $line = strip_tags($lines[0]);
            if (trim($line) !== '') {
                $note->identifier = $line;
            }
        }
        if ($note->identifier === null) {
            if (trim($note->info ?? '') !== '') {
                $note->identifier = $note->info;
            } else if ($note->filename) {
                $note->identifier = $note->filename;
            }
        }
        if (isset($note->identifier)) {
            $note->identifier = self::format_plain_text($note->identifier);
        }

        if (!$note->deleted && board::board_rating_enabled($board)) {
            $note->rating = self::get_rating($note->id);
        } else {
            $note->rating = null;
        }

        return $note;
    }

    /**
     * Returns note info for export purposes.
     *
     * @param stdClass $note formatted note object
     * @return string
     */
    public static function get_export_info(stdClass $note): string {
        $rowstring = '';
        if (trim($note->heading ?? '') !== '') {
            $rowstring .= $note->heading;
        }
        $hascontent = false;
        if (trim($note->content ?? '') !== '') {
            // Note that formatted content always has block style tags in it,
            // there is no need to add line breaks around it.
            $rowstring .= $note->content;
            $hascontent = true;
        }
        if ($note->type) {
            if ($rowstring !== '' && !$hascontent) {
                $rowstring .= "<br />";
            }
            $spacer = '';
            if (trim($note->info ?? '') !== '') {
                $rowstring .= $note->info;
                $spacer = ' ';
            }
            if ($note->info !== $note->url && $note->url) {
                $rowstring .= $spacer . '(' . $note->url . ')';
            }
        }
        return $rowstring;
    }
}
