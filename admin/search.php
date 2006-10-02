<?php // $Id$

// searches for admin settings

require_once('../config.php');
require_once($CFG->libdir.'/adminlib.php');

$adminroot = admin_get_root();
admin_externalpage_setup('search', $adminroot); // now hidden page

$query = required_param('query', PARAM_ALPHAEXT);

$resultshtml = search_settings_html(admin_get_root(), $query);

// now we'll deal with the case that the admin has submitted the form with changed settings
if ($data = data_submitted()) {
    $data = (array)$data;
    if (confirm_sesskey()) {
        $changedsettings = search_settings(admin_get_root(), $query);
        $errors = '';

        foreach($changedsettings as $changedsetting) {
            if (isset($data['s_' . $changedsetting->name])) {
                $errors .= $changedsetting->write_setting($data['s_' . $changedsetting->name]);
            } else {
                $errors .= $changedsetting->write_setting($changedsetting->defaultsetting);
            }
        }

        if (empty($errors)) {
            // there must be either redirect without message or continue button or else upgrade would be sometimes broken
            redirect($CFG->wwwroot . '/' . $CFG->admin . '/search.php?query=' . $query);
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

// and finally, if we get here, then there are matching settings and we have to print a form
// to modify them
admin_externalpage_print_header($adminroot);

// print_simple_box(get_string('upgradesettingsintro','admin'),'','100%','',5,'generalbox','');

echo '<form action="search.php" method="post" name="mainform" id="adminsettings">';
echo '<input type="hidden" name="sesskey" value="' . sesskey() . '" />';
echo '<input type="hidden" name="query" value="' . $query . '" />';
echo '<fieldset>';
echo '<div class="clearer"><!-- --></div>';
if ($resultshtml != '') {
    echo $resultshtml;
    echo '<div class="form-buttons"><input class="form-submit" type="submit" value="' . get_string('savechanges','admin') . '" /></div>';
} else {
    echo get_string('noresults','admin');
}
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
