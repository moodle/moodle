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
 * Privacy Subsystem implementation for scormreport_interactions.
 *
 * @package    scormreport_interactions
 * @copyright  2018 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace scormreport_interactions\privacy;

defined('MOODLE_INTERNAL') || die();

use \core_privacy\local\metadata\collection;
use \core_privacy\local\request\transform;
use \core_privacy\local\request\writer;

/**
 * Privacy Subsystem for scormreport_interactions.
 *
 * @copyright  2018 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\user_preference_provider {

    /**
     * Returns meta data about this system.
     *
     * @param  collection $collection The initialised item collection to add items to.
     * @return collection A listing of user data stored through this system.
     */
    public static function get_metadata(collection $collection) : collection {
        // User preferences shared between different scorm reports.
        $collection->add_user_preference('scorm_report_pagesize', 'privacy:metadata:preference:scorm_report_pagesize');

        // User preferences specific for this scorm report.
        $collection->add_user_preference(
            'scorm_report_interactions_qtext',
            'privacy:metadata:preference:scorm_report_interactions_qtext'
        );
        $collection->add_user_preference(
            'scorm_report_interactions_resp',
            'privacy:metadata:preference:scorm_report_interactions_resp'
        );
        $collection->add_user_preference(
            'scorm_report_interactions_right',
            'privacy:metadata:preference:scorm_report_interactions_right'
        );
        $collection->add_user_preference(
            'scorm_report_interactions_result',
            'privacy:metadata:preference:scorm_report_interactions_result'
        );

        return $collection;
    }

    /**
     * Store all user preferences for the plugin.
     *
     * @param  int $userid The userid of the user whose data is to be exported.
     */
    public static function export_user_preferences(int $userid) {
        static::get_and_export_user_preference($userid, 'scorm_report_pagesize');
        static::get_and_export_user_preference($userid, 'scorm_report_interactions_qtext', true);
        static::get_and_export_user_preference($userid, 'scorm_report_interactions_resp', true);
        static::get_and_export_user_preference($userid, 'scorm_report_interactions_right', true);
        static::get_and_export_user_preference($userid, 'scorm_report_interactions_result', true);
    }

    /**
     * Get and export a user preference.
     *
     * @param  int     $userid The userid of the user whose data is to be exported.
     * @param  string  $userpreference The user preference to export.
     * @param  boolean $transform If true, transform value to yesno.
     */
    protected static function get_and_export_user_preference(int $userid, string $userpreference, $transform = false) {
        $prefvalue = get_user_preferences($userpreference, null, $userid);
        if ($prefvalue !== null) {
            if ($transform) {
                $transformedvalue = transform::yesno($prefvalue);
            } else {
                $transformedvalue = $prefvalue;
            }
            writer::export_user_preference(
                'scormreport_interactions',
                $userpreference,
                $transformedvalue,
                get_string('privacy:metadata:preference:'.$userpreference, 'scormreport_interactions')
            );
        }
    }
}
