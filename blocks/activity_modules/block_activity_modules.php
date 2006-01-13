<?PHP //$Id$

class block_activity_modules extends block_list {
    function init() {
        $this->title = get_string('activities');
        $this->version = 2006011300;
    }

    function get_content() {
        global $USER, $CFG;

        get_all_mods($this->instance->pageid, $mods, $modnames, $modnamesplural, $modnamesused);

        if($this->content !== NULL) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->items = array();
        $this->content->icons = array();
        $this->content->footer = '';

        if ($modnamesused) {
            foreach ($modnamesused as $modname => $modfullname) {
                if ($modname != 'label') {
                    $this->content->items[] = '<a href="'.$CFG->wwwroot.'/mod/'.$modname.'/index.php?id='.$this->instance->pageid.'">'.$modnamesplural[$modname].'</a>';
                    $this->content->icons[] = '<img src="'.$CFG->modpixpath.'/'.$modname.'/icon.gif" height="16" width="16" alt="" />';
                }
            }
        }

        return $this->content;
    }

    function applicable_formats() {
        return array('all' => true, 'mod' => false, 'my' => false);
    }
}

?>
