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
    $needed['missingtype'] = true; // The system needs the missing question type.
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

    // Process any actions.
    $delete  = optional_param('delete', '', PARAM_SAFEDIR);
    $confirm = optional_param('confirm', '', PARAM_BOOL);
    if (!empty($delete) and confirm_sesskey()) {
        // Check it is OK to delete this question type.
        if ($delete == 'missingtype') {
            print_error('cannotdeletemissingqtype', 'admin', admin_url('qtypes.php'));
        }

        if (!isset($QTYPES[$delete])) {
            print_error('unknownquestiontype', 'question', admin_url('qtypes.php'), $delete);
        }

        $qtypename = $QTYPES[$delete]->local_name();
        if ($counts[$delete]->numquestions + $counts[$delete]->numhidden > 0) {
            print_error('cannotdeleteqtypeinuse', 'admin', admin_url('qtypes.php'), $qtypename);
        }

        if ($needed[$delete] > 0) {
            print_error('cannotdeleteqtypeneeded', 'admin', admin_url('qtypes.php'), $qtypename);
        }

        // If not yet confirmed, display a confirmation message.
        if (!$confirm) {
            $qytpename = $QTYPES[$delete]->local_name();
            admin_externalpage_print_header();
            print_heading(get_string('deleteqtypeareyousure', 'admin', $qytpename));
            notice_yesno(get_string('deleteqtypeareyousuremessage', 'admin', $qytpename),
                    admin_url('qtypes.php?delete=' . $delete . '&amp;confirm=1&amp;sesskey=' . sesskey()),
                    admin_url('qtypes.php'), NULL, NULL, 'post', 'get');
            admin_externalpage_print_footer();
            exit;
        }

        // Do the deletion.
        admin_externalpage_print_header();
        print_heading(get_string('deletingqtype', 'admin', $qtypename));

        // Delete any configuration records.
        if (!unset_all_config_for_plugin('qtype_' . $delete)) {
            notify(get_string('errordeletingconfig', 'admin', 'qtype_' . $delete));
        }

        // Then the tables themselves
        if (!drop_plugin_tables($delete, $QTYPES[$delete]->plugin_dir() . '/db/install.xml', false)) {
            
        }

        // Delete the capabilities that were defined by this module
        capabilities_cleanup('qtype/' . $delete);

        // Remove event handlers and dequeue pending events
        events_uninstall('qtype/' . $delete);

        $a->qtype = $qtypename;
        $a->directory = $QTYPES[$delete]->plugin_dir();
        print_box(get_string('qtypedeletefiles', 'admin', $a), 'generalbox', 'notice');
        print_continue(admin_url('qtypes.php'));
        admin_externalpage_print_footer();
        exit;
    }

/// Print the page heading.
    admin_externalpage_print_header();
    print_heading(get_string('manageqtypes', 'admin'));

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
                     $addcapability) . '#report" title = "' . get_string('showdetails', 'admin') . '">' .
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
