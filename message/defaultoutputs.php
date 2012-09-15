<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Default message outputs configuration page
 *
 * @package   core_message
 * @copyright 2011 Lancaster University Network Services Limited
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(dirname(__FILE__) . '/../config.php');
require_once($CFG->dirroot . '/message/lib.php');
require_once($CFG->libdir.'/adminlib.php');

// This is an admin page
admin_externalpage_setup('defaultmessageoutputs');

// Require site configuration capability
require_capability('moodle/site:config', context_system::instance());

// Fetch processors
$processors = get_message_processors(true);
// Fetch message providers
$providers = get_message_providers();

if (($form = data_submitted()) && confirm_sesskey()) {
    $preferences = array();
    // Prepare default message outputs settings
    foreach ( $providers as $provider) {
        $componentproviderbase = $provider->component.'_'.$provider->name;
        foreach (array('permitted', 'loggedin', 'loggedoff') as $setting){
            $value = null;
            $componentprovidersetting = $componentproviderbase.'_'.$setting;
            if ($setting == 'permitted') {
                // if we deal with permitted select element, we need to create individual
                // setting for each possible processor. Note that this block will
                // always be processed first after entring parental foreach iteration
                // so we can change form values on this stage.
                foreach($processors as $processor) {
                    $value = '';
                    if (isset($form->{$componentprovidersetting}[$processor->name])) {
                        $value = $form->{$componentprovidersetting}[$processor->name];
                    }
                    // Ensure that loggedin loggedoff options are set correctly
                    // for this permission
                    if ($value == 'forced') {
                        $form->{$componentproviderbase.'_loggedin'}[$processor->name] = 1;
                        $form->{$componentproviderbase.'_loggedoff'}[$processor->name] = 1;
                    } else if ($value == 'disallowed') {
                        // It might be better to unset them, but I can't figure out why that cause error
                        $form->{$componentproviderbase.'_loggedin'}[$processor->name] = 0;
                        $form->{$componentproviderbase.'_loggedoff'}[$processor->name] = 0;
                    }
                    // record the site preference
                    $preferences[$processor->name.'_provider_'.$componentprovidersetting] = $value;
                }
            } else if (array_key_exists($componentprovidersetting, $form)) {
                // we must be processing loggedin or loggedoff checkboxes. Store
                // defained comma-separated processors as setting value.
                // Using array_filter eliminates elements set to 0 above
                $value = join(',', array_keys(array_filter($form->{$componentprovidersetting})));
                if (empty($value)) {
                    $value = null;
                }
            }
            if ($setting != 'permitted') {
                // we have already recoded site preferences for 'permitted' type
                $preferences['message_provider_'.$componentprovidersetting] = $value;
            }
        }
    }

    // Update database
    $transaction = $DB->start_delegated_transaction();
    foreach ($preferences as $name => $value) {
        set_config($name, $value, 'message');
    }
    $transaction->allow_commit();

    // Redirect
    $url = new moodle_url('defaultoutputs.php');
    redirect($url);
}



// Page settings
$PAGE->set_context(context_system::instance());
$PAGE->requires->js_init_call('M.core_message.init_defaultoutputs');

// Grab the renderer
$renderer = $PAGE->get_renderer('core', 'message');

// Display the manage message outputs interface
$preferences = get_message_output_default_preferences();
$messageoutputs = $renderer->manage_defaultmessageoutputs($processors, $providers, $preferences);

// Display the page
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('defaultmessageoutputs', 'message'));
echo $messageoutputs;
echo $OUTPUT->footer();
