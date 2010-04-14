<?php

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

echo $OUTPUT->header();

$countries = get_string_manager()->get_list_of_countries(true);

foreach ($users as $key => $id) {
    $user = $DB->get_record('user', array('id'=>$id), 'id, firstname, lastname, username, email, country, lastaccess, city');
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

$table = new html_table();
$table->width = "95%";
$columns = array('fullname', /*'username', */'email', 'city', 'country', 'lastaccess');
foreach ($columns as $column) {
    $strtitle = get_string($column);
    if ($sort != $column) {
        $columnicon = '';
        $columndir = 'asc';
    } else {
        $columndir = $dir == 'asc' ? 'desc' : 'asc';
        $columnicon = ' <img src="'.$OUTPUT->pix_url('t/'.($dir == 'asc' ? 'down' : 'up' )).'f" alt="" />';
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

echo $OUTPUT->heading("$usercount / $usertotal ".get_string('users'));
echo html_writer::table($table);

echo $OUTPUT->continue_button($return);

echo $OUTPUT->footer();
