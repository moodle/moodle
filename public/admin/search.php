<?php

// searches for admin settings

require_once('../config.php');
require_once($CFG->libdir.'/adminlib.php');

redirect_if_major_upgrade_required();

$query = trim(optional_param('query', '', PARAM_NOTAGS));  // Search string

$context = context_system::instance();
$PAGE->set_context($context);

// If we are performing a search we need to display the secondary navigation with links as opposed to just anchors.
// NOTE: hassecondarynavigation will be overridden in classic.
$PAGE->set_secondary_navigation(true, !$query);

$hassiteconfig = has_capability('moodle/site:config', $context);

if ($hassiteconfig && moodle_needs_upgrading()) {
    redirect(new moodle_url('/admin/index.php'));
}

// If site registration needs updating, redirect.
\core\hub\registration::registration_reminder('/admin/search.php');

admin_externalpage_setup('search', '', array('query' => $query)); // now hidden page
$PAGE->set_heading(get_string('administrationsite')); // Has to be after setup since it has its' own heading set_heading.

$adminroot = admin_get_root(); // need all settings here
$adminroot->search = $query; // So we can reference it in search boxes later in this invocation
$statusmsg = '';
$errormsg  = '';
$focus = '';

// now we'll deal with the case that the admin has submitted the form with changed settings
if ($data = data_submitted() and confirm_sesskey() and isset($data->action) and $data->action == 'save-settings') {
    require_capability('moodle/site:config', $context);
    $count = admin_write_settings($data);
    if (!empty($adminroot->errors)) {
        $errormsg = get_string('errorwithsettings', 'admin');
        $firsterror = reset($adminroot->errors);
        $focus = $firsterror->id;
    } else {
        // No errors. Did we change any setting? If so, then redirect with success.
        if ($count) {
            redirect($PAGE->url, get_string('changessaved'), null, \core\output\notification::NOTIFY_SUCCESS);
        }
        redirect($PAGE->url);
    }
}

$PAGE->set_primary_active_tab('siteadminnode');

// and finally, if we get here, then there are matching settings and we have to print a form
// to modify them
echo $OUTPUT->header($focus);

// Display a warning if site is not registered.
if (empty($query)) {
    $adminrenderer = $PAGE->get_renderer('core', 'admin');
    echo $adminrenderer->warn_if_not_registered();
}

if ($errormsg !== '') {
    echo $OUTPUT->notification($errormsg);

} else if ($statusmsg !== '') {
    echo $OUTPUT->notification($statusmsg, 'notifysuccess');
}

$showsettingslinks = true;

if ($query && $hassiteconfig) {
    echo '<hr>';
    echo admin_search_settings_html($query);
    $showsettingslinks = false;
}

if ($showsettingslinks) {
    $node = $PAGE->settingsnav->find('root', navigation_node::TYPE_SITE_ADMIN);
    if ($node) {
        $secondarynavigation = false;
        if ($PAGE->has_secondary_navigation()) {
            $moremenu = new \core\navigation\output\more_menu($PAGE->secondarynav, 'nav-tabs', true, true);
            $secondarynavigation = $moremenu->export_for_template($OUTPUT);
        }
        echo $OUTPUT->render_from_template('core/settings_link_page',
            ['node' => $node, 'secondarynavigation' => $secondarynavigation]);
    }
}

echo $OUTPUT->footer();
