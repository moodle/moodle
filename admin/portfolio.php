<?php

require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once($CFG->libdir . '/portfoliolib.php');
require_once($CFG->libdir . '/portfolio/forms.php');
require_once($CFG->libdir . '/adminlib.php');

$portfolio     = optional_param('pf', '', PARAM_FORMAT);
$action        = optional_param('action', '', PARAM_ALPHA);
$sure          = optional_param('sure', '', PARAM_ALPHA);

$display = true; // fall through to normal display

$pagename = 'portfoliocontroller';

if ($action == 'edit') {
    $pagename = 'portfoliosettings' . $portfolio;
} else if ($action == 'delete') {
    $pagename = 'portfoliodelete';
} else if (($action == 'newon') || ($action == 'newoff')) {
    $pagename = 'portfolionew';
}

// Need to remember this for form
$formaction = $action;

// Check what visibility to show the new repository
if ($action == 'newon') {
    $action = 'new';
    $visible = 1;
} else if ($action == 'newoff') {
    $action = 'new';
    $visible = 0;
}

admin_externalpage_setup($pagename);

require_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM));

$baseurl    = "$CFG->wwwroot/$CFG->admin/settings.php?section=manageportfolios";
$sesskeyurl = "$CFG->wwwroot/$CFG->admin/portfolio.php?sesskey=" . sesskey();
$configstr  = get_string('manageportfolios', 'portfolio');

$return = true; // direct back to the main page

if (($action == 'edit') || ($action == 'new')) {
    if (($action == 'edit')) {
        $instance = portfolio_instance($portfolio);
        $plugin = $instance->get('plugin');

        // Since visible is being passed to form
        // and used to set the value when a new
        // instance is created - we must also
        // place the currently visibility into the
        // form as well
        $visible = $instance->get('visible');
    } else {
        $instance = null;
        $plugin = $portfolio;
    }

    $PAGE->set_pagetype('admin-portfolio-' . $plugin);

    // Display the edit form for this instance
    $mform = new portfolio_admin_form('', array('plugin' => $plugin, 'instance' => $instance, 'portfolio' => $portfolio, 'action' => $formaction, 'visible' => $visible));
    // End setup, begin output
    if ($mform->is_cancelled()){
        redirect($baseurl);
        exit;
    } else if (($fromform = $mform->get_data()) && (confirm_sesskey())) {
        // Unset whatever doesn't belong in fromform
        foreach (array('pf', 'action', 'plugin', 'sesskey', 'submitbutton') as $key) {
            unset($fromform->{$key});
        }
        // This branch is where you process validated data.
        if ($action == 'edit') {
            $instance->set_config($fromform);
            $instance->save();
        } else {
            portfolio_static_function($plugin, 'create_instance', $plugin, $fromform->name, $fromform);
        }
        $savedstr = get_string('instancesaved', 'portfolio');
        redirect($baseurl, $savedstr, 1);
        exit;
    } else {
        echo $OUTPUT->header();
        echo $OUTPUT->heading(get_string('configplugin', 'portfolio'));
        echo $OUTPUT->box_start();
        $mform->display();
        echo $OUTPUT->box_end();
        $return = false;
    }
} else if (($action == 'hide') || ($action == 'show')) {
    require_sesskey();

    $instance = portfolio_instance($portfolio);
    $current = $instance->get('visible');
    if (empty($current) && $instance->instance_sanity_check()) {
        print_error('cannotsetvisible', 'portfolio', $baseurl);
    }

    if ($action == 'show') {
        $visible = 1;
    } else {
        $visible = 0;
    }

    $instance->set('visible', $visible);
    $instance->save();
    $return = true;
} else if ($action == 'delete') {
    $instance = portfolio_instance($portfolio);
    if ($sure) {
        if (!confirm_sesskey()) {
            print_error('confirmsesskeybad', '', $baseurl);
        }
        if ($instance->delete()) {
            $deletedstr = get_string('instancedeleted', 'portfolio');
            redirect($baseurl, $deletedstr, 1);
        } else {
            print_error('instancenotdeleted', 'portfolio', $baseurl);
        }
        exit;
    } else {
        echo $OUTPUT->header();
        echo $OUTPUT->confirm(get_string('sure', 'portfolio', $instance->get('name')), $sesskeyurl . '&pf='.$portfolio.'&action=delete&sure=yes', $baseurl);
        $return = false;
    }
}

if (!empty($return)) {
    // normal display. fall through to here (don't call exit) if you want this to run
    redirect($baseurl);
}
echo $OUTPUT->footer();

