<?php

require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once($CFG->libdir . '/portfoliolib.php');
require_once($CFG->libdir . '/adminlib.php');

admin_externalpage_setup('portfoliosettingsall');

$edit    = optional_param('edit', 0, PARAM_INT);
$new     = optional_param('new', '', PARAM_FORMAT);
$hide    = optional_param('hide', 0, PARAM_INT);
$delete  = optional_param('delete', 0, PARAM_INT);
$sure    = optional_param('sure', '', PARAM_ALPHA);

$display = true; // fall through to normal display

require_login(SITEID, false);
require_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM));

$baseurl = $CFG->wwwroot . '/' . $CFG->admin . '/portfolio.php';
$sesskeyurl = $CFG->wwwroot . '/' . $CFG->admin . '/portfolio.php?sesskey=' . sesskey();
$configstr = get_string('manageportfolios', 'portfolio');

if (!empty($edit) || !empty($new)) {
    if (!empty($edit)) {
        $instance = portfolio_instance($edit);
        $plugin = $instance->get('plugin');
    } else {
        $plugin = $new;
        $instance = null;
    }
    // display the edit form for this instance
    $mform = new portfolio_admin_form('', array('plugin' => $plugin, 'instance' => $instance));
    // end setup, begin output
    if ($mform->is_cancelled()){
        redirect($baseurl);
        exit;
    } else if ($fromform = $mform->get_data()){
        if (!confirm_sesskey()) {
            print_error('confirmsesskeybad', '', $baseurl);
        }
        //this branch is where you process validated data.
        if ($edit) {
            $success = $instance->set_config($fromform);
            $success = $success && $instance->save();
        }
        else {
            $success = portfolio_static_function($plugin, 'create_instance', $plugin, $fromform->name, $fromform);
        }
        if ($success) {
            $savedstr = get_string('instancesaved', 'portfolio');
            print_heading($savedstr);
            redirect($baseurl, $savedstr, 3);
        } else {
            print_error('instancenotsaved', 'portfolio', $baseurl);
        }
        exit;
    } else {
        admin_externalpage_print_header();
        print_heading(get_string('configplugin', 'portfolio'));
        print_simple_box_start();
        $mform->display();
        print_simple_box_end();
        $display = false;
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
} else if (!empty($delete)) {
    admin_externalpage_print_header();
    $instance = portfolio_instance($delete);
    if ($sure) {
        if (!confirm_sesskey()) {
            print_error('confirmsesskeybad', '', $baseurl);
        }
        if ($instance->delete()) {
            $deletedstr = get_string('instancedeleted', 'portfolio');
            print_heading($deletedstr);
            redirect($baseurl, $deletedstr, 3);
        } else {
            print_error('instancenotdeleted', 'portfolio', $baseurl);
        }
        exit;
    }
    notice_yesno(get_string('sure', 'portfolio', $instance->get('name')), $sesskeyurl . '&delete=' . $delete . '&sure=yes', $baseurl);
    $display = false;
}

// normal display. fall through to here (don't call exit) if you want this to run
if ($display) {
    admin_externalpage_print_header();
    print_heading($configstr);
    print_simple_box_start();

    $namestr = get_string('name');
    $pluginstr = get_string('plugin', 'portfolio');

    $plugins = get_list_of_plugins('portfolio/type');
    $instances = portfolio_instances(false, false);
    $alreadyplugins = array();

    $insane = portfolio_plugin_sanity_check($plugins);
    $insaneinstances = portfolio_instance_sanity_check($instances);

    portfolio_report_insane($insane);
    portfolio_report_insane($insaneinstances, $instances);

    $table = new StdClass;
    $table->head = array($namestr, $pluginstr, '');
    $table->data = array();

    foreach ($instances as $i) {
        $row = '<a href="' . $sesskeyurl . '&edit=' . $i->get('id') . '"><img src="' . $CFG->pixpath . '/t/edit.gif" alt="' . get_string('edit') . '" /></a>
             <a href="' . $sesskeyurl . '&delete=' .  $i->get('id') . '"><img src="' . $CFG->pixpath . '/t/delete.gif" alt="' . get_string('delete') . '" /></a>';
        if (array_key_exists($i->get('plugin'), $insane) || array_key_exists($i->get('id'), $insaneinstances)) {
            $row .=  '<img src="' . $CFG->pixpath . '/t/show.gif" alt="' . get_string('hidden', 'portfolio') . '" /><br />';
        } else {
            $row .= ' <a href="' . $sesskeyurl . '&hide=' . $i->get('id') . '"><img src="' . $CFG->pixpath . '/t/' . ($i->get('visible') ? 'hide' : 'show') . '.gif" alt="' . get_string($i->get('visible') ? 'hide' : 'show') . '" /></a><br />';
        }
        $table->data[] = array($i->get('name'), $i->get('plugin'), $row);
        if (!in_array($i->get('plugin'), $alreadyplugins)) {
            $alreadyplugins[] = $i->get('plugin');
        }
    }

    print_table($table);

    $instancehtml = '<br /><form action="' . $baseurl . '" method="post">' . get_string('addnewportfolio', 'portfolio') . ': <select name="new">';
    $addable = 0;
    foreach ($plugins as $p) {
        if (!portfolio_static_function($p, 'allows_multiple') && in_array($p, $alreadyplugins)) {
            continue;
        }
        if (array_key_exists($p, $insane)) {
            continue;
        }
        $instancehtml .= '<option value="' . $p . '">' . $p .'</option>' ."\n";
        $addable++;
    }

    if ($addable) {
        $instancehtml .= '</select><input type="submit" value="' . get_string('add') . '" /></form>';
        echo $instancehtml;
    }
    print_simple_box_end();
}
print_footer();
?>
