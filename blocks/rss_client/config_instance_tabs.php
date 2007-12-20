<?php  // $Id$
/// This file to be included so we can assume config.php has already been included.
/// We also assume that $inactive, $activetab and $currentaction have been set

global $USER;
$tabs = $row = array();


if (empty($this->instance->pinned)) {
    $context = get_context_instance(CONTEXT_BLOCK, $this->instance->id);
} else {
    $context = get_context_instance(CONTEXT_SYSTEM); // pinned blocks do not have own context
}

if (has_capability('moodle/site:manageblocks', $context)) {
    $script = $page->url_get_full(array('instanceid' => $this->instance->id, 'sesskey' => $USER->sesskey, 'blockaction' => 'config', 'currentaction' => 'configblock', 'id' => $id, 'section' => 'rss'));
    $row[] = new tabobject('configblock', $script,
                get_string('configblock', 'block_rss_client'));
}

$script = $page->url_get_full(array('instanceid' => $this->instance->id, 'sesskey' => $USER->sesskey, 'blockaction' => 'config', 'currentaction' => 'managefeeds', 'id' => $id, 'section' => 'rss'));
$row[] = new tabobject('managefeeds', $script,
            get_string('managefeeds', 'block_rss_client'));

$tabs[] = $row;

/// Print out the tabs and continue!
print "\n".'<div class="tabs">'."\n";
print_tabs($tabs, $currentaction);
print '</div>' . print_location_comment(__FILE__, __LINE__, true);
?>
