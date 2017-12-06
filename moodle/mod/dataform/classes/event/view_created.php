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
 * The mod_dataform view created event.
 *
 * @property-read array $other {
 *      Extra information about event.
 *
 *      string viewname the name of the view.
 *      int dataid the id of the dataform activity.
 * }
 *
 * @package    mod_dataform
 * @copyright  2014 Itamar Tzadok <itamar@substantialmethods.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_dataform\event;

defined('MOODLE_INTERNAL') || die();

class view_created extends view_base {

    /**
     * Init method.
     *
     * @return void
     */
    protected function init() {
        $this->data['objecttable'] = 'dataform_views';
        $this->data['crud'] = 'c';
        $this->data['edulevel'] = self::LEVEL_TEACHING;
    }

    /**
     * Get the legacy event log data.
     *
     * @return array
     */
    public function get_legacy_logdata() {
        return array($this->courseid, 'dataform', 'views add', 'view/index.php?d=' . $this->other['dataid'] . '&amp;vedit=' .
            $this->objectid, $this->objectid, $this->contextinstanceid);
    }
}
