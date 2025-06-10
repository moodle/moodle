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
 * A link to an admin tool.
 *
 * @package local_o365
 * @author James McQuillan <james.mcquillan@remote-learner.net>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2014 onwards Microsoft, Inc. (http://microsoft.com/)
 */

namespace local_o365\adminsetting;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot.'/lib/adminlib.php');

/**
 * Not a real setting - just a link to an admin tool.
 */
class toollink extends \admin_setting {
    /**
     * Constructor.
     *
     * @param string $name
     * @param string $visiblename
     * @param string $linktext
     * @param string $linkurl
     * @param string $description
     */
    public function __construct($name, $visiblename, $linktext, $linkurl, $description) {
        $this->nosave = true;
        $this->linktext = $linktext;
        $this->linkurl = $linkurl;
        parent::__construct($name, $visiblename, $description, '');
    }

    /**
     * Get setting value, but this is not a real setting.
     *
     * @return bool Always returns true
     */
    public function get_setting() {
        return true;
    }

    /**
     * Get default setting - always true.
     *
     * @return bool Always returns true
     */
    public function get_defaultsetting() {
        return true;
    }

    /**
     * Never write settings.
     *
     * @param mixed $data
     * @return string
     */
    public function write_setting($data) {
        return '';
    }

    /**
     * Return an HTML string.
     *
     * @param mixed $data
     * @param string $query
     * @return string
     */
    public function output_html($data, $query = '') {
        $settinghtml = \html_writer::link($this->linkurl, $this->linktext);
        return format_admin_setting($this, $this->visiblename, $settinghtml, $this->description, false);
    }
}
