<?php

if(version_compare(PHP_VERSION, '5.1.0', '<')) {
    exit('Your current PHP version is: ' . PHP_VERSION . '. Haxe/PHP generates code for version 5.1.0 or later');
}
;
require_once dirname(__FILE__).'/lib/php/Boot.class.php';

Main::main();

?>