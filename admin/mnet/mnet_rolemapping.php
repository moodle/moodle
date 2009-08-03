<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once('mnet_rolemapping_form.php');
require_once($CFG->dirroot .'/mnet/lib.php');

$mnethostid = required_param('hostid', PARAM_INT);
$mnetpeer = mnet_get_peer_host($mnethostid);
$PAGE->set_generaltype('form');
$PAGE->set_url('admin/mnet/mnet_rolemapping.php', array('hostid' => $mnethostid));

$pagetitle = get_string('mnetmaproles');
$extranavlinks = array(
        array('name' => 'Administration', 'link' => '', 'type'=> 'title'),
        array('name' => 'Networking', 'link' => '', 'type' => 'title'),
        array('name' => 'Peers', 'link' => $CFG->wwwroot . '/admin/mnet/peers.php', 'type' => 'title'),
        array('name' => $mnetpeer->name, 'link' => $CFG->wwwroot . '/admin/mnet/peers.php?hostid='.$mnethostid, 'type' => 'title'),
        array('name' => $pagetitle, 'link' => '', 'type' => 'title'),
        );
$navigation = build_navigation($extranavlinks);
print_header_simple($pagetitle, $pagetitle, $navigation, '', '', false);

$mform = new rolemapping_form();
$rolessql =
        'SELECT r.id, r.shortname, r.name, rm.remoterole, rm.id as mapid ' .
        'FROM {role} r ' .
        ' LEFT JOIN {mnet_role_mapping} rm ON rm.localrole = r.id AND rm.mnethost = ? ';
$rolesparams = array($mnethostid);
if ($mform->is_cancelled()){
    redirect($CFG->wwwroot . '/admin/mnet/peers.php?hostid=' . $mnethostid, get_string('changescancelled'), 1);
} else if ($fromform=$mform->get_data()) {
    $roles = $DB->get_records_sql($rolessql, $rolesparams);
    $usersinqueue = 0;

    if (!empty($roles)) {
        foreach ($roles as $role) {
            if (isset($fromform->{'rolemapping-' . $role->id})) {
                $newchoice = $fromform->{'rolemapping-' . $role->id};
            } else {
                // User has somehow supplied the form without saying what to do with this role - assume they mean no role mapping:
                $newchoice = -1;
            }
            $toform['rolemapping-' . $role->id] = $newchoice;
            // Don't actually store non-mappings in db:
            if ($newchoice == -1) {
                $newchoice = NULL;
            }
            if ($newchoice === $role->remoterole) {
                // No change in this role mapping, nothing to do
                continue;
            }
            // Role mapping has changed - get everyone with this local role in queue to get their remote role updated.
            $usersinqueue += manage_role_mapping($mnethostid, $role->id);

            $rolemappingobj = new stdclass;
            $rolemappingobj->mnethost = $mnethostid;
            $rolemappingobj->localrole = $role->id;
            $rolemappingobj->remoterole = $newchoice;
            if (isset($role->remoterole)) {
                if ($newchoice === NULL) {
                    $DB->delete_records('mnet_role_mapping', array('id' => $role->mapid));
                } else {
                    $rolemappingobj->id = $role->mapid;
                    $DB->update_record('mnet_role_mapping', $rolemappingobj);
                }
            } else {
                $DB->insert_record('mnet_role_mapping', $rolemappingobj);
            }
        }
    }
    print_string('settingssaved');
    echo '<br />';
    if ($usersinqueue) {
        print_string('mnetusersinqueue', 'moodle', $usersinqueue);
    }

    $toform['hostid'] = $mnethostid;
    $mform->set_data($toform);
    $mform->display();
    print_footer();

} else {
    $toform = array();
    $roles = $DB->get_records_sql($rolessql, $rolesparams);
    if (!empty($roles)) {
        foreach ($roles as $role) {
            if (isset($role->remoterole)) {
                $toform['rolemapping-' . $role->id] = $role->remoterole;
            } else {
                $toform['rolemapping-' . $role->id] = -1;
            }
        }
    }
    $toform['hostid'] = $mnethostid;

    $mform->set_data($toform);
    $mform->display();
    print_footer();

}
?>
