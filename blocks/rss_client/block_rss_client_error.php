<?php //$Id$

    require_once('../../config.php');
    global $USER, $CFG;

    require_variable($error);

    print_header(get_string('error'), 
                 get_string('error'), 
                 get_string('error') );

    print urldecode($error);

    print_footer();
?>