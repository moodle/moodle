<?php

require_once dirname(dirname(__FILE__)) . '/config.php';
httpsrequired();

$username = required_param('u', PARAM_ALPHANUM);
$sesskey = sesskey();

// if you are logged in then you shouldn't be here
if (isloggedin() and !isguestuser()) {
    redirect( $CFG->wwwroot.'/', get_string('loginalready'), 5);
}

$PAGE->set_url(new moodle_url($CFG->wwwroot.'/login/mnet_email.php', array('u'=>$username)));

$mnetidprovider = get_string('mnetidprovider','mnet');
$PAGE->navbar->add($mnetidprovider);
$PAGE->set_title($mnetidprovider);
$PAGE->set_heading($mnetidprovider);
$PAGE->set_focuscontrol('email');

echo $OUTPUT->header();
echo $OUTPUT->notification(get_string('mnetidproviderdesc', 'mnet'));

if ($form = data_submitted() and confirm_sesskey()) {
    if ($user = $DB->get_record_select('user', 'username = ? AND email = ? AND mnethostid != ?', array($username,$form->email, $CFG->mnet_localhost_id))) {
        if (!empty($user->mnethostid) and $host = $DB->get_record('mnet_host', array('id'=>$user->mnethostid))) {
            $link = "<a href=\"{$host->wwwroot}/login/\">{$host->name}</a>";
            notice(get_string('mnetidprovidermsg','mnet',$link));
        }
    }
    if (empty($link)) {
        notice(get_string('mnetidprovidernotfound', 'mnet'));
    }
}

echo '<p>&nbsp;</p>';
echo $OUTPUT->box_start('generalbox boxaligncenter boxwidthnormal');

?>
  <form method="post">
    <input type="hidden" name="sesskey" value="<?php echo $sesskey; ?>">
    <?php echo get_string('email') ?>:
    <input type="text" name="email" id="email" size="" maxlength="100" />
    <input type="submit" value="<?php echo get_string('findlogin','mnet'); ?>" />
  </form>
<?php

echo $OUTPUT->box_end();
echo $OUTPUT->footer();
