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

use mod_board\board;

/**
 * Board test generator.
 *
 * @package    mod_board
 * @copyright  2020 onward: Brickfield Education Labs, www.brickfield.ie
 * @author     Jay Churchward (jay@brickfieldlabs.ie)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_board_generator extends testing_module_generator {
    /**
     * @var int keep track of how many columns have been created.
     */
    protected $columncount = 3;
    /**
     * @var int keep track of how many comments have been created.
     */
    protected $commentcount = 0;
    /**
     * @var int keep track of how many templates have been created.
     */
    protected $templatecount = 0;

    /**
     * To be called from data reset code only,
     * do not use in tests.
     * @return void
     */
    public function reset() {
        $this->columncount = 3;
        $this->commentcount = 0;
        $this->templatecount = 0;
        parent::reset();
    }

    #[\Override]
    public function create_instance($record = null, ?array $options = null) {
        $record = (object)(array)$record;

        // Apply the same defaults as in mod_form.

        if (!isset($record->background_color)) {
            $record->background_color = '';
        }

        if (!isset($record->addrating)) {
            $record->addrating = board::RATINGDISABLED;
        }

        if (!isset($record->hideheaders)) {
            $record->hideheaders = 0;
        }

        if (!isset($record->sortby)) {
            $record->sortby = board::SORTBYNONE;
        }

        if (!isset($record->singleusermode)) {
            $record->singleusermode = board::SINGLEUSER_DISABLED;
        }

        if (!isset($record->userscanedit)) {
            $record->userscanedit = 0;
        }

        if (!isset($record->enableblanktarget)) {
            $record->enableblanktarget = 0;
        }

        if (!empty($record->postby)) {
            $record->postbyenabled = 1;
        }

        return parent::create_instance($record, $options);
    }

    /**
     * Create a new board column.
     *
     * @param array|stdClass|null $record
     * @return stdClass column record
     */
    public function create_column($record = null): stdClass {
        $record = (object)(array)$record;

        $this->columncount++;

        if (empty($record->boardid)) {
            throw new coding_exception('Column generator requires $record->boardid');
        }

        if (empty($record->name)) {
            $record->name = "Column {$this->columncount}";
        }

        $column = \mod_board\local\column::create($record->boardid, $record->name);
        unset($column->historyid);

        return $column;
    }

    /**
     * Create new a note.
     *
     * @param array|stdClass|null $record
     * @return stdClass column record
     */
    public function create_note($record = null): stdClass {
        global $DB, $USER;

        $record = (object)(array)$record;

        if (empty($record->columnid)) {
            if (empty($record->column) || empty($record->boardid)) {
                throw new coding_exception('Note generator requires $record->columnid');
            } else {
                $board = board::get_board($record->boardid, MUST_EXIST);
                $column = $DB->get_record(
                    'board_columns',
                    ['boardid' => $board->id, 'sortorder' => $record->column],
                    '*',
                    MUST_EXIST
                );
                $record->columnid = $column->id;
            }
        }
        unset($record->column);
        unset($record->boardid);

        if (empty($record->heading) && empty($record->content)) {
            $record->heading = 'Some note';
        }
        $heading = $record->heading ?? '';
        $content = $record->content ?? '';
        $userid = $record->userid ?? $USER->id;
        $ownerid = $record->ownerid ?? $userid;
        $groupid = $record->groupid ?? 0;
        $attachment = ['type' => board::MEDIATYPE_NONE];
        if (isset($record->type)) {
            $attachment['type'] = $record->type;
            if (isset($record->info)) {
                $attachment['info'] = $record->info;
            }
            if (isset($record->url)) {
                $attachment['url'] = $record->url;
            }
            if (isset($record->draftitemid)) {
                $attachment['draftitemid'] = $record->draftitemid;
            }
        }

        $note = \mod_board\local\note::create(
            $record->columnid,
            $ownerid,
            $groupid,
            $heading,
            $content,
            $attachment,
            $userid
        );

        if (!empty($record->deleted)) {
            \mod_board\local\note::delete($note->id);
        }

        return $DB->get_record('board_notes', ['id' => $note->id], '*', MUST_EXIST);
    }

    /**
     * Create new a comment.
     *
     * @param array|stdClass|null $record
     * @return stdClass comment record
     */
    public function create_comment($record = null): stdClass {
        global $USER, $DB;

        $record = (object)(array)$record;
        if (empty($record->noteid)) {
            throw new coding_exception('Comment generator requires $record->noteid');
        }
        $note = board::get_note($record->noteid, MUST_EXIST);

        $this->commentcount++;

        if (empty($record->content)) {
            $record->content = "Comment {$this->commentcount}";
        }

        $comment = \mod_board\local\comment::create($note->id, $record->content, $record->userid ?? $USER->id);

        if (!empty($record->deleted)) {
            \mod_board\local\comment::delete($comment->id);
        }

        return $DB->get_record('board_comments', ['id' => $comment->id], '*', MUST_EXIST);
    }

    /**
     * Create new a template.
     *
     * @param array|stdClass|null $record
     * @return stdClass template record
     */
    public function create_template($record = null): stdClass {
        global $DB;

        $record = (object)(array)$record;
        if (empty($record->contextid)) {
            $record->contextid = context_system::instance()->id;
        }

        $this->templatecount++;

        if (empty($record->name)) {
            $record->name = "Template {$this->templatecount}";
        }

        if (isset($record->columns)) {
            // Workaround for allowing entering of newlines in behat generator tables.
            $record->columns = str_replace('\n', "\n", $record->columns);
        }

        $template = \mod_board\local\template::create($record);

        return $DB->get_record('board_templates', ['id' => $template->id], '*', MUST_EXIST);
    }
}
