<?php
/**
* script for bulk user multi enrol operations
*/

die('this needs to be rewritten to use new enrol framework, sorry');  //TODO: MDL-24064

require_once('../../config.php');
require_once($CFG->libdir.'/adminlib.php');
$processed = optional_param('processed', '', PARAM_BOOL);
$sort = optional_param('sort', 'fullname', PARAM_ALPHA); //Sort by full name
$dir  = optional_param('dir', 'asc', PARAM_ALPHA);       //Order to sort (ASC)

require_login();
admin_externalpage_setup('userbulk');
require_capability('moodle/role:assign', get_context_instance(CONTEXT_SYSTEM)); //TODO: use some enrol cap
$return = $CFG->wwwroot.'/'.$CFG->admin.'/user/user_bulk.php';
//If no users selected then return to user_bulk.php
if (empty($SESSION->bulk_users)) {
    redirect($return);
}
$users = $SESSION->bulk_users; //Get users to display
$usertotal = get_users(false); //Total number of users registered
$usercount = count($users);    //number of users

echo $OUTPUT->header();

//take user info
foreach ($users as $key => $id) {
    $user = $DB->get_record('user', array('id'=>$id));
    $user->fullname = fullname($user, true);
    unset($user->firstname);
    unset($user->lastname);
    $users[$key] = $user;
}

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

//Take courses data (id, shortname, and fullname)
$courses = get_courses_page(1, 'c.sortorder ASC', 'c.id,c.shortname,c.fullname,c.visible', $totalcount);
$table = new html_table();
$table->width = "95%";
$columns = array('fullname');
foreach ($courses as $v)
{
    $columns[] = $v->shortname;
}

//Print columns headers from table
foreach ($columns as $column) {
    $strtitle = $column;
    if ($sort != $column) {
        $columnicon = '';
        $columndir = 'asc';
    } else {
        $columndir = ($dir == 'asc') ? 'desc' : 'asc';
        $columnicon = ' <img src="'.$OUTPUT->pix_url('t/'.($dir == 'asc' ? 'down' : 'up' )).'" alt="" />';
    }
    $table->head[] = '<a href="user_bulk_enrol.php?sort='.$column.'&amp;dir='.$columndir.'">'.$strtitle.'</a>'.$columnicon;
    $table->align[] = 'left';
}

// process data submitting
if(!empty($processed)) {
    //Process data form here
    $total = count($courses) * count($users);

    for ( $i = 0; $i < $total; $i++ )
    {
        $param = "selected".$i;
        $info = optional_param($param, '', PARAM_SEQUENCE);
        /**
         * user id:    ids[0]
         * course id:  ids[1]
         * enrol stat: ids[2]
         */
        $ids = explode(',', $info);
        if(!empty($ids[2])) {
            $context = get_context_instance(CONTEXT_COURSE, $ids[1]);
            role_assign(5, $ids[0], $context->id); //TODO: horrible!!
        } else {
            if( empty($ids[1] ) ) {
                continue;
            }
            $context = get_context_instance(CONTEXT_COURSE, $ids[1]);
            role_unassign(5, $ids[0], $context->id);
        }
    }
    redirect($return, get_string('changessaved')); //TODO: horrible!!
}

//Form beginning
echo '<form id="multienrol" name="multienrol" method="post" action="user_bulk_enrol.php">';
echo '<input type="hidden" name="processed" value="yes" />';
$count = 0;
foreach($users as $user) {
    $temparray = array (
        '<a href="'.$CFG->wwwroot.'/user/view.php?id='.$user->id.'&amp;course='.SITEID.'">'.$user->fullname.'</a>'
    );
    $mycourses = enrol_get_users_courses($user->id, false);
    foreach($courses as $acourse) {
        $state = '';
        if (isset($mycourses[$acourse->id])) {
            $state = 'checked="checked"';
        }
        $temparray[] = '<input type="hidden" name="selected' . $count .
                '" value="' . $user->id . ',' . $acourse->id . ',0" />' .
                '<input type="checkbox" name="selected' . $count .
                '" value="' . $user->id . ',' . $acourse->id . ',1" ' . $state . '/>';
        $count++;
    }
    $table->data[] = $temparray;
}
echo $OUTPUT->heading("$usercount / $usertotal ".get_string('users'));
echo html_writer::table($table);
echo '<div class="continuebutton">';
echo '<input type="submit" name="multienrolsubmit" value="save changes" />';
echo '</div>';
echo '</form>';

echo $OUTPUT->footer();
