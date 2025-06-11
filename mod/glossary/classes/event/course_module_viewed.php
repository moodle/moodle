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
 * The mod_glossary course module viewed event.
 *
 * @package    mod_glossary
 * @copyright  2014 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_glossary\event;
defined('MOODLE_INTERNAL') || die();

/**
 * The mod_glossary course module viewed event class.
 *
 * @property-read array $other {
 *     Extra information about event.
 *
 *     - string mode: (optional)
 * }
 *
 * @package    mod_glossary
 * @since      Moodle 2.7
 * @copyright  2014 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_module_viewed extends \core\event\course_module_viewed {

    /**
     * Init method.
     */
    protected function init() {
        $this->data['crud'] = 'r';
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
        $this->data['objecttable'] = 'glossary';
    }

    /**
     * Get URL related to the action.
     *
     * @return \moodle_url
     */
    public function get_url() {
        $params = array('id' => $this->contextinstanceid);
        if (!empty($this->other['mode'])) {
            $params['mode'] = $this->other['mode'];
        }
        return new \moodle_url("/mod/$this->objecttable/view.php", $params);
    }

    public static function get_objectid_mapping() {
        return array('db' => 'glossary', 'restore' => 'glossary');
    }

    public static function get_other_mapping() {
        // Nothing to map.
        return false;
    }
}
