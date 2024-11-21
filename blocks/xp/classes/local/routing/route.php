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
 * Route.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\routing;

/**
 * Route.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class route {

    /** @var route_definition Route definition. */
    protected $definition;

    /** @var array Paramters. */
    protected $params;

    /**
     * Constructor.
     *
     * @param route_definition $definition The definition this is based on.
     * @param array $params The parameters.
     */
    public function __construct(route_definition $definition, array $params = []) {
        $this->definition = $definition;
        $this->params = $params;
    }

    /**
     * Get the definition this is based on.
     *
     * @return route_definition
     */
    public function get_definition() {
        return $this->definition;
    }

    /**
     * Get the route params.
     *
     * Typically the parameters which were extracted from
     * the route definition from a request.
     *
     * @return array
     */
    public function get_params() {
        return $this->params;
    }

}
