<?php

// detects settings that were added during an upgrade, displays a screen for the admin to
// modify them, and then processes modifications

require_once('../config.php');
require_once($CFG->libdir.'/adminlib.php');

$return = optional_param('return', '', PARAM_ALPHA);

/// no guest autologin
require_login(0, false);

admin_externalpage_setup('upgradesettings'); // now hidden page
$PAGE->set_pagelayout('maintenance'); // do not print any blocks or other rubbish, we want to force saving
$PAGE->blocks->show_only_fake_blocks();
$adminroot = admin_get_root(); // need all settings

// now we'll deal with the case that the admin has submitted the form with new settings
if ($data = data_submitted() and confirm_sesskey()) {
    $count = admin_write_settings($data);
    $adminroot = admin_get_root(true); //reload tree
}

$newsettings = admin_output_new_settings_by_page($adminroot);
if (isset($newsettings['frontpagesettings'])) {
    $frontpage = $newsettings['frontpagesettings'];
    unset($newsettings['frontpagesettings']);
    array_unshift($newsettings, $frontpage);
}
$newsettingshtml = implode($newsettings);
unset($newsettings);

$focus = '';

if (empty($adminroot->errors) and $newsettingshtml === '') {
    // there must be either redirect without message or continue button or else upgrade would be sometimes broken
    if ($return == 'site') {
        redirect("$CFG->wwwroot/");
    } else {
        redirect("$CFG->wwwroot/$CFG->admin/index.php");
    }
}

if (!empty($adminroot->errors)) {
    $firsterror = reset($adminroot->errors);
    $focus = $firsterror->id;
}

// and finally, if we get here, then there are new settings and we have to print a form
// to modify them
echo $OUTPUT->header($focus);

if (!empty($SITE->fullname) and !empty($SITE->shortname)) {
    echo $OUTPUT->box(get_string('upgradesettingsintro','admin'), 'generalbox');
}

echo '<form action="upgradesettings.php" method="post" id="adminsettings">';
echo '<div>';
echo '<input type="hidden" name="sesskey" value="'.sesskey().'" />';
echo '<input type="hidden" name="return" value="'.$return.'" />';
echo '<fieldset>';
echo '<div class="clearer"><!-- --></div>';
echo $newsettingshtml;
echo '</fieldset>';
echo '<div class="form-buttons"><input class="form-submit" type="submit" value="'.get_string('savechanges','admin').'" /></div>';
echo '</div>';
echo '</form>';

echo $OUTPUT->footer();


