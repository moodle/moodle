<?php
/**
 * Handles tabs for the mnet pages.
 * We assume that $currenttab is defined
 *
 */
if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.'); // It must be included from a Moodle page
}
$strmnetservices   = get_string('mnetservices', 'mnet');
$strmnetlog        = get_string('mnetlog', 'mnet');
$strmnetedithost   = get_string('reviewhostdetails', 'mnet');
$strmnetthemes     = get_string('mnetthemes', 'mnet');

$logurl = $CFG->wwwroot.
          '/course/report/log/index.php?chooselog=1&amp;showusers=1&amp;showcourses=1&amp;host_course='.$mnet_peer->id.
          '%2F1&amp;user='.'0'.
          '&amp;date=0'.
          '&amp;modid=&amp;modaction=0&amp;logformat=showashtml';
$tabs = array();
if (isset($mnet_peer->id) && $mnet_peer->id > 0) {
    $tabs[] = new tabobject('mnetdetails', 'peers.php?step=update&amp;hostid='.$mnet_peer->id, $strmnetedithost, $strmnetedithost, false);
    $tabs[] = new tabobject('mnetservices', 'mnet_services.php?step=list&amp;hostid='.$mnet_peer->id, $strmnetservices, $strmnetservices, false);
    $tabs[] = new tabobject('mnetthemes', 'mnet_themes.php?step=list&amp;hostid='.$mnet_peer->id, $strmnetthemes, $strmnetthemes, false);
    if ($mnet_peer->application->name == 'moodle' && $mnet_peer->id != $CFG->mnet_all_hosts_id) {
        $tabs[] = new tabobject('mnetlog', $logurl, $strmnetlog, $strmnetlog, false);
    }
} else {
    $tabs[] = new tabobject('mnetdetails', '#', $strmnetedithost, $strmnetedithost, false);
}
print_tabs(array($tabs), $currenttab);