<?php  // $Id$

define('BLOCK_TYPE_LIST',    1);
define('BLOCK_TYPE_TEXT',    2);
define('BLOCK_TYPE_NUKE',    3);

class MoodleBlock {
    var $str;
    var $title = NULL;
    var $course = NULL;
    var $content_type = NULL;
    var $content = NULL;
    var $edit_controls = NULL;
    var $version = NULL;

    function name() {
        // Returns the block name, as present in the class name,
        // the database, the block directory, etc etc.
        static $myname;
        if($myname === NULL) {
            $myname = strtolower(get_class($this));
            $myname = substr($myname, strpos($myname, '_') + 1);
        }
        return $myname;
    }

    function get_content() {
        // This should be implemented by the derived class.
        return NULL;
    }
    function get_title() {
        // Intentionally doesn't check if a title is set, for _test_self()
        return $this->title;
    }
    function get_content_type() {
        // Intentionally doesn't check if a content_type is set, for _test_self()
        return $this->content_type;
    }
    function get_version() {
        // Intentionally doesn't check if a version is set, for _test_self()
        return $this->version;
    }
    function get_header() {
        // Intentionally doesn't check if a header is set, for _test_self()
        return $this->header;
    }
    function refresh_content() {
        // Nothing special here, depends on content()
        $this->content = NULL;
        return $this->get_content();
    }
    function print_block() {
        // Wrap the title in a floating DIV, in case we have edit controls to display
        // These controls will always be wrapped on a right-floating DIV
        $title = '<div style="float: left;">'.$this->title.'</div>';
        if($this->edit_controls !== NULL) {
            $title .= $this->edit_controls;
        }

        $this->get_content();
        if(!isset($this->content->footer)) {
            $this->content->footer = '';
        }

        switch($this->content_type) {
            case BLOCK_TYPE_NUKE:
            case BLOCK_TYPE_TEXT:
                if(empty($this->content->text) && empty($this->content->footer)) {
                    break;
                }
                if ($this->edit_controls !== NULL || !$this->hide_header()) {
                    print_side_block($title, $this->content->text, NULL, NULL, $this->content->footer);
                } else {
                    print_side_block(NULL, $this->content->text, NULL, NULL, $this->content->footer);
                }
            break;
            case BLOCK_TYPE_LIST:
                if(empty($this->content->items) && empty($this->content->footer)) {
                    break;
                }
                if ($this->edit_controls !== NULL || !$this->hide_header()) {
                    print_side_block($title, '', $this->content->items, $this->content->icons, $this->content->footer);
                } else {
                    print_side_block(NULL, '', $this->content->items, $this->content->icons, $this->content->footer);
                }
            break;
        }
    }
    function print_shadow() {
        $title = '<div style="float: left;">'.$this->title.'</div>';
        if($this->edit_controls !== NULL) {
            $title .= $this->edit_controls;
        }
        print_side_block($title, '&nbsp;', NULL, NULL, '');
    }
    function add_edit_controls($options, $blockid) {
        global $CFG, $THEME;

        // The block may be disabled
        $blockid = intval($blockid);
        $enabled = $blockid > 0;
        $blockid = abs($blockid);

        if (!isset($this->str)) {
            $this->str->delete    = get_string('delete');
            $this->str->moveup    = get_string('moveup');
            $this->str->movedown  = get_string('movedown');
            $this->str->moveright = get_string('moveright');
            $this->str->moveleft  = get_string('moveleft');
            $this->str->hide      = get_string('hide');
            $this->str->show      = get_string('show');
        }

        $path = $CFG->wwwroot.'/course';

        if (empty($THEME->custompix)) {
            $pixpath = $path.'/../pix';
        } else {
            $pixpath = $path.'/../theme/'.$CFG->theme.'/pix';
        }

        $movebuttons = '<div style="float: right;">';

        if($enabled) {
            $icon = '/t/hide.gif';
            $title = $this->str->hide;
        }
        else {
            $icon = '/t/show.gif';
            $title = $this->str->show;
        }

        $movebuttons .= '<a style="margin-right: 10px;" title="'.$title.'" href="'.$path.'/view.php?id='.$this->course->id.'&amp;blockaction=toggle&amp;blockid='.$blockid.'">' .
                        '<img src="'.$pixpath.$icon.'" /></a>';

        $movebuttons .= '<a style="margin-right: 10px;" title="'.$this->str->delete.'" href="'.$path.'/view.php?id='.$this->course->id.'&amp;blockaction=delete&amp;blockid='.$blockid.'">' .
                        '<img src="'.$pixpath.'/t/delete.gif" /></a>';

        if ($options & BLOCK_MOVE_LEFT) {
            $movebuttons .= '<a style="margin-right: 3px;" title="'.$this->str->moveleft.'" href="'.$path.'/view.php?id='.$this->course->id.'&amp;blockaction=moveside&amp;blockid='.$blockid.'">' .
                            '<img src="'.$pixpath.'/t/left.gif" /></a>';
        }
        if ($options & BLOCK_MOVE_UP) {
            $movebuttons .= '<a style="margin-right: 3px;" title="'.$this->str->moveup.'" href="'.$path.'/view.php?id='.$this->course->id.'&amp;blockaction=moveup&amp;blockid='.$blockid.'">' .
                            '<img src="'.$pixpath.'/t/up.gif" /></a>';
        }
        if ($options & BLOCK_MOVE_DOWN) {
            $movebuttons .= '<a style="margin-right: 3px;" title="'.$this->str->movedown.'" href="'.$path.'/view.php?id='.$this->course->id.'&amp;blockaction=movedown&amp;blockid='.$blockid.'">' .
                            '<img src="'.$pixpath.'/t/down.gif" /></a>';
        }
        if ($options & BLOCK_MOVE_RIGHT) {
            $movebuttons .= '<a style="margin-right: 3px;" title="'.$this->str->moveright.'" href="'.$path.'/view.php?id='.$this->course->id.'&amp;blockaction=moveside&amp;blockid='.$blockid.'">' .
                            '<img src="'.$pixpath.'/t/right.gif" /></a>';
        }

        $movebuttons .= '</div>';
        $this->edit_controls = $movebuttons;
    }

    function _self_test() {
        // Tests if this block has been implemented correctly.
        // Also, $errors isn't used right now
        $errors = array();

        $correct = true;
        if($this->get_title() === NULL) {
            $errors[] = 'title_not_set';
            $correct = false;
        }
        if(!in_array($this->get_content_type(), array(BLOCK_TYPE_LIST, BLOCK_TYPE_TEXT, BLOCK_TYPE_NUKE))) {
            $errors[] = 'invalid_content_type';
            $correct = false;
        }
        if($this->get_content() === NULL) {
            $errors[] = 'content_not_set';
            $correct = false;
        }
        if($this->get_version() === NULL) {
            $errors[] = 'version_not_set';
            $correct = false;
        }
        $allformats = COURSE_FORMAT_WEEKS | COURSE_FORMAT_TOPICS | COURSE_FORMAT_SOCIAL;
        if(!($this->applicable_formats() & $allformats)) {
            $errors[] = 'no_course_formats';
            $correct = false;
        }
        $width = $this->preferred_width();
        if(!is_int($width) || $width <= 0) {
            $errors[] = 'invalid_width';
            $correct = false;
        }
        return $correct;
    }

    function has_config() {
        return false;
    }
    function print_config() {
        // This does nothing, it's here to prevent errors from
        // derived classes if they implement has_config() but not print_config()
    }
    function handle_config() {
        // This does nothing, it's here to prevent errors from
        // derived classes if they implement has_config() but not handle_config()
    }
    function applicable_formats() {
        // Default case: the block can be used in all course types
        return COURSE_FORMAT_WEEKS | COURSE_FORMAT_TOPICS | COURSE_FORMAT_SOCIAL;
    }
    function preferred_width() {
        // Default case: the block wants to be 180 pixels wide
        return 180;
    }
    function hide_header() {
        //Default, false--> the header is showed
        return false;
    }
    function html_attributes() {
        // Default case: we want no extra attributes
        return false;
    }
}

class MoodleBlock_Nuke extends MoodleBlock {
    function get_content() {

        if($this->content !== NULL) {
            return $this->content;
        }

        global $CFG;
        $this->content = &New stdClass;

        // This whole thing begs to be written for PHP >= 4.3.0 using glob();
        $dir = $CFG->dirroot.'/blocks/'.$this->name().'/nuke/';
        if($dh = @opendir($dir)) {
            while (($file = readdir($dh)) !== false) {
                $regs = array();
                if(ereg('^block\-(.*)\.php$', $file, $regs)) {
                    // Found it! Let's prepare the environment...

                    $oldvals = array();
                    if(isset($GLOBALS['admin'])) {
                        $oldvals['admin'] = $GLOBALS['admin'];
                    }

                    $GLOBALS['admin'] = isteacher($this->course->id);
                    @include($dir.$file);

                    foreach($oldvals as $key => $val) {
                        $GLOBALS[$key] = $val;
                    }

                    // We should have $content set now
                    if(!isset($content)) {
                        return NULL;
                    }
                    return $this->content->text = $content;
                }
            }
        }

        // If we reached here, we couldn't find the nuke block for some reason
        return $this->content->text = get_string('blockmissingnuke');
    }
}

?>
