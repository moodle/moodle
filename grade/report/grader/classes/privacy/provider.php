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
 * Privacy Subsystem implementation for gradereport_grader.
 *
 * @package    gradereport_grader
 * @copyright  2018 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace gradereport_grader\privacy;

defined('MOODLE_INTERNAL') || die();

use \core_privacy\local\metadata\collection;
use \core_privacy\local\request\transform;
use \core_privacy\local\request\writer;

require_once $CFG->libdir.'/grade/constants.php';


/**
 * Privacy Subsystem for gradereport_grader implementing null_provider.
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
     * @param   collection     $itemcollection The initialised item collection to add items to.
     * @return  collection     A listing of user data stored through this system.
     */
    public static function get_metadata(collection $items) {
        // There are several user preferences (shared between different courses).
        // Show/hide toggles preferences.
        $items->add_user_preference('grade_report_showcalculations', 'privacy:metadata:preference:grade_report_showcalculations');
        $items->add_user_preference('grade_report_showeyecons', 'privacy:metadata:preference:grade_report_showeyecons');
        $items->add_user_preference('grade_report_showaverages', 'privacy:metadata:preference:grade_report_showaverages');
        $items->add_user_preference('grade_report_showlocks', 'privacy:metadata:preference:grade_report_showlocks');
        $items->add_user_preference('grade_report_showuserimage', 'privacy:metadata:preference:grade_report_showuserimage');
        $items->add_user_preference('grade_report_showactivityicons', 'privacy:metadata:preference:grade_report_showactivityicons');
        $items->add_user_preference('grade_report_showranges', 'privacy:metadata:preference:grade_report_showranges');
        $items->add_user_preference('grade_report_showanalysisicon', 'privacy:metadata:preference:grade_report_showanalysisicon');
        // Special rows preferences.
        $items->add_user_preference('grade_report_rangesdisplaytype', 'privacy:metadata:preference:grade_report_rangesdisplaytype');
        $items->add_user_preference('grade_report_rangesdecimalpoints', 'privacy:metadata:preference:grade_report_rangesdecimalpoints');
        $items->add_user_preference('grade_report_averagesdisplaytype', 'privacy:metadata:preference:grade_report_averagesdisplaytype');
        $items->add_user_preference('grade_report_averagesdecimalpoints', 'privacy:metadata:preference:grade_report_averagesdecimalpoints');
        $items->add_user_preference('grade_report_meanselection', 'privacy:metadata:preference:grade_report_meanselection');
        $items->add_user_preference('grade_report_shownumberofgrades', 'privacy:metadata:preference:grade_report_shownumberofgrades');
        // General preferences.
        $items->add_user_preference('grade_report_quickgrading', 'privacy:metadata:preference:grade_report_quickgrading');
        $items->add_user_preference('grade_report_showquickfeedback', 'privacy:metadata:preference:grade_report_showquickfeedback');
        $items->add_user_preference('grade_report_studentsperpage', 'privacy:metadata:preference:grade_report_studentsperpage');
        $items->add_user_preference('grade_report_showonlyactiveenrol', 'privacy:metadata:preference:grade_report_showonlyactiveenrol');
        $items->add_user_preference('grade_report_aggregationposition', 'privacy:metadata:preference:grade_report_aggregationposition');
        $items->add_user_preference('grade_report_enableajax', 'privacy:metadata:preference:grade_report_enableajax');

        // There is also one user preference which can be defined on each course.
        $items->add_user_preference('grade_report_grader_collapsed_categories', 'privacy:metadata:preference:grade_report_grader_collapsed_categories');

        return $items;
    }

    /**
     * Store all user preferences for the plugin.
     *
     * @param   int         $userid The userid of the user whose data is to be exported.
     */
    public static function export_user_preferences($userid) {
        $preferences = get_user_preferences();
        foreach ($preferences as $name => $value) {
            $prefname = null;
            $prefdescription = null;
            $transformedvalue = null;
            switch ($name) {
                case 'grade_report_showcalculations':
                case 'grade_report_showeyecons':
                case 'grade_report_showaverages':
                case 'grade_report_showlocks':
                case 'grade_report_showuserimage':
                case 'grade_report_showactivityicons':
                case 'grade_report_showranges':
                case 'grade_report_showanalysisicon':
                case 'grade_report_shownumberofgrades':
                case 'grade_report_quickgrading':
                case 'grade_report_showonlyactiveenrol':
                case 'grade_report_showquickfeedback':
                case 'grade_report_enableajax':
                    $prefname = $name;
                    $transformedvalue = transform::yesno($value);
                    break;
                case 'grade_report_meanselection':
                    $prefname = $name;
                    switch ($value) {
                        case GRADE_REPORT_MEAN_ALL:
                            $transformedvalue = get_string('meanall', 'grades');
                            break;
                        case GRADE_REPORT_MEAN_GRADED:
                            $transformedvalue = get_string('meangraded', 'grades');
                            break;
                    }
                    break;
                case 'grade_report_rangesdecimalpoints':
                case 'grade_report_averagesdecimalpoints':
                case 'grade_report_studentsperpage':
                    $prefname = $name;
                    $transformedvalue = $value;
                    break;
                case 'grade_report_rangesdisplaytype':
                case 'grade_report_averagesdisplaytype':
                    $prefname = $name;
                    switch ($value) {
                        case GRADE_REPORT_PREFERENCE_INHERIT:
                            $transformedvalue = get_string('inherit', 'grades');
                            break;
                        case GRADE_DISPLAY_TYPE_REAL:
                            $transformedvalue = get_string('real', 'grades');
                            break;
                        case GRADE_DISPLAY_TYPE_PERCENTAGE:
                            $transformedvalue = get_string('percentage', 'grades');
                            break;
                        case GRADE_DISPLAY_TYPE_LETTER:
                            $transformedvalue = get_string('letter', 'grades');
                            break;
                    }
                    break;
                case 'grade_report_aggregationposition':
                    $prefname = $name;
                    switch ($value) {
                        case GRADE_REPORT_AGGREGATION_POSITION_FIRST:
                            $transformedvalue = get_string('positionfirst', 'grades');
                            break;
                        case GRADE_REPORT_AGGREGATION_POSITION_LAST:
                            $transformedvalue = get_string('positionlast', 'grades');
                            break;
                    }
                    break;
                default:
                    if (strpos($name, 'grade_report_grader_collapsed_categories') === 0) {
                        $prefname = 'grade_report_grader_collapsed_categories';
                        $courseid = substr($name, strlen('grade_report_grader_collapsed_categories'));
                        $transformedvalue = $value;
                        $course = get_course($courseid);
                        $prefdescription = get_string(
                            'privacy:request:preference:'.$prefname,
                            'gradereport_grader',
                            (object) [
                                'name' => $course->fullname,
                            ]
                        );
                    }
            }

            if ($prefname !== null) {
                if ($prefdescription == null) {
                    $prefdescription = get_string('privacy:metadata:preference:'.$prefname, 'gradereport_grader');
                }
                writer::export_user_preference(
                    'gradereport_grader',
                    $prefname,
                    $transformedvalue,
                    $prefdescription
                );
            }
        }
    }
}
