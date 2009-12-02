<?php

require_once dirname(dirname(__FILE__)) . '/config.php';
httpsrequired();

$username = required_param('u', PARAM_ALPHANUM);
$sesskey = sesskey();

// if you are logged in then you shouldn't be here
if (isloggedin() and !isguestuser()) {
    redirect( $CFG->wwwroot.'/', get_string('loginalready'), 5);
}
$mnetidprovider = get_string('mnetidprovider','mnet');
$navigation = build_navigation(array(array('name' => $mnetidprovider, 'link' => null, 'type' => 'misc')));

print_header($mnetidprovider, $mnetidprovider, $navigation, 'form.email' );

if ($form = data_submitted() and confirm_sesskey()) {
    if ($user = get_record('user', 'username', $username, 'email', $form->email)) {
        if (!empty($user->mnethostid) and $host = get_record('mnet_host', 'id', $user->mnethostid)) {
            $link = "<a href=\"{$host->wwwroot}/login/\">{$host->name}</a>";
            notice(get_string('mnetidprovidermsg','mnet',$link));
        }
    }
}

echo '<p>&nbsp;</p>';
print_simple_box_start('center','50%','','20');

?>
  <form method="post">
    <input type="hidden" name="sesskey" value="<?php echo $sesskey; ?>">
    <?php echo get_string('email') ?>:
    <input type="text" name="email" size="" maxlength="100" />
    <input type="submit" value="<?php echo get_string('findlogin','mnet'); ?>" />
  </form>
<?php

print_simple_box_end();
print_footer();

?>
