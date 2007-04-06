<?php // $Id$

    require_once('../config.php');
    require_once($CFG->libdir.'/adminlib.php');

    $newuser      = optional_param('newuser', 0, PARAM_BOOL);
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

    if (!$sitecontext = get_context_instance(CONTEXT_SYSTEM, SITEID)) {  // Should never happen
        redirect('index.php');
    }

    if (empty($CFG->rolesactive)) {   // No admin user yet.

        $user = new object();
        $user->firstname    = get_string('admin');
        $user->lastname     = get_string('user');
        $user->username     = 'admin';
        $user->password     = hash_internal_user_password('admin');
        $user->email        = 'root@localhost';
        $user->confirmed    = 1;
        $user->lang         = $CFG->lang;
        $user->maildisplay  = 1;
        $user->timemodified = time();

        if (! $user->id = insert_record('user', $user)) {
            error("SERIOUS ERROR: Could not create admin user record !!!");
        }

        if (! $user = get_record('user', 'id', $user->id)) {   // Double check.
            error("User ID was incorrect (can't find it)");
        }


        // Assign the default admin role to the new user.
        if (!$adminroles = get_roles_with_capability('moodle/legacy:admin', CAP_ALLOW)) {
            error('No admin role could be found');
        }
        foreach ($adminroles as $adminrole) {
            role_assign($adminrole->id, $user->id, 0, $sitecontext->id);
        }
        set_config('rolesactive', 1);


        if (! $site = get_site()) {
            error("Could not find site-level course");
        }

        // Log the user in.
        $USER = $user;
        $USER->loggedin = true;
        $USER->sessionIP = md5(getremoteaddr());   // Store the current IP in the session
        $USER->site = $CFG->wwwroot;
        $USER->admin = true;
        $USER->newadminuser = true;

        sesskey();   // For added security, used to check script parameters

        load_all_capabilities();

        redirect("$CFG->wwwroot/user/edit.php?id=$user->id&amp;course=$site->id");  // Edit thyself
        exit;

    } else {
        if (! $site = get_site()) {
            error("Could not find site-level course");
        }
    }

    require_login();

    $adminroot = admin_get_root();

    if ($newuser) {
        admin_externalpage_setup('addnewuser', $adminroot);
    } else {
        admin_externalpage_setup('editusers', $adminroot);
    }

    if (empty($CFG->loginhttps)) {
        $securewwwroot = $CFG->wwwroot;
    } else {
        $securewwwroot = str_replace('http:','https:',$CFG->wwwroot);
    }

    if ($newuser) {                 // Create a new user

        if (!has_capability('moodle/user:create', $sitecontext)) {
            error('You do not have the required permission to create new users.');
        }

        if (!$user = get_record('user', 'username', 'changeme')) {   // half finished user from another time

            $user = new object();
            $user->auth         = 'manual';
            $user->firstname    = '';
            $user->lastname     = '';
            $user->username     = 'changeme';
            $user->password     = '';
            $user->email        = '';
            $user->lang         = $CFG->lang;
            $user->confirmed    = 1;
            $user->timemodified = time();

            if (! $user->id = insert_record('user', $user)) {
                error('Could not start a new user!');
            }
        }

        redirect("$securewwwroot/user/edit.php?id=$user->id&amp;course=$site->id");

    } else {                        // List all users for editing

        if (!has_capability('moodle/user:update', $sitecontext) and !has_capability('moodle/user:delete', $sitecontext)) {
            error('You do not have the required permission to edit/delete users.');
        }

        $stredit   = get_string('edit');
        $strdelete = get_string('delete');
        $strdeletecheck = get_string('deletecheck');
        $strsearch = get_string('search');
        $strshowallusers = get_string('showallusers');

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
                $updateuser = new object();
                $updateuser->id = $user->id;
                $updateuser->deleted = 1;
                $updateuser->username = addslashes("$user->email.".time());  // Remember it just in case
                $updateuser->email = '';               // Clear this field to free it up
                $updateuser->idnumber = '';               // Clear this field to free it up
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

        $users = get_users_listing($sort, $dir, $page*$perpage, $perpage, $search, $firstinitial, $lastinitial);
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

        echo "<center><p align=\"center\">";
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
        echo "</center>";

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

            $table->head = array ("$firstname / $lastname", $email, $city, $country, $lastaccess, "", "", "");
            $table->align = array ("left", "left", "left", "left", "left", "center", "center", "center");
            $table->width = "95%";
            foreach ($users as $user) {
                if ($user->username == 'changeme' or $user->username == 'guest') {
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

                if (has_capability('moodle/user:update', $sitecontext)) {
                    $editbutton = "<a href=\"$securewwwroot/user/edit.php?id=$user->id&amp;course=$site->id\">$stredit</a>";
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

        echo "<table class=\"searchbox\" align=\"center\" cellpadding=\"10\"><tr><td>";
        echo "<form action=\"user.php\" method=\"get\">";
        echo "<input type=\"text\" name=\"search\" value=\"".s($search, true)."\" size=\"20\" />";
        echo "<input type=\"submit\" value=\"$strsearch\" />";
        if ($search) {
            echo "<input type=\"button\" onclick=\"document.location='user.php';\" value=\"$strshowallusers\" />";
        }
        echo "</form>";
        echo "</td></tr></table>";

        if (has_capability('moodle/user:create', $sitecontext)) {
            print_heading("<a href=\"user.php?newuser=true&amp;sesskey=$USER->sesskey\">".get_string('addnewuser')."</a>");
        }
        if (!empty($table)) {
            print_table($table);
            print_paging_bar($usercount, $page, $perpage,
                             "user.php?sort=$sort&amp;dir=$dir&amp;perpage=$perpage".
                             "&amp;firstinitial=$firstinitial&amp;lastinitial=$lastinitial&amp;search=".urlencode(stripslashes($search))."&amp;");
            if (has_capability('moodle/user:create', $sitecontext)) {
                print_heading("<a href=\"user.php?newuser=true&amp;sesskey=$USER->sesskey\">".get_string("addnewuser")."</a>");
            }
        }


        admin_externalpage_print_footer($adminroot);
    }

?>
