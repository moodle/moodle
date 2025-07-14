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

namespace aiprovider_ollama\form;

use aiprovider_ollama\helper;
use core_ai\form\action_settings_form;

/**
 * Base action settings form for Ollama provider.
 *
 * @package    aiprovider_ollama
 * @copyright  2025 Huong Nguyen <huongnv13@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class action_form extends action_settings_form {
    /**
     * @var array Action configuration.
     */
    protected array $actionconfig;
    /**
     * @var string|null Return URL.
     */
    protected ?string $returnurl;
    /**
     * @var string Action name.
     */
    protected string $actionname;
    /**
     * @var string Action class.
     */
    protected string $action;
    /**
     * @var int Provider ID.
     */
    protected int $providerid;
    /**
     * @var string Provider name.
     */
    protected string $providername;
    /**
     * @var array Stored model settings.
     */
    protected array $storedmodelsettings;

    #[\Override]
    protected function definition(): void {
        $mform = $this->_form;
        $this->actionconfig = $this->_customdata['actionconfig']['settings'] ?? [];
        $this->returnurl = $this->_customdata['returnurl'] ?? null;
        $this->actionname = $this->_customdata['actionname'];
        $this->action = $this->_customdata['action'];
        $this->providerid = $this->_customdata['providerid'] ?? 0;
        $this->providername = $this->_customdata['providername'] ?? 'aiprovider_ollama';
        $this->storedmodelsettings = $this->_customdata['actionconfig']['modelsettings'] ?? [];

        $mform->addElement('header', 'generalsettingsheader', get_string('general', 'core'));
    }

    #[\Override]
    public function set_data($data): void {
        if (!empty($data['modelextraparams'])) {
            $data['modelextraparams'] = json_encode(json_decode($data['modelextraparams']), JSON_PRETTY_PRINT);
        }
        parent::set_data($data);
    }

    #[\Override]
    public function get_data(): ?\stdClass {
        $data = parent::get_data();

        if (isset($data->model)) {
            if ($data->modeltemplate === 'custom') {
                $data->modelsettings['custom']['modelextraparams'] = $data->modelextraparams;
            } else {
                $modelclass = helper::get_model_class($data->model);
                if ($modelclass) {
                    if ($modelclass->has_model_settings()) {
                        $modelsettings = array_keys($modelclass->get_model_settings());
                        // Process the model settings.
                        $modeldata = [];
                        foreach ($data as $key => $value) {
                            if (in_array($key, $modelsettings)) {
                                $modeldata[$key] = $value;
                            }
                        }
                        if (!empty($modeldata)) {
                            $data->modelsettings[$data->model] = $modeldata;
                        }
                    }
                }
            }
        }

        if (!empty($data)) {
            unset($data->custommodel);
            unset($data->modeltemplate);
            // Unset any false-y values.
            $data = (object) array_filter((array) $data);
        }

        return $data;
    }

    #[\Override]
    public function validation($data, $files): array {
        $errors = parent::validation($data, $files);

        // Validate the extra parameters.
        if (!empty($data['modelextraparams'])) {
            json_decode($data['modelextraparams']);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $errors['modelextraparams'] = get_string('invalidjson', 'aiprovider_ollama');
            }
        }

        // Validate the model.
        if ($data['modeltemplate'] === 'custom' && empty($data['custommodel'])) {
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
            $data['modelextraparams'],
        );

        return $data;
    }

    /**
     * Add model fields to the form.
     *
     * @param int $modeltype Model type.
     */
    protected function add_model_fields(int $modeltype): void {
        global $PAGE;
        $PAGE->requires->js_call_amd('aiprovider_ollama/modelchooser', 'init');
        $mform = $this->_form;

        // Determine which model to use as the default.
        if (!empty($this->actionconfig['model']) &&
                (!array_key_exists($this->actionconfig['model'], $this->get_model_list($modeltype)) ||
                !empty($this->actionconfig['modelextraparams']))) {
            $defaultmodel = 'custom';
        } else {
            $defaultmodel = $this->actionconfig['model'] ?? 'llama3.3';
        }

        // Get this model's stored values to assist model switching and value population in JS.
        $modeltemplate = optional_param('modeltemplate', $defaultmodel, PARAM_TEXT);
        if (isset($this->storedmodelsettings[$modeltemplate])) {
            $this->storedmodelsettings = [$modeltemplate => $this->storedmodelsettings[$modeltemplate]];
        }

        // Model chooser.
        $mform->addElement(
            'select',
            'modeltemplate',
            get_string("action:{$this->actionname}:model", 'aiprovider_ollama'),
            $this->get_model_list($modeltype),
            ['data-modelchooser-field' => 'selector', 'data-storedmodelsettings' => json_encode($this->storedmodelsettings)],
        );
        $mform->setType('modeltemplate', PARAM_TEXT);
        $mform->addRule('modeltemplate', null, 'required', null, 'client');
        $mform->setDefault('modeltemplate', $defaultmodel);
        $mform->addHelpButton('modeltemplate', "action:{$this->actionname}:model", 'aiprovider_ollama');

        $mform->addElement('hidden', 'model', $this->actionconfig['model'] ?? 'llama3.3');
        $mform->setType('model', PARAM_TEXT);

        $mform->addElement('text', 'custommodel', get_string('custom_model_name', 'aiprovider_ollama'));
        $mform->setType('custommodel', PARAM_TEXT);
        $mform->setDefault('custommodel', $this->actionconfig['model'] ?? '');
        $mform->hideIf('custommodel', 'modeltemplate', 'neq', 'custom');

        $mform->registerNoSubmitButton('updateactionsettings');
        $mform->addElement(
            'submit',
            'updateactionsettings',
            'updateactionsettings',
            ['data-modelchooser-field' => 'updateButton', 'class' => 'd-none']
        );
    }

    /**
     * Get the list of models.
     *
     * @param int $modeltype Model type.
     * @return array List of models.
     */
    protected function get_model_list(int $modeltype): array {
        $models = [];
        $models['custom'] = get_string('custom', 'core_form');
        foreach (helper::get_model_classes() as $class) {
            $model = new $class();
            if ($model->model_type() == $modeltype) {
                $models[$model->get_model_name()] = $model->get_model_display_name();
            }
        }
        return $models;
    }
}
