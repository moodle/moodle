<?php // $Id$

    require_once('../../config.php');
    require_once($CFG->libdir.'/adminlib.php');

    if(empty($SESSION->bulk_susers)) {
        redirect($CFG->wwwroot . '/admin/user/user_bulk.php');
    }
    $users = $SESSION->bulk_susers;
    $usertotal = get_users(false);
    $usercount = count($users);

    $sort         = optional_param('sort', 'fullname', PARAM_ALPHA);
    $dir          = optional_param('dir', 'asc', PARAM_ALPHA);
    
    $strnever = get_string('never');
    $sitecontext = get_context_instance(CONTEXT_SYSTEM, SITEID);
    $site = get_site();

    admin_externalpage_setup('userbulk');
    admin_externalpage_print_header();

    $countries =& get_list_of_countries();
    foreach ($users as $key => $id) {
        $user =& get_record('user', 'id', $id, null, null, null, null, 'id,firstname,lastname,username,email,country,lastaccess,city');
        $user->fullname = fullname($user, true);
        $user->country = @$countries[$user->country];
        unset($user->firstname);
        unset($user->lastname);
        $users[$key] = $user;
    }
    unset($countries);
    
    // Need to sort by data
    function sort_compare($a, $b) {
        global $sort, $dir;
        if($sort == 'lastaccess') {
            $rez = $b->lastaccess - $a->lastaccess;
        } else {
            $rez = strcasecmp(@$a->$sort, @$b->$sort);
        }
        return $dir == 'desc' ? -$rez : $rez;
    }
    usort($users, 'sort_compare');
    
    $table->width = "95%";
    $columns = array('fullname', 'username', 'email', 'city', 'country', 'lastaccess');
    foreach ($columns as $column) {
        $strtitle = get_string($column);
        if ($sort != $column) {
            $columnicon = '';
            $columndir = 'asc';
        } else {
            $columndir = $dir == 'asc' ? 'desc' : 'asc';
            $columnicon = ' <img src="' . $CFG->pixpath . '/t/' . ($dir == 'asc' ? 'down' : 'up' ). '.gif" alt="" />';
        }
        $table->head[] = '<a href="user_bulk_display.php?sort=' . $column . '&amp;dir=' . $columndir .'">' .$strtitle . '</a>' . $columnicon;
        $table->align[] = 'left';
    }

    foreach($users as $user) {
        $table->data[] = array (
            '<a href="' . $CFG->wwwroot . '/user/view.php?id=' . $user->id . '&amp;course=' . $site->id .'">' . $user->fullname .'</a>',
            $user->username,
            $user->email,
            $user->city,
            $user->country,
            $user->lastaccess ? format_time(time() - $user->lastaccess) : $strnever
        );
    }

    print_heading("$usercount / $usertotal ".get_string('users'));
    print_table($table);

    admin_externalpage_print_footer();
?>