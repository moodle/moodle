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
 * Evidence of prior learning deleted event.
 *
 * @package    core_competency
 * @copyright  2016 Serge Gauthier <serge.gauthier.2@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core\event;

use core\event\base;
use core_competency\user_evidence;

defined('MOODLE_INTERNAL') || die();

/**
 * Evidence of prior learning deleted event class.
 *
 * @property-read array $other {
 *      Extra information about event.
 * }
 *
 * @package    core_competency
 * @since      Moodle 3.1
 * @copyright  2016 Serge Gauthier <serge.gauthier.2@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class competency_user_evidence_deleted extends base {

    /**
     * Convenience method to instantiate the event.
     *
     * @param user_evidence $userevidence The evidence of prior learning.
     * @return self
     */
    final public static function create_from_user_evidence(user_evidence $userevidence) {
        if (!$userevidence->get('id')) {
            throw new \coding_exception('The evidence of prior learning ID must be set.');
        }
        $event = static::create(array(
            'contextid'  => $userevidence->get_context()->id,
            'objectid' => $userevidence->get('id'),
            'relateduserid' => $userevidence->get('userid')
        ));
        $event->add_record_snapshot(user_evidence::TABLE, $userevidence->to_record());
        return $event;
    }

    /**
     * Returns localised general event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('eventuserevidencedeleted', 'core_competency');
    }

    /**
     * Returns non-localised description of what happened.
     *
     * @return string
     */
    public function get_description() {
        return "The user with id '$this->userid' deleted the evidence of prior learning with id '$this->objectid'.";
    }

    /**
     * Initialise the event data.
     */
    protected function init() {
        $this->data['objecttable'] = user_evidence::TABLE;
        $this->data['crud'] = 'd';
        $this->data['edulevel'] = self::LEVEL_OTHER;
    }

    /**
     * Get_objectid_mapping method.
     *
     * @return string the name of the restore mapping the objectid links to
     */
    public static function get_objectid_mapping() {
        return base::NOT_MAPPED;
    }

}
