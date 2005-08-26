<?php
// $Id$
// logout the user from CAS server (destroy the ticket)
defined('MOODLE_INTERNAL') or die('Direct access to this script is forbidden.');

       global $CFG;
       if ($CFG->cas_logout){
         require_once($CFG->dirroot.'/config.php');
         include_once($CFG->dirroot.'/lib/cas/CAS.php');
         phpCAS::client($CFG->cas_version,$CFG->cas_hostname,(Integer)$CFG->cas_port,$CFG->cas_baseuri);
         $backurl = $CFG->wwwroot;
         phpCAS::logout($backurl);
       }

?>