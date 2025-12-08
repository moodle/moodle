<?php
define('CLI_SCRIPT', true);
require(__DIR__ . '/public/config.php');
require_once($CFG->libdir.'/moodlelib.php');
require_once($CFG->dirroot.'/user/lib.php');

$user = new stdClass();
$user->username = 'alerting_test';
$user->password = 'TestPass123!';
$user->email = 'alerting@aust-mfg.com';
$user->firstname = 'Alerting';
$user->lastname = 'Test';
$user->city = 'TestCity';
$user->country = 'AU';
$user->confirmed = 1;
$user->mnethostid = $CFG->mnet_localhost_id;

try {
    $user->id = user_create_user($user);
    echo "User created successfully with ID: " . $user->id . "\n";
} catch (Exception $e) {
    echo "Error creating user: " . $e->getMessage() . "\n";
}
