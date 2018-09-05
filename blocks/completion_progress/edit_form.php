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
 * Completion Progress block configuration form definition
 *
 * @package    block_completion_progress
 * @copyright  2016 Michael de Raadt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->dirroot.'/blocks/completion_progress/lib.php');

defined('MOODLE_INTERNAL') || die;

/**
 * Completion Progress block config form class
 *
 * @copyright 2016 Michael de Raadt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_completion_progress_edit_form extends block_edit_form {

    protected function specific_definition($mform) {
        global $COURSE, $OUTPUT;
        $activities = block_completion_progress_get_activities($COURSE->id, null, 'orderbycourse');
        $numactivies = count($activities);

        // The My home version is not configurable.
        if (block_completion_progress_on_site_page()) {
            return;
        }

        // Start block specific section in config form.
        $mform->addElement('header', 'configheader', get_string('blocksettings', 'block'));

        // Control order of items in Progress Bar.
        $expectedbystring = get_string('completionexpected', 'completion');
        $orderingoptions = array(
            'orderbytime'   => get_string('config_orderby_due_time', 'block_completion_progress', $expectedbystring),
            'orderbycourse' => get_string('config_orderby_course_order', 'block_completion_progress'),
        );
        $orderbylabel = get_string('config_orderby', 'block_completion_progress');
        $mform->addElement('select', 'config_orderby', $orderbylabel, $orderingoptions);
        $mform->setDefault('config_orderby', DEFAULT_COMPLETIONPROGRESS_ORDERBY);
        $mform->addHelpButton('config_orderby', 'how_ordering_works', 'block_completion_progress');

        // Check if all elements have an expect completion by time set.
        $allwithexpected = true;
        $i = 0;
        while ($i < $numactivies && $allwithexpected) {
            $allwithexpected = $activities[$i]['expected'] != 0;
            $i++;
        }
        if (!$allwithexpected) {
            $warningstring = get_string('not_all_expected_set', 'block_completion_progress', $expectedbystring);
            $expectedwarning = HTML_WRITER::tag('div', $warningstring, array('class' => 'warning'));
            $mform->addElement('static', $expectedwarning, '', $expectedwarning);
        }

        // Control how long bars wrap/scroll.
        $longbaroptions = array(
            'squeeze' => get_string('config_squeeze', 'block_completion_progress'),
            'scroll' => get_string('config_scroll', 'block_completion_progress'),
            'wrap' => get_string('config_wrap', 'block_completion_progress'),
        );
        $longbarslabel = get_string('config_longbars', 'block_completion_progress');
        $mform->addElement('select', 'config_longbars', $longbarslabel, $longbaroptions);
        $defaultlongbars = get_config('block_completion_progress', 'defaultlongbars') ?: DEFAULT_COMPLETIONPROGRESS_LONGBARS;
        $mform->setDefault('config_longbars', $defaultlongbars);
        $mform->addHelpButton('config_longbars', 'how_longbars_works', 'block_completion_progress');

        // Allow icons to be turned on/off on the block.
        $mform->addElement('selectyesno', 'config_progressBarIcons',
                           get_string('config_icons', 'block_completion_progress').' '.
                           $OUTPUT->pix_icon('tick', '', 'block_completion_progress', array('class' => 'iconOnConfig')).'&nbsp;'.
                           $OUTPUT->pix_icon('cross', '', 'block_completion_progress', array('class' => 'iconOnConfig')));
        $mform->setDefault('config_progressBarIcons', DEFAULT_COMPLETIONPROGRESS_PROGRESSBARICONS);
        $mform->addHelpButton('config_progressBarIcons', 'why_use_icons', 'block_completion_progress');

        // Allow progress percentage to be turned on for students.
        $mform->addElement('selectyesno', 'config_showpercentage',
                           get_string('config_percentage', 'block_completion_progress'));
        $mform->setDefault('config_showpercentage', DEFAULT_COMPLETIONPROGRESS_SHOWPERCENTAGE);
        $mform->addHelpButton('config_showpercentage', 'why_show_precentage', 'block_completion_progress');

        // Allow the block to be visible to a single group.
        $groups = groups_get_all_groups($COURSE->id);
        if (!empty($groups)) {
            $groupsmenu = array();
            $groupsmenu[0] = get_string('allparticipants');
            foreach ($groups as $group) {
                $groupsmenu[$group->id] = format_string($group->name);
            }
            $grouplabel = get_string('config_group', 'block_completion_progress');
            $mform->addElement('select', 'config_group', $grouplabel, $groupsmenu);
            $mform->setDefault('config_group', '0');
            $mform->addHelpButton('config_group', 'how_group_works', 'block_completion_progress');
            $mform->setAdvanced('config_group', true);
        }

        // Set block instance title.
        $mform->addElement('text', 'config_progressTitle',
                           get_string('config_title', 'block_completion_progress'));
        $mform->setDefault('config_progressTitle', '');
        $mform->setType('config_progressTitle', PARAM_TEXT);
        $mform->addHelpButton('config_progressTitle', 'why_set_the_title', 'block_completion_progress');
        $mform->setAdvanced('config_progressTitle', true);

        // Control which activities are included in the bar.
        $activitiesincludedoptions = array(
            'activitycompletion' => get_string('config_activitycompletion', 'block_completion_progress'),
            'selectedactivities' => get_string('config_selectedactivities', 'block_completion_progress'),
        );
        $activitieslabel = get_string('config_activitiesincluded', 'block_completion_progress');
        $mform->addElement('select', 'config_activitiesincluded', $activitieslabel, $activitiesincludedoptions);
        $mform->setDefault('config_activitiesincluded', DEFAULT_COMPLETIONPROGRESS_ACTIVITIESINCLUDED);
        $mform->addHelpButton('config_activitiesincluded', 'how_activitiesincluded_works', 'block_completion_progress');
        $mform->setAdvanced('config_activitiesincluded', true);

        // Check that there are activities to monitor.
        if (empty($activities)) {
            $warningstring = get_string('no_activities_config_message', 'block_completion_progress');
            $activitieswarning = HTML_WRITER::tag('div', $warningstring, array('class' => 'warning'));
            $mform->addElement('static', '', '', $activitieswarning);
        } else {
            $activitiestoinclude = array();
            foreach ($activities as $index => $activity) {
                $activitiestoinclude[$activity['type'].'-'.$activity['instance']] = $activity['name'];
            }
            $selectactivitieslabel = get_string('config_selectactivities', 'block_completion_progress');
            $mform->addElement('select', 'config_selectactivities', $selectactivitieslabel, $activitiestoinclude);
            $mform->getElement('config_selectactivities')->setMultiple(true);
            $mform->getElement('config_selectactivities')->setSize($numactivies);
            $mform->setAdvanced('config_selectactivities', true);
            $mform->disabledif('config_selectactivities', 'config_activitiesincluded', 'neq', 'selectedactivities');
            $mform->addHelpButton('config_selectactivities', 'how_selectactivities_works', 'block_completion_progress');
        }
    }
}
