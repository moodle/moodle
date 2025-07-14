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

/**
 * Represents the timer panel.
 *
 * @package   mod_assign
 * @copyright  2020 Ilya Tregubov <ilyatregubov@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_assign\output;

use renderable;
use renderer_base;
use stdClass;
use templatable;

/**
 * Represents the timer panel.
 *
 * @package   mod_assign
 * @copyright  2020 Ilya Tregubov <ilyatregubov@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class timelimit_panel implements templatable, renderable {
    /** @var \stdClass assign submission attempt.*/
    protected $submission;
    /** @var object assign object.*/
    protected $assign;

    /**
     * Constructor.
     *
     * @param \stdClass $submission assign submission.
     * @param \stdClass $assign assign object.
     */
    public function __construct(\stdClass $submission, \stdClass $assign) {
        $this->submission = $submission;
        $this->assign = $assign;
    }

    /**
     * Render timer.
     *
     * @param renderer_base $output The current page renderer.
     * @return stdClass - Flat list of exported data.
     */
    public function export_for_template(renderer_base $output): stdClass {
        return (object)['timerstartvalue' => $this->end_time() - time()];
    }

    /**
     * Compute end time for this assign attempt.
     *
     * @return int the time when assign attempt is due.
     */
    private function end_time(): int {
        $timedue = $this->submission->timestarted + $this->assign->timelimit;
        if ($this->assign->duedate) {
            return min($timedue, $this->assign->duedate);
        }

        if ($this->assign->cutoffdate) {
            return min($timedue, $this->assign->cutoffdate);
        }

        return $timedue;
    }
}
