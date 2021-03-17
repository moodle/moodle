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

namespace core\oauth2\service;

use core\oauth2\issuer;

/**
 * Interface for services, with the methods to be implemented by all the issuer implementing it.
 *
 * @package    core
 * @copyright  2021 Sara Arjona (sara@moodle.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface issuer_interface {

    /**
     * Build an OAuth2 issuer, with all the default values for this service.
     *
     * @return issuer|null The issuer initialised with proper default values, or null if no issuer is initialised.
     */
    public static function init(): ?issuer;

    /**
     * Create endpoints for this issuer.
     *
     * @param issuer $issuer Issuer the endpoints should be created for.
     * @return issuer
     */
    public static function create_endpoints(issuer $issuer): issuer;

    /**
     * If the discovery endpoint exists for this issuer, try and determine the list of valid endpoints.
     *
     * @param issuer $issuer
     * @return int The number of discovered services.
     */
    public static function discover_endpoints($issuer): int;

}
