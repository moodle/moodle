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

namespace mod_lti;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot.'/mod/lti/servicelib.php');

/**
 * Tests for servicelib.php
 *
 * @package   mod_lti
 * @copyright Copyright (c) 2015 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class servicelib_test extends \basic_testcase {
    /**
     * Test that lti_parse_message_id never fails with good and bad XML.
     *
     * @dataProvider message_id_provider
     * @param mixed $expected Expected message ID.
     * @param string $xml XML to parse.
     */
    public function test_lti_parse_message_id($expected, $xml) {
        $xml = simplexml_load_string($xml);
        $this->assertEquals($expected, lti_parse_message_id($xml));
    }

    /**
     * Test data provider for testing lti_parse_message_id
     *
     * @return array
     */
    public function message_id_provider() {
        $valid = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<imsx_POXEnvelopeRequest xmlns="http://www.imsglobal.org/services/ltiv1p1/xsd/imsoms_v1p0">
    <imsx_POXHeader>
        <imsx_POXRequestHeaderInfo>
            <imsx_version>V1.0</imsx_version>
            <imsx_messageIdentifier>9999</imsx_messageIdentifier>
        </imsx_POXRequestHeaderInfo>
    </imsx_POXHeader>
    <imsx_POXBody/>
</imsx_POXEnvelopeRequest>
XML;

        $noheader = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<imsx_POXEnvelopeRequest xmlns="http://www.imsglobal.org/services/ltiv1p1/xsd/imsoms_v1p0">
    <badXmlHere>
        <imsx_POXRequestHeaderInfo>
            <imsx_version>V1.0</imsx_version>
            <imsx_messageIdentifier>9999</imsx_messageIdentifier>
        </imsx_POXRequestHeaderInfo>
    </badXmlHere>
    <imsx_POXBody/>
</imsx_POXEnvelopeRequest>
XML;

        $noinfo = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<imsx_POXEnvelopeRequest xmlns="http://www.imsglobal.org/services/ltiv1p1/xsd/imsoms_v1p0">
    <imsx_POXHeader>
        <badXmlHere>
            <imsx_version>V1.0</imsx_version>
            <imsx_messageIdentifier>9999</imsx_messageIdentifier>
        </badXmlHere>
    </imsx_POXHeader>
    <imsx_POXBody/>
</imsx_POXEnvelopeRequest>
XML;

        $noidentifier = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<imsx_POXEnvelopeRequest xmlns="http://www.imsglobal.org/services/ltiv1p1/xsd/imsoms_v1p0">
    <imsx_POXHeader>
        <imsx_POXRequestHeaderInfo>
            <imsx_version>V1.0</imsx_version>
        </imsx_POXRequestHeaderInfo>
    </imsx_POXHeader>
    <imsx_POXBody/>
</imsx_POXEnvelopeRequest>
XML;

        return array(
            array(9999, $valid),
            array('', $noheader),
            array('', $noinfo),
            array('', $noidentifier),
        );
    }
}
