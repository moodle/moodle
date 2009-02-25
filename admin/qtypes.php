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
    $canviewreports = has_capability('report/questioninstances:view', $systemcontext);

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

    // Process actions =========================================================

    // Disable.
    if (($disable = optional_param('disable', '', PARAM_SAFEDIR)) && confirm_sesskey()) {
        if (!isset($QTYPES[$disable])) {
            print_error('unknownquestiontype', 'question', admin_url('qtypes.php'), $disable);
        }

        set_config($disable . '_disabled', 1, 'question');
        redirect(admin_url('qtypes.php'));
    }

    // Enable.
    if (($enable = optional_param('enable', '', PARAM_SAFEDIR)) && confirm_sesskey()) {
        if (!isset($QTYPES[$enable])) {
            print_error('unknownquestiontype', 'question', admin_url('qtypes.php'), $enable);
        }

        if (!$QTYPES[$enable]->menu_name()) {
            print_error('cannotenable', 'question', admin_url('qtypes.php'), $enable);
        }

        unset_config($enable . '_disabled', 'question');
        redirect(admin_url('qtypes.php'));
    }

    // Delete.
    if ($delete = optional_param('delete', '', PARAM_SAFEDIR) && confirm_sesskey()) {
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
        if (!optional_param('confirm', '', PARAM_BOOL)) {
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
        drop_plugin_tables($delete, $QTYPES[$delete]->plugin_dir() . '/db/install.xml', false);

        // Remove event handlers and dequeue pending events
        events_uninstall('qtype/' . $delete);

        $a->qtype = $qtypename;
        $a->directory = $QTYPES[$delete]->plugin_dir();
        print_box(get_string('qtypedeletefiles', 'admin', $a), 'generalbox', 'notice');
        print_continue(admin_url('qtypes.php'));
        admin_externalpage_print_footer();
        exit;
    }

    // End of process actions ==================================================

/// Print the page heading.
    admin_externalpage_print_header();
    print_heading(get_string('manageqtypes', 'admin'));

/// Set up the table.
    $table = new flexible_table('qtypeadmintable');
    $table->define_columns(array('questiontype', 'numquestions', 'version', 'requires',
            'availableto', 'delete', 'settings'));
    $table->define_headers(array(get_string('questiontype', 'admin'), get_string('numquestions', 'admin'),
            get_string('version'), get_string('requires', 'admin'), get_string('availableq', 'question'),
            get_string('delete'), get_string('settings')));
    $table->set_attribute('id', 'qtypes');
    $table->set_attribute('class', 'generaltable generalbox boxaligncenter boxwidthwide');
    $table->setup();

/// Add a row for each question type.
    $createabletypes = question_type_menu();
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
                        '" title="' . get_string('showdetails', 'admin') . '">' . $strcount . '</a>';
            } else {
                $strcount;
            }
        } else {
            $row[] = 0;
        }

        // Question version number.
        $version = get_config('qtype_' . $qtypename, 'version');
        if ($version) {
            $row[] = $version;
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

        // Are people allowed to create new questions of this type?
        $rowclass = '';
        if ($qtype->menu_name()) {
            $createable = isset($createabletypes[$qtypename]);
            $row[] = enable_disable_button($qtypename, $createable);
            if (!$createable) {
                $rowclass = 'dimmed_text';
            }
        } else {
            $row[] = '';
        }

        // Delete link, if available.
        if ($needed[$qtypename]) {
            $row[] = '';
        } else {
            $row[] = '<a href="' . admin_url('qtypes.php?delete=' . $qtypename .
                    '&amp;sesskey=' . sesskey()) . '" title="' .
                    get_string('uninstallqtype', 'admin') . '">' . get_string('delete') . '</a>';
        }

        // Settings link, if available.
        if (file_exists($qtype->plugin_dir() . '/settings.php')) {
            $row[] = '<a href="' . admin_url('settings.php?section=qtypesetting' . $qtypename) .
                    '">' . get_string('settings') . '</a>';
        } else {
            $row[] = '';
        }

        $table->add_data($row, $rowclass);
    }

    $table->finish_output();

    admin_externalpage_print_footer();

function admin_url($endbit) {
    global $CFG;
    return $CFG->wwwroot . '/' . $CFG->admin . '/' . $endbit;
}

function enable_disable_button($qtypename, $createable) {
    global $CFG;
    if ($createable) {
        $action = 'disable';
        $tip = get_string('disable');
        $alt = get_string('enabled', 'question');
        $icon = 'hide';
    } else {
        $action = 'enable';
        $tip = get_string('enable');
        $alt = get_string('disabled', 'question');
        $icon = 'show';
    }
    $html = '<form action="' . admin_url('qtypes.php') . '" method="post"><div>';
    $html .= '<input type="hidden" name="sesskey" value="' . sesskey() . '" />';
    $html .= '<input type="image" name="' . $action . '" value="' . $qtypename .
            '" src="' . $CFG->pixpath . '/i/' . $icon . '.gif" alt="' . $alt . '" title="' . $tip . '" />';
    $html .= '</div></form>';
    return $html;
}
?>
