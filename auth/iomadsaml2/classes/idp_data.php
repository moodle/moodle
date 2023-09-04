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

namespace auth_iomadsaml2;

/**
 * IdP data class.
 *
 * @package    auth_iomadsaml2
 * @author     Nicholas Hoobin <nicholashoobin@catalyst-au.net>
 * @copyright  Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class idp_data {
    /** @var string $idpname */
    public $idpname;

    /** @var string $idpurl */
    public $idpurl;

    /** @var string $idpicon */
    public $idpicon;

    /** @var string $rawxml */
    public $rawxml;

    /**
     * idp_data constructor.
     *
     * @param string $idpname
     * @param string $idpurl
     * @param string $idpicon
     */
    public function __construct($idpname, $idpurl, $idpicon) {
        $this->idpname = $idpname;
        $this->idpurl = $idpurl;
        $this->idpicon = $idpicon;
        $this->rawxml = null;
    }

    /**
     * Get raw xml.
     *
     * @return string
     */
    public function get_rawxml() {
        return $this->rawxml;
    }

    /**
     * Set raw xml.
     *
     * @param string $rawxml
     */
    public function set_rawxml($rawxml) {
        $this->rawxml = $rawxml;
    }
}
