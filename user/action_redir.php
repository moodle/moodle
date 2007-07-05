<?php
/**
* Wrapper script redirecting user operations to correct destination.
*/

require_once("../config.php");

$formaction = required_param('formaction', PARAM_FILE);
$id = required_param('id', PARAM_INT);

// Add every page will be redirected by this script
$actions = array(
        'messageselect.php',
        'extendenrol.php',
        'groupextendenrol.php',
        'addnote.php',
        'groupaddnote.php',
        );

if (array_search($formaction, $actions) === false) {
    print_error('unknownuseraction');
}

if (!confirm_sesskey()) {
    print_error('confirmsesskeybad');
}

require_once($formaction);
?>