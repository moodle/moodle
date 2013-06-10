<?php

define('NO_OUTPUT_BUFFERING', true);

require('../config.php');
require_once($CFG->libdir.'/adminlib.php');    // various admin-only functions
require_once($CFG->libdir.'/upgradelib.php');  // general upgrade/install related functions
require_once($CFG->libdir.'/pluginlib.php');   // available updates notifications
require_once('updatechecker.php');   // available updates notifications

$fetchupdates = optional_param('fetchupdates', 0, PARAM_BOOL);

// Check some PHP server settings

$PAGE->set_url('/calendarsystem/index.php');
$PAGE->set_pagelayout('admin'); // Set a default pagelayout

$version = null;
require("$CFG->dirroot/calendarsystem/version.php");
// Check version of calendarsystem code on disk

$PAGE->set_context(context_system::instance());

// Check for valid admin user - no guest autologin
require_login(0, false);
$context = context_system::instance();
require_capability('moodle/site:config', $context);


// Everything should now be set up, and the user is an admin


// Available updates for Moodle core
$updateschecker = calendarsystem_update_checker::instance();
$availableupdates = array();
$availableupdates['core'] = $updateschecker->get_update_info('core');

// Available updates for calendar system plugins
$calendars = get_plugin_list('calendarsystem');
foreach ($calendars as $calendar => $calendarrootdir) {
    $availableupdates[$calendar] = $updateschecker->get_update_info('calendarsystem_'.$calendar);
}
/*
$pluginman = plugin_manager::instance();
foreach ($pluginman->get_plugins() as $plugintype => $plugintypeinstances) {
    foreach ($plugintypeinstances as $pluginname => $plugininfo) {
        if (!empty($plugininfo->availableupdates)) {
            foreach ($plugininfo->availableupdates as $pluginavailableupdate) {
                if ($pluginavailableupdate->version > $plugininfo->versiondisk) {
                    if (!isset($availableupdates[$plugintype.'_'.$pluginname])) {
                        $availableupdates[$plugintype.'_'.$pluginname] = array();
                    }
                    $availableupdates[$plugintype.'_'.$pluginname][] = $pluginavailableupdate;
                }
            }
        }
    }
}
*/
// The timestamp of the most recent check for available updates
$availableupdatesfetch = $updateschecker->get_last_timefetched();

//admin_externalpage_setup('adminnotifications');

if ($fetchupdates) {
    require_sesskey();
    $updateschecker->fetch();
    redirect($PAGE->url);
}

$strupdatecheck = get_string('updatecheck', 'calendarsystem');
$PAGE->navbar->add($strupdatecheck);

echo $OUTPUT->header();
echo available_updates($availableupdates, $availableupdatesfetch);

echo $OUTPUT->footer();


///////////////////////////////////////////////////////////////////////////////////////
    /**
     * Displays the info about available Moodle core and plugin updates
     *
     * The structure of the $updates param has changed since 2.4. It contains not only updates
     * for the core itself, but also for all other installed plugins.
     *
     * @param array|null $updates array of (string)component => array of calendarsystem_update_info objects or null
     * @param int|null $fetch timestamp of the most recent updates fetch or null (unknown)
     * @return string
     */
    function available_updates($updates, $fetch) {
        global $OUTPUT;

        $updateinfo = $OUTPUT->box_start('generalbox adminwarning calendarsystemupdatesinfo');
        $someupdateavailable = false;
        if (is_array($updates)) {
            if (is_array($updates['core'])) {
                $someupdateavailable = true;
                $updateinfo .= $OUTPUT->heading(get_string('updateavailable', 'calendarsystem'), 3);
                foreach ($updates['core'] as $update) {
                    $updateinfo .= moodle_available_update_info($update);
                }
            }
            unset($updates['core']);
            // If something has left in the $updates array now, it is updates for plugins.
            if (!empty($updates)) {
                foreach ($updates as $pluginname=>$pluginupdates) {
                    if (is_array($pluginupdates)) {
                        $someupdateavailable = true;
                        $updateinfo .= $OUTPUT->heading(get_string('updateavailableforplugin', 'calendarsystem', get_string('name', 'calendarsystem_'.$pluginname)), 3);

                        foreach ($pluginupdates as $update) {
                            $updateinfo .= moodle_available_update_info($update);
                        }
                    }
                }
            }
        }

        if (!$someupdateavailable) {
            $now = time();
            if ($fetch and ($fetch <= $now) and ($now - $fetch < HOURSECS)) {
                $updateinfo .= $OUTPUT->heading(get_string('updateavailablenot', 'calendarsystem'), 3);
            }
        }

        $updateinfo .= $OUTPUT->container_start('checkforupdates');
        $updateinfo .= $OUTPUT->single_button(new moodle_url('', array('fetchupdates' => 1)), get_string('checkforupdates', 'calendarsystem'));
        if ($fetch) {
            $updateinfo .= $OUTPUT->container(get_string('checkforupdateslast', 'core_plugin',
                userdate($fetch, get_string('strftimedatetime', 'core_langconfig'))));
        }
        $updateinfo .= $OUTPUT->container_end();

        $updateinfo .= $OUTPUT->box_end();

        return $updateinfo;
    }
    
    
    /**
     * Helper method to render the information about the available Moodle update
     *
     * @param calendarsystem_update_info $updateinfo information about the available Moodle core update
     */
    function moodle_available_update_info(calendarsystem_update_info $updateinfo) {
        global $OUTPUT;

        $boxclasses = 'moodleupdateinfo';
        $info = array();

        if (isset($updateinfo->version)) {
            $info[] = html_writer::tag('span', get_string('updateavailable_version', 'calendarsystem', $updateinfo->version),
                array('class' => 'info version'));
        }

        if (isset($updateinfo->download)) {
            $info[] = html_writer::link($updateinfo->download, get_string('download'), array('class' => 'info download'));
        }

        if (isset($updateinfo->url)) {
            $info[] = html_writer::link($updateinfo->url, get_string('updateavailable_moreinfo', 'calendarsystem'),
                array('class' => 'info more'));
        }

        $box  = $OUTPUT->box_start($boxclasses);
        $box .= $OUTPUT->box(implode(html_writer::tag('span', ' - ', array('class' => 'separator')), $info), '');
        $box .= $OUTPUT->box_end();

        return $box;
    }
///////////////////////////////////////////////////////////////////////////////////////



