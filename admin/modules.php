<?PHP // $Id$
      // Allows the admin to manage activity modules

    require_once('../config.php');
    require_once('../course/lib.php');
    require_once($CFG->libdir.'/adminlib.php');
    require_once($CFG->libdir.'/tablelib.php');

    // defines
    define('MODULE_TABLE','module_administration_table');

    $adminroot = admin_get_root();
    admin_externalpage_setup('managemodules', $adminroot);

    $show    = optional_param('show', '', PARAM_SAFEDIR);
    $hide    = optional_param('hide', '', PARAM_SAFEDIR);
    $delete  = optional_param('delete', '', PARAM_SAFEDIR);
    $confirm = optional_param('confirm', '', PARAM_BOOL);


/// Print headings

    $stractivities = get_string("activities");
    $strdelete = get_string("delete");
    $strversion = get_string("version");
    $strhide = get_string("hide");
    $strshow = get_string("show");
    $strsettings = get_string("settings");
    $stractivities = get_string("activities");
    $stractivitymodule = get_string("activitymodule");
    $strshowmodulecourse = get_string('showmodulecourse');

    admin_externalpage_print_header($adminroot);

    print_heading($stractivities);

/// If data submitted, then process and store.

    if (!empty($hide) and confirm_sesskey()) {
        if (!$module = get_record("modules", "name", $hide)) {
            error("Module doesn't exist!");
        }
        set_field("modules", "visible", "0", "id", $module->id); // Hide main module
        // Remember the visibility status in visibleold
        // and hide...
        $sql = "UPDATE {$CFG->prefix}course_modules
                SET visibleold=visible,
                    visible=0
                WHERE  module={$module->id}";
        execute_sql($sql, false);
        // clear the course modinfo cache for courses
        // where we just deleted something
        $sql = "UPDATE {$CFG->prefix}course
                SET modinfo=''
                WHERE id IN (SELECT DISTINCT course 
                             FROM {$CFG->prefix}course_modules 
                             WHERE visibleold=1 AND module={$module->id})";
        execute_sql($sql, false);
    }

    if (!empty($show) and confirm_sesskey()) {
        if (!$module = get_record("modules", "name", $show)) {
            error("Module doesn't exist!");
        }
        set_field("modules", "visible", "1", "id", $module->id); // Show main module
        set_field('course_modules', 'visible', '1', 'visibleold', 
                  '1', 'module', $module->id); // Get the previous saved visible state for the course module.
        // clear the course modinfo cache for courses
        // where we just made something visible
        $sql = "UPDATE {$CFG->prefix}course
                SET modinfo=''
                WHERE id IN (SELECT DISTINCT course 
                             FROM {$CFG->prefix}course_modules 
                             WHERE visible=1 AND module={$module->id})";
        execute_sql($sql, false);
    }

    if (!empty($delete) and confirm_sesskey()) {

        $strmodulename = get_string("modulename", "$delete");

        if (!$confirm) {
            notice_yesno(get_string("moduledeleteconfirm", "", $strmodulename),
                         "modules.php?delete=$delete&amp;confirm=1&amp;sesskey=$USER->sesskey",
                         "modules.php");
            admin_externalpage_print_footer($adminroot);
            exit;

        } else {  // Delete everything!!

            if ($delete == "forum") {
                error("You can not delete the forum module!!");
            }

            if (!$module = get_record("modules", "name", $delete)) {
                error("Module doesn't exist!");
            }

            // OK, first delete all the relevant instances from all course sections
            if ($coursemods = get_records("course_modules", "module", $module->id)) {
                foreach ($coursemods as $coursemod) {
                    if (! delete_mod_from_section($coursemod->id, $coursemod->section)) {
                        notify("Could not delete the $strmodulename with id = $coursemod->id from section $coursemod->section");
                    }
                }
            }

            // clear course.modinfo for courses
            // that used this module...
            $sql = "UPDATE {$CFG->prefix}course
                    SET modinfo=''
                    WHERE id IN (SELECT DISTINCT course 
                                 FROM {$CFG->prefix}course_modules 
                                 WHERE module={$module->id})";
            execute_sql($sql, false);

            // Now delete all the course module records
            if (!delete_records("course_modules", "module", $module->id)) {
                notify("Error occurred while deleting all $strmodulename records in course_modules table");
            }

            // Then delete all the logs
            if (!delete_records("log", "module", $module->name)) {
                notify("Error occurred while deleting all $strmodulename records in log table");
            }

            // And log_display information
            if (!delete_records("log_display", "module", $module->name)) {
                notify("Error occurred while deleting all $strmodulename records in log_display table");
            }

            // And the module entry itself
            if (!delete_records("modules", "name", $module->name)) {
                notify("Error occurred while deleting the $strmodulename record from modules table");
            }

            // Then the tables themselves

            if ($tables = $db->Metatables()) {
                $prefix = $CFG->prefix.$module->name;
                foreach ($tables as $table) {
                    if (strpos($table, $prefix) === 0) {
                        if (!execute_sql("DROP TABLE $table", false)) {
                            notify("ERROR: while trying to drop table $table");
                        }
                    }
                }
            }
            // Delete the capabilities that were defined by this module
            capabilities_cleanup('mod/'.$module->name);

            $a->module = $strmodulename;
            $a->directory = "$CFG->dirroot/mod/$delete";
            notice(get_string("moduledeletefiles", "", $a), "modules.php");
        }
    }

/// Get and sort the existing modules

    if (!$modules = get_records("modules")) {
        error("No modules found!!");        // Should never happen
    }

    foreach ($modules as $module) {
        $strmodulename = get_string("modulename", "$module->name");
        // Deal with modules which are lacking the language string
        if ($strmodulename == '[[modulename]]') {
            $strmodulename = $module->name;
        }
        $modulebyname[$strmodulename] = $module;
    }
    ksort($modulebyname);

/// Print the table of all modules
    // construct the flexible table ready to display
    $table = new flexible_table(MODULE_TABLE);
    $table->define_columns(array('name', 'instances', 'version', 'hideshow', 'delete', 'settings'));
    $table->define_headers(array($stractivitymodule, $stractivities, $strversion, "$strhide/$strshow", $strdelete, $strsettings));
    $table->define_baseurl($CFG->wwwroot.'/'.$CFG->admin.'/modules.php');
    $table->set_attribute('id', 'modules');
    $table->set_attribute('class', 'generaltable generalbox boxaligncenter boxwidthwide');
    $table->setup();

    foreach ($modulebyname as $modulename => $module) {

        // took out hspace="\10\", because it does not validate. don't know what to replace with.
        $icon = "<img src=\"$CFG->modpixpath/$module->name/icon.gif\" class=\"icon\" alt=\"\" />";

        $delete = "<a href=\"modules.php?delete=$module->name&amp;sesskey=$USER->sesskey\">$strdelete</a>";

        if (file_exists("$CFG->dirroot/mod/$module->name/config.html")) {
            $settings = "<a href=\"module.php?module=$module->name\">$strsettings</a>";
        } else {
            $settings = "";
        }

        $count = count_records_select("$module->name",'course<>0');
        if ($count>0) {
            $countlink = "<a href=\"{$CFG->wwwroot}/course/search.php?modulelist=$module->name" .
                "&amp;sesskey={$USER->sesskey}\" title=\"$strshowmodulecourse\">$count</a>";
        }
        else {
            $countlink = "$count";
        }

        if ($module->visible) {
            $visible = "<a href=\"modules.php?hide=$module->name&amp;sesskey=$USER->sesskey\" title=\"$strhide\">".
                       "<img src=\"$CFG->pixpath/i/hide.gif\" class=\"icon\" alt=\"$strhide\" /></a>";
            $class = "";
        } else {
            $visible = "<a href=\"modules.php?show=$module->name&amp;sesskey=$USER->sesskey\" title=\"$strshow\">".
                       "<img src=\"$CFG->pixpath/i/show.gif\" class=\"icon\" alt=\"$strshow\" /></a>";
            $class = " class=\"dimmed_text\"";
        }
        if ($module->name == "forum") {
            $delete = "";
            $visible = "";
            $class = "";
        }

        $table->add_data(array(
            '<span'.$class.'>'.$icon.' '.$modulename.'</span>', 
            $countlink, 
            '<span'.$class.'>'.$module->version.'</span>', 
            $visible, 
            $delete, 
            $settings
        ));
    }

    $table->print_html();

    admin_externalpage_print_footer($adminroot);

?>
