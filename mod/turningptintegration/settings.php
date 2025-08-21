<?php
/**
 * @package   mod_turningptintegration
 * Setup admin settigns for mod_turningptintegration plugin
 * @copyright  2019
 */
defined('MOODLE_INTERNAL') || die;
if ($ADMIN->fulltree) {
    $settings->add(new admin_setting_configtext('tt_lms_client_id', get_string('ttclientid', 'turningptintegration'),
                       get_string('ttclientiddescription', 'turningptintegration'), '', PARAM_NOTAGS));

    $settings->add(new admin_setting_configtext('tt_lms_client_secret', get_string('ttclientsecret', 'turningptintegration'),
                           get_string('ttclientsecretdescription', 'turningptintegration'), '', PARAM_NOTAGS));
}
