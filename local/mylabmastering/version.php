<?php

/**
 * Version details.
 *
 * @package    local_mylabmastering
 * @copyright  
 * @license    
 */

defined('MOODLE_INTERNAL') || die;

$plugin->version   = 2019012000;
$plugin->requires  = 2017051500;
$plugin->cron      = 0;
$plugin->component = 'local_mylabmastering';
$plugin->maturity  = MATURITY_STABLE;
$plugin->release   = '1.0.1';

$plugin->dependencies = array(
		'mod_lti' => ANY_VERSION
);
