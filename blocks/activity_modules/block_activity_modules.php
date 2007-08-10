<?php //$Id$

class block_activity_modules extends block_list {
    function init() {
        $this->title = get_string('activities');
        $this->version = 2006011300;
    }

    function get_content() {
        global $USER, $CFG;
        
        // TODO: FIX: HACK: (any other tags I should add? :P)
        // Hacker's improvised caching scheme: avoid fetching the mod
        // data from db if the course format has already fetched them
        if(!isset($GLOBALS['modnamesplural']) || !isset($GLOBALS['modnamesused'])) {
            require_once($CFG->dirroot.'/course/lib.php');
            if (!empty($this->instance)) {
                get_all_mods($this->instance->pageid, $mods, $modnames, $modnamesplural, $modnamesused);
            }
        }
        else {
            $modnamesplural = $GLOBALS['modnamesplural'];
            $modnamesused   = $GLOBALS['modnamesused'];
        }

        if($this->content !== NULL) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->items = array();
        $this->content->icons = array();
        $this->content->footer = '';

        if (isset($modnamesused) && $modnamesused) {
            foreach ($modnamesused as $modname => $modfullname) {
                if ($modname != 'label') {
                    $this->content->items[] = '<a href="'.$CFG->wwwroot.'/mod/'.$modname.'/index.php?id='.$this->instance->pageid.'">'.$modnamesplural[$modname].'</a>';
                    $this->content->icons[] = '<img src="'.$CFG->modpixpath.'/'.$modname.'/icon.gif" class="icon" alt="" />';
                }
            }
        }

        return $this->content;
    }

    function applicable_formats() {
        return array('all' => true, 'mod' => false, 'my' => false, 'admin' => false,
                     'tag' => false);
    }
}

?>
