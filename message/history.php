<?php // $Id$
      // For listing message histories
      
    require('../config.php');

    require_login();

/// Script parameters
    $userid = required_param('id', PARAM_INT);

/// Check the user we are talking to is valid
    if (! $user = get_record("user", "id", $userid)) {
        error("User ID was incorrect");
    }

    print_heading('This script is not completed yet');
?>
