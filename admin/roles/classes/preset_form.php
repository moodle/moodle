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
 * Role add/reset selection form.
 *
 * @package    core_role
 * @copyright  2013 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/formslib.php");


/**
 * Role add/reset selection form.
 *
 * @package    core_role
 * @copyright  2013 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_role_preset_form extends moodleform {

    /**
     * Definition of this form.
     */
    protected function definition() {
        $mform = $this->_form;

        $data = $this->_customdata;
        $options = array();

        $group = get_string('other');
        $options[$group] = array();
        $options[$group][0] = get_string('norole', 'core_role');

        $group = get_string('role', 'core');
        $options[$group] = array();
        foreach (role_get_names(null, ROLENAME_BOTH) as $role) {
            // Allow reset to self too, it may be useful when importing incomplete XML preset.
            $options[$group][$role->id] = $role->localname;
        }

        $group = get_string('archetype', 'core_role');
        $options[$group] = array();
        foreach (get_role_archetypes() as $type) {
            $options[$group][$type] = get_string('archetype'.$type, 'core_role');
        }

        $mform->addElement('header', 'presetheader', get_string('roleresetdefaults', 'core_role'));

        $mform->addElement('selectgroups', 'resettype', get_string('roleresetrole', 'core_role'), $options);

        $mform->addElement('filepicker', 'rolepreset', get_string('rolerepreset', 'core_role'));

        if ($data['roleid']) {
            $mform->addElement('header', 'resetheader', get_string('resetrole', 'core_role'));

            $mform->addElement('advcheckbox', 'shortname', get_string('roleshortname', 'core_role'));
            $mform->addElement('advcheckbox', 'name', get_string('customrolename', 'core_role'));
            $mform->addElement('advcheckbox', 'description', get_string('customroledescription', 'core_role'));
            $mform->addElement('advcheckbox', 'archetype', get_string('archetype', 'core_role'));
            $mform->addElement('advcheckbox', 'contextlevels', get_string('maybeassignedin', 'core_role'));
            $mform->addElement('advcheckbox', 'allowassign', get_string('allowassign', 'core_role'));
            $mform->addElement('advcheckbox', 'allowoverride', get_string('allowoverride', 'core_role'));
            $mform->addElement('advcheckbox', 'allowswitch', get_string('allowswitch', 'core_role'));
            $mform->addElement('advcheckbox', 'allowview', get_string('allowview', 'core_role'));
            $mform->addElement('advcheckbox', 'permissions', get_string('permissions', 'core_role'));
        }

        $mform->addElement('hidden', 'roleid');
        $mform->setType('roleid', PARAM_INT);

        $mform->addElement('hidden', 'action');
        $mform->setType('action', PARAM_ALPHA);

        $mform->addElement('hidden', 'return');
        $mform->setType('return', PARAM_ALPHA);

        $this->add_action_buttons(true, get_string('continue', 'core'));

        $this->set_data($data);
    }

    /**
     * Validate this form.
     *
     * @param array $data submitted data
     * @param array $files not used
     * @return array errors
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        if ($files = $this->get_draft_files('rolepreset')) {
            /** @var stored_file $file */
            $file = reset($files);
            $xml = $file->get_content();
            if (!core_role_preset::is_valid_preset($xml)) {
                $errors['rolepreset'] = get_string('invalidpresetfile', 'core_role');
            }
        }

        return $errors;
    }
}
