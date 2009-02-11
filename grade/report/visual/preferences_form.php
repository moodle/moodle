<?php
///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.org                                            //
//                                                                       //
// Copyright (C) 1999 onwards  Martin Dougiamas  http://moodle.com       //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

/**
 * FIle with class defition for visual_report_preferences_form.
 * Used by preferences.php
 * @package gradebook
 */

require_once($CFG->libdir.'/formslib.php');
/**
 * Moodle form to be used to set user preferences for report/visual
 * gradebook plugin.
 * @uses moodleform
 */
class visual_report_preferences_form extends moodleform {

    /**
     * Fourm definition.
     */
    public function definition() {
        global $USER, $CFG;
        
        $mform    =& $this->_form;
        $course   = $this->_customdata['course'];
	
        $context = get_context_instance(CONTEXT_COURSE, $course->id);
        $systemcontext = get_context_instance(CONTEXT_SYSTEM);
        $stryes                 = get_string('yes');
        $strno                  = get_string('no');

        $checkbox_default = array(GRADE_REPORT_PREFERENCE_DEFAULT => '*default*', 0 => $strno, 1 => $stryes);
	
        $advanced = array();
        $preferences = array();
	
        /*$preferences['prefgeneral'] = array(
                            'aggregationview'     => array(GRADE_REPORT_PREFERENCE_DEFAULT => '*default*',
                                                         GRADE_REPORT_AGGREGATION_VIEW_FULL => get_string('fullmode', 'grades'),
                                                         GRADE_REPORT_AGGREGATION_VIEW_AGGREGATES_ONLY => get_string('aggregatesonly', 'grades'),
                                                         GRADE_REPORT_AGGREGATION_VIEW_GRADES_ONLY => get_string('gradesonly', 'grades')
                            ),
			  
                            'aggregationposition' => array(GRADE_REPORT_PREFERENCE_DEFAULT => '*default*',
                                                                       GRADE_REPORT_AGGREGATION_POSITION_FIRST => get_string('positionfirst', 'grades'),
                                                                       GRADE_REPORT_AGGREGATION_POSITION_LAST => get_string('positionlast', 'grades')
                            )
        );*/
        
        $preferences['prefcalc']['incompleasmin']  = $checkbox_default;
        $preferences['prefcalc']['usehidden']  = $checkbox_default;
        $preferences['prefcalc']['uselocked']  = $checkbox_default;
	
        foreach ($preferences as $group => $prefs) {
            $mform->addElement('header', $group, get_string($group, 'gradereport_visual'));

            foreach ($prefs as $pref => $type) {
                $full_pref  = 'grade_report_visual' . $pref;
                $pref_value = get_user_preferences($full_pref);
		
                $options = $type;
                $type = 'select';
		
                if (!empty($CFG->{$full_pref})) {
                    $course_value = grade_get_setting($course->id, $pref, $CFG->{$full_pref});
                } else {
                    $course_value = null;
                }
		
                if ($pref == 'aggregationposition') {
                    if (!empty($options[$course_value])) {
                        $default = $options[$course_value];
                    } elseif(isset($CFG->grade_aggregationposition)) {
                        $default = $options[$CFG->grade_aggregationposition];
                    }
                } elseif ($pref == 'aggregationview' && isset($CFG->grade_report_aggregationview) && isset($options[$CFG->grade_report_aggregationview])) {
                    $default = $options[$CFG->grade_report_aggregationview];
                } else {
                    if (!empty($options[$course_value])) {
                        $default = $options[$course_value];
                    } else {
                        if ($pref == 'incompleasmin') {
                            $default = $strno;
                        } else {
                            $default = $stryes;
                        }
                    }
                }
		
                $help_string = get_string("config$pref", 'gradereport_visual');
		
                // Replace the '*default*' value with the site default language string - 'default' might collide with custom language packs
                if (!is_null($options) AND isset($options[GRADE_REPORT_PREFERENCE_DEFAULT]) && $options[GRADE_REPORT_PREFERENCE_DEFAULT] == '*default*') {
                    $options[GRADE_REPORT_PREFERENCE_DEFAULT] = get_string('reportdefault', 'grades', $default);
                } elseif ($type == 'text') {
                    $help_string = get_string("config{$pref}default", 'gradereport_visual', $default);
                }

                $label = get_string($pref, 'gradereport_visual');

                $mform->addElement($type, $full_pref, $label, $options);
                $mform->setHelpButton($full_pref, array('visual' . $pref, get_string($pref, 'gradereport_visual'), 'grade'), true);
                $mform->setDefault($full_pref, $pref_value);
                $mform->setType($full_pref, PARAM_ALPHANUM);
            }
        }

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        $mform->setDefault('id', $course->id);

        $this->add_action_buttons();
    }
}

?>
