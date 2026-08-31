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

namespace aiprovider_anthropic\form;

use aiprovider_anthropic\aimodel\custommodel;
use aiprovider_anthropic\helper;
use core_ai\form\action_settings_form;

/**
 * Base action settings form for the Anthropic Claude provider.
 *
 * @package    aiprovider_anthropic
 * @copyright  2026 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class action_form extends action_settings_form {
    /** @var array Action configuration. */
    protected array $actionconfig;
    /** @var string|null Return URL. */
    protected ?string $returnurl;
    /** @var string Action name. */
    protected string $actionname;
    /** @var string Action class. */
    protected string $action;
    /** @var int Provider ID. */
    protected int $providerid;
    /** @var string Provider name. */
    protected string $providername;

    #[\Override]
    protected function definition(): void {
        $mform = $this->_form;
        $this->actionconfig = $this->_customdata['actionconfig']['settings'] ?? [];
        $this->returnurl = $this->_customdata['returnurl'] ?? null;
        $this->actionname = $this->_customdata['actionname'];
        $this->action = $this->_customdata['action'];
        $this->providerid = $this->_customdata['providerid'] ?? 0;
        $this->providername = $this->_customdata['providername'] ?? 'aiprovider_anthropic';

        $mform->addElement('header', 'generalsettingsheader', get_string('general', 'core'));
    }

    #[\Override]
    public function get_data(): ?\stdClass {
        // The parent resolves the selected template (or the entered custom model name) into
        // the model that gets stored, so only the helper fields need clearing here.
        $data = parent::get_data();

        if (!empty($data)) {
            unset($data->modeltemplate, $data->custommodel);
        }

        return $data;
    }

    #[\Override]
    public function validation($data, $files): array {
        $errors = parent::validation($data, $files);

        if (($data['modeltemplate'] ?? '') === custommodel::MODEL_NAME && trim($data['custommodel'] ?? '') === '') {
            $errors['custommodel'] = get_string('required');
        }

        return $errors;
    }

    #[\Override]
    public function get_defaults(): array {
        $data = parent::get_defaults();

        unset(
            $data['modeltemplate'],
            $data['custommodel'],
        );

        return $data;
    }

    /**
     * Add the model selection fields to the form.
     *
     * The selector holds a template value rather than the model itself, so that "Custom"
     * can stand in for a model name typed by the admin. The stored model always comes from
     * the hidden model field, which the parent form populates from the selection on submit.
     */
    protected function add_model_fields(): void {
        global $PAGE;
        $PAGE->requires->js_call_amd('aiprovider_anthropic/modelchooser', 'init');
        $mform = $this->_form;

        // A stored model that is not one of the bundled models was entered through the
        // "Custom" option, so the selector needs to come back up on "Custom".
        $modellist = $this->get_model_list();
        $storedmodel = $this->actionconfig['model'] ?? helper::get_default_model();
        $iscustom = !array_key_exists($storedmodel, $modellist);
        $defaulttemplate = $iscustom ? custommodel::MODEL_NAME : $storedmodel;

        $mform->addElement(
            'select',
            'modeltemplate',
            get_string("action:{$this->actionname}:model", 'aiprovider_anthropic'),
            $modellist,
            ['data-modelchooser-field' => 'selector'],
        );
        $mform->setType('modeltemplate', PARAM_TEXT);
        $mform->addRule('modeltemplate', null, 'required', null, 'client');
        $mform->setDefault('modeltemplate', $defaulttemplate);
        $mform->addHelpButton('modeltemplate', "action:{$this->actionname}:model", 'aiprovider_anthropic');

        $mform->addElement('hidden', 'model', $storedmodel);
        $mform->setType('model', PARAM_TEXT);

        $mform->addElement('text', 'custommodel', get_string('custom_model_name', 'aiprovider_anthropic'));
        $mform->setType('custommodel', PARAM_TEXT);
        $mform->setDefault('custommodel', $iscustom ? $storedmodel : '');
        $mform->addHelpButton('custommodel', 'custom_model_name', 'aiprovider_anthropic');
        $mform->hideIf('custommodel', 'modeltemplate', 'neq', custommodel::MODEL_NAME);

        // Hidden button the modelchooser JS clicks to resubmit the form when the model
        // changes, so the per-model settings added by the after_ai_action_settings_form_hook
        // (see hook_listener::set_model_form_definition_for_aiprovider_anthropic()) refresh
        // to match the newly selected model.
        $mform->registerNoSubmitButton('updateactionsettings');
        $mform->addElement(
            'submit',
            'updateactionsettings',
            'updateactionsettings',
            ['data-modelchooser-field' => 'updateButton', 'class' => 'd-none'],
        );
    }

    /**
     * Get the list of available models for the dropdown.
     *
     * @return array Model name => display name, with the "Custom" option last.
     */
    protected function get_model_list(): array {
        $models = [];
        $custom = [];
        foreach (helper::get_model_classes() as $class) {
            $model = new $class();
            if ($model instanceof custommodel) {
                $custom[$model->get_model_name()] = $model->get_model_display_name();
                continue;
            }
            $models[$model->get_model_name()] = $model->get_model_display_name();
        }
        return $models + $custom;
    }
}
