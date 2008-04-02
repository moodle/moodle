<?PHP  // $Id$
       // enrol.php - allows admin to edit all enrollment variables
       //             Yes, enrol is correct English spelling.

    require_once('../config.php');
    require_once($CFG->libdir.'/adminlib.php');

    $enrol = optional_param('enrol', $CFG->enrol, PARAM_SAFEDIR);
    $CFG->pagepath = 'enrol';

    admin_externalpage_setup('enrolment');



    require_once("$CFG->dirroot/enrol/enrol.class.php");   /// Open the factory class

/// Save settings

    if ($frm = data_submitted()) {
        if (!confirm_sesskey()) {
            print_error('confirmsesskeybad', 'error');
        }
        if (empty($frm->enable)) {
            $frm->enable = array();
        }
        if (empty($frm->default)) {
            $frm->default = '';
        }
        if ($frm->default && $frm->default != 'manual' && !in_array($frm->default, $frm->enable)) {
            $frm->enable[] = $frm->default;
        }
        asort($frm->enable);
        $frm->enable = array_merge(array('manual'), $frm->enable); // make sure manual plugin is called first
        set_config('enrol_plugins_enabled', implode(',', $frm->enable));
        set_config('enrol', $frm->default);
        redirect("enrol.php", get_string("changessaved"), 1);
    }

/// Print the form

    $str = get_strings(array('enrolmentplugins', 'users', 'administration', 'settings', 'edit'));

    admin_externalpage_print_header();

    $modules = get_list_of_plugins("enrol");
    $options = array();
    foreach ($modules as $module) {
        $options[$module] = get_string("enrolname", "enrol_$module");
    }
    asort($options);

    print_simple_box(get_string('configenrolmentplugins', 'admin'), 'center', '700');

    echo "<form $CFG->frametarget id=\"enrolmenu\" method=\"post\" action=\"enrol.php\">";
    echo "<div>";
    echo "<input type=\"hidden\" name=\"sesskey\" value=\"".$USER->sesskey."\" />";

    $table = new stdClass();
    $table->head = array(get_string('name'), get_string('enable'), get_string('default'), $str->settings);
    $table->align = array('left', 'center', 'center', 'center');
    $table->size = array('60%', '', '', '15%');
    $table->width = '700';
    $table->data = array();

    $modules = get_list_of_plugins("enrol");
    $enabledplugins = explode(',', $CFG->enrol_plugins_enabled);
    foreach ($modules as $module) {

        // skip if directory is empty
        if (!file_exists("$CFG->dirroot/enrol/$module/enrol.php")) {
            continue;
        }

        $name = get_string("enrolname", "enrol_$module");
        $plugin = enrolment_factory::factory($module);
        $enable = '<input type="checkbox" name="enable[]" value="'.$module.'"';
        if (in_array($module, $enabledplugins)) {
            $enable .= ' checked="checked"';
        }
        if ($module == 'manual') {
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
                                '<a href="enrol_config.php?enrol='.$module.'">'.$str->edit.'</a>');
    }
    asort($table->data);

    print_table($table);

    echo "<div style=\"text-align:center\"><input type=\"submit\" value=\"".get_string("savechanges")."\" /></div>\n";
    echo "</div>";
    echo "</form>";

    admin_externalpage_print_footer();

?>
