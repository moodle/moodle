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
 * Contains renderer objects for messaging
 *
 * @package    core_message
 * @copyright  2011 Lancaster University Network Services Limited
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * message Renderer
 *
 * Class for rendering various message objects
 *
 * @package    core_message
 * @subpackage message
 * @copyright  2011 Lancaster University Network Services Limited
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_message_renderer extends plugin_renderer_base {

    /**
     * Display the interface to manage both message outputs and default message outputs
     *
     * @param  array $allprocessors  array of objects containing all message processors
     * @param  array $processors  array of objects containing active message processors
     * @param  array $providers   array of objects containing message providers
     * @param  stdClass $preferences object containing current preferences
     * @return string The text to render
     */
    public function manage_messageoutput_settings($allprocessors, $processors, $providers, $preferences) {
        $output = html_writer::start_tag('form', array('id' => 'defaultmessageoutputs', 'method' => 'post'));
        $output .= html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()));

        // Add message output processors enabled/disabled and settings.
        $output .= $this->heading(get_string('messageoutputs', 'message'));
        $output .= $this->manage_messageoutputs($allprocessors);

        // Add active message output processors settings.
        $output .= $this->manage_defaultmessageoutputs($processors, $providers, $preferences);

        $output .= html_writer::start_tag('div', array('class' => 'form-buttons'));
        $output .= html_writer::empty_tag('input',
            array('type' => 'submit', 'value' => get_string('savechanges', 'admin'), 'class' => 'form-submit btn btn-primary')
        );
        $output .= html_writer::end_tag('div');
        $output .= html_writer::end_tag('form');

        return $output;
    }

    /**
     * Display the interface to manage message outputs
     *
     * @param  array  $processors array of objects containing message processors
     * @return string The text to render
     */
    public function manage_messageoutputs($processors) {
        // Display the current workflows
        $table = new html_table();
        $table->attributes['class'] = 'admintable generaltable';
        $table->data        = array();
        $table->head        = array(
            get_string('name'),
            get_string('enable'),
            get_string('settings'),
        );
        $table->colclasses = array(
            'displayname', 'availability text-center', 'settings',
        );

        foreach ($processors as $processor) {
            $row = new html_table_row();
            $row->attributes['class'] = 'messageoutputs';

            $name = new html_table_cell(get_string('pluginname', 'message_'.$processor->name));
            $enable = new html_table_cell();
            if (!$processor->available) {
                $enable->text = html_writer::nonempty_tag('span', get_string('outputnotavailable', 'message'),
                    array('class' => 'error')
                );
            } else {
                $enable->text = html_writer::checkbox($processor->name, $processor->id, $processor->enabled, '',
                    array('id' => $processor->name)
                );
            }
            // Settings
            $settings = new html_table_cell();
            if ($processor->available && $processor->hassettings) {
                $settingsurl = new moodle_url('/admin/settings.php', array('section' => 'messagesetting'.$processor->name));
                $settings->text = html_writer::link($settingsurl, get_string('settings', 'message'));
            }

            $row->cells = array($name, $enable, $settings);
            $table->data[] = $row;
        }
        return html_writer::table($table);
    }

    /**
     * Display the interface to manage default message outputs
     *
     * @param  array $processors  array of objects containing message processors
     * @param  array $providers   array of objects containing message providers
     * @param  stdClass $preferences object containing current preferences
     * @return string The text to render
     */
    public function manage_defaultmessageoutputs($processors, $providers, $preferences) {
        $context = [];

        foreach ($processors as $processor) {
            $processor->displayname = get_string('pluginname', 'message_'.$processor->name);
        }

        $activitycomponents = [];
        $othercomponents = [];

        foreach ($providers as $provider) {
            $provider->displayname = get_string('messageprovider:'.$provider->name, $provider->component);
            $providersettingprefix = $provider->component.'_'.$provider->name.'_';
            $provider->enabledsetting = $providersettingprefix.'disable';
            $provider->enabled = empty($preferences->{$provider->enabledsetting});
            $provider->enabledlabel = get_string('providerenabled', 'message', $provider->displayname);
            $provider->settings = [];

            // Settings for each processor
            foreach ($processors as $processor) {
                $setting = new StdClass();

                $setting->lockedsetting = $providersettingprefix.'locked['.$processor->name.']';
                $preference = $processor->name.'_provider_'.$providersettingprefix.'locked';

                $setting->locked = false;
                if (property_exists($preferences, $preference)) {
                    $setting->locked = $preferences->{$preference} == 1;
                }

                $setting->enabledsetting = $providersettingprefix.'enabled['.$processor->name.']';
                $preference = 'message_provider_'.$providersettingprefix.'enabled';

                $setting->enabled = false;
                if (property_exists($preferences, $preference)) {
                    $setting->enabled = (int)in_array($processor->name, explode(',', $preferences->{$preference}));
                }
                $labelparams = [
                    'provider'  => $provider->displayname,
                    'processor' => $processor->displayname,
                ];
                $setting->enabledlabel = get_string('sendingviaenabled', 'message', $labelparams);
                $setting->lockedlabel = get_string('sendingvialocked', 'message', $labelparams);

                $provider->settings[] = $setting;
            }

            // Order the components so that the activities appear first, followed
            // by the system and then anything else.
            if ($provider->component != 'moodle') {
                if (substr($provider->component, 0, 4) == 'mod_') {
                    // Activities.
                    $activitycomponents[] = $provider->component;
                } else {
                    // Other stuff.
                    $othercomponents[] = $provider->component;
                }
            }
        }

        $activitycomponents = array_unique($activitycomponents);
        asort($activitycomponents);
        $othercomponents = array_unique($othercomponents);
        asort($othercomponents);
        $components = array_merge($activitycomponents, ['moodle'], $othercomponents);
        asort($providers);

        $colspan = count($processors) + 2;
        $componentsexport = [];

        foreach ($components as $component) {
            $componentexport = new StdClass();
            $componentexport->name = $component;

            if ($component != 'moodle') {
                $componentexport->displayname = get_string('pluginname', $component);
            } else {
                $componentexport->displayname = get_string('coresystem');
            }

            $componentexport->providers = [];
            foreach ($providers as $provider) {
                if ($provider->component == $component) {
                    $componentexport->providers[] = $provider;
                }
            }
            $componentexport->colspan = $colspan;
            $componentsexport[] = $componentexport;
        }

        $context['processors'] = array_values($processors);
        $context['components'] = $componentsexport;

        return $this->render_from_template('message/default_notification_preferences', $context);
    }

    /**
     * Display the interface for notification preferences
     *
     * @param object $user instance of a user
     * @return string The text to render
     */
    public function render_user_notification_preferences($user) {
        $processors = get_message_processors();
        $providers = message_get_providers_for_user($user->id);

        $preferences = \core_message\api::get_all_message_preferences($processors, $providers, $user);
        $notificationlistoutput = new \core_message\output\preferences\notification_list($processors, $providers,
            $preferences, $user);
        return $this->render_from_template('message/notification_preferences',
            $notificationlistoutput->export_for_template($this));
    }

    /**
     * Display the interface for message preferences
     *
     * @param object $user instance of a user
     * @return string The text to render
     */
    public function render_user_message_preferences($user) {
        global $CFG;

        // Filter out enabled, available system_configured and user_configured processors only.
        $readyprocessors = array_filter(get_message_processors(), function($processor) {
            return $processor->enabled &&
                $processor->configured &&
                $processor->object->is_user_configured() &&
                // Filter out processors that don't have and message preferences to configure.
                $processor->object->has_message_preferences();
        });

        $providers = array_filter(message_get_providers_for_user($user->id), function($provider) {
            return $provider->component === 'moodle';
        });
        $preferences = \core_message\api::get_all_message_preferences($readyprocessors, $providers, $user);
        $notificationlistoutput = new \core_message\output\preferences\message_notification_list($readyprocessors,
            $providers, $preferences, $user);
        $context = $notificationlistoutput->export_for_template($this);

        // Get the privacy settings options for being messaged.
        $privacysetting = \core_message\api::get_user_privacy_messaging_preference($user->id);
        $choices = array();
        $choices[] = [
            'value' => \core_message\api::MESSAGE_PRIVACY_ONLYCONTACTS,
            'text' => get_string('contactableprivacy_onlycontacts', 'message'),
            'checked' => ($privacysetting == \core_message\api::MESSAGE_PRIVACY_ONLYCONTACTS)
        ];
        $choices[] = [
            'value' => \core_message\api::MESSAGE_PRIVACY_COURSEMEMBER,
            'text' => get_string('contactableprivacy_coursemember', 'message'),
            'checked' => ($privacysetting == \core_message\api::MESSAGE_PRIVACY_COURSEMEMBER)
        ];
        if (!empty($CFG->messagingallusers)) {
            // Add the MESSAGE_PRIVACY_SITE option when site-wide messaging between users is enabled.
            $choices[] = [
                'value' => \core_message\api::MESSAGE_PRIVACY_SITE,
                'text' => get_string('contactableprivacy_site', 'message'),
                'checked' => ($privacysetting == \core_message\api::MESSAGE_PRIVACY_SITE)
            ];
        }
        $context['privacychoices'] = $choices;

        return $this->render_from_template('message/message_preferences', $context);
    }
}
