<?php
require_once ('../../config.php');
require_once ($CFG->libdir . '/formslib.php');

class rolemapping_form extends moodleform {

    function definition() {

        global $CFG;
        $mform =& $this->_form;
        $mform->addElement('header', 'mapping', get_string('mnetrolemapping'));
        $mform->addElement('static', 'instructions', '',get_string('mnetrolemappinginstructions'), ' ');
        $mform->addElement('hidden', 'hostid', 'yes');
        $this->add_action_buttons();

    }

    function definition_after_data() {
        global $CFG, $DB;
        require_once($CFG->dirroot . '/mnet/lib.php');
        $mform =& $this->_form;
        $mnethostid = $mform->getElementValue('hostid');
        $mnethost = $DB->get_record('mnet_host', array('id' => $mnethostid));
        $remoteroleoptions = array();

        $rolessql = 'SELECT r.id, r.shortname, r.name, rm.remoterole, rm.id as mapid ' .
                'FROM {role} r ' .
                ' LEFT JOIN {mnet_role_mapping} rm ON rm.localrole = r.id and rm.mnethost = ? ' .
                'ORDER BY r.id';
        $rolesparams = array($mnethostid);
        $roles = $DB->get_records_sql($rolessql, $rolesparams);

        // Determine if we already have a role mapped to a remote role (mappings to default role don't count)
        $actualremoterole = false;
        foreach ($roles as $role) {
            if (!empty($role->remoterole)) {
                $actualremoterole = true;
                break;
            }
        }

        $remotedefaultrole = false;
        if (!$actualremoterole) {
            // Remote peer may be old & unable to tell us what roles it shares
            // See if the mnet peer has upgraded to new mnet code since we last checked
            // If it knows how to tell us it's default role, it also knows how to tell us what roles it shares w/ us
            $remotedefaultrole = mnet_get_default_role($mnethostid);
            if (!empty($remotedefaultrole)) {
                $DB->set_field('mnet_role_mapping', 'remoterole', $remotedefaultrole->id,
                        array('remoterole' => 0, 'mnethost' => $mnethostid));
                $actualremoterole = true;
            }
        }
        if ($actualremoterole) {
            $remoteroles = mnet_get_allocatable_roles($mnethostid);
            foreach ($remoteroles as $remoterole) {
                $remoteroleoptions[$remoterole->id] = $mnethost->name . ' - ' . $remoterole->shortname;
            }
        } else {
            //Still talking to an mnet peer that does't publish more than one role,
            // and doesn't know how to tell us what that role is:
            $remoteroleoptions[0] = $mnethost->name . ' - Default Role';
        }

        $remoteroleoptions[-1] = 'No Role';
        foreach ($roles as $role) {
            $mform->addElement('select', 'rolemapping-' . $role->id,
                    $role->name . ' (' . $role->shortname . ') ',
                    $remoteroleoptions);
            if (!empty($role->remoterole) && !isset($remoteroleoptions[$role->remoterole])) {
                //The remote role that this role is currently mapped to isn't shared any more
                $DB->delete_records('mnet_role_mapping', array('id' => $role->mapid));
                manage_role_mapping($mnethostid, $role->id);
            }
        }
        $mform->removeElement('buttonar');
        $this->add_action_buttons();
    }
}
?>
