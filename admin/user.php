<?php // $Id$

    require_once('../config.php');
    require_once($CFG->libdir.'/adminlib.php');
    require_once($CFG->dirroot.'/user/filters/lib.php');

    $delete       = optional_param('delete', 0, PARAM_INT);
    $confirm      = optional_param('confirm', '', PARAM_ALPHANUM);   //md5 confirmation hash
    $confirmuser  = optional_param('confirmuser', 0, PARAM_INT);
    $sort         = optional_param('sort', 'name', PARAM_ALPHA);
    $dir          = optional_param('dir', 'ASC', PARAM_ALPHA);
    $page         = optional_param('page', 0, PARAM_INT);
    $perpage      = optional_param('perpage', 30, PARAM_INT);        // how many per page
    $ru           = optional_param('ru', '2', PARAM_INT);            // show remote users
    $lu           = optional_param('lu', '2', PARAM_INT);            // show local users
    $acl          = optional_param('acl', '0', PARAM_INT);           // id of user to tweak mnet ACL (requires $access)


    admin_externalpage_setup('editusers');

    $sitecontext = get_context_instance(CONTEXT_SYSTEM);
    $site = get_site();

    if (!has_capability('moodle/user:update', $sitecontext) and !has_capability('moodle/user:delete', $sitecontext)) {
        error('You do not have the required permission to edit/delete users.');
    }

    $stredit   = get_string('edit');
    $strdelete = get_string('delete');
    $strdeletecheck = get_string('deletecheck');
    $strshowallusers = get_string('showallusers');

    if (empty($CFG->loginhttps)) {
        $securewwwroot = $CFG->wwwroot;
    } else {
        $securewwwroot = str_replace('http:','https:',$CFG->wwwroot);
    }

    admin_externalpage_print_header();

    if ($confirmuser and confirm_sesskey()) {
        if (!$user = get_record('user', 'id', $confirmuser)) {
            error("No such user!", '', true);
        }

        $auth = get_auth_plugin($user->auth);

        $result = $auth->user_confirm(addslashes($user->username), addslashes($user->secret));

        if ($result == AUTH_CONFIRM_OK or $result == AUTH_CONFIRM_ALREADY) {
            notify(get_string('userconfirmed', '', fullname($user, true)) );
        } else {
            notify(get_string('usernotconfirmed', '', fullname($user, true)));
        }

    } else if ($delete and confirm_sesskey()) {              // Delete a selected user, after confirmation

        if (!has_capability('moodle/user:delete', $sitecontext)) {
            error('You do not have the required permission to delete a user.');
        }

        if (!$user = get_record('user', 'id', $delete)) {
            error("No such user!", '', true);
        }

        if (is_primary_admin($user->id)) {
            error("You are not allowed to delete the primary admin user!", '', true);
        }

        if ($confirm != md5($delete)) {
            $fullname = fullname($user, true);
            print_heading(get_string('deleteuser', 'admin'));
            $optionsyes = array('delete'=>$delete, 'confirm'=>md5($delete), 'sesskey'=>sesskey());
            notice_yesno(get_string('deletecheckfull', '', "'$fullname'"), 'user.php', 'user.php', $optionsyes, NULL, 'post', 'get');
            admin_externalpage_print_footer();
            die;
        } else if (data_submitted() and !$user->deleted) {
            if (delete_user($user)) {
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
            error("No such user.", '', true);
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
                error("Database error - Couldn't modify the MNET access control list.", '', true);
            }
        } else {
            $aclrecord->accessctrl = $accessctrl;
            if (!update_record('mnet_sso_access_control', $aclrecord)) {
                error("Database error - Couldn't modify the MNET access control list.", '', true);
            }
        }
        $mnethosts = get_records('mnet_host', '', '', 'id', 'id,wwwroot,name');
        notify("MNET access control list updated: username '$user->username' from host '"
                . $mnethosts[$user->mnethostid]->name
                . "' access now set to '$accessctrl'.");
    }

    // create the user filter form
    $ufiltering = new user_filtering();

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
        $$column = "<a href=\"user.php?sort=$column&amp;dir=$columndir\">".$string[$column]."</a>$columnicon";
    }

    if ($sort == "name") {
        $sort = "firstname";
    }

    $extrasql = $ufiltering->get_sql_filter();
    $users = get_users_listing($sort, $dir, $page*$perpage, $perpage, '', '', '', $extrasql);
    $usercount = get_users(false);
    $usersearchcount = get_users(false, '', true, "", "", '', '', '', '', '*', $extrasql);

    if ($extrasql !== '') {
        print_heading("$usersearchcount / $usercount ".get_string('users'));
        $usercount = $usersearchcount;
    } else {
        print_heading("$usercount ".get_string('users'));
    }

    $alphabet = explode(',', get_string('alphabet'));
    $strall = get_string('all');

    print_paging_bar($usercount, $page, $perpage,
            "user.php?sort=$sort&amp;dir=$dir&amp;perpage=$perpage&amp;");

    flush();


    if (!$users) {
        $match = array();
        print_heading(get_string('nousersfound'));

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

        $override = new object();
        $override->firstname = 'firstname';
        $override->lastname = 'lastname';
        $fullnamelanguage = get_string('fullnamedisplay', '', $override);
        if (($CFG->fullnamedisplay == 'firstname lastname') or
            ($CFG->fullnamedisplay == 'firstname') or
            ($CFG->fullnamedisplay == 'language' and $fullnamelanguage == 'firstname lastname' )) {
            $fullnamedisplay = "$firstname / $lastname";
        } else { // ($CFG->fullnamedisplay == 'language' and $fullnamelanguage == 'lastname firstname') 
            $fullnamedisplay = "$lastname / $firstname";
        }
        $table->head = array ($fullnamedisplay, $email, $city, $country, $lastaccess, "", "", "");
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

    // add filters
    $ufiltering->display_add();
    $ufiltering->display_active();

    if (has_capability('moodle/user:create', $sitecontext)) {
        print_heading('<a href="'.$securewwwroot.'/user/editadvanced.php?id=-1">'.get_string('addnewuser').'</a>');
    }
    if (!empty($table)) {
        print_table($table);
        print_paging_bar($usercount, $page, $perpage,
                         "user.php?sort=$sort&amp;dir=$dir&amp;perpage=$perpage&amp;");
        if (has_capability('moodle/user:create', $sitecontext)) {
            print_heading('<a href="'.$securewwwroot.'/user/editadvanced.php?id=-1">'.get_string('addnewuser').'</a>');
        }
    }

    admin_externalpage_print_footer();


?>
