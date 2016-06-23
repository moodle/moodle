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
     * Display the interface to manage message outputs
     *
     * @param  array  $processors array of objects containing message processors
     * @return string The text to render
     */
    public function manage_messageoutputs($processors) {
        global $CFG;
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
            'displayname', 'availability', 'settings',
        );

        foreach ($processors as $processor) {
            $row = new html_table_row();
            $row->attributes['class'] = 'messageoutputs';

            // Name
            $name = new html_table_cell(get_string('pluginname', 'message_'.$processor->name));

            // Enable
            $enable = new html_table_cell();
            $enable->attributes['class'] = 'mdl-align';
            if (!$processor->available) {
                $enable->text = html_writer::nonempty_tag('span', get_string('outputnotavailable', 'message'), array('class' => 'error'));
            } else if (!$processor->configured) {
                $enable->text = html_writer::nonempty_tag('span', get_string('outputnotconfigured', 'message'), array('class' => 'error'));
            } else if ($processor->enabled) {
                $url = new moodle_url('/admin/message.php', array('disable' => $processor->id, 'sesskey' => sesskey()));
                $enable->text = html_writer::link($url, html_writer::empty_tag('img',
                    array('src'   => $this->output->pix_url('t/hide'),
                          'class' => 'iconsmall',
                          'title' => get_string('outputenabled', 'message'),
                          'alt'   => get_string('outputenabled', 'message'),
                    )
                ));
            } else {
                $row->attributes['class'] = 'dimmed_text';
                $url = new moodle_url('/admin/message.php', array('enable' => $processor->id, 'sesskey' => sesskey()));
                $enable->text = html_writer::link($url, html_writer::empty_tag('img',
                    array('src'   => $this->output->pix_url('t/show'),
                          'class' => 'iconsmall',
                          'title' => get_string('outputdisabled', 'message'),
                          'alt'   => get_string('outputdisabled', 'message'),
                    )
                ));
            }
            // Settings
            $settings = new html_table_cell();
            if ($processor->available && $processor->hassettings) {
                $settingsurl = new moodle_url('settings.php', array('section' => 'messagesetting'.$processor->name));
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
     * @param  array $preferences array of objects containing current preferences
     * @return string The text to render
     */
    public function manage_defaultmessageoutputs($processors, $providers, $preferences) {
        global $CFG;

        // Prepare list of options for dropdown menu
        $options = array();
        foreach (array('disallowed', 'permitted', 'forced') as $setting) {
            $options[$setting] = get_string($setting, 'message');
        }

        $output = html_writer::start_tag('form', array('id'=>'defaultmessageoutputs', 'method'=>'post'));
        $output .= html_writer::empty_tag('input', array('type'=>'hidden', 'name'=>'sesskey', 'value'=>sesskey()));

        // Display users outputs table
        $table = new html_table();
        $table->attributes['class'] = 'generaltable';
        $table->data        = array();
        $table->head        = array('');

        // Populate the header row
        foreach ($processors as $processor) {
            $table->head[]  = get_string('pluginname', 'message_'.$processor->name);
        }
        // Add enable/disable to head
        $table->head[] = get_string('enabled', 'core_message');

        // Generate the matrix of settings for each provider and processor
        foreach ($providers as $provider) {
            $row = new html_table_row();
            $row->attributes['class'] = 'defaultmessageoutputs';
            $row->cells = array();

            // Provider Name
            $providername = get_string('messageprovider:'.$provider->name, $provider->component);
            $row->cells[] = new html_table_cell($providername);
            $providersettingprefix = $provider->component.'_'.$provider->name.'_';
            $disableprovidersetting = $providersettingprefix.'disable';
            $providerdisabled = !empty($preferences->$disableprovidersetting);
            // Settings for each processor
            foreach ($processors as $processor) {
                $cellcontent = '';
                foreach (array('permitted', 'loggedin', 'loggedoff') as $setting) {
                    // pepare element and preference names
                    $elementname = $providersettingprefix.$setting.'['.$processor->name.']';
                    $preferencebase = $providersettingprefix.$setting;
                    // prepare language bits
                    $processorname = get_string('pluginname', 'message_'.$processor->name);
                    $statename = get_string($setting, 'message');
                    $labelparams = array(
                        'provider'  => $providername,
                        'processor' => $processorname,
                        'state'     => $statename
                    );
                    if ($setting == 'permitted') {
                        $label = get_string('sendingvia', 'message', $labelparams);
                        // determine the current setting or use default
                        $select = MESSAGE_DEFAULT_PERMITTED;
                        $preference = $processor->name.'_provider_'.$preferencebase;
                        if ($providerdisabled) {
                            $select = MESSAGE_DISALLOWED;
                        } else if (array_key_exists($preference, $preferences)) {
                            $select = $preferences->{$preference};
                        }
                        // dropdown menu
                        $cellcontent = html_writer::label($label, $elementname, true, array('class' => 'accesshide'));
                        $cellcontent .= html_writer::select($options, $elementname, $select, false, array('id' => $elementname));
                        $cellcontent .= html_writer::tag('div', get_string('defaults', 'message'));
                    } else {
                        $label = get_string('sendingviawhen', 'message', $labelparams);
                        // determine the current setting based on the 'permitted' setting above
                        $checked = false;
                        if ($select == 'forced') {
                            $checked = true;
                        } else if ($select == 'permitted') {
                            $preference = 'message_provider_'.$preferencebase;
                            if (array_key_exists($preference, $preferences)) {
                                $checked = (int)in_array($processor->name, explode(',', $preferences->{$preference}));
                            }
                        }
                        // generate content
                        $cellcontent .= html_writer::start_tag('div');
                        $cellcontent .= html_writer::label($label, $elementname, true, array('class' => 'accesshide'));
                        $cellcontent .= html_writer::checkbox($elementname, 1, $checked, '', array('id' => $elementname));
                        $cellcontent .= $statename;
                        $cellcontent .= html_writer::end_tag('div');
                    }
                }
                $row->cells[] = new html_table_cell($cellcontent);
            }
            $disableprovider = html_writer::checkbox($disableprovidersetting, 1, !$providerdisabled, '',
                    array('id' => $disableprovidersetting, 'class' => 'messagedisable'));
            $disableprovider = html_writer::tag('div', $disableprovider);
            $row->cells[] = new html_table_cell($disableprovider);
            $table->data[] = $row;
        }

        $output .= html_writer::table($table);
        $output .= html_writer::start_tag('div', array('class' => 'form-buttons'));
        $output .= html_writer::empty_tag('input', array('type' => 'submit', 'value' => get_string('savechanges','admin'), 'class' => 'form-submit'));
        $output .= html_writer::end_tag('div');
        $output .= html_writer::end_tag('form');
        return $output;
    }

    /**
     * Get the base key prefix for the given provider.
     *
     * @param stdClass message provider
     * @return string
     */
    private function get_preference_base($provider) {
        return $provider->component.'_'.$provider->name;
    }

    /**
     * Get the display name for the given provider.
     *
     * @param stdClass $provider message provider
     * @return string
     */
    private function get_provider_display_name($provider) {
        return get_string('messageprovider:'.$provider->name, $provider->component);
    }

    /**
     * Get the preferences for the given user.
     *
     * @param array $processors list of message processors
     * @param array $providers list of message providers
     * @param stdClass $user user
     * @return stdClass
     */
    private function get_all_preferences($processors, $providers, $user) {
        $preferences = new stdClass();
        $preferences->userdefaultemail = $user->email;//may be displayed by the email processor

        /// Get providers preferences
        foreach ($providers as $provider) {
            foreach (array('loggedin', 'loggedoff') as $state) {
                $linepref = get_user_preferences('message_provider_'.$provider->component.'_'.$provider->name.'_'.$state, '', $user->id);
                if ($linepref == ''){
                    continue;
                }
                $lineprefarray = explode(',', $linepref);
                $preferences->{$provider->component.'_'.$provider->name.'_'.$state} = array();
                foreach ($lineprefarray as $pref) {
                    $preferences->{$provider->component.'_'.$provider->name.'_'.$state}[$pref] = 1;
                }
            }
        }

        /// For every processors put its options on the form (need to get function from processor's lib.php)
        foreach ($processors as $processor) {
            $processor->object->load_data($preferences, $user->id);
        }

        //load general messaging preferences
        $preferences->blocknoncontacts  =  get_user_preferences( 'message_blocknoncontacts', '', $user->id);
        $preferences->beepnewmessage    =  get_user_preferences( 'message_beepnewmessage', '', $user->id);
        $preferences->mailformat        =  $user->mailformat;
        $preferences->mailcharset       =  get_user_preferences( 'mailcharset', '', $user->id);

        return $preferences;
    }

    /**
     * Check if the given preference is enabled or not.
     *
     * @param string $name preference name
     * @param stdClass $processor the processors for the preference
     * @param stdClass $preferences the preferences config
     * @return bool
     */
    private function is_preference_enabled($name, $processor, $preferences) {
        $defaultpreferences = get_message_output_default_preferences();

        $checked = false;
        // See if user has touched this preference
        if (isset($preferences->{$name})) {
            // User have some preferneces for this state in the database, use them
            $checked = isset($preferences->{$name}[$processor->name]);
        } else {
            // User has not set this preference yet, using site default preferences set by admin
            $defaultpreference = 'message_provider_'.$name;
            if (isset($defaultpreferences->{$defaultpreference})) {
                $checked = (int)in_array($processor->name, explode(',', $defaultpreferences->{$defaultpreference}));
            }
        }

        return $checked;
    }

    /**
     * Build the template context for the given processor.
     *
     * @param stdClass $processor
     * @param stdClass $provider
     * @param stdClass $preferences the preferences config
     * @return array
     */
    private function get_processor_context($processor, $provider, $preferences) {
        $processorcontext = [
            'displayname' => get_string('pluginname', 'message_'.$processor->name),
            'name' => $processor->name,
            'locked' => false,
            'radioname' => strtolower(str_replace(" ", "-", $processor->name)),
            'states' => []
        ];
        // determine the default setting
        $preferencebase = $this->get_preference_base($provider);
        $permitted = MESSAGE_DEFAULT_PERMITTED;
        $defaultpreferences = get_message_output_default_preferences();
        $defaultpreference = $processor->name.'_provider_'.$preferencebase.'_permitted';
        if (isset($defaultpreferences->{$defaultpreference})) {
            $permitted = $defaultpreferences->{$defaultpreference};
        }
        // If settings are disallowed or forced, just display the
        // corresponding message, if not use user settings.
        if ($permitted == 'disallowed') {
            $processorcontext['locked'] = true;
            $processorcontext['lockedmessage'] = get_string('disallowed', 'message');
        } else if ($permitted == 'forced') {
            $processorcontext['locked'] = true;
            $processorcontext['lockedmessage'] = get_string('forced', 'message');
        } else {
            $statescontext = [
                'loggedin' => [
                    'name' => 'loggedin',
                    'displayname' => get_string('loggedindescription', 'message'),
                    'checked' => $this->is_preference_enabled($preferencebase.'_loggedin', $processor, $preferences),
                    'iconurl' => $this->pix_url('i/completion-auto-y')->out(),
                ],
                'loggedoff' => [
                    'name' => 'loggedoff',
                    'displayname' => get_string('loggedoffdescription', 'message'),
                    'checked' => $this->is_preference_enabled($preferencebase.'_loggedoff', $processor, $preferences),
                    'iconurl' => $this->pix_url('i/completion-auto-n')->out(),
                ],
                'both' => [
                    'name' => 'both',
                    'displayname' => get_string('always'),
                    'checked' => false,
                    'iconurl' => $this->pix_url('i/completion-auto-pass')->out(),
                ],
                'none' => [
                    'name' => 'none',
                    'displayname' => get_string('never'),
                    'checked' => false,
                    'iconurl' => $this->pix_url('i/completion-auto-fail')->out(),
                ],
            ];

            if ($statescontext['loggedin']['checked'] && $statescontext['loggedoff']['checked']) {
                $statescontext['both']['checked'] = true;
                $statescontext['loggedin']['checked'] = false;
                $statescontext['loggedoff']['checked'] = false;
            } else if (!$statescontext['loggedin']['checked'] && !$statescontext['loggedoff']['checked']) {
                $statescontext['none']['checked'] = true;
            }

            $processorcontext['states'] = array_values($statescontext);
        }

        return $processorcontext;
    }

    /**
     * Build the template context for the given component.
     *
     * @param string $component the component name
     * @param stdClass $processors an array of processors
     * @param stdClass $providers and array of providers
     * @param stdClass $preferences the preferences config
     * @return array
     */
    private function get_component_context($component, $processors, $providers, $preferences) {
        $defaultpreferences = get_message_output_default_preferences();

        if ($component != 'moodle') {
            $componentname = get_string('pluginname', $component);
        } else {
            $componentname = get_string('coresystem');
        }
        $componentcontext = [
            'displayname' => $componentname,
            'processornames' => [],
            'notifications' => [],
        ];

        foreach ($processors as $processor) {
            $componentcontext['processornames'][] = get_string('pluginname', 'message_'.$processor->name);
        }

        foreach ($providers as $provider) {
            $preferencebase = $this->get_preference_base($provider);
            // If provider component is not same or provider disabled then don't show.
            if (($provider->component != $component) ||
                    (!empty($defaultpreferences->{$preferencebase.'_disable'}))) {
                continue;
            }

            $notificationcontext = [
                'displayname' => $this->get_provider_display_name($provider),
                'preferencekey' => 'message_provider_'.$preferencebase,
                'processors' => [],
            ];

            foreach ($processors as $processor) {
                $notificationcontext['processors'][] = $this->get_processor_context($processor, $provider, $preferences);
            }

            $componentcontext['notifications'][] = $notificationcontext;
        }

        return $componentcontext;
    }

    /**
     * Build the template context for the message preferences page.
     *
     * @param stdClass $processors an array of processors
     * @param stdClass $providers and array of providers
     * @param stdClass $preferences the preferences config
     * @param stdClass $user the current user
     * @return array
     */
    private function get_preferences_context($processors, $providers, $preferences, $user) {
        foreach($providers as $provider) {
            if($provider->component != 'moodle') {
                $components[] = $provider->component;
            }
        }

        // Lets arrange by components so that core settings (moodle) appear as the first table.
        $components = array_unique($components);
        asort($components);
        array_unshift($components, 'moodle'); // pop it in front! phew!
        asort($providers);

        $context = [];

        foreach ($components as $component) {
            $context['components'][] = $this->get_component_context($component, $processors, $providers, $preferences);
        }

        $context['userid'] = $user->id;
        $context['disableall'] = $user->emailstop;

        return $context;
    }

    /**
     * Display the interface for messaging options
     *
     * @param object $user instance of a user
     * @return string The text to render
     */
    public function render_user_preferences($user) {
        // Filter out enabled, available system_configured and user_configured processors only.
        $readyprocessors = array_filter(get_message_processors(), create_function('$a', 'return $a->enabled && $a->configured && $a->object->is_user_configured();'));

        $providers = message_get_providers_for_user($user->id);
        $preferences = $this->get_all_preferences($readyprocessors, $providers, $user);
        $preferencescontext = $this->get_preferences_context($readyprocessors, $providers, $preferences, $user);

        $output = $this->render_from_template('message/preferences_notifications_list', $preferencescontext);

        $processorscontext = [
            'userid' => $user->id,
            'processors' => [],
        ];

        foreach ($readyprocessors as $processor) {
            $formhtml = $processor->object->config_form($preferences);

            if (!$formhtml) {
                continue;
            }

            $processorscontext['processors'][] = [
                'displayname' => get_string('pluginname', 'message_'.$processor->name),
                'name' => $processor->name,
                'formhtml' => $formhtml,
            ];
        }

        $output .= $this->render_from_template('message/preferences_processors', $processorscontext);

        $generalsettingscontext = [
            'userid' => $user->id,
            'beepnewmessage' => $preferences->beepnewmessage,
            'blocknoncontacts' => $preferences->blocknoncontacts,
            'disableall' => $user->emailstop,
            'disableallhelpicon' => $this->output->help_icon('disableall', 'message'),
        ];

        $output .= $this->render_from_template('message/preferences_general_settings', $generalsettingscontext);

        return $output;
    }
}
