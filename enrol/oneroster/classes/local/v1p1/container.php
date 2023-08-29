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
 * One Roster Enrolment Client.
 *
 * @package    enrol_oneroster
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace enrol_oneroster\local\v1p1;

use enrol_oneroster\local\container as abstract_container;
use enrol_oneroster\local\interfaces\cache_factory as cache_factory_interface;
use enrol_oneroster\local\interfaces\collection_factory as collection_factory_interface;
use enrol_oneroster\local\interfaces\container as container_interface;
use enrol_oneroster\local\interfaces\entity_factory as entity_factory_interface;
use enrol_oneroster\local\interfaces\filter as filter_interface;
use enrol_oneroster\local\interfaces\rostering_endpoint as rostering_endpoint_interface;
use enrol_oneroster\local\v1p1\factories\cache_factory;
use enrol_oneroster\local\v1p1\factories\collection_factory;
use enrol_oneroster\local\v1p1\factories\entity_factory;

/**
 * One Roster 1.1 Factory Manager.
 *
 * @package    enrol_oneroster
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class container extends abstract_container implements container_interface {
    /** @var endpoint The rostering endpoint being used */
    protected $rostering = null;

    /** @var entity_factory The current entity factory */
    protected $entityfactory = null;

    /** @var collection_factory The current collection factory */
    protected $collectionfactory = null;

    /** @var cache_factory The current cache factory */
    protected $cachefactory = null;

    /**
     * Get the Rostering endpoint in use.
     *
     * @return  rostering_endpoint_interface
     */
    public function get_rostering_endpoint(): rostering_endpoint_interface {
        if ($this->rostering === null) {
            $this->rostering = $this->client->get_rostering_endpoint();
        }

        return $this->rostering;

    }

    /**
     * Get the Entity Factory.
     *
     * @return  entity_factory_interface
     */
    public function get_entity_factory(): entity_factory_interface {
        if ($this->entityfactory === null) {
            $this->entityfactory = new entity_factory($this);
        }

        return $this->entityfactory;
    }

    /**
     * Get the Collection Factory.
     *
     * @return  collection_factory_interface
     */
    public function get_collection_factory(): collection_factory_interface {
        if ($this->collectionfactory === null) {
            $this->collectionfactory = new collection_factory($this);
        }

        return $this->collectionfactory;
    }

    /**
     * Get the Cache Factory.
     *
     * @return  cache_factory_interface
     */
    public function get_cache_factory(): cache_factory_interface {
        if ($this->cachefactory === null) {
            $this->cachefactory = new cache_factory($this);
        }

        return $this->cachefactory;
    }

    /**
     * Get an instance of a filter.
     *
     * @return  filter_interface
     */
    public function get_filter_instance(): filter_interface {
        return new filter();
    }
}
