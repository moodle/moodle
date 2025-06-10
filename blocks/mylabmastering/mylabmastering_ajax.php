<?php

/**
 * Person MyLab & Mastering block AJAX code.
 *
 * This file is used to perform an async call to validate local mapping information against Highlander mappings.
 * If the courses have been unlinked or changed (new, update, delete), it updates the local data appropriately
 * and sends a response back to the calling method to update the page as needed.
 *
 * @package block_mylabmastering
 * @author Jeffery A. Moulton
 * @copyright 2015-2016 Pearson Education
 * @license
 */

define('AJAX_SCRIPT', true);
global $CFG;

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once(dirname(dirname(dirname(__FILE__))) . '/lib/filelib.php');
require_once(dirname(dirname(dirname(__FILE__))) . '/lib/modinfolib.php');
require_once(dirname(__FILE__) . '/locallib.php');

$err = new stdClass();

// Parameters
// If not found, an error is thrown (Ref: lib/moodlelib.php, line: 522)
$course_id = required_param('course_id', PARAM_TEXT);
$user_id = required_param('user_id', PARAM_TEXT);

//set context
$PAGE->set_context(context_system::instance());

// Send the AJAX headers
echo($OUTPUT->header());

$updated_mapping = false;
$new_course = false;
$description = '';

// Perform the mapping request
$local_config = mylabmastering_course_has_config($course_id);
$mapping = mylabmastering_get_mapping($course_id);

// Check that something came back
if (!isset($mapping->code) || !isset($local_config->code)) {
    echo(json_encode(array('error' => 'Unable to get mapping information for comparison')));
    die;
}

// Was a value returned?
if (isset($mapping->code) && $mapping->code !== 'unmapped') {
    // Mapping was returned. Validate it against the value passed to the object (localcode).
    // If they match, DO NOTHING. If they don't match, then additional processing is required.
    if ($mapping->code !== $local_config->code || $mapping->platform !== $local_config->platform) {
        $updated_mapping = true;

        // The course mapping has been changed. Remove the existing links.
        mylabmastering_handle_code_change($course_id);

        // Get the new links and create the LTI types
        $product_content = mylabmastering_get_content_links($mapping->code);

        if ($product_content) {
            $links = $product_content->bundle->links;
            foreach ($links as $link) {
                mylabmastering_create_lti_type($link, $course_id, $user_id);
            }
        }

        // Update the local config with the new information from the mapping
        $local_config->code = $mapping->code;
        $local_config->platform = $mapping->platform;
        $local_config->description = '<p>Pearson MyLab & Mastering course pairing: ' . $mapping->platform . '</p>';
        $local_config->plugin_name = 'block_mylabmastering';

        mylabmastering_update_course_config($local_config);
    }
}
else {
    // No mapping was not returned from Highlander. If mapping is stored locally it needs to be deleted.
    if ($local_config->code !== 'unmapped') {
        $updated_mapping = true;

        // Remove the LTI links and reset the local configuration.
        mylabmastering_handle_code_change($course_id);
        mylabmastering_reset_local_mapping($local_config);
    }
}

if ($updated_mapping) {
    rebuild_course_cache($course_id);
    echo(json_encode($local_config));
} else {
    echo(json_encode(array("code" => "no update", "plugin_name" => "block_mylabmastering")));
}