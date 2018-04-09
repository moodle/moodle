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

defined('MOODLE_INTERNAL') || die();

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
     * @var int The id of the user we are going to view.
     */
    public $userid;

    /**
     * @var string The fullname.
     */
    public $fullname;

    /**
     * @var string The city.
     */
    public $city;

    /**
     * @var string The country.
     */
    public $country;

    /**
     * @var string The email.
     */
    public $email;

    /**
     * @var string The profile image url.
     */
    public $profileimageurl;

    /**
     * @var string The small profile image url.
     */
    public $profileimageurlsmall;

    /**
     * @var bool Is the user online?
     */
    public $isonline;

    /**
     * @var bool Is the user blocked?
     */
    public $isblocked;

    /**
     * @var bool Is the user a contact?
     */
    public $iscontact;

    /**
     * Constructor.
     *
     * @param \stdClass $profile
     */
    public function __construct($profile) {
        $this->userid = $profile->userid;
        $this->fullname = $profile->fullname;
        $this->isonline = $profile->isonline;
        $this->email = $profile->email;
        $this->country = $profile->country;
        $this->city = $profile->city;
        $this->profileimageurl = $profile->profileimageurl;
        $this->profileimageurlsmall = $profile->profileimageurlsmall;
        $this->isblocked = $profile->isblocked;
        $this->iscontact = $profile->iscontact;
    }

    public function export_for_template(\renderer_base $output) {
        $data = new \stdClass();
        $data->userid = $this->userid;
        $data->fullname = $this->fullname;
        $data->showonlinestatus = is_null($this->isonline) ? false : true;
        $data->isonline = $this->isonline;
        $data->email = $this->email;
        if (!empty($this->country)) {
            $data->country = get_string($this->country, 'countries');
        } else {
            $data->country = '';
        }
        $data->city = $this->city;
        $data->profileimageurl = $this->profileimageurl;
        $data->profileimageurlsmall = $this->profileimageurlsmall;
        $data->isblocked = $this->isblocked;
        $data->iscontact = $this->iscontact;

        return $data;
    }
}
