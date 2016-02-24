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

namespace core\progress;

defined('MOODLE_INTERNAL') || die();

/**
 * Progress handler that uses a standard Moodle progress bar to display
 * progress. The Moodle progress bar cannot show indeterminate progress,
 * so we do extra output in addition to the bar.
 *
 * @package core_progress
 * @copyright 2013 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class display extends base {
    /**
     * @var int Number of wibble states (state0...stateN-1 classes in CSS)
     */
    const WIBBLE_STATES = 13;

    /**
     * @var \progress_bar Current progress bar.
     */
    private $bar;

    private $lastwibble, $currentstate = 0, $direction = 1;

    /**
     * @var bool True to display names
     */
    protected $displaynames = false;

    /**
     * Constructs the progress reporter. This will output HTML code for the
     * progress bar, and an indeterminate wibbler below it.
     *
     * @param bool $startnow If true, outputs HTML immediately.
     */
    public function __construct($startnow = true) {
        if ($startnow) {
            $this->start_html();
        }
    }

    /**
     * By default, the progress section names do not display because
     * these will probably be untranslated and incomprehensible. To make them
     * display, call this method.
     *
     * @param bool $displaynames True to display names
     */
    public function set_display_names($displaynames = true) {
        $this->displaynames = $displaynames;
    }

    /**
     * Starts to output progress.
     *
     * Called in constructor and in update_progress if required.
     *
     * @throws \coding_exception If already started
     */
    public function start_html() {
        if ($this->bar) {
            throw new \coding_exception('Already started');
        }
        $this->bar = new \progress_bar();
        $this->bar->create();
        echo \html_writer::start_div('wibbler');
    }

    /**
     * Finishes output. (Progress can begin again later if there are more
     * calls to update_progress.)
     *
     * Automatically called from update_progress when progress finishes.
     */
    public function end_html() {
        // Finish progress bar.
        $this->bar->update_full(100, '');
        $this->bar = null;

        // End wibbler div.
        echo \html_writer::end_div();
    }

    /**
     * When progress is updated, updates the bar.
     *
     * @see \core\progress\base::update_progress()
     */
    public function update_progress() {
        // If finished...
        if (!$this->is_in_progress_section()) {
            if ($this->bar) {
                $this->end_html();
            }
        } else {
            if (!$this->bar) {
                $this->start_html();
            }
            // In case of indeterminate or small progress, update the wibbler
            // (up to once per second).
            if (time() != $this->lastwibble) {
                $this->lastwibble = time();
                echo \html_writer::div('', 'wibble state' . $this->currentstate);

                // Go on to next colour.
                $this->currentstate += $this->direction;
                if ($this->currentstate < 0 || $this->currentstate >= self::WIBBLE_STATES) {
                    $this->direction = -$this->direction;
                    $this->currentstate += 2 * $this->direction;
                }
                $buffersize = ini_get('output_buffering');
                if ($buffersize) {
                    // Force the buffer full.
                    echo str_pad('', $buffersize);
                }
            }

            // Get progress.
            list ($min, $max) = $this->get_progress_proportion_range();

            // Update progress bar.
            $message = '';
            if ($this->displaynames) {
                $message = $this->get_current_description();
            }
            $this->bar->update_full($min * 100, $message);

            // Flush output.
            flush();
        }
    }
}
