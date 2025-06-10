<?php
// This file is part of the customcert module for Moodle - http://moodle.org/
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
 * Creates an upload form on the settings page.
 *
 * @package    mod_customcert
 * @copyright  2013 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_customcert;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/adminlib.php');

/**
 * Class extends admin setting class to allow/process an uploaded file
 *
 * @package    mod_customcert
 * @copyright  2013 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_link extends \admin_setting_configtext {

    /**
     * @var string the link.
     */
    protected $link;

    /**
     * @var string the link name.
     */
    protected $linkname;

    /**
     * The admin_setting_link constructor.
     *
     * @param string $name
     * @param string $visiblename
     * @param string $description
     * @param string $linkname
     * @param mixed|string $link
     * @param int|null $defaultsetting
     * @param string $paramtype
     * @param null $size
     */
    public function __construct($name, $visiblename, $description, $linkname, $link, $defaultsetting,
                                $paramtype = PARAM_RAW, $size=null) {
        $this->link = $link;
        $this->linkname = $linkname;
        parent::__construct($name, $visiblename, $description, $defaultsetting, $paramtype, $size);
    }

    /**
     * Output the link to the upload image page.
     *
     * @param mixed $data
     * @param string $query
     * @return string
     */
    public function output_html($data, $query = '') {
        // Create a dummy variable for this field to avoid being redirected back to the upgrade settings page.
        $this->config_write($this->name, '');

        return format_admin_setting($this, $this->visiblename,
            \html_writer::link($this->link, $this->linkname), $this->description, true, '', null, $query);
    }
}
