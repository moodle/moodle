<?php
die();
require_once('../../../config.php');

// set after moodle config, otherwise moodle will turn it back off
error_reporting(E_ALL);
ini_set("display_errors", 1);

@set_time_limit(0);
@raise_memory_limit("512M");
ini_set('memory_limit', "512M");

// course copier backup class
require_once('../class.backup.php');
/**
 * BACKUP A COURSE BY ID
 */
$backupid = 10002;
$courseid = 6435;
$userdata = 0;

$cc = new copier_backup($courseid, $backupid, $userdata);
$result = $cc->execute();

var_dump($result);
