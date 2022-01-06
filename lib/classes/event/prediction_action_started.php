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
 * Prediction action clicked event.
 *
 * @property-read array $other {
 *      Extra information about event.
 *
 *      - string actionname: The action name
 * }
 *
 * @package    core_analytics
 * @copyright  2017 David Monllao {@link http://www.davidmonllao.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\event;
defined('MOODLE_INTERNAL') || die();

/**
 * Event triggered after a user clicked on one of the prediction suggested actions.
 *
 * @package    core_analytics
 * @copyright  2017 David Monllao {@link http://www.davidmonllao.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class prediction_action_started extends \core\event\base {

    /**
     * Set basic properties for the event.
     */
    protected function init() {
        $this->data['objecttable'] = 'analytics_predictions';

        // Marked as create because even if the action is something like viewing a course
        // they are starting an action from a prediction, which is kind-of creating an outcome.
        $this->data['crud'] = 'c';
        // It will depend on the action, we have no idea really but we need to chose one and
        // the user is learning from the prediction so LEVEL_PARTICIPATING seems more appropriate
        // than LEVEL_OTHER.
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
    }

    /**
     * Returns localised general event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('eventpredictionactionstarted', 'analytics');
    }

    /**
     * Returns non-localised event description with id's for admin use only.
     *
     * @return string
     */
    public function get_description() {
        return "The user with id '$this->userid' has started '{$this->other['actionname']}' action for the prediction with id '" .
            $this->objectid . "'.";
    }

    /**
     * Returns relevant URL.
     * @return \moodle_url
     */
    public function get_url() {
        return new \moodle_url('/report/insights/prediction.php', array('id' => $this->objectid));
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
    }

    /**
     * get_objectid_mapping
     *
     * @return array
     */
    public static function get_objectid_mapping() {
        return array('db' => 'analytics_predictions');
    }
}
