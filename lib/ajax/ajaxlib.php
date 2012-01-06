<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.


/**
 * Library functions to facilitate the use of ajax JavaScript in Moodle.
 *
 * @package   moodlecore
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * You need to call this function if you wish to use the set_user_preference
 * method in javascript_static.php, to white-list the preference you want to update
 * from JavaScript, and to specify the type of cleaning you expect to be done on
 * values.
 *
 * @param string $name the name of the user_perference we should allow to be
 *      updated by remote calls.
 * @param integer $paramtype one of the PARAM_{TYPE} constants, user to clean
 *      submitted values before set_user_preference is called.
 * @return void
 */
function user_preference_allow_ajax_update($name, $paramtype) {
    global $USER, $PAGE;

    // Make sure that the required JavaScript libraries are loaded.
    $PAGE->requires->yui2_lib('connection');

    // Record in the session that this user_preference is allowed to updated remotely.
    $USER->ajax_updatable_user_prefs[$name] = $paramtype;
}

/**
 * Returns whether ajax is enabled/allowed or not.
 * @param array $browsers optional list of alowed browsers, empty means use default list
 * @return bool
 */
function ajaxenabled(array $browsers = null) {
    global $CFG, $USER;

    if (!empty($browsers)) {
        $valid = false;
        foreach ($browsers as $brand => $version) {
            if (check_browser_version($brand, $version)) {
                $valid = true;
            }
        }

        if (!$valid) {
            return false;
        }
    }

    $ie = check_browser_version('MSIE', 6.0);
    $ff = check_browser_version('Gecko', 20051106);
    $op = check_browser_version('Opera', 9.0);
    $sa = check_browser_version('Safari', 412);
    $ch = check_browser_version('Chrome', 6);

    if (!$ie && !$ff && !$op && !$sa && !$ch) {
        /** @see http://en.wikipedia.org/wiki/User_agent */
        // Gecko build 20051107 is what is in Firefox 1.5.
        // We still have issues with AJAX in other browsers.
        return false;
    }

    if (!empty($CFG->enableajax) && (!empty($USER->ajax) || !isloggedin())) {
        return true;
    } else {
        return false;
    }
}



// ==============================================================================
// TODO: replace this with something more up-to-date with our coding standards

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
        global $CFG, $USER, $OUTPUT, $COURSE, $DB;

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
        $output .= "    main.portal.id = ".$courseid.";\n";
        $output .= "    main.portal.blocks = new Array(".$blocksoutput.");\n";
        $output .= "    main.portal.strings['courseformat']='".$COURSE->format."';\n";
        $output .= "    main.portal.strings['wwwroot']='".$CFG->wwwroot."';\n";
        $output .= "    main.portal.strings['marker']='".addslashes_js(get_string('markthistopic', '', '_var_'))."';\n";
        $output .= "    main.portal.strings['marked']='".addslashes_js(get_string('markedthistopic', '', '_var_'))."';\n";
        $output .= "    main.portal.numsections = ".$COURSE->numsections.";\n";
        $output .= "    main.portal.lastsection = ".$DB->get_field_sql("SELECT MAX(section) FROM {course_sections} WHERE course = ?", array($courseid)).";\n"; // needed for orphaned activities in unavailable sections
        $output .= "    main.portal.strings['hide']='".addslashes_js(get_string('hide'))."';\n";
        $output .= "    main.portal.strings['hidesection']='".addslashes_js(get_string('hidesection', '', '_var_'))."';\n";
        $output .= "    main.portal.strings['show']='".addslashes_js(get_string('show'))."';\n";
        $output .= "    main.portal.strings['delete']='".addslashes_js(get_string('delete'))."';\n";
        $output .= "    main.portal.strings['move']='".addslashes_js(get_string('move'))."';\n";
        $output .= "    main.portal.strings['movesection']='".addslashes_js(get_string('movesection', '', '_var_'))."';\n";
        $output .= "    main.portal.strings['moveleft']='".addslashes_js(get_string('moveleft'))."';\n";
        $output .= "    main.portal.strings['moveright']='".addslashes_js(get_string('moveright'))."';\n";
        $output .= "    main.portal.strings['update']='".addslashes_js(get_string('update'))."';\n";
        $output .= "    main.portal.strings['groupsnone']='".addslashes_js(get_string('clicktochangeinbrackets', 'moodle', get_string('groupsnone')))."';\n";
        $output .= "    main.portal.strings['groupsseparate']='".addslashes_js(get_string('clicktochangeinbrackets', 'moodle', get_string('groupsseparate')))."';\n";
        $output .= "    main.portal.strings['groupsvisible']='".addslashes_js(get_string('clicktochangeinbrackets', 'moodle', get_string('groupsvisible')))."';\n";
        $output .= "    main.portal.strings['deletecheck']='".addslashes_js(get_string('deletecheckfull','','_var_'))."';\n";
        $output .= "    main.portal.strings['resource']='".addslashes_js(get_string('resource'))."';\n";
        $output .= "    main.portal.strings['activity']='".addslashes_js(get_string('activity'))."';\n";
        $output .= "    main.portal.strings['sesskey']='".sesskey()."';\n";
        foreach (array_keys(get_plugin_list('mod')) as $modtype) {
            $output .= "    main.portal.strings['modtype_".$modtype."']='".addslashes_js(get_string('pluginname', 'mod_'.$modtype))."';\n";
        }
        $output .= "    main.portal.icons['spacerimg']='".$OUTPUT->pix_url('spacer')."';\n";
        $output .= "    main.portal.icons['marker']='".$OUTPUT->pix_url('i/marker')."';\n";
        $output .= "    main.portal.icons['ihide']='".$OUTPUT->pix_url('i/hide')."';\n";
        $output .= "    main.portal.icons['move_2d']='".$OUTPUT->pix_url('i/move_2d')."';\n";
        $output .= "    main.portal.icons['show']='".$OUTPUT->pix_url('t/show')."';\n";
        $output .= "    main.portal.icons['hide']='".$OUTPUT->pix_url('t/hide')."';\n";
        $output .= "    main.portal.icons['delete']='".$OUTPUT->pix_url('t/delete')."';\n";
        $output .= "    main.portal.icons['groupn']='".$OUTPUT->pix_url('t/groupn')."';\n";
        $output .= "    main.portal.icons['groups']='".$OUTPUT->pix_url('t/groups')."';\n";
        $output .= "    main.portal.icons['groupv']='".$OUTPUT->pix_url('t/groupv')."';\n";
        if (right_to_left()) {
            $output .= "    main.portal.icons['backwards']='".$OUTPUT->pix_url('t/right')."';\n";
            $output .= "    main.portal.icons['forwards']='".$OUTPUT->pix_url('t/left')."';\n";
        } else {
            $output .= "    main.portal.icons['backwards']='".$OUTPUT->pix_url('t/left')."';\n";
            $output .= "    main.portal.icons['forwards']='".$OUTPUT->pix_url('t/right')."';\n";
        }

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
