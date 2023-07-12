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
 * Strings required for plugin.
 *
 * @package     local_edwiserbridge
 * @copyright   2021 WisdmLabs (https://wisdmlabs.com/) <support@wisdmlabs.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author      Wisdmlabs
 */

defined('MOODLE_INTERNAL') || die();

/**************  by default strings used by the moodle  ****************/

$string["local_edwiserbridgedescription"] = "";
$string['modulename'] = "Edwiser Bridge";
$string['modulenameplural'] = "Edwiser Bridge";
$string['pluginname'] = 'Edwiser Bridge';
$string['pluginadministration'] = "Edwiser Bridge administrator";
$string['modulename_help'] = '';
$string['blank'] = '';
/**************  end of the strings used by default by the moodle  ****************/

/*** TABS  ***/
$string['tab_service'] = 'Web Service';
$string['tab_conn'] = 'Wordpress Site';
$string['tab_synch'] = 'Synchronization';
$string['tab_mdl_required_settings'] = 'General';
$string['summary'] = 'Summary';
/*******/
$string['lang_label'] = 'Language Code';
/******* navigation menu and settings page   ********/

$string["wp_site_settings_title"] = "Site Settings :";
$string["nav_name"] = "Settings";
$string["default_settings_nav"] = "Settings";
$string["run_setup"] = "Run Setup Wizard";


$string["edwiserbridge"] = "Edwiser Bridge";
$string["eb-setting-page-title"] = "Edwiser Bridge Two Way Synchronization Settings";
$string["eb-setting-page-title_help"] = "Edwiser Bridge Two Way Synchronization Settings";

$string["eb-setup-page-title"] = "Edwiser Bridge Setup Wizard";
$string["eb-setup-page-title_help"] = "Edwiser Bridge Setup Wizard";

$string["enrollment_checkbox"] = "Enable User Enrollment.";
$string["enrollment_checkbox_desc"] = "Enroll user from Moodle to Wordpress for linked users.";
$string["unenrollment_checkbox"] = "Enable User Un-enrollment.";
$string["unenrollment_checkbox_desc"] = "Unenroll user from Moodle to Wordpress for linked users.";
$string["user_creation"] = "Enable User Creation";
$string["user_creation_desc"] = "Create user In linked Wordpress site when created in Moodle Site.";
$string["user_deletion"] = "Enable User Deletion";
$string["user_deletion_desc"] = "Delete user In linked Wordpress site when deleted in Moodle Site.";

$string["course_creation"] = "Enable Course Creation";
$string["course_creation_desc"] = "This will create course in Wordpress site.";
$string["course_deletion"] = "Enable Course Deletion";
$string["course_deletion_desc"] = "This won't delete course but it will mark course as deleted in linked Wordpress site.";
$string["user_updation"] = "Enable User Update";
$string["user_updation_desc"] = "This will update user first name, last name and password and won't update Username and Email.";

$string["wp_settings_section"] = "Wordpress Connection Settings";
$string["wordpress_url"] = "Wordpress URL";
$string["wp_token"] = "Moodle Access Token";
$string["wp_test_conn_btn"] = "Test Connection";
$string["wp_test_remove_site"] = "Remove Site";
$string["add_more_sites"] = "Add New Site";
$string["wordpress_site_name"] = "Site Name";
$string["site-list"] = "Wordpress Sites";

$string['next'] = 'Next';
$string['save'] = 'Save';
$string['save_cont'] = 'Save and Continue';

$string["token_help"] = "Please enter the access token generated while creating the web service.";
$string["wordpress_site_name_help"] = "Please enter unique site name.";
$string["wordpress_url_help"] = "Please enter Wordpress site URL.";

$string["token"] = "Access Token";

$string['existing_web_service_desc'] = 'Select existing web service if you have created already.';
$string['new_web_service_desc'] = 'Create new web service';
$string['new_web_new_service'] = 'Create new web service';

$string['new_service_inp_lbl'] = 'Name for the Web Service';
$string['new_serivce_user_lbl'] = 'Select User';
$string['existing_serice_lbl'] = 'Select web service';
$string['token_dropdown_lbl'] = 'Select Token';

$string['web_service_token'] = 'Token generated after creating service';
$string['moodle_url'] = 'Moodle site URL.';
$string['web_service_name'] = 'Web Service Name';
$string['web_service_auth_user'] = 'Authorized user.';

$string['existing_service_desc'] = 'Edwiser web-service functions will get added into it and also be used as reference for upcoming updates.';
$string['auth_user_desc'] = 'All admin users used as Authorized User while creating token.';

$string['eb_settings_msg'] = 'To complete Edwiser Bridge Set up ';
$string['click_here'] = ' Click Here ';
$string['eb_dummy_msg'] = 'Set up Wizard field';

$string['eb_mform_service_desc'] = 'Service desc';
$string['eb_mform_service_desc_help'] = 'Edwiser web-service functions will get added into it and also be used as reference for upcoming updates.';

$string['eb_mform_token_desc'] = 'Token';
$string['eb_mform_token_desc_help'] = 'This is your last created token used in wp for site integration.';
$string['eb_mform_ur_desc_help'] = 'Please copy this URL and paste it to your Wordpress site to complete the connection between Moodle and Wordpress.';
$string['eb_mform_ur_desc'] = 'Site URL';

$string['eb_mform_lang_desc_help'] = 'Please copy this language code and paste it to your Wordpress site to complete the connection between Moodle and Wordpress.';
$string['eb_mform_lang_desc'] = 'Site Language Code';

$string['site_url'] = 'Site URL';
/*********************************/

/*********** Settings page validation and Modal strings************/
$string['create_service_shortname_err'] = 'Unable to create the webservice please contact plugin owner.';
$string['create_service_name_err'] = 'This name is already in use please use different name.';
$string['create_service_creation_err'] = 'Unable to create the webservice please contact plugin owner.';
$string['empty_userid_err'] = 'Please select the user.';
$string['eb_link_success'] = 'Web service sucessfully linked.';
$string['eb_link_err'] = 'Unable to link the web service.';
$string['eb_service_select_err'] = 'Please select valid external web service.';
$string['eb_service_info_error'] = ' service functions missing in your currently selected service, Please Update service to add all missing webservice functions.';

$string['dailog_title'] = 'Token And Url';
$string['site_url'] = 'Site Url ';
$string['token'] = 'Token ';
$string['copy'] = 'Copy';
$string['copied'] = 'Copied !!!';
$string['create'] = 'Create Web Service';
$string['create_wp_site'] = '- Create -';
$string['link'] = 'Update Web Service';
$string['click_to_copy'] = 'Click to copy.';
$string['pop_up_info'] = 'Copy Moodle site URL and the token to your Wordpress site Edwiser Bridge connection settings.';

/**********************************/

/******  Form validation.  ******/
$string['required'] = "- You must supply a value here.";
$string['sitename-duplicate-value'] = " - Site Name already exists, Please provide a different value.";
$string['url-duplicate-value'] = " - Wordpress Url already exists, Please provide a different value.";
/************/

/*****  web service  *******/
$string["web_service_wp_url"] = "Wordpress site URL.";
$string["web_service_wp_token"] = "Web service token.";
$string["web_service_test_conn"] = "Web service test connection type.";

$string["web_service_test_conn_status"] = '1 if successful connection and 0 on failure.';
$string["web_service_test_conn_msg"] = 'Success or error message.';

$string["web_service_site_index"] = "Site index is the nth no. of site saved in Edwiser Bridge settings.";

$string["web_service_course_enrollment"] = "Checks if the course enrollment is performed for the saved site";
$string["web_service_course_un_enrollment"] = "Checks if the course un-enrollment is performed for the saved site";
$string["web_service_user_creation"] = "Checks if the user creation is performed for the saved site";
$string["web_service_user_deletion"] = "Checks if the user deletion is performed for the saved site";
$string["web_service_course_creation"] = "Checks if Edwiser Bridge 2 way sync course creation is enabled.";
$string["web_service_course_deletion"] = "Checks if Edwiser Bridge 2 way sync course deletion is enabled.";
$string["web_service_user_update"] = "Checks if Edwiser Bridge 2 way sync user update is enabled.";

$string["web_service_offset"] = "This is the offset for the select query.";
$string["web_service_limit"] = "This limits the number of users returned.";
$string["web_service_search_string"] = "This string will be searched in the select query.";
$string["web_service_total_users"] = "Total number of users present in Moodle.";

$string["web_service_id"] = "User Id.";
$string["web_service_username"] = "Username of the user.";
$string["web_service_firstname"] = "Firstname of the user.";
$string["web_service_lastname"] = "Lastname of the user.";
$string["web_service_email"] = "Email of the user.";
$string['eb_plugin_name'] = "Plugin Name";
$string['eb_plugin_version'] = "Plugin Version";
$string['web_service_rest_protocol'] = "Check if rest protocol is enabled.";
$string['web_service_web_service'] = "Check if web services setting is enabled.";
$string['web_service_extended_char'] = "Check if extended characters are allowed in username.";
$string['web_service_password_policy'] = "Check if password policy is enabled.";
$string['web_service_lang_code'] = "check what is the default lnguage code for the site.";
$string['web_service_student_role_id'] = "Default role id of student role.";

$string["web_service_courseid"] = "Course ID.";
$string["web_service_fullname"] = "Course Name.";
$string["web_service_categoryid"] = "Category Id of the course.";
$string["web_service_total_courses"] = "Total number of courses present in Moodle.";

/******/

/****  error handling  ***/
$string["default_error"] = "Please check the URL or wordpress site permalink: to know more about this error <a href='https://edwiser.helpscoutdocs.com/collection/85-edwiser-bridge-plugin'  target='_blank'> click here </a>";

$string['eb_empty_name_err'] = 'Please enter valid service name.';
$string['eb_empty_user_err'] = 'Please select user.';

/**/
$string['please_enable'] = 'Error : Please fix this issue in settings';

/*****************  Set up Moodle Settings  *****************/
$string["password_policy_cb"] = "Password Policy.";
$string["password_policy_cb_desc"] = "If enabled, user passwords will be checked against the password policy as specified in the settings below. Enabling the password policy will not affect existing users until they decide to, or are required to, change their password.";

$string["extended_char_username_cb"] = "Allow extended characters in usernames.";
$string["extended_char_username_cb_desc"] = 'Enable this setting to allow students to use any characters in their usernames (note this does not affect their actual names). The default is "false" which restricts usernames to be alphanumeric lowercase characters, underscore (_), hyphen (-), period (.) or at symbol (@).';

$string["web_service_cb"] = "Enable Web Services.";
$string["web_service_cb_desc"] = "Recomended:yes";

$string["web_rest_protocol_cb"] = "Enable REST Protocol.";
$string["web_rest_protocol_cb_desc"] = "Recomended:yes";
/**********************************/

/********  Summary page  ********/
$string['sum_rest_proctocol'] = 'Rest Protocol';
$string['sum_web_services'] = 'Web Service';
$string['sum_pass_policy'] = 'Password Policy';
$string['sum_extended_char'] = 'Allow Extended Characters In Username';
$string['sum_service_link'] = 'Service Linked';
$string['sum_token_link'] = 'Token Linked';
$string['web_service_status'] = 'Web Service Function';
$string['web_service_cap'] = 'Capability';

$string['sum_error_rest_proctocol'] = 'Error: Please enable Rest Protocol';
$string['sum_error_web_services'] = 'Error: Please enable Web Service';
$string['sum_error_pass_policy'] = 'Error: Please disable Password Policy';
$string['sum_error_extended_char'] = 'Error: Please enable Allow Extended Characters in username';
$string['sum_error_service_link'] = 'Error: Please update Service and Token';
$string['sum_error_token_link'] = 'Error: Please update Token ';

$string['here'] = ' here ';

$string['summary_setting_section'] = 'General Settings Summary';
$string['summary_connection_section'] = 'Connection Settings Summary';
$string['edwiser_bridge_plugin_summary'] = 'Edwiser Bridge Plugin Summary';

$string['enabled'] = 'Enabled';
$string['disabled'] = 'Disabled';

$string['mdl_url'] = 'Moodle URL';
$string['wp_test_connection_failed'] = 'Server rewrite rules are not enabled or Wordpress permalink is not postname. Also, check if you have any firewall or security plugin, If yes  Whitelist Moodle URL and IP. If this does not fix then connect with your Hosting providers.';
/**************/

/*****************************  ADDED FOR SETTINGS PAGE   *****************************/
$string["manual_notification"] = "MANUAL NOTIFICATION";

/*********  Form error Handling.    *******/
$string['service_name_empty'] = 'Please enter web service name';
$string['user_empty'] = 'Please select User';
$string['token_empty'] = 'Please select Token';

$string['web_service_creation_status'] = 'Web service creation status';
$string['web_service_creation_msg'] = 'Web service creation message';

/*
 * GDPR compatibility strings.
 */
$string['privacy:metadata:wp_site'] = 'In order to integrate with a WordPress site, user data needs to be exchanged with WordPress. Which will perform actions like user creation, user deletion, user metedata update, user enrollment synchronization and user un-enrollment synchronization on WordPress site.';
$string['privacy:metadata:wp_site:userid'] = 'The userid is sent from Moodle to perform any of the actions mentioned in above site description on WordPress site.';
$string['privacy:metadata:wp_site:email'] = 'Your email is sent to the WordPress site to perform any of the actions mentioned in above site description on WordPress site';
$string['privacy:metadata:wp_site:username'] = 'The username is sent from Moodle to perform any of the actions mentioned in above site description on WordPress site';
$string['privacy:metadata:wp_site:firstname'] = 'The firstname is sent from Moodle to perform any of the actions mentioned in above site description on WordPress site';
$string['privacy:metadata:wp_site:lastname'] = 'The lastname is sent from Moodle to perform any of the actions mentioned in above site description on WordPress site';
$string['privacy:metadata:wp_site:password'] = 'The password is sent from Moodle to perform any of the actions mentioned in above site description on WordPress site';

// Plugin stats.
$string['mdl_edwiser_bridge_lbl'] = 'Edwiser Bridge Moodle:';
$string['mdl_edwiser_bridge_bp_lbl'] = 'Edwiser Bridge Bulk Purchase Moodle:';
$string['mdl_edwiser_bridge_sso_lbl'] = 'Edwiser Bridge Single Sign On Moodle:';
$string['mdl_edwiser_bridge_txt_latest'] = 'Latest';
$string['mdl_edwiser_bridge_txt_download'] = 'Download';
$string['mdl_edwiser_bridge_txt_download_help'] = 'Click here to downaload the plugin file.';
$string['mdl_edwiser_bridge_txt_not_avbl'] = 'Not Available';
$string['mdl_edwiser_bridge_fetch_info'] = 'Check for update';
$string['eb_no_sites'] = "--- No Sites Available ---";



/* ----------
Setup wizard strings.
--------------*/


$string['setup_footer'] = "Copyright © 2022 Edwiser | Brought to you by WisdmLabs and Powered by Edwiser";
$string['setup_contact_us'] = "Contact Us";

$string['setup_installation_note1'] = 'To start the setup you need to have the following plugins installed on WordPress & Moodle.';
$string['setup_installation_note2'] = 'If you have already installed Edwiser Bridge FREE plugin on WordPress & Moodle, please click ';
$string['setup_wp_plugin'] = 'WordPress Plugin';
$string['setup_mdl_plugin'] = 'Moodle Plugin';
$string['setup_free'] = 'Free';
$string['setup_continue_btn'] = 'Continue the setup';
$string['continue_wp_wizard_btn'] = 'Continue Setup on WordPress';


$string['setup_installation_faq'] = 'What to do if I have not installed Wordpress Plugin ';

$string['setup_faq_download_plugin'] = 'Download the plugin now';
$string['setup_faq_steps'] = 'After download please follow the steps below;';



$string['setup_faq_step1'] = 'Login to your WordPress site with Adminstrative access';
$string['setup_faq_step2'] = 'Navigate to Admin dashboard > Plugins > Install plugins ';
$string['setup_faq_step3'] = 'Upload the Edwiser Bridge FREE WordPress plugin here';
$string['setup_faq_step4'] = 'We will assist you with the rest of the setup from there';


$string['no_1'] = '1';
$string['no_2'] = '2';
$string['no_3'] = '3';
$string['no_4'] = '4';



$string['setup_mdl_plugin_note1'] = 'These are mandatory Moodle settings for Edwiser Bridge to work flawlessly on your end.';
$string['setup_mdl_plugin_note2'] = 'Once you are ready, please click on.';
$string['setup_mdl_plugin_check1'] = 'Enabling Rest Protocol';
$string['setup_mdl_plugin_check2'] = 'Enabling Web Service';
$string['setup_mdl_plugin_check3'] = 'Disable Password Policy';
$string['setup_mdl_plugin_check4'] = 'Allow extended characters in usernames';
$string['setup_mdl_settings_success_msg'] = 'The mandatory Moodle settings has been enabled successfully!';
$string['setup_enble_settings'] = 'Enable the Settings';



$string['setup_web_service_note1'] = 'Web service configuration is required for connecting Moodle with your WordPress site. ';
$string['setup_web_service_h1'] = 'You can ‘create a new web service’ by selecting from the dropdown if you are configuring for the first time.';
$string['setup_web_service_h2'] = 'Select an existing web service from the dropdown if you have previously configured Edwiser Bridge plugin';




$string['setup_wp_site_note1'] = 'Select the WordPress site that needs to be connected to your Moodle site';
$string['setup_wp_site_note2'] = 'Add your WordPress website name and URL';
$string['setup_wp_site_dropdown'] = 'The WordPress Site';
$string['name'] = 'Name';
$string['url'] = 'URL';


$string['setup_permalink_note1'] = 'Check and confirm if the permalink sturcture is set to ';
$string['es_postname'] = '‘Postname’.';
$string['setup_permalink_click'] = 'Click on the ';
$string['setup_permalink_note2'] = ' link. It will open in a new tab and check the permalink structure.';

$string['setup_permalink_note3'] = 'Click on “Confirmed” once checked.';

$string['confirmed'] = 'Confirmed';
$string['back'] = 'Back';
$string['skip'] = 'Skip';


$string['setup_sync_note1'] = 'Set your Moodle to WordPress data synchronization preferences';
$string['select_all'] = 'Select all';
$string['recommended'] = '(Recommended)';
$string['user_enrollment'] = 'User enrollment';
$string['user_unenrollment'] = 'User unenrollment';
$string['user_creation'] = 'User Creation';
$string['user_deletion'] = 'User Deletion';
$string['user_update'] = 'User Update';
$string['course_creation'] = 'Course Creation';
$string['course_deletion'] = 'Course Deletion';


$string['what_next'] = 'What Next?';
$string['setup_completion_note1'] = 'You need to add Moodle credentials to Edwiser Bridge FREE WordPress setup.';
$string['setup_completion_note2'] = 'Copy and Save the following moodle credential to add details on Edwiser Bridge FREE WordPress plugin setup ';
$string['setup_completion_note3'] = 'Alternately, you can download the Edwiser Bridge Moodle ‘Credentials file’ from here. ';
$string['setup_completion_note4'] = 'Once you have saved the details please continue with the Edwiser Bridge FREE WordPress setup by clicking on “Continue the setup”. ';

$string['setup_completion_note5'] = 'Edwiser Bridge FREE plugin Setup is Completed.';



$string['wp_site_details_note'] = 'Click on ‘Test connection’ once you have added the details.';

$string['or'] = 'OR';
$string['select'] = 'Select';
$string['setup_test_conn_succ'] = 'Moodle to WordPress connection successful';
$string['setup_test_conn_error'] = 'Please check WordPress site configuration.';


/*  Tooltip  */
$string['enabling_rest_tip'] = 'Enables Moodle Site to communicate with the Wordpress site via API (Recommended)';
$string['enabling_service_tip'] = "Moodle's API service to enable connection from WordPress to Moodle (Recommended)";
$string['disable_passw_policy_tip'] = '- We Recommend disabling this setting.
- If enabled, it will check user passwords against the password policy as specified in the settings.
- ​Enabling the password policy will not affect existing users until they decide to, or are required to, change their password.';
$string['allow_exte_char_tip'] = 'Enabling this will allow users to use any characters in their usernames (note this does not affect their actual names). 
The default is "false" which restricts usernames to be alphanumeric lowercase characters, underscore (_), hyphen (-), period (.), or at symbol (@).';
$string['web_service_tip'] = 'Its an API service which enables proper communication between WordPress and Moodle sites.';
$string['name_web_service_tip'] = 'Setup a name for the Web Service for e.g. "EdwiserBridge"';
$string['wp_site_tip'] = 'Select the WordPress site you wish to integrate with the Moodle site.';

$string['wp_site_name_tip'] = 'Key in your WordPress sites name.';
$string['wp_site_url_tip'] = 'Ensure there is no blank space. And it should follow the URL with Hypertext Transfer Protocol "https://"';
$string['user_enrollment_tip'] = 'It will auto enroll the users from Moodle to Wordpress. Only applies to linked users.';
$string['user_unenrollment_tip'] = 'It will auto unenroll the users from Moodle to Wordpress. Only applies to linked users.';
$string['user_creation_tip'] = 'This will auto-create user in linked Wordpress site.';
$string['user_deletion_tip'] = 'Deletes the user in the linked Wordpress site when deleted in Moodle Site.';
$string['user_update_tip'] = "This will update user first name, last name and password and won't update Username and Email.";
$string['course_creation_tip'] = 'This will draft the course on the linked WordPress site and the course will have to publised.';
$string['course_deletion_tip'] = 'It will mark the course as deleted in the linked WordPress site.';
// $string['mdl_url_tip'] = 'Setup a name for the Web Service for e.g. "EdwiserBridge"';
// $string['msl_acc_token_tip'] = 'Setup a name for the Web Service for e.g. "EdwiserBridge"';



/*****  Pop up close button  *****/

$string['close_quest'] = 'Are you sure you want to close the Edwiser Bridge Moodle setup wizard?';
$string['yes'] = 'Yes';
$string['no'] = 'No';
$string['note'] = 'Note';
$string['close_note'] = 'You can run the setup wizard again by navigating to Moodle Adminstration > Plugins > Edwiser Bridge > Run Setup wizard.';

