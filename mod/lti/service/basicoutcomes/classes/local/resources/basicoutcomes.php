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
 * This file contains a class definition for the Basic Outcomes resource
 *
 * @package    ltiservice_basicoutcomes
 * @copyright  2019 Stephen Vickers
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace ltiservice_basicoutcomes\local\resources;

defined('MOODLE_INTERNAL') || die();

/**
 * A resource implementing the Basic Outcomes service.
 *
 * @package    ltiservice_basicoutcomes
 * @since      Moodle 3.7
 * @copyright  2019 Stephen Vickers
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class basicoutcomes extends \mod_lti\local\ltiservice\resource_base {

    /**
     * Class constructor.
     *
     * @param \mod_lti\local\ltiservice\service_base $service Service instance
     */
    public function __construct($service) {

        parent::__construct($service);
        $this->id = 'Outcomes.LTI1';
        $this->template = '';
        $this->formats[] = 'application/vnd.ims.lti.v1.outcome+xml';
        $this->methods[] = 'POST';

    }

    /**
     * Get the resource fully qualified endpoint.
     *
     * @return string
     */
    public function get_endpoint() {

        $url = new \moodle_url('/mod/lti/service.php');
        return $url->out(false);

    }

    /**
     * Execute the request for this resource.
     *
     * @param \mod_lti\local\ltiservice\response $response  Response object for this request.
     */
    public function execute($response) {
        // Should never be called as the endpoint sends requests to the LTI 1 service endpoint.
    }

}
