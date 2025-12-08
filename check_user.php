<?php
define('CLI_SCRIPT', true);
require(__DIR__ . '/public/config.php');
require_once($CFG->libdir.'/moodlelib.php');

$email = 'alerting@aust-mfg.com';
echo "Checking user with email: $email\n";

$user = $DB->get_record('user', ['email' => $email]);

if ($user) {
    echo "User FOUND:\n";
    echo "ID: " . $user->id . "\n";
    echo "Username: " . $user->username . "\n";
    echo "Confirmed: " . $user->confirmed . "\n";
    echo "Suspended: " . $user->suspended . "\n";
    echo "Deleted: " . $user->deleted . "\n";
    echo "Auth: " . $user->auth . "\n";
} else {
    echo "User NOT FOUND.\n";
}
