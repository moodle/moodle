<?php // $Id$

// detects settings that were added during an upgrade, displays a screen for the admin to
// modify them, and then processes modifications

require_once('../config.php');
require_once($CFG->libdir.'/adminlib.php');

admin_externalpage_setup('upgradesettings'); // now hidden page

// a caveat: we're depending on only having one admin access this page at once. why? the following line
// (the function call to find_new_settings) must have the EXACT SAME RETURN VALUE both times that this
// page is loaded (i.e. both when we're displaying the form and then when we process the form's input).
// if the return values don't match, we could potentially lose changes that the admin is making.

$newsettingshtml = output_new_settings_by_page(admin_get_root());

// first we deal with the case where there are no new settings to be set
if ($newsettingshtml == '') {
    redirect($CFG->wwwroot . '/' . $CFG->admin . '/index.php');
    die;
}

// now we'll deal with the case that the admin has submitted the form with new settings
if ($data = data_submitted()) {
    $unslashed = (array)stripslashes_recursive($data);
    if (confirm_sesskey()) {
        $newsettings = find_new_settings(admin_get_root());
        $errors = '';

        foreach($newsettings as $newsetting) {
            if (isset($unslashed['s_' . $newsetting->name])) {
                $errors .= $newsetting->write_setting($unslashed['s_' . $newsetting->name]);
            } else {
                $errors .= $newsetting->write_setting($newsetting->defaultsetting);
            }
        }

        if (empty($errors)) {
            // there must be either redirect without message or continue button or else upgrade would be sometimes broken
            redirect($CFG->wwwroot . '/' . $CFG->admin . '/index.php');
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

print_simple_box(get_string('upgradesettingsintro','admin'),'','100%','',5,'generalbox','');

echo '<form action="upgradesettings.php" method="post" id="adminsettings">';
echo '<input type="hidden" name="sesskey" value="' . sesskey() . '" />';
echo '<fieldset>';
echo '<div class="clearer"><!-- --></div>';
echo $newsettingshtml;
echo '</fieldset>';
echo '<div class="form-buttons"><input class="form-submit" type="submit" value="' . get_string('savechanges','admin') . '" /></div>';
echo '</form>';

admin_externalpage_print_footer();


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

function output_new_settings_by_page(&$node) {

    if (is_a($node, 'admin_category')) {
        $entries = array_keys($node->children);
        $return = '';
        foreach ($entries as $entry) {
            $return .= output_new_settings_by_page($node->children[$entry]);
        }
        return $return;
    }

    if (is_a($node, 'admin_settingpage')) {
        $newsettings = array();
        foreach ($node->settings as $setting) {
            if ($setting->get_setting() === NULL) {
                $newsettings[] =& $setting;
            }
            unset($setting); // needed to prevent odd (imho) reference behaviour
                             // see http://www.php.net/manual/en/language.references.whatdo.php#AEN6399
        }
        $return = '';
        if (count($newsettings) > 0) {
            $return .= print_heading(get_string('upgradesettings','admin').' - '.$node->visiblename, '', 2, 'main', true);
            $return .= '<fieldset class="adminsettings">' . "\n";
            foreach ($newsettings as $newsetting) {
                $return .= '<div class="clearer"><!-- --></div>' . "\n";
                $return .= $newsetting->output_html();
            }
            $return .= '</fieldset>';
        }
        return $return;
    }

    return '';

}

?>
