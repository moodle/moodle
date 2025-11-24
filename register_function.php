#!/usr/bin/env php
<?php
/**
 * Manually register the core_course_create_modules external function
 */

define('CLI_SCRIPT', true);
require_once(__DIR__ . '/public/config.php');
require_once($CFG->dirroot . '/lib/externallib.php');

echo "Registering core_course_create_modules\n";
echo "=======================================\n\n";

// Load the services definition
$functions = array();
require($CFG->dirroot . '/lib/db/services.php');

if (!isset($functions['core_course_create_modules'])) {
    echo "✗ Function not found in services.php\n";
    exit(1);
}

$functiondef = $functions['core_course_create_modules'];

echo "Function definition found:\n";
echo "  Class: {$functiondef['classname']}\n";
echo "  Method: {$functiondef['methodname']}\n";
echo "  Description: {$functiondef['description']}\n\n";

// Check if already registered
$existing = $DB->get_record('external_functions', array('name' => 'core_course_create_modules'));

if ($existing) {
    echo "Function already registered in database (ID: {$existing->id})\n";
    echo "Updating definition...\n";
    
    $existing->classname = $functiondef['classname'];
    $existing->methodname = $functiondef['methodname'];
    $existing->classpath = isset($functiondef['classpath']) ? $functiondef['classpath'] : null;
    $existing->component = 'moodle';
    $existing->capabilities = isset($functiondef['capabilities']) ? $functiondef['capabilities'] : '';
    $existing->services = isset($functiondef['services']) ? $functiondef['services'] : null;
    
    $DB->update_record('external_functions', $existing);
    echo "✓ Function updated\n";
} else {
    echo "Registering new function...\n";
    
    $function = new stdClass();
    $function->name = 'core_course_create_modules';
    $function->classname = $functiondef['classname'];
    $function->methodname = $functiondef['methodname'];
    $function->classpath = isset($functiondef['classpath']) ? $functiondef['classpath'] : null;
    $function->component = 'moodle';
    $function->capabilities = isset($functiondef['capabilities']) ? $functiondef['capabilities'] : '';
    $function->services = isset($functiondef['services']) ? $functiondef['services'] : null;
    
    $function->id = $DB->insert_record('external_functions', $function);
    echo "✓ Function registered (ID: {$function->id})\n";
}

// Add to Custom API service
echo "\nAdding to Custom API service...\n";
$service = $DB->get_record('external_services', array('shortname' => 'customapi'));

if (!$service) {
    echo "✗ Custom API service not found. Run setup_webservices.php first.\n";
    exit(1);
}

$exists = $DB->record_exists('external_services_functions', array(
    'externalserviceid' => $service->id,
    'functionname' => 'core_course_create_modules'
));

if (!$exists) {
    $servicefunction = new stdClass();
    $servicefunction->externalserviceid = $service->id;
    $servicefunction->functionname = 'core_course_create_modules';
    $DB->insert_record('external_services_functions', $servicefunction);
    echo "✓ Function added to Custom API service\n";
} else {
    echo "✓ Function already in Custom API service\n";
}

// Purge cache
echo "\nPurging caches...\n";
purge_all_caches();
echo "✓ Caches purged\n";

echo "\n✓ Setup complete!\n";
echo "\nThe function is now available in:\n";
echo "  Site administration > Server > Web services > External services > Custom API\n";

exit(0);
