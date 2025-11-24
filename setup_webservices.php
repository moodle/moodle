#!/usr/bin/env php
<?php
/**
 * Enable web services and add core_course_create_modules to a service
 */

define('CLI_SCRIPT', true);
require_once(__DIR__ . '/public/config.php');
require_once($CFG->libdir . '/adminlib.php');

echo "Enabling Web Services for Moodle\n";
echo "=================================\n\n";

// Enable web services
echo "1. Enabling web services...\n";
set_config('enablewebservices', 1);
echo "   ✓ Web services enabled\n\n";

// Enable REST protocol
echo "2. Enabling REST protocol...\n";
$protocols = get_config('core', 'webserviceprotocols');
if (strpos($protocols, 'rest') === false) {
    $protocols = empty($protocols) ? 'rest' : $protocols . ',rest';
    set_config('webserviceprotocols', $protocols);
}
echo "   ✓ REST protocol enabled\n\n";

// Check if a custom service exists, if not create one
echo "3. Setting up web service...\n";
$service = $DB->get_record('external_services', array('shortname' => 'customapi'));

if (!$service) {
    echo "   Creating new 'Custom API' service...\n";
    $service = new stdClass();
    $service->name = 'Custom API';
    $service->shortname = 'customapi';
    $service->enabled = 1;
    $service->restrictedusers = 0;
    $service->downloadfiles = 1;
    $service->uploadfiles = 1;
    $service->timecreated = time();
    $service->timemodified = time();
    $service->id = $DB->insert_record('external_services', $service);
    echo "   ✓ Service created (ID: {$service->id})\n";
} else {
    echo "   ✓ Service already exists (ID: {$service->id})\n";
}

// Add core_course_create_modules to the service
echo "\n4. Adding core_course_create_modules to service...\n";
$function = $DB->get_record('external_functions', array('name' => 'core_course_create_modules'));

if (!$function) {
    echo "   ✗ Function not found in external_functions table\n";
    echo "   Running upgrade to register new functions...\n";
    
    // Purge cache and upgrade
    purge_all_caches();
    
    // Re-check
    $function = $DB->get_record('external_functions', array('name' => 'core_course_create_modules'));
    if ($function) {
        echo "   ✓ Function now registered\n";
    } else {
        echo "   ✗ Function still not found. You may need to run upgrade.php\n";
        exit(1);
    }
}

// Check if function is already in service
$exists = $DB->record_exists('external_services_functions', array(
    'externalserviceid' => $service->id,
    'functionname' => 'core_course_create_modules'
));

if (!$exists) {
    $servicefunction = new stdClass();
    $servicefunction->externalserviceid = $service->id;
    $servicefunction->functionname = 'core_course_create_modules';
    $DB->insert_record('external_services_functions', $servicefunction);
    echo "   ✓ Function added to service\n";
} else {
    echo "   ✓ Function already in service\n";
}

echo "\n5. Web Service Configuration Summary\n";
echo "   Service Name: Custom API\n";
echo "   Service Shortname: customapi\n";
echo "   Service ID: {$service->id}\n";
echo "   Function: core_course_create_modules\n";

echo "\n✓ Setup complete!\n\n";
echo "Next steps:\n";
echo "1. Go to: Site administration > Server > Web services > Manage tokens\n";
echo "2. Create a token for the 'Custom API' service\n";
echo "3. Use that token to call core_course_create_modules via REST API\n";

exit(0);
