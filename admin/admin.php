<?PHP // $Id$
      // Admin-only script to assign administrative rights to users

	require_once("../config.php");
    
    define("MAX_USERS_PER_PAGE", 30);

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
        "searchresults"
        );

    foreach ($stringstoload as $stringtoload){
        $strstringtoload = "str" . $stringtoload;
        $$strstringtoload = get_string($stringtoload);
    }

    if ($search) {
        $searchstring = $strsearchagain;
    } else {
        $searchstring = $strsearch;
    }

	print_header("$site->shortname: $course->shortname: $strassignadmins", 
                 "$site->fullname", 
                 "<a href=\"index.php\">$stradministration</a> -> 
                  <a href=\"{$_server['php_self']}\">$strassignadmins</a>", "");

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
    echo "<table cellpadding=2 cellspacing=10 align=center>";
    echo "<tr><th width=50%>$strexistingadmins</th><th width=50%>$strpotentialadmins</th></tr>";
    echo "<tr><td width=50% nowrap valign=top>";

/// First, show existing admins

    if (! $admins) { 
        echo "<p align=center>$strnoexistingadmins</a>";

        $adminlist = "";

    } else {
        $adminarray = array();

        foreach ($admins as $admin) {
            $adminarray[] = $admin->id;
            echo "<p align=right>$admin->firstname $admin->lastname,
                     $admin->email &nbsp;&nbsp; ";
            if ($primaryadmin->id == $admin->id){
                print_spacer(10, 9, false);
            } else {
                echo "<a href=\"{$_SERVER['PHP_SELF']}?remove=$admin->id\"
                title=\"$strremoveadmin\"><img src=\"../pix/t/right.gif\"
                border=0></A>";
            }
            echo "</p>";
        }
        
        $adminlist = implode(",",$adminarray);
        unset($adminarray);
    }

    echo "<td width=50% nowrap valign=top>";

/// Print list of potential admins

    $usercount = get_users(false, $search, true, $adminlist);

    if ($usercount == 0) { 
        echo "<p align=center>$strnopotentialadmins</p>";

    } else if ($usercount > MAX_USERS_PER_PAGE) { 
        echo "<p align=center>$strtoomanytoshow</p>";

    } else {

        if ($search) {
            echo "<p align=center>($strsearchresults : $search)</p>";
        }
         
        if (!$users = get_users(true, $search, true, $adminlist)) {
            error("Could not get users!");
        }

        foreach ($users as $user) {
            echo "<p align=left><a href=\"{$_SERVER['PHP_SELF']}?add=$user->id\"".
                   "title=\"$straddadmin\"><img src=\"../pix/t/left.gif\"".
                   "border=0></a>&nbsp;&nbsp;$user->firstname $user->lastname, $user->email";
        }
    }

    if ($search or $usercount > MAX_USERS_PER_PAGE) {
        echo "<form action={$_SERVER['PHP_SELF']} method=post>";
        echo "<input type=text name=search size=20>";
        echo "<input type=submit value=\"$searchstring\">";
        echo "</form>";
    }

    echo "</tr></table>";

    print_footer();

?>
