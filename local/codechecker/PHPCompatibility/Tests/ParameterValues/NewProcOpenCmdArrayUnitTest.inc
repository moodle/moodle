<?php

// OK.
$proc = proc_open(
        '/usr/bin/passwd ' . escapeshellarg($username),
        $descriptorspec,
        $pipes
);
$proc = proc_open('tail -F /var/log/nginx/stats.access.log', $descriptorspec, $pipes);
$proc = proc_open('php ' . $abs_path, $spec, $pipes, null, $_ENV);

// Undetermined. Ignore.
$proc = proc_open($command, $descriptorSpec, $pipes);
$proc = proc_open(escapeshellarg($scriptFile), $descriptorspec, $pipes, $wd);
$proc = proc_open(self::COMMAND, $descriptorSpec, $pipes);

// PHP 7.4: passing $cmd as an array.
$proc = proc_open(['php', '-r', 'echo "Hello World\n";'], $descriptors, $pipes);
$proc = proc_open(
    array(
        'php',
        '-r',
        'echo "Hello World\n";',
    ),
    $descriptors,
    $pipes
);

// PHP 7.4: Warning. PHP will automatically escape arguments when $cmd is passed as an array.
$proc = proc_open(['php', '-r', escapeshellarg($echo)], $descriptors, $pipes);
$proc = proc_open(
    array(
        'phpcs',
        '--standard=' . escapeshellarg($standard),
        './path/to/' . escapeshellarg($file),
    ),
    $descriptors,
    $pipes
);
