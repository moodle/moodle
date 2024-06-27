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
    /** @var string html id */
    private $htmlid;
    /** @var int total width */
    private $width;
    /** @var int last percentage printed */
    private $percent = 0;
    /** @var int time when last printed */
    private $lastupdate = 0;
    /** @var int when did we start printing this */
    private $timestart = 0;

    /**
     * Constructor
     *
     * Prints JS code if $autostart true.
     *
     * @param string $htmlid The container ID.
     * @param int $width The suggested width.
     * @param bool $autostart Whether to start the progress bar right away.
     */
    public function __construct($htmlid = '', $width = 500, $autostart = false) {
        if (!CLI_SCRIPT && !NO_OUTPUT_BUFFERING) {
            debugging('progress_bar used in a non-CLI script without setting NO_OUTPUT_BUFFERING.', DEBUG_DEVELOPER);
        }

        if (!empty($htmlid)) {
            $this->htmlid  = $htmlid;
        } else {
            $this->htmlid  = 'pbar_' . uniqid();
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
        return $this->htmlid;
    }

    /**
     * Create a new progress bar, this function will output html.
     *
     * @return void Echo's output
     */
    public function create() {
        global $OUTPUT;

        $this->timestart = microtime(true);

        flush();
        echo $OUTPUT->render($this);
        flush();
    }

    /**
     * Update the progress bar.
     *
     * @param int $percent From 1-100.
     * @param string $msg The message.
     * @return void Echo's output
     * @throws coding_exception
     */
    private function update_raw($percent, $msg) {
        global $OUTPUT;

        if (empty($this->timestart)) {
            throw new coding_exception('You must call create() (or use the $autostart ' .
                'argument to the constructor) before you try updating the progress bar.');
        }

        $estimate = $this->estimate($percent);

        if ($estimate === null) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedIf
            // Always do the first and last updates.
        } else if ($estimate == 0) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedIf
            // Always do the last updates.
        } else if ($this->lastupdate + 20 < time()) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedIf
            // We must update otherwise browser would time out.
        } else if (round($this->percent, 2) === round($percent, 2)) {
            // No significant change, no need to update anything.
            return;
        }

        $estimatemsg = '';
        if ($estimate != 0 && is_numeric($estimate)) {
            // Err on the conservative side and also avoid showing 'now' as the estimate.
            $estimatemsg = format_time(ceil($estimate));
        }

        $this->percent = $percent;
        $this->lastupdate = microtime(true);

        echo $OUTPUT->render_progress_bar_update($this->htmlid, $this->percent, $msg, $estimatemsg);
        flush();
    }

    /**
     * Estimate how much time it is going to take.
     *
     * @param int $pt From 1-100.
     * @return mixed Null (unknown), or int.
     */
    private function estimate($pt) {
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
            'id' => $this->htmlid,
            'width' => $this->width,
        ];
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(progress_bar::class, \progress_bar::class);
