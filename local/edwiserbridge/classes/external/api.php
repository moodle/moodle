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
 * Provides local_edwiserbridge\external\api class.
 *
 * @package     local_edwiserbridge
 * @copyright   2021 WisdmLabs (https://wisdmlabs.com/) <support@wisdmlabs.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author      Wisdmlabs
 */

namespace local_edwiserbridge\external;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/externallib.php');

use external_api;

/**
 * Provides an external API of the block.
 *
 * Each external function is implemented in its own trait. This class
 * aggregates them all.
 */
class api extends external_api {
    use eb_create_service;
    use eb_get_course_progress;
    use eb_get_edwiser_plugins_info;
    use eb_get_service_info;
    use eb_get_site_data;
    use eb_get_users;
    use eb_get_courses;
    use eb_link_service;
    use eb_test_connection;
    use edwiserbridge_local_get_course_enrollment_method;
    use edwiserbridge_local_update_course_enrollment_method;
    /* Setup wizard services */
    use edwiserbridge_local_setup_wizard_save_and_continue;
    use edwiserbridge_local_enable_plugin_settings;
    use edwiserbridge_local_setup_test_connection;
    use edwiserbridge_local_get_mandatory_settings;
}
