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
 * User profile field updated event.
 *
 * @package    core
 * @copyright  2017 Web Courseworks, Ltd. {@link http://www.webcourseworks.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\event;

defined('MOODLE_INTERNAL') || die();

/**
 * Event when user profile is updated.
 *
 * @property-read array $other {
 *      Extra information about event.
 *
 *      - string name: the name of the field.
 * }
 *
 * @package    core
 * @copyright  2017 Web Courseworks, Ltd. {@link http://www.webcourseworks.com}
 * @since      Moodle 3.4
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_info_category_updated extends base {

    /**
     * Initialise the event data.
     */
    protected function init() {
        $this->data['objecttable'] = 'user_info_category';
        $this->data['crud'] = 'u';
        $this->data['edulevel'] = self::LEVEL_OTHER;
    }

    /**
     * Creates an event from a profile info category.
     *
     * @since Moodle 3.4
     * @param \stdClass $category A snapshot of the updated category.
     * @return \core\event\base
     */
    public static function create_from_category($category) {
        $event = self::create(array(
            'objectid' => $category->id,
            'context' => \context_system::instance(),
            'other' => array(
                'name' => $category->name,
            )
        ));

        $event->add_record_snapshot('user_info_category', $category);

        return $event;
    }

    /**
     * Returns localised event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('eventuserinfocategoryupdated');
    }

    /**
     * Returns non-localised event description with id's for admin use only.
     *
     * @return string
     */
    public function get_description() {
        $name = s($this->other['name']);
        return "The user with id '$this->userid' updated the user profile field category '$name' with id '$this->objectid'.";
    }

    /**
     * Returns relevant URL.
     *
     * @return \moodle_url
     */
    public function get_url() {
        return new \moodle_url('/user/profile/index.php', array(
            'action' => 'editcategory',
            'id' => $this->objectid
        ));
    }

    /**
     * Custom validation.
     *
     * @throws \coding_exception
     * @return void
     */
    protected function validate_data() {
        parent::validate_data();

        if (!isset($this->other['name'])) {
            throw new \coding_exception('The \'name\' value must be set in other.');
        }
    }

    /**
     * Get the backup/restore table mapping for this event.
     *
     * @return string
     */
    public static function get_objectid_mapping() {
        return base::NOT_MAPPED;
    }
}
