<?PHP // $Id$
      // Admin-only script to assign administrative rights to users

	require_once("../config.php");
    
    define("MAX_USERS_PER_PAGE", 50);

    optional_variable($add, "");
    optional_variable($remove, "");
    optional_variable($search, "");

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
        "users",
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

	print_header("$site->shortname: $strassignadmins", 
                 "$site->fullname", 
                 "<a href=\"index.php\">$stradministration</a> -> <a href=\"users.php\">$strusers</a> -> $strassignadmins", "");

/// Add an admin if one is specified
    if (!empty($_GET['add'])) {
        if (! add_admin($add)) {
            error("Could not add that admin!");
        }
    }

/// Remove an admin if one is specified.
    if (!empty($_GET['remove'])) {
        if (! remove_admin($remove)) {
            error("Could not remove that admin!");
        }
    }

/// Print a help notice about this page
    if (empty($add) and empty($remove) and empty($search)) {
        print_simple_box("<center>".get_string("adminhelpassignadmins")."</center>", "center", "50%");
    }

/// Get all existing admins
    $admins = get_admins();

/// Print the lists of existing and potential admins
    echo "<table cellpadding=2 cellspacing=10 align=center>";
    echo "<tr><th width=50%>$strexistingadmins</th><th width=50%>$strpotentialadmins</th></tr>";
    echo "<tr><td width=50% nowrap valign=top>";

/// First, show existing admins

    if (! $admins) { 
        echo "<p align=center>$strnoexistingadmins</p>";
        $adminlist = "";

    } else {
        $adminarray = array();

        foreach ($admins as $admin) {
            $adminarray[] = $admin->id;
            echo "<p align=right>".fullname($admin, true).",
                     $admin->email &nbsp;&nbsp; ";
            if ($primaryadmin->id == $admin->id){
                print_spacer(10, 9, false);
            } else {
                echo "<a href=\"admin.php?remove=$admin->id\"
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
        echo "<p align=center>$strtoomanytoshow ($usercount) </p>";

    } else {

        if ($search) {
            echo "<p align=center>($strsearchresults : $search)</p>";
        }
         
        if (!$users = get_users(true, $search, true, $adminlist)) {
            error("Could not get users!");
        }

        foreach ($users as $user) {
            echo "<p align=left><a href=\"admin.php?add=$user->id\"".
                   "title=\"$straddadmin\"><img src=\"../pix/t/left.gif\"".
                   "border=0></a>&nbsp;&nbsp;".fullname($user).", $user->email";
        }
    }

    if ($search or $usercount > MAX_USERS_PER_PAGE) {
        echo "<form action=admin.php method=post>";
        echo "<input type=text name=search size=20>";
        echo "<input type=submit value=\"$searchstring\">";
        echo "</form>";
    }

    echo "</tr></table>";

    print_footer();

?>
