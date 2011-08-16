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
 * Messaging libraries
 *
 * @package    message
 * @copyright  2011 Lancaster University Network Services Limited
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * message Renderer
 *
 * Class for rendering various message objects
 *
 * @package    message
 * @copyright  2011 Lancaster University Network Services Limited
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_message_renderer extends plugin_renderer_base {

    /**
     * Display the interface to manage message outputs
     *
     * @param   mixed   $processors array of objects containing message processors
     * @return  string              The text to render
     */
    public function manage_messageoutputs($processors) {
        global $CFG;
        // Display the current workflows
        $table = new html_table();
        $table->attributes['class'] = 'generaltable';
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
                    array('src'   => $this->output->pix_url('i/hide'),
                          'class' => 'icon',
                          'title' => get_string('outputenabled', 'message'),
                          'alt'   => get_string('outputenabled', 'message'),
                    )
                ));
            } else {
                $name->attributes['class'] = 'dimmed_text';
                $url = new moodle_url('/admin/message.php', array('enable' => $processor->id, 'sesskey' => sesskey()));
                $enable->text = html_writer::link($url, html_writer::empty_tag('img',
                    array('src'   => $this->output->pix_url('i/show'),
                          'class' => 'icon',
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
     * @param   mixed   $processors  array of objects containing message processors
     * @param   mixed   $providers   array of objects containing message providers
     * @param   mixed   $preferences array of objects containing current preferences
     * @return  string               The text to render
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
        // Generate the matrix of settings for each provider and processor
        foreach ($providers as $provider) {
            $row = new html_table_row();
            $row->attributes['class'] = 'defaultmessageoutputs';
            $row->cells = array();

            // Provider Name
            $providername = get_string('messageprovider:'.$provider->name, $provider->component);
            $row->cells[] = new html_table_cell($providername);

            // Settings for each processor
            foreach ($processors as $processor) {
                $cellcontent = '';
                foreach (array('permitted', 'loggedin', 'loggedoff') as $setting) {
                    // pepare element and preference names
                    $elementname = $provider->component.'_'.$provider->name.'_'.$setting.'['.$processor->name.']';
                    $preferencebase = $provider->component.'_'.$provider->name.'_'.$setting;
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
                        if (array_key_exists($preference, $preferences)) {
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
     * Display the interface for messaging options
     *
     * @param   mixed   $processors         array of objects containing message processors
     * @param   mixed   $providers          array of objects containing message providers
     * @param   mixed   $preferences        array of objects containing current preferences
     * @param   mixed   $defaultpreferences array of objects containing site default preferences
     * $param   boolean $notificationsdisabled indicates whether the user's "emailstop" flag is
     *                                      set so shouldn't receive any non-forced notifications
     * @return  string                      The text to render
     */
    public function manage_messagingoptions($processors, $providers, $preferences, $defaultpreferences, $notificationsdisabled = false) {
        // Filter out enabled, available system_configured and user_configured processors only.
        $readyprocessors = array_filter($processors, create_function('$a', 'return $a->enabled && $a->configured && $a->object->is_user_configured();'));

        // Start the form.  We're not using mform here because of our special formatting needs ...
        $output = html_writer::start_tag('form', array('method'=>'post', 'class' => 'mform'));
        $output .= html_writer::empty_tag('input', array('type'=>'hidden', 'name'=>'sesskey', 'value'=>sesskey()));

        /// Settings table...
        $output .= html_writer::start_tag('fieldset', array('id' => 'providers', 'class' => 'clearfix'));
        $output .= html_writer::nonempty_tag('legend', get_string('providers_config', 'message'), array('class' => 'ftoggler'));

        // Display the messging options table
        $table = new html_table();
        $table->attributes['class'] = 'generaltable';
        $table->data        = array();
        $table->head        = array('');

        foreach ($readyprocessors as $processor) {
            $table->head[]  = get_string('pluginname', 'message_'.$processor->name);
        }

        $number_procs = count($processors);
        // Populate the table with rows
        foreach ( $providers as $provider) {
            $preferencebase = $provider->component.'_'.$provider->name;

            $headerrow = new html_table_row();
            $providername = get_string('messageprovider:'.$provider->name, $provider->component);
            $providercell = new html_table_cell($providername);
            $providercell->header = true;
            $providercell->colspan = $number_procs + 1;
            $providercell->attributes['class'] = 'c0';
            $headerrow->cells = array($providercell);
            $table->data[] = $headerrow;

            foreach (array('loggedin', 'loggedoff') as $state) {
                $optionrow = new html_table_row();
                $optionname = new html_table_cell(get_string($state.'description', 'message'));
                $optionname->attributes['class'] = 'c0';
                $optionrow->cells = array($optionname);
                foreach ($readyprocessors as $processor) {
                    // determine the default setting
                    $permitted = MESSAGE_DEFAULT_PERMITTED;
                    $defaultpreference = $processor->name.'_provider_'.$preferencebase.'_permitted';
                    if (isset($defaultpreferences->{$defaultpreference})) {
                        $permitted = $defaultpreferences->{$defaultpreference};
                    }
                    // If settings are disallowed, just display the message that
                    // the setting is not permitted, if not use user settings or
                    // force them.
                    if ($permitted == 'disallowed') {
                        if ($state == 'loggedoff') {
                            // skip if we are rendering the second line
                            continue;
                        }
                        $cellcontent = html_writer::nonempty_tag('div', get_string('notpermitted', 'message'), array('class' => 'dimmed_text'));
                        $optioncell = new html_table_cell($cellcontent);
                        $optioncell->rowspan = 2;
                        $optioncell->attributes['class'] = 'disallowed';
                    } else {
                        // determine user preferences and use then unless we force
                        // the preferences.
                        $disabled = array();
                        if ($permitted == 'forced') {
                            $checked = true;
                            $disabled['disabled'] = 1;
                        } else {
                            $checked = false;
                            if ($notificationsdisabled) {
                                $disabled['disabled'] = 1;
                            }
                            // See if user has touched this preference
                            if (isset($preferences->{$preferencebase.'_'.$state})) {
                                // User have some preferneces for this state in the database, use them
                                $checked = isset($preferences->{$preferencebase.'_'.$state}[$processor->name]);
                            } else {
                                // User has not set this preference yet, using site default preferences set by admin
                                $defaultpreference = 'message_provider_'.$preferencebase.'_'.$state;
                                if (isset($defaultpreferences->{$defaultpreference})) {
                                    $checked = (int)in_array($processor->name, explode(',', $defaultpreferences->{$defaultpreference}));
                                }
                            }
                        }
                        $elementname = $preferencebase.'_'.$state.'['.$processor->name.']';
                        // prepare language bits
                        $processorname = get_string('pluginname', 'message_'.$processor->name);
                        $statename = get_string($state, 'message');
                        $labelparams = array(
                            'provider'  => $providername,
                            'processor' => $processorname,
                            'state'     => $statename
                        );
                        $label = get_string('sendingviawhen', 'message', $labelparams);
                        $cellcontent = html_writer::label($label, $elementname, true, array('class' => 'accesshide'));
                        $cellcontent .= html_writer::checkbox($elementname, 1, $checked, '', array_merge(array('id' => $elementname, 'class' => 'notificationpreference'), $disabled));
                        $optioncell = new html_table_cell($cellcontent);
                        $optioncell->attributes['class'] = 'mdl-align';
                    }
                    $optionrow->cells[] = $optioncell;
                }
                $table->data[] = $optionrow;
            }
        }
        $output .= html_writer::start_tag('div');
        $output .= html_writer::table($table);
        $output .= html_writer::end_tag('div');

        $disableallcheckbox = $this->output->help_icon('disableall', 'message') . get_string('disableall', 'message') . html_writer::checkbox('disableall', 1, $notificationsdisabled, '', array('class'=>'disableallcheckbox'));
        $output .= html_writer::nonempty_tag('div', $disableallcheckbox, array('class'=>'disableall'));

        $output .= html_writer::end_tag('fieldset');

        foreach ($processors as $processor) {
            if (($processorconfigform = $processor->object->config_form($preferences)) && $processor->enabled) {
                $output .= html_writer::start_tag('fieldset', array('id' => 'messageprocessor_'.$processor->name, 'class' => 'clearfix'));
                $output .= html_writer::nonempty_tag('legend', get_string('pluginname', 'message_'.$processor->name), array('class' => 'ftoggler'));
                $output .= html_writer::start_tag('div');
                $output .= $processorconfigform;
                $output .= html_writer::end_tag('div');
                $output .= html_writer::end_tag('fieldset');
            }
        }

        $output .= html_writer::start_tag('fieldset', array('id' => 'messageprocessor_general', 'class' => 'clearfix'));
        $output .= html_writer::nonempty_tag('legend', get_string('generalsettings','admin'), array('class' => 'ftoggler'));
        $output .= html_writer::start_tag('div');
        $output .= get_string('blocknoncontacts', 'message').': ';
        $output .= html_writer::checkbox('blocknoncontacts', 1, $preferences->blocknoncontacts, '');
        $output .= html_writer::end_tag('div');
        $output .= html_writer::end_tag('fieldset');
        $output .= html_writer::start_tag('div', array('class' => 'mdl-align'));
        $output .= html_writer::empty_tag('input', array('type' => 'submit', 'value' => get_string('updatemyprofile'), 'class' => 'form-submit'));
        $output .= html_writer::end_tag('div');
        $output .= html_writer::end_tag('form');
        return $output;
    }

}
