<?php  // $Id$

define('BLOCK_TYPE_LIST',    1);
define('BLOCK_TYPE_TEXT',    2);
define('BLOCK_TYPE_NUKE',    3);

class MoodleBlock {
    var $str;
    var $title         = NULL;
    var $course        = NULL;
    var $content_type  = NULL;
    var $content       = NULL;
    var $edit_controls = NULL;
    var $version       = NULL;
    var $instance      = NULL;
    var $config        = NULL;

    function MoodleBlock() {
        $this->init();
    }

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
        // Intentionally doesn't check if a title is set. This is already done in _self_test()
        return $this->title;
    }
    function get_content_type() {
        // Intentionally doesn't check if a content_type is set. This is already done in _self_test()
        return $this->content_type;
    }
    function get_version() {
        // Intentionally doesn't check if a version is set. This is already done in _self_test()
        return $this->version;
    }
    function get_header() {
        // Intentionally doesn't check if a header is set. This is already done in _self_test()
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
                    if(empty($this->edit_controls)) {
                        // No content, no edit controls, so just shut up
                        break;
                    }
                    else {
                        // No content but editing, so show something at least
                        $this->print_shadow();
                    }
                }
                else {
                    if($this->hide_header() && empty($this->edit_controls)) {
                        // Header wants to hide, no edit controls to show, so no header it is
                        print_side_block(NULL, $this->content->text, NULL, NULL, $this->content->footer, $this->html_attributes());
                    }
                    else {
                        // The full treatment, please
                        print_side_block($title, $this->content->text, NULL, NULL, $this->content->footer, $this->html_attributes());
                    }
                }
            break;
            case BLOCK_TYPE_LIST:
                if(empty($this->content->items) && empty($this->content->footer)) {
                    if(empty($this->edit_controls)) {
                        // No content, no edit controls, so just shut up
                        break;
                    }
                    else {
                        // No content but editing, so show something at least
                        $this->print_shadow();
                    }
                }
                else {
                    if($this->hide_header() && empty($this->edit_controls)) {
                        // Header wants to hide, no edit controls to show, so no header it is
                        print_side_block(NULL, '', $this->content->items, $this->content->icons, $this->content->footer, $this->html_attributes());
                    }
                    else {
                        // The full treatment, please
                        print_side_block($title, '', $this->content->items, $this->content->icons, $this->content->footer, $this->html_attributes());
                    }
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

    function add_edit_controls($options) {
        global $CFG, $THEME, $USER;

        if (!isset($this->str)) {
            $this->str->delete    = get_string('delete');
            $this->str->moveup    = get_string('moveup');
            $this->str->movedown  = get_string('movedown');
            $this->str->moveright = get_string('moveright');
            $this->str->moveleft  = get_string('moveleft');
            $this->str->hide      = get_string('hide');
            $this->str->show      = get_string('show');
            $this->str->configure = get_string('configuration');
        }

        $path = $CFG->wwwroot.'/course';

        if (empty($THEME->custompix)) {
            $pixpath = $path.'/../pix';
        } else {
            $pixpath = $path.'/../theme/'.$CFG->theme.'/pix';
        }
 
        $sesskeystr = '&amp;sesskey='.$USER->sesskey;

        $movebuttons = '<div style="float: right;">';

        if($this->instance->visible) {
            $icon = '/t/hide.gif';
            $title = $this->str->hide;
        }
        else {
            $icon = '/t/show.gif';
            $title = $this->str->show;
        }

        $page = new stdClass;
        $page->id   = $this->instance->pageid;
        $page->type = $this->instance->pagetype;
        $script = page_source_script($page);
     
        $movebuttons .= '<a style="margin-right: 6px; margin-left: 2px;" title="'. $title .'" href="'.$script.'&amp;blockaction=toggle&amp;instanceid='. $this->instance->id . $sesskeystr .'">' .
                        '<img src="'. $pixpath.$icon .'" alt=\"\" /></a>';

        if($options & BLOCK_CONFIGURE) {
            $movebuttons .= '<a style="margin-right: 6px; margin-left: 2px;" title="'. $this->str->configure .'" href="'. $script .'&amp;blockaction=config&amp;instanceid='. $this->instance->id.$sesskeystr .'">' .
                            '<img src="'. $pixpath .'/t/edit.gif" alt=\"\" /></a>';
        }

        $movebuttons .= '<a style="margin-right: 2px; margin-left: 2px;" title="'. $this->str->delete .'" href="'. $script .'&amp;blockaction=delete&amp;instanceid='. $this->instance->id.$sesskeystr .'">' .
                        '<img src="'. $pixpath .'/t/delete.gif" alt=\"\" /></a> ';

        if ($options & BLOCK_MOVE_LEFT) {
            $movebuttons .= '<a style="margin-right: 2px; margin-left: 2px;" title="'. $this->str->moveleft .'" href="'. $script .'&amp;blockaction=moveleft&amp;instanceid='. $this->instance->id.$sesskeystr .'">' .
                            '<img src="'. $pixpath .'/t/left.gif" alt=\"\" /></a>';
        }
        if ($options & BLOCK_MOVE_UP) {
            $movebuttons .= '<a style="margin-right: 2px; margin-left: 2px;" title="'. $this->str->moveup .'" href="'. $script .'&amp;blockaction=moveup&amp;instanceid='. $this->instance->id.$sesskeystr .'">' .
                            '<img src="'. $pixpath .'/t/up.gif" alt=\"\" /></a>';
        }
        if ($options & BLOCK_MOVE_DOWN) {
            $movebuttons .= '<a style="margin-right: 2px; margin-left: 2px;" title="'. $this->str->movedown .'" href="'. $script .'&amp;blockaction=movedown&amp;instanceid='. $this->instance->id.$sesskeystr .'">' .
                            '<img src="'. $pixpath .'/t/down.gif" alt=\"\" /></a>';
        }
        if ($options & BLOCK_MOVE_RIGHT) {
            $movebuttons .= '<a style="margin-right: 2px; margin-left: 2px;" title="'. $this->str->moveright .'" href="'. $script .'&amp;blockaction=moveright&amp;instanceid='. $this->instance->id.$sesskeystr .'">' .
                            '<img src="'. $pixpath .'/t/right.gif" alt=\"\" /></a>';
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

        $formats = $this->applicable_formats();
        if(empty($formats) || array_sum($formats) === 0) {
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
        // Default behavior: print the config_global.html file
        // You don't need to override this if you 're satisfied with the above
        if(!$this->has_config()) {
            return false;
        }
        global $CFG, $USER, $THEME;
        print_simple_box_start('center', '', $THEME->cellheading);
        include($CFG->dirroot.'/blocks/'. $this->name() .'/config_global.html');
        print_simple_box_end();
        return true;
    }
    
    function handle_config($config) {
        // Default behavior: save all variables as $CFG properties
        // You don't need to override this if you 're satisfied with the above
        if(!$this->has_config()) {
            return false;
        }
        foreach ($config as $name => $value) {
            set_config($name, $value);
        }
        return true;
    }
    
    function applicable_formats() {
        // Default case: the block can be used in all course types
        return array('all' => true);
    }
    
    function preferred_width() {
        // Default case: the block wants to be 180 pixels wide
        return 180;
    }
    
    function hide_header() {
        //Default, false--> the header is shown
        return false;
    }
    
    function html_attributes() {
        // Default case: just an id for the block, with our name in it
        return array('id' => 'block_'. $this->name());
    }
    
    function load_instance($instance) {
        if(!empty($instance->configdata)) {
            $this->config = unserialize(base64_decode($instance->configdata));
        }
        unset($instance->configdata);
        $this->instance = $instance;
        $this->specialization();
    }

    /**
     * This function is called on your subclass right after an instance is loaded
     * Use this function to act on instance data just after it's loaded and before anything else is done
     * For instance: if your block will have different title's dependant on location (site, course, blog, etc)
     */
    function specialization() {
        // Just to make sure that this method exists.
        return;
    }

    function instance_allow_multiple() {
        // Are you going to allow multiple instances of each block?
        // If yes, then it is assumed that the block WILL USE per-instance configuration
        return false;
    }

    function instance_config_print() {
        // Default behavior: print the config_instance.html file
        // You don't need to override this if you're satisfied with the above
        if(!$this->instance_allow_multiple()) {
            return false;
        }
        global $CFG, $USER, $THEME;

        if(is_file($CFG->dirroot .'/blocks/'. $this->name() .'/config_instance.html')) {
            print_simple_box_start('center', '', $THEME->cellheading);
            include($CFG->dirroot .'/blocks/'. $this->name() .'/config_instance.html');
            print_simple_box_end();
        } else {
            notice(get_string('blockconfigbad'), str_replace('blockaction=', 'dummy=', qualified_me()));
        }
        
        return true;
    }
    
    function instance_config_save($data) {
        $data = stripslashes_recursive($data);
        $this->config = $data;
        return set_field('block_instance', 'configdata', base64_encode(serialize($data)), 'id', $this->instance->id);
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
        $dir = $CFG->dirroot .'/blocks/'. $this->name() .'/nuke/';
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