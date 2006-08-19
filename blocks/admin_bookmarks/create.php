<?php // $Id$

require('../../config.php');

require_once($CFG->dirroot . '/admin/adminlib.php');

if ($section = optional_param('section', '', PARAM_ALPHAEXT)) {

    if ($USER->preference['admin_bookmarks']) {
        $bookmarks = explode(',',$USER->preference['admin_bookmarks']);
		
        if (in_array($section, $bookmarks)) {
    	    error('Bookmark already exists.');
    		die;
    	}
		
	} else {
	    $bookmarks = array();
	}

    $temp = $ADMIN->locate($section);
    
    if (is_a($temp, 'admin_settingpage') || is_a($temp, 'admin_externalpage')) {
	
        $bookmarks[] = $section;
	
    	$bookmarks = implode(',',$bookmarks);
	
    	set_user_preference('admin_bookmarks', $bookmarks);
    
    } else {
    
        error('Invalid section.');
        die;
        
    }
	
	if (is_a($temp, 'admin_settingpage')) {
	
        redirect("$CFG->wwwroot/admin/settings.php?section=" . $section, 'Bookmark added.',1);	
    
    } elseif (is_a($temp, 'admin_externalpage')) {
    
        redirect($temp->url, 'Bookmark added.', 1);
        
    }

} else {
    error('Valid section not specified.');
	die;
}


?>