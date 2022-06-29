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

namespace tool_brickfield\event;

use tool_brickfield\manager;

/**
 * Class report_downloaded
 *
 * @package tool_brickfield
 * @copyright 2020 onward Brickfield Education Labs, https://www.brickfield.ie
 * @author Mike Churchward <mike@brickfieldlabs.ie>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL
 */
class report_downloaded extends \core\event\base {

    /**
     * Init function.
     */
    protected function init() {
        $this->data['crud'] = 'r';
        $this->data['edulevel'] = self::LEVEL_OTHER;
    }

    /**
     * Get name.
     * @return \lang_string|string
     */
    public static function get_name() {
        return get_string('eventreport_downloaded', manager::PLUGINNAME);
    }

    /**
     * Get description.
     * @return \lang_string|string|null
     */
    public function get_description() {
        return get_string('eventreport_downloadeddesc', manager::PLUGINNAME, $this->courseid);
    }
}
