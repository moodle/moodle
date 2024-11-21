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
 * Route definition.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\routing;

/**
 * Route definition.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class route_definition {

    /** @var string The route name. */
    protected $name;
    /** @var string The route URL. */
    protected $url;
    /** @var string The regex. */
    protected $regex;
    /** @var string The controller name. */
    protected $controllername;
    /** @var array The mappings. */
    protected $mapping;

    /**
     * Constructor.
     *
     * @param string $name The route name.
     * @param string $url The route URL.
     * @param string $regex The regex.
     * @param string $controller The controller name.
     * @param array $mapping The mappings.
     */
    public function __construct($name, $url, $regex, $controller, array $mapping = []) {
        $this->name = $name;
        $this->url = $url;
        $this->regex = $regex;
        $this->mapping = $mapping;
        $this->controllername = $controller;
    }

    /**
     * Return the route name.
     *
     * @return string
     */
    public function get_name() {
        return $this->name;
    }

    /**
     * Return the route URL.
     *
     * @return string
     */
    public function get_url() {
        return $this->url;
    }

    /**
     * Return the regex to match the route.
     *
     * @return string
     */
    public function get_regex() {
        return $this->regex;
    }

    /**
     * Return the mapping between regex match and arguments.
     *
     * @return array Where keys are group numbers, and values are argument names.
     */
    public function get_mapping() {
        return $this->mapping;
    }

    /**
     * Return the name of the controller.
     *
     * @return string
     */
    public function get_controller_name() {
        return $this->controllername;
    }

}
