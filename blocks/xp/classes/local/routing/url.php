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
 * URL.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\routing;

/**
 * URL.
 *
 * The purpose of this override is to better handle the slasharguments.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class url extends \moodle_url {

    /** @var string The parameter name for slash arguments. */
    protected $slasharg;

    /**
     * Constructor.
     *
     * @param mixed $url The URL.
     * @param array $params The parameters.
     * @param string $anchor The anchor.
     */
    public function __construct($url, array $params = null, $anchor = null) {
        if ($url instanceof url) {
            // Make sure we carry that around when cloning the URL.
            $this->slasharg = $url->slasharg;
        }
        parent::__construct($url, $params, $anchor);
    }

    /**
     * Get a compatible URL.
     *
     * There are various situations where Moodle does not cope well with
     * slasharguments, notably in single_select and the like. This force
     * the slashargument to be a parameter, which is always properly handled.
     *
     * @return url
     */
    public function get_compatible_url() {
        global $CFG;
        $url = new url($this);
        if (!empty($this->slashargument) && $this->slasharg) {
            // From Moodle 4.5, we can no longer explicitly request a URL without slasharguments. However as there
            // are still issues with single_select, etc. we translate the slashargument to a parameter manually.
            if ($CFG->branch >= 405) {
                $url->param($this->slasharg, $this->slashargument);
                $url->slashargument = '';
            } else {
                $url->set_slashargument($this->slashargument, $this->slasharg, false);
            }
        }
        return $url;
    }

    /**
     * Set the slash argument.
     *
     * Override to catch the parameter name.
     *
     * @param string $path The path.
     * @param string $parameter The name of the parameter.
     * @param bool $supported Whether slash argument is supported.
     */
    public function set_slashargument($path, $parameter = 'file', $supported = null) {
        global $CFG;
        // We can't always trust that $CFG->slasharguments is set in older versions. From Moodle 4.5, the parameter is deprecated.
        $supported = $supported === null && $CFG->branch < 405 ? !empty($CFG->slasharguments) : $supported;
        $this->slasharg = $parameter;
        parent::set_slashargument($path, $parameter, $supported);
    }

}
