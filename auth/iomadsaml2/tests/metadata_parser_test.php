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
 * Testcase class for metadata_parser class.
 *
 * @package    auth_iomadsaml2
 * @author     Sam Chaffee
 * @copyright  Copyright (c) 2017 Blackboard Inc. (http://www.blackboard.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
use auth_iomadsaml2\metadata_parser;

/**
 * Testcase class for metadata_parser class.
 *
 * @package    auth_iomadsaml2
 * @copyright  Copyright (c) 2017 Blackboard Inc. (http://www.blackboard.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class auth_iomadsaml2_metadata_parser_testcase extends basic_testcase {

    public function test_parse_metadata() {
        $xml = file_get_contents(__DIR__ . '/fixtures/metadata.xml');

        $parser = new metadata_parser();
        $parser->parse($xml);

        $this->assertEquals('https://idp.example.org/idp/shibboleth', $parser->get_entityid());
        $this->assertEquals('Example.com test IDP', $parser->get_idpdefaultname());
    }

    public function test_parse_metadata_fail() {
        $malformedxml = <<<XML
<EntitiesDescriptor Name="https://your-federation.org/metadata/federation-name.xml"
                    xmlns="urn:oasis:names:tc:SAML:2.0:metadata"
                    xmlns:ds="http://www.w3.org/2000/09/xmldsig#"
                    xmlns:shibmd="urn:mace:shibboleth:metadata:1.0"
                    xmlns:mdui="urn:oasis:names:tc:SAML:metadata:ui"
                    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">

    <!-- Actual providers go here.  -->

    <!-- An identity provider. -->
    <EntityDescriptor entityID="https://idp.example.org/idp/shibboleth">
XML;

        $parser = new metadata_parser();
        $this->expectException(\moodle_exception::class);
        $parser->parse($malformedxml);
    }
}
