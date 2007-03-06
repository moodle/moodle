<?php // $Id$

require('../../config.php');

require_once($CFG->libdir.'/adminlib.php');
$adminroot = admin_get_root();
require_login();

if ($section = optional_param('section', '', PARAM_ALPHAEXT) and confirm_sesskey()) {

    if (get_user_preferences('admin_bookmarks')) {
        $bookmarks = explode(',',get_user_preferences('admin_bookmarks'));

        if (in_array($section, $bookmarks)) {
            error(get_string('bookmarkalreadyexists','admin'));
            die;
        }

    } else {
        $bookmarks = array();
    }

    $temp = $adminroot->locate($section);

    if (is_a($temp, 'admin_settingpage') || is_a($temp, 'admin_externalpage')) {

        $bookmarks[] = $section;

        $bookmarks = implode(',',$bookmarks);

        set_user_preference('admin_bookmarks', $bookmarks);

    } else {

        error(get_string('invaludsection','admin'));
        die;

    }

    if (is_a($temp, 'admin_settingpage')) {

        redirect($CFG->wwwroot . '/' . $CFG->admin . '/settings.php?section=' . $section);

    } elseif (is_a($temp, 'admin_externalpage')) {

        redirect($temp->url);

    }

} else {
    error(get_string('invalidsection','admin'));
    die;
}


?>