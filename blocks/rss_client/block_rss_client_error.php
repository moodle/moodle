<?php //$Id$

    require_once('../../config.php');

    require_variable($error);

    print_header(get_string('error'), 
                 get_string('error'), 
                 get_string('error') );

    echo clean_text(urldecode($error));

    print_footer();
?>
