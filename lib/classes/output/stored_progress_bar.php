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

namespace core\output;

/**
 * Stored progress bar class.
 *
 * @package    core
 * @copyright  2023 onwards Catalyst IT {@link http://www.catalyst-eu.net/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Conn Warwicker <conn.warwicker@catalyst-eu.net>
 */
class stored_progress_bar extends progress_bar {

    /** @var bool Can use output buffering. */
    protected static $supportsoutputbuffering = true;

    /** @var int DB record ID */
    protected $recordid;

    /** @var string|null Message to associate with bar */
    protected $message = null;

    /** @var \core\clock Clock object */
    protected $clock;

    /**
     * This overwrites the progress_bar::__construct method.
     *
     * @param string $idnumber
     */
    public function __construct($idnumber) {

        $this->clock = \core\di::get(\core\clock::class);

        // Construct from the parent.
        parent::__construct($idnumber, 0, true);

    }

    /**
     * Just set the timestart, do not render the bar immediately.
     *
     * @return void
     */
    public function create(): void {
        $this->timestart = $this->clock->time();
    }

    /**
     * Load the stored progress bar from the database based on its uniqued idnumber
     *
     * @param string $idnumber Unique ID of the bar
     * @return stored_progress_bar|null
     */
    public static function get_by_idnumber(string $idnumber): ?stored_progress_bar {
        global $DB;

        $record = $DB->get_record('stored_progress', ['idnumber' => $idnumber]);
        if ($record) {
            return self::load($record);
        } else {
            return null;
        }
    }

    /**
     * Load the stored progress bar from the database, based on it's record ID
     *
     * @param int $id Database record ID
     * @return stored_progress_bar|null
     */
    public static function get_by_id(int $id): ?stored_progress_bar {
        global $DB;

        $record = $DB->get_record('stored_progress', ['id' => $id]);
        if ($record) {
            return self::load($record);
        } else {
            return null;
        }
    }

    /**
     * Load the stored progress bar object from its record in the database.
     *
     * @param stdClass $record
     * @return stored_progress_bar
     */
    public static function load(\stdClass $record): stored_progress_bar {
        $progress = new stored_progress_bar($record->idnumber);
        $progress->set_record_id($record->id);
        $progress->set_time_started($record->timestart);
        $progress->set_last_updated($record->lastupdate);
        $progress->set_percent($record->percentcompleted);
        $progress->set_message($record->message);
        $progress->set_haserrored($record->haserrored);
        return $progress;
    }

    /**
     * Set the DB record ID
     *
     * @param int $id
     * @return void
     */
    protected function set_record_id(int $id): void {
        $this->recordid = $id;
    }

    /**
     * Set the time we started the process.
     *
     * @param int $value
     * @return void
     */
    protected function set_time_started(int $value): void {
        $this->timestart = $value;
    }

    /**
     * Set the time we started last updated the progress.
     *
     * @param int|null $value
     * @return void
     */
    protected function set_last_updated(?int $value = null): void {
        $this->lastupdate = $value;
    }

    /**
     * Set the percent completed.
     *
     * @param float|null $value
     * @return void
     */
    protected function set_percent($value = null): void {
        $this->percent = $value;
    }

    /**
     * Set the message.
     *
     * @param string|null $value
     * @return void
     */
    protected function set_message(?string $value = null): void {
        $this->message = $value;
    }

    /**
     * Set that the process running has errored and store that against the bar
     *
     * @param string $errormsg
     * @return void
     */
    public function error(string $errormsg): void {
        // Update the error variables.
        parent::error($errormsg);

        // Update the record.
        $this->update_record();
    }

    /**
     * Get the progress bar message.
     *
     * @return string|null
     */
    public function get_message(): ?string {
        return $this->message;
    }

    /**
     * Get the content to display the progress bar and start polling via AJAX
     *
     * @return string
     */
    public function get_content(): string {
        global $CFG, $PAGE, $OUTPUT;

        $PAGE->requires->js_call_amd('core/stored_progress', 'init', [
            self::get_timeout(),
        ]);

        $context = $this->export_for_template($OUTPUT);
        return $OUTPUT->render_from_template('core/progress_bar', $context);
    }

    /**
     * Export for template.
     *
     * @param  renderer_base $output The renderer.
     * @return array
     */
    public function export_for_template(\renderer_base $output): array {
        return [
            'id' => $this->recordid,
            'idnumber' => $this->idnumber,
            'width' => $this->width,
            'class' => 'stored-progress-bar',
            'value' => $this->percent,
            'message' => $this->message,
            'error' => $this->haserrored,
        ];
    }

    /**
     * Start the recording of the progress and store in the database
     *
     * @return int ID of the DB record
     */
    public function start(): int {
        global $OUTPUT, $DB;

        // If we are running in an non-interactive CLI environment, call the progress bar renderer to avoid warnings
        // when we do an update.
        if (defined('STDOUT') && !stream_isatty(STDOUT)) {
            $OUTPUT->render_progress_bar($this);
        }

        // Delete any existing records for this.
        $this->clear_records();

        // Create new progress record.
        $this->recordid = $DB->insert_record('stored_progress', [
            'idnumber' => $this->idnumber,
            'timestart' => (int)$this->timestart,
        ]);

        return $this->recordid;
    }

    /**
     * End the polling progress and delete the DB record.
     *
     * @return void
     */
    protected function clear_records(): void {
        global $DB;

        $DB->delete_records('stored_progress', [
            'idnumber' => $this->idnumber,
        ]);
    }

    /**
     * Update the database record with the percentage and message
     *
     * @param float $percent
     * @param string $msg
     * @return void
     */
    protected function update_raw($percent, $msg): void {
        $this->percent = $percent;
        $this->message = $msg;

        // Update the database record with the new data.
        $this->update_record();

        // Update any CLI script's progress with an ASCII progress bar.
        $this->render_update();
    }

    /**
     * Render an update to the CLI
     *
     * This will only work in CLI scripts, and not in scheduled/adhoc tasks even though they run via CLI,
     * as they seem to use a different renderer (core_renderer instead of core_renderer_cli).
     *
     * We also can't check this based on "CLI_SCRIPT" const as that is true for tasks.
     *
     * So this will just check a flag to see if we want auto rendering of updates.
     *
     * @return void
     */
    protected function render_update(): void {
        global $OUTPUT;

        // If no output buffering, don't render it at all.
        if (defined('NO_OUTPUT_BUFFERING') && NO_OUTPUT_BUFFERING) {
            $this->auto_update(false);
        }

        // If we want the screen to auto update, render it.
        if ($this->autoupdate) {
            echo $OUTPUT->render_progress_bar_update(
                $this->idnumber, sprintf("%.1f", $this->percent), $this->message, $this->get_estimate_message($this->percent)
            );
        }
    }

    /**
     * Update the database record
     *
     * @throws \moodle_exception
     * @return void
     */
    protected function update_record(): void {
        global $DB;

        if (is_null($this->recordid)) {
            throw new \moodle_exception('Polling has not been started. Cannot set iteration.');
        }

        // Update time.
        $this->lastupdate = $this->clock->time();

        // Update the database record.
        $record = new \stdClass();
        $record->id = $this->recordid;
        $record->lastupdate = (int)$this->lastupdate;
        $record->percentcompleted = $this->percent;
        $record->message = $this->message;
        $record->haserrored = $this->haserrored;
        $DB->update_record('stored_progress', $record);
    }

    /**
     * We need a way to specify a unique idnumber for processes being monitored, so that
     * firstly we don't accidentally overwrite a running process, and secondly so we can
     * automatically load them in some cases, without having to manually code in its name.
     *
     * So this uses the classname of the object being monitored, along with its id.
     *
     * This method should be used when creating the stored_progress record to set it's idnumber.
     *
     * @param string $class Class name of the object being monitored, e.g. \local_something\task\my_task
     * @param int|null $id ID of an object from database, e.g. 123
     * @return string Converted string, e.g. local_something_task_my_task_123
     */
    public static function convert_to_idnumber(string $class, ?int $id = null): string {
        $idnumber = preg_replace("/[^a-z0-9_]/", "_", ltrim($class, '\\'));
        if (!is_null($id)) {
            $idnumber .= '_' . $id;
        }

        return $idnumber;
    }

    /**
     * Get the polling timeout in seconds. Default: 5.
     *
     * @return int
     */
    public static function get_timeout(): int {
        global $CFG;
        return $CFG->progresspollinterval ?? 5;
    }

}
