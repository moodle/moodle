<?php

   require_once('../config.php');
   require_once($CFG->libdir.'/adminlib.php');

   admin_externalpage_setup('toinodb');

   $confirm = optional_param('confirm', 0, PARAM_BOOL);

   require_login();

   require_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM));

   admin_externalpage_print_header();
   print_heading('Convert all MySQL tables from MYISAM to InnoDB');

   if ($CFG->dbfamily != 'mysql') {
        notice('This function is for MySQL databases only!', 'index.php');
   }

   if (data_submitted() and $confirm and confirm_sesskey()) {

       notify('Please be patient and wait for this to complete...', 'notifysuccess');

       if ($tables = $db->MetaTables()) {
           $db->debug = true;
           foreach ($tables as $table) {
               execute_sql("ALTER TABLE $table TYPE=INNODB; ");
           }
           $db->debug = false;
       }
       notify('... done.', 'notifysuccess');
       print_continue('index.php');
       admin_externalpage_print_footer();

   } else {
       $optionsyes = array('confirm'=>'1', 'sesskey'=>sesskey());
       notice_yesno('Are you sure you want convert all your tables to the InnoDB format?',
                    'innodb.php', 'index.php', $optionsyes, NULL, 'post', 'get');
       admin_externalpage_print_footer();
   }

?>
