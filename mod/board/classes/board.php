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

namespace mod_board;

/**
 * The main board class functions.
 * @package     mod_board
 * @author      Jay Churchward <jay@brickfieldlabs.ie>
 * @copyright   2021 Brickfield Education Labs <https://www.brickfield.ie/>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class board {

    /** @var int Minumum file size of 100 bytes. */
    const ACCEPTED_FILE_MIN_SIZE = 100;

    /** @var int Maximum file size of 10Mb. */
    const ACCEPTED_FILE_MAX_SIZE = 1024 * 1024 * 10;

    /** @var int Value for the max column name length, consistent with db */
    const LENGTH_COLNAME = 100;

    /** @var int Value for the max heading length, consistent with db */
    const LENGTH_HEADING = 100;

    /** @var int Value for the max info length, consistent with db */
    const LENGTH_INFO = 100;

    /** @var int Value for the max url length, consistent with db */
    const LENGTH_URL = 200;

    /** @var int Value for disabling rating. */
    const RATINGDISABLED = 0;

    /** @var int Value for allowing students to rate posts. */
    const RATINGBYSTUDENTS = 1;

    /** @var int Value for allowing teachers to rate posts */
    const RATINGBYTEACHERS = 2;

    /** @var int Value for allowing all roles to rate posts */
    const RATINGBYALL = 3;

    /** @var int Value for sorting all posts by date */
    const SORTBYDATE = 1;

    /** @var int Value for sorting all posts by rating */
    const SORTBYRATING = 2;

    /** @var int Value for no sorting on posts */
    const SORTBYNONE = 3;

    /** @var int Value for the singlusermode not set*/
    const SINGLEUSER_DISABLED = 0;

    /** @var int Value for the singlusermode setting in private mode*/
    const SINGLEUSER_PRIVATE = 1;

    /** @var int Value for the singleusermode setting in public mode*/
    const SINGLEUSER_PUBLIC = 2;

    /**
     * Retrieves the course module for the board
     *
     * @param object $board
     * @return object
     */
    public static function coursemodule_for_board($board) {
        return get_coursemodule_from_instance('board', $board->id, $board->course, false, MUST_EXIST);
    }

    /**
     * Gets the configuration for this board.
     * @param int $id The board id.
     * @param int $ownerid The user board to get notes from.
     */
    public static function get_configuration($id, $ownerid) {
        global $DB, $USER;

        $board = $DB->get_record('board', array('id' => $id));
        $contextid = \context_module::instance(self::coursemodule_for_board($board)->id)->id;
        $config = get_config('mod_board');

        $conf = [
            'board' => $board,
            'contextid' => $contextid,
            'isEditor' => self::board_is_editor($board->id),
            'usersCanEdit' => self::board_users_can_edit($board->id),
            'userId' => $USER->id,
            'ownerId' => $ownerid,
            'readonly' => (self::board_readonly($board->id) || !self::can_post($board->id, $USER->id, $ownerid)),
            'columnicon' => $config->new_column_icon,
            'noteicon' => $config->new_note_icon,
            'mediaselection' => $config->media_selection,
            'post_max_length' => $config->post_max_length,
            'history_refresh' => $config->history_refresh,
            'file' => [
                'extensions' => self::get_accepted_file_extensions(),
                'size_min' => self::ACCEPTED_FILE_MIN_SIZE,
                'size_max' => self::ACCEPTED_FILE_MAX_SIZE
            ],
            'ratingenabled' => self::board_rating_enabled($board->id),
            'hideheaders' => self::board_hide_headers($board->id),
            'sortby' => $board->sortby,
            'colours' => self::get_column_colours(),
            'enableblanktarget' => $board->enableblanktarget
        ];

        return $conf;
    }

    /**
     * Get the supported filetype extensions
     *
     * @return array of strings of supported file extensions.
     */
    public static function get_accepted_file_extensions() {
        $config = get_config('mod_board');
        if (isset($config->acceptedfiletypeforcontent)) {
            $extensions = explode(',', $config->acceptedfiletypeforcontent);
        } else {
            $extensions = [];
        }
        return $extensions;
    }

    /**
     * Retrieves a record of the selected board.
     *
     * @param int $id
     * @return object
     */
    public static function get_board($id) {
        global $DB;
        return $DB->get_record('board', array('id' => $id));
    }

    /**
     * Retrieves a record of the selected column.
     *
     * @param int $id
     * @return object
     */
    public static function get_column($id) {
        global $DB;
        return $DB->get_record('board_columns', array('id' => $id));
    }

    /**
     * Retrieves a record of the selected note.
     *
     * @param int $id
     * @return object
     */
    public static function get_note($id) {
        global $DB;
        return $DB->get_record('board_notes', array('id' => $id, 'deleted' => 0));
    }

    /**
     * Retrieves a record of the rating for the selected note.
     *
     * @param int $noteid
     * @return int
     */
    public static function get_note_rating($noteid) {
        global $DB;
        return $DB->count_records('board_note_ratings', array('noteid' => $noteid));
    }

    /**
     * Retrieves the context of the selected board.
     *
     * @param int $id
     * @return object
     */
    public static function context_for_board($id) {
        if (!$board = static::get_board($id)) {
            return null;
        }

        $cm = static::coursemodule_for_board($board);
        return \context_module::instance($cm->id);
    }

    /**
     * Retrieves the context of the selected column.
     *
     * @param int $id
     * @return object
     */
    public static function context_for_column($id) {
        if (!$column = static::get_column($id)) {
            return null;
        }

        return static::context_for_board($column->boardid);
    }

    /**
     * Adds a capability check to view the board.
     *
     * @param int $id
     * @return void
     */
    public static function require_capability_for_board_view($id) {
        $context = static::context_for_board($id);
        if ($context) {
            require_capability('mod/board:view', $context);
        }
    }

    /**
     * Adds a capability check for the board.
     *
     * @param int $id
     * @return void
     */
    public static function require_capability_for_board($id) {
        $context = static::context_for_board($id);
        if ($context) {
            require_capability('mod/board:manageboard', $context);
        }
    }

    /**
     * Adds a capability check for the columns.
     *
     * @param int $id
     * @return void
     */
    public static function require_capability_for_column($id) {
        $context = static::context_for_column($id);
        if ($context) {
            require_capability('mod/board:manageboard', $context);
        }
    }

    /**
     * Requires the users to be in groups.
     *
     * @param int $groupid
     * @param int $boardid
     * @return mixed
     */
    public static function require_access_for_group($groupid, $boardid) {
        $cm = static::coursemodule_for_board(static::get_board($boardid));
        $context = \context_module::instance($cm->id);

        if (has_capability('mod/board:manageboard', $context)) {
            return true;
        }

        $groupmode = groups_get_activity_groupmode($cm);
        if (!in_array($groupmode, [VISIBLEGROUPS, SEPARATEGROUPS])) {
            return true;
        }

        if (!static::can_access_group($groupid, $context)) {
            throw new \Exception('Invalid group');
        }
    }

    /**
     * Clears the records in the history table for the last minute.
     *
     * @return bool
     */
    public static function clear_history() {
        global $DB;

        return $DB->delete_records_select('board_history', 'timecreated < :timecreated',
                                        array('timecreated' => time() - 60)); // 1 minute history
    }

    /**
     * Hides the headers of the board.
     *
     * @param int $boardid
     * @return bool
     */
    public static function board_hide_headers($boardid) {
        $board = static::get_board($boardid);
        if (!$board->hideheaders) {
            return false;
        }

        $context = static::context_for_board($boardid);
        $iseditor = has_capability('mod/board:manageboard', $context);
        return !$iseditor;
    }

    /**
     * Check if there are any notes on this board.
     *
     * @param int $boardid
     * @return bool true if there are notes.
     */
    public static function board_has_notes($boardid): bool {
        global $DB;
        $sql = "SELECT COUNT(*) FROM {board_notes}
            LEFT JOIN {board_columns} ON {board_notes}.columnid = {board_columns}.id
            WHERE {board_columns}.boardid = :boardid
            AND {board_notes}.deleted = 0";
        return $DB->count_records_sql($sql, ['boardid' => $boardid]) > 0;
    }


    /**
     * Retrieves the board.
     *
     * @param int $boardid
     * @param int $ownerid The user board to get notes from.
     * @return array
     */
    public static function board_get(int $boardid, int $ownerid = 0): array {
        global $DB, $USER;

        static::require_capability_for_board_view($boardid);

        if (!$board = $DB->get_record('board', array('id' => $boardid))) {
            return [];
        }

        $groupid = groups_get_activity_group(static::coursemodule_for_board(static::get_board($boardid)), true) ?: null;
        $hideheaders = static::board_hide_headers($boardid);

        $columns = $DB->get_records('board_columns', array('boardid' => $boardid), 'sortorder, id', 'id, name, locked');
        $columnindex = 0;

        if ($board->singleusermode == static::SINGLEUSER_PRIVATE) {
            if (!static::can_view_user($board->id, $ownerid) || $ownerid == 0) {
                $ownerid = $USER->id;
            }
        } else if ($board->singleusermode == static::SINGLEUSER_PUBLIC) {
            if ($ownerid == 0) {
                $ownerid = $USER->id;
            }
        } else {
            $ownerid = 0;
        }

        foreach ($columns as $columnid => $column) {
            if ($column->locked === null) {
                $column->locked = false;
            }
            if ($hideheaders) {
                $column->name = ++$columnindex;
            }
            $params = array('columnid' => $columnid, 'deleted' => 0);
            if (!empty($groupid)) {
                $params['groupid'] = $groupid;
            }

            if ($ownerid) {
                $params['ownerid'] = $ownerid;
            }

            $column->notes = $DB->get_records('board_notes', $params, 'sortorder',
                                            'id, userid, heading, content, type, info, url, timecreated, sortorder');
            foreach ($column->notes as $colid => $note) {
                $note->rating = static::get_note_rating($note->id);
            }
        }

        static::clear_history();
        return $columns;
    }

    /**
     * Retrieves the boards history.
     *
     * @param int $boardid
     * @param int $ownerid
     * @param int|null $since
     * @return array
     */
    public static function board_history(int $boardid, int $ownerid, ?int $since): array {
        global $DB;

        static::require_capability_for_board_view($boardid);

        if (!$board = $DB->get_record('board', array('id' => $boardid))) {
            return [];
        }

        $groupid = groups_get_activity_group(static::coursemodule_for_board(static::get_board($boardid)), true) ?: null;

        static::clear_history();

        $condition = "boardid = :boardid";
        $params = array('boardid' => $boardid);

        if ($since !== null) {
            $condition .= " AND id > :since";
            $params['since'] = $since;
        }
        if (!empty($groupid)) {
            $condition .= " AND groupid=:groupid";
            $params['groupid'] = $groupid;
        }
        if ($board->singleusermode == self::SINGLEUSER_PUBLIC || $board->singleusermode == self::SINGLEUSER_PRIVATE) {
            if (self::can_view_user($boardid, $ownerid)) {
                $condition .= " AND (ownerid=:ownerid OR ownerid=null)";
                $params['ownerid'] = $ownerid;
            }
        }

        return $DB->get_records_select('board_history', $condition, $params);
    }

    /**
     * Adds a column to the board
     *
     * @param int $boardid
     * @param string $name
     * @return array
     */
    public static function board_add_column(int $boardid, string $name): array {
        global $DB, $USER;

        $name = mb_substr($name, 0, static::LENGTH_COLNAME);

        static::require_capability_for_board($boardid);

        $transaction = $DB->start_delegated_transaction();

        $maxsortorder = $DB->get_field('board_columns', 'MAX(sortorder)', ['boardid' => $boardid]);

        $columnid = $DB->insert_record('board_columns', array('boardid' => $boardid, 'name' => $name,
            'sortorder' => $maxsortorder + 1));
        $historyid = $DB->insert_record('board_history', array('boardid' => $boardid, 'action' => 'add_column',
            'ownerid' => 0, 'userid' => $USER->id, 'content' => json_encode(array('id' => $columnid, 'name' => $name)),
            'timecreated' => time()));
        $DB->update_record('board', array('id' => $boardid, 'historyid' => $historyid));
        $transaction->allow_commit();

        static::board_add_column_log($boardid, $name, $columnid);

        static::clear_history();
        return array('id' => $columnid, 'historyid' => $historyid);
    }

    /**
     * Triggers the add column event log.
     *
     * @param int $boardid
     * @param string $name
     * @param int $columnid
     * @return void
     */
    public static function board_add_column_log($boardid, $name, $columnid) {
        if (!get_config('mod_board', 'addcolumnnametolog')) {
            $name = '';
        }
        $event = \mod_board\event\add_column::create(array(
            'objectid' => $columnid,
            'context' => \context_module::instance(static::coursemodule_for_board(static::get_board($boardid))->id),
            'other' => array('name' => $name)
        ));
        $event->trigger();
    }

    /**
     * Updates the column.
     *
     * @param int $id
     * @param string $name
     * @return array
     */
    public static function board_update_column(int $id, string $name): array {
        global $DB, $USER;

        $name = mb_substr($name, 0, static::LENGTH_COLNAME);

        static::require_capability_for_column($id);

        $boardid = $DB->get_field('board_columns', 'boardid', array('id' => $id));
        if ($boardid) {
            $transaction = $DB->start_delegated_transaction();
            $update = $DB->update_record('board_columns', array('id' => $id, 'name' => $name));
            $historyid = $DB->insert_record('board_history', array('boardid' => $boardid, 'action' => 'update_column',
                'ownerid' => 0, 'userid' => $USER->id, 'content' => json_encode(array('id' => $id, 'name' => $name)),
                'timecreated' => time()));
            $DB->update_record('board', array('id' => $id, 'historyid' => $historyid));
            $transaction->allow_commit();

            static::board_update_column_log($boardid, $name, $id);
        } else {
            $update = false;
            $historyid = 0;
        }

        static::clear_history();
        return array('status' => $update, 'historyid' => $historyid);
    }

    /**
     * Triggers the update column log.
     *
     * @param int $boardid
     * @param string $name
     * @param int $columnid
     * @return void
     */
    public static function board_update_column_log($boardid, $name, $columnid) {
        if (!get_config('mod_board', 'addcolumnnametolog')) {
            $name = '';
        }
        $event = \mod_board\event\update_column::create(array(
            'objectid' => $columnid,
            'context' => \context_module::instance(static::coursemodule_for_board(static::get_board($boardid))->id),
            'other' => array('name' => $name)
        ));
        $event->trigger();
    }

    /**
     * Deletes a column.
     *
     * @param int $id
     * @return array
     */
    public static function board_delete_column(int $id): array {
        global $DB, $USER;

        static::require_capability_for_column($id);

        $boardid = $DB->get_field('board_columns', 'boardid', array('id' => $id));
        if ($boardid) {
            $transaction = $DB->start_delegated_transaction();
            $notes = $DB->get_records('board_notes', array('columnid' => $id));
            foreach ($notes as $noteid => $note) {
                $DB->delete_records('board_note_ratings', array('noteid' => $note->id));
                $DB->update_record('board_notes', array('id' => $note->id, 'deleted' => 1));
                static::delete_note_file($note->id);
            }
            $delete = $DB->delete_records('board_columns', array('id' => $id));
            $historyid = $DB->insert_record('board_history', array('boardid' => $boardid, 'action' => 'delete_column',
                'ownerid' => 0, 'content' => json_encode(array('id' => $id)),
                'userid' => $USER->id, 'timecreated' => time()));
            $DB->update_record('board', array('id' => $boardid, 'historyid' => $historyid));
            $transaction->allow_commit();

            static::board_delete_column_log($boardid, $id);
        } else {
            $delete = false;
            $historyid = 0;
        }

        static::clear_history();
        return array('status' => $delete, 'historyid' => $historyid);
    }

    /**
     * Locks a columns
     *
     * @param int $id
     * @param bool $locked True to lock the column, false to unlock it.
     * @return array
     */
    public static function board_lock_column(int $id, bool $locked): array {
        global $DB, $USER;

        static::require_capability_for_column($id);
        $boardid = $DB->get_field('board_columns', 'boardid', array('id' => $id));

        $result = $DB->set_field('board_columns', 'locked', $locked, ['id' => $id]);
        $historyid = $DB->insert_record('board_history', array('boardid' => $boardid, 'action' => 'lock_column',
                                        'content' => json_encode(array('id' => $id, 'locked' => $locked)),
                                        'userid' => $USER->id, 'timecreated' => time()));
        return array('status' => $result, 'historyid' => $historyid);
    }

    /**
     * Triggers the delete column log.
     *
     * @param int $boardid
     * @param int $columnid
     * @return void
     */
    public static function board_delete_column_log($boardid, $columnid) {
        $event = \mod_board\event\delete_column::create(array(
            'objectid' => $columnid,
            'context' => \context_module::instance(static::coursemodule_for_board(static::get_board($boardid))->id)
        ));
        $event->trigger();
    }

    /**
     * Adds a capability check for the notes.
     *
     * @param int $id
     * @return void
     */
    public static function require_capability_for_note($id) {
        global $DB, $USER;

        if (!$note = $DB->get_record('board_notes', array('id' => $id))) {
            return false;
        }

        $context = static::context_for_column($note->columnid);
        if ($context) {
            require_capability('mod/board:post', $context);

            if ($USER->id != $note->userid) {
                require_capability('mod/board:manageboard', $context);
            }
        }
    }

    /**
     * Retrieves the file storage settings
     *
     * @param int $noteid
     * @return object
     */
    public static function get_file_storage_settings($noteid) {
        $note = static::get_note($noteid);
        if (!$note) {
            return null;
        }

        $column = static::get_column($note->columnid);
        if (!$column) {
            return null;
        }

        return (object) [
            'contextid' => static::context_for_board($column->boardid)->id,
            'component' => 'mod_board',
            'filearea'  => 'images',
            'itemid'    => $noteid,
            'filepath'  => '/'
        ];
    }

    /**
     * Retrieves the file added to a note.
     *
     * @param int $noteid
     * @return object
     */
    public static function get_note_file($noteid) {
        $note = static::get_note($noteid);
        if (!$note || empty($note->url)) {
            return null;
        }
        $file = static::get_file_storage_settings($noteid);
        $fs = get_file_storage();
        return $fs->get_file($file->contextid, $file->component, $file->filearea, $file->itemid,
                             $file->filepath, basename($note->url));
    }

    /**
     * Deletes the stored file.
     *
     * @param int $noteid
     * @return void
     */
    public static function delete_note_file($noteid) {
        $storedfile = static::get_note_file($noteid);
        if ($storedfile) {
            $storedfile->delete();
        }
    }

    /**
     * Stores the added file.
     *
     * @param int $noteid
     * @param int $draftitemid
     * @return string|null
     */
    public static function store_note_file($noteid, $draftitemid) {
        $settings = static::get_file_storage_settings($noteid);

        file_save_draft_area_files($draftitemid, $settings->contextid, $settings->component, $settings->filearea,
            $settings->itemid);

        $fs = get_file_storage();
        $files = $fs->get_area_files($settings->contextid, $settings->component, $settings->filearea, $settings->itemid,
            'itemid, filepath, filename', false);

        $storedfile = reset($files);
        if (!$storedfile) {
            // This means there is no file here.
            return null;
        }

        return \moodle_url::make_pluginfile_url($storedfile->get_contextid(), $storedfile->get_component(),
            $storedfile->get_filearea(), $storedfile->get_itemid(), $storedfile->get_filepath(),
            $storedfile->get_filename())->get_path();
    }

    /**
     * Updates the attachment.
     *
     * @param int $noteid
     * @param array $attachment
     * @param int|null $previoustype
     * @return array
     */
    public static function board_note_update_attachment($noteid, $attachment, $previoustype = null) {
        if (!empty($attachment['draftitemid'])) {
            $attachment['url'] = static::store_note_file($noteid, $attachment['draftitemid']);
            unset($attachment['draftitemid']);
        }

        if (empty($attachment['info']) && empty($attachment['url'])) {
            // In this case, we want to reset the media type to none.
            $attachment['type'] = 0;
            $attachment['info'] = null;
            $attachment['url'] = null;
        }

        if ($previoustype) {
            if (isset($attachment['type']) && $attachment['type'] != 2 && $previoustype == 2) {
                // This case is if we are changing from a picture type to a non-picture type. We should remove files.
                $fs = get_file_storage();
                $settings = static::get_file_storage_settings($noteid);

                $fs->delete_area_files($settings->contextid, $settings->component, $settings->filearea, $settings->itemid);
            }
        }

        return $attachment;
    }

    /**
     * Adds a note to the board
     *
     * @param int $columnid
     * @param int $ownerid
     * @param string $heading
     * @param string $content
     * @param array $attachment
     * @return array
     */
    public static function board_add_note(int $columnid, int $ownerid, string $heading, string $content, array $attachment): array {
        global $DB, $USER;

        $context = static::context_for_column($columnid);
        if ($context) {
            require_capability('mod/board:post', $context);
        }

        $heading = empty($heading) ? null : mb_substr($heading, 0, static::LENGTH_HEADING);
        $content = empty($content) ? "" : mb_substr($content, 0, get_config('mod_board', 'post_max_length'));
        $content = clean_text($content, FORMAT_HTML);

        $column = static::get_column($columnid);

        $boardid = $column->boardid;
        // Get the count of notes in the column to add to bottom of sort order.
        $countnotes = $DB->count_records('board_notes', ['columnid' => $columnid, 'deleted' => 0]);

        if ($boardid) {
            $cm = static::coursemodule_for_board(static::get_board($boardid));
            $groupid = groups_get_activity_group($cm, true) ?: null;
            static::require_access_for_group($groupid, $boardid);

            if (static::board_readonly($boardid)) {
                throw new \Exception('board_add_note not available');
            }
            if (!self::can_post($boardid, $USER->id, $ownerid)) {
                throw new \Exception('board_add_note not available');
            }
            $transaction = $DB->start_delegated_transaction();
            $type = !empty($attachment['type']) ? $attachment['type'] : 0;
            $info = !empty($type) ? mb_substr(s($attachment['info']), 0, static::LENGTH_INFO) : null;
            $url = !empty($type) ? mb_substr($attachment['url'], 0, static::LENGTH_URL) : null;

            $notecreated = time();
            $noteid = $DB->insert_record('board_notes', array('groupid' => $groupid, 'columnid' => $columnid, 'ownerid' => $ownerid,
                'heading' => $heading, 'content' => $content, 'type' => $type, 'info' => $info,
                'url' => $url, 'userid' => $USER->id, 'timecreated' => $notecreated,
                'sortorder' => $countnotes, 'deleted' => 0));

            $attachment = static::board_note_update_attachment($noteid, $attachment);
            $url = $attachment['url'];
            $DB->update_record('board_notes', array('id' => $noteid, 'url' => $url));

            $historyid = $DB->insert_record('board_history', array('boardid' => $boardid, 'groupid' => $groupid,
                'action' => 'add_note', 'ownerid' => $ownerid, 'userid' => $USER->id,
                'content' => json_encode(array('id' => $noteid, 'columnid' => $columnid,
                'heading' => $heading, 'content' => $content,
                'attachment' => array('type' => $type, 'info' => $info, 'url' => $url), 'rating' => 0,
                'timecreated' => $notecreated, 'sortorder' => $countnotes)),
                'timecreated' => time()));

            $DB->update_record('board', array('id' => $boardid, 'historyid' => $historyid));
            $transaction->allow_commit();

            $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
            $board = $DB->get_record('board', array('id' => $boardid), '*', MUST_EXIST);
            $completion = new \completion_info($course);
            if ($completion->is_enabled($cm) && $board->completionnotes) {
                $completion->update_state($cm);
            }

            static::board_add_note_log($boardid, $groupid, $heading, $content, $attachment, $columnid, $noteid);

            $note = static::get_note($noteid);
            $note->rating = 0;

        } else {
            $note = null;
            $historyid = 0;
        }

        static::clear_history();
        return array('status' => !empty($note), 'note' => $note, 'historyid' => $historyid);
    }

    /**
     * Triggers the add note log.
     *
     * @param int $boardid
     * @param int $groupid
     * @param string $heading
     * @param string $content
     * @param array $attachment
     * @param int $columnid
     * @param int $noteid
     * @return void
     */
    public static function board_add_note_log($boardid, $groupid, $heading, $content, $attachment, $columnid, $noteid) {
        if (!get_config('mod_board', 'addnotetolog')) {
            $content = '';
        }
        if (!get_config('mod_board', 'addheadingtolog')) {
            $heading = '';
        }
        if (!get_config('mod_board', 'addattachmenttolog')) {
            $attachment = '';
        }
        $event = \mod_board\event\add_note::create(array(
            'objectid' => $noteid,
            'context' => \context_module::instance(static::coursemodule_for_board(static::get_board($boardid))->id),
            'other' => array('groupid' => $groupid, 'columnid' => $columnid, 'heading' => $heading,
                            'content' => $content, 'attachment' => $attachment)
        ));
        $event->trigger();
    }

    /**
     * Updates a note.
     *
     * @param int $id
     * @param int $ownerid
     * @param string $heading
     * @param string $content
     * @param array $attachment
     * @return array
     */
    public static function board_update_note(int $id, int $ownerid, string $heading, string $content, array $attachment): array {
        global $DB, $USER;

        static::require_capability_for_note($id);

        $heading = empty($heading) ? null : mb_substr($heading, 0, static::LENGTH_HEADING);
        $content = empty($content) ? "" : mb_substr($content, 0, get_config('mod_board', 'post_max_length'));
        $content = clean_text($content, FORMAT_HTML);

        $note = static::get_note($id);
        $columnid = $note->columnid;
        $boardid = $DB->get_field('board_columns', 'boardid', array('id' => $columnid));

        if (!empty($note->groupid)) {
            static::require_access_for_group($note->groupid, $boardid);
        }

        if (static::board_readonly($boardid)) {
            throw new \Exception('board_update_note not available');
        }

        if ($columnid && $boardid) {
            $transaction = $DB->start_delegated_transaction();
            $previoustype = $note->type;
            $attachment = static::board_note_update_attachment($id, $attachment, $previoustype);

            $type = !empty($attachment['type']) ? $attachment['type'] : 0;
            $info = !empty($type) ? mb_substr(s($attachment['info']), 0, static::LENGTH_INFO) : null;
            $url = !empty($type) ? mb_substr($attachment['url'], 0, static::LENGTH_URL) : null;

            $historyid = $DB->insert_record('board_history', array('boardid' => $boardid, 'action' => 'update_note',
                'ownerid' => $ownerid, 'userid' => $USER->id, 'content' => json_encode(array('id' => $id,
                'columnid' => $columnid, 'heading' => $heading, 'content' => $content,
                'attachment' => array('type' => $type, 'info' => $info, 'url' => $url))),
                'timecreated' => time()));
            $update = $DB->update_record('board_notes', array('id' => $id, 'heading' => $heading, 'content' => $content,
                'type' => $type, 'info' => $info, 'url' => $url));
            $DB->update_record('board', array('id' => $boardid, 'historyid' => $historyid));

            $transaction->allow_commit();

            static::board_update_note_log($boardid, $heading, $content, $attachment, $columnid, $id);

            $note = static::get_note($id);
        } else {
            $note = null;
            $update = false;
            $historyid = 0;
        }

        static::clear_history();
        return array('status' => $update, 'note' => $note, 'historyid' => $historyid);
    }

    /**
     * Triggers the update note log.
     *
     * @param int $boardid
     * @param string $heading
     * @param string $content
     * @param array $attachment
     * @param int $columnid
     * @param int $noteid
     * @return void
     */
    public static function board_update_note_log($boardid, $heading, $content, $attachment, $columnid, $noteid) {
        if (!get_config('mod_board', 'addnotetolog')) {
            $content = '';
        }
        if (!get_config('mod_board', 'addheadingtolog')) {
            $heading = '';
        }
        if (!get_config('mod_board', 'addattachmenttolog')) {
            $attachment = '';
        }
        $event = \mod_board\event\update_note::create(array(
            'objectid' => $noteid,
            'context' => \context_module::instance(static::coursemodule_for_board(static::get_board($boardid))->id),
            'other' => array('columnid' => $columnid, 'heading' => $heading, 'content' => $content, 'attachment' => $attachment)
        ));
        $event->trigger();
    }

    /**
     * Deletes a note from the board.
     *
     * @param int $id
     * @return array
     */
    public static function board_delete_note(int $id): array {
        global $DB, $USER;

        static::require_capability_for_note($id);

        $note = static::get_note($id);
        $sortorder = $note->sortorder;
        $columnid = $note->columnid;
        $boardid = $DB->get_field('board_columns', 'boardid', array('id' => $columnid));

        if (!empty($note->groupid)) {
            static::require_access_for_group($note->groupid, $boardid);
        }

        if (static::board_readonly($boardid)) {
            throw new \Exception('board_delete_note not available');
        }

        if ($columnid && $boardid) {

            $deleteratings = $DB->delete_records('board_note_ratings', array('noteid' => $note->id));
            static::delete_note_file($note->id);

            // Delete all note comments.
            $commentrecords = $DB->get_records('board_comments', array('noteid' => $note->id));
            foreach ($commentrecords as $commentrecord) {
                $comment = new \mod_board\comment(['commentid' => $commentrecord->id]);
                $comment->delete();
            }

            $transaction = $DB->start_delegated_transaction();
            $delete = $DB->update_record('board_notes', array('id' => $id, 'deleted' => 1));
            $historyid = $DB->insert_record('board_history', array('boardid' => $boardid, 'action' => 'delete_note',
                'ownerid' => 0, 'content' => json_encode(array('id' => $id, 'columnid' => $columnid)),
                'userid' => $USER->id, 'timecreated' => time()));

            $sql = "UPDATE {board_notes} bn
                       SET sortorder = sortorder - 1
                     WHERE sortorder > :sortorder AND columnid = :columnid";
            $DB->execute($sql, ['sortorder' => $sortorder, 'columnid' => $columnid]);

            $DB->update_record('board', array('id' => $boardid, 'historyid' => $historyid));
            $transaction->allow_commit();

            static::board_delete_note_log($boardid, $columnid, $id);
        } else {
            $delete = false;
            $historyid = 0;
        }
        static::clear_history();
        return array('status' => $delete, 'historyid' => $historyid);
    }

    /**
     * Triggers the delete note log.
     *
     * @param int $boardid
     * @param int $columnid
     * @param int $noteid
     * @return void
     */
    public static function board_delete_note_log($boardid, $columnid, $noteid) {
        $event = \mod_board\event\delete_note::create(array(
            'objectid' => $noteid,
            'context' => \context_module::instance(static::coursemodule_for_board(static::get_board($boardid))->id),
            'other' => array('columnid' => $columnid)
        ));
        $event->trigger();
    }

    /**
     * Moves a column to a new position.
     *
     * @param int $id the column id
     * @param int $sortorder the new sortorder
     */
    public static function board_move_column(int $id, int $sortorder): array {
        global $DB, $USER;

        $column = static::get_column($id);
        $columns = $DB->get_records('board_columns', ['boardid' => $column->boardid], 'sortorder ASC, id ASC');
        self::repositionan_array_element($columns, $id, $sortorder);
        $sortorder = 1;
        $neworder = [];
        foreach ($columns as $column) {
            $column->sortorder = $sortorder++;
            $neworder[] = $column->id;
            $DB->update_record('board_columns', $column);
        }
        $historyid = $DB->insert_record('board_history', [
            'boardid' => $column->boardid, 'action' => 'move_column',
            'content' => json_encode(['sortorder' => $neworder]),
            'userid' => $USER->id, 'timecreated' => time()]);
        return ['status' => 1, 'historyid' => $historyid];

    }

    /**
     * Reposition an array element by its key.
     *
     * @param array      $array The array being reordered.
     * @param string|int $key They key of the element you want to reposition.
     * @param int        $order The position in the array you want to move the element to. (0 is first)
     *
     * @throws \Exception
     */
    private static function repositionan_array_element(array &$array, $key, int $order): void {
        if (($a = array_search($key, array_keys($array))) === false) {
            throw new \Exception("The {$key} cannot be found in the given array.");
        }
        $p1 = array_splice($array, $a, 1);
        $p2 = array_splice($array, 0, $order);
        $array = array_merge($p2, $p1, $array);
    }

    /**
     * Moves a note to a different column
     *
     * @param int $id
     * @param int $ownerid
     * @param int $columnid
     * @param int $sortorder The order in the column the note was placed.
     * @return array
     */
    public static function board_move_note(int $id, int $ownerid, int $columnid, int $sortorder): array {
        global $DB, $USER;

        $note = static::get_note($id);
        $boardid = $DB->get_field('board_columns', 'boardid', array('id' => $columnid));

        if (!static::board_users_can_edit($boardid) && $USER->id != $note->userid) {
            static::require_capability_for_column($note->columnid);
        }

        if ($columnid && $boardid) {

            $transaction = $DB->start_delegated_transaction();

            $DB->insert_record('board_history', array('boardid' => $boardid, 'action' => 'delete_note',
                'content' => json_encode(array('id' => $note->id, 'columnid' => $note->columnid)),
                'ownerid' => $ownerid, 'userid' => $USER->id, 'timecreated' => time()));
            $historyid = $DB->insert_record('board_history', array('boardid' => $boardid, 'groupid' => $note->groupid,
                'action' => 'add_note', 'userid' => $note->userid, 'ownerid' => $ownerid,
                'content' => json_encode(array('id' => $note->id, 'columnid' => $columnid,
                'heading' => $note->heading, 'content' => $note->content,
                'attachment' => array('type' => $note->type, 'info' => $note->info,
                'url' => $note->url), 'timecreated' => $note->timecreated,
                'rating' => static::get_note_rating($note->id), 'sortorder' => $sortorder)),
                'timecreated' => time()));
            // Checking if we move the note up or down.
            $ismovingup = $note->sortorder < $sortorder;
            $ismovingdown = $note->sortorder > $sortorder;
            $issamecolumn = $columnid == $note->columnid;
            // Check whether it is the same column and then increment or decrement notes above or below
            // the set sortorder according to whether the sortorder has moved up or down.
            if ($issamecolumn) {
                $params = ['newsort' => $sortorder, 'oldsort' => $note->sortorder, 'columnid' => $columnid];
                if ($ismovingup) {
                    $sql = "UPDATE {board_notes} bn
                               SET sortorder = bn.sortorder - 1
                             WHERE sortorder <= :newsort
                                   AND sortorder >= :oldsort
                                   AND columnid = :columnid";
                    $DB->execute($sql, $params);
                } else if ($ismovingdown) {
                    $sql = "UPDATE {board_notes} bn
                               SET sortorder = bn.sortorder + 1
                             WHERE sortorder >= :newsort
                                   AND sortorder <= :oldsort
                                   AND columnid = :columnid";
                    $DB->execute($sql, $params);
                }
            } else {
                // Increment the new column notes to fit the moved note.
                $sql = "UPDATE {board_notes} bn
                           SET sortorder = bn.sortorder + 1
                         WHERE sortorder >= :newsort AND columnid = :columnid";
                $DB->execute($sql, ['newsort' => $sortorder, 'columnid' => $columnid]);
                // Decrement the old column notes above where the moved note left.
                $sql = "UPDATE {board_notes} bn
                           SET sortorder = sortorder - 1
                         WHERE sortorder > :oldsort AND columnid = :columnid";
                $DB->execute($sql, ['oldsort' => $note->sortorder, 'columnid' => $note->columnid]);
            }
            // Update the note record.
            $note->columnid = $columnid;
            $note->sortorder = $sortorder;
            $move = $DB->update_record('board_notes', $note);

            $DB->update_record('board', array('id' => $boardid, 'historyid' => $historyid));
            $transaction->allow_commit();

            static::board_move_note_log($boardid, $columnid, $id);
        } else {
            $move = false;
            $historyid = 0;
        }
        static::clear_history();
        return array('status' => $move, 'historyid' => $historyid);
    }

    /**
     * Triggers the move note log.
     *
     * @param int $boardid
     * @param int $columnid
     * @param int $noteid
     * @return void
     */
    public static function board_move_note_log($boardid, $columnid, $noteid) {
        $event = \mod_board\event\move_note::create(array(
            'objectid' => $noteid,
            'context' => \context_module::instance(static::coursemodule_for_board(static::get_board($boardid))->id),
            'other' => array('columnid' => $columnid)
        ));
        $event->trigger();
    }

    /**
     * Checks to see if the user can rate the note.
     *
     * @param int $noteid
     * @return array [canrate, hasrated]
     */
    public static function board_can_rate_note(int $noteid): array {
        global $DB, $USER;

        $hasrated = $DB->record_exists('board_note_ratings', array('userid' => $USER->id, 'noteid' => $noteid));

        $result = ['canrate' => false, 'hasrated' => $hasrated];

        $note = static::get_note($noteid);
        if (!$note) {
            return $result;
        }

        $column = static::get_column($note->columnid);
        if (!$column) {
            return $result;
        }

        $board = static::get_board($column->boardid);
        if (!$board) {
            return $result;
        }

        if (!static::board_rating_enabled($board->id)) {
            return $result;
        }

        if (static::board_readonly($board->id)) {
            return $result;
        }

        $context = static::context_for_board($board->id);
        if (!has_capability('mod/board:post', $context)) {
            return $result;
        }

        $iseditor = has_capability('mod/board:manageboard', $context);

        if ($board->addrating == self::RATINGBYSTUDENTS && $iseditor) {
            return $result;
        }

        if ($board->addrating == self::RATINGBYTEACHERS && !$iseditor) {
            return $result;
        }

        return ['canrate' => true, 'hasrated' => $hasrated];
    }

    /**
     * Checks to see if rating has been enabled for the board.
     *
     * @param int $boardid
     * @return bool
     */
    public static function board_rating_enabled($boardid) {
        $board = static::get_board($boardid);
        if (!$board) {
            return false;
        }

        return !empty($board->addrating);
    }

    /**
     * Rates the note
     *
     * @param int $noteid
     * @return array
     */
    public static function board_rate_note(int $noteid): array {
        global $DB, $USER;

        $return = ['status' => false];
        $note = static::get_note($noteid);
        if (!$note) {
            return $return;
        }

        $column = static::get_column($note->columnid);
        if (!$column) {
            return $return;
        }

        $boardid = $column->boardid;
        if (!static::board_can_rate_note($noteid)['canrate']) {
            return $return;
        }
        if (static::board_readonly($boardid)) {
            return $return;
        }

        if ($note) {
            $transaction = $DB->start_delegated_transaction();
            $hasrating = $DB->record_exists('board_note_ratings', array('userid' => $USER->id, 'noteid' => $noteid));
            $action = $hasrating ? 'delete_note_rating' : 'add_note_rating';
            if ($hasrating) {
                $DB->delete_records('board_note_ratings', array('userid' => $USER->id, 'noteid' => $noteid));
            } else {
                $DB->insert_record('board_note_ratings', array('userid' => $USER->id, 'noteid' => $noteid,
                    'timecreated' => time()));
            }
            $rate = true;
            $rating = static::get_note_rating($noteid);
            $historyid = $DB->insert_record('board_history', array('boardid' => $boardid, 'action' => $action,
                                            'content' => json_encode(array('id' => $note->id, 'rating' => $rating)),
                                            'userid' => $USER->id, 'timecreated' => time()));

            $DB->update_record('board', array('id' => $boardid, 'historyid' => $historyid));

            $transaction->allow_commit();

            static::board_rate_note_log($boardid, $noteid, $rating);
        } else {
            $rate = false;
            $rating = 0;
            $historyid = 0;
        }
        static::clear_history();
        return array('status' => $rate, 'rating' => $rating, 'historyid' => $historyid);
    }

    /**
     * Triggers the rate note log.
     *
     * @param int $boardid
     * @param int $noteid
     * @param int $rating
     * @return void
     */
    public static function board_rate_note_log($boardid, $noteid, $rating) {
        if (!get_config('mod_board', 'addratingtolog')) {
            $rating = '';
        }
        $event = \mod_board\event\rate_note::create(array(
            'objectid' => $noteid,
            'context' => \context_module::instance(static::coursemodule_for_board(static::get_board($boardid))->id),
            'other' => array('rating' => $rating)
        ));
        $event->trigger();
    }

    /**
     * Checks if the user can access all groups.
     *
     * @param mixed $context
     * @return boolean
     */
    public static function can_access_all_groups($context) {
        return has_capability('moodle/site:accessallgroups', $context);
    }

    /**
     * Checks if the user can access a specific group.
     *
     * @param int $groupid
     * @param mixed $context
     * @return boolean
     */
    public static function can_access_group($groupid, $context) {
        global $USER;

        if (static::can_access_all_groups($context)) {
            return true;
        }

        return groups_is_member($groupid);
    }

    /**
     * Checks if the user can edit the board.
     *
     * @param int $boardid
     * @return bool
     */
    public static function board_is_editor($boardid) {
        $context = static::context_for_board($boardid);
        return has_capability('mod/board:manageboard', $context);
    }

    /**
     * Asserts whether users may edit their own note placement on
     * a particular board.
     *
     * @param int $boardid
     * @return boolean
     */
    public static function board_users_can_edit($boardid) {
        global $DB;

        $context = static::context_for_board($boardid);
        if (!has_capability('mod/board:post', $context)) {
            // The user is not allowed to post via capabilities.
            return false;
        }

        return $DB->get_field('board', 'userscanedit', ['id' => $boardid], IGNORE_MISSING);
    }

    /**
     * Checks if the user can only view the board
     *
     * @param int $boardid
     * @return mixed
     */
    public static function board_readonly($boardid) {
        if (!$board = static::get_board($boardid)) {
            return false;
        }

        $iseditor = static::board_is_editor($boardid);
        $cm = static::coursemodule_for_board($board);
        $context = static::context_for_board($boardid);
        $groupmode = groups_get_activity_groupmode($cm);
        $postbyoverdue = !empty($board->postby) && time() > $board->postby;

        $readonlyboard = !$iseditor && (($groupmode == VISIBLEGROUPS &&
                         !static::can_access_group(groups_get_activity_group($cm, true),
        $context)) || $postbyoverdue);

        return $readonlyboard;
    }

    /**
     * Prepares board notes for export.
     * @param object $note
     * @return string
     */
    public static function get_export_note($note) {
        $breaks = array("<br />", "<br>", "<br/>");

        $rowstring = '';
        if (!empty($note->heading)) {
            $rowstring .= $note->heading;
        }
        if (!empty($note->content)) {
            if (!empty($rowstring)) {
                $rowstring .= "\n";
            }
            $rowstring .= str_ireplace($breaks, "\n", $note->content);
        }
        if (!empty($note->type)) {
            if (!empty($rowstring)) {
                $rowstring .= "\n";
            }
            $rowstring .= (!empty($note->info) ? ($note->info.' ') : '') . $note->url;
        }
        return $rowstring;
    }

    /**
     * Prepares submissions for export.
     * @param string $content
     * @return array|string|string[]
     */
    public static function get_export_submission(string $content) {
        $breaks = array("<br />", "<br>", "<br/>");
        return str_ireplace($breaks, "\n", $content);
    }

    /**
     * Returns basic options for the image file picker.
     *
     * @return array
     */
    public static function get_image_picker_options() {
        $extensions = self::get_accepted_file_extensions();

        $extensions = array_map(function($extension) {
            return '.' . $extension;
        }, $extensions);

        return [
            'accepted_types' => $extensions,
            'maxfiles' => 1,
            'subdirs' => 0,
            'maxbytes' => self::ACCEPTED_FILE_MAX_SIZE
        ];
    }

    /**
     * Gets the available column colours in order or the backup
     * colours if the config is not set.
     * @return string[] An array of hex colour strings.
     */
    public static function get_column_colours() {
        $colours = explode(PHP_EOL, get_config('mod_board', 'column_colours'));
        foreach ($colours as $index => $colour) {
            $colours[$index] = trim($colour,  "\t\n\r\0\x0B#");
            $matched = preg_match('/\b[A-Fa-f0-9]{6}\b|\b[A-Fa-f0-9]{3}\b/', $colours[$index]);
            if ($matched != 1) {
                // One hex was wrong, use the default.
                return self::get_default_colours();
            }
        }
        return $colours;
    }

    /**
     * Returns a single string containing the 7 default colours for
     * column headings.
     * @return string[]
     */
    public static function get_default_colours() {
        return ["1B998B", "2D3047", "FFFD82", "FF9B71", "E84855", "AF9BB6", "F18F01"];
    }

    /**
     * Get the users you can view if the board is set to single user with public posts.
     * @param int $boardid the board id.
     * @param int $groupid the group id.
     * @return array the users.
     */
    public static function get_users_for_board($boardid, $groupid = 0): array {
        if ($groupid) {
            static::require_access_for_group($groupid, $boardid);
            $userlist = groups_get_members($groupid,
            'u.id, u.lastname, u.firstname, u.firstnamephonetic, u.lastnamephonetic, u.middlename, u.alternatename',
            'lastname ASC, firstname ASC');
        } else {
            $userlist = get_enrolled_users(static::context_for_board($boardid), 'mod/board:view', 0, 'u.id,
                u.lastname, u.firstname, u.firstnamephonetic, u.lastnamephonetic, u.middlename, u.alternatename');
        }
        $users = [];
        foreach ($userlist as $user) {
            $users[$user->id] = fullname($user);
        }
        return $users;
    }

    /**
     * Check if you can view the notes for this user.
     * @param int $boardid the board id.
     * @param int $userid the user id.
     * @return bool true if you can view the notes, false otherwise.
     */
    public static function can_view_user($boardid, $userid): bool {
        global $USER;

        $board = static::get_board($boardid);
        $context = static::context_for_board($boardid);
        if (has_capability('mod/board:manageboard', $context)) {
            return true;
        }
        if ($board->singleusermode == self::SINGLEUSER_PUBLIC) {
            return true;
        }
        if ($board->singleusermode == self::SINGLEUSER_PRIVATE && $USER->id == $userid) {
            return true;
        }
        return false;
    }

    /**
     * Check if the user can post on this board
     *
     * @param int $boardid the board id.
     * @param int $userid the user id.
     * @param int $ownerid the board owner id.
     */
    public static function can_post(int $boardid, int $userid, int $ownerid): bool {
        global $USER;

        $context = static::context_for_board($boardid);
        if ($userid == $ownerid && has_capability('mod/board:post', $context)) {
            return true;
        }
        $board = static::get_board($boardid);
        $context = static::context_for_board($boardid);
        if (has_capability('mod/board:manageboard', $context) &&
            ($board->singleusermode == self::SINGLEUSER_PUBLIC ||
            $board->singleusermode == self::SINGLEUSER_PRIVATE)
            ) {
            return true;
        }
        return false;
    }
}
