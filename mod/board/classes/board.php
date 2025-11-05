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

use stdClass;

/**
 * The main board class functions.
 * @package     mod_board
 * @author      Jay Churchward <jay@brickfieldlabs.ie>
 * @copyright   2021 Brickfield Education Labs <https://www.brickfield.ie/>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class board {
    /** @var int Maximum file size of 10Mb. */
    const ACCEPTED_FILE_MAX_SIZE = 1024 * 1024 * 10;

    /** @var int Value for the max column name length, consistent with db */
    const LENGTH_COLNAME = 100;

    /** @var int Value for the max heading length, consistent with db */
    const LENGTH_HEADING = 100;

    /** @var int Value for the max info length, consistent with db */
    const LENGTH_INFO = 100;

    /** @var int Value for the max url length, consistent with db */
    const LENGTH_URL = 1333;

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

    /** @var int no additional media */
    const MEDIATYPE_NONE = 0;

    /** @var int YouTube video */
    const MEDIATYPE_YOUTUBE = 1;

    /** @var int uploaded image */
    const MEDIATYPE_IMAGE = 2;

    /** @var int general URL */
    const MEDIATYPE_URL = 3;

    /** @var int general uploaded file */
    const MEDIATYPE_FILE = 4;

    /**
     * Retrieves the course module for the board
     *
     * @param stdClass $board
     * @return stdClass
     */
    public static function coursemodule_for_board(stdClass $board): stdClass {
        return get_coursemodule_from_instance('board', $board->id, $board->course, false, MUST_EXIST);
    }

    /**
     * Retrieves a record of the selected board.
     *
     * @param int $id
     * @param int $strictness IGNORE_MISSING or MUST_EXIST
     * @return stdClass|null board record with extra cmid property
     */
    public static function get_board(int $id, int $strictness = IGNORE_MISSING): ?stdClass {
        global $DB;
        $sql = "SELECT b.*, cm.id AS cmid
                  FROM {board} b
                  JOIN {course_modules} cm ON cm.instance = b.id
                  JOIN {modules} md ON md.id = cm.module AND md.name = 'board'
                 WHERE b.id = :id";

        $result = $DB->get_record_sql($sql, ['id' => $id], $strictness);
        if ($result === false) {
            $result = null;
        }
        return $result;
    }

    /**
     * Retrieves a record of board for given column id.
     *
     * @param int $columnid
     * @param int $strictness IGNORE_MISSING or MUST_EXIST
     * @return stdClass|null board record with extra cmid property
     */
    public static function get_board_for_columnid(int $columnid, int $strictness = IGNORE_MISSING): ?stdClass {
        global $DB;
        $sql = "SELECT b.*, cm.id AS cmid
                  FROM {board} b
                  JOIN {course_modules} cm ON cm.instance = b.id
                  JOIN {modules} md ON md.id = cm.module AND md.name = 'board'
                  JOIN {board_columns} c ON c.boardid = b.id
                 WHERE c.id = :columnid";

        $result = $DB->get_record_sql($sql, ['columnid' => $columnid], $strictness);
        if ($result === false) {
            $result = null;
        }
        return $result;
    }

    /**
     * Retrieves a record of board for given note id.
     *
     * NOTE: deleted notes are ignored
     *
     * @param int $noteid
     * @param int $strictness IGNORE_MISSING or MUST_EXIST
     * @return stdClass|null board record with extra cmid property
     */
    public static function get_board_for_noteid(int $noteid, int $strictness = IGNORE_MISSING): ?stdClass {
        global $DB;
        $sql = "SELECT b.*, cm.id AS cmid
                  FROM {board} b
                  JOIN {course_modules} cm ON cm.instance = b.id
                  JOIN {modules} md ON md.id = cm.module AND md.name = 'board'
                  JOIN {board_columns} c ON c.boardid = b.id
                  JOIN {board_notes} n ON n.columnid = c.id AND n.deleted = 0
                 WHERE n.id = :noteid";

        $result = $DB->get_record_sql($sql, ['noteid' => $noteid], $strictness);
        if ($result === false) {
            $result = null;
        }
        return $result;
    }

    /**
     * Retrieves a record of the selected column.
     *
     * @param int $id
     * @param int $strictness IGNORE_MISSING or MUST_EXIST
     * @return stdClass|null
     */
    public static function get_column(int $id, int $strictness = IGNORE_MISSING): ?stdClass {
        global $DB;
        $result = $DB->get_record('board_columns', ['id' => $id], '*', $strictness);
        if ($result === false) {
            $result = null;
        }
        return $result;
    }

    /**
     * Retrieves a record of the selected note.
     *
     * NOTE: deleted notes are ignored
     *
     * @param int $id
     * @param int $strictness IGNORE_MISSING or MUST_EXIST
     * @return stdClass|null
     */
    public static function get_note(int $id, int $strictness = IGNORE_MISSING): ?stdClass {
        global $DB;
        $result = $DB->get_record('board_notes', ['id' => $id, 'deleted' => 0], '*', $strictness);
        if ($result === false) {
            $result = null;
        }
        return $result;
    }

    /**
     * Retrieves a record of the selected template.
     *
     * @param int $id
     * @param int $strictness IGNORE_MISSING or MUST_EXIST
     * @return stdClass|null
     */
    public static function get_template(int $id, int $strictness = IGNORE_MISSING): ?stdClass {
        global $DB;
        $result = $DB->get_record('board_templates', ['id' => $id], '*', $strictness);
        if ($result === false) {
            $result = null;
        }
        return $result;
    }

    /**
     * Retrieves the context of the selected board.
     *
     * @param int|stdClass $boardorid
     * @return \context_module
     */
    public static function context_for_board(int|stdClass $boardorid): \context {
        if (is_object($boardorid)) {
            $board = $boardorid;
            if (!isset($board->id) || !isset($board->course)) {
                throw new \core\exception\coding_exception('invalid board record');
            }
            if (!isset($board->cmid)) {
                $board = self::get_board($board->id, MUST_EXIST);
            }
        } else {
            $board = self::get_board($boardorid, MUST_EXIST);
        }
        return \context_module::instance($board->cmid);
    }

    /**
     * Retrieves the context of the selected column.
     *
     * @param int|stdClass $columnorid
     * @return \context_module
     */
    public static function context_for_column(int|stdClass $columnorid): \context {
        if (is_object($columnorid)) {
            $board = self::get_board($columnorid->boardid);
        } else {
            $board = self::get_board_for_columnid($columnorid, MUST_EXIST);
        }
        return \context_module::instance($board->cmid);
    }

    /**
     * Requires the users to be in groups.
     *
     * @param stdClass $board
     * @param int $groupid
     * @return void
     */
    public static function require_access_for_group(stdClass $board, int $groupid): void {
        if (!$groupid) {
            debugging('groupid expected', DEBUG_DEVELOPER);
        }

        $context = self::context_for_board($board);
        if (has_capability('mod/board:manageboard', $context)) {
            return;
        }

        $cm = self::coursemodule_for_board($board);
        $groupmode = groups_get_activity_groupmode($cm);
        if ($groupmode == NOGROUPS) {
            return;
        }

        if (!self::can_access_group($groupid, $context)) {
            require_capability('moodle/site:accessallgroups', $context);
        }
    }

    /**
     * Can current user view the note?
     *
     * NOTE: deleted notes are not visible
     * NOTE: this has to be used after require_login() because access restriction is not checked
     *
     * @param stdClass $note
     * @return \context|null null means user cannot view the note
     */
    public static function can_view_note(stdClass $note): ?\context {
        global $USER;

        $board = self::get_board_for_noteid($note->id, MUST_EXIST);
        $context = self::context_for_board($board);

        if (!has_capability('mod/board:view', $context)) {
            return null;
        }

        if (!has_capability('mod/board:manageboard', $context)) {
            if ($note->deleted) {
                return null;
            }

            if ($board->singleusermode == self::SINGLEUSER_PRIVATE) {
                if (!$USER->id) {
                    return null;
                }
                if ($note->userid != $USER->id && $note->ownerid != $USER->id) {
                    return null;
                }
            }

            if ($note->groupid) {
                $cm = self::coursemodule_for_board($board);
                $groupmode = groups_get_activity_groupmode($cm);
                if ($groupmode == SEPARATEGROUPS) {
                    if (!self::can_access_group($note->groupid, $context)) {
                        return null;
                    }
                }
            }
        }

        return $context;
    }

    /**
     * Clears the records in the history table for the last minute.
     *
     * @return bool
     */
    public static function clear_history() {
        global $DB;

        return $DB->delete_records_select(
            'board_history',
            'timecreated < :timecreated',
            ['timecreated' => time() - 60]
        ); // 1 minute history
    }

    /**
     * Hides the headers of the board.
     *
     * @param stdClass $board
     * @return bool
     */
    public static function board_hide_headers(stdClass $board): bool {
        if (!$board->hideheaders) {
            return false;
        }

        $context = self::context_for_board($board);
        $iseditor = has_capability('mod/board:manageboard', $context);
        return !$iseditor;
    }

    /**
     * Check if there are any notes on this board.
     *
     * @param int $boardid
     * @return bool true if there are notes.
     */
    public static function board_has_notes(int $boardid): bool {
        global $DB;
        $sql = "SELECT 'x'
                  FROM {board_notes} bn
                  JOIN {board_columns} bc ON bc.id = bn.columnid
                 WHERE bc.boardid = :boardid AND bn.deleted = 0";
        return $DB->record_exists_sql($sql, ['boardid' => $boardid]);
    }

    /**
     * Reposition an array element by its key.
     *
     * @param array      $array The array being reordered.
     * @param string|int $key They key of the element you want to reposition.
     * @param int        $order The position in the array you want to move the element to. (0 is first)
     */
    public static function repositionan_array_element(array &$array, $key, int $order): void {
        if (($a = array_search($key, array_keys($array))) === false) {
            throw new \core\exception\invalid_parameter_exception("The {$key} cannot be found in the given array.");
        }
        $p1 = array_splice($array, $a, 1);
        $p2 = array_splice($array, 0, $order);
        $array = array_merge($p2, $p1, $array);
    }

    /**
     * Checks to see if rating has been enabled for the board.
     *
     * @param stdClass $board
     * @return bool
     */
    public static function board_rating_enabled(stdClass $board): bool {
        return ($board->addrating != self::RATINGDISABLED);
    }

    /**
     * Checks if the user can access all groups.
     *
     * @param \context $context
     * @return bool
     */
    public static function can_access_all_groups(\context $context): bool {
        return has_capability('moodle/site:accessallgroups', $context);
    }

    /**
     * Checks if the user can access a specific group.
     *
     * @param int $groupid
     * @param \context $context
     * @return bool
     */
    public static function can_access_group(int $groupid, \context $context): bool {
        if (self::can_access_all_groups($context)) {
            return true;
        }

        return groups_is_member($groupid);
    }

    /**
     * Checks if the user can edit the board.
     *
     * @param stdClass $board
     * @return bool
     */
    public static function board_is_editor(stdClass $board): bool {
        $context = self::context_for_board($board);
        return has_capability('mod/board:manageboard', $context);
    }

    /**
     * Asserts whether users may edit their own note placement on
     * a particular board.
     *
     * @param stdClass $board
     * @return boolean
     */
    public static function board_users_can_edit(stdClass $board): bool {
        if (!$board->userscanedit) {
            return false;
        }

        $context = self::context_for_board($board);
        return has_capability('mod/board:post', $context);
    }

    /**
     * Checks if the user can only view the board.
     *
     * @param stdClass $board
     * @param int|null $groupid
     * @return mixed
     */
    public static function board_readonly(stdClass $board, ?int $groupid): bool {
        $context = self::context_for_board($board);

        $cm = self::coursemodule_for_board($board);
        $groupmode = groups_get_activity_groupmode($cm);

        $iseditor = self::board_is_editor($board);
        $postbyoverdue = !empty($board->postby) && time() > $board->postby;

        $readonlyboard = !$iseditor && (($groupmode != NOGROUPS && $board->singleusermode == self::SINGLEUSER_DISABLED
                            && !self::can_access_group((int)$groupid, $context)) || $postbyoverdue);

        return $readonlyboard;
    }

    /**
     * Gets the available column colours in order or the backup
     * colours if the config is not set.
     *
     * @return string[] An array of hex colour strings.
     */
    public static function get_column_colours(): array {
        $colours = explode(PHP_EOL, get_config('mod_board', 'column_colours'));
        foreach ($colours as $index => $colour) {
            $colours[$index] = trim($colour, "\t\n\r\0\x0B#");
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
     *
     * @return string[]
     */
    public static function get_default_colours(): array {
        return ["1B998B", "2D3047", "FFFD82", "FF9B71", "E84855", "AF9BB6", "F18F01"];
    }

    /**
     * Get the users you can view if the board is set to single user with public or private mode.
     *
     * NOTE: this is meant for the mod/board/view.php page only
     *
     * @param stdClass $board the board id.
     * @param int|null $groupid the group id.
     * @return array the users.
     */
    public static function get_users_for_board(stdClass $board, ?int $groupid = 0): array {
        global $DB;

        if ($groupid) {
            $groups[] = $groupid;
        } else {
            $groups = 0;
        }
        $context = self::context_for_board($board);
        $userlist = get_enrolled_users(
            $context,
            'mod/board:view',
            $groups,
            // phpcs:ignore moodle.Files.LineLength.TooLong
            'u.id, u.lastname, u.firstname, u.firstnamephonetic, u.lastnamephonetic, u.middlename, u.alternatename, u.suspended, u.confirmed',
            onlyactive: true
        );
        foreach ($userlist as $k => $user) {
            if ($user->suspended || !$user->confirmed) {
                unset($userlist[$k]);
            }
        }

        $course = $DB->get_record('course', ['id' => $board->course], '*', MUST_EXIST);
        $modinfo = get_fast_modinfo($course);
        $cm = $modinfo->get_cm($board->cmid);
        $info = new \core_availability\info_module($cm);
        $userlist = $info->filter_user_list($userlist);

        $users = [];
        foreach ($userlist as $user) {
            $users[$user->id] = fullname($user);
        }

        return $users;
    }

    /**
     * Get the owners with posts in single user with public or private mode.
     *
     * NOTE: this is meant for the mod/board/export.php page only
     * NOTE: deleted users are visible here
     *
     * @param stdClass $board the board id.
     * @param int $groupid the group id, 0 means all participants.
     * @param bool $onlycomments
     * @return array the users.
     */
    public static function get_existing_owners_for_board(stdClass $board, int $groupid, bool $onlycomments): array {
        global $DB;

        if ($board->singleusermode != self::SINGLEUSER_PUBLIC && $board->singleusermode != self::SINGLEUSER_PRIVATE) {
            throw new \core\exception\coding_exception('get_existing_owners_for_board can be used only in singleusemode');
        }

        $params = [
            'boardid' => $board->id,
        ];
        // phpcs:ignore moodle.Files.LineLength.TooLong
        $sql = "SELECT DISTINCT u.id, u.lastname, u.firstname, u.firstnamephonetic, u.lastnamephonetic, u.middlename, u.alternatename
                  FROM {user} u
                  JOIN {board_notes} bn ON bn.ownerid = u.id
                  JOIN {board_columns} bc ON bc.id = bn.columnid
                 WHERE bc.boardid = :boardid";
        if ($groupid) {
            $sql .= " AND EXISTS (SELECT 'x' FROM {groups_members} gm WHERE gm.userid = u.id AND gm.groupid = :groupid)";
            $params['groupid'] = $groupid;
        }
        if ($onlycomments) {
            $sql .= " AND EXISTS (SELECT 'x' FROM {board_comments} bc WHERE bc.noteid = bn.id)";
        }
        [$sort, $sortparams] = users_order_by_sql('u');
        $sql .= " ORDER BY $sort";
        $params = array_merge($params, $sortparams);

        $userlist = $DB->get_records_sql($sql, $params);

        $users = [];
        foreach ($userlist as $user) {
            $users[$user->id] = fullname($user);
        }

        return $users;
    }

    /**
     * Check if you can view the notes for this user.
     *
     * @param stdClass $board the board.
     * @param int $ownerid the user id.
     * @return bool true if you can view the notes, false otherwise.
     */
    public static function can_view_owner(stdClass $board, int $ownerid): bool {
        global $USER;

        $context = self::context_for_board($board);
        if (has_capability('mod/board:manageboard', $context)) {
            return true;
        }
        if (!is_enrolled($context, $ownerid, 'mod/board:view', true)) {
            // Non-managers can only view boards of enrolled users.
            return false;
        }
        $cm = self::coursemodule_for_board($board);
        if (!\core_availability\info_module::is_user_visible($cm, $ownerid, true)) {
            return false;
        }
        if ($board->singleusermode == self::SINGLEUSER_PUBLIC) {
            return true;
        }
        if ($board->singleusermode == self::SINGLEUSER_PRIVATE && $USER->id == $ownerid) {
            return true;
        }
        return false;
    }

    /**
     * Check if current user can post on this board.
     *
     * @param stdClass $board the board.
     * @param int $ownerid the board owner
     * @return bool
     */
    public static function can_post(stdClass $board, int $ownerid): bool {
        global $USER;

        $context = self::context_for_board($board);

        if ($board->singleusermode == self::SINGLEUSER_DISABLED) {
            if ($ownerid && $ownerid != $USER->id) {
                debugging('ownerid should not be used when single user mode disabled', DEBUG_DEVELOPER);
                return false;
            }
            return has_capability('mod/board:post', $context);
        }

        if ($USER->id == $ownerid) {
            return has_capability('mod/board:post', $context);
        }

        return has_capability('mod/board:manageboard', $context);
    }

    /**
     * Get the supported filetype extensions for board backgound.
     *
     * @return array of strings of supported file extensions.
     */
    public static function get_accepted_background_file_extensions(): array {
        $config = get_config('mod_board');
        if (isset($config->acceptedfiletypeforbackground)) {
            $extensions = explode(',', $config->acceptedfiletypeforbackground);
        } else {
            $extensions = [];
        }
        return $extensions;
    }

    /**
     * Returns basic options for the background file picker.
     *
     * @return array
     */
    public static function get_background_picker_options(): array {
        $extensions = self::get_accepted_background_file_extensions();

        $extensions = array_map(function ($extension) {
            return '.' . $extension;
        }, $extensions);

        return [
            'accepted_types' => $extensions,
            'maxfiles' => 1,
            'subdirs' => 0,
            'maxbytes' => 0,
        ];
    }
}
