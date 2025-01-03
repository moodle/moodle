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

namespace local_ai_manager;

use local_ai_manager\local\config_manager;
use local_ai_manager\local\connector_factory;
use local_ai_manager\local\tenant;
use local_ai_manager\local\userinfo;
use stdClass;

/**
 * Instance class for a connector instance.
 *
 * @package    local_ai_manager
 * @copyright  2024 ISB Bayern
 * @author     Philipp Memmel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class base_instance {

    /** @var string the string representing that a model cannot be chosen, but is preconfigured by the external AI service */
    public const PRECONFIGURED_MODEL = 'preconfigured';

    /** @var ?stdClass The database record */
    protected ?stdClass $record = null;

    /** @var int The record id */
    protected int $id = 0;

    /** @var ?string The name of the instance */
    protected ?string $name = null;

    /** @var ?string The tenant the instance belongs to */
    protected ?string $tenant = null;

    /** @var ?string The connector to which the instance belongs */
    protected ?string $connector = null;

    /** @var ?string The endpoint of the instance */
    protected ?string $endpoint = null;

    /** @var ?string The API key of the instance */
    protected ?string $apikey = null;

    /** @var ?string The model which is configured for this instance */
    protected ?string $model = null;

    /** @var ?string The info link */
    protected ?string $infolink = null;

    /** @var ?string First customfield attribute. */
    protected ?string $customfield1 = null;

    /** @var string Second customfield attribute. */
    protected ?string $customfield2 = null;

    /** @var ?string Third customfield attribute. */
    private ?string $customfield3 = null;

    /** @var ?string Fourth customfield attribute. */
    protected ?string $customfield4 = null;

    /** @var ?string Fifth customfield attribute. */
    protected ?string $customfield5 = null;

    /**
     * Create an object for this connector instance and - if the instance already exists - load all data from database.
     *
     * @param int $id the (record) id of the instance, pass 0 if you want to create a new instance
     */
    public function __construct(int $id = 0) {
        $this->id = $id;
        $this->load();
    }

    /**
     * Loads the instance data from database, if exists, and stores it into the class variables.
     */
    final public function load(): void {
        global $DB;
        $record = $DB->get_record('local_ai_manager_instance', ['id' => $this->id]);
        if (!$record) {
            return;
        }
        $this->record = $record;
        [
                $this->id,
                $this->name,
                $this->tenant,
                $this->connector,
                $this->endpoint,
                $this->apikey,
                $this->model,
                $this->infolink,
                $this->customfield1,
                $this->customfield2,
                $this->customfield3,
                $this->customfield4,
                $this->customfield5,
        ] = [
                $record->id,
                $record->name,
                $record->tenant,
                $record->connector,
                $record->endpoint,
                $record->apikey,
                $record->model,
                $record->infolink,
                $record->customfield1,
                $record->customfield2,
                $record->customfield3,
                $record->customfield4,
                $record->customfield5,
        ];
    }

    /**
     * Persists the object data to the database.
     */
    final public function store(): void {
        global $DB;
        $clock = \core\di::get(\core\clock::class);
        $record = new stdClass();
        $record->name = $this->name;
        $record->tenant = $this->tenant;
        $record->connector = $this->connector;
        $record->endpoint = $this->endpoint;
        $record->apikey = $this->apikey;
        $record->model = $this->model;
        $record->infolink = $this->infolink;
        $record->customfield1 = $this->customfield1;
        $record->customfield2 = $this->customfield2;
        $record->customfield3 = $this->customfield3;
        $record->customfield4 = $this->customfield4;
        $record->customfield5 = $this->customfield5;
        $currenttime = $clock->time();
        $record->timemodified = $currenttime;
        if (is_null($this->record)) {
            $record->timecreated = $currenttime;
            $record->id = $DB->insert_record('local_ai_manager_instance', $record);
            $this->id = $record->id;
        } else {
            $record->id = $this->id;
            $DB->update_record('local_ai_manager_instance', $record);
        }
        $this->record = $record;
    }

    /**
     * Returns all instance objects.
     *
     * @param bool $allinstances true if all instances should be returned, by default only the instances of the current tenant are
     *  returned
     * @return array array of instance objects
     */
    public static function get_all_instances(bool $allinstances = false): array {
        global $DB;

        $params = [];
        if (!$allinstances) {
            $params['tenant'] = \core\di::get(tenant::class)->get_identifier();
        }
        $records = $DB->get_records('local_ai_manager_instance', $params, '', 'id');
        $instances = [];
        foreach ($records as $record) {
            $instances[] = new self($record->id);
        }
        return $instances;
    }

    /**
     * Standard getter.
     *
     * @return int the id of the instance
     */
    public function get_id(): int {
        return $this->id;
    }

    /**
     * Standard getter.
     *
     * @return string the name of the instance
     */
    public function get_name(): string {
        return $this->name;
    }

    /**
     * Standard setter.
     *
     * @param string $name the name of the instance
     */
    public function set_name(string $name): void {
        $this->name = $name;
    }

    /**
     * Standard getter.
     *
     * @return string the tenant identifier
     */
    public function get_tenant(): string {
        return $this->tenant;
    }

    /**
     * Standard setter.
     *
     * @param string $tenant the identifier of the tenant the instance belongs to
     */
    public function set_tenant(string $tenant): void {
        $this->tenant = $tenant;
    }

    /**
     * Standard getter.
     *
     * @return ?string the connector identifier
     */
    public function get_connector(): ?string {
        return $this->connector;
    }

    /**
     * Standard setter.
     *
     * @param string $connector the connector name
     */
    public function set_connector(string $connector): void {
        $this->connector = $connector;
    }

    /**
     * Standard getter.
     *
     * @return string the endpoint of this instance
     */
    public function get_endpoint(): string {
        return $this->endpoint;
    }

    /**
     * Standard setter.
     *
     * @param string $endpoint the endpoint of this instance
     */
    public function set_endpoint(string $endpoint): void {
        $this->endpoint = $endpoint;
    }

    /**
     * Standard getter.
     *
     * @return ?string the apikey, can be null if not set
     */
    public function get_apikey(): ?string {
        return $this->apikey;
    }

    /**
     * Standard setter.
     *
     * @param ?string $apikey The API key of this instance
     */
    public function set_apikey(?string $apikey): void {
        $this->apikey = $apikey;
    }

    /**
     * Standard getter.
     *
     * @return string name of the model
     */
    public function get_model(): string {
        return $this->model;
    }

    /**
     * Standard setter.
     *
     * @param string $model the name of the model
     */
    public function set_model(string $model): void {
        $this->model = $model;
    }

    /**
     * Standard getter.
     *
     * @return ?string the info link, can be null
     */
    public function get_infolink(): ?string {
        return $this->infolink;
    }

    /**
     * Standard setter.
     *
     * @param ?string $infolink the info link
     */
    public function set_infolink(?string $infolink): void {
        $this->infolink = $infolink;
    }

    /**
     * Standard getter.
     *
     * @return ?string the content of the first customfield, null if not set
     */
    public function get_customfield1(): ?string {
        return $this->customfield1;
    }

    /**
     * Standard setter.
     *
     * @param ?string $customfield1 the value of the first customfield
     */
    public function set_customfield1(?string $customfield1): void {
        $this->customfield1 = $customfield1;
    }

    /**
     * Standard getter.
     *
     * @return ?string the content of the second customfield, null if not set
     */
    public function get_customfield2(): ?string {
        return $this->customfield2;
    }

    /**
     * Standard setter.
     *
     * @param ?string $customfield2 the value of the second customfield
     */
    public function set_customfield2(?string $customfield2): void {
        $this->customfield2 = $customfield2;
    }

    /**
     * Standard getter.
     *
     * @return ?string the content of the third customfield, null if not set
     */
    public function get_customfield3(): ?string {
        return $this->customfield3;
    }

    /**
     * Standard setter.
     *
     * @param ?string $customfield3 the value of the third customfield
     */
    public function set_customfield3(?string $customfield3): void {
        $this->customfield3 = $customfield3;
    }

    /**
     * Standard getter.
     *
     * @return ?string the content of the fourth customfield, null if not set
     */
    public function get_customfield4(): ?string {
        return $this->customfield4;
    }

    /**
     * Standard setter.
     *
     * @param ?string $customfield4 the value of the fourth customfield
     */
    public function set_customfield4(?string $customfield4): void {
        $this->customfield4 = $customfield4;
    }

    /**
     * Standard getter.
     *
     * @return ?string the content of the fifth customfield, null if not set
     */
    public function get_customfield5(): ?string {
        return $this->customfield5;
    }

    /**
     * Standard setter.
     *
     * @param ?string $customfield5 the value of the fifth customfield
     */
    public function set_customfield5(?string $customfield5): void {
        $this->customfield5 = $customfield5;
    }

    /**
     * Returns if we have already a database record for this object.
     *
     * @return bool true if there is a database record
     */
    public function record_exists(): bool {
        if (!is_null($this->record)) {
            return true;
        } else {
            $this->load();
            return is_null($this->record);
        }
    }

    /**
     * Passes the data of the object to a stdClass object which can be passed into a form to represent the initial values.
     *
     * @return stdClass the object containing the data for loading the form
     */
    final public function get_formdata(): stdClass {
        $this->load();
        $data = new stdClass();
        if (is_null($this->record)) {
            return $data;
        }
        $data->name = $this->get_name();
        $data->connector = $this->get_connector();
        $data->endpoint = $this->get_endpoint();
        $data->apikey = $this->get_apikey();
        $data->model = $this->get_model();
        $data->infolink = $this->get_infolink();
        foreach ($this->get_extended_formdata() as $key => $value) {
            $data->{$key} = $value;
        }
        return $data;
    }

    /**
     * Function to extend the form definition for subclasses.
     *
     * @param \MoodleQuickForm $mform the mform object which can be modified by the subclass
     */
    protected function extend_form_definition(\MoodleQuickForm $mform): void {
    }

    /**
     * Function to extend the form data stdClass.
     *
     * Should be overwritten by subclasses to pass additional data to the configuration form when the form is loaded.
     *
     * @return stdClass the form data to pass to the form for loading
     */
    protected function get_extended_formdata(): stdClass {
        return new stdClass();
    }

    /**
     * Function to add form definitions to the edit form.
     *
     * @param \MoodleQuickForm $mform the mform object
     * @param array $customdata the customdata which has been passed to the form when created
     */
    final public function edit_form_definition(\MoodleQuickForm $mform, array $customdata): void {
        $textelementparams = ['style' => 'width: 100%'];
        $mform->addElement('text', 'name', get_string('instancename', 'local_ai_manager'), $textelementparams);
        $mform->setType('name', PARAM_TEXT);
        $mform->addElement('text', 'tenant', get_string('tenant', 'local_ai_manager'), $textelementparams);
        $mform->setType('tenant', PARAM_ALPHANUM);
        if (empty($this->_customdata['id'])) {
            $mform->setDefault('tenant', $customdata['tenant']);
        }
        if (!is_siteadmin()) {
            $mform->freeze('tenant');
        }

        $connector = $customdata['connector'];
        $mform->addElement('text', 'connector', get_string('aitool', 'local_ai_manager'), $textelementparams);
        $mform->setType('connector', PARAM_TEXT);
        // That we have a valid connector here is being ensured by edit_instance.php.
        $mform->setDefault('connector', $connector);
        $mform->freeze('connector');

        $mform->addElement('text', 'endpoint', get_string('endpoint', 'local_ai_manager'), $textelementparams);
        $mform->setType('endpoint', PARAM_URL);

        $mform->addElement('passwordunmask', 'apikey', get_string('apikey', 'local_ai_manager'), $textelementparams);
        $mform->setType('apikey', PARAM_TEXT);

        $classname = '\\aitool_' . $connector . '\\connector';
        $connectorobject = \core\di::get($classname);
        $availablemodels = [];
        foreach ($connectorobject->get_models() as $modelname) {
            // phpcs:disable moodle.Commenting.TodoComment.MissingInfoInline
            // TODO maybe add lang strings, so we have $availablemodels[$modelname] = get_string($modelname); or sth similar.
            // phpcs:enable moodle.Commenting.TodoComment.MissingInfoInline
            $availablemodels[$modelname] = $modelname;
        }
        $mform->addElement('select', 'model', get_string('model', 'local_ai_manager'), $availablemodels, $textelementparams);

        $mform->addElement('text', 'infolink', get_string('infolink', 'local_ai_manager'), $textelementparams);
        $mform->setType('infolink', PARAM_URL);

        $this->extend_form_definition($mform);
    }

    /**
     * Stores the form data after form has been submitted.
     *
     * @param stdClass $data the form data
     */
    final public function store_formdata(stdClass $data): void {
        $this->set_name(trim($data->name));
        if (!empty($data->endpoint)) {
            $this->set_endpoint(trim($data->endpoint));
        }
        $this->set_apikey(!empty($data->apikey) ? trim($data->apikey) : '');
        $this->set_connector($data->connector);
        $this->set_tenant(trim($data->tenant));
        if (empty($data->model)) {
            // This is only a fallback. If the connector does not support the selection of a model,
            // it is supposed to overwrite this default value in the extend_store_formdata function.
            $data->model = self::PRECONFIGURED_MODEL;
        }
        $this->set_model($data->model);
        $this->set_infolink(trim($data->infolink));
        $this->extend_store_formdata($data);
        $this->store();
    }

    /**
     * Function to store additional form data.
     *
     * Should be overwritten by subclasses to store subclass specific form data.
     *
     * @param stdClass $data the form data after the form has been submitted
     */
    protected function extend_store_formdata(stdClass $data): void {
    }

    /**
     * Validates the form data after submission.
     *
     * @param array $data the form data
     * @param array $files the form data files
     * @return array associative array of the form ['nameofmformelement' => 'error if there is one'], should be empty if
     *  validation was successful
     */
    final public function validation(array $data, array $files): array {
        $errors = [];
        if (empty($data['name'])) {
            $errors['name'] = get_string('formvalidation_editinstance_name', 'local_ai_manager');
        }
        if (!empty($data['endpoint'])
                && str_starts_with($data['endpoint'], 'http://')
                && !str_starts_with($data['endpoint'], 'https://')) {
            $errors['endpoint'] = get_string('formvalidation_editinstance_endpointnossl', 'local_ai_manager');
        }
        return $errors + $this->extend_validation($data, $files);
    }

    /**
     * Function to do some extra validation.
     *
     * Should be overwritten by subclasses to validate the subclass specific mform fields.
     *
     * @param array $data the form data
     * @param array $files the form data files
     * @return array associative array of the form ['nameofmformelement' => 'error if there is one'], should be empty if
     *   validation was successful
     */
    protected function extend_validation(array $data, array $files): array {
        return [];
    }

    /**
     * Deletes the record related to this object from database.
     *
     * @throws \moodle_exception if the record does not exist (anymore)
     */
    public function delete(): void {
        global $DB;
        if (empty($this->id)) {
            $this->load();
            if (empty($this->id)) {
                throw new \moodle_exception('exception_instancenotexists', 'local_ai_manager', '', $this->id);
            }
        }

        // Before deleting we remove all assignments of purposes to this instance, if there are any.
        // We intentionally do not use dependency injection here to make sure we are using the config manager that belongs
        // to this instance.
        $configmanager = new config_manager(new tenant($this->get_tenant()));
        foreach (base_purpose::get_all_purposes() as $purpose) {
            foreach ([userinfo::ROLE_BASIC, userinfo::ROLE_EXTENDED] as $role) {
                $configkey = base_purpose::get_purpose_tool_config_key($purpose, $role);
                $configvalue = $configmanager->get_config($configkey);
                if (!$configvalue || intval($configvalue) === $this->get_id()) {
                    $configmanager->unset_config($configkey);
                }
            }
        }
        $DB->delete_records('local_ai_manager_instance', ['id' => $this->id]);
    }

    /**
     * Function which determines the supported purposes based on the definitions of available models in the connector class.
     *
     * @return array list of purpose names
     */
    final public function supported_purposes(): array {
        if (empty($this->get_model())) {
            return [];
        }
        $connector = \core\di::get(connector_factory::class)->get_connector_by_connectorname($this->connector);
        if (!in_array($this->get_model(), $connector->get_models())) {
            // This typically is the case if we are using a model that is preconfigured (for example when using Azure).
            return array_keys($connector->get_models_by_purpose());
        }
        $purposesofcurrentmodel = [];
        foreach ($connector->get_models_by_purpose() as $purpose => $models) {
            if (in_array($this->get_model(), $models)) {
                $purposesofcurrentmodel[] = $purpose;
            }
        }
        return $purposesofcurrentmodel;
    }
}
