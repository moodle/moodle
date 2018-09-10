<?php

require_once(dirname(__FILE__). '/../../../config.php');

global $USER;

$sesskey = $USER->sesskey;

echo $sesskey;