<?PHP // $Id$

	require("../config.php");
	require("../user/lib.php");
    require("../lib/countries.php");

    optional_variable($id);       // user id

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

        if (! $course = get_site()) {
            error("Could not find site-level course");
        }

        $teacher->user = $user->id;
        $teacher->course = $course->id;
        $teacher->authority = 1;
        if (! insert_record("user_teachers", $teacher)) {
            error("Could not make user $id a teacher of site-level course !!!");
        }

        $USER = $user;
        $USER->loggedin = true;
        $USER->admin = true;
        $USER->teacher["$course->id"] = true;
        save_session("USER");

        $id = $user->id;

    } else {
        if (! $course = get_site()) {
            error("Could not find site-level course");
        }
    }

    require_login();

    if (!isadmin()) {
        error("You must be an administrator to edit users this way.");
    }

    if ($newuser) {                 // Create a new user

        $user->firstname = "New";
        $user->lastname  = "User";
        $user->username  = "username";
        $user->password  = "";
        $user->email     = "";
        $user->confirmed = 1;
        $user->timemodified = time();

        if (! $user->id = insert_record("user", $user)) {
            error("Could not create new user record !!!");
        }

        redirect("$CFG->wwwroot/user/edit.php?id=$user->id&course=$course->id");
        

    } else if ($id) {               // Edit a particular user 

        if (! $user = get_record("user", "id", $id)) {
            error("User ID was incorrect (can't find it)");
        }
    
        redirect("$CFG->wwwroot/user/edit.php?id=$user->id&course=$course->id");

        
    } else {                        // List all users for editing

        if ($users = get_records_sql("SELECT * from user WHERE username <> 'guest' ORDER BY firstname")) {
	        print_header("Edit users", "Edit users", "<A HREF=\"$CFG->wwwroot/admin\">Admin</A> -> Edit users", "");
            print_heading("Choose a user to edit");
            $table->head  = array ("Name", "Email", "City/Town", "Country");
            $table->align = array ("LEFT", "LEFT", "CENTER", "CENTER");
            foreach ($users as $user) {
                $table->data[] = array ("<A HREF=\"user.php?id=$user->id\">$user->firstname $user->lastname</A>",
                                        "$user->email",
                                        "$user->city",
                                        $COUNTRIES[$user->country]);
            }
            print_table($table);
        } else {
            error("No users found!");
            
        }
        print_footer();
    }

?>
