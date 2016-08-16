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
 * Badge deleted event.
 *
 * @package    core
 * @copyright  2016 Stephen Bourget
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\event;
defined('MOODLE_INTERNAL') || die();
require_once($CFG->libdir . '/badgeslib.php');


/**
 * Event triggered after a badge is deleted.
 *
 * @package    core
 * @since      Moodle 3.2
 * @copyright  2016 Stephen Bourget
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class badge_deleted extends base {

    /**
     * Set basic properties for the event.
     */
    protected function init() {
        $this->data['objecttable'] = 'badge';
        $this->data['crud'] = 'd';
        $this->data['edulevel'] = self::LEVEL_TEACHING;
    }

    /**
     * Returns localised general event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('eventbadgedeleted', 'badges');
    }

    /**
     * Returns non-localised event description with id's for admin use only.
     *
     * @return string
     */
    public function get_description() {
        return "The user with id '$this->userid' has deleted the badge with id '$this->objectid'.";
    }

    /**
     * Returns relevant URL.
     * @return \moodle_url
     */
    public function get_url() {
        if ($this->other['badgetype'] == BADGE_TYPE_COURSE) {
            // Course badge.
            $return = new \moodle_url('/badges/index.php',
                    array('type' => BADGE_TYPE_COURSE, 'id' => $this->other['courseid']));
        } else {
            // Site badge.
            $return = new \moodle_url('/badges/index.php', array('type' => BADGE_TYPE_SITE));
        }
        return $return;
    }

    /**
     * Custom validations.
     *
     * @throws \coding_exception
     * @return void
     */
    protected function validate_data() {
        parent::validate_data();

        if (!isset($this->objectid)) {
            throw new \coding_exception('The \'objectid\' must be set.');
        }
        if (!isset($this->other['badgetype'])) {
            throw new \coding_exception('The \'badgetype\' value must be set in other.');
        } else {
            if (($this->other['badgetype'] != BADGE_TYPE_COURSE) && ($this->other['badgetype'] != BADGE_TYPE_SITE)) {
                throw new \coding_exception('Invalid \'badgetype\' value.');
            }
        }
        if ($this->other['badgetype'] == BADGE_TYPE_COURSE) {
            if (!isset($this->other['courseid'])) {
                throw new \coding_exception('The \'courseid\' value must be set in other.');
            }
        }
    }

    /**
     * Used for maping events on restore
     * @return array
     */
    public static function get_objectid_mapping() {
        return array('db' => 'badge', 'restore' => 'badge');
    }

}


