<?php

class block_admin_tree extends block_base {

    var $currentdepth;
    var $divcounter;
    var $tempcontent;
    var $destination;
    var $section = null;
    var $pathtosection = array();
    var $expandnodes = array();

    function init() {
        $this->title = get_string('pluginname', 'block_admin_tree');
        $this->version = 2007101509;
        $this->currentdepth = 0;
        $this->divcounter = 1;
        $this->tempcontent = '';
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
        global $OUTPUT;
        $strfolderopened = s(get_string('folderopened'));

        $this->tempcontent .= '<div class="depth'.$this->currentdepth.'"><a name="d'.$this->divcounter.'">';
        $this->tempcontent .= '<img id="vh_div'.$this->divcounter.'indicator" src="'.$OUTPUT->pix_url('i/open') . '" alt="'.$strfolderopened.'" /> ';
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
        global $CFG, $OUTPUT;
        if ($content instanceof admin_settingpage) {
            // show hidden pages in tree if hidden page active
            if ($content->check_access() and (($content->name == $this->section) or !$content->is_hidden())) {
                $class = ($content->name == $this->section) ? 'link current' : 'link';
                if ($content->is_hidden()) {
                    $class .= ' hidden';
                }
                $this->create_item($content->visiblename, $CFG->wwwroot.'/'.$CFG->admin.'/settings.php?section='.$content->name,$OUTPUT->pix_url('i/item'), $class);
            }
        } else if ($content instanceof admin_externalpage) {
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
                $this->create_item($content->visiblename, $content->url, $OUTPUT->pix_url('i/item'), $class);
            }
        } else if ($content instanceof admin_category) {
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
        global $CFG, $OUTPUT, $COURSE;

        if ($this->content !== NULL) {
            return $this->content;
        }

        if (isguestuser() or !isloggedin()) {
            // shortcut - these users can not change any settings
            $this->content = '';
            return '';
        }

        if ($COURSE->shortname === '') {
            // remove admin block if site not fully configured yet
            $this->content = '';
            return '';
        }

        require_once($CFG->libdir.'/adminlib.php');
        $adminroot = admin_get_root(false, false); // settings not required - only pages

        $this->section = $this->page->url->param('section');
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
            $this->page->requires->yui2_lib('event');
            $this->page->requires->js('/blocks/admin_tree/admintree.js');
            $this->page->requires->js_function_call('admin_tree.init',
                    array($this->divcounter - 1, $this->expandnodes,
                    $OUTPUT->pix_url('i/open'), $OUTPUT->pix_url('i/closed'),
                    get_string('folderopened'), get_string('folderclosed')));

            $this->content = new object();
            $this->content->text = '<div class="admintree">' . $this->tempcontent . "</div>\n";

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

