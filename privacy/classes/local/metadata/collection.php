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
 * This file defines the core_privacy\local\metadata\collection class object.
 *
 * The collection class is used to organize a collection of types
 * objects, which contains the privacy field details of a component.
 *
 * @package core_privacy
 * @copyright 2018 Jake Dallimore <jrhdallimore@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_privacy\local\metadata;

use core_privacy\local\metadata\types\type;

defined('MOODLE_INTERNAL') || die();

/**
 * A collection of metadata items.
 *
 * @copyright 2018 Jake Dallimore <jrhdallimore@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class collection {

    /**
     * @var string The component that the items in the collection belong to.
     */
    protected $component;

    /**
     * @var array   The collection of metadata items.
     */
    protected $collection = [];

    /**
     * Constructor for a component's privacy collection class.
     *
     * @param string $component component name.
     */
    public function __construct($component) {
        $this->component = $component;
    }

    /**
     * Function to add an object that implements type interface to the current collection.
     *
     * @param   type    $type to add to collection.
     * @return  $this
     */
    public function add_type(type $type) {
        $this->collection[] = $type;

        return $this;
    }

    /**
     * Function to add a database table which contains user data to this collection.
     *
     * @param   string  $name the name of the database table.
     * @param   array   $privacyfields An associative array of fieldname to description.
     * @param   string  $summary A description of what the table is used for.
     * @return  $this
     */
    public function add_database_table($name, array $privacyfields, $summary = '') {
        $this->add_type(new types\database_table($name, $privacyfields, $summary));

        return $this;
    }

    /**
     * Function to link a subsystem to the component.
     *
     * @param   string $name the name of the subsystem to link.
     * @param   array $privacyfields An optional associative array of fieldname to description.
     * @param   string $summary A description of what is stored within this subsystem.
     * @return  $this
     */
    public function add_subsystem_link($name, array $privacyfields = [], $summary = '') {
        $this->add_type(new types\subsystem_link($name, $privacyfields, $summary));

        return $this;
    }

    /**
     * Old function to link a subsystem to the component.
     *
     * This function is legacy and is not recommended. Please use add_subsystem_link() instead.
     *
     * @param   string $name the name of the subsystem to link.
     * @param   string $summary A description of what is stored within this subsystem.
     * @return  $this
     */
    public function link_subsystem($name, $summary = '') {
        $this->add_type(new types\subsystem_link($name, [], $summary));

        return $this;
    }

    /**
     * Function to link a plugin to the component.
     *
     * @param   string  $name the name of the plugin to link.
     * @param   array $privacyfields An optional associative array of fieldname to description.
     * @param   string  $summary A description of what is stored within this plugin.
     * @return  $this
     */
    public function add_plugintype_link($name, array $privacyfields = [], $summary = '') {
        $this->add_type(new types\plugintype_link($name, $privacyfields, $summary));

        return $this;
    }

    /**
     * Old function to link a plugin to the component.
     *
     * This function is legacy and is not recommended. Please use add_plugintype_link() instead.
     *
     * @param   string  $name the name of the plugin to link.
     * @param   string  $summary A description of what is stored within this plugin.
     * @return  $this
     */
    public function link_plugintype($name, $summary = '') {
        $this->add_type(new types\plugintype_link($name, [], $summary));

        return $this;
    }

    /**
     * Function to indicate that data may be exported to an external location.
     *
     * @param   string  $name A name for the type of data exported.
     * @param   array   $privacyfields A list of fields with their description.
     * @param   string  $summary A description of what the table is used for. This is a language string identifier
     *                           within the component.
     * @return  $this
     */
    public function add_external_location_link($name, array $privacyfields, $summary = '') {
        $this->add_type(new types\external_location($name, $privacyfields, $summary));

        return $this;
    }

    /**
     * Old function to indicate that data may be exported to an external location.
     *
     * This function is legacy and is not recommended. Please use add_external_location_link() instead.
     *
     * @param   string  $name A name for the type of data exported.
     * @param   array   $privacyfields A list of fields with their description.
     * @param   string  $summary A description of what the table is used for. This is a language string identifier
     *                           within the component.
     * @return  $this
     */
    public function link_external_location($name, array $privacyfields, $summary = '') {
        $this->add_type(new types\external_location($name, $privacyfields, $summary));

        return $this;
    }

    /**
     * Add a type of user preference to the collection.
     *
     * Typically this is a single user preference, but in some cases the
     * name of a user preference fits a particular format.
     *
     * @param   string  $name The name of the user preference.
     * @param   string  $summary A description of what the preference is used for.
     * @return  $this
     */
    public function add_user_preference($name, $summary = '') {
        $this->add_type(new types\user_preference($name, $summary));

        return $this;
    }

    /**
     * Function to return the current component name.
     *
     * @return string
     */
    public function get_component() {
        return $this->component;
    }

    /**
     * The content of this collection.
     *
     * @return  types\type[]
     */
    public function get_collection() {
        return $this->collection;
    }
}
