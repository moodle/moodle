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
use renderer_base;
use templatable;
use pix_icon;
use moodle_url;
use stdClass;

/**
 * Data structure representing an attendance password icon.
 *
 * @copyright 2017 Dan Marsden
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class password_icon implements renderable, templatable {

    /**
     * @var string text to show
     */
    public $text;

    /**
     * @var string Extra descriptive text next to the icon
     */
    public $linktext = null;

    /**
     * Constructor
     *
     * @param string $text string for help page title,
     *  string with _help suffix is used for the actual help text.
     *  string with _link suffix is used to create a link to further info (if it exists)
     * @param string $sessionid
     */
    public function __construct($text, $sessionid) {
        $this->text  = $text;
        $this->sessionid = $sessionid;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output Used to do a final render of any components that need to be rendered for export.
     * @return array
     */
    public function export_for_template(renderer_base $output) {

        $title = get_string('password', 'attendance');

        $data = new stdClass();
        $data->heading = '';
        $data->text = $this->text;

        if ($this->includeqrcode == 1) {
            $pix = 'qrcode';
        } else {
            $pix = 'key';
        }

        $data->alt = $title;
        $data->icon = (new pix_icon($pix, '', 'attendance'))->export_for_template($output);
        $data->linktext = '';
        $data->title = $title;
        $data->url = (new moodle_url('/mod/attendance/password.php', [
            'session' => $this->sessionid]))->out(false);

        $data->ltr = !right_to_left();
        return $data;
    }
}
