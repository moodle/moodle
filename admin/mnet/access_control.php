<?php // $Id$

// Allows the admin to control user logins from remote moodles.

require_once dirname(dirname(dirname(__FILE__))) . '/config.php';
require_once($CFG->libdir.'/adminlib.php');
include_once($CFG->dirroot.'/mnet/lib.php');

$sort         = optional_param('sort', 'username', PARAM_ALPHA);
$dir          = optional_param('dir', 'ASC', PARAM_ALPHA);
$page         = optional_param('page', 0, PARAM_INT);
$perpage      = optional_param('perpage', 30, PARAM_INT);
$action       = trim(strtolower(optional_param('action', '', PARAM_ALPHA)));

require_login();

admin_externalpage_setup('ssoaccesscontrol');

admin_externalpage_print_header();

if (!extension_loaded('openssl')) {
    print_error('requiresopenssl', 'mnet');
}

$sitecontext = get_context_instance(CONTEXT_SYSTEM);
$sesskey = sesskey();
$formerror = array();

// grab the mnet hosts and remove the localhost
$mnethosts = get_records_menu('mnet_host', '', '', 'name', 'id, name');
if (array_key_exists($CFG->mnet_localhost_id, $mnethosts)) {
    unset($mnethosts[$CFG->mnet_localhost_id]);
}



// process actions
if (!empty($action) and confirm_sesskey()) {
    
    // boot if insufficient permission
    if (!has_capability('moodle/user:delete', $sitecontext)) {
        print_error('nomodifyacl','mnet');
    }

    // fetch the record in question
    $id = required_param('id', PARAM_INT);
    if (!$idrec = get_record('mnet_sso_access_control', 'id', $id)) {
        print_error('recordnoexists','mnet', "$CFG->wwwroot/$CFG->admin/mnet/access_control.php");
    }

    switch ($action) {

        case "delete":
            delete_records('mnet_sso_access_control', 'id', $id);
            redirect('access_control.php', get_string('deleteuserrecord', 'mnet', array($idrec->username, $mnethosts[$idrec->mnet_host_id])));
            break;

        case "acl":
        
            // require the access parameter, and it must be 'allow' or 'deny'
            $accessctrl = trim(strtolower(required_param('accessctrl', PARAM_ALPHA)));
            if ($accessctrl != 'allow' and $accessctrl != 'deny') {
                print_error('invalidaccessparam', 'mnet', "$CFG->wwwroot/$CFG->admin/mnet/access_control.php");
            }

            if (mnet_update_sso_access_control($idrec->username, $idrec->mnet_host_id, $accessctrl)) {
                if ($accessctrl == 'allow') {
                    redirect('access_control.php', get_string('ssl_acl_allow','mnet', array($idrec->username, $mnethosts[$idrec->mnet_host_id])));
                } elseif ($accessctrl == 'deny') {
                    redirect('access_control.php', get_string('ssl_acl_deny','mnet', array($idrec->username, $mnethosts[$idrec->mnet_host_id])));
                }
            }
            break;

        default:
            print_error('invalidactionparam', 'mnet', "$CFG->wwwroot/$CFG->admin/mnet/access_control.php");
    }
}



// process the form results
if ($form = data_submitted() and confirm_sesskey()) {

    // check permissions and verify form input
    if (!has_capability('moodle/user:delete', $sitecontext)) {
        print_error('nomodifyacl','mnet', "$CFG->wwwroot/$CFG->admin/mnet/access_control.php");
    }
    if (empty($form->username)) {
        $formerror['username'] = get_string('enterausername','mnet');
    }
    if (empty($form->mnet_host_id)) {
        $formerror['mnet_host_id'] = get_string('selectahost','mnet');
    }
    if (empty($form->accessctrl)) {
        $formerror['accessctrl'] = get_string('selectaccesslevel','mnet'); ;
    }

    // process if there are no errors
    if (count($formerror) == 0) {

        // username can be a comma separated list
        $usernames = explode(',', $form->username);

        foreach ($usernames as $username) {
            $username = trim(moodle_strtolower($username));
            if (!empty($username)) {
                if (mnet_update_sso_access_control($username, $form->mnet_host_id, $form->accessctrl)) {
                    if ($form->accessctrl == 'allow') {
                        redirect('access_control.php', get_string('ssl_acl_allow','mnet', array($username, $mnethosts[$form->mnet_host_id])));
                    } elseif ($form->accessctrl == 'deny') {
                        redirect('access_control.php', get_string('ssl_acl_deny','mnet', array($username, $mnethosts[$form->mnet_host_id])));
                    }
                }
            }
        }
    }
    exit;
}

// Explain
print_box(get_string('ssoacldescr','mnet'));
// Are the needed bits enabled?
$warn = '';
if (empty($CFG->mnet_dispatcher_mode) || $CFG->mnet_dispatcher_mode !== 'strict') {
    $warn = '<p>' . get_string('mnetdisabled','mnet') .'</p>';
}

if (!is_enabled_auth('mnet')) {
    $warn .= '<p>' .  get_string('authmnetdisabled','mnet').'</p>';
}

if (get_config('auth/mnet', 'auto_add_remote_users') != true) {
    $warn .= '<p>' .  get_string('authmnetautoadddisabled','mnet').'</p>';
}
if (!empty($warn)) {
    $warn = '<p>' .  get_string('ssoaclneeds','mnet').'</p>' . $warn;
    print_box($warn);
}
// output the ACL table
$columns = array("username", "mnet_host_id", "access", "delete");
$headings = array();
$string = array('username'     => get_string('username'),
                'mnet_host_id' => get_string('remotehost', 'mnet'),
                'access'       => get_string('accesslevel', 'mnet'),
                'delete'       => get_string('delete'));
foreach ($columns as $column) {
    if ($sort != $column) {
        $columnicon = "";
        $columndir = "ASC";
    } else {
        $columndir = $dir == "ASC" ? "DESC" : "ASC";
        $columnicon = $dir == "ASC" ? "down" : "up";
        $columnicon = " <img src=\"$CFG->pixpath/t/$columnicon.gif\" alt=\"\" />";
    }
    $headings[$column] = "<a href=\"?sort=$column&amp;dir=$columndir&amp;\">".$string[$column]."</a>$columnicon";
}
$headings['delete'] = '';
$acl = get_records('mnet_sso_access_control', '', '', "$sort $dir", '*'); //, $page * $perpage, $perpage);
$aclcount = count_records('mnet_sso_access_control');

if (!$acl) {
    print_heading(get_string('noaclentries','mnet'));
    $table = NULL;
} else {
    $table->head = $headings;
    $table->align = array('left', 'left', 'center');
    $table->width = "95%";
    foreach ($acl as $aclrecord) {
        if ($aclrecord->accessctrl == 'allow') {
            $accesscolumn = get_string('allow', 'mnet')
                . " (<a href=\"?id={$aclrecord->id}&amp;action=acl&amp;accessctrl=deny&amp;sesskey={$USER->sesskey}\">"
                . get_string('deny', 'mnet') . "</a>)";
        } else {
            $accesscolumn = get_string('deny', 'mnet')
                . " (<a href=\"?id={$aclrecord->id}&amp;action=acl&amp;accessctrl=allow&amp;sesskey={$USER->sesskey}\">"
                . get_string('allow', 'mnet') . "</a>)";
        }
        $deletecolumn = "<a href=\"?id={$aclrecord->id}&amp;action=delete&amp;sesskey={$USER->sesskey}\">"
                . get_string('delete') . "</a>";
        $table->data[] = array ($aclrecord->username, $aclrecord->mnet_host_id, $accesscolumn, $deletecolumn);
    }
}

if (!empty($table)) {
    print_table($table);
    echo '<p>&nbsp;</p>';
    print_paging_bar($aclcount, $page, $perpage, "?sort=$sort&amp;dir=$dir&amp;perpage=$perpage&amp;");
}



// output the add form
print_simple_box_start('center','90%','','20');

?>
 <div class="mnetaddtoaclform"> 
  <form id="mnetaddtoacl" method="post">
    <input type="hidden" name="sesskey" value="<?php echo $sesskey; ?>" />
<?php

// enter a username
echo get_string('username') . ":\n";
if (!empty($formerror['username'])) {
    echo '<span class="error"> * </span>';
}
echo '<input type="text" name="username" size="20" maxlength="100" />';

// choose a remote host
echo " " . get_string('remotehost', 'mnet') . ":\n";
if (!empty($formerror['mnet_host_id'])) {
    echo '<span class="error"> * </span>';
}
choose_from_menu($mnethosts, 'mnet_host_id');

// choose an access level
echo " " . get_string('accesslevel', 'mnet') . ":\n";
if (!empty($formerror['accessctrl'])) {
    echo '<span class="error"> * </span>';
}
$accessmenu['allow'] = get_string('allow', 'mnet');
$accessmenu['deny'] = get_string('deny', 'mnet');
choose_from_menu($accessmenu, 'accessctrl');

// submit button
echo '<input type="submit" value="' . get_string('addtoacl', 'mnet') . '" />';
echo "</form></div>\n";

// print errors
foreach ($formerror as $error) {
    echo "<br><span class=\"error\">$error<span>";
}

print_simple_box_end();
admin_externalpage_print_footer();

?>
