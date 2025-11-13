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
 * Choicegroup activity instance editing form.
 *
 * @package    mod_choicegroup
 * @copyright  2013 Université de Lausanne
 * @author     Nicolas Dunand <Nicolas.Dunand@unil.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/course/moodleform_mod.php');

/**
 * Activity instance editing form.
 */
class mod_choicegroup_mod_form extends moodleform_mod {

    /**
     * @var string Column to sort groups by
     */
    protected $sortgroupsby = 'timecreated';

    /**
     * Define form elements
     *
     * @return void
     * @throws coding_exception
     * @throws dml_exception
     */
    public function definition() {
        global $CFG, $choicegroupshowresults, $choicegrouppublish, $choicegroupdisplay, $DB, $COURSE, $PAGE;

        $mform =& $this->_form;

        // -------------------------------------------------------------------------------
        $mform->addElement('header', 'general', get_string('general', 'form'));

        $mform->addElement('text', 'name', get_string('choicegroupname', 'choicegroup'), ['size' => '64']);
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

        // -------------------------------------------------------------------------------

        // -------------------------
        // Fetch data from database.
        // -------------------------.

        $choicegroupid = $this->get_instance();

        if ($choicegroupid && $choicegroup = $DB->get_record("choicegroup", ["id" => $choicegroupid])) {
            $this->sortgroupsby = choicegroup_get_sort_column($choicegroup);
        }

        $groups = [];
        $dbgroups = $DB->get_records('groups', ['courseid' => $COURSE->id], $this->sortgroupsby);

        foreach ($dbgroups as $group) {
            $groups[$group->id] = new stdClass();
            $groups[$group->id]->name = format_string($group->name);
            $groups[$group->id]->timecreated = $group->timecreated;
            $groups[$group->id]->mentioned = false;
            $groups[$group->id]->id = $group->id;
        }

        // Display warning if no groups are set.
        if ($PAGE->pagetype === 'mod-choicegroup-mod' && count($dbgroups) < 1) {
            $a = new stdClass();
            $a->linkgroups = $CFG->wwwroot . '/group/index.php?id=' . $COURSE->id;
            $a->linkcourse = $CFG->wwwroot . '//course/view.php?id=' . $COURSE->id;
            $message = get_string('pleasesetonegroupor', 'choicegroup', $a);
            \core\notification::add($message, \core\notification::WARNING);
        }

        $dbgroupings = $DB->get_records('groupings', ['courseid' => $COURSE->id], $this->sortgroupsby);
        $groupings = [];
        if ($dbgroupings) {
            foreach ($dbgroupings as $grouping) {
                $groupings[$grouping->id] = new stdClass();
                $groupings[$grouping->id]->name = $grouping->name;
                $groupings[$grouping->id]->timecreated = $grouping->timecreated;
            }

            list($sqlin, $inparams) = $DB->get_in_or_equal(array_keys($groupings));

            $sql = "SELECT gg.*
                    FROM {groupings_groups} gg
                    JOIN {groups} g ON gg.groupid = g.id
                    WHERE gg.groupingid $sqlin
                    ORDER BY g.$this->sortgroupsby ASC";

            $dbgroupingsgroups = $DB->get_records_sql($sql, $inparams);

            foreach ($dbgroupingsgroups as $groupinggrouplink) {
                $groupings[$groupinggrouplink->groupingid]->linkedGroupsIDs[] = $groupinggrouplink->groupid;
            }
        }
        // -------------------------
        // -------------------------

        // -------------------------
        // Continue generating form
        // -------------------------.
        $mform->addElement('header', 'miscellaneoussettingshdr', get_string('miscellaneoussettings', 'form'));
        $mform->setExpanded('miscellaneoussettingshdr');
        $mform->addElement('checkbox', 'multipleenrollmentspossible', get_string('multipleenrollmentspossible', 'choicegroup'));

        $mform->addElement('text', 'maxenrollments', get_string('maxenrollments', 'choicegroup'), ['size' => '6']);
        $mform->addHelpButton('maxenrollments', 'maxenrollments', 'choicegroup');
        $mform->setType('maxenrollments', PARAM_INT);
        $mform->hideIf('maxenrollments', 'multipleenrollmentspossible');
        $mform->addRule('maxenrollments', get_string('error'), 'numeric', 'extraruledata', 'client', false, false);
        $mform->setDefault('maxenrollments', 0);

        $mform->addElement('select', 'showresults', get_string("publish", "choicegroup"), $choicegroupshowresults);
        $mform->setDefault('showresults', CHOICEGROUP_SHOWRESULTS_DEFAULT);

        $mform->addElement('select', 'publish', get_string("privacy", "choicegroup"), $choicegrouppublish,
            CHOICEGROUP_PUBLISH_DEFAULT);
        $mform->setDefault('publish', CHOICEGROUP_PUBLISH_DEFAULT);
        $mform->disabledIf('publish', 'showresults', 'eq', 0);

        $mform->addElement('selectyesno', 'allowupdate', get_string("allowupdate", "choicegroup"));

        $mform->addElement('selectyesno', 'showunanswered', get_string("showunanswered", "choicegroup"));

        $mform->addElement('selectyesno', 'onlyactive', get_string('onlyactive', 'choicegroup'));
        $mform->setDefault('onlyactive', 0);

        $menuoptions = [];
        $menuoptions[0] = get_string('disable');
        $menuoptions[1] = get_string('enable');
        $mform->addElement('select', 'limitanswers', get_string('limitanswers', 'choicegroup'), $menuoptions);
        $mform->addHelpButton('limitanswers', 'limitanswers', 'choicegroup');

        $mform->addElement('text', 'generallimitation', get_string('generallimitation', 'choicegroup'), ['size' => '6']);
        $mform->setType('generallimitation', PARAM_INT);
        $mform->disabledIf('generallimitation', 'limitanswers', 'neq', 1);
        $mform->addRule('generallimitation', get_string('error'), 'numeric', 'extraruledata', 'client', false, false);
        $mform->setDefault('generallimitation', 0);
        $mform->addElement('button', 'setlimit', get_string('applytoallgroups', 'choicegroup'));
        $mform->disabledIf('setlimit', 'limitanswers', 'neq', 1);

        // -------------------------
        // Generate the groups section of the form
        // -------------------------.

        $mform->addElement('header', 'groups', get_string('groupsheader', 'choicegroup'));
        $mform->addElement('html', '
                <div class="fcontainer clearfix">
                <label for="fitem_id_option_0" class="fitemtitle">'
                    . get_string('choicegroupoptions_description', 'choicegroup') . '</label>
                <div id="fitem_id_option_0" name="fitem_id_option_0" class="fitem fitem_fselect ">
                <div class="felement fselect">
                <div class="tablecontainer">
                <table class="table-reboot">
                    <tr class="row">
                        <th class="col-lg-7">' . get_string('available_groups', 'choicegroup') . '</th>
                        <th class="col-lg-5">' . get_string('selected_groups', 'choicegroup') . '</th>
                    </tr>
                    <tr class="row">
                        <td style="vertical-align: top" class="col-5">');
        $mform->addHelpButton('groups', 'choicegroupoptions', 'choicegroup');

        $mform->addElement('html', '<select class="col-12" id="availablegroups" name="availableGroups" multiple size=10>');
        foreach ($groupings as $groupingid => $grouping) {
            // Find all linked groups to this grouping.
            if (isset($grouping->linkedGroupsIDs) && count($grouping->linkedGroupsIDs) > 1) {
                // Grouping has more than 2 items, thus we should display it (otherwise it would be clearer to display only that
                // single group alone).
                $mform->addElement('html', '<option value="' . $groupingid .
                    '" style="font-weight: bold" class="grouping" data-timecreated="'. $grouping->timecreated .'">' .
                    get_string('char_bullet_expanded', 'choicegroup') . $grouping->name . '</option>');
                foreach ($grouping->linkedGroupsIDs as $linkedgroupid) {
                    if (isset($groups[$linkedgroupid])) {
                        $mform->addElement('html', '<option value="' . $linkedgroupid .
                            '" class="group nested" data-timecreated="'. $groups[$linkedgroupid]->timecreated .'">' .
                            $groups[$linkedgroupid]->name . '</option>');
                        $groups[$linkedgroupid]->mentioned = true;
                    }
                }
            }
        }
        foreach ($groups as $group) {
            if ($group->mentioned === false) {
                $mform->addElement('html', '<option title="' . $group->name . '" value="' . $group->id .
                    '" class="group toplevel" data-timecreated="'. $group->timecreated .'">' . format_string($group->name) .
                    '</option>');
            }
        }
        $mform->addElement('html', '</select><br><button name="expandButton" type="button" id="expandButton" ' .
            'class="btn btn-secondary mt-1">' . get_string('expand_all_groupings', 'choicegroup') .
            '</button><button name="collapseButton" type="button" id="collapseButton" class="btn btn-secondary mt-1">' .
            get_string('collapse_all_groupings', 'choicegroup') .
            '</button><br>' . get_string('double_click_grouping_legend', 'choicegroup') . '<br>' .
            get_string('double_click_group_legend', 'choicegroup'));

        $mform->addElement('html', '
                </td><td class="col-2"><button id="addGroupButton" name="add" type="button" class="btn btn-secondary mt-1">' .
            get_string('add', 'choicegroup') .
            '</button><div><button name="remove" type="button" id="removeGroupButton" class="btn btn-secondary mt-1">' .
            get_string('del', 'choicegroup') . '</button></div></td>');
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
                ');

        $mform->setExpanded('groups');

        foreach ($groups as $group) {
            $mform->addElement('hidden', 'group_' . $group->id . '_limit', '', ['id' => 'group_' . $group->id . '_limit',
                'class' => 'limit_input_node', ]);
            $mform->setType('group_' . $group->id . '_limit', PARAM_RAW);
        }

        $serializedselectedgroupsvalue = '';
        if (isset($this->_instance) && $this->_instance != '') {
            // This is presumably edit mode, try to fill in the data for javascript.
            $cg = choicegroup_get_choicegroup($this->_instance);
            foreach ($cg->option as $optionid => $groupid) {
                $serializedselectedgroupsvalue .= ';' . $groupid;
                $mform->setDefault('group_' . $groupid . '_limit', $cg->maxanswers[$optionid]);
            }

        }

        $mform->addElement('hidden', 'serializedselectedgroups', $serializedselectedgroupsvalue,
            ['id' => 'serializedselectedgroups']);
        $mform->setType('serializedselectedgroups', PARAM_RAW);

        switch (get_config('choicegroup', 'sortgroupsby')) {
            case CHOICEGROUP_SORTGROUPS_CREATEDATE:
                $systemdefault = [CHOICEGROUP_SORTGROUPS_SYSTEMDEFAULT => get_string('systemdefault_date', 'choicegroup')];
                break;
            case CHOICEGROUP_SORTGROUPS_NAME:
                $systemdefault = [CHOICEGROUP_SORTGROUPS_SYSTEMDEFAULT => get_string('systemdefault_name', 'choicegroup')];
                break;
        }

        $options = array_merge($systemdefault, choicegroup_get_sort_options());
        $mform->addElement('select', 'sortgroupsby', get_string('sortgroupsby', 'choicegroup'), $options);
        $mform->setDefault('sortgroupsby', CHOICEGROUP_SORTGROUPS_SYSTEMDEFAULT);

        // Default Group Description Display.
        $descriptionoptions = [
            CHOICEGROUP_GROUPDESCRIPTIONSTATE_VISIBLE => get_string('showdescription', 'choicegroup'),
            CHOICEGROUP_GROUPDESCRIPTIONSTATE_HIDDEN => get_string('hidedescription', 'choicegroup'),
        ];
        $groupdescriptionstate = get_config('choicegroup', 'defaultgroupdescriptionstate');
        $mform->addElement(
            'select',
            'defaultgroupdescriptionstate',
            get_string('defaultgroupdescriptionstate', 'choicegroup'),
            $descriptionoptions
        );
        $mform->setDefault('defaultgroupdescriptionstate', $groupdescriptionstate);
        // -------------------------
        // Go on the with the remainder of the form.
        // -------------------------.

        // -------------------------------------------------------------------------------
        $mform->addElement('header', 'timerestricthdr', get_string('timerestrict', 'choicegroup'));
        $mform->addElement('checkbox', 'timerestrict', get_string('timerestrict', 'choicegroup'));

        $mform->addElement('date_time_selector', 'timeopen', get_string("choicegroupopen", "choicegroup"));
        $mform->disabledIf('timeopen', 'timerestrict');

        $mform->addElement('date_time_selector', 'timeclose', get_string("choicegroupclose", "choicegroup"));
        $mform->disabledIf('timeclose', 'timerestrict');

        // -------------------------------------------------------------------------------
        $this->standard_coursemodule_elements();
        // -------------------------------------------------------------------------------
        $this->add_action_buttons();
    }

    /**
     * Pre-process form data
     *
     * @param array $defaultvalues
     * @return void
     */
    public function data_preprocessing(&$defaultvalues) {
        global $PAGE;

        if ($PAGE->pagetype != 'course-defaultcompletion') {
            $this->js_call();
        }

        if (empty($defaultvalues['timeopen'])) {
            $defaultvalues['timerestrict'] = 0;
        } else {
            $defaultvalues['timerestrict'] = 1;
        }

    }

    /**
     * Add JavaScript to the form
     *
     * @return void
     * @throws coding_exception
     */
    public function js_call() {
        global $PAGE;
        $params = [$this->_form->getAttribute('id')];
        $PAGE->requires->yui_module('moodle-mod_choicegroup-form', 'Y.Moodle.mod_choicegroup.form.init', $params);
        foreach (array_keys(get_string_manager()->load_component_strings('choicegroup', current_language())) as $string) {
            $PAGE->requires->string_for_js($string, 'choicegroup');
        }
    }

    /**
     * Allows module to modify the data returned by form get_data().
     * This method is also called in the bulk activity completion form.
     *
     * Only available on moodleform_mod.
     *
     * @param stdClass $data the form data to be modified.
     * @return void
     */
    public function data_postprocessing($data): void {
        parent::data_postprocessing($data);
        // Set up completion section even if checkbox is not ticked.
        if (!empty($data->completionunlocked)) {
            if (empty($data->completionsubmit)) {
                $data->completionsubmit = 0;
            }
        }
    }

    /**
     * Validate the form data
     *
     * @param array $data array of ("fieldname"=>value) of submitted data
     * @param array $files array of uploaded files "element_name"=>tmp_file_path
     * @return array
     * @throws coding_exception
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        $groupids = explode(';', $data['serializedselectedgroups']);
        $groupids = array_diff($groupids, ['']);

        if ($data['timeopen'] > $data['timeclose']) {
            $errors['timeopen'] = get_string('activitydate:closingbeforeopening', 'choicegroup');
        }

        if (count($groupids) < 1) {
            $errors['groups'] = get_string('fillinatleastoneoption', 'choicegroup');
        }

        return $errors;
    }

    /**
     * Get the form data
     *
     * @return false|object
     */
    public function get_data() {
        $data = parent::get_data();
        if (!$data) {
            return false;
        }
        // Set up completion section even if checkbox is not ticked.
        if (empty($data->completionsection)) {
            $data->completionsection = 0;
        }
        return $data;
    }

    /**
     * Add completion rules
     *
     * @return string[]
     * @throws coding_exception
     */
    public function add_completion_rules() {
        global $CFG;

        $mform =& $this->_form;

        // Changes for Moodle 4.3 - MDL-78516.
        if ($CFG->branch < 403) {
            $suffix = '';
        } else {
            $suffix = $this->get_suffix();
        }

        $mform->addElement('checkbox', 'completionsubmit' . $suffix, '', get_string('completionsubmit', 'choicegroup'));
        return ['completionsubmit' . $suffix];
    }

    /**
     * Are completion rules enabled?
     *
     * @param array $data Input data (not yet validated)
     * @return bool
     */
    public function completion_rule_enabled($data) {
        return !empty($data['completionsubmit']);
    }

}
