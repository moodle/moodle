<?php // $Id$

//---------------------------------------------------------------------------------------------------
// Miscellaneous Header Stuff
//---------------------------------------------------------------------------------------------------

include_once($CFG->dirroot . '/backup/lib.php');

//---------------------------------------------------------------------------------------------------
// Interfaces (pseudointerfaces, for PHP 4 compatibility)
//---------------------------------------------------------------------------------------------------

// part_of_admin_tree indicates that a node (whether it be an admin_settingpage or an
// admin_category or an admin_externalpage) is searchable
class part_of_admin_tree {

	function &locate($name) { trigger_error('Admin class does not implement method <strong>locate()</strong>', E_USER_WARNING); return; }
	function check_access() { trigger_error('Admin class does not implement method <strong>check_access()</strong>', E_USER_WARNING); return; }
	function path($name, $path = array()) { trigger_error('Admin class does not implement method <strong>path()</strong>', E_USER_WARNING); return; }

}

// parentable_part_of_admin_tree indicates that a node can have children in the hierarchy. only
// admin_category implements this interface (yes, yes, theoretically admin_setting* is a child of
// admin_settingpage, but you can't navigate admin_setting*s through the hierarchy)
class parentable_part_of_admin_tree extends part_of_admin_tree {

    function add($destinationname, &$something) { trigger_error('Admin class does not implement method <strong>add()</strong>', E_USER_WARNING); return; }
	
}

//---------------------------------------------------------------------------------------------------
// Classes
//---------------------------------------------------------------------------------------------------

// admin categories don't handle much... they can't be printed to the screen (except as a hierarchy), and when we
// check_access() to a category, we're actually just checking if any of its children are accessible
class admin_category extends parentable_part_of_admin_tree {

    var $children;
	var $name;
	var $visiblename;
	
	function admin_category($name, $visiblename) {
	    $this->children = array();
	    $this->name = $name;
		$this->visiblename = $visiblename;
	}
	
	function path($name, $path = array()) {
	
	    $path[count($path)] = $this->name;
	
	    if ($this->name == $name) {
			return $path;
		}
		
		foreach($this->children as $child) {
		    if ($return = $child->path($name, $path)) {
			    return $return;
			}
		}
		
		return NULL;
	
	}

    function &locate($name) {
		
	    if ($this->name == $name) {
		    return $this;
		}
	
	    foreach($this->children as $child) {
		    if ($return =& $child->locate($name)) {
			    return $return;
			}
		}
		$return = NULL;
		return $return;
	}

    function add($destinationname, &$something, $precedence = '') {
	
	    if (!is_a($something, 'part_of_admin_tree')) {
		    return false;
		}

        if ($destinationname == $this->name) {
            if ($precedence === '') {
                $this->children[] = $something;
            } else {
                if (isset($this->children[$precedence])) { // this should never, ever be triggered in a release version of moodle.
                    echo ('<font style="color: red;">There is a precedence conflict in the category ' . $this->name . '. The object named ' . $something->name . ' is overwriting the object named ' . $this->children[$precedence]->name . '.</font><br />');
                }
                $this->children[$precedence] = $something;
            }
			return true;
		}
		
		foreach($this->children as $child) {
			if (is_a($child, 'parentable_part_of_admin_tree')) {
			    if ($child->add($destinationname, $something, $precedence)) {
				    return true;
				}
			}
		}
		
		return false;
		
    }
	
	function check_access() {
	
	    $return = false;
		foreach ($this->children as $child) {
		    $return = $return || $child->check_access();
		}
	
	    return $return;
	
	}
	
}

// this is the class we use to add an external page to the admin hierarchy. on the
// external page (if you'd like), do the following for a consistent look & feel:
//  -require_once admin/adminlib.php
//  -start the page with a call to admin_externalpage_setup($name)
//  -use admin_externalpage_print_header() to print the header & blocks
//  -use admin_externalpage_print_footer() to print the footer
class admin_externalpage extends part_of_admin_tree {

    var $name;
    var $visiblename;
    var $url;
    var $role;
    
    function admin_externalpage($name, $visiblename, $url, $role = 'moodle/legacy:admin') {
        $this->name = $name;
        $this->visiblename = $visiblename;
        $this->url = $url;
        $this->role = $role;
    }
    
	function path($name, $path = array()) {
	    if ($name == $this->name) {
		    array_push($path, $this->name);
		    return $path;
		} else {
		    return NULL;
		}
	}
	
	function &locate($name) {
        $return = ($this->name == $name ? $this : NULL);
	    return $return;
	}
    
	function check_access() {
	    $context = get_context_instance(CONTEXT_SYSTEM, SITEID); 
        return has_capability($this->role, $context);
	}

}

// authentication happens at this level
// an admin_settingpage is a LEAF of the admin_tree, it can't have children. it only contains
// an array of admin_settings that can be printed out onto a webpage
class admin_settingpage extends part_of_admin_tree {

    var $name;
	var $visiblename;
	var $settings;
	var $role;
	
	function path($name, $path = array()) {
	    if ($name == $this->name) {
		    array_push($path, $this->name);
		    return $path;
		} else {
		    return NULL;
		}
	}
	
	function &locate($name) {
        $return = ($this->name == $name ? $this : NULL);
	    return $return;
	}
	
	function admin_settingpage($name, $visiblename, $role = 'moodle/legacy:admin') {
	    global $CFG;
	    $this->settings = new stdClass();
		$this->name = $name;
		$this->visiblename = $visiblename;
		$this->role = $role;
	}
	
	function add(&$setting) {
	    if (is_a($setting, 'admin_setting')) {
		    $temp = $setting->name;
    	    $this->settings->$temp =& $setting;
			return true;
		}
		return false;
	}
	
	function check_access() {
	    $context = get_context_instance(CONTEXT_SYSTEM, SITEID); 
        return has_capability($this->role, $context);
	}
	
	function output_html() {
	    $return = '<table class="generaltable" width="100%" border="0" align="center" cellpadding="5" cellspacing="1">' . "\n";
	    foreach($this->settings as $setting) {
		  $return .= $setting->output_html();
		}
		$return .= '</table>';
		return $return;
	}

    // return '' (empty string) for successful write, otherwise return language-specific error
    function write_settings($data) {
	    $return = '';
		foreach($this->settings as $setting) {
		    $return .= $setting->write_setting($data['s_' . $setting->name]);
		}
		return $return;
	}

}


// read & write happens at this level; no authentication
class admin_setting {

    var $name;
	var $visiblename;
	var $description;
	var $data;

    function admin_setting($name, $visiblename, $description) {
	    $this->name = $name;
		$this->visiblename = $visiblename;
		$this->description = $description;
	}
	
	function get_setting() {
	    return; // has to be overridden
	}
	
	function write_setting($data) {
	    return; // has to be overridden
	}
	
	function output_html() {
        return; // has to be overridden
	}
		
}


class admin_setting_configtext extends admin_setting {

    var $paramtype;

    function admin_setting_configtext($name, $visiblename, $description, $paramtype = PARAM_RAW) {
        $this->paramtype = $paramtype;
        parent::admin_setting($name, $visiblename, $description);
    }

    function get_setting() {
	    global $CFG;
		$temp = $this->name;  // there's gotta be a more elegant way
	    return $CFG->$temp;   // of doing this
	}
	
	function write_setting($data) {
	    $data = clean_param($data, $this->paramtype);
	    return (set_config($this->name,$data) ? '' : get_string('errorsetting', 'admin') . $this->visiblename . '<br />');
	}

    function output_html() {
        return '<tr><td width="100" align="right" valign="top">' . $this->visiblename . '</td>' .
            '<td align="left"><input type="text" size="50" name="s_'. $this->name .'" value="'. $this->get_setting() .'" /></td></tr>' .
            '<tr><td>&nbsp;</td><td align="left">' . $this->description . '</td></tr>';
    }

}

class admin_setting_configcheckbox extends admin_setting {

    function admin_setting_configcheckbox($name, $visiblename, $description) {
        parent::admin_setting($name, $visiblename, $description);
    }

    function get_setting() {
	    global $CFG;
		$temp = $this->name;  // there's gotta be a more elegant way
	    return $CFG->$temp;   // of doing this
	}
	
	function write_setting($data) {
	    if ($data == '1') {
    	    return (set_config($this->name,1) ? '' : get_string('errorsetting', 'admin') . $this->visiblename . '<br />');
		} else {
    	    return (set_config($this->name,0) ? '' : get_string('errorsetting', 'admin') . $this->visiblename . '<br />');
		}
	}

    function output_html() {
        return '<tr><td width="100" align="right" valign="top">' . $this->visiblename . '</td>' .
            '<td align="left"><input type="checkbox" size="50" name="s_'. $this->name .'" value="1" ' . ($this->get_setting() == true ? 'checked="checked"' : '') . ' /></td></tr>' .
            '<tr><td>&nbsp;</td><td align="left">' . $this->description . '</td></tr>';
    }

}

class admin_setting_configselect extends admin_setting {

    var $choices;
	
    function admin_setting_configselect($name, $visiblename, $description, $choices) {
	    $this->choices = $choices;
		parent::admin_setting($name, $visiblename, $description);
	}

    function get_setting() {
	    global $CFG;
        $temp = $this->name;
	    return $CFG->$temp;
	}
	
	function write_setting($data) {
         // check that what we got was in the original choices
		 if (! in_array($data, array_keys($this->choices))) {
		     return 'Error setting ' . $this->visiblename . '<br />';
	     }
		 
		 return (set_config($this->name, $data) ? '' : get_string('errorsetting', 'admin') . $this->visiblename . '<br />');
    }
	
	function output_html() {
	    $return = '<tr><td width="100" align="right" valign="top">' . $this->visiblename . '</td><td align="left"><select name="s_' . $this->name .'">';
        foreach ($this->choices as $key => $value) {
		    $return .= '<option value="' . $key . '"' . ($key == $this->get_setting() ? ' selected="selected"' : '') . '>' . $value . '</option>';
		}
		$return .= '</select></td></tr><tr><td>&nbsp;</td><td align="left">' . $this->description . '</td></tr>';
	    return $return;
	}

}

// this is a liiitle bit messy. we're using two selects, but we're returning them as an array named after $name (so we only use $name2
// internally for the setting)
class admin_setting_configtime extends admin_setting {

    var $name2;
	var $choices;
	var $choices2;

    function admin_setting_configtime($hoursname, $minutesname, $visiblename, $description) {
	    $this->name2 = $minutesname;
		$this->choices = array();
		for ($i = 0; $i < 24; $i++) {
		    $this->choices[$i] = $i;
		}
		$this->choices2 = array();
		for ($i = 0; $i < 60; $i += 5) {
		    $this->choices2[$i] = $i;
		}
		parent::admin_setting($hoursname, $visiblename, $description);
	}

    function get_setting() {
	    global $CFG;
        $temp = $this->name;
		$temp2 = $this->name2;
	    return array((empty($CFG->$temp) ? 0 : $CFG->$temp), (empty($CFG->$temp2) ? 0 : $CFG->$temp2));
	}
	
	function write_setting($data) {
         // check that what we got was in the original choices
		 if (!(in_array($data['h'], array_keys($this->choices)) && in_array($data['m'], array_keys($this->choices2)))) {
		     return get_string('errorsetting', 'admin') . $this->visiblename . '<br />';
	     }
		 
		 return (set_config($this->name, $data['h']) && set_config($this->name2, $data['m']) ? '' : get_string('errorsetting', 'admin') . $this->visiblename . '<br />');
    }
	
	function output_html() {
	    $setvalue = $this->get_setting();
	    $return = '<tr><td width="100" align="right" valign="top">' . $this->visiblename . '</td><td align="left"><select name="s_' . $this->name .'[h]">';
        foreach ($this->choices as $key => $value) {
		    $return .= '<option value="' . $key . '"' . ($key == $setvalue[0] ? ' selected="selected"' : '') . '>' . $value . '</option>';
		}
		$return .= '</select>&nbsp;&nbsp;&nbsp;<select name="s_' . $this->name . '[m]">';
        foreach ($this->choices2 as $key => $value) {
		    $return .= '<option value="' . $key . '"' . ($key == $setvalue[1] ? ' selected="selected"' : '') . '>' . $value . '</option>';
		}		
		$return .= '</select></td></tr><tr><td>&nbsp;</td><td align="left">' . $this->description . '</td></tr>';
	    return $return;
	}

}

class admin_setting_configmultiselect extends admin_setting_configselect {

    function admin_setting_configmultiselect($name, $visiblename, $description, $choices) {
        parent::admin_setting_configselect($name, $visiblename, $description, $choices);
    }

    function get_setting() {
	    global $CFG;
	    $temp = $this->name;
	    return explode(',', $CFG->$temp);
	}
	
	function write_setting($data) {
	    foreach ($data as $datum) {
		    if (! in_array($datum, array_keys($this->choices))) {
			    return get_string('errorsetting', 'admin') . $this->visiblename . '<br />';
			}
		}
		
		return (set_config($this->name, implode(',',$data)) ? '' : get_string('errorsetting', 'admin') . $this->visiblename . '<br />');
	}
	
	function output_html() {
	    $return = '<tr><td width="100" align="right" valign="top">' . $this->visiblename . '</td><td align="left"><select name="s_' . $this->name .'[]" size="10" multiple="multiple">';
        foreach ($this->choices as $key => $value) {
		    $return .= '<option value="' . $key . '"' . (in_array($key,$this->get_setting()) ? ' selected="selected"' : '') . '>' . $value . '</option>';
		}
		$return .= '</select></td></tr><tr><td>&nbsp;</td><td align="left">' . $this->description . '</td></tr>';
	    return $return;
    }

}

class admin_setting_special_adminseesall extends admin_setting_configcheckbox {
    
	function admin_setting_special_adminseesall() {
	    $name = 'calendar_adminseesall';
		$visiblename = get_string('adminseesall', 'admin');
		$description = get_string('helpadminseesall', 'admin');
		parent::admin_setting($name, $visiblename, $description);
	}

    function write_setting($data) {
	    global $SESSION;
        unset($SESSION->cal_courses_shown);
		parent::write_setting($data);
	}
}

class admin_setting_sitesetselect extends admin_setting_configselect {

    var $id;

    function admin_setting_sitesetselect($name, $visiblename, $description, $choices) {

    	$site = get_site();	
    	$this->id = $site->id;
    	parent::admin_setting_configselect($name, $visiblename, $description, $choices);
	
	}
	
	function get_setting() {
    	$site = get_site();
    	$temp = $this->name;
    	return $site->$temp;
	}
	
	function write_setting($data) {
	    if (!in_array($data, array_keys($this->choices))) {
            return get_string('errorsetting', 'admin') . $this->visiblename . '<br />';
		}
	    $record = new stdClass();
		$record->id = $this->id;
		$temp = $this->name;
		$record->$temp = $data;
		$record->timemodified = time();
	    return (update_record('course', $record) ? '' : get_string('errorsetting', 'admin') . $this->visiblename . '<br />');
	}
	
}


class admin_setting_special_frontpage extends admin_setting_configselect {

    function admin_setting_special_frontpage($loggedin = false) {
	    global $CFG;
	    require_once($CFG->dirroot . '/course/lib.php');
	    $name = 'frontpage' . ($loggedin ? 'loggedin' : '');
		$visiblename = get_string('frontpage' . ($loggedin ? 'loggedin' : ''),'admin');
		$description = get_string('configfrontpage' . ($loggedin ? 'loggedin' : ''),'admin');
		$choices = array(FRONTPAGENEWS          => get_string('frontpagenews'),
                         FRONTPAGECOURSELIST    => get_string('frontpagecourselist'),
                         FRONTPAGECATEGORYNAMES => get_string('frontpagecategorynames'),
                         FRONTPAGECATEGORYCOMBO => get_string('frontpagecategorycombo'),
						 ''                     => get_string('none'));
		if (count_records("course") > FRONTPAGECOURSELIMIT) {
		    unset($choices[FRONTPAGECOURSELIST]);
		}
	    parent::admin_setting_configselect($name, $visiblename, $description, $choices);
	}
	
    function get_setting() {
	    global $CFG;
		$temp = $this->name;
		return (explode(',', $CFG->$temp));
	}
	
	function write_setting($data) {
	    if (empty($data)) {
		    $data = array();
		}
	    foreach($data as $datum) {
		    if (! in_array($datum, array_keys($this->choices))) {
			    return get_string('errorsetting', 'admin') . $this->visiblename . '<br />';
			}
		}
		return (set_config($this->name, implode(',', $data)) ? '' : get_string('errorsetting', 'admin') . $this->visiblename . '<br />');
	}
	
	function output_html() {
	    
		$currentsetting = $this->get_setting();
	    $return = '<tr><td width="100" align="right" valign="top">' . $this->visiblename . '</td><td align="left">';
		for ($i = 0; $i < count($this->choices) - 1; $i++) {
    		$return .='<select name="s_' . $this->name .'[]">';		
            foreach ($this->choices as $key => $value) {
    		    $return .= '<option value="' . $key . '"' . ($key == $currentsetting[$i] ? ' selected="selected"' : '') . '>' . $value . '</option>';
    		}
    		$return .= '</select>';
			if ($i !== count($this->choices) - 2) {
			  $return .= '&nbsp;&nbsp;' . get_string('then') . '&nbsp;&nbsp;';
			}
		}
		$return .= '</td></tr><tr><td>&nbsp;</td><td align="left">' . $this->description . '</td></tr>';
	    return $return;	
	
	
	}
}

class admin_setting_sitesetcheckbox extends admin_setting_configcheckbox {

    var $id;

    function admin_setting_sitesetcheckbox($name, $visiblename, $description) {

    	$site = get_site();	
    	$this->id = $site->id;
    	parent::admin_setting_configcheckbox($name, $visiblename, $description);
	
	}
	
	function get_setting() {
    	$site = get_site();
    	$temp = $this->name;
    	return ($site->$temp == '1' ? 1 : 0);
	}
	
	function write_setting($data) {
	    $record = new stdClass();
		$record->id = $this->id;
		$temp = $this->name;
		$record->$temp = ($data == '1' ? 1 : 0);
		$record->timemodified = time();
	    return (update_record('course', $record) ? '' : get_string('errorsetting', 'admin') . $this->visiblename . '<br />');
	}
	
}

class admin_setting_sitesettext extends admin_setting_configtext {

    var $id;

    function admin_setting_sitesettext($name, $visiblename, $description) {

    	$site = get_site();	
    	$this->id = $site->id;
    	parent::admin_setting_configtext($name, $visiblename, $description);
	
	}
	
	function get_setting() {
    	$site = get_site();
    	$temp = $this->name;
    	return $site->$temp;
	}
	
	function write_setting($data) {
	    $record = new stdClass();
		$record->id = $this->id;
		$temp = $this->name;
		$record->$temp = $data;
		$record->timemodified = time();
	    return (update_record('course', $record) ? '' : get_string('errorsetting', 'admin') . $this->visiblename . '<br />');
	}
	
}

class admin_setting_special_frontpagedesc extends admin_setting {

    var $id;
	
	function admin_setting_special_frontpagedesc() {
	    $site = get_site();
		$this->id = $site->id;
		$name = 'summary';
		$visiblename = get_string('frontpagedescription');
		$description = get_string('frontpagedescriptionhelp');
	    parent::admin_setting($name, $visiblename, $description);
	}

    function output_html() {
	
		$usehtmleditor = can_use_html_editor();
	
        $return = '<tr><td width="100" align="right" valign="top">' . $this->visiblename . '</td>' .
		           '<td>';
				   
		ob_start();  // double-check the number of columns below... might overrun some screen resolutions
		print_textarea($usehtmleditor, 20, 40, 0, 0, 's_' . $this->name, $this->get_setting());
		
		if ($usehtmleditor) {
		    use_html_editor();
		}	
		$return .= ob_get_contents();
		ob_end_clean();		
		$return .= '</td></tr><tr><td>&nbsp;</td><td>' . $this->description . '</td></tr>';
	    return $return;
	
	}
	
	function get_setting() {
	
	    $site = get_site();
		$temp = $this->name;
		return ($site->$temp);
	
	}
	
	function write_setting($data) {
	
	    $data = addslashes(clean_param($data, PARAM_CLEANHTML));
		
		$record = new stdClass();
		$record->id = $this->id;
		$temp = $this->name;
		$record->$temp = $data;
		$record->timemodified = time();
		
		return(update_record('course', $record) ? '' : get_string('errorsetting', 'admin') . $this->visiblename . '<br />');
	
	}

}


class admin_setting_special_editorfontlist extends admin_setting {

    var $items;

    function admin_setting_special_editorfontlist() {
	    global $CFG;
	    $name = 'editorfontlist';
		$visiblename = get_string('editorfontlist', 'admin');
		$description = get_string('configeditorfontlist', 'admin');
		$items = explode(';', $CFG->editorfontlist);
		$this->items = array();
		foreach ($items as $item) {
		  $item = explode(':', $item);
		  $this->items[$item[0]] = $item[1];
		}
		parent::admin_setting($name, $visiblename, $description);
	}
	
	function get_setting() {
	    return $this->items;
	}
	
	function write_setting($data) {
	
	    // there miiight be an easier way to do this :)
		
	    $keys = array();
		$values = array();
		
		foreach ($data as $key => $value) {
		    if (substr($key,0,1) == 'k') {
			    $keys[substr($key,1)] = $value;
			} elseif (substr($key,0,1) == 'v') {
			    $values[substr($key,1)] = $value;
			}
		}
		
		$result = '';
		for ($i = 0; $i < count($keys); $i++) {
		    if (($keys[$i] !== '') && ($values[$i] !== '')) {
    		    $result .= $keys[$i] . ':' . $values[$i] . ';';
			}
		}
		
		$result = substr($result, 0, -1); // trim the last semicolon
		
		return (set_config($this->name, $result) ? '' : get_string('errorsetting', 'admin') . $this->visiblename . '<br />');
	}
    
	function output_html() {
        $return = '<tr><td width="100" align="right" valign="top">' . $this->visiblename . '</td><td align="left">';
		$count = 0;
		foreach ($this->items as $key => $value) {
		    $return .= '<input type="text" name="s_editorfontlist[k' . $count . ']" value="' . $key . '" size="20" />';
			$return .= '&nbsp;&nbsp;';
            $return .= '<input type="text" name="s_editorfontlist[v' . $count . ']" value="' . $value . '" size="40" /><br />';
		    $count++;
		}
	    $return .= '<input type="text" name="s_editorfontlist[k' . $count . ']" value="" size="20" />';
		$return .= '&nbsp;&nbsp;';
        $return .= '<input type="text" name="s_editorfontlist[v' . $count . ']" value="" size="40" /><br />';
	    $return .= '<input type="text" name="s_editorfontlist[k' . ($count + 1) . ']" value="" size="20" />';
		$return .= '&nbsp;&nbsp;';
        $return .= '<input type="text" name="s_editorfontlist[v' . ($count + 1) . ']" value="" size="40" />';
		$return .= '</td></tr><tr><td>&nbsp;</td><td align="left">' . $this->description . '</td></tr>';	
        return $return;
	}
	
}

class admin_setting_special_editordictionary extends admin_setting_configselect {

    function admin_setting_special_editordictionary() {
	    $name = 'editordictionary';
		$visiblename = get_string('editordictionary','admin');
		$description = get_string('configeditordictionary', 'admin');
		$choices = $this->editor_get_dictionaries();
		if (! is_array($choices)) {
		    $choices = array('');
		}
	
	    parent::admin_setting_configselect($name, $visiblename, $description, $choices);
	}

    // function borrowed from the old moodle/admin/editor.php, slightly modified
    function editor_get_dictionaries () {
    /// Get all installed dictionaries in the system

        global $CFG;
    
//        error_reporting(E_ALL); // for debug, final version shouldn't have this...
        clearstatcache();

        // If aspellpath isn't set don't even bother ;-)
        if (empty($CFG->aspellpath)) {
            return 'Empty aspell path!';
        }

        // Do we have access to popen function?
        if (!function_exists('popen')) {
            return 'Popen function disabled!';
        }
    
        $cmd          = $CFG->aspellpath;
        $output       = '';
        $dictionaries = array();
        $dicts        = array();

        if(!($handle = @popen(escapeshellarg($cmd) .' dump dicts', 'r'))) {
            return 'Couldn\'t create handle!';
        }

        while(!feof($handle)) {
            $output .= fread($handle, 1024);
        }
        @pclose($handle);

        $dictionaries = explode(chr(10), $output);

        // Get rid of possible empty values
        if (is_array($dictionaries)) {

            $cnt = count($dictionaries);

            for ($i = 0; $i < $cnt; $i++) {
                if (!empty($dictionaries[$i])) {
                    $dicts[] = $dictionaries[$i];
                }
            }
        }

        if (count($dicts) >= 1) {
            return $dicts;
        }

        return 'Error! Check your aspell installation!';
    }

    

}


class admin_setting_special_editorhidebuttons extends admin_setting {

    var $name;
	var $visiblename;
	var $description;
	var $items;

    function admin_setting_special_editorhidebuttons() {
	    $this->name = 'editorhidebuttons';
		$this->visiblename = get_string('editorhidebuttons', 'admin');
		$this->description = get_string('confeditorhidebuttons', 'admin');
        // weird array... buttonname => buttonimage (assume proper path appended). if you leave buttomimage blank, text will be printed instead
		$this->items = array('fontname' => '',
		                 'fontsize' => '',
						 'formatblock' => '',
						 'bold' => 'ed_format_bold.gif',
						 'italic' => 'ed_format_italic.gif',
						 'underline' => 'ed_format_underline.gif',
						 'strikethrough' => 'ed_format_strike.gif',
						 'subscript' => 'ed_format_sub.gif',
						 'superscript' => 'ed_format_sup.gif',
						 'copy' => 'ed_copy.gif',
						 'cut' => 'ed_cut.gif',
						 'paste' => 'ed_paste.gif',
						 'clean' => 'ed_wordclean.gif',
						 'undo' => 'ed_undo.gif',
						 'redo' => 'ed_redo.gif',
						 'justifyleft' => 'ed_align_left.gif',
						 'justifycenter' => 'ed_align_center.gif',
						 'justifyright' => 'ed_align_right.gif',
						 'justifyfull' => 'ed_align_justify.gif',
						 'lefttoright' => 'ed_left_to_right.gif',
						 'righttoleft' => 'ed_right_to_left.gif',
						 'insertorderedlist' => 'ed_list_num.gif',
						 'insertunorderedlist' => 'ed_list_bullet.gif',
						 'outdent' => 'ed_indent_less.gif',
						 'indent' => 'ed_indent_more.gif',
						 'forecolor' => 'ed_color_fg.gif',
						 'hilitecolor' => 'ed_color_bg.gif',
						 'inserthorizontalrule' => 'ed_hr.gif',
						 'createanchor' => 'ed_anchor.gif',
						 'createlink' => 'ed_link.gif',
						 'unlink' => 'ed_unlink.gif',
						 'insertimage' => 'ed_image.gif',
						 'inserttable' => 'insert_table.gif',
						 'insertsmile' => 'em.icon.smile.gif',
						 'insertchar' => 'icon_ins_char.gif',
						 'spellcheck' => 'spell-check.gif',
						 'htmlmode' => 'ed_html.gif',
						 'popupeditor' => 'fullscreen_maximize.gif',
						 'search_replace' => 'ed_replace.gif');
	}

    function get_setting() {
	    global $CFG;
	    $temp = $this->name;
	    return explode(' ', $CFG->$temp);
	}

    function write_setting($data) {
	    $result = array();
		if (empty($data)) { $data = array(); }
        foreach ($data as $key => $value) {
		    if (!in_array($key, array_keys($this->items))) {
		        return get_string('errorsetting', 'admin') . $this->visiblename . '<br />';
			}
			if ($value == '1') {
			    $result[] = $key;
			}
		}
		return (set_config($this->name, implode(' ',$result)) ? '' : get_string('errorsetting', 'admin') . $this->visiblename . '<br />');
	}

    function output_html() {
	
	    global $CFG;
		
	    // checkboxes with input name="$this->name[$key]" value="1"
		// we do 15 fields per column
		
		$currentsetting = $this->get_setting();
		
		$return = '<tr><td width="100" align="right" valign="top">' . $this->visiblename . '</td><td align="left">';
		
		$return .= '<table><tr><td valign="top" align="right">';
		
		$count = 0;
		
		foreach($this->items as $key => $value) {
		    if ($count % 15 == 0) {
			    $return .= '</div></td><td valign="top" align="right">';
			}
			
			$return .= ($value == '' ? get_string($key,'editor') : '<img width="18" height="18" src="' . $CFG->wwwroot . '/lib/editor/htmlarea/images/' . $value . '" alt="' . get_string($key,'editor') . '" title="' . get_string($key,'editor') . '" />') . '&nbsp;';
			$return .= '<input type="checkbox" value="1" name="s_' . $this->name . '[' . $key . ']"' . (in_array($key,$currentsetting) ? ' checked="checked"' : '') . ' />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			$count++;
			if ($count % 15 != 0) {
			    $return .= '<br /><br />';
			}
		}
		
		$return .= '</td></tr>';	
		$return .= '</table>';
		$return .= '</td></tr><tr><td>&nbsp;</td><td align="left">' . $this->description . '</td></tr>';

	    return $return;
	}

}

class admin_setting_backupselect extends admin_setting_configselect {

    function admin_setting_backupselect($name, $visiblename, $description, $choices) {
        parent::admin_setting_configselect($name, $visiblename, $description, $choices);
    }

    function get_setting() {
    	$backup_config =  backup_get_config(); // we need this function from backup/lib.php ... but it causes conflicts. ideas?
		$temp = $this->name;
		return (isset($backup_config->$temp) ? $backup_config->$temp : 0); // we default to false/0 if the pair doesn't exist
	}
	
	function write_setting($data) {
         // check that what we got was in the original choices
		 if (! in_array($data, array_keys($this->choices))) {
		     return get_string('errorsetting', 'admin') . $this->visiblename . '<br />';
	     }
		 
		 return (backup_set_config($this->name, $data) ? '' : get_string('errorsetting', 'admin') . $this->visiblename . '<br />');
    }

}

class admin_setting_special_backupsaveto extends admin_setting_configtext {

    function admin_setting_special_backupsaveto() {
	    $name = 'backup_sche_destination';
		$visiblename = get_string('saveto');
		$description = get_string('backupsavetohelp');
		parent::admin_setting_configtext($name, $visiblename, $description);
	}
	
	function get_setting() {
    	$backup_config =  backup_get_config();
		$temp = $this->name;
		return (isset($backup_config->$temp) ? $backup_config->$temp : ''); // we default to false/0 if the pair doesn't exist
	}
	
	function write_setting($data) {
        $data = clean_param($data, PARAM_PATH);
    	if (!empty($data) and (substr($data,-1) == '/' or substr($data,-1) == '\\')) {
            return get_string('pathslasherror') . '<br />';
        } else if (!empty($data) and !is_dir($data)) {
		    return get_string('pathnotexists') . '<br />';
        }
		return (backup_set_config($this->name, $data) ? '' : get_string('errorsetting', 'admin') . $this->visiblename . '<br />');
	}

}

class admin_setting_backupcheckbox extends admin_setting_configcheckbox {

    function admin_setting_backupcheckbox($name, $visiblename, $description) {
        parent::admin_setting_configcheckbox($name, $visiblename, $description);
    }

    function write_setting($data) {
	    if ($data == '1') {
		    return (backup_set_config($this->name, 1) ? '' : get_string('errorsetting', 'admin') . $this->visiblename . '<br />');
		} else {
		    return (backup_set_config($this->name, 0) ? '' : get_string('errorsetting', 'admin') . $this->visiblename . '<br />');
		}
	}
	
	function get_setting() {
    	$backup_config =  backup_get_config();
		$temp = $this->name;
		return (isset($backup_config->$temp) ? $backup_config->$temp : 0); // we default to false if the pair doesn't exist
	}

}

class admin_setting_special_backuptime extends admin_setting_configtime {

    function admin_setting_special_backuptime() {
	    $name = 'backup_sche_hour';
		$name2 = 'backup_sche_minute';
		$visiblename = get_string('executeat');
		$description = get_string('backupexecuteathelp');
        parent::admin_setting_configtime($name, $name2, $visiblename, $description);
    }
	
	function get_setting() {
	    $backup_config =  backup_get_config();
		$temp = $this->name;
		$temp2 = $this->name2;
		return array(isset($backup_config->$temp) ? $backup_config->$temp : 0, isset($backup_config->$temp2) ? $backup_config->$temp2 : 0); // we default to 0:0 if the pair doesn't exist	
	}
	
	function write_setting($data) {
         // check that what we got was in the original choices
		 if (!(in_array($data['h'], array_keys($this->choices)) && in_array($data['m'], array_keys($this->choices2)))) {
		     return get_string('errorsetting', 'admin') . $this->visiblename . '<br />';
	     }
		 
		 return (backup_set_config($this->name, $data['h']) && backup_set_config($this->name2, $data['m']) ? '' : get_string('errorsetting', 'admin') . $this->visiblename . '<br />');	
	}
	
}

class admin_setting_special_backupdays extends admin_setting {

    function admin_setting_special_backupdays() {
	    $name = 'backup_sche_weekdays';
		$visiblename = get_string('schedule');
		$description = get_string('backupschedulehelp');
		parent::admin_setting($name, $visiblename, $description);
	}
	
	function get_setting() {
	    $backup_config =  backup_get_config();
		$temp = $this->name;
		return (isset($backup_config->$temp) ? $backup_config->$temp : '0000000');
	}
	
	function output_html() {
	    
        return '<tr><td width="100" align="right" valign="top">' . $this->visiblename . '</td><td align="left">' .
		'<table><tr><td><div align="center">&nbsp;&nbsp;' . get_string('sunday', 'calendar') . '&nbsp;&nbsp;</div></td><td><div align="center">&nbsp;&nbsp;' . 
		get_string('monday', 'calendar') . '&nbsp;&nbsp;</div></td><td><div align="center">&nbsp;&nbsp;' . get_string('tuesday', 'calendar') . '&nbsp;&nbsp;</div></td><td><div align="center">&nbsp;&nbsp;' .
		get_string('wednesday', 'calendar') . '&nbsp;&nbsp;</div></td><td><div align="center">&nbsp;&nbsp;' . get_string('thursday', 'calendar') . '&nbsp;&nbsp;</div></td><td><div align="center">&nbsp;&nbsp;' .
		get_string('friday', 'calendar') . '&nbsp;&nbsp;</div></td><td><div align="center">&nbsp;&nbsp;' . get_string('saturday', 'calendar') . '&nbsp;&nbsp;</div></td></tr><tr>' .
		'<td><div align="center"><input type="checkbox" name="s_'. $this->name .'[u]" value="1" ' . (substr($this->get_setting(),0,1) == '1' ? 'checked="checked"' : '') . ' /></div></td>' . 
		'<td><div align="center"><input type="checkbox" name="s_'. $this->name .'[m]" value="1" ' . (substr($this->get_setting(),1,1) == '1' ? 'checked="checked"' : '') . ' /></div></td>' . 
		'<td><div align="center"><input type="checkbox" name="s_'. $this->name .'[t]" value="1" ' . (substr($this->get_setting(),2,1) == '1' ? 'checked="checked"' : '') . ' /></div></td>' . 
		'<td><div align="center"><input type="checkbox" name="s_'. $this->name .'[w]" value="1" ' . (substr($this->get_setting(),3,1) == '1' ? 'checked="checked"' : '') . ' /></div></td>' . 
		'<td><div align="center"><input type="checkbox" name="s_'. $this->name .'[r]" value="1" ' . (substr($this->get_setting(),4,1) == '1' ? 'checked="checked"' : '') . ' /></div></td>' . 
		'<td><div align="center"><input type="checkbox" name="s_'. $this->name .'[f]" value="1" ' . (substr($this->get_setting(),5,1) == '1' ? 'checked="checked"' : '') . ' /></div></td>' . 
		'<td><div align="center"><input type="checkbox" name="s_'. $this->name .'[s]" value="1" ' . (substr($this->get_setting(),6,1) == '1' ? 'checked="checked"' : '') . ' /></div></td>' . 
		'</tr></table>' . 												
        '</td></tr><tr><td>&nbsp;</td><td align="left">' . $this->description . '</td></tr>';
	
	}
	
	// we're using the array trick (see http://ca.php.net/manual/en/faq.html.php#faq.html.arrays) to get the data passed to use without having to modify
	// admin_settingpage (note that admin_settingpage only calls write_setting with the data that matches $this->name... so if we have multiple form fields,
	// they MUST go into an array named $this->name, or else we won't receive them here
	function write_setting($data) {
		$week = 'umtwrfs';
	    $result = array(0 => 0, 1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0);
	    foreach($data as $key => $value) {
		  if ($value == '1') { 
		      $result[strpos($week, $key)] = 1;
		  }
	    }
		return (backup_set_config($this->name, implode('',$result)) ? '' : get_string('errorsetting', 'admin') . $this->visiblename . '<br />');
	}
}

class admin_setting_special_debug extends admin_setting_configcheckbox {

    function admin_setting_special_debug() {
	    $name = 'debug';
		$visiblename = get_string('debug', 'admin');
		$description = get_string('configdebug', 'admin');
		parent::admin_setting_configcheckbox($name, $visiblename, $description);
	}

	function write_setting($data) {
	    if ($data == '1') {
    	    return (set_config($this->name,15) ? '' : get_string('errorsetting', 'admin') . $this->visiblename . '<br />');
		} else {
    	    return (set_config($this->name,7) ? '' : get_string('errorsetting', 'admin') . $this->visiblename . '<br />');
		}
	}

    function output_html() {
        return '<tr><td width="100" align="right" valign="top">' . $this->visiblename . '</td>' .
            '<td align="left"><input type="checkbox" size="50" name="s_'. $this->name .'" value="1" ' . ($this->get_setting() == 15 ? 'checked="checked"' : '') . ' /></td></tr>' .
            '<tr><td>&nbsp;</td><td align="left">' . $this->description . '</td></tr>';
    }

}


class admin_setting_special_calendar_weekend extends admin_setting {

    function admin_setting_special_calendar_weekend() {
        $name = 'calendar_weekend';
        $visiblename = get_string('calendar_weekend', 'admin');
        $description = get_string('helpweekenddays', 'admin');
        parent::admin_setting($name, $visiblename, $description);
    }

    function get_setting() {
        global $CFG;
        $temp = $this->name;
        $setting = intval($CFG->$temp);
        return array('u' => $setting & 1, 'm' => $setting & 2, 't' => $setting & 4, 'w' => $setting & 8, 'r' => $setting & 16, 'f' => $setting & 32, 's' => $setting & 64);
    }
    
	function write_setting($data) {
		$week = 'umtwrfs';
	    $result = array(0 => 0, 1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0);
	    foreach($data as $key => $value) {
		  if ($value == '1') { 
		      $result[strpos($week, $key)] = 1;
		  }
	    }
		return (set_config($this->name, bindec(implode('',$result))) ? '' : get_string('errorsetting', 'admin') . $this->visiblename . '<br />');
	}
    
	function output_html() {

        $result = $this->get_setting();
	    
        return '<tr><td width="100" align="right" valign="top">' . $this->visiblename . '</td><td align="left">' .
		'<table><tr><td><div align="center">&nbsp;&nbsp;' . get_string('sunday', 'calendar') . '&nbsp;&nbsp;</div></td><td><div align="center">&nbsp;&nbsp;' . 
		get_string('monday', 'calendar') . '&nbsp;&nbsp;</div></td><td><div align="center">&nbsp;&nbsp;' . get_string('tuesday', 'calendar') . '&nbsp;&nbsp;</div></td><td><div align="center">&nbsp;&nbsp;' .
		get_string('wednesday', 'calendar') . '&nbsp;&nbsp;</div></td><td><div align="center">&nbsp;&nbsp;' . get_string('thursday', 'calendar') . '&nbsp;&nbsp;</div></td><td><div align="center">&nbsp;&nbsp;' .
		get_string('friday', 'calendar') . '&nbsp;&nbsp;</div></td><td><div align="center">&nbsp;&nbsp;' . get_string('saturday', 'calendar') . '&nbsp;&nbsp;</div></td></tr><tr>' .
		'<td><div align="center"><input type="checkbox" name="s_'. $this->name .'[u]" value="1" ' . ($result['u'] ? 'checked="checked"' : '') . ' /></div></td>' . 
		'<td><div align="center"><input type="checkbox" name="s_'. $this->name .'[m]" value="1" ' . ($result['m'] ? 'checked="checked"' : '') . ' /></div></td>' . 
		'<td><div align="center"><input type="checkbox" name="s_'. $this->name .'[t]" value="1" ' . ($result['t'] ? 'checked="checked"' : '') . ' /></div></td>' . 
		'<td><div align="center"><input type="checkbox" name="s_'. $this->name .'[w]" value="1" ' . ($result['w'] ? 'checked="checked"' : '') . ' /></div></td>' . 
		'<td><div align="center"><input type="checkbox" name="s_'. $this->name .'[r]" value="1" ' . ($result['r'] ? 'checked="checked"' : '') . ' /></div></td>' . 
		'<td><div align="center"><input type="checkbox" name="s_'. $this->name .'[f]" value="1" ' . ($result['f'] ? 'checked="checked"' : '') . ' /></div></td>' . 
		'<td><div align="center"><input type="checkbox" name="s_'. $this->name .'[s]" value="1" ' . ($result['s'] ? 'checked="checked"' : '') . ' /></div></td>' . 
		'</tr></table>' . 												
        '</td></tr><tr><td>&nbsp;</td><td align="left">' . $this->description . '</td></tr>';
	
	}

}


class admin_setting_special_perfdebug extends admin_setting_configcheckbox {

    function admin_setting_special_perfdebug() {
	    $name = 'perfdebug';
		$visiblename = get_string('perfdebug', 'admin');
		$description = get_string('configperfdebug', 'admin');
		parent::admin_setting_configcheckbox($name, $visiblename, $description);
	}

	function write_setting($data) {
	    if ($data == '1') {
    	    return (set_config($this->name,15) ? '' : get_string('errorsetting', 'admin') . $this->visiblename . '<br />');
		} else {
    	    return (set_config($this->name,7) ? '' : get_string('errorsetting', 'admin') . $this->visiblename . '<br />');
		}
	}

    function output_html() {
        return '<tr><td width="100" align="right" valign="top">' . $this->visiblename . '</td>' .
            '<td align="left"><input type="checkbox" size="50" name="s_'. $this->name .'" value="1" ' . ($this->get_setting() == 15 ? 'checked="checked"' : '') . ' /></td></tr>' .
            '<tr><td>&nbsp;</td><td align="left">' . $this->description . '</td></tr>';
    }

}

// Code for a function that helps externalpages print proper headers and footers
// N.B.: THIS FUNCTION HANDLES AUTHENTICATION
function admin_externalpage_setup($section) {

    global $CFG, $ADMIN, $PAGE, $_GET, $USER;
    
    require_once($CFG->libdir . '/blocklib.php');
    require_once($CFG->dirroot . '/admin/pagelib.php');
    

    // this needs to be changed.
    $_GET['section'] = $section;
    
    define('TEMPORARY_ADMIN_PAGE_ID',26);

    define('BLOCK_L_MIN_WIDTH',160);
    define('BLOCK_L_MAX_WIDTH',210);

    $pagetype = PAGE_ADMIN;               
    $pageclass = 'page_admin';            
    page_map_class($pagetype, $pageclass);

    $PAGE = page_create_object($pagetype,TEMPORARY_ADMIN_PAGE_ID);

    $PAGE->init_full();

    $root = $ADMIN->locate($PAGE->section);

    if ($site = get_site()) {
        require_login();
    } else {
        redirect($CFG->wwwroot . '/admin/index.php');
        die;
    }

    if (!is_a($root, 'admin_externalpage')) {
        error(get_string('sectionerror','admin'));
    	die;
    }

    // this eliminates our need to authenticate on the actual pages
    if (!($root->check_access())) {
        error(get_string('accessdenied', 'admin'));
    	die;
    }
    
    $adminediting = optional_param('adminedit', -1, PARAM_BOOL);
    
    if (!isset($USER->adminediting)) {
        $USER->adminediting = true;
    }
    
    if ($PAGE->user_allowed_editing()) {
        if ($adminediting == 1) {
            $USER->adminediting = true;
        } elseif ($adminediting == 0) {
            $USER->adminediting = false;
        }
    }
    
}

function admin_externalpage_print_header() {

    global $CFG, $ADMIN, $PAGE;
    
    $pageblocks = blocks_setup($PAGE);

    $preferred_width_left = bounded_number(BLOCK_L_MIN_WIDTH, blocks_preferred_width($pageblocks[BLOCK_POS_LEFT]), BLOCK_L_MAX_WIDTH);

    $PAGE->print_header();
    echo '<table id="layout-table"><tr>';
    echo '<td style="width: ' . $preferred_width_left . 'px;" id="left-column">';
    blocks_print_group($PAGE, $pageblocks, BLOCK_POS_LEFT);
    echo '</td>';
    echo '<td id="middle-column" width="*">';

}

function admin_externalpage_print_footer() {

    echo '</td></tr></table>';
    print_footer();
    
}



// Code to build admin-tree ----------------------------------------------------------------------------

// hrm... gotta put this somewhere more systematic
$site = get_site();

// start the admin tree!
$ADMIN = new admin_category('root','Administration');

// we process this file first to get categories up and running
include_once($CFG->dirroot . '/admin/settings/first.php');

// now we process all other files in admin/settings to build the
// admin tree
foreach (glob($CFG->dirroot . '/admin/settings/*.php') as $file) {
    if ($file != $CFG->dirroot . '/admin/settings/first.php') {
        include_once($file);
    }
}

?>