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
 * Defines the form for editing Quiz results block instances.
 *
 * @package    block_activity_results
 * @copyright  2009 Tim Hunt
 * @copyright  2015 Stephen Bourget
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot . '/lib/grade/constants.php');

/**
 * Form for editing activity results block instances.
 *
 * @copyright 2009 Tim Hunt
 * @copyright 2015 Stephen Bourget
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_activity_results_edit_form extends block_edit_form {
    /**
     * The definition of the fields to use.
     *
     * @param MoodleQuickForm $mform
     */
    protected function specific_definition($mform) {
        global $DB;

        // Load defaults.
        $blockconfig = get_config('block_activity_results');

        // Fields for editing activity_results block title and contents.
        $mform->addElement('header', 'configheader', get_string('blocksettings', 'block'));

        // Get supported modules (Only modules using grades or scales will be listed).
        $sql = 'SELECT id, itemname FROM {grade_items} WHERE courseid = ? and itemtype = ? and (gradetype = ? or gradetype = ?)';
        $params = array($this->page->course->id, 'mod', GRADE_TYPE_VALUE, GRADE_TYPE_SCALE);
        $activities = $DB->get_records_sql_menu($sql, $params);
        core_collator::asort($activities);

        if (empty($activities)) {
            $mform->addElement('static', 'noactivitieswarning', get_string('config_select_activity', 'block_activity_results'),
                    get_string('config_no_activities_in_course', 'block_activity_results'));
        } else {
            foreach ($activities as $id => $name) {
                $activities[$id] = strip_tags(format_string($name));
            }
            $mform->addElement('select', 'config_activitygradeitemid',
                    get_string('config_select_activity', 'block_activity_results'), $activities);
            $mform->setDefault('config_activitygradeitemid', $this->block->get_owning_activity()->id);
        }

        $mform->addElement('text', 'config_showbest',
                get_string('config_show_best', 'block_activity_results'), array('size' => 3));
        $mform->setDefault('config_showbest', $blockconfig->config_showbest);
        $mform->setType('config_showbest', PARAM_INT);
        if ($blockconfig->config_showbest_locked) {
            $mform->freeze('config_showbest');
        }

        $mform->addElement('text', 'config_showworst',
                get_string('config_show_worst', 'block_activity_results'), array('size' => 3));
        $mform->setDefault('config_showworst', $blockconfig->config_showworst);
        $mform->setType('config_showworst', PARAM_INT);
        if ($blockconfig->config_showworst_locked) {
            $mform->freeze('config_showworst');
        }

        $mform->addElement('selectyesno', 'config_usegroups', get_string('config_use_groups', 'block_activity_results'));
        $mform->setDefault('config_usegroups', $blockconfig->config_usegroups);
        if ($blockconfig->config_usegroups_locked) {
            $mform->freeze('config_usegroups');
        }

        $nameoptions = array(
            B_ACTIVITYRESULTS_NAME_FORMAT_FULL => get_string('config_names_full', 'block_activity_results'),
            B_ACTIVITYRESULTS_NAME_FORMAT_ID => get_string('config_names_id', 'block_activity_results'),
            B_ACTIVITYRESULTS_NAME_FORMAT_ANON => get_string('config_names_anon', 'block_activity_results')
        );
        $mform->addElement('select', 'config_nameformat',
                get_string('config_name_format', 'block_activity_results'), $nameoptions);
        $mform->setDefault('config_nameformat', $blockconfig->config_nameformat);
        if ($blockconfig->config_nameformat_locked) {
            $mform->freeze('config_nameformat');
        }

        $gradeeoptions = array(
            B_ACTIVITYRESULTS_GRADE_FORMAT_PCT => get_string('config_format_percentage', 'block_activity_results'),
            B_ACTIVITYRESULTS_GRADE_FORMAT_FRA => get_string('config_format_fraction', 'block_activity_results'),
            B_ACTIVITYRESULTS_GRADE_FORMAT_ABS => get_string('config_format_absolute', 'block_activity_results')
        );
        $mform->addElement('select', 'config_gradeformat',
                get_string('config_grade_format', 'block_activity_results'), $gradeeoptions);
        $mform->setDefault('config_gradeformat', $blockconfig->config_gradeformat);
        if ($blockconfig->config_gradeformat_locked) {
            $mform->freeze('config_gradeformat');
        }

        $options = array();
        for ($i = 0; $i <= 5; $i++) {
            $options[$i] = $i;
        }
        $mform->addElement('select', 'config_decimalpoints', get_string('config_decimalplaces', 'block_activity_results'),
                $options);
        $mform->setDefault('config_decimalpoints', $blockconfig->config_decimalpoints);
        $mform->setType('config_decimalpoints', PARAM_INT);
        if ($blockconfig->config_decimalpoints_locked) {
            $mform->freeze('config_decimalpoints');
        }
    }

    /**
     * Display the configuration form when block is being added to the page
     *
     * @return bool
     */
    public static function display_form_when_adding(): bool {
        return true;
    }
}