<?php // $Id$

// n.b. documentation is still in progress for this code

/// INTRODUCTION

/// This file performs the following tasks:
///  -it defines the necessary objects and interfaces to build the Moodle
///   admin hierarchy
///  -it builds the $ADMIN global object (the admin tree)
///  -it defines the admin_externalpage_setup(), admin_externalpage_print_header(), 
///   and admin_externalpage_print_footer() functions used on admin pages

/// ADMIN_SETTING OBJECTS

/// Moodle settings are represented by objects that inherit from the admin_setting 
/// class. These objects encapsulate how to read a setting, how to write a new value
/// to a setting, and how to appropriately display the HTML to modify the setting.

/// ADMIN_SETTINGPAGE OBJECTS

/// The admin_setting objects are then grouped into admin_settingpages. The latter
/// appear in the Moodle admin tree block. All interaction with admin_settingpage
/// objects is handled by the admin/settings.php file.

/// ADMIN_EXTERNALPAGE OBJECTS

/// There are some settings in Moodle that are too complex to (efficiently) handle
/// with admin_settingpages. (Consider, for example, user management and displaying
/// lists of users.) In this case, we use the admin_externalpage object. This object
/// places a link to an external PHP file in the admin tree block.

/// If you're using an admin_externalpage object for some settings, you can take
/// advantage of the admin_externalpage_* functions. For example, suppose you wanted
/// to add a foo.php file into admin. First off, you add the following line to
/// admin/settings/first.php (at the end of the file) or to some other file in
/// admin/settings:

///    $ADMIN->add('userinterface', new admin_externalpage('foo', get_string('foo'), 
///        $CFG->wwwdir . '/' . '$CFG->admin . '/foo.php', 'some_role_permission'));

/// Next, in foo.php, your file structure would resemble the following:

///        require_once('.../config.php');
///        require_once($CFG->dirroot . '/' . $CFG->admin . '/adminlib.php');
///        admin_externalpage_setup('foo');
///        // functionality like processing form submissions goes here
///        admin_externalpage_print_header();
///        // your HTML goes here
///        admin_externalpage_print_footer();

/// The admin_externalpage_setup() function call ensures the user is logged in,
/// and makes sure that they have the proper role permission to access the page.

/// The admin_externalpage_print_header() function prints the header (it figures
/// out what category and subcategories the page is classified under) and ensures
/// that you're using the admin pagelib (which provides the admin tree block and
/// the admin bookmarks block).

/// The admin_externalpage_print_footer() function properly closes the tables
/// opened up by the admin_externalpage_print_header() function and prints the
/// standard Moodle footer.

/// ADMIN_CATEGORY OBJECTS

/// Above and beyond all this, we have admin_category objects. These objects
/// appear as folders in the admin tree block. They contain admin_settingpage's,
/// admin_externalpage's, and other admin_category's.

/// OTHER NOTES

/// admin_settingpage's, admin_externalpage's, and admin_category's all inherit
/// from part_of_admin_tree (a pseudointerface). This interface insists that
/// a class has a check_access method for access permissions, a locate method
/// used to find a specific node in the $ADMIN tree, and a path method used
/// to determine the path to a specific node in the $ADMIN tree.

/// admin_category's inherit from parentable_part_of_admin_tree. This pseudo-
/// interface ensures that the class implements a recursive add function which
/// accepts a part_of_admin_tree object and searches for the proper place to
/// put it. parentable_part_of_admin_tree implies part_of_admin_tree.

/// Please note that the $this->name field of any part_of_admin_tree must be
/// UNIQUE throughout the ENTIRE admin tree.

/// The $this->name field of an admin_setting object (which is *not* part_of_
/// admin_tree) must be unique on the respective admin_settingpage where it is
/// used.


/// MISCELLANEOUS STUFF (used by classes defined below) ///////////////////////
include_once($CFG->dirroot . '/backup/lib.php');

/// CLASS DEFINITIONS /////////////////////////////////////////////////////////

/**
 * Pseudointerface for anything appearing in the admin tree
 *
 * The pseudointerface that is implemented by anything that appears in the admin tree
 * block. It forces inheriting classes to define a method for checking user permissions
 * and methods for finding something in the admin tree.
 *
 * @author Vincenzo K. Marcovecchio
 * @package admin
 */
class part_of_admin_tree {

    /**
     * Finds a named part_of_admin_tree.
     *
     * Used to find a part_of_admin_tree. If a class only inherits part_of_admin_tree
     * and not parentable_part_of_admin_tree, then this function should only check if
     * $this->name matches $name. If it does, it should return a reference to $this,
     * otherwise, it should return a reference to NULL.
     *
     * If a class inherits parentable_part_of_admin_tree, this method should be called
     * recursively on all child objects (assuming, of course, the parent object's name
     * doesn't match the search criterion).
     *
     * @param string $name The internal name of the part_of_admin_tree we're searching for.
     * @return mixed An object reference or a NULL reference.
     */
    function &locate($name) { 
        trigger_error('Admin class does not implement method <strong>locate()</strong>', E_USER_WARNING); 
        return; 
    }
    
    /**
     * Verifies current user's access to this part_of_admin_tree.
     *
     * Used to check if the current user has access to this part of the admin tree or
     * not. If a class only inherits part_of_admin_tree and not parentable_part_of_admin_tree,
     * then this method is usually just a call to has_capability() in the site context.
     *
     * If a class inherits parentable_part_of_admin_tree, this method should return the
     * logical OR of the return of check_access() on all child objects.
     *
     * @return bool True if the user has access, false if she doesn't.
     */
    function check_access() { 
        trigger_error('Admin class does not implement method <strong>check_access()</strong>', E_USER_WARNING); 
        return; 
    }
    
    /**
     * Determines the path to $name in the admin tree.
     *
     * Used to determine the path to $name in the admin tree. If a class inherits only
     * part_of_admin_tree and not parentable_part_of_admin_tree, then this method should
     * check if $this->name matches $name. If it does, $name is pushed onto the $path
     * array (at the end), and $path should be returned. If it doesn't, NULL should be
     * returned.
     *
     * If a class inherits parentable_part_of_admin_tree, it should do the above, but not
     * return NULL on failure. Instead, it pushes $this->name onto $path, and then
     * recursively calls path() on its child objects. If any are non-NULL, it should
     * return $path (being certain that the last element of $path is equal to $name).
     * If they are all NULL, it returns NULL.
     *
     * @param string $name The internal name of the part_of_admin_tree we're searching for.
     * @param array $path Not used on external calls. Defaults to empty array.
     * @return mixed If found, an array containing the internal names of each part_of_admin_tree that leads to $name. If not found, NULL.
     */
    function path($name, $path = array()) { 
        trigger_error('Admin class does not implement method <strong>path()</strong>', E_USER_WARNING); 
        return; 
    }
}

/**
 * Pseudointerface implemented by any part_of_admin_tree that has children.
 *
 * The pseudointerface implemented by any part_of_admin_tree that can be a parent
 * to other part_of_admin_tree's. (For now, this only includes admin_category.) Apart
 * from ensuring part_of_admin_tree compliancy, it also ensures inheriting methods 
 * include an add method for adding other part_of_admin_tree objects as children.
 *
 * @author Vincenzo K. Marcovecchio
 * @package admin
 */
class parentable_part_of_admin_tree extends part_of_admin_tree {
    
    /**
     * Adds a part_of_admin_tree object to the admin tree.
     *
     * Used to add a part_of_admin_tree object to this object or a child of this
     * object. $something should only be added if $destinationname matches
     * $this->name. If it doesn't, add should be called on child objects that are
     * also parentable_part_of_admin_tree's.
     *
     * @param string $destinationname The internal name of the new parent for $something.
     * @param part_of_admin_tree &$something The object to be added.
     * @return bool True on success, false on failure.
     */
    function add($destinationname, &$something) { 
        trigger_error('Admin class does not implement method <strong>add()</strong>', E_USER_WARNING); 
        return; 
    }
    
}

/**
 * The object used to represent folders (a.k.a. categories) in the admin tree block.
 * 
 * Each admin_category object contains a number of part_of_admin_tree objects.
 *
 * @author Vincenzo K. Marcovecchio
 * @package admin
 */
class admin_category extends parentable_part_of_admin_tree {

    /**
     * @var mixed An array of part_of_admin_tree objects that are this object's children
     */
    var $children;
    
    /**
     * @var string An internal name for this category. Must be unique amongst ALL part_of_admin_tree objects
     */
    var $name;
    
    /**
     * @var string The displayed name for this category. Usually obtained through get_string()
     */
    var $visiblename;
    
    // constructor for an empty admin category
    // $name is the internal name of the category. it MUST be unique in the entire hierarchy
    // $visiblename is the displayed name of the category. use a get_string for this

    /**
     * Constructor for an empty admin category
     *
     * @param string $name The internal name for this category. Must be unique amongst ALL part_of_admin_tree objects
     * @param string $visiblename The displayed named for this category. Usually obtained through get_string()
     * @return mixed Returns the new object.
     */
    function admin_category($name, $visiblename) {
        $this->children = array();
        $this->name = $name;
        $this->visiblename = $visiblename;
    }
    
    /**
     * Finds the path to the part_of_admin_tree called $name.
     *
     * @param string $name The internal name that we're searching for.
     * @param array $path Used internally for recursive calls. Do not specify on external calls. Defaults to array().
     * @return mixed An array of internal names that leads to $name, or NULL if not found.
     */
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

    /**
     * Returns a reference to the part_of_admin_tree object with internal name $name.
     *
     * @param string $name The internal name of the object we want.
     * @return mixed A reference to the object with internal name $name if found, otherwise a reference to NULL.
     */
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

    /**
     * Adds a part_of_admin_tree to a child or grandchild (or great-grandchild, and so forth) of this object.
     *
     * @param string $destinationame The internal name of the immediate parent that we want for &$something.
     * @param mixed &$something A part_of_admin_tree object to be added.
     * @param int $precedence The precedence of &$something when displayed. Smaller numbers mean it'll be displayed higher up in the admin menu. Defaults to '', meaning "next available position".
     * @return bool True if successfully added, false if &$something is not a part_of_admin_tree or if $name is not found.
     */
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
        
        unset($entries);
        
        $entries = array_keys($this->children);
        
        foreach($entries as $entry) {
            $child =& $this->children[$entry];
            if (is_a($child, 'parentable_part_of_admin_tree')) {
                if ($child->add($destinationname, $something, $precedence)) {
                    return true;
                }
            }
        }
        
        return false;
        
    }
    
    /**
     * Checks if the user has access to anything in this category.
     *
     * @return bool True if the user has access to atleast one child in this category, false otherwise.
     */
    function check_access() {
    
        $return = false;
        foreach ($this->children as $child) {
            $return = $return || $child->check_access();
        }
    
        return $return;
    
    }
    
}

/**
 * Links external PHP pages into the admin tree.
 *
 * See detailed usage example at the top of this document (adminlib.php)
 *
 * @author Vincenzo K. Marcovecchio
 * @package admin
 */
class admin_externalpage extends part_of_admin_tree {

    /** 
     * @var string An internal name for this external page. Must be unique amongst ALL part_of_admin_tree objects
     */
    var $name;
    
    /**
     * @var string The displayed name for this external page. Usually obtained through get_string().
     */
    var $visiblename;
    
    /**
     * @var string The external URL that we should link to when someone requests this external page.
     */
    var $url;
    
    /**
     * @var string The role capability/permission a user must have to access this external page.
     */
    var $role;
    
    /**
     * Constructor for adding an external page into the admin tree.
     *
     * @param string $name The internal name for this external page. Must be unique amongst ALL part_of_admin_tree objects.
     * @param string $visiblename The displayed name for this external page. Usually obtained through get_string().
     * @param string $url The external URL that we should link to when someone requests this external page.
     * @param string $role The role capability/permission a user must have to access this external page. Defaults to 'moodle/legacy:admin'.
     */
    function admin_externalpage($name, $visiblename, $url, $role = 'moodle/legacy:admin') {
        $this->name = $name;
        $this->visiblename = $visiblename;
        $this->url = $url;
        $this->role = $role;
    }
    
    /**
     * Finds the path to the part_of_admin_tree called $name.
     *
     * @param string $name The internal name that we're searching for.
     * @param array $path Used internally for recursive calls. Do not specify on external calls. Defaults to array().
     * @return mixed An array of internal names that leads to $name, or NULL if not found.
     */
    function path($name, $path = array()) {
        if ($name == $this->name) {
            array_push($path, $this->name);
            return $path;
        } else {
            return NULL;
        }
    }
    
    /**
     * Returns a reference to the part_of_admin_tree object with internal name $name.
     *
     * @param string $name The internal name of the object we want.
     * @return mixed A reference to the object with internal name $name if found, otherwise a reference to NULL.
     */
    function &locate($name) {
        $return = ($this->name == $name ? $this : NULL);
        return $return;
    }
    
    /**
     * Determines if the current user has access to this external page based on $this->role.
     *
     * @uses CONTEXT_SYSTEM
     * @uses SITEID
     * @return bool True if user has access, false otherwise.
     */
    function check_access() {
        $context = get_context_instance(CONTEXT_SYSTEM, SITEID); 
        return has_capability($this->role, $context);
    }

}

/**
 * Used to group a number of admin_setting objects into a page and add them to the admin tree.
 *
 * @author Vincenzo K. Marcovecchio
 * @package admin
 */
class admin_settingpage extends part_of_admin_tree {

    /** 
     * @var string An internal name for this external page. Must be unique amongst ALL part_of_admin_tree objects
     */
    var $name;
    
    /**
     * @var string The displayed name for this external page. Usually obtained through get_string().
     */
    var $visiblename;
    /**
     * @var mixed An array of admin_setting objects that are part of this setting page.
     */
    var $settings;
    
    /**
     * @var string The role capability/permission a user must have to access this external page.
     */
    var $role;
    
    // see admin_category
    function path($name, $path = array()) {
        if ($name == $this->name) {
            array_push($path, $this->name);
            return $path;
        } else {
            return NULL;
        }
    }
    
    // see admin_category
    function &locate($name) {
        $return = ($this->name == $name ? $this : NULL);
        return $return;
    }
    
    // see admin_externalpage
    function admin_settingpage($name, $visiblename, $role = 'moodle/legacy:admin') {
        global $CFG;
        $this->settings = new stdClass();
        $this->name = $name;
        $this->visiblename = $visiblename;
        $this->role = $role;
    }
    
    // not the same as add for admin_category. adds an admin_setting to this admin_settingpage. settings appear (on the settingpage) in the order in which they're added
    // n.b. each admin_setting in an admin_settingpage must have a unique internal name
    // &$setting is the admin_setting object you want to add
    // returns true if successful, false if not (will fail if &$setting is an admin_setting or child thereof)
    function add(&$setting) {
        if (is_a($setting, 'admin_setting')) {
            $this->settings->{$setting->name} =& $setting;
            return true;
        }
        return false;
    }
    
    // see admin_externalpage
    function check_access() {
        $context = get_context_instance(CONTEXT_SYSTEM, SITEID); 
        return has_capability($this->role, $context);
    }
    
    // outputs this page as html in a table (suitable for inclusion in an admin pagetype)
    // returns a string of the html
    function output_html() {
        $return = '<table class="generaltable" width="100%" border="0" align="center" cellpadding="5" cellspacing="1">' . "\n";
        foreach($this->settings as $setting) {
          $return .= $setting->output_html();
        }
        $return .= '</table>';
        return $return;
    }

    // writes settings (the ones that have been added to this admin_settingpage) to the database, or wherever else they're supposed to be written to
    // -- calls write_setting() to each child setting, sending it only the data that matches each setting's internal name
    // $data should be the result from data_submitted()
    // returns an empty string if everything went well, otherwise returns a printable error string (that's language-specific)
    function write_settings($data) {
        $return = '';
        foreach($this->settings as $setting) {
            if (isset($data['s_' . $setting->name])) {
                $return .= $setting->write_setting($data['s_' . $setting->name]);
            } else {
                $return .= $setting->write_setting('');
            }
        }
        return $return;
    }

}


// read & write happens at this level; no authentication
class admin_setting {

    var $name;
    var $visiblename;
    var $description;
    var $defaultsetting;

    function admin_setting($name, $visiblename, $description, $defaultsetting) {
        $this->name = $name;
        $this->visiblename = $visiblename;
        $this->description = $description;
        $this->defaultsetting = $defaultsetting;
    }
    
    function get_setting() {
        return NULL; // has to be overridden
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

    function admin_setting_configtext($name, $visiblename, $description, $defaultsetting, $paramtype) {
        $this->paramtype = $paramtype;
        parent::admin_setting($name, $visiblename, $description, $defaultsetting);
    }

    function get_setting() {
        global $CFG;
        return (isset($CFG->{$this->name}) ? $CFG->{$this->name} : NULL);
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

    function admin_setting_configcheckbox($name, $visiblename, $description, $defaultsetting) {
        parent::admin_setting($name, $visiblename, $description, $defaultsetting);
    }

    function get_setting() {
        global $CFG;
        return (isset($CFG->{$this->name}) ? $CFG->{$this->name} : NULL);
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
    
    function admin_setting_configselect($name, $visiblename, $description, $defaultsetting, $choices) {
        $this->choices = $choices;
        parent::admin_setting($name, $visiblename, $description, $defaultsetting);
    }

    function get_setting() {
        global $CFG;
        return (isset($CFG->{$this->name}) ? $CFG->{$this->name} : NULL);
    }
    
    function write_setting($data) {
         // check that what we got was in the original choices
         // or that the data is the default setting - needed during install when choices can not be constructed yet
         if ($data != $this->defaultsetting and ! in_array($data, array_keys($this->choices))) {
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
    var $defaultsetting2;

    function admin_setting_configtime($hoursname, $minutesname, $visiblename, $description, $defaultsetting) {
        $this->name2 = $minutesname;
        $this->choices = array();
        for ($i = 0; $i < 24; $i++) {
            $this->choices[$i] = $i;
        }
        $this->choices2 = array();
        for ($i = 0; $i < 60; $i += 5) {
            $this->choices2[$i] = $i;
        }
        parent::admin_setting($hoursname, $visiblename, $description, $defaultsetting);
    }

    function get_setting() {
        global $CFG;
        return (isset($CFG->{$this->name}) && isset($CFG->{$this->name2}) ? array($CFG->{$this->name}, $CFG->{$this->name2}) : NULL);
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
        if (!is_array($setvalue)) {
            $setvalue = array(0,0);
        }
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

    function admin_setting_configmultiselect($name, $visiblename, $description, $defaultsetting, $choices) {
        parent::admin_setting_configselect($name, $visiblename, $description, $defaultsetting, $choices);
    }

    function get_setting() {
        global $CFG;
        return (isset($CFG->{$this->name}) ? explode(',', $CFG->{$this->name}) : NULL);;
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
        $currentsetting = $this->get_setting();
        if (!is_array($currentsetting)) {
            $currentsetting = array();
        }
        $return = '<tr><td width="100" align="right" valign="top">' . $this->visiblename . '</td><td align="left"><select name="s_' . $this->name .'[]" size="10" multiple="multiple">';
        foreach ($this->choices as $key => $value) {
            $return .= '<option value="' . $key . '"' . (in_array($key,$currentsetting) ? ' selected="selected"' : '') . '>' . $value . '</option>';
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
        parent::admin_setting($name, $visiblename, $description, 0);
    }

    function write_setting($data) {
        global $SESSION;
        unset($SESSION->cal_courses_shown);
        parent::write_setting($data);
    }
}

class admin_setting_sitesetselect extends admin_setting_configselect {

    var $id;

    function admin_setting_sitesetselect($name, $visiblename, $description, $defaultsetting, $choices) {

        $site = get_site();    
        $this->id = $site->id;
        parent::admin_setting_configselect($name, $visiblename, $description, $defaultsetting, $choices);
    
    }
    
    function get_setting() {
        $site = get_site();
        return (isset($site->{$this->name}) ? $site->{$this->name} : NULL);
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
        parent::admin_setting_configselect($name, $visiblename, $description, '', $choices);
    }
    
    function get_setting() {
        global $CFG;
        return (isset($CFG->{$this->name}) ? explode(',', $CFG->{$this->name}) : NULL);
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
        if (!is_array($currentsetting)) {
            $currentsetting = array();
        }
        for ($i = 0; $i < count($this->choices) - 1; $i++) {
            if (!isset($currentsetting[$i])) {
                $currentsetting[$i] = 0;
            }
        }
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

    function admin_setting_sitesetcheckbox($name, $visiblename, $description, $defaultsetting) {

        $site = get_site();    
        $this->id = $site->id;
        parent::admin_setting_configcheckbox($name, $visiblename, $description, $defaultsetting);
    
    }
    
    function get_setting() {
        $site = get_site();
        return (isset($site->{$this->name}) ? $site->{$this->name} : NULL);
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

    function admin_setting_sitesettext($name, $visiblename, $description, $defaultsetting, $paramtype) {

        $site = get_site();    
        $this->id = $site->id;
        parent::admin_setting_configtext($name, $visiblename, $description, $defaultsetting, $paramtype);
    
    }
    
    function get_setting() {
        $site = get_site();
        return (isset($site->{$this->name}) ? $site->{$this->name} : NULL);
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
        parent::admin_setting($name, $visiblename, $description, '');
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
        return (isset($site->{$this->name}) ? $site->{$this->name} : NULL);
    
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
        if (isset($CFG->editorfontlist)) {
            $items = explode(';', $CFG->editorfontlist);
            $this->items = array();
            foreach ($items as $item) {
              $item = explode(':', $item);
              $this->items[$item[0]] = $item[1];
            }
        } else {
            $items = NULL;
        }
        unset($defaults);
        $defaults = array('k0' => 'Trebuchet',
                          'v0' => 'Trebuchet MS,Verdana,Arial,Helvetica,sans-serif',
                          'k1' => 'Arial',
                          'v1' => 'arial,helvetica,sans-serif',
                          'k2' => 'Courier New',
                          'v2' => 'courier new,courier,monospace',
                          'k3' => 'Georgia',
                          'v3' => 'georgia,times new roman,times,serif',
                          'k4' => 'Tahoma',
                          'v4' => 'tahoma,arial,helvetica,sans-serif',
                          'k5' => 'Times New Roman',
                          'v5' => 'times new roman,times,serif',
                          'k6' => 'Verdana',
                          'v6' => 'verdana,arial,helvetica,sans-serif',
                          'k7' => 'Impact',
                          'v7' => 'impact',
                          'k8' => 'Wingdings',
                          'v8' => 'wingdings');
        parent::admin_setting($name, $visiblename, $description, $defaults);
    }
    
    function get_setting() {
        return $this->items;
    }
    
    function write_setting($data) {
    
        // there miiight be an easier way to do this :)
        // if this is changed, make sure the $defaults array above is modified so that this
        // function processes it correctly
        
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
                $result .= clean_param($keys[$i],PARAM_NOTAGS) . ':' . clean_param($values[$i], PARAM_NOTAGS) . ';';
            }
        }
        
        $result = substr($result, 0, -1); // trim the last semicolon
        
        return (set_config($this->name, $result) ? '' : get_string('errorsetting', 'admin') . $this->visiblename . '<br />');
    }
    
    function output_html() {
        $return = '<tr><td width="100" align="right" valign="top">' . $this->visiblename . '</td><td align="left">';
        $count = 0;
        $currentsetting = $this->items;
        if (!is_array($currentsetting)) {
            $currentsetting = NULL;
        }
        foreach ($currentsetting as $key => $value) {
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
    
        parent::admin_setting_configselect($name, $visiblename, $description, '', $choices);
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
        $this->defaultsetting = array();
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
        return (isset($CFG->{$this->name}) ? explode(' ', $CFG->{$this->name}) : NULL);
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
        if (!is_array($currentsetting)) {
            $currentsetting = array();
        }
        
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

    function admin_setting_backupselect($name, $visiblename, $description, $default, $choices) {
        parent::admin_setting_configselect($name, $visiblename, $description, $default, $choices);
    }

    function get_setting() {
        $backup_config =  backup_get_config();
        return (isset($backup_config->{$this->name}) ? $backup_config->{$this->name} : NULL);
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
        parent::admin_setting_configtext($name, $visiblename, $description, '', PARAM_PATH);
    }
    
    function get_setting() {
        $backup_config =  backup_get_config();
        return (isset($backup_config->{$this->name}) ? $backup_config->{$this->name} : NULL);
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

    function admin_setting_backupcheckbox($name, $visiblename, $description, $default) {
        parent::admin_setting_configcheckbox($name, $visiblename, $description, $default);
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
        return (isset($backup_config->{$this->name}) ? $backup_config->{$this->name} : NULL);
    }

}

class admin_setting_special_backuptime extends admin_setting_configtime {

    function admin_setting_special_backuptime() {
        $name = 'backup_sche_hour';
        $name2 = 'backup_sche_minute';
        $visiblename = get_string('executeat');
        $description = get_string('backupexecuteathelp');
        $default = array('h' => 0, 'm' => 0);
        parent::admin_setting_configtime($name, $name2, $visiblename, $description, $default);
    }
    
    function get_setting() {
        $backup_config =  backup_get_config();
        return (isset($backup_config->{$this->name}) && isset($backup_config->{$this->name}) ? array($backup_config->{$this->name}, $backup_config->{$this->name2}) : NULL);
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
        parent::admin_setting($name, $visiblename, $description, array());
    }
    
    function get_setting() {
        $backup_config =  backup_get_config();
        return (isset($backup_config->{$this->name}) ? $backup_config->{$this->name} : NULL);
    }
    
    function output_html() {
    
        $currentsetting = $this->get_setting();
        if ($currentsetting === NULL) {
            $currentsetting = '0000000';
        }
        
        return '<tr><td width="100" align="right" valign="top">' . $this->visiblename . '</td><td align="left">' .
        '<table><tr><td><div align="center">&nbsp;&nbsp;' . get_string('sunday', 'calendar') . '&nbsp;&nbsp;</div></td><td><div align="center">&nbsp;&nbsp;' . 
        get_string('monday', 'calendar') . '&nbsp;&nbsp;</div></td><td><div align="center">&nbsp;&nbsp;' . get_string('tuesday', 'calendar') . '&nbsp;&nbsp;</div></td><td><div align="center">&nbsp;&nbsp;' .
        get_string('wednesday', 'calendar') . '&nbsp;&nbsp;</div></td><td><div align="center">&nbsp;&nbsp;' . get_string('thursday', 'calendar') . '&nbsp;&nbsp;</div></td><td><div align="center">&nbsp;&nbsp;' .
        get_string('friday', 'calendar') . '&nbsp;&nbsp;</div></td><td><div align="center">&nbsp;&nbsp;' . get_string('saturday', 'calendar') . '&nbsp;&nbsp;</div></td></tr><tr>' .
        '<td><div align="center"><input type="checkbox" name="s_'. $this->name .'[u]" value="1" ' . (substr($currentsetting,0,1) == '1' ? 'checked="checked"' : '') . ' /></div></td>' . 
        '<td><div align="center"><input type="checkbox" name="s_'. $this->name .'[m]" value="1" ' . (substr($currentsetting,1,1) == '1' ? 'checked="checked"' : '') . ' /></div></td>' . 
        '<td><div align="center"><input type="checkbox" name="s_'. $this->name .'[t]" value="1" ' . (substr($currentsetting,2,1) == '1' ? 'checked="checked"' : '') . ' /></div></td>' . 
        '<td><div align="center"><input type="checkbox" name="s_'. $this->name .'[w]" value="1" ' . (substr($currentsetting,3,1) == '1' ? 'checked="checked"' : '') . ' /></div></td>' . 
        '<td><div align="center"><input type="checkbox" name="s_'. $this->name .'[r]" value="1" ' . (substr($currentsetting,4,1) == '1' ? 'checked="checked"' : '') . ' /></div></td>' . 
        '<td><div align="center"><input type="checkbox" name="s_'. $this->name .'[f]" value="1" ' . (substr($currentsetting,5,1) == '1' ? 'checked="checked"' : '') . ' /></div></td>' . 
        '<td><div align="center"><input type="checkbox" name="s_'. $this->name .'[s]" value="1" ' . (substr($currentsetting,6,1) == '1' ? 'checked="checked"' : '') . ' /></div></td>' . 
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
        parent::admin_setting_configcheckbox($name, $visiblename, $description, '');
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
        parent::admin_setting($name, $visiblename, $description, array('u' => 1, 's' => 1));
    }

    function get_setting() {
        global $CFG;
        if (isset($CFG->{$this->name})) {
            $setting = intval($CFG->{$this->name});
            return array('u' => $setting & 1, 'm' => $setting & 2, 't' => $setting & 4, 'w' => $setting & 8, 'r' => $setting & 16, 'f' => $setting & 32, 's' => $setting & 64);
        } else {
            return NULL;
        }
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

        $currentsetting = $this->get_setting();
        if (!is_array($currentsetting)) {
            $currentsetting = array('u' => 0, 'm' => 0, 't' => 0, 'w' => 0, 'r' => 0, 'f' => 0, 's' => 0);
        }
        return '<tr><td width="100" align="right" valign="top">' . $this->visiblename . '</td><td align="left">' .
        '<table><tr><td><div align="center">&nbsp;&nbsp;' . get_string('sunday', 'calendar') . '&nbsp;&nbsp;</div></td><td><div align="center">&nbsp;&nbsp;' . 
        get_string('monday', 'calendar') . '&nbsp;&nbsp;</div></td><td><div align="center">&nbsp;&nbsp;' . get_string('tuesday', 'calendar') . '&nbsp;&nbsp;</div></td><td><div align="center">&nbsp;&nbsp;' .
        get_string('wednesday', 'calendar') . '&nbsp;&nbsp;</div></td><td><div align="center">&nbsp;&nbsp;' . get_string('thursday', 'calendar') . '&nbsp;&nbsp;</div></td><td><div align="center">&nbsp;&nbsp;' .
        get_string('friday', 'calendar') . '&nbsp;&nbsp;</div></td><td><div align="center">&nbsp;&nbsp;' . get_string('saturday', 'calendar') . '&nbsp;&nbsp;</div></td></tr><tr>' .
        '<td><div align="center"><input type="checkbox" name="s_'. $this->name .'[u]" value="1" ' . ($currentsetting['u'] ? 'checked="checked"' : '') . ' /></div></td>' . 
        '<td><div align="center"><input type="checkbox" name="s_'. $this->name .'[m]" value="1" ' . ($currentsetting['m'] ? 'checked="checked"' : '') . ' /></div></td>' . 
        '<td><div align="center"><input type="checkbox" name="s_'. $this->name .'[t]" value="1" ' . ($currentsetting['t'] ? 'checked="checked"' : '') . ' /></div></td>' . 
        '<td><div align="center"><input type="checkbox" name="s_'. $this->name .'[w]" value="1" ' . ($currentsetting['w'] ? 'checked="checked"' : '') . ' /></div></td>' . 
        '<td><div align="center"><input type="checkbox" name="s_'. $this->name .'[r]" value="1" ' . ($currentsetting['r'] ? 'checked="checked"' : '') . ' /></div></td>' . 
        '<td><div align="center"><input type="checkbox" name="s_'. $this->name .'[f]" value="1" ' . ($currentsetting['f'] ? 'checked="checked"' : '') . ' /></div></td>' . 
        '<td><div align="center"><input type="checkbox" name="s_'. $this->name .'[s]" value="1" ' . ($currentsetting['s'] ? 'checked="checked"' : '') . ' /></div></td>' . 
        '</tr></table>' .                                                 
        '</td></tr><tr><td>&nbsp;</td><td align="left">' . $this->description . '</td></tr>';
    
    }

}


class admin_setting_special_perfdebug extends admin_setting_configcheckbox {

    function admin_setting_special_perfdebug() {
        $name = 'perfdebug';
        $visiblename = get_string('perfdebug', 'admin');
        $description = get_string('configperfdebug', 'admin');
        parent::admin_setting_configcheckbox($name, $visiblename, $description, '');
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

    global $CFG, $ADMIN, $PAGE, $USER;
    
    require_once($CFG->libdir . '/blocklib.php');
    require_once($CFG->dirroot . '/' . $CFG->admin . '/pagelib.php');
    
    define('TEMPORARY_ADMIN_PAGE_ID',26);

    define('BLOCK_L_MIN_WIDTH',160);
    define('BLOCK_L_MAX_WIDTH',210);

    $pagetype = PAGE_ADMIN;               
    $pageclass = 'page_admin';            
    page_map_class($pagetype, $pageclass);

    $PAGE = page_create_object($pagetype,TEMPORARY_ADMIN_PAGE_ID);

    $PAGE->init_full($section);

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
        $USER->adminediting = false;
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

// n.b. this function unconditionally applies default settings
function apply_default_settings(&$node) {

    global $CFG;

    if (is_a($node, 'admin_category')) {
        $entries = array_keys($node->children);
        foreach ($entries as $entry) {
            apply_default_settings($node->children[$entry]);
        }
        return;
    } 

    if (is_a($node, 'admin_settingpage')) { 
        foreach ($node->settings as $setting) {
                $CFG->{$setting->name} = $setting->defaultsetting;
                $setting->write_setting($setting->defaultsetting);
            unset($setting); // needed to prevent odd (imho) reference behaviour
                             // see http://www.php.net/manual/en/language.references.whatdo.php#AEN6399
        }
        return;
    }

    return;

}

// n.b. this function unconditionally applies default settings
function apply_default_exception_settings($defaults) {

    global $CFG;

    foreach($defaults as $key => $value) {
            $CFG->$key = $value;
            set_config($key, $value);
    }

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