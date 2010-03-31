<?php
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

/// Get some data we will need - question counts and which types are needed.
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

/// Work of the correct sort order.
    $config = get_config('question');
    $sortedqtypes = array();
    foreach ($QTYPES as $qtypename => $qtype) {
        $sortedqtypes[$qtypename] = $qtype->local_name();
    }
    $sortedqtypes = question_sort_qtype_array($sortedqtypes, $config);

/// Process actions ============================================================

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

    // Move up in order.
    if (($up = optional_param('up', '', PARAM_SAFEDIR)) && confirm_sesskey()) {
        if (!isset($QTYPES[$up])) {
            print_error('unknownquestiontype', 'question', admin_url('qtypes.php'), $up);
        }

        $neworder = question_reorder_qtypes($sortedqtypes, $up, -1);
        question_save_qtype_order($neworder, $config);
        redirect(admin_url('qtypes.php'));
    }

    // Move down in order.
    if (($down = optional_param('down', '', PARAM_SAFEDIR)) && confirm_sesskey()) {
        if (!isset($QTYPES[$down])) {
            print_error('unknownquestiontype', 'question', admin_url('qtypes.php'), $down);
        }

        $neworder = question_reorder_qtypes($sortedqtypes, $down, +1);
        question_save_qtype_order($neworder, $config);
        redirect(admin_url('qtypes.php'));
    }

    // Delete.
    if (($delete = optional_param('delete', '', PARAM_SAFEDIR)) && confirm_sesskey()) {
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
            $qtypename = $QTYPES[$delete]->local_name();
            echo $OUTPUT->header();
            echo $OUTPUT->heading(get_string('deleteqtypeareyousure', 'admin', $qtypename));
            echo $OUTPUT->confirm(get_string('deleteqtypeareyousuremessage', 'admin', $qtypename),
                    admin_url('qtypes.php?delete=' . $delete . '&confirm=1'),
                    admin_url('qtypes.php'));
            echo $OUTPUT->footer();
            exit;
        }

        // Do the deletion.
        echo $OUTPUT->header();
        echo $OUTPUT->heading(get_string('deletingqtype', 'admin', $qtypename));

        // Delete any configuration records.
        if (!unset_all_config_for_plugin('qtype_' . $delete)) {
            echo $OUTPUT->notification(get_string('errordeletingconfig', 'admin', 'qtype_' . $delete));
        }
        unset_config($delete . '_disabled', 'question');
        unset_config($delete . '_sortorder', 'question');

        // Then the tables themselves
        drop_plugin_tables($delete, $QTYPES[$delete]->plugin_dir() . '/db/install.xml', false);

        // Remove event handlers and dequeue pending events
        events_uninstall('qtype/' . $delete);

        $a->qtype = $qtypename;
        $a->directory = $QTYPES[$delete]->plugin_dir();
        echo $OUTPUT->box(get_string('qtypedeletefiles', 'admin', $a), 'generalbox', 'notice');
        echo $OUTPUT->continue_button(admin_url('qtypes.php'));
        echo $OUTPUT->footer();
        exit;
    }

    // End of process actions ==================================================

/// Print the page heading.
    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('manageqtypes', 'admin'));

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
    foreach ($sortedqtypes as $qtypename => $localname) {
        $qtype = $QTYPES[$qtypename];
        $row = array();

        // Question icon and name.
        $fakequestion = new stdClass;
        $fakequestion->qtype = $qtypename;
        $icon = print_question_icon($fakequestion, true);
        $row[] = $icon . ' ' . $localname;

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
            $icons = enable_disable_button($qtypename, $createable);
            if (!$createable) {
                $rowclass = 'dimmed_text';
            }
        } else {
            $icons = '<img src="' . $OUTPUT->pix_url('spacer') . '" alt="" class="spacer" />';
        }

        // Move icons.
        $icons .= icon_html('up', $qtypename, 't/up', get_string('up'), '');
        $icons .= icon_html('down', $qtypename, 't/down', get_string('down'), '');
        $row[] = $icons;

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

    echo $OUTPUT->footer();

function admin_url($endbit) {
    global $CFG;
    return $CFG->wwwroot . '/' . $CFG->admin . '/' . $endbit;
}

function enable_disable_button($qtypename, $createable) {
    if ($createable) {
        return icon_html('disable', $qtypename, 'i/hide', get_string('enabled', 'question'), get_string('disable'));
    } else {
        return icon_html('enable', $qtypename, 'i/show', get_string('disabled', 'question'), get_string('enable'));
    }
}

function icon_html($action, $qtypename, $icon, $alt, $tip) {
    global $OUTPUT;
    if ($tip) {
        $tip = 'title="' . $tip . '" ';
    }
    $html = ' <form action="' . admin_url('qtypes.php') . '" method="post"><div>';
    $html .= '<input type="hidden" name="sesskey" value="' . sesskey() . '" />';
    $html .= '<input type="image" name="' . $action . '" value="' . $qtypename .
            '" src="' . $OUTPUT->pix_url($icon) . '" alt="' . $alt . '" ' . $tip . '/>';
    $html .= '</div></form>';
    return $html;
}

