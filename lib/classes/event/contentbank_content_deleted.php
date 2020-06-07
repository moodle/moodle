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
 * Contentbank content deleted event.
 *
 * @package    core
 * @copyright  2020 Amaia Anabitarte <amaia@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\event;

/**
 * Content bank content deleted class.
 *
 * @property-read array $other {
 *      Extra information about event.
 *      - string contenttype: the contenttype of the content.
 *      - string name: the name of the content.
 * }
 *
 * @package    core
 * @since      Moodle 3.9
 * @copyright  2020 Amaia Anabitarte <amaia@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class contentbank_content_deleted extends base {

    /**
     * Initialise the event data.
     */
    protected function init() {
        $this->data['objecttable'] = 'contentbank_content';
        $this->data['crud'] = 'd';
        $this->data['edulevel'] = self::LEVEL_OTHER;
    }

    /**
     * Creates an event from content bank content object
     *
     * @since Moodle 3.9
     * @param \stdClass $record Data to create the event
     * @return contentbank_content_deleted
     */
    public static function create_from_record(\stdClass $record) {
        $event = self::create([
            'objectid' => $record->id,
            'relateduserid' => $record->usercreated,
            'context' => \context::instance_by_id($record->contextid),
            'other' => [
                'contenttype' => $record->contenttype,
                'name' => $record->name
            ]
        ]);
        return $event;
    }

    /**
     * Returns localised general event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('eventcontentdeleted', 'core_contentbank');
    }

    /**
     * Returns non-localised description of what happened.
     *
     * @return string
     */
    public function get_description() {
        return "The user with id '$this->userid' deleted the content with id '$this->objectid'.";
    }

    /**
     * Custom validation.
     *
     * @throws \coding_exception
     * @return void
     */
    protected function validate_data() {
        parent::validate_data();

        if (!isset($this->other['contenttype'])) {
            throw new \coding_exception('The \'contenttype\' value must be set in other.');
        }

        if (!isset($this->other['name'])) {
            throw new \coding_exception('The \'name\' value must be set in other.');
        }
    }

    /**
     * Used for mapping events on restore
     *
     * @return array
     */
    public static function get_objectid_mapping() {
        return array('db' => 'contentbank_content', 'restore' => 'contentbank_content');
    }

    /**
     * Used for mapping events on restore
     *
     * @return bool
     */
    public static function get_other_mapping() {
        // No mapping required.
        return false;
    }
}
