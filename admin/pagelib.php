<?php // $Id$

// Places where there's a multilingual issue (i.e. a string is hardcoded when it should be
// fetched from current lang) are marked with  /*ML*/

// Two Big Issues
//  -What do I use as the pageid? Is 1 okay for index.php given that no other pages
//   in Moodle use this pagelib?
//  -How do I handle user_is_editing()? I'm sure what I have below isn't... well... "proper".

require_once($CFG->libdir.'/pagelib.php');

define('PAGE_ADMIN', 'admin-index');

page_map_class(PAGE_ADMIN, 'page_admin');

// $DEFINEDPAGES = array(PAGE_CHAT_VIEW);  -- is anything like this needed?

class page_admin extends page_base {

    var $section;
	var $pathtosection;
    var $visiblepathtosection;

    function init_full() { 
        global $CFG, $ADMIN;

        if($this->full_init_done) {
            return;
        }

        // fetch the path parameter
        $this->section = optional_param("section","",PARAM_PATH);

        $this->visiblepathtosection = array();
		
		// this part is (potentially) processor-intensive... there's gotta be a better way
		// of handling this
		if ($this->pathtosection = $ADMIN->path($this->section)) {
		    foreach($this->pathtosection as $element) {
			    if ($pointer = $ADMIN->locate($element)) {
				    array_push($this->visiblepathtosection, $pointer->visiblename);
				}
			}
		}

        // all done
        $this->full_init_done = true;
    }

    function blocks_get_default() {
        return 'admin_2';
    }

    // seems reasonable that the only people that can edit blocks on the admin pages
    // are the admins... but maybe we want a role for this?
    function user_allowed_editing() { 
        return isadmin();
    }

    // has to be fixed. i know there's a "proper" way to do this
    function user_is_editing() { 
        global $USER;
        return (($_GET["edit"] == 'on') && isadmin());
    }

    function url_get_path() {  // erm.... this has to be based on the current location, right?
        global $CFG;
        return $CFG->wwwroot .'/admin/settings.php';
    }

    function url_get_parameters() {  // only handles parameters relevant to the admin pagetype
        $this->init_full();
        return array('section' => $this->section);
    }

    function blocks_get_positions() { 
        return array(BLOCK_POS_LEFT);
    }

    function blocks_default_position() { 
        return BLOCK_POS_LEFT;
    }

    // does anything need to be done here?
    function init_quick($data) {
        parent::init_quick($data);
    }

    function print_header() {
        global $USER, $CFG, $SITE;

        $this->init_full();

        // should this rely on showblocksonmodpages in any way? after all, teachers aren't accessing this...
        if ($this->user_allowed_editing()) {
            $buttons = '<table><tr><td><form target="' . $CFG->framename . '" method="get" action="settings.php">'.
                       '<input type="hidden" name="edit" value="'.($this->user_is_editing()?'off':'on').'" />'.
                       '<input type="hidden" name="section" value="'.$this->section.'" />'.
                       '<input type="submit" value="'.get_string($this->user_is_editing()?'blockseditoff':'blocksediton').'" /></form></td>' . 
                       '</tr></table>';
        } else {
            $buttons = '&nbsp;';
        }
		
/*ML*/  print_header("$SITE->shortname: " . implode(": ",$this->visiblepathtosection), $SITE->fullname, implode(" -> ",$this->visiblepathtosection),'', '', true, $buttons, '');
    }

    function get_type() {
        return PAGE_ADMIN;
    }
}

?>
