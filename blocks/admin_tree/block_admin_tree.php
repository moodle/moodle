<?php  // $Id$

class block_admin_tree extends block_base {

    var $currentdepth;
    var $spancounter;
    var $tempcontent;
    var $pathtosection;
    var $expandjavascript;
    var $destination;

    function init() {
        global $PAGE;
        $this->title = get_string('administrationsite');
        $this->version = 2006090300;
        $this->currentdepth = 0;
        $this->spancounter = 1;
        $this->tempcontent = '';
        $this->section = (isset($PAGE->section) ? $PAGE->section : '');
        $this->pathtosection = array();
        $this->expandjavascript = '';
    }

    function applicable_formats() {
        return array('site' => true, 'admin' => true);
    }

    function preferred_width() {
        return 210;
    }

    function open_folder($visiblename) {
        global $CFG;
        $strfolderopened = s(get_string('folderopened'));

        for ($i = 0; $i < $this->currentdepth; $i++) {
            $this->tempcontent .= "&nbsp; &nbsp;";
        }
        $this->tempcontent .= '<a href="#" onclick="toggle(\'vh_span' . $this->spancounter . '\');return false">';
        $this->tempcontent .= '<span id="vh_span' . $this->spancounter . 'indicator"><img src="' . $CFG->wwwroot . '/blocks/admin_tree/open.gif" alt="'.$strfolderopened.'" /></span> ';
        $this->tempcontent .= $visiblename . '</a><br /><span id="vh_span' . $this->spancounter . '">' . "\n";
        $this->currentdepth++;
        $this->spancounter++;
    }

    function close_folder() {
        $this->currentdepth--;
        $this->tempcontent .= "</span>\n";
    }

    function create_item($visiblename,$link,$icon,$class) {
        global $CFG;
        for ($i = 0; $i < $this->currentdepth; $i++) {
            $this->tempcontent .= "&nbsp; &nbsp;";
        }
        $this->tempcontent .= '<a class="'.$class.'" href="'.$link.'"><img src="'.$icon.'" alt="" />'.
                               $visiblename.'</a><br />'."\n";
    }

    function build_tree (&$content) {
        global $CFG;
        if (is_a($content, 'admin_settingpage')) {
            if ($content->check_access() and !$content->is_hidden()) {
                $class = ($content->name == $this->section) ? 'link current' : 'link';
                $this->create_item($content->visiblename,$CFG->wwwroot.'/'.$CFG->admin.'/settings.php?section=' . $content->name,$CFG->wwwroot .'/blocks/admin_tree/item.gif', $class);
            }
        } else if (is_a($content, 'admin_externalpage')) {
            if ($content->check_access() and !$content->is_hidden()) {
                $class = ($content->name == $this->section) ? 'link current' : 'link';
                $this->create_item($content->visiblename, $content->url, $CFG->wwwroot . '/blocks/admin_tree/item.gif', $class);
            }
        } else if (is_a($content, 'admin_category')) {
            if ($content->check_access() and !$content->is_hidden()) {

                // check if the category we're currently printing is a parent category for the current page; if it is, we
                // make a note (in the javascript) that it has to be expanded after the page has loaded
                if ($this->pathtosection[count($this->pathtosection) - 1] == $content->name) {
                    $this->expandjavascript .= 'expand("vh_span' . ($this->spancounter) . '");' . "\n";
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

        global $CFG, $ADMIN;

        require_once($CFG->libdir.'/adminlib.php');
        $adminroot = admin_get_root();

        if ($this->content !== NULL) {
            return $this->content;
        }

        if ($this->pathtosection = $adminroot->path($this->section)) {
            $this->pathtosection = array_reverse($this->pathtosection);
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

            $this->content = new stdClass;
            $this->content->text  = '<script type="text/javascript">'."\n";
            $this->content->text .= '//<![CDATA[' . "\n";
            $this->content->text .= 'var vh_numspans = ' . ($this->spancounter - 1) . ';' . "\n";
            $this->content->text .= 'var vh_content = new Array();' . "\n";
            $this->content->text .= 'function getspan(spanid) {' . "\n";
            $this->content->text .= '  if (document.getElementById) {' . "\n";
            $this->content->text .= '    return document.getElementById(spanid);' . "\n";
            $this->content->text .= '  } else if (window[spanid]) {' . "\n";
            $this->content->text .= '    return window[spanid];' . "\n";
            $this->content->text .= '  }' . "\n";
            $this->content->text .= '  return null;' . "\n";
            $this->content->text .= '}' . "\n";

            $this->content->text .= 'function toggle(spanid) {' . "\n";
            $this->content->text .= '  if (getspan(spanid).innerHTML == "") {' . "\n";
            $this->content->text .= '    getspan(spanid).innerHTML = vh_content[spanid];' . "\n";
            $this->content->text .= '    getspan(spanid + "indicator").innerHTML = \'<img src="' . $CFG->wwwroot . '/blocks/admin_tree/open.gif" alt="'.$strfolderopened.'" />\';' . "\n";
            $this->content->text .= '  } else {' . "\n";
            $this->content->text .= '    vh_content[spanid] = getspan(spanid).innerHTML;' . "\n";
            $this->content->text .= '    getspan(spanid).innerHTML = "";' . "\n";
            $this->content->text .= '    getspan(spanid + "indicator").innerHTML = \'<img src="' . $CFG->wwwroot . '/blocks/admin_tree/closed.gif" alt="'.$strfolderclosed.'" />\';' . "\n";
            $this->content->text .= '  }' . "\n";
            $this->content->text .= '}' . "\n";

            $this->content->text .= 'function collapse(spanid) {' . "\n";
            $this->content->text .= '  if (getspan(spanid).innerHTML !== "") {' . "\n";
            $this->content->text .= '    vh_content[spanid] = getspan(spanid).innerHTML;' . "\n";
            $this->content->text .= '    getspan(spanid).innerHTML = "";' . "\n";
            $this->content->text .= '    getspan(spanid + "indicator").innerHTML = \'<img src="' . $CFG->wwwroot . '/blocks/admin_tree/closed.gif" alt="'.$strfolderclosed.'" />\';' . "\n";
            $this->content->text .= '  }' . "\n";
            $this->content->text .= '}' . "\n";

            $this->content->text .= 'function expand(spanid) {' . "\n";
            $this->content->text .= '  getspan(spanid).innerHTML = vh_content[spanid];' . "\n";
            $this->content->text .= '  getspan(spanid + "indicator").innerHTML = \'<img src="' . $CFG->wwwroot . '/blocks/admin_tree/open.gif" alt="'.$strfolderopened.'" />\';' . "\n";
            $this->content->text .= '}' . "\n";

            $this->content->text .= 'function expandall() {' . "\n";
            $this->content->text .= '  for (i = 1; i <= vh_numspans; i++) {' . "\n";
            $this->content->text .= '    expand("vh_span" + String(i));' . "\n";
            $this->content->text .= '  }' . "\n";
            $this->content->text .= '}' . "\n";

            $this->content->text .= 'function collapseall() {' . "\n";
            $this->content->text .= '  for (i = vh_numspans; i > 0; i--) {' . "\n";
            $this->content->text .= '    collapse("vh_span" + String(i));' . "\n";
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

            $searchcontent = isset($CFG->adminsearchquery) ? $CFG->adminsearchquery : '';

            $this->content->footer = '<div class="adminsearchform">'.
                                     '<form action="'.$CFG->wwwroot.'/admin/search.php" method="get"><div>'.
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
