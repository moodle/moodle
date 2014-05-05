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
 * The tag added event.
 *
 * @package    core
 * @copyright  2014 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\event;

defined('MOODLE_INTERNAL') || die();

/**
 * Event class for when a tag has been added to an item.
 *
 * @property-read array $other {
 *      Extra information about event.
 *
 *      - int tagid: the id of the tag.
 *      - string tagname: the name of the tag.
 *      - string tagrawname: the raw name of the tag.
 *      - int itemid: the id of the item tagged.
 *      - string itemtype: the type of item tagged.
 * }
 *
 * @package    core
 * @since      Moodle 2.7
 * @copyright  2014 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tag_added extends base {

    /**
     * Initialise the event data.
     */
    protected function init() {
        $this->data['objecttable'] = 'tag_instance';
        $this->data['crud'] = 'c';
        $this->data['edulevel'] = self::LEVEL_OTHER;
    }

    /**
     * Returns localised general event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('eventtagadded', 'tag');
    }

    /**
     * Returns non-localised description of what happened.
     *
     * @return string
     */
    public function get_description() {
        return "The user with the id '$this->userid' added the tag with the id '{$this->other['tagid']}' to the item type '" .
            s($this->other['itemtype']) . "' with the id '{$this->other['itemid']}'.";
    }

    /**
     * Return legacy data for add_to_log().
     *
     * @return array
     */
    protected function get_legacy_logdata() {
        if ($this->other['itemtype'] === 'course') {
            $url = 'tag/search.php?query=' . urlencode($this->other['tagrawname']);
            return array($this->courseid, 'coursetags', 'add', $url, 'Course tagged');
        }

        return null;
    }

    /**
     * Custom validation.
     *
     * @throws \coding_exception when validation does not pass.
     * @return void
     */
    protected function validate_data() {
        parent::validate_data();

        if (!isset($this->other['tagid'])) {
            throw new \coding_exception('The \'tagid\' value must be set in other.');
        }

        if (!isset($this->other['itemid'])) {
            throw new \coding_exception('The \'itemid\' value must be set in other.');
        }

        if (!isset($this->other['itemtype'])) {
            throw new \coding_exception('The \'itemtype\' value must be set in other.');
        }

        if (!isset($this->other['tagname'])) {
            throw new \coding_exception('The \'tagname\' value must be set in other.');
        }

        if (!isset($this->other['tagrawname'])) {
            throw new \coding_exception('The \'tagrawname\' value must be set in other.');
        }
    }
}
