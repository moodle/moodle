<?php

$module->version = 2012030500;
$module->requires = 2012010100;
$module->component = 'mod_foo';
$module->dependencies = array(
    'mod_bar' => 2012030500,
    'mod_missing' => ANY_VERSION,
    'foolish_frog' => ANY_VERSION,
);
