<?PHP  
      // Admin only  script to assign administrative rights to users
      // !!! based on admin.php (cut and pasted, then mangled)

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
        "assigncreators",
        "administration",
        "existingcreators",
        "noexistingcreators",
        "potentialcreators",
        "nopotentialcreators",
        "addcreator",
        "removecreator",
        "search",
        "searchagain",
        "toomanytoshow",
        );

    foreach ($stringstoload as $stringtoload){
        $strstringtoload = "str" . $stringtoload;
        $$strstringtoload = get_string($stringtoload);
    }

	print_header("$site->shortname: $course->shortname: $strassigncreators", 
                 "$site->fullname", 
                 "<A HREF=\"index.php\">$stradministration</A> -> 
                  <A HREF=\"{$_SERVER['PHP_SELF']}\">$strassigncreators</A>", "");

/// Get all existing creators
    $creators = get_creators();

/// Add an creator if one is specified
    if ($_REQUEST['add']) {
        $user = @get_record("user", "id", $_REQUEST['add']) or
            error("That account (id = {$_REQUEST['add']}) doesn't exist");

        if ($creators) {
            foreach ($creators as $aa) {
                if ($aa->id == $user->id) {
                    error("That user is already a creator .");
                }            
            }
        }

        $creator->userid = $user->id;
        $creator->id = insert_record("user_coursecreators", $creator);
        $creators[] = $user;
    }

/// Remove an creator if one is specified.
    if ($_REQUEST['remove']) {

        $user = @get_record("user", "id", $_REQUEST['remove']) or 
            error("That account (id = {$_REQUEST['remove']}) doesn't exist");

        if ($creators) {
            foreach ($creators as $key => $aa) {
                if ($aa->id == $user->id) {
                        delete_records("user_coursecreators","userid",$user->id);
                        unset($creators[$key]);
                } 
            }
        }
    }


/// Print the lists of existing and potential creators
    echo "<TABLE CELLPADDING=2 CELLSPACING=10 ALIGN=CENTER>";
    echo "<TR><TH WIDTH=50%>$strexistingcreators</TH><TH WIDTH=50%>$strpotentialcreators</TH></TR>";
    echo "<TR><TD WIDTH=50% NOWRAP VALIGN=TOP>";

/// First, show existing creators

    if (! $creators) { 
        echo "<P ALIGN=CENTER>$strnoexistingcreators</A>";

    } else {
        foreach ($creators as $creator) {
            echo "<P ALIGN=right>$creator->firstname $creator->lastname,
            $creator->email &nbsp;&nbsp; ";
                echo "<A HREF=\"{$_SERVER['PHP_SELF']}?remove=$creator->id\"
                TITLE=\"$strremovecreator\"><IMG SRC=\"../pix/t/right.gif\"
                BORDER=0></A>";
            echo "</P>";
        }
    }

    echo "<TD WIDTH=50% NOWRAP VALIGN=TOP>";

/// Print list of potential  creators

    if ($search) {
        $users = get_users_search($search);
    } else {
        $users = get_users_confirmed();
    }

    
    if ($users) {
        foreach ($users as $user) {  // Remove users who are already creators
            if ($creators) {
                foreach ($creators as $creator) {
                    if ($creator->id == $user->id) {
                        continue 2;
                    }
                }
            }
            $potential[] = $user;
        }
    }

    if (! $potential) { 
        echo "<P ALIGN=CENTER>$strnopotentialcreators</A>";
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
                TITLE=\"$straddcreator\"><IMG SRC=\"../pix/t/left.gif\" BORDER=0></A>&nbsp;&nbsp;$user->firstname $user->lastname, $user->email";
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
