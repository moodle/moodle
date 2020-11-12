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
 * Message outputs configuration page
 *
 * @package    message
 * @copyright  2011 Lancaster University Network Services Limited
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(__DIR__ . '/../config.php');
require_once($CFG->dirroot . '/message/lib.php');
require_once($CFG->libdir.'/adminlib.php');

// This is an admin page.
admin_externalpage_setup('managemessageoutputs');

// Fetch processors.
$allprocessors = get_message_processors();
$processors = array_filter($allprocessors, function($processor) {
    return $processor->enabled;
});
$disabledprocessors = array_filter($allprocessors, function($processor) {
    return !$processor->enabled;
});

// Fetch message providers.
$providers = get_message_providers();
// Fetch the manage message outputs interface.
$preferences = get_message_output_default_preferences();

if (($form = data_submitted()) && confirm_sesskey()) {
    $newpreferences = array();
    // Prepare default message outputs settings.
    foreach ($providers as $provider) {
        $componentproviderbase = $provider->component.'_'.$provider->name;
        $disableprovidersetting = $componentproviderbase.'_disable';
        $providerdisabled = false;
        if (!isset($form->$disableprovidersetting)) {
            $providerdisabled = true;
            $newpreferences[$disableprovidersetting] = 1;
        } else {
            $newpreferences[$disableprovidersetting] = 0;
        }

        foreach (array('permitted', 'loggedin', 'loggedoff') as $setting) {
            $value = null;
            $componentprovidersetting = $componentproviderbase.'_'.$setting;
            if ($setting == 'permitted') {
                // If we deal with permitted select element, we need to create individual
                // setting for each possible processor. Note that this block will
                // always be processed first after entring parental foreach iteration
                // so we can change form values on this stage.
                foreach ($processors as $processor) {
                    $value = '';
                    if (isset($form->{$componentprovidersetting}[$processor->name])) {
                        $value = $form->{$componentprovidersetting}[$processor->name];
                    }
                    // Ensure that loggedin loggedoff options are set correctly for this permission.
                    if (($value == 'disallowed') || $providerdisabled) {
                        // It might be better to unset them, but I can't figure out why that cause error.
                        $form->{$componentproviderbase.'_loggedin'}[$processor->name] = 0;
                        $form->{$componentproviderbase.'_loggedoff'}[$processor->name] = 0;
                    } else if ($value == 'forced') {
                        $form->{$componentproviderbase.'_loggedin'}[$processor->name] = 1;
                        $form->{$componentproviderbase.'_loggedoff'}[$processor->name] = 1;
                    }
                    // Record the site preference.
                    $newpreferences[$processor->name.'_provider_'.$componentprovidersetting] = $value;
                }
            } else {
                $newsettings = array();
                if (property_exists($form, $componentprovidersetting)) {
                    // We must be processing loggedin or loggedoff checkboxes.
                    // Store defained comma-separated processors as setting value.
                    // Using array_filter eliminates elements set to 0 above.
                    $newsettings = array_keys(array_filter($form->{$componentprovidersetting}));
                }

                // Let's join existing setting values for disabled processors.
                $property = 'message_provider_'.$componentprovidersetting;
                if (property_exists($preferences, $property)) {
                    $existingsetting = $preferences->$property;
                    foreach ($disabledprocessors as $disable) {
                        if (strpos($existingsetting, $disable->name) > -1) {
                            $newsettings[] = $disable->name;
                        }
                    }
                }

                $value = join(',', $newsettings);
                if (empty($value)) {
                    $value = null;
                }
            }
            if ($setting != 'permitted') {
                // We have already recoded site preferences for 'permitted' type.
                $newpreferences['message_provider_'.$componentprovidersetting] = $value;
            }
        }
    }

    // Update database.
    $transaction = $DB->start_delegated_transaction();

    // Save processors enabled/disabled status.
    foreach ($allprocessors as $processor) {
        $enabled = isset($form->{$processor->name});
        if ($enabled != $processor->enabled) {
            add_to_config_log($processor->name, $processor->enabled, $enabled, 'core');
        }
        \core_message\api::update_processor_status($processor, $enabled);
    }

    foreach ($newpreferences as $name => $value) {
        $old = isset($preferences->$name) ? $preferences->$name : '';

        if ($old != $value) {
            add_to_config_log($name, $old, $value, 'core');
        }

        set_config($name, $value, 'message');
    }
    $transaction->allow_commit();

    core_plugin_manager::reset_caches();

    $url = new moodle_url('message.php');
    redirect($url);
}

// Page settings
$PAGE->set_context(context_system::instance());
$PAGE->requires->js_init_call('M.core_message.init_defaultoutputs');

$renderer = $PAGE->get_renderer('core', 'message');

// Display the page.
echo $OUTPUT->header();
echo $renderer->manage_messageoutput_settings($allprocessors, $processors, $providers, $preferences);
echo $OUTPUT->footer();
