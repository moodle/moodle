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
 * Class containing data for myprofile block.
 *
 * @package    block_myprofile
 * @copyright  2018 Mihail Geshoski <mihail@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_myprofile\output;

defined('MOODLE_INTERNAL') || die();

use renderable;
use renderer_base;
use templatable;

/**
 * Class containing data for myprofile block.
 *
 * @copyright  2018 Mihail Geshoski <mihail@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class myprofile implements renderable, templatable {

    /**
     * @var object An object containing the configuration information for the current instance of this block.
     */
    protected $config;

    /**
     * Constructor.
     *
     * @param object $config An object containing the configuration information for the current instance of this block.
     */
    public function __construct($config) {
        $this->config = $config;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param \renderer_base $output
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        global $USER, $OUTPUT;

        $data = new \stdClass();

        if (!isset($this->config->display_picture) || $this->config->display_picture == 1) {
            $data->userpicture = $OUTPUT->user_picture($USER, array('class' => 'userpicture'));
        }

        $data->userfullname = fullname($USER);

        if (!isset($this->config->display_country) || $this->config->display_country == 1) {
            $countries = get_string_manager()->get_list_of_countries(true);
            if (isset($countries[$USER->country])) {
                $data->usercountry = $countries[$USER->country];
            }
        }

        if (!isset($this->config->display_city) || $this->config->display_city == 1) {
            $data->usercity = $USER->city;
        }

        if (!isset($this->config->display_email) || $this->config->display_email == 1) {
            $data->useremail = obfuscate_mailto($USER->email, '');
        }

        if (!empty($this->config->display_icq) && !empty($USER->icq)) {
            $data->usericq = s($USER->icq);
        }

        if (!empty($this->config->display_skype) && !empty($USER->skype)) {
            $data->userskype = s($USER->skype);
        }

        if (!empty($this->config->display_yahoo) && !empty($USER->yahoo)) {
            $data->useryahoo = s($USER->yahoo);
        }

        if (!empty($this->config->display_aim) && !empty($USER->aim)) {
            $data->useraim = s($USER->aim);
        }

        if (!empty($this->config->display_msn) && !empty($USER->msn)) {
            $data->usermsn = s($USER->msn);
        }

        if (!empty($this->config->display_phone1) && !empty($USER->phone1)) {
            $data->userphone1 = s($USER->phone1);
        }

        if (!empty($this->config->display_phone2) && !empty($USER->phone2)) {
            $data->userphone2 = s($USER->phone2);
        }

        if (!empty($this->config->display_institution) && !empty($USER->institution)) {
            $data->userinstitution = format_string($USER->institution);
        }

        if (!empty($this->config->display_address) && !empty($USER->address)) {
            $data->useraddress = format_string($USER->address);
        }

        if (!empty($this->config->display_firstaccess) && !empty($USER->firstaccess)) {
            $data->userfirstaccess = userdate($USER->firstaccess);
        }

        if (!empty($this->config->display_lastaccess) && !empty($USER->lastaccess)) {
            $data->userlastaccess = userdate($USER->lastaccess);
        }

        if (!empty($this->config->display_currentlogin) && !empty($USER->currentlogin)) {
            $data->usercurrentlogin = userdate($USER->currentlogin);
        }

        if (!empty($this->config->display_lastip) && !empty($USER->lastip)) {
            $data->userlastip = $USER->lastip;
        }

        return $data;
    }
}
