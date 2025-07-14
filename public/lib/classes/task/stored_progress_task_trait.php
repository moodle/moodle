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

namespace core\task;

use core\progress\db_updater;
use core\progress\stored;
use core\output\stored_progress_bar;

/**
 * Trait to use in tasks to automatically add stored progress functionality.
 *
 * @package    core
 * @copyright  2024 onwards Catalyst IT {@link http://www.catalyst-eu.net/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Conn Warwicker <conn.warwicker@catalyst-eu.net>
 */
trait stored_progress_task_trait {
    /** @var ?stored_progress_bar $progress */
    protected $progress = null;

    /**
     * Construct a unique name for the progress bar.
     *
     * For adhoc tasks, this will need the ID in it. For scheduled tasks just the class name.
     *
     * @return string
     */
    protected function get_progress_name(): string {
        if (method_exists($this, 'get_id')) {
            return get_class($this) . '_' . $this->get_id();
        } else {
            return get_class($this);
        }
    }

    /**
     * Initialise a stored progress record.
     */
    public function initialise_stored_progress(): void {
        $this->progress = new stored_progress_bar(
            stored_progress_bar::convert_to_idnumber($this->get_progress_name()),
            autostart: false,
        );
        $this->progress->store_pending();
    }

    /**
     * Get a stored object for the stored progress record.
     *
     * @return stored
     */
    public function get_progress(): stored {
        return new stored($this->progress);
    }

    /**
     * Start a stored progress bar implementation for the task this trait is used in.
     *
     * @return void
     */
    protected function start_stored_progress(): void {
        global $OUTPUT, $PAGE;

        // To get around the issue in MDL-80770, we are manually setting the renderer to cli.
        $OUTPUT = $PAGE->get_renderer('core', null, 'cli');

        $this->progress = new stored_progress_bar(
            stored_progress_bar::convert_to_idnumber($this->get_progress_name())
        );

        // Start the progress.
        $this->progress->start();
    }

}
