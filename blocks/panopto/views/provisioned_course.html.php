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
 * Displays provisioning and user sync information for Panopto.
 *
 * This template renders details of an attempted Panopto course provisioning,
 * including course and server information, error messages, and user sync details.
 *
 * @package    block_panopto
 * @copyright  Panopto 2009 - 2024
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Ensures the file is accessed within Moodle.
defined('MOODLE_INTERNAL') || die();
?>

<div class='block_panopto'>
    <div class='panoptoProcessInformation'>
        <div class='value'>
        <?php
        // Check if there is provisioned data to display.
        if (!empty($provisioneddata)) {
            if (!empty($provisioneddata->errormessage)) {
            ?>
                <div class='errorMessage'>
                    <?php
                    /**
                     * Displays an error message for a general provisioning failure.
                     *
                     * @package block_panopto
                     * @copyright  Panopto 2009 - 2024
                     * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
                     *
                     * @var string $provisioneddata->errormessage Error message text
                     */
                    echo $provisioneddata->errormessage;
                    ?>
                </div>
                <br />
                <div class='attribute'>
                    <?php
                    /**
                     * Displays the label for the attempted Moodle course ID.
                     *
                     * @package block_panopto
                     * @copyright  Panopto 2009 - 2024
                     * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
                     */
                    echo get_string('attempted_moodle_course_id', 'block_panopto');
                    ?>
                </div>
                <div class='value'>
                    <?php
                    /**
                     * Displays the Moodle course ID associated with the provisioning attempt.
                     *
                     * @package block_panopto
                     * @copyright  Panopto 2009 - 2024
                     * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
                     *
                     * @var int $provisioneddata->moodlecourseid Moodle course ID
                     */
                    echo $provisioneddata->moodlecourseid;
                    ?>
                </div>
                <div class='attribute'>
                    <?php
                    /**
                     * Displays the label for the attempted Panopto server.
                     *
                     * @package block_panopto
                     * @copyright  Panopto 2009 - 2024
                     * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
                     */
                    echo get_string('attempted_panopto_server', 'block_panopto');
                    ?>
                </div>
                <div class='value'>
                    <?php
                    /**
                     * Displays the Panopto server name associated with the provisioning attempt.
                     *
                     * @package block_panopto
                     * @copyright  Panopto 2009 - 2024
                     * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
                     *
                     * @var string $provisioneddata->servername Panopto server name
                     */
                    echo $provisioneddata->servername;
                    ?>
                </div>
                <?php
            } else if (isset($provisioneddata->accesserror) && $provisioneddata->accesserror === true) {
            ?>
                <div class='errorMessage'>
                    <?php
                    /**
                     * Displays an access error message when provisioning fails due to access issues.
                     *
                     * @package block_panopto
                     * @copyright  Panopto 2009 - 2024
                     * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
                     */
                    echo get_string('provision_access_error', 'block_panopto');
                    ?>
                </div>
                <br />
                <div class='attribute'>
                    <?php
                    /**
                     * Displays the label for the attempted Moodle course ID in case of access error.
                     *
                     * @package block_panopto
                     * @copyright  Panopto 2009 - 2024
                     * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
                     */
                    echo get_string('attempted_moodle_course_id', 'block_panopto');
                    ?>
                </div>
                <div class='value'>
                    <?php
                    /**
                     * Displays the Moodle course ID associated with the provisioning attempt.
                     *
                     * @package block_panopto
                     * @copyright  Panopto 2009 - 2024
                     * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
                     *
                     * @var int $provisioneddata->moodlecourseid Moodle course ID
                     */
                    echo $provisioneddata->moodlecourseid;
                    ?>
                </div>
                <div class='attribute'>
                    <?php
                    /**
                     * Displays the label for the attempted Panopto server in case of access error.
                     *
                     * @package block_panopto
                     * @copyright  Panopto 2009 - 2024
                     * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
                     */
                    echo get_string('attempted_panopto_server', 'block_panopto');
                    ?>
                </div>
                <div class='value'>
                    <?php
                    /**
                     * Displays the Panopto server name associated with the provisioning attempt.
                     *
                     * @package block_panopto
                     * @copyright  Panopto 2009 - 2024
                     * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
                     *
                     * @var string $provisioneddata->servername Panopto server name
                     */
                    echo $provisioneddata->servername;
                    ?>
                </div>
                <?php
            } else if (isset($provisioneddata->unknownerror) && $provisioneddata->unknownerror === true) {
            ?>
                <div class='errorMessage'>
                    <?php
                    /**
                     * Displays a general error message when an unknown error occurs during provisioning.
                     *
                     * @package block_panopto
                     * @copyright  Panopto 2009 - 2024
                     * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
                     */
                    echo get_string('provision_error', 'block_panopto');
                    ?>
                </div>
                <br />
                <div class='attribute'>
                    <?php
                    /**
                     * Displays the label for the attempted Moodle course ID in case of unknown error.
                     *
                     * @package block_panopto
                     * @copyright  Panopto 2009 - 2024
                     * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
                     */
                    echo get_string('attempted_moodle_course_id', 'block_panopto');
                    ?>
                </div>
                <div class='value'>
                    <?php
                    /**
                     * Displays the Moodle course ID associated with the provisioning attempt.
                     *
                     * @package block_panopto
                     * @copyright  Panopto 2009 - 2024
                     * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
                     *
                     * @var int $provisioneddata->moodlecourseid Moodle course ID
                     */
                    echo $provisioneddata->moodlecourseid;
                    ?>
                </div>
                <div class='attribute'>
                    <?php
                    /**
                     * Displays the label for the attempted Panopto server in case of unknown error.
                     *
                     * @package block_panopto
                     * @copyright  Panopto 2009 - 2024
                     * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
                     */
                    echo get_string('attempted_panopto_server', 'block_panopto');
                    ?>
                </div>
                <div class='value'>
                    <?php
                    /**
                     * Displays the Panopto server name associated with the provisioning attempt.                     *
                     *
                     * @package block_panopto
                     * @copyright  Panopto 2009 - 2024
                     * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
                     *
                     * @var string $provisioneddata->servername Panopto server name
                     */
                    echo $provisioneddata->servername;
                    ?>
                </div>
                <?php
            } else {
            ?>
                <div class='attribute'>
                    <?php
                    /**
                     * Displays the label for the course name.
                     *
                     * @package block_panopto
                     * @copyright  Panopto 2009 - 2024
                     * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
                     */
                    echo get_string('course_name', 'block_panopto');
                    ?>
                </div>
                <div class='value'>
                    <?php
                    /**
                     * Displays the full name of the course.                     *
                     *
                     * @package block_panopto
                     * @copyright  Panopto 2009 - 2024
                     * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
                     *
                     * @var string $provisioningdata->fullname Course full name
                     */
                    echo $provisioningdata->fullname;
                    ?>
                </div>

                <div class='attribute'>
                    <?php
                    /**
                     * Displays the label for synced user information.
                     *
                     * @package block_panopto
                     * @copyright  Panopto 2009 - 2024
                     * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
                     */
                    echo get_string('synced_user_info', 'block_panopto');
                    ?>
                </div>
                <?php if (get_config('block_panopto', 'sync_after_login') || get_config('block_panopto', 'sync_on_enrolment')) { ?>
                    <div class='value'>
                        <?php
                        /**
                         * Displays a message about custom user sync settings.
                         *
                         * @package block_panopto
                         * @copyright  Panopto 2009 - 2024
                         * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
                         */
                        echo get_string('users_will_be_synced_custom', 'block_panopto');
                        ?>
                    </div>
                <?php } ?>
                <?php if (get_config('block_panopto', 'async_tasks')) { ?>
                    <div class='value'>
                        <?php
                        /**
                         * Displays a warning about asynchronous task delays.
                         *
                         * @package block_panopto
                         * @copyright  Panopto 2009 - 2024
                         * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
                         */
                        echo get_string('async_wait_warning', 'block_panopto');
                        ?>
                    </div>
                <?php } ?>
                <?php if (!get_config('block_panopto', 'sync_after_provisioning')) { ?>
                    <div class='value'>
                        <?php
                        /**
                         * Displays a message indicating that no users are synced.
                         *
                         * @package block_panopto
                         * @copyright  Panopto 2009 - 2024
                         * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
                         */
                        echo get_string('no_users_synced_desc', 'block_panopto');
                        ?>
                    </div>
                <?php } else { ?>
                    <div class='value'>
                        <?php
                        /**
                         * Displays a message indicating that users have been synced.
                         *
                         * @package block_panopto
                         * @copyright  Panopto 2009 - 2024
                         * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
                         */
                        echo get_string('users_have_been_synced', 'block_panopto');
                        ?>
                    </div>
                    <div class='attribute'>
                        <?php
                        /**
                         * Displays the label for publishers.
                         *
                         * @package block_panopto
                         * @copyright  Panopto 2009 - 2024
                         * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
                         */
                        echo get_string('publishers', 'block_panopto');
                        ?>
                    </div>
                    <div class='value'>
                    <?php
                    /**
                     * Displays a comma-separated list of publishers if available.
                     *
                     * @package block_panopto
                     * @copyright  Panopto 2009 - 2024
                     * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
                     */
                    if (!empty($provisioneddata->publishers)) {
                        echo join(', ', $provisioneddata->publishers);
                    } else {
                        ?><div class='errorMessage'><?php
                        /**
                         * Displays an error message if no publishers are found.
                         *
                         * @package block_panopto
                         * @copyright  Panopto 2009 - 2024
                         * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
                         */
                        echo get_string('no_publishers', 'block_panopto');
                        ?></div><?php
                    }
                    ?>
                    </div>
                    <div class='attribute'>
                        <?php
                        /**
                         * Displays the label for creators.
                         *
                         * @package block_panopto
                         * @copyright  Panopto 2009 - 2024
                         * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
                         */
                        echo get_string('creators', 'block_panopto');
                        ?>
                    </div>
                    <div class='value'>
                    <?php
                    /**
                     * Displays a comma-separated list of creators if available.
                     *
                     * @package block_panopto
                     * @copyright  Panopto 2009 - 2024
                     * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
                     */
                    if (!empty($provisioneddata->creators)) {
                        echo join(', ', $provisioneddata->creators);
                    } else {
                        ?><div class='errorMessage'><?php
                        /**
                         * Displays an error message if no creators are found.
                         *
                         * @package block_panopto
                         * @copyright  Panopto 2009 - 2024
                         * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
                         */
                        echo get_string('no_creators', 'block_panopto');
                        ?></div><?php
                    }
                    ?>
                    </div>
                    <div class='attribute'>
                        <?php
                        /**
                         * Displays the label for viewers.
                         *
                         * @package block_panopto
                         * @copyright  Panopto 2009 - 2024
                         * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
                         */
                        echo get_string('viewers', 'block_panopto');
                        ?>
                    </div>
                    <div class='value'>
                    <?php
                    /**
                     * Displays a comma-separated list of viewers if available.
                     *
                     * @package block_panopto
                     * @copyright  Panopto 2009 - 2024
                     * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
                     */
                    if (!empty($provisioneddata->viewers)) {
                        echo join(', ', $provisioneddata->viewers);
                    } else {
                        ?><div class='errorMessage'><?php
                        /**
                         * Displays an error message if no viewers are found.
                         *
                         * @package block_panopto
                         * @copyright  Panopto 2009 - 2024
                         * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
                         */
                        echo get_string('no_viewers', 'block_panopto');
                        ?></div><?php
                    }
                    ?>
                    </div>
                <?php } ?>
                <div class='attribute'>
                    <?php
                    /**
                     * Displays the label for the result of the provisioning process.
                     *
                     * @package block_panopto
                     * @copyright  Panopto 2009 - 2024
                     * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
                     */
                    echo get_string('result', 'block_panopto');
                    ?>
                </div>
                <div class="value">
                    <div class='successMessage'>
                        <?php
                        /**
                         * Displays a success message with the ID of the successful provisioning.
                         *
                         * @package block_panopto
                         * @copyright  Panopto 2009 - 2024
                         * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
                         *
                         * @var string $provisioneddata->Id Provisioning ID
                         */
                        echo get_string('provision_successful', 'block_panopto', $provisioneddata->Id);
                        ?>
                    </div>
                </div>
                <?php
            }
        } else {
            ?>
            <div class='errorMessage'>
                <?php
                /**
                 * Displays a generic error message if no provisioning data is available.
                 *
                 * @package block_panopto
                 * @copyright  Panopto 2009 - 2024
                 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
                 */
                echo get_string('provision_error', 'block_panopto');
                ?>
            </div>
            <?php
            /**
             * Closing else condition.
             *
             * @package block_panopto
             * @copyright  Panopto 2009 - 2024
             * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
             */
        }
        ?>
        </div>
    </div>
</div>
