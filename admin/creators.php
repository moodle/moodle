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

    if (!confirm_sesskey()) {
        error(get_string('confirmsesskeybad', 'error'));
    }

    $primaryadmin = get_admin();

/// assign all of the configurable language strings
    $stringstoload = array (
        "assigncreators",
        "administration",
        "existingcreators",
        "potentialcreators",
        "search",
        "users",
        "searchresults",
        "showall"
        );

    foreach ($stringstoload as $stringtoload){
        $strstringtoload = "str" . $stringtoload;
        $$strstringtoload = get_string($stringtoload);
    }

	print_header("$site->shortname: $strassigncreators", 
                 "$site->fullname", 
                 "<a href=\"index.php\">$stradministration</a> -> <a href=\"users.php\">$strusers</a> ->
                  $strassigncreators", "creatorsform.searchtext");


    if (!$frm = data_submitted()) {
        print_simple_box("<center>".get_string("adminhelpassigncreators")."</center>", "center", "50%");

/// A form was submitted so process the input

    } else {
        if (!empty($frm->add) and !empty($frm->addselect)) {
            foreach ($frm->addselect as $addcreator) {
                if (! add_creator($addcreator)) {
                    error("Could not add course creator with user id $addcreator!");
                }
            }
        } else if (!empty($frm->remove) and !empty($frm->removeselect)) {
            foreach ($frm->removeselect as $removecreator) {
                if (! remove_creator($removecreator)) {
                    error("Could not remove course creator with user id $removecreator!");
                }
            }
        } else if (!empty($frm->showall)) {
            unset($frm->searchtext);
            $frm->previoussearch = 0;
        }
    }

/// Is there a current search?
    $previoussearch = (!empty($frm->search) or ($frm->previoussearch == 1)) ;


/// Get all existing creators
    if (! $creators = get_creators()) {
        $creators = array();
    }

    $creatorsarray = array();
    foreach ($creators as $creator) {
        $creatorsarray[] = $creator->id;
    }
    $creatorlist = implode(',', $creatorsarray);

    unset($creatorarray);


/// Get search results excluding any current admins
    if (!empty($frm->searchtext) and $previoussearch) {
        $searchusers = get_users(true, $frm->searchtext, true, $creatorlist, 'firstname ASC, lastname ASC',
                                      '', '', 0, 99999, 'id, firstname, lastname, email');
        $usercount = get_users(false, '', true, $creatorlist);
    }

/// If no search results then get potential users excluding current creators
    if (empty($searchusers)) {
        if (!$users = get_users(true, '', true, $creatorlist, 'firstname ASC, lastname ASC', '', '',
                                0, 99999, 'id, firstname, lastname, email') ) {
            $users = array();
        }
        $usercount = count($users);
    }

    $searchtext = (isset($frm->searchtext)) ? $frm->searchtext : "";
    $previoussearch = ($previoussearch) ? '1' : '0';

    include('./creators.html');

    print_footer();

?>
