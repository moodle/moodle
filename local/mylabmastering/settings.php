<?php

/**
 * Add page to menu.
 *
 * @package    local_mylabmastering
 * @copyright  
 * @license    
 */

defined('MOODLE_INTERNAL') || die;

if ($hassiteconfig) { // needs this condition or there is error on login page
	require_once($CFG->dirroot.'/local/mylabmastering/locallib.php');
	$settings = new admin_settingpage(
	        'local_mylabmastering',
	        get_string('mylabmastering_config_title', 'local_mylabmastering')
	    );
	    
	$strConfigPageText = get_string('mylabmastering_config_text', 'local_mylabmastering');
	
	$html = '<p>'.$strConfigPageText.'</p>';
	$html .= '</br>';
	$html .= '<a href="'.mylabmastering_getkeymasterlink().'" target="_blank">'.get_string('mylabmastering_keymaster_link', 'local_mylabmastering').'</a>';
	    
    $settings->add(new admin_setting_heading('mylabmastering_config_heading', get_string('mylabmastering_config_heading', 'local_mylabmastering'), format_text($html, FORMAT_HTML)));
    $settings->add(new admin_setting_configtext('mylabmastering_url', get_string('mylabmastering_url_label', 'local_mylabmastering'), get_string('mylabmastering_url_desc', 'local_mylabmastering'), '', PARAM_TEXT));
    $settings->add(new admin_setting_configtext('mylabmastering_key', get_string('mylabmastering_key_label', 'local_mylabmastering'), get_string('mylabmastering_key_desc', 'local_mylabmastering'), '', PARAM_TEXT));
    $settings->add(new admin_setting_configtext('mylabmastering_secret', get_string('mylabmastering_secret_label', 'local_mylabmastering'), get_string('mylabmastering_secret_desc', 'local_mylabmastering'), '', PARAM_TEXT));
    $settings->add(new admin_setting_configcheckbox('mylabmastering_use_icons', get_string('mylabmastering_use_icons_label', 'local_mylabmastering'),
    		get_string('mylabmastering_use_icons_desc', 'local_mylabmastering'), 1));
    		
    $settings->add(new admin_setting_configtext('mylabmastering_grade_sync_url', get_string('mylabmastering_grade_sync_url_label', 'local_mylabmastering'), get_string('mylabmastering_grade_sync_url_desc', 'local_mylabmastering'), '', PARAM_TEXT));    		
    
	$ADMIN->add('localplugins', $settings);
}
