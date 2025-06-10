<?php

/**
 * 
 *
 * @package    mod
 * @subpackage mylabmastering
 * @copyright
 * @author 
 * @license
 */

defined('MOODLE_INTERNAL') || die;

$plugin->version   = 2019012000;
$plugin->requires  = 2017051500;
$plugin->cron      = 0;
$plugin->component = 'mod_mylabmastering';
$plugin->maturity  = MATURITY_STABLE;
$plugin->release   = '1.0.1';

$plugin->dependencies = array(
	'mod_lti' => ANY_VERSION,
	'block_mylabmastering' => 2015081800
);
