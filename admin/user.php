<?php // $Id$

    require_once('../config.php');
    require_once($CFG->libdir.'/adminlib.php');

    $delete       = optional_param('delete', 0, PARAM_INT);
    $confirm      = optional_param('confirm', '', PARAM_ALPHANUM);   //md5 confirmation hash
    $confirmuser  = optional_param('confirmuser', 0, PARAM_INT);
    $sort         = optional_param('sort', 'name', PARAM_ALPHA);
    $dir          = optional_param('dir', 'ASC', PARAM_ALPHA);
    $page         = optional_param('page', 0, PARAM_INT);
    $perpage      = optional_param('perpage', 30, PARAM_INT);        // how many per page
    $search       = trim(optional_param('search', '', PARAM_RAW));
    $lastinitial  = optional_param('lastinitial', '', PARAM_CLEAN);  // only show students with this last initial
    $firstinitial = optional_param('firstinitial', '', PARAM_CLEAN); // only show students with this first initial
    $ru           = optional_param('ru', '2', PARAM_INT);            // show remote users
    $lu           = optional_param('lu', '2', PARAM_INT);            // show local users
    $acl          = optional_param('acl', '0', PARAM_INT);           // id of user to tweak mnet ACL (requires $access)

    $adminroot = admin_get_root();
    admin_externalpage_setup('editusers', $adminroot);

    // Let's see if we have *any* mnet users. Just ask for a single record
    $mnet_users = get_records_select('user', " auth='mnet' AND mnethostid != '{$CFG->mnet_localhost_id}' ", '', '*', '0', '1');
    if(is_array($mnet_users) && count($mnet_users) > 0) {
        $mnet_auth_users = true;
    } else {
        $mnet_auth_users = false;
    }
    
    if($mnet_auth_users) {
        // Determine which users we are looking at (local, remote, or both). Start with both.
        if (!isset($_SESSION['admin-user-remoteusers'])) {
            $_SESSION['admin-user-remoteusers'] = 1;
            $_SESSION['admin-user-localusers']  = 1;
        }
        if ($ru == 0 or $ru == 1) {
            $_SESSION['admin-user-remoteusers'] = $ru;
        }
        if ($lu == 0 or $lu == 1) {
             $_SESSION['admin-user-localusers'] = $lu;
        }
        $remoteusers = $_SESSION['admin-user-remoteusers'];
        $localusers  = $_SESSION['admin-user-localusers'];
    
        // if neither remote nor local, set to sensible local only
        if (!$remoteusers and !$localusers) {
            $_SESSION['admin-user-localusers'] = 1;
            $localusers = 1;
        }
    }

    $sitecontext = get_context_instance(CONTEXT_SYSTEM, SITEID);
    $site = get_site();

    if (!has_capability('moodle/user:update', $sitecontext) and !has_capability('moodle/user:delete', $sitecontext)) {
        error('You do not have the required permission to edit/delete users.');
    }

    $stredit   = get_string('edit');
    $strdelete = get_string('delete');
    $strdeletecheck = get_string('deletecheck');
    $strsearch = get_string('search');
    $strshowallusers = get_string('showallusers');

    if (empty($CFG->loginhttps)) {
        $securewwwroot = $CFG->wwwroot;
    } else {
        $securewwwroot = str_replace('http:','https:',$CFG->wwwroot);
    }

    admin_externalpage_print_header($adminroot);

    if ($confirmuser and confirm_sesskey()) {
        if (!$user = get_record('user', 'id', $confirmuser)) {
            error("No such user!");
        }

        $confirmeduser = new object();
        $confirmeduser->id = $confirmuser;
        $confirmeduser->confirmed = 1;
        $confirmeduser->timemodified = time();

        if (update_record('user', $confirmeduser)) {
            notify(get_string('userconfirmed', '', fullname($user, true)) );
        } else {
            notify(get_string('usernotconfirmed', '', fullname($user, true)));
        }

    } else if ($delete and confirm_sesskey()) {              // Delete a selected user, after confirmation

        if (!has_capability('moodle/user:delete', $sitecontext)) {
            error('You do not have the required permission to delete a user.');
        }

        if (!$user = get_record('user', 'id', $delete)) {
            error("No such user!");
        }

        $primaryadmin = get_admin();
        if ($user->id == $primaryadmin->id) {
            error("You are not allowed to delete the primary admin user!");
        }

        if ($confirm != md5($delete)) {
            $fullname = fullname($user, true);
            print_heading(get_string('deleteuser', 'admin'));
            $optionsyes = array('delete'=>$delete, 'confirm'=>md5($delete), 'sesskey'=>sesskey());
            notice_yesno(get_string('deletecheckfull', '', "'$fullname'"), 'user.php', 'user.php', $optionsyes, NULL, 'post', 'get');
            admin_externalpage_print_footer($adminroot);
            die;
        } else if (data_submitted() and !$user->deleted) {
            //following code is also used in auth sync scripts
            $updateuser = new object();
            $updateuser->id           = $user->id;
            $updateuser->deleted      = 1;
            $updateuser->username     = addslashes("$user->email.".time());  // Remember it just in case
            $updateuser->email        = '';               // Clear this field to free it up
            $updateuser->idnumber     = '';               // Clear this field to free it up
            $updateuser->timemodified = time();
            if (update_record('user', $updateuser)) {
                // not sure if this is needed. unenrol_student($user->id);  // From all courses
                delete_records('role_assignments', 'userid', $user->id); // unassign all roles
                // remove all context assigned on this user?
                notify(get_string('deletedactivity', '', fullname($user, true)) );
            } else {
                notify(get_string('deletednot', '', fullname($user, true)));
            }
        }
    } else if ($acl and confirm_sesskey()) {
        if (!has_capability('moodle/user:delete', $sitecontext)) {
            // TODO: this should be under a separate capability
            error('You are not permitted to modify the MNET access control list.');
        }
        if (!$user = get_record('user', 'id', $acl)) {
            error("No such user.");
        }
        if (!is_mnet_remote_user($user)) {
            error('Users in the MNET access control list must be remote MNET users.');
        }
        $accessctrl = strtolower(required_param('accessctrl', PARAM_ALPHA));
        if ($accessctrl != 'allow' and $accessctrl != 'deny') {
            error('Invalid access parameter.');
        }
        $aclrecord = get_record('mnet_sso_access_control', 'username', $user->username, 'mnet_host_id', $user->mnethostid);
        if (empty($aclrecord)) {
            $aclrecord = new object();
            $aclrecord->mnet_host_id = $user->mnethostid;
            $aclrecord->username = $user->username;
            $aclrecord->accessctrl = $accessctrl;
            if (!insert_record('mnet_sso_access_control', $aclrecord)) {
                error("Database error - Couldn't modify the MNET access control list.");
            }
        } else {
            $aclrecord->accessctrl = $accessctrl;
            if (!update_record('mnet_sso_access_control', $aclrecord)) {
                error("Database error - Couldn't modify the MNET access control list.");
            }
        }
        $mnethosts = get_records('mnet_host', '', '', 'id', 'id,wwwroot,name');
        notify("MNET access control list updated: username '$user->username' from host '"
                . $mnethosts[$user->mnethostid]->name
                . "' access now set to '$accessctrl'.");
    }

    // Carry on with the user listing

    $columns = array("firstname", "lastname", "email", "city", "country", "lastaccess");

    foreach ($columns as $column) {
        $string[$column] = get_string("$column");
        if ($sort != $column) {
            $columnicon = "";
            if ($column == "lastaccess") {
                $columndir = "DESC";
            } else {
                $columndir = "ASC";
            }
        } else {
            $columndir = $dir == "ASC" ? "DESC":"ASC";
            if ($column == "lastaccess") {
                $columnicon = $dir == "ASC" ? "up":"down";
            } else {
                $columnicon = $dir == "ASC" ? "down":"up";
            }
            $columnicon = " <img src=\"$CFG->pixpath/t/$columnicon.gif\" alt=\"\" />";

        }
        $$column = "<a href=\"user.php?sort=$column&amp;dir=$columndir&amp;search=".urlencode(stripslashes($search))."&amp;firstinitial=$firstinitial&amp;lastinitial=$lastinitial\">".$string[$column]."</a>$columnicon";
    }

    if ($sort == "name") {
        $sort = "firstname";
    }
    
    // tell the query which users we are looking at (local, remote, or both)
    $remotewhere = '';
    if($mnet_auth_users && ($localusers XOR $remoteusers)) {
        if ($localusers) {
            $remotewhere .= " and mnethostid = {$CFG->mnet_localhost_id} ";
        } else {
            $remotewhere .= " and mnethostid <> {$CFG->mnet_localhost_id} ";
        }
    }
    
    $users = get_users_listing($sort, $dir, $page*$perpage, $perpage, $search, $firstinitial, $lastinitial, $remotewhere);
    $usercount = get_users(false);
    $usersearchcount = get_users(false, $search, true, "", "", $firstinitial, $lastinitial);

    if ($search or $firstinitial or $lastinitial) {
        print_heading("$usersearchcount / $usercount ".get_string('users'));
        $usercount = $usersearchcount;
    } else {
        print_heading("$usercount ".get_string('users'));
    }

    $alphabet = explode(',', get_string('alphabet'));
    $strall = get_string('all');


    /// Bar of first initials

    echo "<p style=\"text-align:center\">";
    echo get_string("firstname")." : ";
    if ($firstinitial) {
        echo " <a href=\"user.php?sort=firstname&amp;dir=ASC&amp;".
             "perpage=$perpage&amp;lastinitial=$lastinitial\">$strall</a> ";
    } else {
        echo " <b>$strall</b> ";
    }
    foreach ($alphabet as $letter) {
        if ($letter == $firstinitial) {
            echo " <b>$letter</b> ";
        } else {
            echo " <a href=\"user.php?sort=firstname&amp;dir=ASC&amp;".
                 "perpage=$perpage&amp;lastinitial=$lastinitial&amp;firstinitial=$letter\">$letter</a> ";
        }
    }
    echo "<br />";

    /// Bar of last initials

    echo get_string("lastname")." : ";
    if ($lastinitial) {
        echo " <a href=\"user.php?sort=lastname&amp;dir=ASC&amp;".
             "perpage=$perpage&amp;firstinitial=$firstinitial\">$strall</a> ";
    } else {
        echo " <b>$strall</b> ";
    }
    foreach ($alphabet as $letter) {
        if ($letter == $lastinitial) {
            echo " <b>$letter</b> ";
        } else {
            echo " <a href=\"user.php?sort=lastname&amp;dir=ASC&amp;".
                 "perpage=$perpage&amp;firstinitial=$firstinitial&amp;lastinitial=$letter\">$letter</a> ";
        }
    }
    echo "</p>";

    print_paging_bar($usercount, $page, $perpage,
            "user.php?sort=$sort&amp;dir=$dir&amp;perpage=$perpage&amp;firstinitial=$firstinitial&amp;lastinitial=$lastinitial&amp;search=".urlencode(stripslashes($search))."&amp;");

    flush();


    if (!$users) {
        $match = array();
        if ($search !== '') {
           $match[] = s($search);
        }
        if ($firstinitial) {
           $match[] = get_string('firstname').": $firstinitial"."___";
        }
        if ($lastinitial) {
           $match[] = get_string('lastname').": $lastinitial"."___";
        }
        $matchstring = implode(", ", $match);
        print_heading(get_string('nousersmatching', '', $matchstring));

        $table = NULL;

    } else {

        $countries = get_list_of_countries();
        if (empty($mnethosts)) {
            $mnethosts = get_records('mnet_host', '', '', 'id', 'id,wwwroot,name');
        }

        foreach ($users as $key => $user) {
            if (!empty($user->country)) {
                $users[$key]->country = $countries[$user->country];
            }
        }
        if ($sort == "country") {  // Need to resort by full country name, not code
            foreach ($users as $user) {
                $susers[$user->id] = $user->country;
            }
            asort($susers);
            foreach ($susers as $key => $value) {
                $nusers[] = $users[$key];
            }
            $users = $nusers;
        }

        $mainadmin = get_admin();

        $table->head = array ("$firstname / $lastname", $email, $city, $country, $lastaccess, "", "", "");
        $table->align = array ("left", "left", "left", "left", "left", "center", "center", "center");
        $table->width = "95%";
        foreach ($users as $user) {
            if ($user->username == 'guest') {
                continue; // do not dispaly dummy new user and guest here
            }

            if ($user->id == $USER->id) {
                $deletebutton = "";
            } else {
                if (has_capability('moodle/user:delete', $sitecontext)) {
                    $deletebutton = "<a href=\"user.php?delete=$user->id&amp;sesskey=$USER->sesskey\">$strdelete</a>";
                } else {
                    $deletebutton ="";
                }
            }

            if (has_capability('moodle/user:update', $sitecontext) and ($user->id==$USER->id or $user->id != $mainadmin->id) and !is_mnet_remote_user($user)) {
                $editbutton = "<a href=\"$securewwwroot/user/editadvanced.php?id=$user->id&amp;course=$site->id\">$stredit</a>";
                if ($user->confirmed == 0) {
                    $confirmbutton = "<a href=\"user.php?confirmuser=$user->id&amp;sesskey=$USER->sesskey\">" . get_string('confirm') . "</a>";
                } else {
                    $confirmbutton = "";
                }
            } else {
                $editbutton ="";
                if ($user->confirmed == 0) {
                    $confirmbutton = "<span class=\"dimmed_text\">".get_string('confirm')."</span>";
                } else {
                    $confirmbutton = "";
                }
            }

            // for remote users, shuffle columns around and display MNET stuff
            if (is_mnet_remote_user($user)) {
                $accessctrl = 'allow';
                if ($acl = get_record('mnet_sso_access_control', 'username', $user->username, 'mnet_host_id', $user->mnethostid)) {
                    $accessctrl = $acl->accessctrl;
                }
                $changeaccessto = ($accessctrl == 'deny' ? 'allow' : 'deny');
                // delete button in confirm column - remote users should already be confirmed
                // TODO: no delete for remote users, for now. new userid, delete flag, unique on username/host...
                $confirmbutton = "";
                // ACL in delete column
                $deletebutton = get_string($accessctrl, 'mnet');
                if (has_capability('moodle/user:delete', $sitecontext)) {
                    // TODO: this should be under a separate capability
                    $deletebutton .= " (<a href=\"?acl={$user->id}&amp;accessctrl=$changeaccessto&amp;sesskey={$USER->sesskey}\">"
                            . get_string($changeaccessto, 'mnet') . " access</a>)";
                }
                // mnet info in edit column
                if (isset($mnethosts[$user->mnethostid])) {
                    $editbutton = $mnethosts[$user->mnethostid]->name;
                }
            }

            if ($user->lastaccess) {
                $strlastaccess = format_time(time() - $user->lastaccess);
            } else {
                $strlastaccess = get_string('never');
            }
            $fullname = fullname($user, true);

            $table->data[] = array ("<a href=\"../user/view.php?id=$user->id&amp;course=$site->id\">$fullname</a>",
                                "$user->email",
                                "$user->city",
                                "$user->country",
                                $strlastaccess,
                                $editbutton,
                                $deletebutton,
                                $confirmbutton);
        }
    }

    if($mnet_auth_users) {
        echo "<p style=\"text-align:center\">";
        if ($localusers == 1 && $remoteusers == 1) {
            echo '<a href="?lu=0">'.get_string('hidelocal','mnet').'</a> | ';
        } elseif ($localusers == 0)  {
            echo '<a href="?lu=1">'.get_string('showlocal','mnet').'</a> | ';
        } else {
            echo get_string('hidelocal','mnet').' | ';
        }
        if ($localusers == 1 && $remoteusers == 1) {
            echo '<a href="?ru=0">'.get_string('hideremote','mnet').'</a>';
        } elseif ($remoteusers == 0) {
            echo '<a href="?ru=1">'.get_string('showremote','mnet').'</a>';
        } else {
            echo get_string('hideremote','mnet');
        }
        echo "</p>";
    }

    echo "<table class=\"searchbox\" style=\"margin-left:auto;margin-right:auto\" cellpadding=\"10\"><tr><td>";
    echo "<form action=\"user.php\" method=\"get\"><fieldset class=\"invisiblefieldset\">";
    echo "<input type=\"text\" name=\"search\" value=\"".s($search, true)."\" size=\"20\" />";
    echo "<input type=\"submit\" value=\"$strsearch\" />";
    if ($search) {
        echo "<input type=\"button\" onclick=\"document.location='user.php';\" value=\"$strshowallusers\" />";
    }
    echo "</fieldset></form>";
    echo "</td></tr></table>";

    if (has_capability('moodle/user:create', $sitecontext)) {
        print_heading('<a href="'.$securewwwroot.'/user/editadvanced.php?id=-1">'.get_string('addnewuser').'</a>');
    }
    if (!empty($table)) {
        print_table($table);
        print_paging_bar($usercount, $page, $perpage,
                         "user.php?sort=$sort&amp;dir=$dir&amp;perpage=$perpage".
                         "&amp;firstinitial=$firstinitial&amp;lastinitial=$lastinitial&amp;search=".urlencode(stripslashes($search))."&amp;");
        if (has_capability('moodle/user:create', $sitecontext)) {
            print_heading('<a href="'.$securewwwroot.'/user/editadvanced.php?id=-1">'.get_string('addnewuser').'</a>');
        }
    }


    admin_externalpage_print_footer($adminroot);


?>
