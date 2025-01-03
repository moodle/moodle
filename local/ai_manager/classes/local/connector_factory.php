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

namespace local_ai_manager\local;

use local_ai_manager\base_connector;
use local_ai_manager\base_purpose;
use local_ai_manager\base_instance;

/**
 * Class for creating/retrieving some important objects.
 *
 * @package    local_ai_manager
 * @copyright  2024 ISB Bayern
 * @author     Philipp Memmel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class connector_factory {

    /** @var base_purpose $purpose the purpose object */
    private base_purpose $purpose;

    /** @var base_instance the connector instance object */
    private base_instance $connectorinstance;

    /** @var base_connector the connector object */
    private base_connector $connector;

    /**
     * Constructs the connector factory object.
     *
     * @param config_manager $configmanager the config manager of the currently used tenant
     */
    public function __construct(
            /** @var config_manager $configmanager the config manager of the currently used tenant */
            private readonly config_manager $configmanager
    ) {
    }

    /**
     * Returns the connector instance object for a given connector instance id.
     *
     * @param int $id the connector instance id (of the database record)
     * @return base_instance the instance object
     */
    public function get_connector_instance_by_id(int $id): base_instance {
        global $DB;
        if (!empty($this->connectorinstance) && $this->connectorinstance->get_id() === $id) {
            return $this->connectorinstance;
        }
        $instancerecord = $DB->get_record('local_ai_manager_instance', ['id' => $id], '*', MUST_EXIST);
        $instanceclassname = '\\aitool_' . $instancerecord->connector . '\\instance';
        $this->connectorinstance = new $instanceclassname($id);
        return $this->connectorinstance;
    }

    /**
     * Returns the connector instance object defined for the given purpose.
     *
     * @param string $purpose the purpose name
     * @param int $role the local_ai_manager internal role
     * @return ?base_instance the instance object or null if no instance has been configured for the given purpose
     */
    public function get_connector_instance_by_purpose(string $purpose, int $role): ?base_instance {
        $instanceid = $this->configmanager->get_config(base_purpose::get_purpose_tool_config_key($purpose, $role));
        if (empty($instanceid)) {
            return null;
        }
        return $this->get_connector_instance_by_id($instanceid);
    }

    /**
     * Returns a new connector instance object for a given connector.
     *
     * @param string $connectorname the name of the connector
     * @return base_instance a new connector instance object
     */
    public function get_new_instance(string $connectorname): base_instance {
        $instanceclassname = '\\aitool_' . $connectorname . '\\instance';
        $this->connectorinstance = new $instanceclassname();
        $this->connectorinstance->set_connector($connectorname);
        return $this->connectorinstance;
    }

    /**
     * Converts the name of a connector to a connector object.
     *
     * @param string $connectorname the connector name
     * @return base_connector the corresponding connector object
     */
    public function get_connector_by_connectorname(string $connectorname): base_connector {
        $connectorclassname = '\\aitool_' . $connectorname . '\\connector';
        $instance = $this->get_new_instance($connectorname);
        $this->connector = new $connectorclassname($instance);
        return $this->connector;
    }

    /**
     * Retrieve the connector object based on the purpose.
     *
     * @param string $purpose the purpose name
     * @param int $role the local_ai_manager internal role
     * @return ?base_connector the connector object or null if no connector instance for the purpose has been configured for this
     *  tenant
     */
    public function get_connector_by_purpose(string $purpose, int $role): ?base_connector {
        $instance = $this->get_connector_instance_by_purpose($purpose, $role);
        if ($instance === null) {
            return null;
        }
        $connectorclassname = '\\aitool_' . $instance->get_connector() . '\\connector';
        $this->connector = new $connectorclassname($instance);
        return $this->connector;
    }

    /**
     * Helper function to determine if an instance already exists.
     *
     * @param int $id the id of the instance to check
     * @return bool true if the instance exists
     */
    public function instance_exists(int $id): bool {
        global $DB;
        return $DB->record_exists('local_ai_manager_instance', ['id' => $id]);
    }

    /**
     * Returns the purpose object for the given purpose name.
     *
     * @param string $purpose the purpose name
     * @return base_purpose the corresponding purpose object
     * @throws \coding_exception if there is no purpose with this name or the purpose subplugin is not enabled
     */
    public function get_purpose_by_purpose_string(string $purpose): base_purpose {
        if (empty($purpose) || !in_array($purpose, \local_ai_manager\plugininfo\aipurpose::get_enabled_plugins())) {
            throw new \coding_exception('Purpose ' . $purpose . ' does not exist or is not enabled');
        }
        $purposeclassname = '\\aipurpose_' . $purpose . '\\purpose';
        $this->purpose = new $purposeclassname();
        return $this->purpose;
    }

    /**
     * Retrieves all connector instances for a given purpose.
     *
     * @param string $purpose the purpose
     * @return array list of all possible connector instances available for this purpose
     */
    public static function get_connector_instances_for_purpose(string $purpose): array {
        $instances = [];
        foreach (base_instance::get_all_instances() as $instance) {
            if (in_array($purpose, $instance->supported_purposes())) {
                $instances[$instance->get_id()] = $instance;
            }
        }
        return $instances;
    }
}
