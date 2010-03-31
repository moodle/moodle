<?php

require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once($CFG->libdir . '/portfoliolib.php');
require_once($CFG->libdir . '/portfolio/forms.php');
require_once($CFG->libdir . '/adminlib.php');

$edit    = optional_param('edit', 0, PARAM_INT);
$new     = optional_param('new', '', PARAM_FORMAT);
$hide    = optional_param('hide', 0, PARAM_INT);
$delete  = optional_param('delete', 0, PARAM_INT);
$sure    = optional_param('sure', '', PARAM_ALPHA);

$display = true; // fall through to normal display

$pagename = 'portfoliocontroller';

if ($edit) {
    $pagename = 'portfoliosettings' . $edit;
} else if ($delete) {
    $pagename = 'portfoliodelete';
} else if ($new) {
    $pagename = 'portfolionew';
}
admin_externalpage_setup($pagename);

$baseurl    = "$CFG->wwwroot/$CFG->admin/settings.php?section=manageportfolios";
$sesskeyurl = "$CFG->wwwroot/$CFG->admin/portfolio.php?sesskey=" . sesskey();
$configstr  = get_string('manageportfolios', 'portfolio');

$return = true; // direct back to the main page

if (!empty($edit) || !empty($new)) {
    if (!empty($edit)) {
        $instance = portfolio_instance($edit);
        $plugin = $instance->get('plugin');
    } else {
        $plugin = $new;
        $instance = null;
    }

    $PAGE->set_pagetype('admin-portfolio-' . $plugin);

    // display the edit form for this instance
    $mform = new portfolio_admin_form('', array('plugin' => $plugin, 'instance' => $instance));
    // end setup, begin output
    if ($mform->is_cancelled()){
        redirect($baseurl);
        exit;
    } else if ($fromform = $mform->get_data()){
        // unset whatever doesn't belong in fromform
        foreach (array('edit', 'new', 'plugin', 'sesskey', 'submitbutton') as $key) {
            unset($fromform->{$key});
        }
        //this branch is where you process validated data.
        if ($edit) {
            $instance->set_config($fromform);
            $instance->save();
        } else {
            portfolio_static_function($plugin, 'create_instance', $plugin, $fromform->name, $fromform);
        }
        $savedstr = get_string('instancesaved', 'portfolio');
        echo $OUTPUT->header();
        echo $OUTPUT->heading($savedstr);
        redirect($baseurl, $savedstr, 3);
        exit;
    } else {
        echo $OUTPUT->header();
        echo $OUTPUT->heading(get_string('configplugin', 'portfolio'));
        echo $OUTPUT->box_start();
        $mform->display();
        echo $OUTPUT->box_end();
        $return = false;
    }
} else if (!empty($hide)) {
    if (!confirm_sesskey()) {
        print_error('confirmsesskeybad', '', $baseurl);
    }
    $instance = portfolio_instance($hide);
    $current = $instance->get('visible');
    if (empty($current) && $instance->instance_sanity_check()) {
        print_error('cannotsetvisible', 'portfolio', $baseurl);
    }
    $instance->set('visible', !$instance->get('visible'));
    $instance->save();
    $return = true;
} else if (!empty($delete)) {
    echo $OUTPUT->header();
    $instance = portfolio_instance($delete);
    if ($sure) {
        if (!confirm_sesskey()) {
            print_error('confirmsesskeybad', '', $baseurl);
        }
        if ($instance->delete()) {
            $deletedstr = get_string('instancedeleted', 'portfolio');
            echo $OUTPUT->heading($deletedstr);
            redirect($baseurl, $deletedstr, 3);
        } else {
            print_error('instancenotdeleted', 'portfolio', $baseurl);
        }
        exit;
    }
    echo $OUTPUT->confirm(get_string('sure', 'portfolio', $instance->get('name')), $sesskeyurl . '&delete=' . $delete . '&sure=yes', $baseurl);
    $return = false;
}


if (!empty($return)) {
    // normal display. fall through to here (don't call exit) if you want this to run
    redirect($baseurl);
}
echo $OUTPUT->footer();

