<?php // $Id$

// searches for admin settings

require_once('../config.php');
require_once($CFG->libdir.'/adminlib.php');

$query = trim(stripslashes(optional_param('query', '', PARAM_NOTAGS)));  // Search string

$adminroot =& admin_get_root(); // need all settings here
$adminroot->search = $query; // So we can reference it in search boxes later in this invocation
$statusmsg = '';
$errormsg  = '';
$focus = '';

admin_externalpage_setup('search'); // now hidden page

// now we'll deal with the case that the admin has submitted the form with changed settings
if ($data = data_submitted() and confirm_sesskey()) {
    if (admin_write_settings($data)) {
        $statusmsg = get_string('changessaved');
    }
    $adminroot =& admin_get_root(true); //reload tree

    if (!empty($adminroot->errors)) {
        $errormsg = get_string('errorwithsettings', 'admin');
        $firsterror = reset($adminroot->errors);
        $focus = $firsterror->id;
    }
}

// and finally, if we get here, then there are matching settings and we have to print a form
// to modify them
admin_externalpage_print_header($focus);

if ($errormsg !== '') {
    notify ($errormsg);

} else if ($statusmsg !== '') {
    notify ($statusmsg, 'notifysuccess');
}

$resultshtml = admin_search_settings_html($query); // case insensitive search only

echo '<form action="search.php" method="post" id="adminsettings">';
echo '<div>';
echo '<input type="hidden" name="sesskey" value="'.sesskey().'" />';
echo '<input type="hidden" name="query" value="'.s($query).'" />';
echo '</div>';
echo '<fieldset>';
echo '<div class="clearer"><!-- --></div>';
if ($resultshtml != '') {
    echo $resultshtml;
} else {
    echo get_string('noresults','admin');
}
echo '</fieldset>';
echo '</form>';

print_footer();

?>
