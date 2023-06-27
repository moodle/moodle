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
 * Provides local_edwiserbridge\external\course_progress_data trait.
 *
 * @package     local_edwiserbridge
 * @category    external
 * @copyright   2021 WisdmLabs (https://wisdmlabs.com/) <support@wisdmlabs.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author      Wisdmlabs
 */

namespace local_edwiserbridge\external;

defined('MOODLE_INTERNAL') || die();

use external_function_parameters;
use external_multiple_structure;
use external_single_structure;
use external_value;
use core_completion\progress;

// require_once($CFG->libdir.'/externallib.php');

/**
 * Trait implementing the external function local_edwiserbridge_course_progress_data
 */
trait eb_get_site_data {

    /**
     * functionality to get all site related data.
     * @param  string $siteindex siteindex
     * @return array
     */
    public static function eb_get_site_data($siteindex) {
        $params = self::validate_parameters(
            self::eb_get_site_data_parameters(),
            array('site_index' => $siteindex)
        );
        return get_synch_settings($params['site_index']);
    }

    /**
     * paramters defined for get site data function.
     */
    public static function eb_get_site_data_parameters() {
        return new external_function_parameters(
            array(
                'site_index' => new external_value(PARAM_TEXT, get_string('web_service_site_index', 'local_edwiserbridge'))
            )
        );
    }

    /**
     * paramters which will be returned from get site data function.
     */
    public static function eb_get_site_data_returns() {
        return new external_single_structure(
            array(
                'course_enrollment'    => new external_value(
                    PARAM_INT,
                    get_string('web_service_course_enrollment', 'local_edwiserbridge')
                ),
                'course_un_enrollment' => new external_value(
                    PARAM_INT,
                    get_string('web_service_course_un_enrollment', 'local_edwiserbridge')
                ),
                'user_creation'        => new external_value(
                    PARAM_INT,
                    get_string('web_service_user_creation', 'local_edwiserbridge')
                ),
                'user_updation'        => new external_value(
                    PARAM_INT,
                    get_string('web_service_user_update', 'local_edwiserbridge')
                ),
                'user_deletion'        => new external_value(
                    PARAM_INT,
                    get_string('web_service_user_deletion', 'local_edwiserbridge')
                ),
                'course_creation'        => new external_value(
                    PARAM_INT,
                    get_string('web_service_course_creation', 'local_edwiserbridge')
                ),
                'course_deletion'        => new external_value(
                    PARAM_INT,
                    get_string('web_service_course_deletion', 'local_edwiserbridge')
                ),
            )
        );
    }
}
