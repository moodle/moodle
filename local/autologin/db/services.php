<?php

$functions = array(
   'local_autologin' => array(
       'classname'  => 'local_autologin_autologin',
       'methodname' => 'attemptautologin',
       'description' => 'Directly logs in a student based on their ID number',
       'type' => 'read',
       'ajax' => true,
       'capabilities' => '',
   )
);
