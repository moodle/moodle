<?php  // $Id$

class block_admin_2 extends block_base {

    private $currentdepth;
    private $spancounter;
    private $tempcontent;
    private $pathtosection;
    private $expandjavascript;
	private $destination;

    function init() {
        $this->title = "Administration (Beta)";
        $this->version = 2006081800;
        $this->currentdepth = 0;
        $this->spancounter = 1;
        $this->tempcontent = '';
		$this->section = optional_param("section","",PARAM_PATH);
        $this->pathtosection = array();
        $this->expandjavascript = '';
    }

    function preferred_width() {
        return 210;
    }

    function open_folder($visiblename) {
        global $CFG;
        for ($i = 0; $i < $this->currentdepth; $i++) {
            $this->tempcontent .= "&nbsp;&nbsp;&nbsp;";
        }
        $this->tempcontent .= '<a href="javascript:toggle(\'vh_span' . $this->spancounter . '\');">';
        $this->tempcontent .= '<span id="vh_span' . $this->spancounter . 'indicator"><img src="' . $CFG->wwwroot . '/blocks/admin_2/open.gif" border="0" alt="[open folder]" /></span> ';
        $this->tempcontent .= $visiblename . '</a><br /><span id="vh_span' . $this->spancounter . '">' . "\n";
        $this->currentdepth++;
        $this->spancounter++;
    }

    function close_folder() {
        $this->currentdepth--;
        $this->tempcontent .= "</span>\n";
    }

    function create_item($visiblename,$link,$icon) {
        global $CFG;
        for ($i = 0; $i < $this->currentdepth; $i++) {
            $this->tempcontent .= "&nbsp;&nbsp;&nbsp;";
        }
        $this->tempcontent .= '<a href="' . $link . '"><img src="' . $icon . '" border="0" alt="[item]" /> ' . $visiblename . '</a><br />' . "\n";
    }

    function build_tree (&$content) {
	    global $CFG;
		if ($content instanceof admin_settingpage) {
		    if ($content->check_access()) {
        		$this->create_item($content->visiblename,$CFG->wwwroot.'/admin/settings.php?section=' . $content->name,$CFG->wwwroot .'/blocks/admin_2/item.gif');
			}
		} else if ($content instanceof admin_externalpage) {
		    if ($content->check_access()) {
		        $this->create_item($content->visiblename, $content->url, $CFG->wwwroot . '/blocks/admin_2/item.gif');
		    }
		} else if ($content instanceof admin_category) {
		    if ($content->check_access()) {
			
                // check if the category we're currently printing is a parent category for the current page; if it is, we
				// make a note (in the javascript) that it has to be expanded after the page has loaded
        		if ($this->pathtosection[count($this->pathtosection) - 1] == $content->name) {
        		    $this->expandjavascript .= 'expand("vh_span' . ($this->spancounter) . '");' . "\n";
        			array_pop($this->pathtosection);
        		}

			    $this->open_folder($content->visiblename);

    		    foreach ($content->children as &$child) {
			        $this->build_tree($child);
    			}

				$this->close_folder();
			}
		}
    }

    function get_content() {

        global $CFG, $ADMIN;
		
		// better place to require this?
		if (!$ADMIN) {
           	require_once($CFG->dirroot . '/admin/adminlib.php');
        }

        if ($this->content !== NULL) {
            return $this->content;
        }

        if ($this->pathtosection = $ADMIN->path($this->section)) {
            $this->pathtosection = array_reverse($this->pathtosection);
    		array_pop($this->pathtosection);
		}

        // we need to do this instead of $this->build_tree($ADMIN) because the top-level folder
		// is redundant (and ideally ignored). (the top-level folder is "administration".)
		ksort($ADMIN->children);			    
	    foreach ($ADMIN->children as &$child) {
            $this->build_tree($child);
        }
	
        if ($this->tempcontent !== '') {
            $this->content = new stdClass;
            $this->content->text = '<script language="JavaScript">' . "\n\n";
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
            $this->content->text .= '    getspan(spanid + "indicator").innerHTML = "<img src=\"' . $CFG->wwwroot . '/blocks/admin_2/open.gif\" border=\"0\" alt=\"[open folder]\" />";' . "\n";
            $this->content->text .= '  } else {' . "\n";
            $this->content->text .= '    vh_content[spanid] = getspan(spanid).innerHTML;' . "\n";
            $this->content->text .= '    getspan(spanid).innerHTML = "";' . "\n";
            $this->content->text .= '    getspan(spanid + "indicator").innerHTML = "<img src=\"' . $CFG->wwwroot . '/blocks/admin_2/closed.gif\" border=\"0\" alt=\"[closed folder]\" />";' . "\n";
            $this->content->text .= '  }' . "\n";
            $this->content->text .= '}' . "\n";

            $this->content->text .= 'function collapse(spanid) {' . "\n";
            $this->content->text .= '  if (getspan(spanid).innerHTML !== "") {' . "\n";
            $this->content->text .= '    vh_content[spanid] = getspan(spanid).innerHTML;' . "\n";
            $this->content->text .= '    getspan(spanid).innerHTML = "";' . "\n";
            $this->content->text .= '    getspan(spanid + "indicator").innerHTML = "<img src=\"' . $CFG->wwwroot . '/blocks/admin_2/closed.gif\" border=\"0\" alt=\"[closed folder]\" />";' . "\n";
            $this->content->text .= '  }' . "\n";
            $this->content->text .= '}' . "\n";

            $this->content->text .= 'function expand(spanid) {' . "\n";
            $this->content->text .= '  getspan(spanid).innerHTML = vh_content[spanid];' . "\n";
            $this->content->text .= '  getspan(spanid + "indicator").innerHTML = "<img src=\"' . $CFG->wwwroot . '/blocks/admin_2/open.gif\" border=\"0\" alt=\"[open folder]\" />";' . "\n";
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

            $this->content->text .= '</script>' . "\n";
            $this->content->text .= '<div align="left">' . "\n";
            
            $this->content->text .= $this->tempcontent;
  
            $this->content->text .= '</div>' . "\n";
            $this->content->text .= '<script language="JavaScript">' . "\n";
            $this->content->text .= 'collapseall();' . "\n";
            $this->content->text .= $this->expandjavascript;

            $this->content->text .= '</script>' . "\n";
            $this->content->footer = '';
        }
        return $this->content;

    }
}

?>
