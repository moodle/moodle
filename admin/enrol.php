<?PHP  // $Id$
       // enrol.php - allows admin to edit all enrollment variables
       //             Yes, enrol is correct English spelling.

    include('../config.php');

    $enrol = optional_param('enrol', $CFG->enrol, PARAM_SAFEDIR);

    require_login();

    if (!$site = get_site()) {
        redirect("index.php");
    }

    if (!isadmin()) {
        error("Only the admin can use this page");
    }

    if (!confirm_sesskey()) {
        error(get_string('confirmsesskeybad', 'error'));
    }

    require_once("$CFG->dirroot/enrol/enrol.class.php");   /// Open the factory class

/// Save settings

    if ($frm = data_submitted()) {
        if (empty($frm->enable)) {
            $frm->enable = array();
        }
        if (empty($frm->default)) {
            $frm->default = '';
        }
        if ($frm->default && $frm->default != 'internal' && !in_array($frm->default, $frm->enable)) {
            $frm->enable[] = $frm->default;
        }
        asort($frm->enable);
        $frm->enable = array_merge(array('internal'), $frm->enable); // make sure internal plugin is called first
        set_config('enrol_plugins_enabled', implode(',', $frm->enable));
        set_config('enrol', $frm->default);
        redirect("enrol.php?sesskey=$USER->sesskey", get_string("changessaved"), 1);
    }

/// Print the form

    $str = get_strings(array('enrolmentplugins', 'users', 'administration', 'settings', 'edit'));

    print_header("$site->shortname: $str->enrolmentplugins", "$site->fullname",
                  "<a href=\"index.php\">$str->administration</a> -> 
                   <a href=\"users.php\">$str->users</a> -> $str->enrolmentplugins");

    $modules = get_list_of_plugins("enrol");
    $options = array();
    foreach ($modules as $module) {
        $options[$module] = get_string("enrolname", "enrol_$module");
    }
    asort($options);

    print_simple_box(get_string('configenrolmentplugins', 'admin'), 'center', '700');

    echo "<form target=\"{$CFG->framename}\" name=\"enrolmenu\" method=\"post\" action=\"enrol.php\">";
    echo "<input type=\"hidden\" name=\"sesskey\" value=\"".$USER->sesskey."\">";

    $table = new stdClass();
    $table->head = array(get_string('name'), get_string('enable'), get_string('default'), $str->settings);
    $table->align = array('left', 'center', 'center', 'center');
    $table->size = array('60%', '', '', '15%');
    $table->width = '700';
    $table->data = array();

    $modules = get_list_of_plugins("enrol");
    foreach ($modules as $module) {
        $name = get_string("enrolname", "enrol_$module");
        $plugin = enrolment_factory::factory($module);
        $enable = '<input type="checkbox" name="enable[]" value="'.$module.'"';
        if (stristr($CFG->enrol_plugins_enabled, $module) !== false) {
            $enable .= ' checked="checked"';
        }
        if ($module == 'internal') {
            $enable .= ' disabled="disabled"';
        }
        $enable .= ' />';
        if (method_exists($plugin, 'print_entry')) {
            $default = '<input type="radio" name="default" value="'.$module.'"';
            if ($CFG->enrol == $module) {
                $default .= ' checked="checked"';
            }
            $default .= ' />';
        } else {
            $default = '';
        }
        $table->data[$name] = array($name, $enable, $default,
                                '<a href="enrol_config.php?sesskey='.$USER->sesskey.'&amp;enrol='.$module.'">'.$str->edit.'</a>');
    }
    asort($table->data);

    print_table($table);

    echo "<center><input type=\"submit\" value=\"".get_string("savechanges")."\"></center>\n";
    echo "</form>";

    print_footer();

?>