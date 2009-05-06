<?php // $Id$

require_once($CFG->libdir.'/pagelib.php');

define('PAGE_ADMIN', 'admin');

// Bounds for block widths
// more flexible for theme designers taken from theme config.php
$lmin = (empty($THEME->block_l_min_width)) ? 0 :   $THEME->block_l_min_width;
$lmax = (empty($THEME->block_l_max_width)) ? 210 : $THEME->block_l_max_width;
$rmin = (empty($THEME->block_r_min_width)) ? 0 :   $THEME->block_r_min_width;
$rmax = (empty($THEME->block_r_max_width)) ? 210 : $THEME->block_r_max_width;

define('BLOCK_L_MIN_WIDTH', $lmin);
define('BLOCK_L_MAX_WIDTH', $lmax);
define('BLOCK_R_MIN_WIDTH', $rmin);
define('BLOCK_R_MAX_WIDTH', $rmax);

page_map_class(PAGE_ADMIN, 'page_admin');

class page_admin extends page_base {
    var $extrabutton = '';

    /**
     * Use this to pass extra HTML that is added after the turn blocks editing on/off button.
     *
     * @param string $extrabutton HTML code.
     */
    function set_extra_button($extrabutton) {
        $this->extrabutton = $extrabutton;
    }

    function print_header($focus='') {
        global $USER, $CFG, $SITE;

        $adminroot = admin_get_root(false, false); //settings not required - only pages

        // fetch the path parameter
        $section = $this->url->param('section');
        $current = $adminroot->locate($section, true);
        $visiblepathtosection = array_reverse($current->visiblepath);

        // The search page currently doesn't handle block editing
        if ($this->user_allowed_editing()) {
            $options = $this->url->params();
            if ($this->user_is_editing()) {
                $caption = get_string('blockseditoff');
                $options['adminedit'] = 'off';
            } else {
                $caption = get_string('blocksediton');
                $options['adminedit'] = 'on';
            }
            $buttons = print_single_button($this->url->out(false), $options, $caption, 'get', '', true);
        }
        $buttons .= $this->extrabutton;

        $navlinks = array();
        foreach ($visiblepathtosection as $element) {
            $navlinks[] = array('name' => $element, 'link' => null, 'type' => 'misc');
        }
        $navigation = build_navigation($navlinks);

        print_header("$SITE->shortname: " . implode(": ",$visiblepathtosection), $SITE->fullname, $navigation, $focus, '', true, $buttons, '');
    }
}

?>
