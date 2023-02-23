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

namespace tiny_autosave;

use stdClass;

/**
 * Autosave Manager.
 *
 * @package   tiny_autosave
 * @copyright 2022 Andrew Lyons <andrew@nicols.co.uk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class autosave_manager {

    /** @var int The contextid */
    protected $contextid;

    /** @var string The page hash reference */
    protected $pagehash;

    /** @var string The page instance reference */
    protected $pageinstance;

    /** @var string The elementid for this editor */
    protected $elementid;

    /** @var stdClass The user record */
    protected $user;

    /**
     * Constructor for the autosave manager.
     *
     * @param int $contextid The contextid of the session
     * @param string $pagehash The page hash
     * @param string $pageinstance The page instance
     * @param string $elementid The element id
     * @param null|stdClass $user The user object for the owner of the autosave
     */
    public function __construct(
        int $contextid,
        string $pagehash,
        string $pageinstance,
        string $elementid,
        ?stdClass $user = null
    ) {
        global $USER;

        $this->contextid = $contextid;
        $this->pagehash = $pagehash;
        $this->pageinstance = $pageinstance;
        $this->elementid = $elementid;
        $this->user = $user ?? $USER;
    }

    /**
     * Get the autosave record for this session.
     *
     * @return stdClass|null
     */
    public function get_autosave_record(): ?stdClass {
        global $DB;

        $record = $DB->get_record('tiny_autosave', [
            'contextid' => $this->contextid,
            'userid' => $this->user->id,
            'pagehash' => $this->pagehash,
            'elementid' => $this->elementid,
        ]);

        if (empty($record)) {
            return null;
        }

        return $record;
    }

    /**
     * Create an autosave record for the session.
     *
     * @param string $drafttext The draft text to save
     * @param null|int $draftid The draft file area if one is used
     * @return stdClass The autosave record
     */
    public function create_autosave_record(string $drafttext, ?int $draftid = null): stdClass {
        global $DB;
        $record = (object) [
            'userid' => $this->user->id,
            'contextid' => $this->contextid,
            'pagehash' => $this->pagehash,
            'pageinstance' => $this->pageinstance,
            'elementid' => $this->elementid,
            'drafttext' => $drafttext,
            'timemodified' => time(),
        ];

        if ($draftid) {
            $record->draftid = $draftid;
        }

        $record->id = $DB->insert_record('tiny_autosave', $record);

        return $record;
    }

    /**
     * Update the text of the autosave session.
     *
     * @param string $drafttext The text to save
     * @return stdClass The updated record
     */
    public function update_autosave_record(string $drafttext): stdClass {
        global $DB;

        $record = $this->get_autosave_record();
        if ($record) {
            $record->drafttext = $drafttext;
            $record->timemodified = time();
            $DB->update_record('tiny_autosave', $record);

            return $record;
        } else {
            return $this->create_autosave_record($drafttext);
        }
    }

    /**
     * Resume an autosave session, updating the draft file area if relevant.
     *
     * @param null|int $draftid The draft file area to update
     * @return stdClass The updated autosave record
     */
    public function resume_autosave_session(?int $draftid = null): stdClass {
        $record = $this->get_autosave_record();
        if (!$record) {
            return $this->create_autosave_record('', $draftid);
        }

        if ($this->is_autosave_stale($record)) {
            // If the autosave record it stale, remove it and create a new, blank, record.
            $this->remove_autosave_record();

            return $this->create_autosave_record('', $draftid);
        }

        if (empty($draftid)) {
            // There is no file area to handle, so just return the record without any further changes.
            return $record;
        }

        // This autosave is not stale, so update the draftid and move any files over to the new draft file area.
        return $this->update_draftid_for_record($record, $draftid);
    }

    /**
     * Check whether the autosave data is stale.
     *
     * Records are considered stale if either of the following conditions are true:
     * - The record is older than the stale period
     * - Any of the files in the draft area are newer than the autosave data itself
     *
     * @param stdClass $record The autosave record
     * @return bool Whether the record is stale
     */
    protected function is_autosave_stale(stdClass $record): bool {
        $timemodified = $record->timemodified;
        // TODO Create the UI for the stale period.
        $staleperiod = get_config('tiny_autosave', 'staleperiod');
        if (empty($staleperiod)) {
            $staleperiod = (4 * DAYSECS);
        }

        $stale = $timemodified < (time() - $staleperiod);

        if (empty($record->draftid)) {
            return $stale;
        }

        $fs = get_file_storage();
        $files = $fs->get_directory_files($record->contextid, 'user', 'draft', $record->draftid, '/', true, true);

        $lastfilemodified = 0;
        foreach ($files as $file) {
            if ($record->timemodified < $file->get_timemodified()) {
                $stale = true;
                break;
            }
        }

        return $stale;
    }

    /**
     * Move the files relating to the autosave session to a new draft file area.
     *
     * @param stdClass $record The autosave record
     * @param int $newdraftid The new draftid to move files to
     * @return stdClass The updated autosave record
     */
    protected function update_draftid_for_record(stdClass $record, int $newdraftid): stdClass {
        global $CFG, $DB;

        require_once("{$CFG->libdir}/filelib.php");

        // Copy all draft files from the old draft area.
        $usercontext = \context_user::instance($this->user->id);

        // This function copies all the files in one draft area, to another area (in this case it's
        // another draft area). It also rewrites the text to @@PLUGINFILE@@ links.
        $record->drafttext = file_save_draft_area_files(
            $record->draftid,
            $usercontext->id,
            'user',
            'draft',
            $newdraftid,
            [],
            $record->drafttext
        );

        // Final rewrite to the new draft area (convert the @@PLUGINFILES@@ again).
        $record->drafttext = file_rewrite_pluginfile_urls(
            $record->drafttext,
            'draftfile.php',
            $usercontext->id,
            'user',
            'draft',
            $newdraftid
        );

        $record->draftid = $newdraftid;
        $record->pageinstance = $this->pageinstance;
        $record->timemodified = time();

        $DB->update_record('tiny_autosave', $record);

        return $record;
    }

    /**
     * Remove the autosave record.
     */
    public function remove_autosave_record(): void {
        global $DB;

        $DB->delete_records('tiny_autosave', [
            'contextid' => $this->contextid,
            'userid' => $this->user->id,
            'pagehash' => $this->pagehash,
            'elementid' => $this->elementid,
        ]);
    }

}
