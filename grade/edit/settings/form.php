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
 * A form for editing course grade settings
 *
 * @package   core_grades
 * @copyright 2007 Petr Skoda
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->libdir.'/formslib.php');

/**
 * First implementation of the preferences in the form of a moodleform.
 * TODO add "reset to site defaults" button
 */
class course_settings_form extends moodleform {

    function definition() {
        global $USER, $CFG;

        $mform =& $this->_form;

        $systemcontext = context_system::instance();
        $can_view_admin_links = false;
        if (has_capability('moodle/grade:manage', $systemcontext)) {
            $can_view_admin_links = true;
        }

        // General settings
        $strchangedefaults = get_string('changedefaults', 'grades');
        $mform->addElement('header', 'general', get_string('generalsettings', 'grades'));
        if ($can_view_admin_links) {
            $link = '<a href="' . $CFG->wwwroot.'/'.$CFG->admin.'/settings.php?section=gradessettings">' . $strchangedefaults . '</a>';
            $mform->addElement('static', 'generalsettingslink', null, $link);
        }
        $options = array(-1                                      => get_string('default', 'grades'),
                         GRADE_REPORT_AGGREGATION_POSITION_FIRST => get_string('positionfirst', 'grades'),
                         GRADE_REPORT_AGGREGATION_POSITION_LAST  => get_string('positionlast', 'grades'));
        $default_gradedisplaytype = $CFG->grade_aggregationposition;
        foreach ($options as $key=>$option) {
            if ($key == $default_gradedisplaytype) {
                $options[-1] = get_string('defaultprev', 'grades', $option);
                break;
            }
        }
        $mform->addElement('select', 'aggregationposition', get_string('aggregationposition', 'grades'), $options);
        $mform->addHelpButton('aggregationposition', 'aggregationposition', 'grades');

        // Grade item settings
        $mform->addElement('header', 'grade_item_settings', get_string('gradeitemsettings', 'grades'));
        $mform->setExpanded('grade_item_settings');
        if ($can_view_admin_links) {
            $link = '<a href="' . $CFG->wwwroot.'/'.$CFG->admin.'/settings.php?section=gradeitemsettings">' . $strchangedefaults . '</a>';
            $mform->addElement('static', 'gradeitemsettingslink', null, $link);
        }

        $options = array(-1                            => get_string('default', 'grades'),
                         GRADE_DISPLAY_TYPE_REAL       => get_string('real', 'grades'),
                         GRADE_DISPLAY_TYPE_REAL_PERCENTAGE => get_string('realpercentage', 'grades'),
                         GRADE_DISPLAY_TYPE_REAL_LETTER => get_string('realletter', 'grades'),
                         GRADE_DISPLAY_TYPE_PERCENTAGE => get_string('percentage', 'grades'),
                         GRADE_DISPLAY_TYPE_PERCENTAGE_REAL => get_string('percentagereal', 'grades'),
                         GRADE_DISPLAY_TYPE_PERCENTAGE_LETTER => get_string('percentageletter', 'grades'),
                         GRADE_DISPLAY_TYPE_LETTER     => get_string('letter', 'grades'),
                         GRADE_DISPLAY_TYPE_LETTER_REAL => get_string('letterreal', 'grades'),
                         GRADE_DISPLAY_TYPE_LETTER_PERCENTAGE => get_string('letterpercentage', 'grades'));

        $default_gradedisplaytype = $CFG->grade_displaytype;
        foreach ($options as $key=>$option) {
            if ($key == $default_gradedisplaytype) {
                $options[-1] = get_string('defaultprev', 'grades', $option);
                break;
            }
        }
        $mform->addElement('select', 'displaytype', get_string('gradedisplaytype', 'grades'), $options);
        $mform->addHelpButton('displaytype', 'gradedisplaytype', 'grades');
        $mform->setDefault('displaytype', -1);

        $options = array(-1=> get_string('defaultprev', 'grades', $CFG->grade_decimalpoints), 0=>0, 1=>1, 2=>2, 3=>3, 4=>4, 5=>5);
        $mform->addElement('select', 'decimalpoints', get_string('decimalpoints', 'grades'), $options);
        $mform->addHelpButton('decimalpoints', 'decimalpoints', 'grades');

// add setting options for plugins
        $types = array('report', 'export', 'import');

        foreach($types as $type) {
            foreach (core_component::get_plugin_list('grade'.$type) as $plugin => $plugindir) {
             // Include all the settings commands for this plugin if there are any
                if (file_exists($plugindir.'/lib.php')) {
                    require_once($plugindir.'/lib.php');
                    $functionname = 'grade_'.$type.'_'.$plugin.'_settings_definition';
                    if (function_exists($functionname)) {
                        $mform->addElement('header', 'grade_'.$type.$plugin, get_string('pluginname', 'grade'.$type.'_'.$plugin, NULL));
                        $mform->setExpanded('grade_'.$type.$plugin);
                        if ($can_view_admin_links) {
                            $link = '<a href="' . $CFG->wwwroot.'/'.$CFG->admin.'/settings.php?section=gradereport' . $plugin . '">' . $strchangedefaults . '</a>';
                            $mform->addElement('static', 'gradeitemsettingslink', null, $link);
                        }
                        $functionname($mform);
                    }
                }
            }
        }

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $this->add_action_buttons();
    }
}

