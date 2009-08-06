<?php  //$Id$

require_once("../../config.php");
require_once("lib.php");

$confirm = optional_param('confirm', false, PARAM_BOOL);

require_login();

$return = $CFG->wwwroot.'/';

if (isguestuser()) {
    redirect($return);
}

$strunsubscribeall = get_string('unsubscribeall', 'forum');
$navlinks = array(array('name' => get_string('modulename', 'forum'), 'link' => null, 'type' => 'misc'),
                  array('name' => $strunsubscribeall, 'link' => null, 'type' => 'misc'));
$navigation = build_navigation($navlinks);

print_header($strunsubscribeall, format_string($COURSE->fullname), $navigation);
echo $OUTPUT->heading($strunsubscribeall);

if (data_submitted() and $confirm and confirm_sesskey()) {
    $DB->delete_records('forum_subscriptions', array('userid'=>$USER->id));
    $DB->set_field('user', 'autosubscribe', 0, array('id'=>$USER->id));
    print_box(get_string('unsubscribealldone', 'forum'));
    print_continue($return);
    print_footer();
    die;

} else {
    $a = $DB->count_records('forum_subscriptions', array('userid'=>$USER->id));

    if ($a) {
        $msg = get_string('unsubscribeallconfirm', 'forum', $a);
        notice_yesno($msg, 'unsubscribeall.php', $return, array('confirm'=>1, 'sesskey'=>sesskey()), NULL, 'post', 'get');
        print_footer();
        die;

    } else {
        print_box(get_string('unsubscribeallempty', 'forum'));
        print_continue($return);
        print_footer();
        die;
    }
}
