<?PHP // $Id$
      // Admin only script to assign course creator rights to users

	require_once("../config.php");

    define("MAX_USERS_PER_PAGE", 50);

    optional_variable($search, "");
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
        "users",
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

	print_header("$site->shortname: $strassigncreators", 
                 "$site->fullname", 
                 "<a href=\"index.php\">$stradministration</a> -> <a href=\"users.php\">$strusers</a> ->
                  $strassigncreators", "");

/// Add a creator if one is specified

    if (!empty($_GET['add'])) {
        if (! add_creator($add)) {
            error("Could not add that creator!");
        }
    }

/// Remove a creator if one is specified.

    if (!empty($_GET['remove'])) {
        if (! remove_creator($remove)) {
            error("Could not remove that creator!");
        }
    }

/// Print a help notice about this page
    if (empty($add) and empty($remove) and empty($search)) {
        print_simple_box("<center>".get_string("adminhelpassigncreators")."</center>", "center", "50%");
    }

/// Get all existing creators
    $creators = get_creators();

/// Print the lists of existing and potential creators
    echo "<table cellpadding=2 cellspacing=10 align=center>";
    echo "<tr><th width=50%>$strexistingcreators</th><th width=50%>$strpotentialcreators</th></tr>";
    echo "<tr><td width=50% nowrap valign=top>";

/// First, show existing creators

    if (! $creators) { 
        echo "<p align=center>$strnoexistingcreators</a>";

        $creatorlist = "";

    } else {
        $creatorarray = array();
        foreach ($creators as $creator) {
            $creatorarray[] = $creator->id;
            echo "<p align=right>".fullname($creator, true).", $creator->email &nbsp;&nbsp; ";
                echo "<a href=\"creators.php?remove=$creator->id\"
                title=\"$strremovecreator\"><img src=\"../pix/t/right.gif\"
                border=0></a>";
            echo "</p>";
        }
        $creatorlist = implode(",",$creatorarray);
        unset($creatorarray);
    }

    echo "<td width=50% nowrap valign=top>";

/// Print list of potential creators

    $usercount = get_users(false, $search, true, $creatorlist);

    if ($usercount == 0) {
        echo "<p align=center>$strnopotentialcreators</p>";

    } else if ($usercount > MAX_USERS_PER_PAGE) {
        echo "<p align=center>$strtoomanytoshow ($usercount) </p>";

    } else {

        if ($search) {
            echo "<p align=center>($strsearchresults : $search)</p>";
        }

        if (!$users = get_users(true, $search, true, $creatorlist)) {
            error("Could not get users!");
        }

        foreach ($users as $user) {
            $fullname = fullname($user, TRUE);
            echo "<p align=left><a href=\"creators.php?add=$user->id\"".
                   "title=\"$straddcreator\"><img src=\"../pix/t/left.gif\"".
                   "border=0></a>&nbsp;&nbsp;$fullname, $user->email";
        }
    }

    if ($search or $usercount > MAX_USERS_PER_PAGE) {
        echo "<form action=creators.php method=post>";
        echo "<input type=text name=search size=20>";
        echo "<input type=submit value=\"$searchstring\">";
        echo "</form>";
    }

    echo "</tr></table>";

    print_footer();

?>
