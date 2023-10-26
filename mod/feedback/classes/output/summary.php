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

    /**
     * Constructor.
     *
     * @todo MDL-71494 Final deprecation of the $extradetails parameter in Moodle 4.3
     * @param mod_feedback_structure $feedbackstructure
     * @param int $mygroupid currently selected group
     * @param bool|null $extradetails Deprecated
     */
    public function __construct($feedbackstructure, $mygroupid = false, $extradetails = null) {
        if (isset($extradetails)) {
            debugging('The $extradetails parameter is deprecated.', DEBUG_DEVELOPER);
        }
        $this->feedbackstructure = $feedbackstructure;
        $this->mygroupid = $mygroupid;
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

        return $r;
    }
}
