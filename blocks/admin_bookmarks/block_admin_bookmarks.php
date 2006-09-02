<?php  // $Id$

// seems to work...
// maybe I should add some pretty icons?
// or possibly add the ability to custom-name things?

class block_admin_bookmarks extends block_base {

    function init() {
        $this->title = get_string('adminbookmarks');
        $this->version = 2006090300;
    }

    function applicable_formats() {
        return array('site' => true, 'admin' => true);
    }

    function preferred_width() {
        return 210;
    }

    function create_item($visiblename,$link,$icon) {
        $this->tempcontent .= '<a href="' . $link . '"><img src="' . $icon . '" border="0" alt="[item]" /> ' . $visiblename . '</a><br />' . "\n";
    }

    function get_content() {

        global $CFG, $USER, $PAGE;
		
        require_once($CFG->libdir.'/adminlib.php');
        $adminroot = admin_get_root();
		
        if ($this->content !== NULL) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->text = '';
		if (isset($USER->preference['admin_bookmarks'])) {
            $bookmarks = explode(',',$USER->preference['admin_bookmarks']);
			// hmm... just a liiitle (potentially) processor-intensive
			// (recall that $adminroot->locate is a huge recursive call... and we're calling it repeatedly here
            foreach($bookmarks as $bookmark) {
			    $temp = $adminroot->locate($bookmark);
			    if (is_a($temp, 'admin_settingpage')) {
                    $this->content->text .= '<a href="' . $CFG->wwwroot . '/' . $CFG->admin . '/settings.php?section=' . $bookmark . '">' . $temp->visiblename . '</a>' . '<br />';
                } elseif (is_a($temp, 'admin_externalpage')) {
                    $this->content->text .= '<a href="' . $temp->url . '">' . $temp->visiblename . '</a>' . '<br />';
                }                
    		}
		} else {
			$bookmarks = array();
		}
		
        if (($section = (isset($PAGE->section) ? $PAGE->section : '')) && (in_array($section, $bookmarks))) {
            $this->content->footer = '<a href="' . $CFG->wwwroot . '/blocks/admin_bookmarks/delete.php?section=' . $section . '&returnurl=' . $CFG->wwwroot . '">unbookmark this page</a>';	
		} elseif ($section = (isset($PAGE->section) ? $PAGE->section : '')) {
    	    $this->content->footer = '<a href="' . $CFG->wwwroot . '/blocks/admin_bookmarks/create.php?section=' . $section . '">bookmark this page</a>';		
		} else {
		    $this->content->footer = '';
		}
	
	    return $this->content;

    }
}

?>