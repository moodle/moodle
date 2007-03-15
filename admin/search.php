<?php // $Id$

// searches for admin settings

require_once('../config.php');
require_once($CFG->libdir.'/adminlib.php');

$query = trim(stripslashes_safe(required_param('query', PARAM_NOTAGS)));  // Search string

$adminroot = admin_get_root();
admin_externalpage_setup('search', $adminroot); // now hidden page

$CFG->adminsearchquery = $query;  // So we can reference it in search boxes later in this invocation


// now we'll deal with the case that the admin has submitted the form with changed settings

$statusmsg = '';

if ($data = data_submitted()) {
    $unslashed = (array)stripslashes_recursive($data);
    if (confirm_sesskey()) {
        $olddbsessions = !empty($CFG->dbsessions);
        $changedsettings = search_settings(admin_get_root(), $query);
        $errors = '';

        foreach($changedsettings as $changedsetting) {
            if (!isset($unslashed['s_' . $changedsetting->name])) {
                $unslashed['s_' . $changedsetting->name] = ''; // needed for checkboxes
            }
            $errors .= $changedsetting->write_setting($unslashed['s_' . $changedsetting->name]);
        }

        if ($olddbsessions != !empty($CFG->dbsessions)) {
            require_logout();
        }

        if (empty($errors)) {
            $statusmsg = get_string('changessaved');
        } else {
            $statusmsg = get_string('errorwithsettings', 'admin') . ' <br />' . $errors;
        }
    } else {
        error(get_string('confirmsesskeybad', 'error'));
    }
    // now update $SITE - it might have been changed
    $SITE = get_record('course', 'id', $SITE->id);
    $COURSE = clone($SITE);
}

// and finally, if we get here, then there are matching settings and we have to print a form
// to modify them
admin_externalpage_print_header($adminroot);

if ($statusmsg != '') {
    notify ($statusmsg);
}

$resultshtml = search_settings_html(admin_get_root(), $query);

echo '<form action="search.php" method="post" id="adminsettings">';
echo '<input type="hidden" name="sesskey" value="' . sesskey() . '" />';
echo '<input type="hidden" name="query" value="' . s($query) . '" />';
echo '<fieldset>';
echo '<div class="clearer"><!-- --></div>';
if ($resultshtml != '') {
    echo $resultshtml;
    echo '<div class="form-buttons"><input class="form-submit" type="submit" value="' . get_string('savechanges','admin') . '" /></div>';
} else {
    echo get_string('noresults','admin');
}
echo '</fieldset>';
echo '</form>';

admin_externalpage_print_footer($adminroot);


/**
 * Find settings using a query.
 *
 * @param string &$node The node at which to start searching. Should be $ADMIN for all external calls to this function.
 * @param string $query The search string.
 * @return array An array containing admin_setting objects that match $query.
 */
function search_settings(&$node, $query) {

    if (is_a($node, 'admin_category')) {
        $return = array();
        $entries = array_keys($node->children);
        foreach ($entries as $entry) {
            $return = array_merge($return, search_settings($node->children[$entry], $query));
        }
        return $return;
    }

    if (is_a($node, 'admin_settingpage')) {
        $return = array();
        foreach ($node->settings as $setting) {
            if (stristr($setting->name,$query) || stristr($setting->visiblename,$query) || stristr($setting->description,$query)) {
                $return[] =& $setting;
            }
            unset($setting); // needed to prevent odd (imho) reference behaviour
                             // see http://www.php.net/manual/en/language.references.whatdo.php#AEN6399
        }
        return $return;
    }

    return array();

}

function search_settings_html(&$node, $query) {

    global $CFG;

    if ($query == ''){
        return '';
    }

    if (is_a($node, 'admin_category')) {
        $entries = array_keys($node->children);
        $return = '';
        foreach ($entries as $entry) {
            $return .= search_settings_html($node->children[$entry], $query);
        }
        return $return;
    }

    if (is_a($node, 'admin_settingpage')) {
        $foundsettings = array();
        foreach ($node->settings as $setting) {
            if (stristr($setting->name,$query) || stristr($setting->visiblename,$query) || stristr($setting->description,$query)) {
                $foundsettings[] =& $setting;
            }
            unset($setting); // needed to prevent odd (imho) reference behaviour
                             // see http://www.php.net/manual/en/language.references.whatdo.php#AEN6399
        }
        $return = '';
        if (count($foundsettings) > 0) {
            $return .= print_heading(get_string('searchresults','admin').' - '. '<a href="' . $CFG->wwwroot . '/' . $CFG->admin . '/settings.php?section=' . $node->name . '">' . $node->visiblename . '</a>', '', 2, 'main', true);
            $return .= '<fieldset class="adminsettings">' . "\n";
            foreach ($foundsettings as $foundsetting) {
                $return .= '<div class="clearer"><!-- --></div>' . "\n";
                $return .= highlight($query,$foundsetting->output_html());
            }
            $return .= '</fieldset>';
        }
        return $return;
    }

    return '';

}

?>
