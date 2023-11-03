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

/** LearnerScript Reports
 * A Moodle block for creating customizable reports
 * @package blocks
 * @author: eAbyas Info Solutions
 * @date: 2017
 */
if (!defined('MOODLE_INTERNAL')) {
    die(get_string('nodirectaccess','block_learnerscript'));    ///  It must be included from a Moodle page
}

require_once($CFG->libdir . '/formslib.php');
require_once($CFG->libdir . '/accesslib.php');
require_once($CFG->dirroot . '/blocks/learnerscript/lib.php');

class roleincourse_form extends moodleform {

    function definition() {
        global $DB, $USER, $CFG;

        $mform = & $this->_form;
        $cid = $this->_customdata['cid'];
        $pluginclass = $this->_customdata['pluginclass'];
        $reportclass = $this->_customdata['reportclass'];
        $comp = $this->_customdata['comp'];
        $compclass = $this->_customdata['compclass'];

        if (!empty($reportclass->componentdata[$comp]['elements'])) {
            foreach ($reportclass->componentdata[$comp]['elements'] as $p) {
                    if($p['id'] == $cid) {
                        $contextlevel= $p['formdata']->contextlevel;
                    }
            }
        }
        $mform->addElement('header', 'crformheader', get_string('roleincourse', 'block_learnerscript'), '');

        $levels = context_helper::get_all_levels(); 

        $validlevels = array_filter($levels, function($level){
            //We don't want to handle reports at this level
        if($level != CONTEXT_BLOCK && $level != CONTEXT_MODULE && $level != CONTEXT_USER){
            return true;
            } 
        }, ARRAY_FILTER_USE_KEY);

        foreach ($validlevels as $level => $classname) { 
            $allcontextlevels[$level] = context_helper::get_level_name($level);
        }
        $reportid = $this->_customdata['pluginclass']->report->id;
        $mform->addElement('select', 'contextlevel', get_string('contextid', 'block_learnerscript'), $allcontextlevels, 
            ['onchange' => "(function(e){ require(['block_learnerscript/helper'], function(helper){ console.log(e); helper.rolesforcontext(e.target.value, $reportid); }) })(event); "]);
    
        if($cid){
            $userroles = get_roles_in_context($contextlevel);
        } else {
            $userroles = get_roles_in_context(CONTEXT_SYSTEM);

        }
        $mform->addElement('select', 'roleid', get_string('roles'), $userroles);

        // buttons
        $this->add_action_buttons(true, get_string('add'));
    }

}
