<?php // $Id$

// detects settings that were added during an upgrade, displays a screen for the admin to 
// modify them, and then processes modifications

require_once('../config.php');
require_once($CFG->dirroot . '/' . $CFG->admin . '/adminlib.php');

admin_externalpage_setup('adminnotifications'); // we pretend to be the adminnotifications page... don't wanna show up in the menu :)

// a caveat: we're depending on only having one admin access this page at once. why? the following line
// (the function call to find_new_settings) must have the EXACT SAME RETURN VALUE both times that this
// page is loaded (i.e. both when we're displaying the form and then when we process the form's input).
// if the return values don't match, we could potentially lose changes that the admin is making.
$newsettings = find_new_settings($ADMIN);

// first we deal with the case where there are no new settings to be set
if (count($newsettings) === 0) {
    redirect($CFG->wwwroot . '/' . $CFG->admin . '/index.php', get_string('nonewsettings','admin'),1);	
    die;
}

// now we'll deal with the case that the admin has submitted the form with new settings
if ($data = data_submitted()) {
    $data = (array)$data;
    if (confirm_sesskey()) {
        $errors = '';

        foreach($newsettings as $newsetting) {
            if (isset($data['s_' . $newsetting->name])) {
                $errors .= $newsetting->write_setting($data['s_' . $newsetting->name]);
            } else {
                $errors .= $newsetting->write_setting($newsetting->defaultsetting);
            }
        }

        if (empty($errors)) {
            redirect($CFG->wwwroot . '/' . $CFG->admin . '/index.php', get_string('changessaved'),1);
            die;
        } else {
            error(get_string('errorwithsettings', 'admin') . ' <br />' . $errors);
            die;
        }
    } else {
        error(get_string('confirmsesskeybad', 'error'));
        die;
    }

}

// and finally, if we get here, then there are new settings and we have to print a form
// to modify them
admin_externalpage_print_header();


echo '<form action="upgradesettings.php" method="post" name="mainform">';
echo '<input type="hidden" name="sesskey" value="' . $USER->sesskey . '" />';
print_simple_box_start('','100%','',5,'generalbox','');
echo '<table class="generaltable" width="100%" border="0" align="center" cellpadding="5" cellspacing="1">' . "\n";
echo '<tr><td colspan="2">' . get_string('modifiedsettingsintro','admin') . '</td></tr>';
foreach ($newsettings as $newsetting) {
    echo $newsetting->output_html();
}
echo '</table>';
echo '<center><input type="submit" value="Save Changes" /></center>';
print_simple_box_end();
echo '</form>';

admin_externalpage_print_footer();



// function that we use (vital to this page working)

/**
 * Find settings that have not been initialized (e.g. during initial install or an upgrade).
 * 
 * Tests each setting's get_setting() method. If the result is NULL, we consider the setting
 * to be uninitialized.
 *
 * @param string &$node The node at which to start searching. Should be $ADMIN for all external calls to this function.
 * @return array An array containing admin_setting objects that haven't yet been initialized
 */
function find_new_settings(&$node) {

    if (is_a($node, 'admin_category')) {
        $return = array();
        $entries = array_keys($node->children);
        foreach ($entries as $entry) {
            $return = array_merge($return, find_new_settings($node->children[$entry]));
        }
        return $return;
    } 

    if (is_a($node, 'admin_settingpage')) { 
        $return = array();
        foreach ($node->settings as $setting) {
            if ($setting->get_setting() === NULL) {
                $return[] =& $setting;
            }
            unset($setting); // needed to prevent odd (imho) reference behaviour
                             // see http://www.php.net/manual/en/language.references.whatdo.php#AEN6399
        }
        return $return;
    }

    return array();

}

?>