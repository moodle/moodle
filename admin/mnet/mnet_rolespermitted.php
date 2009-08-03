<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once('mnet_rolespermitted_form.php');
require_once($CFG->dirroot .'/mnet/lib.php');

$mnethostid = required_param('hostid', PARAM_INT);
$mnetpeer = mnet_get_peer_host($mnethostid);
$PAGE->set_generaltype('form');
$PAGE->set_url('admin/mnet/mnet_rolespermitted.php', array('hostid' => $mnethostid));

$pagetitle = get_string('mnetauthorisemnetroles');
$extranavlinks = array(
        array('name' => 'Administration', 'link' => '', 'type' => 'title'),
        array('name' => 'Networking', 'link' => '', 'type' => 'title'),
        array('name' => 'Peers', 'link' => $CFG->wwwroot . '/admin/mnet/peers.php', 'type' => 'title'),
        array('name' => $mnetpeer->name, 'link' => $CFG->wwwroot . '/admin/mnet/peers.php?hostid='.$mnethostid, 'type' => 'title'),
        array('name' => $pagetitle, 'link' => '', 'type' => 'title'),
        );
$navigation = build_navigation($extranavlinks);
print_header_simple($pagetitle, $pagetitle, $navigation, '', '', false);
$rolessql =
        'SELECT ' .
        ' r.id, r.shortname, r.name, rp.localrole as prepublished ' .
        'FROM {role} r ' .
        ' LEFT JOIN {mnet_role_published} rp ON rp.localrole = r.id and rp.mnethost = ? ';
$rolesparams = array($mnethostid);

$mform = new rolespermitted_form();
if ($mform->is_cancelled()){
   redirect($CFG->wwwroot . '/admin/mnet/peers.php?hostid=' . $mnethostid, get_string('changescancelled'), 1);
} else if ($fromform=$mform->get_data()) {
    $roles = $DB->get_records_sql($rolessql, $rolesparams);
    $rolepublication = new stdclass();
    $rolepublication->mnethost = $mnethostid;
    foreach ($roles as $role) {
        $rolepublication->localrole = $role->id;
        if(isset($fromform->{$role->shortname})) {
            // Role checkbox was ticked - add publication entry if not already present.
            if (empty($role->prepublished)) {
                $DB->insert_record('mnet_role_published', $rolepublication);
                $toform[$role->shortname] = 1;
            }
        } else {
            // Role checkbox was not ticked - delete its publication entry (if present)
            if (!empty($role->prepublished)) {
                $DB->delete_records('mnet_role_published',
                        array('mnethost' => $rolepublication->mnethost,
                        'localrole' => $rolepublication->localrole));
                unassign_role_peer($rolepublication->localrole, $rolepublication->mnethost);
            }
        }
    }
    print_string('settingssaved');
    $toform['hostid'] = $mnethostid;
    $mform->set_data($toform);
    $mform->display();
    print_footer();

} else {
    $toform = array();

    $roles = $DB->get_records_sql($rolessql, $rolesparams);
    foreach ($roles as $role) {
        if (!empty($role->prepublished)) {
            $toform[$role->shortname] = 1;
        }
    }
    $toform['hostid'] = $mnethostid;

    $mform->set_data($toform);
    $mform->display();
    print_footer();

}
?>
