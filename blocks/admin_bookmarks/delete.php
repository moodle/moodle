<?php // $Id$

require('../../config.php');

require_once($CFG->dirroot . '/admin/adminlib.php');

if ($section = optional_param('section', '', PARAM_ALPHAEXT)) {

    if (isset($USER->preference['admin_bookmarks'])) {

        $bookmarks = explode(',', $USER->preference['admin_bookmarks']);

        $key = array_search($section, $bookmarks);

        if ($key === false) {
		    error('Bookmark doesn\'t exist.');
			die;
		}

		unset($bookmarks[$key]);
		$bookmarks = implode(',',$bookmarks);
        set_user_preference('admin_bookmarks', $bookmarks);
        
        $temp = $ADMIN->locate($section);
        
        if (is_a($temp, 'admin_externalpage')) {
            redirect($temp->url, 'Bookmark deleted.',1);
        } elseif (is_a($temp, 'admin_settingpage')) {
            redirect($CFG->wwwroot . '/admin/settings.php?section=' . $section, 'Bookmark deleted.',1);        
        } else {
            redirect($CFG->wwwroot, 'Bookmark deleted.',1);
        }
		die;


	}
	
    error('No bookmarks found for current user.');
	die;

} else {
    error('Valid section not specified.');
	die;
}

?>