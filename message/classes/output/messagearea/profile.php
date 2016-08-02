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
 * Contains class used to prepare a profile for display.
 *
 * @package   core_message
 * @copyright 2016 Mark Nelson <markn@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_message\output\messagearea;

use renderable;
use templatable;

/**
 * Class to prepare a profile for display.
 *
 * @package   core_message
 * @copyright 2016 Mark Nelson <markn@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class profile implements templatable, renderable {

    /**
     * The id of the user who is viewing the profile.
     */
    protected $currentuserid;

    /**
     * The profile of the user we are going to view.
     */
    protected $otheruser;

    /**
     * Constructor.
     *
     * @param int $currentuserid
     * @param \stdClass $otheruser
     */
    public function __construct($currentuserid, $otheruser) {
        $this->currentuserid = $currentuserid;
        $this->otheruser = $otheruser;
    }

    public function export_for_template(\renderer_base $output) {
        global $USER;

        $data = new \stdClass();
        $data->iscurrentuser = $USER->id == $this->currentuserid;
        $data->currentuserid = $this->currentuserid;
        $data->otheruserid = $this->otheruser->userid;
        $data->fullname = $this->otheruser->fullname;
        $data->email = $this->otheruser->email;
        if (!empty($this->otheruser->country)) {
            $data->country = get_string($this->otheruser->country, 'countries');
        } else {
            $data->country = '';
        }
        $data->city = $this->otheruser->city;
        $data->profileimageurl = $this->otheruser->profileimageurl;
        $data->profileimageurlsmall = $this->otheruser->profileimageurlsmall;
        $data->isblocked = $this->otheruser->isblocked;
        $data->iscontact = $this->otheruser->iscontact;

        return $data;
    }
}