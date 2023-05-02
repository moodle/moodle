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
 * @package filter_oembed
 * @author Sushant Gawali <sushant@introp.net>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2014 onwards Microsoft Open Technologies, Inc. (http://msopentech.com/)
 */

namespace filter_oembed\provider\powerbi\rest;

defined('MOODLE_INTERNAL') || die();

/**
 * API client for Power BI.
 */
class powerbi extends \local_o365\rest\o365api {
    /**
     * Get the base URI that API calls should be sent to.
     *
     * @return string|bool The URI to send API calls to, or false if a precondition failed.
     */
    public function get_apiuri() {
        return "https://api.powerbi.com/beta/myorg/";
    }
    /**
     * Get the API client's oauth2 resource.
     *
     * @return string The resource for oauth2 tokens.
     */
    public static function get_resource() {
        return 'https://analysis.windows.net/powerbi/api';
    }
    public function getreportoembedurl($reportid, $reportsdata) {
        $reportsdata = $this->process_apicall_response($reportsdata);
        foreach ($reportsdata['value'] as $report) {
            if ($report['id'] == $reportid) {
                return $report['embedUrl'];
            }
        }
    }
}