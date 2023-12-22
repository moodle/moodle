<?php

namespace local_autologin\classes;

defined('MOODLE_INTERNAL') || die();

use external_api;
use external_function_parameters;
use external_value;

class autologin extends external_api {

   public static function autologin_parameters() {
       return new external_function_parameters(
           array(
               'idnumber' => new external_value(PARAM_TEXT, 'ID number of the student'),
           )
       );
   }

   public static function attemptautologin($idnumber) {
       global $DB, $USER;

       // Find the student with the matching ID number
       $student = $DB->get_record('user', array('idnumber' => $idnumber));

       if ($student) {
           // Log the student in
           complete_user_login($student);

           return true;
       } else {
           throw new moodle_exception('Student not found');
       }
   }
}
