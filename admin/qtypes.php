<?PHP // $Id$
// Allows the admin to manage question types.

    require_once(dirname(__FILE__) . '/../config.php');
    require_once($CFG->libdir . '/questionlib.php');
    require_once($CFG->libdir . '/adminlib.php');
    require_once($CFG->libdir . '/tablelib.php');

/// Check permissions.
    require_login();
    $systemcontext = get_context_instance(CONTEXT_SYSTEM);
    require_capability('moodle/question:config', $systemcontext);
    $canviewreports = has_capability('moodle/site:viewreports', $systemcontext);

    admin_externalpage_setup('manageqtypes');

/// If data submitted, then process and store.
    $delete  = optional_param('delete', '', PARAM_SAFEDIR);
    $confirm = optional_param('confirm', '', PARAM_BOOL);

    if (!empty($delete) and confirm_sesskey()) {
        // TODO
//        admin_externalpage_print_header();
//        print_heading($stractivities);
//
//        $strmodulename = get_string("modulename", "$delete");
//
//        if (!$confirm) {
//            notice_yesno(get_string("moduledeleteconfirm", "", $strmodulename),
//                         "modules.php?delete=$delete&amp;confirm=1&amp;sesskey=$USER->sesskey",
//                         "modules.php");
//            admin_externalpage_print_footer();
//            exit;
//
//        } else {  // Delete everything!!
//
//            if ($delete == "forum") {
//                print_error("cannotdeleteforummudule", 'forum');
//            }
//
//            if (!$module = $DB->get_record("modules", array("name"=>$delete))) {
//                print_error('moduledoesnotexist', 'error');
//            }
//
//            // OK, first delete all the relevant instances from all course sections
//            if ($coursemods = $DB->get_records("course_modules", array("module"=>$module->id))) {
//                foreach ($coursemods as $coursemod) {
//                    if (! delete_mod_from_section($coursemod->id, $coursemod->section)) {
//                        notify("Could not delete the $strmodulename with id = $coursemod->id from section $coursemod->section");
//                    }
//                }
//            }
//
//            // delete calendar events
//            if (!$DB->delete_records("event", array("modulename"=>$delete))) {
//                notify("Error occurred while deleting all $strmodulename records in calendar event table");
//            }
//
//            // clear course.modinfo for courses
//            // that used this module...
//            $sql = "UPDATE {course}
//                       SET modinfo=''
//                     WHERE id IN (SELECT DISTINCT course
//                                    FROM {course_modules}
//                                   WHERE module=?)";
//            $DB->execute($sql, array($module->id));
//
//            // Now delete all the course module records
//            if (!$DB->delete_records("course_modules", array("module"=>$module->id))) {
//                notify("Error occurred while deleting all $strmodulename records in course_modules table");
//            }
//
//            // Then delete all the logs
//            if (!$DB->delete_records("log", array("module"=>$module->name))) {
//                notify("Error occurred while deleting all $strmodulename records in log table");
//            }
//
//            // And log_display information
//            if (!$DB->delete_records("log_display", array("module"=>$module->name))) {
//                notify("Error occurred while deleting all $strmodulename records in log_display table");
//            }
//
//            // And the module entry itself
//            if (!$DB->delete_records("modules", array("name"=>$module->name))) {
//                notify("Error occurred while deleting the $strmodulename record from modules table");
//            }
//
//            // And the module configuration records
//            if (!$DB->execute("DELETE FROM {config} WHERE name LIKE ?", array("{$module->name}_%"))) {
//                notify("Error occurred while deleting the $strmodulename records from the config table");
//            }
//
//            // cleanup the gradebook
//            require_once($CFG->libdir.'/gradelib.php');
//            grade_uninstalled_module($module->name);
//
//            // Then the tables themselves
//            drop_plugin_tables($module->name, "$CFG->dirroot/mod/$module->name/db/install.xml", false);
//
//            // Delete the capabilities that were defined by this module
//            capabilities_cleanup('mod/'.$module->name);
//
//            // remove entent handlers and dequeue pending events
//            events_uninstall('mod/'.$module->name);
//
//            // Perform any custom uninstall tasks
//            if (file_exists($CFG->dirroot . '/mod/' . $module->name . '/lib.php')) {
//                require_once($CFG->dirroot . '/mod/' . $module->name . '/lib.php');
//                $uninstallfunction = $module->name . '_uninstall';
//                if (function_exists($uninstallfunction)) {
//                    if (! $uninstallfunction() ) {
//                        notify('Encountered a problem running uninstall function for '. $module->name.'!');
//                    }
//                }
//            }
//
//            $a->module = $strmodulename;
//            $a->directory = "$CFG->dirroot/mod/$delete";
//            notice(get_string("moduledeletefiles", "", $a), "modules.php");
//        }
    }

/// Get some data we will need.
    $counts = $DB->get_records_sql("
            SELECT qtype, COUNT(1) as numquestions, SUM(hidden) as numhidden
            FROM {question} GROUP BY qtype", array());
    $needed = array();
    foreach ($QTYPES as $qtypename => $qtype) {
        if (!isset($counts[$qtypename])) {
            $counts[$qtypename] = new stdClass;
            $counts[$qtypename]->numquestions = 0;
            $counts[$qtypename]->numhidden = 0;
        }
        $needed[$qtypename] = $counts[$qtypename]->numquestions > 0;
        $counts[$qtypename]->numquestions -= $counts[$qtypename]->numhidden;
    }
    foreach ($QTYPES as $qtypename => $qtype) {
        foreach ($qtype->requires_qtypes() as $reqtype) {
            $needed[$reqtype] = true;
        }
    }
    foreach ($counts as $qtypename => $count) {
        if (!isset($QTYPES[$qtypename])) {
            $counts['missingtype']->numquestions += $count->numquestions - $count->numhidden;
            $counts['missingtype']->numhidden += $count->numhidden;
        }
    }

/// Print the page heading.
    admin_externalpage_print_header();
    print_heading(get_string('manageqtypes', 'admin'));

/// Print the table of all modules.

/// Set up the table.
    $table = new flexible_table('qtypeadmintable');
    $table->define_columns(array('questiontype', 'numquestions', 'version', 'requires',
            'availableto', 'delete', 'settings'));
    $table->define_headers(array(get_string('questiontype', 'admin'), get_string('numquestions', 'admin'),
            get_string('version'), get_string('requires', 'admin'), get_string('availableto', 'admin'),
            get_string('delete'), get_string('settings')));
    $table->set_attribute('id', 'qtypes');
    $table->set_attribute('class', 'generaltable generalbox boxaligncenter boxwidthwide');
    $table->setup();

/// Add a row for each question type.
    foreach ($QTYPES as $qtypename => $qtype) {
        $row = array();

        // Question icon and name.
        $fakequestion = new stdClass;
        $fakequestion->qtype = $qtypename;
        $icon = print_question_icon($fakequestion, true);
        $row[] = $icon . ' ' . $qtype->local_name();

        // Number of questions of this type.
        if ($counts[$qtypename]->numquestions + $counts[$qtypename]->numhidden > 0) {
            if ($counts[$qtypename]->numhidden > 0) {
                $strcount = get_string('numquestionsandhidden', 'admin', $counts[$qtypename]);
            } else {
                $strcount = $counts[$qtypename]->numquestions;
            }
            if ($canviewreports) {
                $row[] = '<a href="' . admin_url('/report/questioninstances/index.php?qtype=' . $qtypename) .
                        '">' . $strcount . '</a>';
            } else {
                $strcount;
            }
        } else {
            $row[] = 0;
        }

        // Question version number.
        $versionfield = 'qtype_' . $qtypename . '_version';
        if (isset($CFG->$versionfield)) {
            $row[] = $CFG->$versionfield;
        } else {
            $row[] = '<span class="disabled">' . get_string('nodatabase', 'admin') . '</span>';
        }

        // Other question types required by this one.
        $requiredtypes = $qtype->requires_qtypes();
        $strtypes = array();
        if (!empty($requiredtypes)) {
            foreach ($requiredtypes as $required) {
                $strtypes[] = $QTYPES[$required]->local_name();
            }
            $row[] = implode(', ', $strtypes);
        } else {
            $row[] = '';
        }

        // Who is allowed to create this question type.
// TODO        $addcapability = 'qtype/' . $qtypename . ':add';
        $addcapability = 'moodle/question:add';
        $adderroles = get_roles_with_capability($addcapability, CAP_ALLOW, $systemcontext);
        $hasoverrides = $DB->record_exists_select('role_capabilities',
                'capability = ? AND contextid <> ?', array($addcapability, $systemcontext->id));
        $rolelinks = array();
        foreach ($adderroles as $role) {
            $rolelinks[] = '<a href="' . admin_url('roles/manage.php?action=view&amp;roleid=' . $role->id) .
                    '" title="' . get_string('editrole', 'role') . '">' . $role->name . '</a>';
        }
        $rolelinks = implode(', ', $rolelinks);
        if (!$rolelinks) {
            $rolelinks = get_string('noroles', 'admin');
        }
        if ($hasoverrides) {
            $a = new stdClass;
            $a->roles = $rolelinks;
            $a->exceptions = '<a href="' . admin_url('report/capability/index.php?capability=' .
                     $addcapability) . '" title = "' . get_string('showdetails', 'admin') . '">' .
                     get_string('exceptions', 'admin') . '</a>';
            $rolelinks = get_string('roleswithexceptions', 'admin', $a) ;
        }
        if (empty($adderroles) && !$hasoverrides) {
            $rolelinks = '<span class="disabled">' . $rolelinks . '</span>';
        }
        $row[] = $rolelinks;

        // Delete link, if available.
        if ($needed[$qtypename]) {
            $row[] = '';
        } else {
            $row[] = '<a href="' . admin_url('qtypes.php?delete=' . $qtypename .
                    '&amp;sesskey=' . sesskey()) . '">' . get_string('delete') . '</a>';
        }

        // Settings link, if available.
        if (file_exists($qtype->plugin_dir() . '/settings.php')) {
            $row[] = '<a href="' . admin_url('settings.php?section=qtypesetting' . $qtypename) .
                    '">' . get_string('settings') . '</a>';
        } else {
            $row[] = '';
        }

        $table->add_data($row);
    }

    $table->print_html();

    admin_externalpage_print_footer();

function admin_url($endbit) {
    global $CFG;
    return $CFG->wwwroot . '/' . $CFG->admin . '/' . $endbit;
}
?>
