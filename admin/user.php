<?PHP // $Id$

	require("../config.php");
	require("../user/lib.php");
    require("../lib/countries.php");

    if (! record_exists_sql("SELECT * FROM user_admins")) {
        $user->firstname = "Admin";
        $user->lastname  = "User";
        $user->username  = "admin";
        $user->password  = "";
        $user->email     = "root@localhost";
        $user->confirmed = 1;
        $user->timemodified = time();

        if (! $user->id = insert_record("user", $user)) {
            error("SERIOUS ERROR: Could not create admin user record !!!");
        }

        $admin->user = $user->id;

        if (! insert_record("user_admins", $admin)) {
            error("Could not make user $user->id an admin !!!");
        }

        if (! $user = get_record("user", "id", $user->id)) {     // Double check
            error("User ID was incorrect (can't find it)");
        }

        if (! $site = get_site()) {
            error("Could not find site-level course");
        }

        $teacher->user = $user->id;
        $teacher->course = $site->id;
        $teacher->authority = 1;
        if (! insert_record("user_teachers", $teacher)) {
            error("Could not make user $id a teacher of site-level course !!!");
        }

        $USER = $user;
        $USER->loggedin = true;
        $USER->admin = true;
        $USER->teacher["$site->id"] = true;
        save_session("USER");

        redirect("$CFG->wwwroot/user/edit.php?id=$user->id&course=$site->id");

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
        $user->confirmed = 1;
        $user->timemodified = time();

        if (! $user->id = insert_record("user", $user)) {
            if (!$user = get_record("user", "username", "changeme")) {   // half finished user from another time
                error("Could not start a new user!");
            }
        }

        redirect("$CFG->wwwroot/user/edit.php?id=$user->id&course=$site->id");
        
    } else {                        // List all users for editing

        if ($users = get_records_sql("SELECT * from user WHERE username <> 'guest' ORDER BY firstname")) {
            $stredituser = get_string("edituser");
            $stradministration = get_string("administration");
            $stredit   = get_string("edit");
            $strdelete = get_string("delete");

	        print_header("$site->fullname : $stredituser", $site->fullname, 
                         "<A HREF=\"$CFG->wwwroot/admin\">$stradministration</A> -> $stredituser");

            print_heading(get_string("chooseuser"));

            $table->head  = array (get_string("fullname"), get_string("email"), get_string("city"), 
                                   get_string("country"), " ");
            $table->align = array ("LEFT", "LEFT", "CENTER", "CENTER", "CENTER", "CENTER");
            foreach ($users as $user) {
                $table->data[] = array ("<A HREF=\"../user/view.php?id=$user->id&course=$site->id\">$user->firstname $user->lastname</A>",
                                        "$user->email",
                                        "$user->city",
                                        $COUNTRIES[$user->country],
                                        "<A HREF=\"../user/edit.php?id=$user->id&course=$site->id\">$stredit</A>");
            }
            print_table($table);

            print_heading("<A HREF=\"user.php?newuser=true\">".get_string("addnewuser")."</A>");
        } else {
            error("No users found!");
            
        }
        print_footer();
    }

?>
