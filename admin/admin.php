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
        "potentialadmins",
        "search",
        "users",
        "searchresults",
        "showall"
        );

    foreach ($stringstoload as $stringtoload){
        $strstringtoload = "str" . $stringtoload;
        $$strstringtoload = get_string($stringtoload);
    }

	print_header("$site->shortname: $strassignadmins", 
                 "$site->fullname", 
                 "<a href=\"index.php\">$stradministration</a> -> <a href=\"users.php\">$strusers</a> -> $strassignadmins", "adminform.searchtext");


    if (!$frm = data_submitted()) {
        print_simple_box("<center>".get_string("adminhelpassignadmins")."</center>", "center", "50%");

/// A form was submitted so process the input
    
    } else {
        if (!empty($frm->add) and !empty($frm->addselect)) {
            foreach ($frm->addselect as $addadmin) {
                if (! add_admin($addadmin)) {
                    error("Could not add admin with user id $addadmin!");
                }
            }
        } else if (!empty($frm->remove) and !empty($frm->removeselect)) {
            $admins = get_admins();
            if (count($admins) > count($frm->removeselect)) {
                foreach ($frm->removeselect as $removeadmin) {
                    if (! remove_admin($removeadmin)) {
                        error("Could not remove admin with user id $removeadmin!");
                    }
                }
            }
        } else if (!empty($frm->showall)) {
            unset($frm->searchtext);
            $frm->previoussearch = 0;
        }
    }

/// Is there a current search?
    $previoussearch = (!empty($frm->search) or ($frm->previoussearch == 1)) ;

/// Get all existing admins
    $admins = get_admins();


    $adminarray = array();
    foreach ($admins as $admin) {
        $adminarray[] = $admin->id;
    }
    $adminlist = implode(',', $adminarray);

    unset($adminarray);


/// Get search results excluding any current admins
    if (!empty($frm->searchtext) and $previoussearch) {
        $searchusers = get_users(true, $frm->searchtext, true, $adminlist, 'firstname ASC, lastname ASC',
                                      '', '', 0, 99999, 'id, firstname, lastname, email');
        $usercount = get_users(false, '', true, $adminlist);
    }

/// If no search results then get potential users excluding current admins
    if (empty($searchusers)) {
        if (!$users = get_users(true, '', true, $adminlist, 'firstname ASC, lastname ASC', '', '',
                                0, 99999, 'id, firstname, lastname, email') ) {
            $users = array();
        }
        $usercount = count($users);
    }

    $searchtext = (isset($frm->searchtext)) ? $frm->searchtext : "";
    $previoussearch = ($previoussearch) ? '1' : '0';

    include('./admin.html');

    print_footer();

?>
