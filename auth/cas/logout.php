<?php
 // logout the user from CAS server (destroy the ticket)
       global $CFG;
       require_once($CFG->dirroot.'/config.php');
       include_once($CFG->dirroot.'/lib/cas/CAS.php');
       phpCAS::client($CFG->cas_version,$CFG->cas_hostname,(Integer)$CFG->cas_port,$CFG->cas_baseuri);
       $backurl = $CFG->wwwroot;
       phpCAS::logout($backurl);

?>