<?php  // $Id$

class block_admin_tree extends block_base {

    var $currentdepth;
    var $divcounter;
    var $tempcontent;
    var $pathtosection;
    var $expandjavascript;
    var $destination;

    function init() {
        global $PAGE;
        $this->title = get_string('administrationsite');
        $this->version = 2007101509;
        $this->currentdepth = 0;
        $this->divcounter = 1;
        $this->tempcontent = '';
        $this->section = (isset($PAGE->section) ? $PAGE->section : '');
        $this->pathtosection = array();
        $this->expandnodes = array();
    }

    function applicable_formats() {
        if (has_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM))) {
            return array('site' => true, 'admin' => true, 'my' => true);
        } else {
            return array('site' => true, 'admin' => true);
        }
    }

    function preferred_width() {
        return 210;
    }

    function open_folder($visiblename) {
        global $CFG;
        $strfolderopened = s(get_string('folderopened'));

        $this->tempcontent .= '<div class="depth'.$this->currentdepth.'"><a name="d'.$this->divcounter.'">';
        $this->tempcontent .= '<img id="vh_div'.$this->divcounter.'indicator" src="'.$CFG->pixpath.'/i/open.gif" alt="'.$strfolderopened.'" /> ';
        $this->tempcontent .= $visiblename.'</a></div><div id="vh_div'.$this->divcounter.'">'."\n";
        $this->currentdepth++;
        $this->divcounter++;
    }

    function close_folder() {
        $this->currentdepth--;
        $this->tempcontent .= "</div>\n";
    }

    function create_item($visiblename,$link,$icon,$class) {
        global $CFG;
        $this->tempcontent .= '<div class="depth'.$this->currentdepth.'"><a class="'.$class.'" href="'.$link.'"><img src="'.$icon.'" alt="" />'.
                $visiblename.'</a></div>'."\n";
    }

    function build_tree (&$content) {
        global $CFG;
        if (is_a($content, 'admin_settingpage')) {
            // show hidden pages in tree if hidden page active
            if ($content->check_access() and (($content->name == $this->section) or !$content->is_hidden())) {
                $class = ($content->name == $this->section) ? 'link current' : 'link';
                if ($content->is_hidden()) {
                    $class .= ' hidden';
                }
                $this->create_item($content->visiblename, $CFG->wwwroot.'/'.$CFG->admin.'/settings.php?section='.$content->name,$CFG->pixpath.'/i/item.gif', $class);
            }
        } else if (is_a($content, 'admin_externalpage')) {
            // show hidden pages in tree if hidden page active
            if ($content->check_access() and (($content->name == $this->section) or !$content->is_hidden())) {
                $class = ($content->name == $this->section) ? 'link current' : 'link';
                if ($content->name === 'adminnotifications') {
                    if (admin_critical_warnings_present()) {
                        $class .= ' criticalnotification';
                    }
                } 
                if ($content->is_hidden()) {
                    $class .= ' hidden';
                }
                $this->create_item($content->visiblename, $content->url, $CFG->pixpath.'/i/item.gif', $class);
            }
        } else if (is_a($content, 'admin_category')) {
            if ($content->check_access() and !$content->is_hidden()) {

                // check if the category we're currently printing is a parent category for the current page; if it is, we
                // make a note (in the javascript) that it has to be expanded after the page has loaded
                if ($this->section != '' and $this->pathtosection[count($this->pathtosection) - 1] == $content->name) {
                    $this->expandnodes[] = $this->divcounter;
                    array_pop($this->pathtosection);
                }

                $this->open_folder($content->visiblename);

                $entries = array_keys($content->children);

                foreach ($entries as $entry) {
                    $this->build_tree($content->children[$entry]);
                }

                $this->close_folder();
            }
        }
    }

    function get_content() {
        global $CFG;

        if ($this->content !== NULL) {
            return $this->content;
        }

        if (isguestuser() or !isloggedin()) {
            // these users can not change any settings
            $this->content = '';
            return '';
        }

        require_once($CFG->libdir.'/adminlib.php');
        $adminroot =& admin_get_root(false, false); // settings not required - only pages

        if ($current = $adminroot->locate($this->section, true)) {
            $this->pathtosection = $current->path;
            array_pop($this->pathtosection);
        }

        // we need to do this instead of $this->build_tree($adminroot) because the top-level folder
        // is redundant (and ideally ignored). (the top-level folder is "administration".)
        $entries = array_keys($adminroot->children);
        asort($entries);
        foreach ($entries as $entry) {
            $this->build_tree($adminroot->children[$entry]);
        }

        if ($this->tempcontent !== '') {
            require_js(array('yui_yahoo','yui_event'));
            require_js($CFG->wwwroot . '/blocks/admin_tree/admintree.js');
            $this->content = new object();
            $this->content->text = '<div class="admintree">' . $this->tempcontent . "</div>\n";
            $this->content->text .= print_js_call('admin_tree.init',
                    array($this->divcounter - 1, $this->expandnodes, $CFG->pixpath,
                    get_string('folderopened'), get_string('folderclosed')), true);

            // only do search if you have moodle/site:config
            if (has_capability('moodle/site:config',get_context_instance(CONTEXT_SYSTEM)) ) {
                $this->content->footer =
                        '<div class="adminsearchform">'.
                        '<form action="'.$CFG->wwwroot.'/'.$CFG->admin.'/search.php" method="get"><div>'.
                        '<label for="query" class="accesshide">'.get_string('searchinsettings', 'admin').'</label>'.
                        '<input type="text" name="query" id="query" size="8" value="'.s($adminroot->search).'" />'.
                        '<input type="submit" value="'.get_string('search').'" /></div>'.
                        '</form></div>';
            } else {
                $this->content->footer = '';
            }
        } else {
            $this->content = new object();
            $this->content->text = '';
        }

        return $this->content;
    }
}
?>
