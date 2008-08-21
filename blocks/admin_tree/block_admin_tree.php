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
        $this->expandjavascript = '';
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

        $this->tempcontent .= '<div class="depth'.$this->currentdepth.'"><a href="#" onclick="menu_toggle(\''.$this->divcounter.'\');return false">';
        $this->tempcontent .= '<span id="vh_div'.$this->divcounter.'indicator"><img src="'.$CFG->pixpath.'/i/open.gif" alt="'.$strfolderopened.'" /></span> ';
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
                    $this->expandjavascript .= 'expand('.$this->divcounter.');'."\n";
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
            $closedimg = '<img src="'.$CFG->pixpath.'/i/closed.gif" alt="'.s(get_string('folderclosed')).'" />';
            $openedimg = '<img src="'.$CFG->pixpath.'/i/open.gif" alt="'.s(get_string('folderopened')).'" />';

            $this->content = new object();
            $this->content->text  = '
<script type="text/javascript">
//<![CDATA[
var vh_numdivs = '.($this->divcounter - 1).';
var parkplatz  = new Array();
for (var i=1; i<=vh_numdivs; i++) {
    parkplatz[i] = null;
}

function menu_toggle(i) {
    i = parseInt(i);
    if (parkplatz[i] === null) {
        collapse(i);
    } else {
        expand(i);
    }
}

function collapse(i) {
    if (parkplatz[i] !== null) {
        return;
    }
    var obj = document.getElementById("vh_div"+String(i));
    if (obj === null) {
        return;
    }
    var nothing = document.createElement("span");
    nothing.setAttribute("id", "vh_div"+String(i));
    parkplatz[i] = obj;
    obj.parentNode.replaceChild(nothing, obj);
    var icon = document.getElementById("vh_div"+String(i)+"indicator");
    icon.innerHTML = "'.addslashes_js($closedimg).'";
}

function expand(i) {
    if (parkplatz[i] === null) {
        return;
    }
    var nothing = document.getElementById("vh_div"+String(i));
    var obj = parkplatz[i];
    parkplatz[i] = null;
    nothing.parentNode.replaceChild(obj, nothing);
    var icon = document.getElementById("vh_div"+String(i)+"indicator");
    icon.innerHTML = "'.addslashes_js($openedimg).'";
}

function expandall() {
    for (i=1; i<=vh_numdivs; i++) {
        expand(i);
    }
}

function collapseall() {
    for (var i=vh_numdivs; i>0; i--) {
        collapse(i);
    }
}

//]]>
</script>
<div class="admintree">

'.$this->tempcontent.'

</div>
<script type="text/javascript">
//<![CDATA[
collapseall();
'.$this->expandjavascript.';
//]]>
</script>';

            // only do search if you have moodle/site:config
            if (has_capability('moodle/site:config',get_context_instance(CONTEXT_SYSTEM)) ) {
                $this->content->footer = '<div class="adminsearchform">'.
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
