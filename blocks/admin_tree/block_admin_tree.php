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
        $this->version = 2006090300;
        $this->currentdepth = 0;
        $this->divcounter = 1;
        $this->tempcontent = '';
        $this->section = (isset($PAGE->section) ? $PAGE->section : '');
        $this->pathtosection = array();
        $this->expandjavascript = '';
    }

    function applicable_formats() {
        //TODO: add 'my' only if user has role assigned in system or any course category context
        return array('site' => true, 'admin' => true, 'my' => true);
    }

    function preferred_width() {
        return 210;
    }

    function open_folder($visiblename) {
        global $CFG;
        $strfolderopened = s(get_string('folderopened'));

        $this->tempcontent .= '<div class="depth'.$this->currentdepth.'"><a href="#" onclick="toggle(\'vh_div'.$this->divcounter.'\');return false">';
        $this->tempcontent .= '<span id="vh_div'.$this->divcounter.'indicator"><img src="'.$CFG->wwwroot.'/blocks/admin_tree/open.gif" alt="'.$strfolderopened.'" /></span> ';
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
                $this->create_item($content->visiblename, $CFG->wwwroot.'/'.$CFG->admin.'/settings.php?section='.$content->name, $CFG->wwwroot.'/blocks/admin_tree/item.gif', $class);
            }
        } else if (is_a($content, 'admin_externalpage')) {
            // show hidden pages in tree if hidden page active
            if ($content->check_access() and (($content->name == $this->section) or !$content->is_hidden())) {
                $class = ($content->name == $this->section) ? 'link current' : 'link';
                if ($content->is_hidden()) {
                    $class .= ' hidden';
                }
                $this->create_item($content->visiblename, $content->url, $CFG->wwwroot.'/blocks/admin_tree/item.gif', $class);
            }
        } else if (is_a($content, 'admin_category')) {
            if ($content->check_access() and !$content->is_hidden()) {

                // check if the category we're currently printing is a parent category for the current page; if it is, we
                // make a note (in the javascript) that it has to be expanded after the page has loaded
                if ($this->section != '' and $this->pathtosection[count($this->pathtosection) - 1] == $content->name) {
                    $this->expandjavascript .= 'expand("vh_div'.($this->divcounter).'");'."\n";
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
            $strfolderopened = s(get_string('folderopened'));
            $strfolderclosed = s(get_string('folderclosed'));

            $this->content = new object();
            $this->content->text  = '<script type="text/javascript">'."\n";
            $this->content->text .= '//<![CDATA[' . "\n";
            $this->content->text .= 'var vh_numdivs = ' . ($this->divcounter - 1) . ';' . "\n";
            $this->content->text .= 'var vh_content = new Array();' . "\n";
            $this->content->text .= 'function getdiv(divid) {' . "\n";
            $this->content->text .= '  if (document.getElementById) {' . "\n";
            $this->content->text .= '    return document.getElementById(divid);' . "\n";
            $this->content->text .= '  } else if (window[divid]) {' . "\n";
            $this->content->text .= '    return window[divid];' . "\n";
            $this->content->text .= '  }' . "\n";
            $this->content->text .= '  return null;' . "\n";
            $this->content->text .= '}' . "\n";

            $this->content->text .= 'function toggle(divid) {' . "\n";
            $this->content->text .= '  if (getdiv(divid).innerHTML == "") {' . "\n";
            $this->content->text .= '    getdiv(divid).innerHTML = vh_content[divid];' . "\n";
            $this->content->text .= '    getdiv(divid + "indicator").innerHTML = \'<img src="' . $CFG->wwwroot . '/blocks/admin_tree/open.gif" alt="'.$strfolderopened.'" />\';' . "\n";
            $this->content->text .= '  } else {' . "\n";
            $this->content->text .= '    vh_content[divid] = getdiv(divid).innerHTML;' . "\n";
            $this->content->text .= '    getdiv(divid).innerHTML = "";' . "\n";
            $this->content->text .= '    getdiv(divid + "indicator").innerHTML = \'<img src="' . $CFG->wwwroot . '/blocks/admin_tree/closed.gif" alt="'.$strfolderclosed.'" />\';' . "\n";
            $this->content->text .= '  }' . "\n";
            $this->content->text .= '}' . "\n";

            $this->content->text .= 'function collapse(divid) {' . "\n";
            $this->content->text .= '  if (getdiv(divid).innerHTML !== "") {' . "\n";
            $this->content->text .= '    vh_content[divid] = getdiv(divid).innerHTML;' . "\n";
            $this->content->text .= '    getdiv(divid).innerHTML = "";' . "\n";
            $this->content->text .= '    getdiv(divid + "indicator").innerHTML = \'<img src="' . $CFG->wwwroot . '/blocks/admin_tree/closed.gif" alt="'.$strfolderclosed.'" />\';' . "\n";
            $this->content->text .= '  }' . "\n";
            $this->content->text .= '}' . "\n";

            $this->content->text .= 'function expand(divid) {' . "\n";
            $this->content->text .= '  getdiv(divid).innerHTML = vh_content[divid];' . "\n";
            $this->content->text .= '  getdiv(divid + "indicator").innerHTML = \'<img src="' . $CFG->wwwroot . '/blocks/admin_tree/open.gif" alt="'.$strfolderopened.'" />\';' . "\n";
            $this->content->text .= '}' . "\n";

            $this->content->text .= 'function expandall() {' . "\n";
            $this->content->text .= '  for (i = 1; i <= vh_numdivs; i++) {' . "\n";
            $this->content->text .= '    expand("vh_div" + String(i));' . "\n";
            $this->content->text .= '  }' . "\n";
            $this->content->text .= '}' . "\n";

            $this->content->text .= 'function collapseall() {' . "\n";
            $this->content->text .= '  for (i = vh_numdivs; i > 0; i--) {' . "\n";
            $this->content->text .= '    collapse("vh_div" + String(i));' . "\n";
            $this->content->text .= '  }' . "\n";
            $this->content->text .= '}' . "\n";

            $this->content->text .= '//]]>' . "\n";
            $this->content->text .= '</script>' . "\n";
            $this->content->text .= '<div class="admintree">' . "\n";

            $this->content->text .= $this->tempcontent;

            $this->content->text .= '</div>' . "\n";
            $this->content->text .= '<script type="text/javascript">' . "\n";
            $this->content->text .= '//<![CDATA[' . "\n";
            $this->content->text .= 'collapseall();' . "\n";
            $this->content->text .= $this->expandjavascript;

            $this->content->text .= '//]]>' . "\n";
            $this->content->text .= '</script>' . "\n";

            $searchcontent = $adminroot->search;

            $this->content->footer = '<div class="adminsearchform">'.
                                     '<form action="'.$CFG->wwwroot.'/'.$CFG->admin.'/search.php" method="get"><div>'.
                                     '<label for="query" class="accesshide">'.get_string('searchinsettings', 'admin').'</label>'.
                                     '<input type="text" name="query" id="query" size="8" value="'.s($searchcontent).'" />'.
                                     '<input type="submit" value="'.get_string('search').'" /></div>'.
                                     '</form></div>';
        } else {
            $this->content = new stdClass;
            $this->content->text = '';
        }

        return $this->content;

    }
}

?>
