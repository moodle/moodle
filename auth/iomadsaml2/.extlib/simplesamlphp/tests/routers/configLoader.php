<?php

declare(strict_types=1);

use SimpleSAML\Configuration;

/*
 * This "router" (a script that's executed for every request received by PHP's built-in web server) will look
 * for a file in the system's temporary directory, with the PID of the current process as its name, and the
 * '.lock' extension. If the file exists, it will try to include it and preload SimpleSAMLphp's configuration with
 * the $config array defined in that file.
 * This is useful to configure SimpleSAMLphp dynamically when running inside the built-in server, so that
 * we can test different configurations without the need to keep a structure of files.
 *
 * In order to use it:
 *
 * 1. Create an array with the SimpleSAMLphp configuration you would like to use.
 * 2. Start the built-in server passing "configLoader" as the first parameter to the constructor:
 *      $server = new BuiltInServer('configLoader');
 *      $addr = $server->start();
 * 3. Get the PID of the server once it has started:
 *      $pid = $server->getPid();
 * 4. Build the path to the file that this script will use:
 *      $file = sys_get_temp_dir().'/'.$pid.'.lock';
 * 5. Dump the configuration array to the file:
 *      file_put_contents("<?php\n\$config = ".var_export($config, true).";\n");
 * 6. Make a request to the server:
 *      $server->get($query, $parameters);
 * 7. Remove the temporary file when done:
 *      unlink($file);
 */

include_once(sys_get_temp_dir() . '/' . getmypid() . '.lock');

// load SimpleSAMLphp's autoloader
require_once(dirname(dirname(dirname(__FILE__))) . '/vendor/autoload.php');

// initialize configuration
if (isset($config)) {
    Configuration::loadFromArray($config, '[ARRAY]', 'simplesaml');
}

// let the script proceed
// see: http://php.net/manual/en/features.commandline.webserver.php
return false;
