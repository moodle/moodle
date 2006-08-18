<?php  // $Id$

// seems to work...
// maybe I should add some pretty icons?
// or possibly add the ability to custom-name things?

class block_admin_bookmarks extends block_base {

    function init() {
        $this->title = "Admin Bookmarks";
        $this->version = 2006081800;
    }

    function preferred_width() {
        return 210;
    }

    function create_item($visiblename,$link,$icon) {
        $this->tempcontent .= '<a href="' . $link . '"><img src="' . $icon . '" border="0" alt="[item]" /> ' . $visiblename . '</a><br />' . "\n";
    }

    function get_content() {

        global $CFG, $USER, $ADMIN;
		
		if (!$ADMIN) {
            require_once($CFG->dirroot . '/admin/adminlib.php');
        }
		
        if ($this->content !== NULL) {
            return $this->content;
        }

        $this->content = new stdClass;
		if ($USER->preference['admin_bookmarks']) {
            $bookmarks = explode(',',$USER->preference['admin_bookmarks']);
			// hmm... just a liiitle (potentially) processor-intensive
			// (recall that $ADMIN->locate is a huge recursive call... and we're calling it repeatedly here
            foreach($bookmarks as $bookmark) {
			    $temp = $ADMIN->locate($bookmark);
			    if ($temp instanceof admin_settingpage) {
                    $this->content->text .= '<a href="' . $CFG->wwwroot . '/admin/settings.php?section=' . $bookmark . '">' . $temp->visiblename . '</a>' . '<br />';
                } elseif ($temp instanceof admin_externalpage) {
                    $this->content->text .= '<a href="' . $temp->url . '">' . $temp->visiblename . '</a>' . '<br />';
                }                
    		}
		} else {
			$bookmarks = array();
		}
		
        if (($section = optional_param('section','',PARAM_ALPHAEXT)) && (in_array($section, $bookmarks))) {
            $this->content->footer = '<a href="' . $CFG->wwwroot . '/blocks/admin_bookmarks/delete.php?section=' . $section . '&returnurl=' . $CFG->wwwroot . '">unbookmark this page</a>';	
		} elseif ($section = optional_param('section','',PARAM_ALPHAEXT)) {
    	    $this->content->footer = '<a href="' . $CFG->wwwroot . '/blocks/admin_bookmarks/create.php?section=' . $section . '">bookmark this page</a>';		
		}
	
	    return $this->content;

    }
}

?>
