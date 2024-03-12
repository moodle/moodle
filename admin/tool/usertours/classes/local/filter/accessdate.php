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
namespace tool_usertours\local\filter;

use context;
use tool_usertours\tour;

/**
 * Access date filter. Used to determine if USER should see a tour based on a particular access date.
 *
 * @package    tool_usertours
 * @copyright  2019 Tom Dickman <tomdickman@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class accessdate extends base {
    /**
     * Access date filtering constant for setting base date as account creation date.
     */
    const FILTER_ACCOUNT_CREATION = 'tool_usertours_accountcreation';

    /**
     * Access date filtering constant for setting base date as account first login date.
     */
    const FILTER_FIRST_LOGIN = 'tool_usertours_firstlogin';

    /**
     * Access date filtering constant for setting base date as account last login date.
     */
    const FILTER_LAST_LOGIN = 'tool_usertours_lastlogin';

    /**
     * Default this filter to not be enabled.
     */
    const FILTER_ENABLED_DEFAULT = 0;

    /**
     * The name of the filter.
     *
     * @return  string
     */
    public static function get_filter_name() {
        return 'accessdate';
    }

    /**
     * Retrieve the list of available filter options.
     *
     * @return  array  An array whose keys are the valid options
     *                 And whose values are the values to display
     * @throws \coding_exception
     */
    public static function get_filter_options() {

        return [
            self::FILTER_ACCOUNT_CREATION => get_string('filter_date_account_creation', 'tool_usertours'),
            self::FILTER_FIRST_LOGIN => get_string('filter_date_first_login', 'tool_usertours'),
            self::FILTER_LAST_LOGIN => get_string('filter_date_last_login', 'tool_usertours'),
        ];
    }

    /**
     * Add the form elements for the filter to the supplied form.
     *
     * @param \MoodleQuickForm $mform The form to add filter settings to.
     *
     * @throws \coding_exception
     */
    public static function add_filter_to_form(\MoodleQuickForm &$mform) {

        $filtername = static::get_filter_name();
        $key = "filter_{$filtername}";
        $range = "{$key}_range";
        $enabled = "{$key}_enabled";

        $mform->addElement(
            'advcheckbox',
            $enabled,
            get_string($key, 'tool_usertours'),
            get_string('filter_accessdate_enabled', 'tool_usertours'),
            null,
            [0, 1]
        );
        $mform->addHelpButton($enabled, $enabled, 'tool_usertours');

        $mform->addElement('select', $key, ' ', self::get_filter_options());
        $mform->setDefault($key, self::FILTER_ACCOUNT_CREATION);
        $mform->hideIf($key, $enabled, 'notchecked');

        $mform->addElement('duration', $range, null, [
            'optional' => false,
            'defaultunit' => DAYSECS,
        ]);
        $mform->setDefault($range, 90 * DAYSECS);
        $mform->hideIf($range, $enabled, 'notchecked');
    }

    /**
     * Prepare the filter values for the form.
     *
     * @param   tour            $tour       The tour to prepare values from
     * @param   stdClass        $data       The data value
     * @return  stdClass
     */
    public static function prepare_filter_values_for_form(tour $tour, \stdClass $data) {
        $filtername = static::get_filter_name();

        $key = "filter_{$filtername}";
        $range = "{$key}_range";
        $enabled = "{$key}_enabled";

        $values = $tour->get_filter_values($filtername);

        // Prepare the advanced checkbox value and prepare filter values based on previously set values.
        if (!empty($values)) {
            $data->$enabled = $values->$enabled ? $values->$enabled : self::FILTER_ENABLED_DEFAULT;
            if ($data->$enabled) {
                if (isset($values->$key)) {
                    $data->$key = $values->$key;
                }
                if (isset($values->$range)) {
                    $data->$range = $values->$range;
                }
            }
        } else {
            $data->$enabled = self::FILTER_ENABLED_DEFAULT;
        }
        return $data;
    }

    /**
     * Save the filter values from the form to the tour.
     *
     * @param   tour            $tour       The tour to save values to
     * @param   \stdClass        $data       The data submitted in the form
     */
    public static function save_filter_values_from_form(tour $tour, \stdClass $data) {
        $filtername = static::get_filter_name();
        $key = "filter_{$filtername}";
        $range = "{$key}_range";
        $enabled = "{$key}_enabled";

        $savedata = [];
        $savedata[$key] = $data->$key;
        $savedata[$range] = $data->$range;
        $savedata[$enabled] = $data->$enabled;

        $tour->set_filter_values($filtername, $savedata);
    }

    /**
     * Check whether the filter matches the specified tour and/or context.
     *
     * @param   tour        $tour       The tour to check
     * @param   context     $context    The context to check
     * @return  boolean
     */
    public static function filter_matches(tour $tour, context $context) {
        global $USER;

        $filtername = static::get_filter_name();
        $key = "filter_{$filtername}";
        $range = "{$key}_range";
        $enabled = "{$key}_enabled";

        // Default behaviour is to match filter.
        $result = true;
        $values = (array) $tour->get_filter_values(self::get_filter_name());

        // If the access date filter is not enabled, end here.
        if (empty($values[$enabled])) {
            return $result;
        }

        if (!empty($values[$key])) {
            switch ($values[$key]) {
                case (self::FILTER_ACCOUNT_CREATION):
                    $filterbasedate = (int) $USER->timecreated;
                    break;
                case (self::FILTER_FIRST_LOGIN):
                    $filterbasedate = (int) $USER->firstaccess;
                    break;
                case (self::FILTER_LAST_LOGIN):
                    $filterbasedate = (int) $USER->lastlogin;
                    break;
                default:
                    // Use account creation as default.
                    $filterbasedate = (int) $USER->timecreated;
                    break;
            }
            // If the base date has no value because a user hasn't accessed Moodle yet, default to account creation.
            if (empty($filterbasedate)) {
                $filterbasedate = (int) $USER->timecreated;
            }

            if (!empty($values[$range])) {
                $filterrange = (int) $values[$range];
            } else {
                $filterrange = 90 * DAYSECS;
            }

            // If we're outside the set range from the set base date, filter out tour.
            if ((time() > ($filterbasedate + $filterrange))) {
                $result = false;
            }
        }
        return $result;
    }
}
