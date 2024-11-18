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
 * IdP metadata parser class.
 *
 * @package    auth_iomadsaml2
 * @author     Sam Chaffee
 * @copyright  Copyright (c) 2017 Blackboard Inc. (http://www.blackboard.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace auth_iomadsaml2;

/**
 * IdP metadata parser class.
 *
 * @package    auth_iomadsaml2
 * @copyright  Copyright (c) 2017 Blackboard Inc. (http://www.blackboard.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class metadata_parser {
    /**
     * @var string
     */
    private $entityid = '';

    /**
     * @var string
     */
    private $idpdefaultname = '';

    /**
     * Parse raw xml.
     *
     * @param string $rawxml
     * @throws \moodle_exception
     */
    public function parse($rawxml) {
        try {

            $xml = new \SimpleXMLElement($rawxml);
            $xml->registerXPathNamespace('md',   'urn:oasis:names:tc:SAML:2.0:metadata');
            $xml->registerXPathNamespace('mdui', 'urn:oasis:names:tc:SAML:metadata:ui');

            // Find all IDPSSODescriptor elements and then work back up to the entityID.
            $idps = $xml->xpath('//md:EntityDescriptor[//md:IDPSSODescriptor]');
            if ($idps && isset($idps[0])) {
                $this->entityid = (string)$idps[0]->attributes('', true)->entityID[0];

                $names = @$idps[0]->xpath('//mdui:DisplayName');
                if ($names && isset($names[0])) {
                    $this->idpdefaultname = (string)$names[0];
                }
            }
        } catch (\Exception $e) {
            throw new \moodle_exception('errorparsingxml', 'auth_iomadsaml2', '', $e->getMessage());
        }
    }

    /**
     * Get entity.
     *
     * @return string
     */
    public function get_entityid() {
        return $this->entityid;
    }

    /**
     * Get idp defaultname.
     *
     * @return string
     */
    public function get_idpdefaultname() {
        return $this->idpdefaultname;
    }
}
