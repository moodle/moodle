<?php
/**
 * Library functions for using AJAX with Moodle.
 */



/**
 * Used to include JavaScript libraries.
 *
 * When the $lib parameter is given, the function will add $lib to an
 * internal list of libraries. When called without any parameters, it will
 * return the html that is needed to load the JavaScript libraries in that
 * list. Libraries that are included more than once will still only get loaded
 * once, so this works like require_once() in PHP.
 *
 * @param $lib - string or array of strings
 *               string(s) should be the shortname for the library or the
 *               full path to the library file.
 *        $add - 1 to return the libraries in lib
 *               not already loaded.
 * @return string or false or nothing.
 */
function require_js($lib='', $add = 0) {
    global $CFG;
    static $loadlibs = array();

    if (!ajaxenabled()) {
        return false;
    }

    if (!empty($lib)) {
        // Add the lib to the list of libs to be loaded, if it isn't already
        // in the list.
        // if (is_array($lib)) {
            // array_map('require_js', $lib);
        // } else {
        foreach ($lib as $lib1)  {
            // $libpath = ajax_get_lib($lib);
            $libpath = ajax_get_lib($lib1);
            if (array_search($libpath, $loadlibs) === false) {
                // array_push($loadlibs, $libpath);
                // array_push($addedlibs, $libpath);
                $loadlibs[] = $libpath;
                $addedlibs[] = $libpath;
            }
        }
    }
    // } else {
    if (empty($lib) || ((!empty($addedlibs)) && ($add != 0))) {
        // Return the html needed to load the JavaScript files defined in
        // our list of libs to be loaded.
        $output = '';

        $thelibs = (!empty($addedlibs)) ? $addedlibs : $loadlibs;
        // foreach ($loadlibs as $loadlib) {
        foreach ($thelibs as $loadlib) {
            $output .= '<script type="text/javascript" ';
            $output .= " src=\"$loadlib\"></script>\n";
            if ($loadlib == $CFG->wwwroot.'/lib/yui/logger/logger-min.js') {
                // Special case, we need the CSS too.
                $output .= '<link type="text/css" rel="stylesheet" ';
                $output .= " href=\"{$CFG->wwwroot}/lib/yui/logger/assets/logger.css\" />\n";
            }
        }
        return $output;
    }
}


/**
 * Get the path to a JavaScript library.
 * @param $libname - the name of the library whose path we need.
 * @return string
 */
function ajax_get_lib($libname) {

    global $CFG;
    $libpath = '';

    $translatelist = array(
            'yui_yahoo' => '/lib/yui/yahoo/yahoo-min.js',
			'yui_animation' => '/lib/yui/animation/animation-min.js',
			'yui_autocomplete' => '/lib/yui/autocomplete/autocomplete-min.js',
			'yui_calendar' => '/lib/yui/calendar/calendar-min.js',
			'yui_connection' => '/lib/yui/connection/connection-min.js',
			'yui_container' => '/lib/yui/container/container-min.js',
            'yui_dom' => '/lib/yui/dom/dom-min.js',
			'yui_dom-event' => '/lib/yui/yahoo-dom-event/yahoo-dom-event.js',
			'yui_dragdrop' => '/lib/yui/dragdrop/dragdrop-min.js',
            'yui_event' => '/lib/yui/event/event-min.js',
            'yui_logger' => '/lib/yui/logger/logger-min.js',
			'yui_menu' => '/lib/yui/menu/menu-min.js',
			'yui_tabview' => '/lib/yui/tabview/tabview-min.js',
			'yui_treeview' => '/lib/yui/treeview/treeview-min.js',
			'yui_slider' => '/lib/yui/slider/slider-min.js',
			'yui_utilities' => '/lib/yui/utilities/utilities.js',
            'ajaxcourse_blocks' => '/lib/ajax/block_classes.js',
            'ajaxcourse_sections' => '/lib/ajax/section_classes.js',
            'ajaxcourse' => '/lib/ajax/ajaxcourse.js'
            );

    if (array_key_exists($libname, $translatelist)) {
        $libpath = $CFG->wwwroot . $translatelist[$libname];
    } else {
        $libpath = $libname;
    }

    $testpath = str_replace($CFG->wwwroot, $CFG->dirroot, $libpath);
    if (!file_exists($testpath)) {        
        error('require_js: '.$libpath.' - file not found.');
    }

    return $libpath;
}


/**
 * Returns whether ajax is enabled/allowed or not.
 */
function ajaxenabled() {

    global $CFG, $USER;

    if (!check_browser_version('MSIE', 6.0)
                && !check_browser_version('Gecko', 20051111)) {
		// Gecko build 20051111 is what is in Firefox 1.5.
        // We still have issues with AJAX in other browsers.
        return false;
    }

    if (!empty($CFG->enableajax) && (!empty($USER->ajax) || !isloggedin())) {
        return true;
    } else {
        return false;
    }
}


/**
 * Used to create view of document to be passed to JavaScript on pageload.
 * We use this class to pass data from PHP to JavaScript.
 */
class jsportal {

    var $currentblocksection = null;
    var $blocks = array();


    /**
     * Takes id of block and adds it
     */
    function block_add($id, $hidden=false){
        $hidden_binary = 0;

        if ($hidden) {
            $hidden_binary = 1;
        }
        $this->blocks[count($this->blocks)] = array($this->currentblocksection, $id, $hidden_binary);
    }


    /**
     * Prints the JavaScript code needed to set up AJAX for the course.
     */
    function print_javascript($courseid, $return=false) {
        global $CFG, $USER;

        $blocksoutput = $output = '';
        for ($i=0; $i<count($this->blocks); $i++) {
            $blocksoutput .= "['".$this->blocks[$i][0]."',
                             '".$this->blocks[$i][1]."',
                             '".$this->blocks[$i][2]."']";

            if ($i != (count($this->blocks) - 1)) {
                $blocksoutput .= ',';
            }
        }
        $output .= "<script type=\"text/javascript\">\n";
        $output .= " 	main.portal.id = ".$courseid.";\n";
        $output .= "    main.portal.blocks = new Array(".$blocksoutput.");\n";
        $output .= "    main.portal.strings['wwwroot']='".$CFG->wwwroot."';\n";
        $output .= "    main.portal.strings['pixpath']='".$CFG->pixpath."';\n";
        $output .= "    main.portal.strings['move']='".get_string('move')."';\n";
        $output .= "    main.portal.strings['moveleft']='".get_string('moveleft')."';\n";
        $output .= "    main.portal.strings['moveright']='".get_string('moveright')."';\n";
        $output .= "    main.portal.strings['update']='".get_string('update')."';\n";
        $output .= "    main.portal.strings['groupsnone']='".get_string('groupsnone')."';\n";
        $output .= "    main.portal.strings['groupsseparate']='".get_string('groupsseparate')."';\n";
        $output .= "    main.portal.strings['groupsvisible']='".get_string('groupsvisible')."';\n";
        $output .= "    main.portal.strings['clicktochange']='".get_string('clicktochange')."';\n";
        $output .= "    main.portal.strings['deletecheck']='".get_string('deletecheck','','_var_')."';\n";
        $output .= "    main.portal.strings['resource']='".get_string('resource')."';\n";
        $output .= "    main.portal.strings['activity']='".get_string('activity')."';\n";
        $output .= "    main.portal.strings['sesskey']='".$USER->sesskey."';\n";
        $output .= "    onloadobj.load();\n";
        $output .= "    main.process_blocks();\n";
        $output .= "</script>";
        if ($return) {
            return $output;
        } else {
            echo $output;
        }
    }

}

?>
