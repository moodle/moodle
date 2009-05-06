<?php  //$Id$

require_once($CFG->libdir.'/pagelib.php');

class page_my_moodle extends page_base {

    function user_allowed_editing() {
        if ($PAGE->pagetype == PAGE_MY_MOODLE) {
            return true;
        } else if (has_capability('moodle/my:manageblocks', get_context_instance(CONTEXT_SYSTEM)) && defined('ADMIN_STICKYBLOCKS')) {
            return true;
        }
        return false;
    }

    function print_header($title) {

        global $USER, $CFG;

        $replacements = array(
                              '%fullname%' => get_string('mymoodle','my')
        );
        foreach($replacements as $search => $replace) {
            $title = str_replace($search, $replace, $title);
        }

        $site = get_site();

        $button = update_mymoodle_icon($USER->id);
        $nav = get_string('mymoodle','my');
        $header = $site->shortname.': '.$nav;
        $navlinks = array(array('name' => $nav, 'link' => '', 'type' => 'misc'));
        $navigation = build_navigation($navlinks);
        
        $loggedinas = user_login_string($site);

        if (empty($CFG->langmenu)) {
            $langmenu = '';
        } else {
            $currlang = current_language();
            $langs = get_list_of_languages();
            $langlabel = get_accesshide(get_string('language'));
            $langmenu = popup_form($CFG->wwwroot .'/my/index.php?lang=', $langs, 'chooselang', $currlang, '', '', '', true, 'self', $langlabel);
        }

        print_header($title, $header,$navigation,'','',true, $button, $loggedinas.$langmenu);

    }
}

define('PAGE_MY_MOODLE',   'my-index');
define('MY_MOODLE_FORMAT', 'my'); //doing this so we don't run into problems with applicable formats.

page_map_class(PAGE_MY_MOODLE, 'page_my_moodle');

?>
