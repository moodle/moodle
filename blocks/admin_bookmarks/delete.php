<?php // $Id$

require('../../config.php');

require_once($CFG->libdir.'/adminlib.php');
$adminroot = admin_get_root();

if ($section = optional_param('section', '', PARAM_ALPHAEXT)) {

    if (isset($USER->preference['admin_bookmarks'])) {

        $bookmarks = explode(',', $USER->preference['admin_bookmarks']);

        $key = array_search($section, $bookmarks);

        if ($key === false) {
                    error(get_string('nonexistentbookmark','admin'));
            die;
        }

        unset($bookmarks[$key]);
        $bookmarks = implode(',',$bookmarks);
        set_user_preference('admin_bookmarks', $bookmarks);
        
        $temp = $adminroot->locate($section);
        
        if (is_a($temp, 'admin_externalpage')) {
            redirect($temp->url, get_string('bookmarkdeleted','admin'),1);
        } elseif (is_a($temp, 'admin_settingpage')) {
            redirect($CFG->wwwroot . '/' . $CFG->admin . '/settings.php?section=' . $section, 'Bookmark deleted.',1);        
        } else {
            redirect($CFG->wwwroot, get_string('bookmarkdeleted', 'admin'),1);
        }
        die;


    }
    
        error(get_string('nobookmarksforuser','admin'));
    die;

} else {
    error(get_string('invalidsection', 'admin'));
    die;
}

?>