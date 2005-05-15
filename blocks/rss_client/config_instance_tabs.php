<?php  // $Id$
/// This file to be included so we can assume config.php has already been included.
/// We also assume that $inactive, $activetab and $currentaction have been set

    $tabs = $row = array();

    $row[] = new tabobject('configblock', me().'&amp;currentaction=configblock', 
                get_string('block_rss_configblock', 'block_rss_client'));

    $row[] = new tabobject('managefeeds', me().'&amp;currentaction=managefeeds', 
                get_string('block_rss_managefeeds', 'block_rss_client'));

    $tabs[] = $row;

    /// Print out the tabs and continue!
    print '<div align="center">';
    print_tabs($tabs, $currentaction);
    print '</div>';
?>