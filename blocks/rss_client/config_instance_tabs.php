<?php  // $Id$
/// This file to be included so we can assume config.php has already been included.
/// We also assume that $inactive, $activetab and $currentaction have been set

global $USER;
$tabs = $row = array();

$script = $page->url_get_full(array('instanceid' => $this->instance->id, 'sesskey' => $USER->sesskey, 'blockaction' => 'config', 'currentaction' => 'configblock', 'id' => $id));
$row[] = new tabobject('configblock', $script, 
            get_string('configblock', 'block_rss_client'));

$script = $page->url_get_full(array('instanceid' => $this->instance->id, 'sesskey' => $USER->sesskey, 'blockaction' => 'config', 'currentaction' => 'managefeeds', 'id' => $id));
$row[] = new tabobject('managefeeds', $script, 
            get_string('managefeeds', 'block_rss_client'));

$tabs[] = $row;

/// Print out the tabs and continue!
print '<div align="center">';
print_tabs($tabs, $currentaction);
print '</div>';
?>