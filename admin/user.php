<?PHP // $Id$

    require_once("../config.php");

    $recordsperpage = 30;

    optional_variable($newuser, "");
    optional_variable($delete, "");
    optional_variable($confirm, "");
    optional_variable($confirmuser, "");
    optional_variable($sort, "name");
    optional_variable($dir, "ASC");
    optional_variable($page, 0);
    optional_variable($search, "");

    unset($user);
    unset($admin);
    unset($teacher);

    if (! record_exists("user_admins")) {   // No admin user yet

        $user->firstname = get_string("admin");
        $user->lastname  = get_string("user");
        $user->username  = "admin";
        $user->password  = md5("admin");
        $user->email     = "root@localhost";
        $user->confirmed = 1;
        $user->lang = $CFG->lang;
        $user->maildisplay = 1;
        $user->timemodified = time();

        if (! $user->id = insert_record("user", $user)) {
            error("SERIOUS ERROR: Could not create admin user record !!!");
        }

        $admin->userid = $user->id;

        if (! insert_record("user_admins", $admin)) {
            error("Could not make user $user->id an admin !!!");
        }

        if (! $user = get_record("user", "id", $user->id)) {     // Double check
            error("User ID was incorrect (can't find it)");
        }

        if (! $site = get_site()) {
            error("Could not find site-level course");
        }

        $teacher->userid = $user->id;
        $teacher->course = $site->id;
        $teacher->authority = 1;
        if (! insert_record("user_teachers", $teacher)) {
            error("Could not make user $id a teacher of site-level course !!!");
        }

        $USER = $user;
        $USER->loggedin = true;
        $USER->site = $CFG->wwwroot;
        $USER->admin = true;
        $USER->teacher["$site->id"] = true;
        $USER->newadminuser = true;

        redirect("$CFG->wwwroot/user/edit.php?id=$user->id&course=$site->id");
        exit;

    } else {
        if (! $site = get_site()) {
            error("Could not find site-level course");
        }
    }

    require_login();

    if (!isadmin()) {
        error("You must be an administrator to edit users this way.");
    }

    if ($newuser) {                 // Create a new user
        $user->firstname = "";
        $user->lastname  = "";
        $user->username  = "changeme";
        $user->password  = "";
        $user->email     = "";
        $user->lang      = $CFG->lang;
        $user->confirmed = 1;
        $user->timemodified = time();

        if (! $user->id = insert_record("user", $user)) {
            if (!$user = get_record("user", "username", "changeme")) {   // half finished user from another time
                error("Could not start a new user!");
            }
        }

        redirect("$CFG->wwwroot/user/edit.php?id=$user->id&course=$site->id");
        
    } else {                        // List all users for editing

        $stredituser = get_string("edituser");
        $stradministration = get_string("administration");
        $strusers = get_string("users");
        $stredit   = get_string("edit");
        $strdelete = get_string("delete");
        $strdeletecheck = get_string("deletecheck");
        $strsearch = get_string("search");
        $strshowallusers = get_string("showallusers");

        print_header("$site->shortname: $stredituser", $site->fullname, 
                     "<a href=\"index.php\">$stradministration</a> -> <a href=\"users.php\">$strusers</a> -> $stredituser");

        if ($confirmuser) {
            if (!$user = get_record("user", "id", "$confirmuser")) {
                error("No such user!");
            }

            unset($confirmeduser);
            $confirmeduser->id = $confirmuser;
            $confirmeduser->confirmed = 1;
            $confirmeduser->timemodified = time();

            if (update_record("user", $confirmeduser)) {
                notify(get_string("userconfirmed", "", fullname($user, true)) );
            } else {
                notify(get_string("usernotconfirmed", "", fullname($user, true)));
            }

        } else if ($delete) {              // Delete a selected user, after confirmation
            if (!$user = get_record("user", "id", "$delete")) {
                error("No such user!");
            }

            $primaryadmin = get_admin();
            if ($user->id == $primaryadmin->id) {
                error("You are not allowed to delete the primary admin user!");
            }

            if ($confirm != md5($delete)) {
                $fullname = fullname($user, true);
                notice_yesno(get_string("deletecheckfull", "", "'$fullname'"),
                     "user.php?delete=$delete&confirm=".md5($delete), "user.php");

                exit;
            } else if (!$user->deleted) {
                unset($updateuser);
                $updateuser->id = $user->id;
                $updateuser->deleted = "1";
                $updateuser->username = "$user->email.".time();  // Remember it just in case
                $updateuser->email = "";               // Clear this field to free it up
                $updateuser->timemodified = time();
                if (update_record("user", $updateuser)) {
                    unenrol_student($user->id);  // From all courses
                    remove_teacher($user->id);   // From all courses
                    remove_admin($user->id);
                    notify(get_string("deletedactivity", "", fullname($user, true)) );
                } else {
                    notify(get_string("deletednot", "", fullname($user, true)));
                }
            }
        }

        // Carry on with the user listing


        $columns = array("name", "email", "city", "country", "lastaccess");

        foreach ($columns as $column) {
            $string[$column] = get_string("$column");
            $columnsort = "$column";
            if ($column == "lastaccess") {
                $columndir = "DESC";
            } else {
                $columndir = "ASC";
            }
            if ($columnsort == $sort) {
               $$column = $string[$column];
            } else {
               $$column = "<A HREF=\"user.php?sort=$columnsort&dir=$columndir&search=$search\">".$string[$column]."</A>";
            }
        }

        if ($sort == "name") {
            $sort = "firstname";
        }

        if (!$users = get_users_listing($sort, $dir, $page, $recordsperpage, $search)) {
            if (!$users = get_users_listing($sort, $dir, 0, $recordsperpage)) {
                error("No users found!");
            } else {
                notify(get_string("nousersmatching", "", $search));
                $search = "";
            }
        }

        $usercount = get_users(false);

        if ($search) {
            $usersearchcount = get_users(false, $search);
            print_heading("$usersearchcount / $usercount ".get_string("users"));
            $usercount = $usersearchcount;
        } else {
            print_heading("$usercount ".get_string("users"));
        }
            
        $a->start = $page;
        $a->end = $page + $recordsperpage;
        if ($a->end > $usercount) {
            $a->end = $usercount;
        }
        echo "<TABLE align=center cellpadding=10><TR>";
        echo "<TD>";
        if ($page) {
            $prevpage = $page - $recordsperpage;
            if ($prevpage < 0) {
                $prevpage = 0;
            }
            $options["dir"] = $dir;
            $options["page"] = 0;
            $options["sort"] = $sort;
            $options["search"] = $search;
            print_single_button("user.php", $options, "  <<  ");
            echo "</TD><TD>";
            $options["page"] = $prevpage;
            print_single_button("user.php", $options, "  <  ");
        }
        echo "</TD><TD>";
        print_heading(get_string("displayingusers", "", $a));
        echo "</TD><TD>";
        $nextpage = $page + $recordsperpage;
        if ($nextpage < $usercount) {
            $options["dir"] = $dir;
            $options["page"] = $nextpage;
            $options["sort"] = $sort;
            $options["search"] = $search;
            print_single_button("user.php", $options, "  >  ");
            echo "</TD><TD>";
            $options["page"] = $usercount-$recordsperpage;
            print_single_button("user.php", $options, "  >>  ");
        }
        echo "</TD></TR></TABLE>";

        flush();

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

        $table->head = array ($name, $email, $city, $country, $lastaccess, "", "", "");
        $table->align = array ("left", "left", "left", "left", "left", "center", "center", "center");
        $table->width = "95%";
        foreach ($users as $user) {
            if ($user->id == $USER->id or $user->username == "changeme") {
                $deletebutton = "";
            } else {
                $deletebutton = "<a href=\"user.php?delete=$user->id\">$strdelete</a>";
            }
            if ($user->lastaccess) {
                $strlastaccess = format_time(time() - $user->lastaccess);
            } else {
                $strlastaccess = get_string("never");
            }
            if ($user->confirmed == 0) {
                $confirmbutton = "<a href=\"user.php?confirmuser=$user->id\">" . get_string("confirm") . "</a>";
            } else {
                $confirmbutton = "";
            }
            $fullname = fullname($user, true);
            $table->data[] = array ("<a href=\"../user/view.php?id=$user->id&course=$site->id\">$fullname</a>",
                             "$user->email",
                             "$user->city",
                             "$user->country",
                             $strlastaccess,
                             "<a href=\"../user/edit.php?id=$user->id&course=$site->id\">$stredit</a>",
                             $deletebutton,
                             $confirmbutton);
        }

        echo "<table align=center cellpadding=10><tr><td>";
        echo "<form action=user.php method=post>";
        echo "<input type=text name=search value=\"$search\" size=20>";
        echo "<input type=submit value=\"$strsearch\">";
        if ($search) {
            echo "<input type=\"button\" onclick=\"document.location='user.php';\" value=\"$strshowallusers\">";
        }
        echo "</form>";    
        echo "</td></tr></table>";

        print_table($table);

        if ($CFG->auth == "email" || $CFG->auth == "none" || $CFG->auth == "manual"){
            print_heading("<a href=\"user.php?newuser=true\">".get_string("addnewuser")."</a>");
        }

        print_footer();
    }

?>
