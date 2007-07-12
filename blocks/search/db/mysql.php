<?php

// THIS FILE IS DEPRECATED!  PLEASE DO NOT MAKE CHANGES TO IT!
//
// IT IS USED ONLY FOR UPGRADES FROM BEFORE MOODLE 1.7, ALL 
// LATER CHANGES SHOULD USE upgrade.php IN THIS DIRECTORY.
//
// This file is tailored to MySQL

function search_upgrade($oldversion=0) {

    global $CFG;
    
    $result = true;

    if ($oldversion < 2007080100 and $result) {
        modify_database ("", "ALTER TABLE `{$CFG->prefix}search_documents` ADD `item_type` VARCHAR( 32 ) NOT NULL AFTER `doctype` ; ");
        modify_database ("", "ALTER TABLE `{$CFG->prefix}search_documents` ADD INDEX ( `item_type` ) ; ");
        modify_database ("", "ALTER TABLE `{$CFG->prefix}search_documents` CHANGE `doctype` `doctype` VARCHAR( 32 ) DEFAULT 'none' ; ");
        modify_database ("", "ALTER TABLE `{$CFG->prefix}search_documents` CHANGE `title` `title` VARCHAR( 255 ) ; ");
        modify_database ("", "ALTER TABLE `{$CFG->prefix}search_documents` CHANGE `url` `url` VARCHAR( 255 ) ; ");
        modify_database ("", "ALTER TABLE `{$CFG->prefix}mdl_search_documents` CHANGE `docid` `docid` VARCHAR( 32 ) ; ");
        $result = true;
    }

    //////  DO NOT ADD NEW THINGS HERE!!  USE upgrade.php and the lib/ddllib.php functions.

    //Finally, return result
    return $result;
}
