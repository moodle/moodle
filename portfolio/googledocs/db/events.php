<?php

$handlers = array (
    'user_deleted' => array (
         'handlerfile'      => '/portfolio/googledocs/lib.php',
         'handlerfunction'  => 'portfolio_googledocs_user_deleted',
         'schedule'         => 'cron',
         'internal'         => 0,
     ),
);


