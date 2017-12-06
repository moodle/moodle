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
 * @package dataformfield
 * @subpackage entrystate
 * @copyright 2013 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class dataformfield_entrystate_form extends mod_dataform\pluginbase\dataformfieldform {

    /**
     *
     */
    protected function field_definition() {
        // States.
        $this->definition_states();
        // Transitions.
        $this->definition_transitions();
    }

    /**
     *
     */
    protected function definition_states() {
        global $COURSE;

        $field = &$this->_field;
        $mform = &$this->_form;

        // Header.
        $mform->addElement('header', 'hdrstates', get_string('states', 'dataformfield_entrystate'));
        $mform->setExpanded('hdrstates');

        // States name.
        $statenames = '';
        if ($states = $field->states) {
            $statenames = implode("\n", $states);
        }
        $mform->addElement('textarea', 'states', get_string('states', 'dataformfield_entrystate'), array('rows' => 5));
        $mform->setType('states', PARAM_TEXT);
        $mform->setDefault('states', $statenames);
        $mform->addHelpButton('states', 'states', 'dataformfield_entrystate');

        // State icon
        // $options = array('subdirs' => 0, 'maxbytes' => $COURSE->maxbytes, 'maxfiles' => 1, 'accepted_types' => array('image'));
        // $draftitemid = file_get_submitted_draft_itemid("stateicon$num");
        // file_prepare_draft_area($draftitemid, $field->df->context->id, 'dataformfield_entrystate', "stateicon$num", $field->id, $options);
        // $mform->addElement('filemanager', "stateicon$num", get_string('stateicon', 'dataformfield_entrystate'), null, $options);
        // $mform->setDefault("stateicon$num", $draftitemid);
        // $mform->addHelpButton("stateicon$num", 'stateicon', 'dataformfield_entrystate');.

    }

    /**
     *
     */
    protected function definition_transitions() {
        $field = &$this->_field;
        $transitions = $field->transitions;
        $count = 0;
        foreach ($transitions as $transition) {
            $this->definition_transition($count, $transition);
            $count++;
        }

        // Add 3 blank transitions.
        $this->definition_transition($count++);
        $this->definition_transition($count++);
        $this->definition_transition($count++);
    }

    /**
     *
     */
    protected function definition_transition($num, $trans = null) {
        global $COURSE;

        $field = &$this->_field;
        $mform = &$this->_form;
        $statenames = array(-1 => '* '. get_string('any')) + $field->states;

        $nostate = '['.get_string('state', 'dataformfield_entrystate').']';

        $transfrom = isset($trans['from']) ? $trans['from'] : null;
        $statefrom = isset($statenames[$transfrom]) ? $statenames[$transfrom] : $nostate;
        $transto = isset($trans['to']) ? $trans['to'] : null;
        $stateto = isset($statenames[$transto]) ? $statenames[$transto] : $nostate;
        $permission = !empty($trans['permission']) ? $trans['permission'] : null;
        $notification = !empty($trans['notification']) ? $trans['notification'] : null;
        $contextroles = $this->get_context_roles_menu();
        $statesmenu = array('' => get_string('choosedots')) + $statenames;

        $allowedtostr = get_string('allowedto', 'dataformfield_entrystate');
        $notifystr = get_string('notify', 'dataformfield_entrystate');

        // Header.
        $headerstr = get_string('transition', 'dataformfield_entrystate'). ": $statefrom - $stateto";
        $allowedsummary = $this->get_roles_summary($allowedtostr, $permission, $contextroles);
        $notificationsummary = $this->get_roles_summary($notifystr, $notification, $contextroles);
        $mform->addElement('header', "hdrtrans$num", $headerstr. $allowedsummary. $notificationsummary);

        // From to.
        $grp = array();
        $grp[] = $mform->createElement('select', "from$num", null, $statesmenu);
        $grp[] = $mform->createElement('select', "to$num", null, $statesmenu);
        $mform->addGroup($grp, "fromto_grp$num", get_string('from'), '&nbsp;&nbsp;'. get_string('to'). '&nbsp;&nbsp;', false);
        $mform->setDefault("from$num", $transfrom);
        $mform->setDefault("to$num", $transto);

        // Permissions.
        $options = array(
            $field::ROLE_AUTHOR => get_string('author', 'dataform'),
            $field::ROLE_ENTRIES_MANAGER => get_string('entriesmanager', 'dataform'),
        );
        $options = $options + $contextroles;
        $select = &$mform->addElement('select', "permission$num", $allowedtostr, $options);
        $select->setMultiple(true);
        $mform->addHelpButton("permission$num", 'allowedto', 'dataformfield_entrystate');
        $mform->setDefault("permission$num", $permission);
        $mform->disabledIf("permission$num", "from$num", 'eq', '');
        $mform->disabledIf("permission$num", "to$num", 'eq', '');

        // Notifications.
        $options = array(
            $field::ROLE_AUTHOR => get_string('author', 'dataform'),
            $field::ROLE_ENTRIES_MANAGER => get_string('entriesmanager', 'dataform'),
        );
        $options = $options + $contextroles;
        $select = &$mform->addElement('select', "notification$num", $notifystr, $options);
        $select->setMultiple(true);
        $mform->addHelpButton("notification$num", 'notify', 'dataformfield_entrystate');
        $mform->setDefault("notification$num", $notification);
        $mform->disabledIf("notification$num", "from$num", 'eq', '');
        $mform->disabledIf("notification$num", "to$num", 'eq', '');

    }

    /**
     *
     */
    public function definition_default_content() {
        $mform = &$this->_form;
        $field = &$this->_field;

        // Content elements.
        $label = get_string('fielddefaultvalue', 'dataform');
        $options = array('' => get_string('choosedots')) + $field->states;
        $mform->addElement('select', 'contentdefault', $label, $options);
        $mform->disabledIf('contentdefault', 'states', 'eq', '');
    }

    /**
     *
     */
    public function data_preprocessing(&$data) {
        $field = &$this->_field;

        // Default content.
        $data->contentdefault = $field->defaultcontent;
    }

    /**
     *
     */
    public function get_data() {
        if ($data = parent::get_data()) {
            // Set config (param1).
            $config = array();

            // Must have states.
            if (!empty($data->states)) {
                $config['states'] = $data->states;

                // Transitions.
                $transitions = array();
                $i = 0;
                while (isset($data->{"to$i"})) {
                    if ($data->{"to$i"} === '') {
                        $i++;
                        continue;
                    }
                    $trans = array();
                    $trans['from'] = $data->{"from$i"};
                    $trans['to'] = $data->{"to$i"};

                    if (!empty($data->{"permission$i"})) {
                        $trans['permission'] = $data->{"permission$i"};
                    }
                    if (!empty($data->{"notification$i"})) {
                        $trans['notification'] = $data->{"notification$i"};
                    }
                    if ($trans) {
                        $transitions[] = $trans;
                    }
                    $i++;
                }
                if ($transitions) {
                    $config['transitions'] = $transitions;
                }
            }
            // Set param1.
            $data->param1 = $config ? base64_encode(serialize($config)) : null;
        }
        return $data;
    }

    /**
     * Returns the default content data.
     *
     * @param stdClass $data
     * @return mix|null
     */
    protected function get_data_default_content(\stdClass $data) {
        if (!empty($data->contentdefault)) {
            return $data->contentdefault;
        }
        return null;
    }

    /**
     * A hook method for validating field default content. Returns list of errors.
     *
     * @param array The form data
     * @return void
     */
    protected function validation_default_content(array $data) {
        $errors = array();

        if (!empty($data['contentdefault'])) {
            $selected = $data['contentdefault'];
            // Get the options.
            if (!empty($data['states'])) {
                $options = explode("\n", $data['states']);
            } else {
                $options = null;
            }

            // The default must be a valid option.
            if (!$options or $selected > count($options)) {
                $errors['contentdefault'] = get_string('invaliddefaultvalue', 'dataformfield_select');
            }
        }

        return $errors;
    }

    /**
     *
     */
    protected function get_context_roles_menu() {
        $context = $this->_field->df->context;
        return role_get_names($context, ROLENAME_ALIAS, true);
    }

    /**
     *
     */
    protected function get_roles_summary($label, $roleids, array $rolesmenu) {
        $field = $this->_field;
        if ($roleids) {
            $options = array(
                $field::ROLE_AUTHOR => get_string('author', 'dataform'),
                $field::ROLE_ENTRIES_MANAGER => get_string('entriesmanager', 'dataform'),
            );
            $rolesmenu = $rolesmenu + $options;
            $permittedroles = implode(', ', array_intersect_key($rolesmenu, array_fill_keys($roleids, null)));
        } else {
            $permittedroles = '---';
        }
        $labelspan = html_writer::tag('span', $label, array('class' => 'summarylabel'));
        return html_writer::tag('div', $labelspan. ': '. $permittedroles, array('class' => 'summary'));
    }

}
