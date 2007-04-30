<?php
/**
 * this page defines what roles can access (grant user that role and override that roles'
 * capabilities in different context. For example, we can say that Teachers can only grant
 * student role or modify student role's capabilities. Note that you need both the right
 * capability moodle/role:assign or moodle/role:manage and this database table roles_deny_grant
 * to be able to grant roles. If a user has moodle/role:manage at site level assignment
 * then he can modify the roles_allow_assign table via this interface.
 */
    require_once('../../config.php');
    require_once($CFG->libdir.'/adminlib.php');

    admin_externalpage_setup('defineroles');


    $sitecontext = get_context_instance(CONTEXT_SYSTEM, SITEID);
    require_capability('moodle/role:manage', $sitecontext);

/// form processiong here

/// get all roles

    $roles = get_all_roles();

    if ($grant = data_submitted()) {

        foreach ($grant as $grole => $val) {
            if ($grole == 'dummy') {
                continue;
            }

            $string = explode('_', $grole);
            $temp[$string[1]][$string[2]] = 1; // if set, means can access
        }

// if current assignment is in data_submitted, ignore, else, write deny into db
        foreach ($roles as $srole) {
            foreach ($roles as $trole) {
                if (isset($temp[$srole->id][$trole->id])) { // if set, need to write to db
                    if (!$record = get_record('role_allow_assign', 'roleid', $srole->id, 'allowassign', $trole->id)) {
                        allow_assign($srole->id, $trole->id);
                    }
                } else { //if set, means can access, attempt to remove it from db
                    delete_records('role_allow_assign', 'roleid', $srole->id, 'allowassign', $trole->id);
                }
            }
        }
    }
/// displaying form here

    admin_externalpage_print_header();

    $currenttab='allowassign';
    require_once('managetabs.php');

    $table->tablealign = 'center';
    $table->cellpadding = 5;
    $table->cellspacing = 0;
    $table->width = '90%';
    $table->align[] = 'right';

/// get all the roles identifier
    foreach ($roles as $role) {
        $rolesname[] = format_string($role->name);
        $roleids[] = $role->id;
        $table->align[] = 'center';
        $table->wrap[] = 'nowrap';
    }

    $table->head = array_merge(array(''), $rolesname);

    foreach ($roles as $role) {
        $beta = get_box_list($role->id, $roleids);
        $table->data[] = array_merge(array(format_string($role->name)), $beta);
    }

    print_simple_box(get_string('configallowassign', 'admin'), 'center');

    echo '<form action="allowassign.php" method="post">';
    print_table($table);
    echo '<div class="buttons"><input type="submit" value="'.get_string('savechanges').'"/>';
    echo '<input type="hidden" name="dummy" value="1" />'; // this is needed otherwise we do not know a form has been submitted
    echo '</div></form>';

    admin_externalpage_print_footer();



function get_box_list($roleid, $arraylist){

    foreach ($arraylist as $targetid) {
        if (get_record('role_allow_assign', 'roleid', $roleid, 'allowassign', $targetid)) {
            $array[] = '<input type="checkbox" name="s_'.$roleid.'_'.$targetid.'" value="1" checked="checked"/>';
        } else {
            $array[] = '<input type="checkbox" name="s_'.$roleid.'_'.$targetid.'" value="1" />';
        }
    }
    return $array;
}
?>
