<?php
// Vérifie si l'utilisateur est connecté à Moodle
require_once('config.php');
global $CFG, $SESSION;
session_set_cookie_params(0, $CFG->sessioncookiepath, $CFG->sessioncookiedomain, $CFG->cookiesecure, true);
session_name($CFG->sessioncookiename);
session_start();

if (!empty($SESSION->userid)) {
    // L'utilisateur est connecté à Moodle, on renvoie une réponse HTTP 200
    http_response_code(200);
} else {
    // L'utilisateur n'est pas connecté à Moodle, on renvoie une réponse HTTP 401
    http_response_code(401);
}
?>
