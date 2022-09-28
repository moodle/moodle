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
 * Attendance module renderable component.
 *
 * @package    mod_attendance
 * @copyright  2022 Dan Marsden
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_attendance\output;

use renderable;
use stdClass;
use mod_attendance_structure;
use moodle_url;

/**
 * Class filter_controls
 * @copyright  2011 Artem Andreev <andreev.artem@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class filter_controls implements renderable {
    /** @var int current view mode */
    public $pageparams;
    /** @var stdclass  */
    public $cm;
    /** @var int  */
    public $curdate;
    /** @var int  */
    public $prevcur;
    /** @var int  */
    public $nextcur;
    /** @var string  */
    public $curdatetxt;
    /** @var boolean  */
    public $reportcontrol;
    /** @var string  */
    private $urlpath;
    /** @var array  */
    private $urlparams;
    /** @var mod_attendance_structure */
    public $att;

    /**
     * filter_controls constructor.
     * @param mod_attendance_structure $att
     * @param bool $report
     */
    public function __construct(mod_attendance_structure $att, $report = false) {
        global $PAGE;

        $this->pageparams = $att->pageparams;

        $this->cm = $att->cm;

        // This is a report control only if $reports is true and the attendance block can be graded.
        $this->reportcontrol = $report;

        $this->curdate = $att->pageparams->curdate;

        $date = usergetdate($att->pageparams->curdate);
        $mday = $date['mday'];
        $mon = $date['mon'];
        $year = $date['year'];

        switch ($this->pageparams->view) {
            case ATT_VIEW_DAYS:
                $format = get_string('strftimedm', 'attendance');
                $this->prevcur = make_timestamp($year, $mon, $mday - 1);
                $this->nextcur = make_timestamp($year, $mon, $mday + 1);
                $this->curdatetxt = userdate($att->pageparams->startdate, $format);
                break;
            case ATT_VIEW_WEEKS:
                $format = get_string('strftimedm', 'attendance');
                $this->prevcur = $att->pageparams->startdate - WEEKSECS;
                $this->nextcur = $att->pageparams->startdate + WEEKSECS;
                $this->curdatetxt = userdate($att->pageparams->startdate, $format).
                                    " - ".userdate($att->pageparams->enddate, $format);
                break;
            case ATT_VIEW_MONTHS:
                $format = '%B';
                $this->prevcur = make_timestamp($year, $mon - 1);
                $this->nextcur = make_timestamp($year, $mon + 1);
                $this->curdatetxt = userdate($att->pageparams->startdate, $format);
                break;
        }

        $this->urlpath = $PAGE->url->out_omit_querystring();
        $params = $att->pageparams->get_significant_params();
        $params['id'] = $att->cm->id;
        $this->urlparams = $params;

        $this->att = $att;
    }

    /**
     * Helper function for url.
     *
     * @param array $params
     * @return moodle_url
     */
    public function url($params=array()) {
        $params = array_merge($this->urlparams, $params);

        return new moodle_url($this->urlpath, $params);
    }

    /**
     * Helper function for url path.
     * @return string
     */
    public function url_path() {
        return $this->urlpath;
    }

    /**
     * Helper function for url_params.
     * @param array $params
     * @return array
     */
    public function url_params($params=array()) {
        $params = array_merge($this->urlparams, $params);

        return $params;
    }

    /**
     * Return groupmode.
     * @return int
     */
    public function get_group_mode() {
        return $this->att->get_group_mode();
    }

    /**
     * Return groupslist.
     * @return mixed
     */
    public function get_sess_groups_list() {
        return $this->att->pageparams->get_sess_groups_list();
    }

    /**
     * Get current session type.
     * @return mixed
     */
    public function get_current_sesstype() {
        return $this->att->pageparams->get_current_sesstype();
    }
}
