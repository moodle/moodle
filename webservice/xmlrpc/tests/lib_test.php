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
 * Unit tests for the XML-RPC web service.
 *
 * @package    webservice_xmlrpc
 * @category   test
 * @copyright  2015 Jun Pataleta <jun@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/webservice/xmlrpc/lib.php');

/**
 * Unit tests for the XML-RPC web service.
 *
 * @package    webservice_xmlrpc
 * @category   test
 * @copyright  2015 Jun Pataleta <jun@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class webservice_xmlrpc_test extends advanced_testcase {

    /**
     * Setup.
     */
    public function setUp() {
        $this->resetAfterTest();

        // All tests require xmlrpc. Skip tests, if xmlrpc is not installed.
        if (!function_exists('xmlrpc_decode')) {
            $this->markTestSkipped('XMLRPC is not installed.');
        }
    }

    /**
     * Test for array response.
     */
    public function test_client_with_array_response() {
        global $CFG;

        $client = new webservice_xmlrpc_client_mock('/webservice/xmlrpc/server.php', 'anytoken');
        $mockresponse = file_get_contents($CFG->dirroot . '/webservice/xmlrpc/tests/fixtures/array_response.xml');
        $client->set_mock_response($mockresponse);
        $result = $client->call('testfunction');
        $this->assertEquals(xmlrpc_decode($mockresponse), $result);
    }

    /**
     * Test for value response.
     */
    public function test_client_with_value_response() {
        global $CFG;

        $client = new webservice_xmlrpc_client_mock('/webservice/xmlrpc/server.php', 'anytoken');
        $mockresponse = file_get_contents($CFG->dirroot . '/webservice/xmlrpc/tests/fixtures/value_response.xml');
        $client->set_mock_response($mockresponse);
        $result = $client->call('testfunction');
        $this->assertEquals(xmlrpc_decode($mockresponse), $result);
    }

    /**
     * Test for fault response.
     */
    public function test_client_with_fault_response() {
        global $CFG;

        $client = new webservice_xmlrpc_client_mock('/webservice/xmlrpc/server.php', 'anytoken');
        $mockresponse = file_get_contents($CFG->dirroot . '/webservice/xmlrpc/tests/fixtures/fault_response.xml');
        $client->set_mock_response($mockresponse);
        $this->expectException('moodle_exception');
        $client->call('testfunction');
    }

    /**
     * Test the XML-RPC request encoding.
     */
    public function test_encode_request() {

        $client = new webservice_xmlrpc_client_mock('/webservice/xmlrpc/server.php', 'anytoken');

        // Encode the request with the proper encoding and escaping options.
        $xml = $client->encode_request('do_it', ['foo' => '<bar>ŠČŘŽÝÁÍÉ</bar>']);

        // Assert that decoding with explicit encoding will work. This appeared
        // to fail if the markup escaping was not set.
        $this->assertEquals(['<bar>ŠČŘŽÝÁÍÉ</bar>'], xmlrpc_decode($xml, 'UTF-8'));

        // Decoding also works with our wrapper method.
        $this->assertEquals(['<bar>ŠČŘŽÝÁÍÉ</bar>'], $client->decode_response($xml));

        // Our experiments show that even with default/implicit encoding,
        // requests encoded with markup escaping set are also decoded
        // correctly. This is known to be used in some servers so we test it
        // here, too.
        // However, this does not work for all strings, see next test.
        $this->assertEquals(['<bar>ŠČŘŽÝÁÍÉ</bar>'], xmlrpc_decode($xml));
    }

    /**
     * Test the XML-RPC response decoding
     */
    public function test_decode_response() {
        $client = new webservice_xmlrpc_client_mock('/webservice/xmlrpc/server.php', 'anytoken');

        $teststring = '<bar>Recherche thématique:Villes & Développement durable</bar>';

        // Encode the string with the proper encoding and escaping options. Assert that decoding will work.
        $xml = $client->encode_request('do_it', [$teststring]);
        $this->assertEquals([$teststring], $client->decode_response($xml));
        // For this particular string bare decoding function does not work.
        // It can't really be explained why it works for the string 'ŠČŘŽÝÁÍÉ' in the previous test but not this one.
        // Symbol é comes as chr(233) . It looks like '<bar>Recherche th�matique:Villes & D�veloppement durable</bar>'.
        $this->assertEquals([preg_replace('/é/', chr(233), $teststring)], xmlrpc_decode($xml));

        // Encode the string without any options (default encoding "iso-8859-1" is used). Assert that decoding will work.
        $xml = xmlrpc_encode_request('do_it', [$teststring]);
        $this->assertEquals([$teststring], $client->decode_response($xml));
        $this->assertEquals([$teststring], xmlrpc_decode($xml));

        // Another example of the string where bare xmlrpc_decode() does not work but our wrapper does.
        $teststring = 'Formación Docente';

        $xml = $client->encode_request('do_it', [$teststring]);
        $this->assertEquals([$teststring], $client->decode_response($xml));
        // Bare decoding function xmlrpc_decode() does not work.
        // Symbol ó comes as chr(243), it looks like 'Formaci�n Docente'.
        $this->assertEquals([preg_replace('/ó/', chr(243), $teststring)], xmlrpc_decode($xml));
    }
}

/**
 * Class webservice_xmlrpc_client_mock.
 *
 * Mock class that returns the processed XML-RPC response.
 *
 * @package    webservice_xmlrpc
 * @category   test
 * @copyright  2015 Jun Pataleta <jun@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class webservice_xmlrpc_client_mock extends webservice_xmlrpc_client {

    /** @var string The mock XML-RPC response string.  */
    private $mockresponse;

    /**
     * XML-RPC mock response setter.
     *
     * @param string $mockresponse
     */
    public function set_mock_response($mockresponse) {
        $this->mockresponse = $mockresponse;
    }

    /**
     * Since the call method uses download_file_content and it is hard to make an actual call to a web service,
     * we'll just have to simulate the receipt of the response from the server using the mock response so we
     * can test the processing result of this method.
     *
     * @param string $functionname the function name
     * @param array $params the parameters of the function
     * @return mixed The decoded XML RPC response.
     * @throws moodle_exception
     */
    public function call($functionname, $params = array()) {
        // Get the response.
        $response = $this->mockresponse;

        // This is the part of the code in webservice_xmlrpc_client::call() what we would like to test.
        // Decode the response.
        $result = xmlrpc_decode($response);
        if (is_array($result) && xmlrpc_is_fault($result)) {
            throw new moodle_exception($result['faultString']);
        }

        return $result;
    }

    /**
     * Allows to test the request encoding.
     *
     * @param string $functionname Name of the method to call.
     * @param mixed $params Method parameters compatible with the method signature.
     * @return string
     */
    public function encode_request($functionname, $params) {
        return parent::encode_request($functionname, $params);
    }

    /**
     * Allows to test the response decoding.
     *
     * @param string $response
     * @return array
     */
    public function decode_response($response) {
        return parent::decode_response($response);
    }
}
