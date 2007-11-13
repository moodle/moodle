<?php // $Id$

require_once('../../config.php');
require_once($CFG->libdir.'/adminlib.php');

$sort = optional_param('sort', 'fullname', PARAM_ALPHA);
$dir  = optional_param('dir', 'asc', PARAM_ALPHA);

admin_externalpage_setup('userbulk');

$return = $CFG->wwwroot.'/'.$CFG->admin.'/user/user_bulk.php';

if (empty($SESSION->bulk_users)) {
    redirect($return);
}

$users = $SESSION->bulk_users;
$usertotal = get_users(false);
$usercount = count($users);

$strnever = get_string('never');

admin_externalpage_print_header();

$countries = get_list_of_countries();

foreach ($users as $key => $id) {
    $user = get_record('user', 'id', $id, null, null, null, null, 'id, firstname, lastname, username, email, country, lastaccess, city');
    $user->fullname = fullname($user, true);
    $user->country = @$countries[$user->country];
    unset($user->firstname);
    unset($user->lastname);
    $users[$key] = $user;
}
unset($countries);

// Need to sort by date
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
$columns = array('fullname', /*'username', */'email', 'city', 'country', 'lastaccess');
foreach ($columns as $column) {
    $strtitle = get_string($column);
    if ($sort != $column) {
        $columnicon = '';
        $columndir = 'asc';
    } else {
        $columndir = $dir == 'asc' ? 'desc' : 'asc';
        $columnicon = ' <img src="'.$CFG->pixpath.'/t/'.($dir == 'asc' ? 'down' : 'up' ).'.gif" alt="" />';
    }
    $table->head[] = '<a href="user_bulk_display.php?sort='.$column.'&amp;dir='.$columndir.'">'.$strtitle.'</a>'.$columnicon;
    $table->align[] = 'left';
}

foreach($users as $user) {
    $table->data[] = array (
        '<a href="'.$CFG->wwwroot.'/user/view.php?id='.$user->id.'&amp;course='.SITEID.'">'.$user->fullname.'</a>',
//        $user->username,
        $user->email,
        $user->city,
        $user->country,
        $user->lastaccess ? format_time(time() - $user->lastaccess) : $strnever
    );
}

print_heading("$usercount / $usertotal ".get_string('users'));
print_table($table);

print_continue($return);

admin_externalpage_print_footer();
?>