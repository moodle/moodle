<?PHP // $Id$
      // Admin-only script to assign administrative rights to users
      // !!! based on ../course/teacher.php (cut and pasted, then mangled)

	require_once("../config.php");

    optional_variable($add, "");
    optional_variable($remove, "");

    if (! $site = get_site()) {
        redirect("$CFG->wwwroot/$CFG->admin/index.php");
    }

    require_login();

    if (!isadmin()) {
        error("You must be an administrator to use this page.");
    }

    $primaryadmin = get_admin();

/// If you want any administrator to have the ability to assign admin
/// rights, then comment out the following if statement
    if ($primaryadmin->id != $USER->id) {
        error("You must be the primary administrator to use this page.");
    }

/// assign all of the configurable language strings
    $stringstoload = array (
        "assignadmins",
        "administration",
        "existingadmins",
        "noexistingadmins",
        "potentialadmins",
        "nopotentialadmins",
        "addadmin",
        "removeadmin",
        "search",
        "searchagain",
        "toomanytoshow",
        );

    foreach ($stringstoload as $stringtoload){
        $strstringtoload = "str" . $stringtoload;
        $$strstringtoload = get_string($stringtoload);
    }

	print_header("$site->shortname: $course->shortname: $strassignadmins", 
                 "$site->fullname", 
                 "<A HREF=\"index.php\">$stradministration</A> -> 
                  <A HREF=\"{$_SERVER['PHP_SELF']}\">$strassignadmins</A>", "");

/// Get all existing admins
    $admins = get_admins();

/// Add an admin if one is specified
    if ($_REQUEST['add']) {
        $user = @get_record("user", "id", $_REQUEST['add']) or
            error("That account (id = {$_REQUEST['add']}) doesn't exist");

        if ($admins) {
            foreach ($admins as $aa) {
                if ($aa->id == $user->id) {
                    error("That user is already an admin.");
                }            
            }
        }

        $admin->userid = $user->id;
        $admin->id = insert_record("user_admins", $admin);
        $admins[] = $user;
    }

/// Remove an admin if one is specified.
    if ($_REQUEST['remove']) {

        $user = @get_record("user", "id", $_REQUEST['remove']) or 
            error("That account (id = {$_REQUEST['remove']}) doesn't exist");

        if ($admins) {
            foreach ($admins as $key => $aa) {
                if ($aa->id == $user->id) {
                    /// make sure that we don't delete the primary admin
                    /// account, so that there is always at least on admin
                    if ($aa->id == $primaryadmin->id) {
                        error("That user is the primary admin, and shouldn't be removed.");
                    } else {
                        remove_admin($user->id);
                        unset($admins[$key]);
                    }
                } 
            }
        }
    }


/// Print the lists of existing and potential admins
    echo "<TABLE CELLPADDING=2 CELLSPACING=10 ALIGN=CENTER>";
    echo "<TR><TH WIDTH=50%>$strexistingadmins</TH><TH WIDTH=50%>$strpotentialadmins</TH></TR>";
    echo "<TR><TD WIDTH=50% NOWRAP VALIGN=TOP>";

/// First, show existing admins

    if (! $admins) { 
        echo "<P ALIGN=CENTER>$strnoexistingadmins</A>";

    } else {
        foreach ($admins as $admin) {
            echo "<P ALIGN=right>$admin->firstname $admin->lastname,
            $admin->email &nbsp;&nbsp; ";
            if ($primaryadmin->id == $admin->id){
                print_spacer(10, 9, false);
            } else {
                echo "<A HREF=\"{$_SERVER['PHP_SELF']}?remove=$admin->id\"
                TITLE=\"$strremoveadmin\"><IMG SRC=\"../pix/t/right.gif\"
                BORDER=0></A>";
            }
            echo "</P>";
        }
    }

    echo "<TD WIDTH=50% NOWRAP VALIGN=TOP>";

/// Print list of potential admins

    if ($search) {
        $users = get_users_search($search);
    } else {
        $users = get_users_confirmed();
    }

    
    if ($users) {
        foreach ($users as $user) {  // Remove users who are already admins
            if ($admins) {
                foreach ($admins as $admin) {
                    if ($admin->id == $user->id) {
                        continue 2;
                    }
                }
            }
            $potential[] = $user;
        }
    }

    if (! $potential) { 
        echo "<P ALIGN=CENTER>$strnopotentialadmins</A>";
        if ($search) {
            echo "<FORM ACTION={$_SERVER['PHP_SELF']} METHOD=POST>";
            echo "<INPUT TYPE=text NAME=search SIZE=20>";
            echo "<INPUT TYPE=submit VALUE=\"$strsearchagain\">";
            echo "</FORM>";
        }

    } else {
        if ($search) {
            echo "<P ALIGN=CENTER>($strsearchresults)</P>";
        }
        if (count($potential) <= 20) {
            foreach ($potential as $user) {
                echo "<P ALIGN=LEFT><A HREF=\"{$_SERVER['PHP_SELF']}?add=$user->id\"
                TITLE=\"$straddadmin\"><IMG SRC=\"../pix/t/left.gif\" BORDER=0></A>&nbsp;&nbsp;$user->firstname $user->lastname, $user->email";
            }
        } else {
            echo "<P ALIGN=CENTER>There are too many users to show.<BR>";
            echo "Enter a search word here.";
            echo "<FORM ACTION={$_SERVER['PHP_SELF']} METHOD=POST>";
            echo "<INPUT TYPE=text NAME=search SIZE=20>";
            echo "<INPUT TYPE=submit VALUE=\"$strsearch\">";
            echo "</FORM>";
        }
    }

    echo "</TR></TABLE>";

    print_footer();

?>
