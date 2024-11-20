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
        if (!isset($form->$disableprovidersetting)) {
            $newpreferences[$disableprovidersetting] = 1;
        } else {
            $newpreferences[$disableprovidersetting] = 0;
        }

        $componentprovidersetting = $componentproviderbase.'_locked';
        foreach ($processors as $processor) {
            $value = 0;
            if (isset($form->{$componentprovidersetting}[$processor->name])) {
                $value = $form->{$componentprovidersetting}[$processor->name];
                if ($value == 'on') {
                    $value = 1;
                }
            }

            // Record the site preference.
            $newpreferences[$processor->name.'_provider_'.$componentprovidersetting] = $value;
        }

        $componentprovidersetting = $componentproviderbase.'_enabled';
        $newsettings = [];
        if (isset($form->$componentprovidersetting)) {
            // Store defined comma-separated processors as setting value.
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

        // Record the site preference.
        $newpreferences['message_provider_'.$componentprovidersetting] = $value;
    }

    // Update database.
    $transaction = $DB->start_delegated_transaction();

    // Save processors enabled/disabled status.
    foreach ($allprocessors as $processor) {
        $enabled = isset($form->{$processor->name});
        $class = \core_plugin_manager::resolve_plugininfo_class('message');
        $class::enable_plugin($processor->name, $enabled);
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
$renderer = $PAGE->get_renderer('core', 'message');

// Display the page.
echo $OUTPUT->header();
echo $renderer->manage_messageoutput_settings($allprocessors, $processors, $providers, $preferences);
echo $OUTPUT->footer();
