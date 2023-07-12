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
 * Provides edwiserbridge_local\external\course_progress_data trait.
 *
 * @package     edwiserbridge_local
 * @category    external
 * @copyright   2018 Wisdmlabs
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_edwiserbridge\external;
defined('MOODLE_INTERNAL') || die();

use external_function_parameters;
use external_multiple_structure;
use external_single_structure;
use external_value;
use core_completion\progress;

require_once($CFG->dirroot.'/local/edwiserbridge/classes/class-setup-wizard.php');

/**
 * Trait implementing the external function edwiserbridge_local_course_progress_data
 */
trait edwiserbridge_local_setup_wizard_save_and_continue {

    /**
     * Returns description of edwiserbridge_local_get_course_enrollment_method() parameters
     *
     * @return external_function_parameters
     */
    public static function edwiserbridge_local_setup_wizard_save_and_continue_parameters() {
        return new external_function_parameters(
            array(
                'data' => new external_value(PARAM_RAW, get_string('web_service_name', 'local_edwiserbridge')),
            )
        );
    }

    /**
     * Get list of active course enrolment methods for current user.
     *
     * @param int $courseid
     * @return array of course enrolment methods
     * @throws moodle_exception
     */
    public static function edwiserbridge_local_setup_wizard_save_and_continue( $data ) {
        global $DB,$CFG;

        $response = array(
            'html_data' => '',
            'title'     => ''
        );

        $data = json_decode( $data );

        $current_step = $data->current_step;
        $next_step = $data->next_step;
        $is_next_sub_step = $data->is_next_sub_step;

        $setup_wizard_handler = new \eb_setup_wizard();
        $steps = $setup_wizard_handler->eb_setup_wizard_get_steps();

        // Check if there are any sub steps available. 
        $function = $steps[$next_step]['function'];

        // Save progress data.
        set_config('eb_setup_progress', $current_step);

       switch ( $current_step ) {
           case 'web_service':
               // Create web service and update data in EB settings
                $settingshandler = new \eb_settings_handler();
                // Get main admin user

                $adminuser = get_admin();

                if ( isset( $data->service_name ) && isset( $data->existing_service ) && ! $data->existing_service ) {
                    $response = $settingshandler->eb_create_externle_service( $data->service_name , $adminuser->id );
                } elseif ( isset( $data->service_name ) && isset( $data->existing_service ) && $data->existing_service ) {
                    // Set Service. edwiser_bridge_last_created_token
                    set_config('ebexistingserviceselect', $data->service_name);

                    // select token update web services and set token.
                    // If token is not created dreate token.
                    $token = $settingshandler->eb_create_token( $data->service_name, $adminuser->id );

                    // Set last created token.
                    set_config('edwiser_bridge_last_created_token', $token);
                }

               break;

            case 'wordpress_site_details':
                if ( isset( $data->site_name ) && ! empty( $data->site_name ) && isset( $data->url ) && ! empty( $data->url ) ) {
                    // Get existing data
                    $sites = get_connection_settings();
                    $connectionsettings = $sites['eb_connection_settings'];


                    $token = isset($CFG->edwiser_bridge_last_created_token) ? $CFG->edwiser_bridge_last_created_token : ' - ';
                   // Update Moodle Wordpress site details.
                    $connectionsettings[$data->site_name] = array(
                        "wp_url"   => $data->url,
                        "wp_token" => $token,
                        "wp_name"  => $data->site_name
                    );

                    set_config( 'eb_connection_settings', serialize( $connectionsettings ) );
                    set_config( 'eb_setup_wp_site_name', $data->site_name );
                } elseif( isset( $data->site_name ) ){
                    set_config( 'eb_setup_wp_site_name', $data->site_name );
                }

               break;


            case 'user_and_course_sync':

                // Update Moodle Wordpress site details.
                $existingsynchsettings = isset($CFG->eb_synch_settings) ? unserialize($CFG->eb_synch_settings) : array();
                $synchsettings = $existingsynchsettings;
                $sitename =  $CFG->eb_setup_wp_site_name;

                $synchsettings[$sitename] = array(
                    "course_enrollment"    => $data->user_enrollment,
                    "course_un_enrollment" => $data->user_unenrollment,
                    "user_creation"        => $data->user_creation,
                    "user_deletion"        => $data->user_deletion,
                    "course_creation"      => $data->course_creation,
                    "course_deletion"      => $data->course_deletion,
                    "user_updation"        => $data->user_update,
                );

                set_config( 'eb_synch_settings', serialize( $synchsettings ) );

               break;





            case 'complete_details':

                set_config('eb_setup_progress', '');
                

               break;

           default:

               break;
       }


       


        // get next step.

        /*
        * There are multiple steps inside 1 step which are listed below.
        * 1. Web sevice
        *    a. web service
        *    b. WP site details
        *
        * 2. user and course sync
        *    a. User and course sync
        *    b. success screens
        */
        if ( 'complete_details' != $current_step ) {

            $next_step_html = $setup_wizard_handler->$function( 1 );
            $title          = $setup_wizard_handler->eb_get_step_title( $next_step );






            $response = array(
                'html_data' => $next_step_html,
                'title'     => $title
            );
        }

        return $response;

    }

    /**
     * Returns description of edwiserbridge_local_get_course_enrollment_method() result value
     *
     * @return external_description
     */
    public static function edwiserbridge_local_setup_wizard_save_and_continue_returns() {
        new external_single_structure(
            array(
                'html_data' => new external_value(PARAM_RAW, 'Setup wizards next step html content'),
                'title' => new external_value(PARAM_RAW, 'Setup wizards next step title'),
            )
        );
    }
}
