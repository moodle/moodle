<?PHP // $Id$

	require("../config.php");
	require("../user/lib.php");
    require("../lib/countries.php");

    optional_variable($newuser, "");
    optional_variable($delete, "");
    optional_variable($confirm, "");
    optional_variable($sort, "name");
    optional_variable($dir, "ASC");

    if (! record_exists_sql("SELECT * FROM user_admins")) {   // No admin user yet
        $user->firstname = "Admin";
        $user->lastname  = "User";
        $user->username  = "admin";
        $user->password  = md5("admin");
        $user->email     = "root@localhost";
        $user->confirmed = 1;
        $user->maildisplay = 1;
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
        $USER->site = $CFG->wwwroot;
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

        $stredituser = get_string("edituser");
        $stradministration = get_string("administration");
        $stredit   = get_string("edit");
        $strdelete = get_string("delete");
        $strdeletecheck = get_string("deletecheck");

        print_header("$site->fullname : $stredituser", $site->fullname, 
                     "<A HREF=\"$CFG->wwwroot/admin\">$stradministration</A> -> $stredituser");

        if ($delete) {              // Delete a selected user, after confirmation
            if (!$user = get_record("user", "id", "$delete")) {
                error("No such user!");
            }
            if ($confirm != md5($delete)) {
                notice_yesno(get_string("deletecheckfull", "", "'$user->firstname $user->lastname'"),
                     "user.php?delete=$delete&confirm=".md5($delete), "user.php");

                exit;
            } else if (!$user->deleted) {
                $user->deleted = "1";
                $user->username = $user->email;  // Remember it just in case
                $user->email = "";               // Clear this field to free it up
                $user->timemodified = time();
                if (update_record("user", $user)) {
                    unenrol_student($user->id);  // From all courses
                    remove_teacher($user->id);   // From all courses
                    remove_admin($user->id);
                    notify(get_string("deletedactivity", "", "$user->firstname $user->lastname"));
                } else {
                    notify(get_string("deletednot", "", "$user->firstname $user->lastname"));
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
               $$column = "<A HREF=\"user.php?sort=$columnsort&dir=$columndir\">".$string[$column]."</A>";
            }
        }

        if ($sort == "name") {
            $sort = "firstname";
        }

        if ($users = get_records_sql("SELECT * from user WHERE username <> 'guest' 
                                      AND deleted <> '1' ORDER BY $sort $dir")) {

            foreach ($users as $key => $user) {
                $users[$key]->country = $COUNTRIES[$user->country];
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

            print_heading(get_string("chooseuser"));

            $table->head = array ($name, $email, $city, $country, $lastaccess, "", "");
            $table->align = array ("LEFT", "LEFT", "LEFT", "LEFT", "LEFT", "CENTER", "CENTER");
            $table->width = "95%";
            foreach ($users as $user) {
                if ($user->id == $USER->id or $user->username == "changeme") {
                    $deletebutton = "";
                } else {
                    $deletebutton = "<A HREF=\"user.php?delete=$user->id\" TARGET=\"$strdeletecheck\">$strdelete</A>";
                }
                if ($user->lastaccess) {
                    $strlastaccess = format_time(time() - $user->lastaccess);
                } else {
                    $strlastaccess = get_string("never");
                }
                $table->data[] = array ("<A HREF=\"../user/view.php?id=$user->id&course=$site->id\">$user->firstname $user->lastname</A>",
                                 "$user->email",
                                 "$user->city",
                                 "$user->country",
                                 $strlastaccess,
                                 "<A HREF=\"../user/edit.php?id=$user->id&course=$site->id\">$stredit</A>",
                                 $deletebutton);
            }
            print_table($table);

            print_heading("<A HREF=\"user.php?newuser=true\">".get_string("addnewuser")."</A>");
        } else {
            error("No users found!");
            
        }
        print_footer();
    }

?>
