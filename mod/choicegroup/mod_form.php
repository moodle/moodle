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
 * Version information
 *
 * @package    mod
 * @subpackage choicegroup
 * @copyright  2013 Université de Lausanne
 * @author     Nicolas Dunand <Nicolas.Dunand@unil.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/course/moodleform_mod.php');

class mod_choicegroup_mod_form extends moodleform_mod
{

    function definition()
    {
        global $CFG, $CHOICEGROUP_SHOWRESULTS, $CHOICEGROUP_PUBLISH, $CHOICEGROUP_DISPLAY, $DB, $COURSE, $PAGE;

        $mform =& $this->_form;

        //-------------------------------------------------------------------------------
        $mform->addElement('header', 'general', get_string('general', 'form'));

        $mform->addElement('text', 'name', get_string('choicegroupname', 'choicegroup'), array('size' => '64'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEANHTML);
        }
        $mform->addRule('name', null, 'required', null, 'client');

        if (method_exists($this, 'standard_intro_elements')) {
            $this->standard_intro_elements(get_string('description'));
        } else {
            $this->add_intro_editor(true, get_string('description'));
        }

        //-------------------------------------------------------------------------------


        // -------------------------
        // Fetch data from database
        // -------------------------
        $groups = array();
        $db_groups = $DB->get_records('groups', array('courseid' => $COURSE->id));
        foreach ($db_groups as $group) {
            $groups[$group->id] = new stdClass();
            $groups[$group->id]->name = format_string($group->name);
            $groups[$group->id]->mentioned = false;
            $groups[$group->id]->id = $group->id;
        }

        if (count($db_groups) < 1) {
            $a = new stdClass();
            $a->linkgroups = $CFG->wwwroot . '/group/index.php?id=' . $COURSE->id;
            $a->linkcourse = $CFG->wwwroot . '//course/view.php?id=' . $COURSE->id;
            $message = get_string('pleasesetonegroupor', 'choicegroup', $a);
            \core\notification::add($message, \core\notification::WARNING);
            print_error('nogroupincourse', 'choicegroup', new moodle_url('/course/view.php?id=' . $COURSE->id), $a);
        }

        $db_groupings = $DB->get_records('groupings', array('courseid' => $COURSE->id));
        $groupings = array();
        if ($db_groupings) {
            foreach ($db_groupings as $grouping) {
                $groupings[$grouping->id] = new stdClass();
                $groupings[$grouping->id]->name = $grouping->name;
            }

            list($sqlin, $inparams) = $DB->get_in_or_equal(array_keys($groupings));
            $db_groupings_groups = $DB->get_records_select('groupings_groups', 'groupingid ' . $sqlin, $inparams);

            foreach ($db_groupings_groups as $grouping_group_link) {
                $groupings[$grouping_group_link->groupingid]->linkedGroupsIDs[] = $grouping_group_link->groupid;
            }
        }
        // -------------------------
        // -------------------------

        // -------------------------
        // Continue generating form
        // -------------------------
        $mform->addElement('header', 'miscellaneoussettingshdr', get_string('miscellaneoussettings', 'form'));
        $mform->setExpanded('miscellaneoussettingshdr');
        $mform->addElement('checkbox', 'multipleenrollmentspossible', get_string('multipleenrollmentspossible', 'choicegroup'));

        $mform->addElement('text', 'maxenrollments', get_string('maxenrollments', 'choicegroup'), array('size' => '6'));
        $mform->addHelpButton('maxenrollments', 'maxenrollments', 'choicegroup');
        $mform->setType('maxenrollments', PARAM_INT);
        $mform->hideIf('maxenrollments', 'multipleenrollmentspossible');
        $mform->addRule('maxenrollments', get_string('error'), 'numeric', 'extraruledata', 'client', false, false);
        $mform->setDefault('maxenrollments', 0);

        $mform->addElement('select', 'showresults', get_string("publish", "choicegroup"), $CHOICEGROUP_SHOWRESULTS);
        $mform->setDefault('showresults', CHOICEGROUP_SHOWRESULTS_DEFAULT);

        $mform->addElement('select', 'publish', get_string("privacy", "choicegroup"), $CHOICEGROUP_PUBLISH, CHOICEGROUP_PUBLISH_DEFAULT);
        $mform->setDefault('publish', CHOICEGROUP_PUBLISH_DEFAULT);
        $mform->disabledIf('publish', 'showresults', 'eq', 0);

        $mform->addElement('selectyesno', 'allowupdate', get_string("allowupdate", "choicegroup"));

        $mform->addElement('selectyesno', 'showunanswered', get_string("showunanswered", "choicegroup"));

        $mform->addElement('selectyesno', 'onlyactive', get_string('onlyactive', 'choicegroup'));
        $mform->setDefault('onlyactive', 0);

        $menuoptions = array();
        $menuoptions[0] = get_string('disable');
        $menuoptions[1] = get_string('enable');
        $mform->addElement('select', 'limitanswers', get_string('limitanswers', 'choicegroup'), $menuoptions);
        $mform->addHelpButton('limitanswers', 'limitanswers', 'choicegroup');

        $mform->addElement('text', 'generallimitation', get_string('generallimitation', 'choicegroup'), array('size' => '6'));
        $mform->setType('generallimitation', PARAM_INT);
        $mform->disabledIf('generallimitation', 'limitanswers', 'neq', 1);
        $mform->addRule('generallimitation', get_string('error'), 'numeric', 'extraruledata', 'client', false, false);
        $mform->setDefault('generallimitation', 0);
        $mform->addElement('button', 'setlimit', get_string('applytoallgroups', 'choicegroup'));
        $mform->disabledIf('setlimit', 'limitanswers', 'neq', 1);


        // -------------------------
        // Generate the groups section of the form
        // -------------------------


        $mform->addElement('header', 'groups', get_string('groupsheader', 'choicegroup'));
        $mform->addElement('html', '<fieldset class="clearfix">
				<div class="fcontainer clearfix">
				<div id="fitem_id_option_0" class="fitem fitem_fselect ">
				<div class="fitemtitle"><label for="id_option_0">' . get_string('groupsheader', 'choicegroup') . '</label><span class="helptooltip"><a href="' . $CFG->wwwroot . '/help.php?component=choicegroup&amp;identifier=choicegroupoptions&amp;lang=' . current_language() . '" title="' . get_string('choicegroupoptions_help', 'choicegroup') . '" aria-haspopup="true" target="_blank"><img src="' . $CFG->wwwroot . '/theme/image.php?theme=' . $PAGE->theme->name . '&component=core&image=help" alt="' . get_string('choicegroupoptions_help', 'choicegroup') . '" class="iconhelp"></a></span></div><div class="felement fselect">
                <div class="tablecontainer">
				<table>
				    <tr class="row">
				        <th class="col-lg-6">' . get_string('available_groups', 'choicegroup') . '</th>
				        <th class="col-lg-6">' . get_string('selected_groups', 'choicegroup') . '</th>
                    </tr>
                    <tr class="row">
                        <td style="vertical-align: top" class="col-5">');

        $mform->addElement('html', '<select class="col-12" id="availablegroups" name="availableGroups" multiple size=10>');
        foreach ($groupings as $groupingID => $grouping) {
            // find all linked groups to this grouping
            if (isset($grouping->linkedGroupsIDs) && count($grouping->linkedGroupsIDs) > 1) { // grouping has more than 2 items, thus we should display it (otherwise it would be clearer to display only that single group alone)
                $mform->addElement('html', '<option value="' . $groupingID . '" style="font-weight: bold" class="grouping">' . get_string('char_bullet_expanded', 'choicegroup') . $grouping->name . '</option>');
                foreach ($grouping->linkedGroupsIDs as $linkedGroupID) {
                    if (isset($groups[$linkedGroupID])) {
                        $mform->addElement('html', '<option value="' . $linkedGroupID . '" class="group nested">&nbsp;&nbsp;&nbsp;&nbsp;' . $groups[$linkedGroupID]->name . '</option>');
                        $groups[$linkedGroupID]->mentioned = true;
                    }
                }
            }
        }
        foreach ($groups as $group) {
            if ($group->mentioned === false) {
                $mform->addElement('html', '<option title="' . $group->name . '" value="' . $group->id . '" class="group toplevel">' . format_string($group->name) . '</option>');
            }
        }
        $mform->addElement('html', '</select><br><button name="expandButton" type="button" disabled id="expandButton" class="btn btn-secondary">' . get_string('expand_all_groupings', 'choicegroup') .
            '</button><button name="collapseButton" type="button" disabled id="collapseButton" class="btn btn-secondary">' . get_string('collapse_all_groupings', 'choicegroup') .
            '</button><br>' . get_string('double_click_grouping_legend', 'choicegroup') . '<br>' . get_string('double_click_group_legend', 'choicegroup'));


        $mform->addElement('html', '
				</td><td class="col-2"><button id="addGroupButton" name="add" type="button" disabled class="btn btn-secondary">' . get_string('add', 'choicegroup') .
            '</button><div><button name="remove" type="button" disabled id="removeGroupButton" class="btn btn-secondary">' . get_string('del', 'choicegroup') . '</button></div></td>');
        $mform->addElement('html', '<td style="vertical-align: top" class="col-5">
    <select class="col-12" id="id_selectedGroups" name="selectedGroups" multiple size=10></select>
    <div id="fitem_id_limit_0" class="fitem fitem_ftext" style="display:none">
        <div>
            <label for="id_limit_0" id="label_for_limit_ui">' . get_string('set_limit_for_group', 'choicegroup') . ' </label>
        </div>
        <div class="ftext">
            <input class="mod-choicegroup-limit-input" type="text" value="0" id="ui_limit_input" disabled="disabled">
        </div>
    </div>
</td>');

        $mform->addElement('html', '</tr></table>
            </div>
        </div>
    </div>
</div>
				</fieldset>');

        $mform->setExpanded('groups');

        foreach ($groups as $group) {
            $mform->addElement('hidden', 'group_' . $group->id . '_limit', '', array('id' => 'group_' . $group->id . '_limit', 'class' => 'limit_input_node'));
            $mform->setType('group_' . $group->id . '_limit', PARAM_RAW);
        }


        $serializedselectedgroupsValue = '';
        if (isset($this->_instance) && $this->_instance != '') {
            // this is presumably edit mode, try to fill in the data for javascript
            $cg = choicegroup_get_choicegroup($this->_instance);
            foreach ($cg->option as $optionID => $groupID) {
                $serializedselectedgroupsValue .= ';' . $groupID;
                $mform->setDefault('group_' . $groupID . '_limit', $cg->maxanswers[$optionID]);
            }

        }


        $mform->addElement('hidden', 'serializedselectedgroups', $serializedselectedgroupsValue, array('id' => 'serializedselectedgroups'));
        $mform->setType('serializedselectedgroups', PARAM_RAW);

        switch (get_config('choicegroup', 'sortgroupsby')) {
            case CHOICEGROUP_SORTGROUPS_CREATEDATE:
                $systemdefault = array(CHOICEGROUP_SORTGROUPS_SYSTEMDEFAULT => get_string('systemdefault_date', 'choicegroup'));
                break;
            case CHOICEGROUP_SORTGROUPS_NAME:
                $systemdefault = array(CHOICEGROUP_SORTGROUPS_SYSTEMDEFAULT => get_string('systemdefault_name', 'choicegroup'));
                break;
        }

        $options = array_merge($systemdefault, choicegroup_get_sort_options());
        $mform->addElement('select', 'sortgroupsby', get_string('sortgroupsby', 'choicegroup'), $options);
        $mform->setDefault('sortgroupsby', CHOICEGROUP_SORTGROUPS_SYSTEMDEFAULT);

        // -------------------------
        // Go on the with the remainder of the form
        // -------------------------


        //-------------------------------------------------------------------------------
        $mform->addElement('header', 'timerestricthdr', get_string('timerestrict', 'choicegroup'));
        $mform->addElement('checkbox', 'timerestrict', get_string('timerestrict', 'choicegroup'));

        $mform->addElement('date_time_selector', 'timeopen', get_string("choicegroupopen", "choicegroup"));
        $mform->disabledIf('timeopen', 'timerestrict');

        $mform->addElement('date_time_selector', 'timeclose', get_string("choicegroupclose", "choicegroup"));
        $mform->disabledIf('timeclose', 'timerestrict');

        //-------------------------------------------------------------------------------
        $this->standard_coursemodule_elements();
        //-------------------------------------------------------------------------------
        $this->add_action_buttons();
    }

    function data_preprocessing(&$default_values)
    {
        global $DB;
        $this->js_call();

        if (empty($default_values['timeopen'])) {
            $default_values['timerestrict'] = 0;
        } else {
            $default_values['timerestrict'] = 1;
        }

    }

    public function js_call()
    {
        global $PAGE;
        $params = [$this->_form->getAttribute('id')];
        $PAGE->requires->yui_module('moodle-mod_choicegroup-form', 'Y.Moodle.mod_choicegroup.form.init', $params);
        foreach (array_keys(get_string_manager()->load_component_strings('choicegroup', current_language())) as $string) {
            $PAGE->requires->string_for_js($string, 'choicegroup');
        }
    }

    function validation($data, $files)
    {
        $errors = parent::validation($data, $files);

        $groupIDs = explode(';', $data['serializedselectedgroups']);
        $groupIDs = array_diff($groupIDs, array(''));

        if (array_key_exists('multipleenrollmentspossible', $data) && $data['multipleenrollmentspossible'] === '1') {
            if (count($groupIDs) < 1) {
                $errors['groups'] = get_string('fillinatleastoneoption', 'choicegroup');
            }
        } else {
            if (count($groupIDs) < 1) {
                $errors['groups'] = get_string('fillinatleastoneoption', 'choicegroup');
            }
        }


        return $errors;
    }

    function get_data()
    {
        $data = parent::get_data();
        if (!$data) {
            return false;
        }
        // Set up completion section even if checkbox is not ticked
        if (empty($data->completionsection)) {
            $data->completionsection = 0;
        }
        return $data;
    }

    function add_completion_rules()
    {
        $mform =& $this->_form;

        $mform->addElement('checkbox', 'completionsubmit', '', get_string('completionsubmit', 'choicegroup'));
        return array('completionsubmit');
    }

    function completion_rule_enabled($data)
    {
        return !empty($data['completionsubmit']);
    }

}
