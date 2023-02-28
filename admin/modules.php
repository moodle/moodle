<?php
      // Allows the admin to manage activity modules

    require_once('../config.php');
    require_once('../course/lib.php');
    require_once($CFG->libdir.'/adminlib.php');
    require_once($CFG->libdir.'/tablelib.php');

    // defines
    define('MODULE_TABLE','module_administration_table');

    admin_externalpage_setup('managemodules');

    $show    = optional_param('show', '', PARAM_PLUGIN);
    $hide    = optional_param('hide', '', PARAM_PLUGIN);


/// Print headings

    $stractivities = get_string("activities");
    $struninstall = get_string('uninstallplugin', 'core_admin');
    $strversion = get_string("version");
    $strhide = get_string("hide");
    $strshow = get_string("show");
    $strsettings = get_string("settings");
    $stractivities = get_string("activities");
    $stractivitymodule = get_string("activitymodule");
    $strshowmodulecourse = get_string('showmodulecourse');

/// If data submitted, then process and store.

    if (!empty($hide) and confirm_sesskey()) {
        $class = \core_plugin_manager::resolve_plugininfo_class('mod');
        if ($class::enable_plugin($hide, false)) {
            // Settings not required - only pages.
            admin_get_root(true, false);
        }
        redirect(new moodle_url('/admin/modules.php'));
    }

    if (!empty($show) && confirm_sesskey()) {
        $class = \core_plugin_manager::resolve_plugininfo_class('mod');
        if ($class::enable_plugin($show, true)) {
            // Settings not required - only pages.
            admin_get_root(true, false);
        }
        redirect(new moodle_url('/admin/modules.php'));
    }

    echo $OUTPUT->header();
    echo $OUTPUT->heading($stractivities);

/// Get and sort the existing modules

    if (!$modules = $DB->get_records('modules', array(), 'name ASC')) {
        print_error('moduledoesnotexist', 'error');
    }

/// Print the table of all modules
    // construct the flexible table ready to display
    $table = new flexible_table(MODULE_TABLE);
    $table->define_columns(array('name', 'instances', 'version', 'hideshow', 'uninstall', 'settings'));
    $table->define_headers(array($stractivitymodule, $stractivities, $strversion, "$strhide/$strshow", $strsettings, $struninstall));
    $table->define_baseurl($CFG->wwwroot.'/'.$CFG->admin.'/modules.php');
    $table->set_attribute('id', 'modules');
    $table->set_attribute('class', 'admintable generaltable');
    $table->setup();

    $pluginmanager = core_plugin_manager::instance();

    foreach ($modules as $module) {
        $plugininfo = $pluginmanager->get_plugin_info('mod_'.$module->name);
        $status = $plugininfo->get_status();

        if ($status === core_plugin_manager::PLUGIN_STATUS_MISSING) {
            $strmodulename = '<span class="notifyproblem">'.$module->name.' ('.get_string('missingfromdisk').')</span>';
            $missing = true;
        } else {
            // took out hspace="\10\", because it does not validate. don't know what to replace with.
            $icon = "<img src=\"" . $OUTPUT->image_url('monologo', $module->name) . "\" class=\"icon\" alt=\"\" />";
            $strmodulename = $icon.' '.get_string('modulename', $module->name);
            $missing = false;
        }

        $uninstall = '';
        if ($uninstallurl = core_plugin_manager::instance()->get_uninstall_url('mod_'.$module->name, 'manage')) {
            $uninstall = html_writer::link($uninstallurl, $struninstall);
        }

        if (file_exists("$CFG->dirroot/mod/$module->name/settings.php") ||
                file_exists("$CFG->dirroot/mod/$module->name/settingstree.php")) {
            $settings = "<a href=\"settings.php?section=modsetting$module->name\">$strsettings</a>";
        } else {
            $settings = "";
        }

        try {
            $count = $DB->count_records_select($module->name, "course<>0");
        } catch (dml_exception $e) {
            $count = -1;
        }
        if ($count>0) {
            $countlink = $OUTPUT->action_link(new moodle_url('/course/search.php', ['modulelist' => $module->name]),
                $count, null, ['title' => $strshowmodulecourse]);
        } else if ($count < 0) {
            $countlink = get_string('error');
        } else {
            $countlink = "$count";
        }

        if ($missing) {
            $visible = '';
            $class   = '';
        } else if ($module->visible) {
            $visible = "<a href=\"modules.php?hide=$module->name&amp;sesskey=".sesskey()."\" title=\"$strhide\">".
                       $OUTPUT->pix_icon('t/hide', $strhide) . '</a>';
            $class   = '';
        } else {
            $visible = "<a href=\"modules.php?show=$module->name&amp;sesskey=".sesskey()."\" title=\"$strshow\">".
                       $OUTPUT->pix_icon('t/show', $strshow) . '</a>';
            $class =   'dimmed_text';
        }
        if ($module->name == "forum") {
            $uninstall = "";
            $visible = "";
            $class = "";
        }
        $version = get_config('mod_'.$module->name, 'version');

        $table->add_data(array(
            $strmodulename,
            $countlink,
            $version,
            $visible,
            $settings,
            $uninstall,
        ), $class);
    }

    $table->print_html();

    echo $OUTPUT->footer();
