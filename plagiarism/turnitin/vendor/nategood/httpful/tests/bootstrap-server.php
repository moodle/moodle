<?php

$php_version = phpversion();
$php_major = (float) substr($php_version, 0, 3);
$pid = 0;

// Define SIGKILL if pcntl is not found
if (!function_exists('pcntl_signal')) {
    define('SIGKILL', 9);
}

if ($php_major < 5.4) {
    define('WITHOUT_SERVER', true);
} else {
    // Command that starts the built-in web server
    $command = sprintf('php -S %s:%d -t %s >./server.log 2>&1 & echo $!', \WEB_SERVER_HOST, \WEB_SERVER_PORT, \WEB_SERVER_DOCROOT);

    // Execute the command and store the process ID
    $output = [];
    exec($command, $output, $exit_code);

    // sleep for a second to let server come up
    sleep(1);
    $pid = (int) $output[0];

    // check server.log to see if it failed to start
    $server_logs = file_get_contents('./server.log');
    if (strpos($server_logs, 'Fail') !== false) {
        // server failed to start for some reason
        print 'Failed to start server! Logs:' . \PHP_EOL . \PHP_EOL;
        print_r($server_logs);
        exit(1);
    }

    echo sprintf('%s - Web server started on %s:%d with PID %d', date('r'), \WEB_SERVER_HOST, \WEB_SERVER_PORT, $pid) . \PHP_EOL;

    register_shutdown_function(function() use ($pid) {
        // cleanup after ourselves -- remove log file, shut down server
        unlink("./server.log");
        posix_kill($pid, \SIGKILL);
    });
}
