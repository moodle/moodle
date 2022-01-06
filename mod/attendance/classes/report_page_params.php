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
 * Class definition for mod_attendance_report_page_params
 *
 * @package   mod_attendance
 * @copyright  2016 Dan Marsden http://danmarsden.com
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

/**
 * contains specific data/functions for report_page.
 *
 * @copyright  2016 Dan Marsden http://danmarsden.com
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_attendance_report_page_params extends mod_attendance_page_with_filter_controls {
    /** @var int */
    public $group;
    /** @var int */
    public $sort;
    /** @var int */
    public $showextrauserdetails;
    /** @var int */
    public $showsessiondetails;
    /** @var int */
    public $sessiondetailspos;

    /**
     * mod_attendance_report_page_params constructor.
     */
    public function  __construct() {
        $this->selectortype = self::SELECTOR_GROUP;
    }

    /**
     * Initialise params.
     *
     * @param stdClass $cm
     */
    public function init($cm) {
        parent::init($cm);

        if (!isset($this->group)) {
            $this->group = $this->get_current_sesstype() > 0 ? $this->get_current_sesstype() : 0;
        }
        if (!isset($this->sort)) {
            $this->sort = ATT_SORT_DEFAULT;
        }
    }

    /**
     * Get params for this page.
     * @return array
     */
    public function get_significant_params() {
        $params = array();

        if ($this->sort != ATT_SORT_DEFAULT) {
            $params['sort'] = $this->sort;
        }

        if (empty($this->showextrauserdetails)) {
            $params['showextrauserdetails'] = 0;
        }

        if (empty($this->showsessiondetails)) {
            $params['showsessiondetails'] = 0;
        }

        if ($this->sessiondetailspos != 'left') {
            $params['sessiondetailspos'] = $this->sessiondetailspos;
        }

        return $params;
    }
}
