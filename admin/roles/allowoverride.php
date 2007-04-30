<?php
/**
 * this page defines what roles can override (override roles in different context. For example,
 * we can say that Admin can override teacher roles in a course
 * To be able to override roles. If a user has moodle/role:override at context level
 * and be in the roles_allow_override table.
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
                    if (!$record = get_record('role_allow_override', 'roleid', $srole->id, 'allowoverride', $trole->id)) {
                        allow_override($srole->id, $trole->id);
                    }
                } else { //if set, means can access, attempt to remove it from db
                    delete_records('role_allow_override', 'roleid', $srole->id, 'allowoverride', $trole->id);
                }
            }
        }
    }
/// displaying form here

    admin_externalpage_print_header();

    $currenttab='allowoverride';
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

    print_simple_box(get_string('configallowoverride', 'admin'), 'center');

    echo '<form action="allowoverride.php" method="post">';
    print_table($table);
    echo '<div class="buttons"><input type="submit" value="'.get_string('savechanges').'"/>';
    echo '<input type="hidden" name="dummy" value="1" />'; // this is needed otherwise we do not know a form has been submitted
    echo '</div></form>';

    admin_externalpage_print_footer();

// returns array
function get_box_list($roleid, $arraylist){

    foreach ($arraylist as $targetid) {
        if (get_record('role_allow_override', 'roleid', $roleid, 'allowoverride', $targetid)) {
            $array[] = '<input type="checkbox" name="s_'.$roleid.'_'.$targetid.'" value="1" checked="checked"/>';
        } else {
            $array[] = '<input type="checkbox" name="s_'.$roleid.'_'.$targetid.'" value="1" />';
        }
    }
    return $array;
}
?>
