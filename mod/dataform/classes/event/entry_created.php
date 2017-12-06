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
 * The mod_dataforwm entry created event.
 *
 * @property-read array $other {
 *      Extra information about event.
 *
 *      int dataid the id of the dataform activity.
 *      int viewid the id of the view where the event occured.
 *      int userid the id of the entry owner.
 * }
 *
 * @package    mod_dataform
 * @copyright  2013 Itamar Tzadok {@link http://substantialmethods.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_dataform\event;

defined('MOODLE_INTERNAL') || die();

class entry_created extends entry_base {

    /**
     * Init method.
     *
     * @return void
     */
    protected function init() {
        $this->data['objecttable'] = 'dataform_entries';
        $this->data['crud'] = 'c';
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
    }

    /**
     * Get the legacy event log data.
     *
     * @return array
     */
    public function get_legacy_logdata() {
        $d = $this->other['dataid'];
        $viewid = $this->other['viewid'];
        return array($this->courseid, 'dataform', 'entries add', "view.php?d=$d&amp;view=$viewid&amp;eids=$this->objectid", $this->objectid, $this->contextinstanceid);
    }

}
