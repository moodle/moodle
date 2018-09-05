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
 * Framework mapper.
 *
 * @package    tool_lpmigrate
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_lpmigrate;
defined('MOODLE_INTERNAL') || die();

use core_competency\api;

/**
 * Framework mapper class.
 *
 * @package    tool_lpmigrate
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class framework_mapper {

    /** @var int The ID of the framework we're migrating from. */
    protected $from;
    /** @var int The ID of the framework we're migrating to. */
    protected $to;
    /** @var array The collection of objects at origin. */
    protected $collectionfrom;
    /** @var array The collection of objects at destination. */
    protected $collectionto;
    /** @var array Mappings. */
    protected $mappings = array();

    /**
     * Constructor.
     * @param int $from Framework ID from.
     * @param int $to Framework ID to.
     */
    public function __construct($from, $to) {
        $this->from = $from;
        $this->to = $to;
    }

    /**
     * Add a mapping.
     * @param int $idfrom From ID.
     * @param int $idto To ID.
     */
    public function add_mapping($idfrom, $idto) {
        $this->mappings[$idfrom] = $idto;
    }

    /**
     * Auto map the frameworks.
     * @return void
     */
    public function automap() {
        $map = array();

        // Shallow copy.
        $collectionfrom = $this->get_collection_from();
        $collectionto = $this->get_collection_to();

        // Find mappings.
        foreach ($collectionfrom as $keyfrom => $compfrom) {
            foreach ($collectionto as $keyto => $compto) {
                if ($compfrom->get('idnumber') == $compto->get('idnumber')) {
                    $map[$compfrom->get('id')] = $compto->get('id');
                    unset($collectionfrom[$keyfrom]);
                    unset($collectionto[$keyto]);
                    break;
                }
            }
        }

        $this->mappings = $map;
    }

    /**
     * Get all IDs at origin.
     * @return array
     */
    public function get_all_from() {
        return array_keys($this->get_collection_from());
    }

    /**
     * Get all IDs at destination.
     * @return array
     */
    public function get_all_to() {
        return array_keys($this->get_collection_to());
    }

    /**
     * Get the collection at origin.
     * @return array
     */
    protected function get_collection_from() {
        if ($this->collectionfrom === null) {
            $this->collectionfrom = api::search_competencies('', $this->from);
        }
        return $this->collectionfrom;
    }

    /**
     * Get the collection at destination.
     * @return array
     */
    protected function get_collection_to() {
        if ($this->collectionto === null) {
            $this->collectionto = api::search_competencies('', $this->to);
        }
        return $this->collectionto;
    }

    /**
     * Get the defined mappings.
     * @return array
     */
    public function get_mappings() {
        return $this->mappings;
    }

    /**
     * Get the IDs of the objects at origin which do not have a mapping at destination.
     * @return array
     */
    public function get_unmapped_from() {
        return array_keys(array_diff_key($this->get_collection_from(), $this->mappings));
    }

    /**
     * Get the origin objects with missing mappings.
     * @return array
     */
    public function get_unmapped_objects_from() {
        return array_diff_key($this->get_collection_from(), $this->mappings);
    }

    /**
     * Get the IDs of the objects at destination which do not have a mapping at origin.
     * @return array
     */
    public function get_unmapped_to() {
        return array_keys(array_diff_key($this->get_collection_to(), array_flip($this->mappings)));
    }

    /**
     * Get the destination objects with missing mappings.
     * @return array
     */
    public function get_unmapped_objects_to() {
        return array_diff_key($this->get_collection_to(), array_flip($this->mappings));
    }

    /**
     * Whether some mappings were set.
     * @return bool
     */
    public function has_mappings() {
        return !empty($this->mappings);
    }

    /**
     * Reset the mappings.
     * @return void
     */
    public function reset_mappings() {
        $this->mappings = array();
    }

}
