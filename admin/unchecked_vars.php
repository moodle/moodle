<?php

    include("../config.php");

    $action = optional_param('action','LIST',PARAM_ALPHA);
    $url    = optional_param('url');
    $info   = optional_param('info');

    if (!isadmin()) {
        error("Only the admin can use this page");
    }

    print_header('Admin : Unchecked Vars' );

    if ($action == 'DELETE') {
        delete_records('log', 'url', $url, 'info', $info);
    }

    if ($errors = get_records_sql("SELECT DISTINCT url, info 
                                   FROM {$CFG->prefix}log
                                   WHERE module = 'dev' AND
                                         action = 'unchecked vars'")) {
        echo '<table border="1" cellpadding="5">';
        foreach ($errors as $error) {
            echo '<tr>';
            echo '<td>'.$error->url.'</td>';
            echo '<td>'.$error->info.'</td>';
            echo '<td><a href="unchecked_vars.php?action=DELETE&amp;url='.$error->url.'&amp;info='.$error->info.'">Delete this</a></td>';
            echo '</tr>';
        }
        echo '</table>';
    } else {
        echo "No unchecked vars were detected";
    }
?>
