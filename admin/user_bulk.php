<?php  //$Id$
    require_once('../config.php');
    require_once($CFG->libdir.'/adminlib.php');
    require_once($CFG->libdir.'/dmllib.php');

    require_once($CFG->dirroot.'/user/filters/text.php');
    require_once($CFG->dirroot.'/user/filters/select.php');
    require_once($CFG->dirroot.'/user/filters/courserole.php');
    require_once($CFG->dirroot.'/user/filters/globalrole.php');
    require_once($CFG->dirroot.'/user/filters/profilefield.php');
    require_once($CFG->dirroot.'/user/filters/yesno.php');

    require_once($CFG->dirroot.'/admin/user_bulk_form.php');
    define("MAX_USERS_PER_PAGE", 5000);

    $sitecontext = get_context_instance(CONTEXT_SYSTEM, SITEID);

    // array of bulk operations
    $actions = array();
    $actions[1] = get_string('confirm');
    if (has_capability('moodle/site:readallmessages', $sitecontext) && !empty($CFG->messaging)) {
        $actions[2] = get_string('messageselectadd');
    }
    $actions[3] = get_string('delete');

    // create the bulk operations form
    $user_bulk_form =& new user_bulk_form(null, $actions);
    // check if an action should be performed and do so
    switch ($user_bulk_form->getAction()) {
    case 1:
        include($CFG->dirroot . '/admin/user_bulk_confirm.php');
        return;
    case 2:
        include($CFG->dirroot . '/admin/user_bulk_message.php');
        return;
    case 3:
        include($CFG->dirroot . '/admin/user_bulk_delete.php');
        return;
    }

    // prepare user filter types
    $filters[] = new user_filter_text('username', get_string('username'), 'username');
    $filters[] = new user_filter_text('realname', get_string('fullname'), sql_fullname());
    $filters[] = new user_filter_text('email', get_string('email'), 'email');
    $filters[] = new user_filter_text('city', get_string('city'), 'city');
    $filters[] = new user_filter_select('country', get_string('country'), 'country', get_list_of_countries());
    $filters[] = new user_filter_yesno('confirmed', get_string('confirm'), 'confirmed');
    $filters[] = new user_filter_profilefield('profile', get_string('profile'));
    $filters[] = new user_filter_courserole('course', get_string('courserole', 'filters'));
    $filters[] = new user_filter_globalrole('system', get_string('globalrole', 'role'));
    
    // create the user filter form
    $user_filter_form =& new user_filter_form(null, $filters);
    
    // do output
    admin_externalpage_setup('userbulk');
    admin_externalpage_print_header();
    
    // put the user filter form first
    $user_filter_form->display();
    // get the SQL filter
    $where =& $user_filter_form->getSQLFilter('id<>1 AND NOT deleted');
    $ausercount = count_records_select('user', $where);
    // limit the number of options 
    $comment = null;
    if($ausercount <= MAX_USERS_PER_PAGE) {
        $user_bulk_form->setAvailableUsersSQL($where);
    } else {
        $comment = get_string('toomanytoshow');
    }
    $user_bulk_form->setUserCount($ausercount, $comment);
    // display the bulk user form
    $user_bulk_form->display();
    admin_externalpage_print_footer();
