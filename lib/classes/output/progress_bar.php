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

use core\exception\coding_exception;

/**
 * Progress bar class.
 *
 * Manages the display of a progress bar.
 *
 * To use this class.
 * - construct
 * - call create (or use the 3rd param to the constructor)
 * - call update or update_full() or update() repeatedly
 *
 * @copyright 2008 jamiesensei
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package core
 * @category output
 */
class progress_bar implements renderable, templatable {

    /** @var bool Can use output buffering. */
    protected static $supportsoutputbuffering = false;

    /** @var string unique id */
    protected $idnumber;

    /** @var int total width */
    protected $width;

    /** @var int last percentage printed */
    protected $percent = 0;

    /** @var int time when last printed */
    protected $lastupdate = 0;

    /** @var int when did we start printing this */
    protected $timestart = 0;

    /** @var bool Whether or not to auto render updates to the screen */
    protected $autoupdate = true;

    /** @var bool Whether or not an error has occured */
    protected $haserrored = false;

    /**
     * Constructor
     *
     * Prints JS code if $autostart true.
     *
     * @param string $htmlid The unique ID for the progress bar or HTML container id.
     * @param int $width The suggested width.
     * @param bool $autostart Whether to start the progress bar right away.
     */
    public function __construct($htmlid = '', $width = 500, $autostart = false) {
        if (!static::$supportsoutputbuffering && !CLI_SCRIPT && !NO_OUTPUT_BUFFERING) {
            debugging('progress_bar used in a non-CLI script without setting NO_OUTPUT_BUFFERING.', DEBUG_DEVELOPER);
        }

        if (!empty($htmlid)) {
            $this->idnumber  = $htmlid;
        } else {
            $this->idnumber  = 'pbar_'.uniqid();
        }

        $this->width = $width;

        if ($autostart) {
            $this->create();
        }
    }

    /**
     * Getter for ID
     * @return string id
     */
    public function get_id(): string {
        return $this->idnumber;
    }

    /**
     * Get the percent
     * @return float
     */
    public function get_percent(): float {
        return $this->percent;
    }

    /**
     * Create a new progress bar, this function will output html.
     *
     * @return void Echo's output
     */
    public function create() {

        $this->timestart = microtime(true);
        $this->render();

    }

    /**
     * Render the progress bar.
     *
     * @return void
     */
    public function render(): void {
        flush();
        echo $this->get_content();
        flush();
    }

    /**
     * Get the content to be rendered
     *
     * @return string
     */
    public function get_content(): string {
        global $OUTPUT;
        return $OUTPUT->render($this);
    }

    /**
     * Set whether or not to auto render updates to the screen
     *
     * @param bool $value
     * @return void
     */
    public function auto_update(bool $value): void {
        $this->autoupdate = $value;
    }

    /**
     * Update the progress bar.
     *
     * @param int $percent From 1-100.
     * @param string $msg The message.
     * @return void Echo's output
     * @throws coding_exception
     */
    protected function update_raw($percent, $msg) {
        global $OUTPUT;

        if (empty($this->timestart)) {
            throw new coding_exception('You must call create() (or use the $autostart ' .
                'argument to the constructor) before you try updating the progress bar.');
        }

        $estimate = $this->estimate($percent);

        // Always do the first and last updates. Estimate would be null in the beginning and 0 at the end.
        $isfirstorlastupdate = empty($estimate);
        // We need to update every 20 seconds since last update to prevent browser timeout.
        $timetoupdate = $this->lastupdate + 20 < time();
        // Whether the progress has moved.
        $issameprogress = round($this->percent, 2) === round($percent, 2);

        // No need to update if it's not yet time to update and there's no progress.
        if (!$isfirstorlastupdate && !$timetoupdate && $issameprogress) {
            return;
        }

        $estimatemsg = $this->get_estimate_message($percent);

        $this->percent = $percent;
        $this->lastupdate = microtime(true);

        if ($this->autoupdate) {
            echo $OUTPUT->render_progress_bar_update($this->idnumber, $this->percent, $msg, $estimatemsg);
            flush();
        }
    }

    /**
     * Estimate how much time it is going to take.
     *
     * @param int $pt From 1-100.
     * @return mixed Null (unknown), or int.
     */
    protected function estimate($pt) {
        if ($this->lastupdate == 0) {
            return null;
        }
        if ($pt < 0.00001) {
            return null; // We do not know yet how long it will take.
        }
        if ($pt > 99.99999) {
            return 0; // Nearly done, right?
        }
        $consumed = microtime(true) - $this->timestart;
        if ($consumed < 0.001) {
            return null;
        }

        return (100 - $pt) * ($consumed / $pt);
    }

    /**
     * Update progress bar according percent.
     *
     * @param int $percent From 1-100.
     * @param string $msg The message needed to be shown.
     */
    public function update_full($percent, $msg) {
        $percent = max(min($percent, 100), 0);
        $this->update_raw($percent, $msg);
    }

    /**
     * Update progress bar according the number of tasks.
     *
     * @param int $cur Current task number.
     * @param int $total Total task number.
     * @param string $msg The message needed to be shown.
     */
    public function update($cur, $total, $msg) {
        $percent = ($cur / $total) * 100;
        $this->update_full($percent, $msg);
    }

    /**
     * Restart the progress bar.
     */
    public function restart() {
        $this->percent    = 0;
        $this->lastupdate = 0;
        $this->timestart = 0;
    }

    /**
     * Export for template.
     *
     * @param  renderer_base $output The renderer.
     * @return array
     */
    public function export_for_template(renderer_base $output) {
        return [
            'id' => '',
            'idnumber' => $this->idnumber,
            'width' => $this->width,
            'class' => '',
            'value' => 0,
            'error' => 0,
        ];
    }

    /**
     * This gets the estimate message to be displayed with the progress bar.
     *
     * @param float $percent
     * @return string
     */
    public function get_estimate_message(float $percent): string {
        $estimate = $this->estimate($percent);
        $estimatemsg = '';
        if ($estimate != 0 && is_numeric($estimate)) {
            $estimatemsg = format_time(ceil($estimate));
        }

        return $estimatemsg;
    }

    /**
     * Set the error flag on the object
     *
     * @param bool $value
     * @return void
     */
    protected function set_haserrored(bool $value): void {
        $this->haserrored = $value;
    }

    /**
     * Check if the process has errored
     *
     * @return bool
     */
    public function get_haserrored(): bool {
        return $this->haserrored;
    }

    /**
     * Set that the process running has errored
     *
     * @param string $errormsg
     * @return void
     */
    public function error(string $errormsg): void {
        global $OUTPUT;

        $this->haserrored = true;
        $this->message = $errormsg;

        if ($this->autoupdate) {
            echo $OUTPUT->render_progress_bar_update($this->idnumber, $this->percent, $errormsg, '', true);
            flush();
        }
    }

}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(progress_bar::class, \progress_bar::class);
