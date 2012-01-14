<?php

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
        print_error('nopermissions', 'error', '', 'edit/delete users');
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

    $returnurl = "$CFG->wwwroot/$CFG->admin/user.php";

    if ($confirmuser and confirm_sesskey()) {
        if (!$user = $DB->get_record('user', array('id'=>$confirmuser))) {
            print_error('nousers');
        }

        $auth = get_auth_plugin($user->auth);

        $result = $auth->user_confirm($user->username, $user->secret);

        if ($result == AUTH_CONFIRM_OK or $result == AUTH_CONFIRM_ALREADY) {
            redirect($returnurl);
        } else {
            echo $OUTPUT->header();
            redirect($returnurl, get_string('usernotconfirmed', '', fullname($user, true)));
        }

    } else if ($delete and confirm_sesskey()) {              // Delete a selected user, after confirmation

        require_capability('moodle/user:delete', $sitecontext);

        $user = $DB->get_record('user', array('id'=>$delete), '*', MUST_EXIST);

        if (is_siteadmin($user->id)) {
            print_error('useradminodelete', 'error');
        }

        if ($confirm != md5($delete)) {
            echo $OUTPUT->header();
            $fullname = fullname($user, true);
            echo $OUTPUT->heading(get_string('deleteuser', 'admin'));
            $optionsyes = array('delete'=>$delete, 'confirm'=>md5($delete), 'sesskey'=>sesskey());
            echo $OUTPUT->confirm(get_string('deletecheckfull', '', "'$fullname'"), new moodle_url('user.php', $optionsyes), 'user.php');
            echo $OUTPUT->footer();
            die;
        } else if (data_submitted() and !$user->deleted) {
            if (delete_user($user)) {
                session_gc(); // remove stale sessions
                redirect($returnurl);
            } else {
                session_gc(); // remove stale sessions
                echo $OUTPUT->header();
                echo $OUTPUT->notification($returnurl, get_string('deletednot', '', fullname($user, true)));
            }
        }
    } else if ($acl and confirm_sesskey()) {
        if (!has_capability('moodle/user:delete', $sitecontext)) {
            // TODO: this should be under a separate capability
            print_error('nopermissions', 'error', '', 'modify the NMET access control list');
        }
        if (!$user = $DB->get_record('user', array('id'=>$acl))) {
            print_error('nousers', 'error');
        }
        if (!is_mnet_remote_user($user)) {
            print_error('usermustbemnet', 'error');
        }
        $accessctrl = strtolower(required_param('accessctrl', PARAM_ALPHA));
        if ($accessctrl != 'allow' and $accessctrl != 'deny') {
            print_error('invalidaccessparameter', 'error');
        }
        $aclrecord = $DB->get_record('mnet_sso_access_control', array('username'=>$user->username, 'mnet_host_id'=>$user->mnethostid));
        if (empty($aclrecord)) {
            $aclrecord = new stdClass();
            $aclrecord->mnet_host_id = $user->mnethostid;
            $aclrecord->username = $user->username;
            $aclrecord->accessctrl = $accessctrl;
            $DB->insert_record('mnet_sso_access_control', $aclrecord);
        } else {
            $aclrecord->accessctrl = $accessctrl;
            $DB->update_record('mnet_sso_access_control', $aclrecord);
        }
        $mnethosts = $DB->get_records('mnet_host', null, 'id', 'id,wwwroot,name');
        redirect($returnurl);
    }

    // create the user filter form
    $ufiltering = new user_filtering();
    echo $OUTPUT->header();

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
            $columnicon = " <img src=\"" . $OUTPUT->pix_url('t/' . $columnicon) . "\" alt=\"\" />";

        }
        $$column = "<a href=\"user.php?sort=$column&amp;dir=$columndir\">".$string[$column]."</a>$columnicon";
    }

    if ($sort == "name") {
        $sort = "firstname";
    }

    list($extrasql, $params) = $ufiltering->get_sql_filter();
    $users = get_users_listing($sort, $dir, $page*$perpage, $perpage, '', '', '', $extrasql, $params);
    $usercount = get_users(false);
    $usersearchcount = get_users(false, '', false, null, "", '', '', '', '', '*', $extrasql, $params);

    if ($extrasql !== '') {
        echo $OUTPUT->heading("$usersearchcount / $usercount ".get_string('users'));
        $usercount = $usersearchcount;
    } else {
        echo $OUTPUT->heading("$usercount ".get_string('users'));
    }

    $strall = get_string('all');

    $baseurl = new moodle_url('user.php', array('sort' => $sort, 'dir' => $dir, 'perpage' => $perpage));
    echo $OUTPUT->paging_bar($usercount, $page, $perpage, $baseurl);

    flush();


    if (!$users) {
        $match = array();
        echo $OUTPUT->heading(get_string('nousersfound'));

        $table = NULL;

    } else {

        $countries = get_string_manager()->get_list_of_countries(false);
        if (empty($mnethosts)) {
            $mnethosts = $DB->get_records('mnet_host', null, 'id', 'id,wwwroot,name');
        }

        foreach ($users as $key => $user) {
            if (isset($countries[$user->country])) {
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

        $override = new stdClass();
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

        $table = new html_table();
        $table->head = array ($fullnamedisplay, $email, $city, $country, $lastaccess, "", "", "");
        $table->align = array ("left", "left", "left", "left", "left", "center", "center", "center");
        $table->width = "95%";
        foreach ($users as $user) {
            if (isguestuser($user)) {
                continue; // do not display guest here
            }

            if ($user->id == $USER->id or is_siteadmin($user)) {
                $deletebutton = "";
            } else {
                if (has_capability('moodle/user:delete', $sitecontext)) {
                    $deletebutton = "<a href=\"user.php?delete=$user->id&amp;sesskey=".sesskey()."\">$strdelete</a>";
                } else {
                    $deletebutton ="";
                }
            }

            if (has_capability('moodle/user:update', $sitecontext) and (is_siteadmin($USER) or !is_siteadmin($user)) and !is_mnet_remote_user($user)) {
                $editbutton = "<a href=\"$securewwwroot/user/editadvanced.php?id=$user->id&amp;course=$site->id\">$stredit</a>";
                if ($user->confirmed == 0) {
                    $confirmbutton = "<a href=\"user.php?confirmuser=$user->id&amp;sesskey=".sesskey()."\">" . get_string('confirm') . "</a>";
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
                if ($acl = $DB->get_record('mnet_sso_access_control', array('username'=>$user->username, 'mnet_host_id'=>$user->mnethostid))) {
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
                    $deletebutton .= " (<a href=\"?acl={$user->id}&amp;accessctrl=$changeaccessto&amp;sesskey=".sesskey()."\">"
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
        echo $OUTPUT->heading('<a href="'.$securewwwroot.'/user/editadvanced.php?id=-1">'.get_string('addnewuser').'</a>');
    }
    if (!empty($table)) {
        echo html_writer::table($table);
        echo $OUTPUT->paging_bar($usercount, $page, $perpage, $baseurl);
        if (has_capability('moodle/user:create', $sitecontext)) {
            echo $OUTPUT->heading('<a href="'.$securewwwroot.'/user/editadvanced.php?id=-1">'.get_string('addnewuser').'</a>');
        }
    }

    echo $OUTPUT->footer();



