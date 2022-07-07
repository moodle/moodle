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
 * Contains class mod_feedback\output\summary
 *
 * @package   mod_feedback
 * @copyright 2016 Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_feedback\output;

use renderable;
use templatable;
use renderer_base;
use stdClass;
use moodle_url;
use mod_feedback_structure;

/**
 * Class to help display feedback summary
 *
 * @package   mod_feedback
 * @copyright 2016 Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class summary implements renderable, templatable {

    /** @var mod_feedback_structure */
    protected $feedbackstructure;

    /** @var int */
    protected $mygroupid;

    /** @var bool  */
    protected $extradetails;

    /**
     * Constructor.
     *
     * @param mod_feedback_structure $feedbackstructure
     * @param int $mygroupid currently selected group
     * @param bool $extradetails display additional details (time open, time closed)
     */
    public function __construct($feedbackstructure, $mygroupid = false, $extradetails = false) {
        $this->feedbackstructure = $feedbackstructure;
        $this->mygroupid = $mygroupid;
        $this->extradetails = $extradetails;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        $r = new stdClass();
        $r->completedcount = $this->feedbackstructure->count_completed_responses($this->mygroupid);
        $r->itemscount = count($this->feedbackstructure->get_items(true));
        if ($this->extradetails && ($timeopen = $this->feedbackstructure->get_feedback()->timeopen)) {
            $r->timeopen = userdate($timeopen);
        }
        if ($this->extradetails && ($timeclose = $this->feedbackstructure->get_feedback()->timeclose)) {
            $r->timeclose = userdate($timeclose);
        }

        return $r;
    }
}
