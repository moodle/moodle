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
 * Unit tests for the WSDL class.
 *
 * @package    webservice_soap
 * @category   test
 * @copyright  2016 Jun Pataleta <jun@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace webservice_soap;
defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/webservice/soap/classes/wsdl.php');

/**
 * Unit tests for the WSDL class.
 *
 * @package    webservice_soap
 * @category   test
 * @copyright  2016 Jun Pataleta <jun@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class wsdl_test extends \advanced_testcase {

    /**
     * Test generated WSDL with no added complex types nor functions.
     */
    public function test_minimum_wsdl(): void {
        $this->resetAfterTest();

        $serviceclass = 'testserviceclass';
        $namespace = 'testnamespace';
        $wsdl = new wsdl($serviceclass, $namespace);

        // Test definitions node.
        $definitions = new \SimpleXMLElement($wsdl->to_xml());
        $defattrs = $definitions->attributes();
        $this->assertEquals($serviceclass, $defattrs->name);
        $this->assertEquals($namespace, $defattrs->targetNamespace);

        // Test types node and attributes.
        $this->assertNotNull($definitions->types);
        $this->assertEquals($namespace, $definitions->types->children('xsd', true)->schema->attributes()->targetNamespace);

        // Test portType node and attributes.
        $this->assertNotNull($definitions->portType);
        $this->assertEquals($serviceclass . wsdl::PORT, $definitions->portType->attributes()->name);

        // Test binding node and attributes.
        $this->assertNotNull($definitions->binding);
        $this->assertEquals($serviceclass . wsdl::BINDING, $definitions->binding->attributes()->name);
        $this->assertEquals('tns:' . $serviceclass . wsdl::PORT, $definitions->binding->attributes()->type);

        $bindingattrs = $definitions->binding->children('soap', true)->binding->attributes();
        $this->assertNotEmpty('rpc', $bindingattrs);
        $this->assertEquals('rpc', $bindingattrs->style);
        $this->assertEquals(wsdl::NS_SOAP_TRANSPORT, $bindingattrs->transport);

        // Test service node.
        $this->assertNotNull($definitions->service);
        $this->assertEquals($serviceclass . wsdl::SERVICE, $definitions->service->attributes()->name);

        $serviceport = $definitions->service->children()->port;
        $this->assertNotEmpty($serviceport);
        $this->assertEquals($serviceclass . wsdl::PORT, $serviceport->attributes()->name);
        $this->assertEquals('tns:' . $serviceclass . wsdl::BINDING, $serviceport->attributes()->binding);

        $serviceportaddress = $serviceport->children('soap', true)->address;
        $this->assertNotEmpty($serviceportaddress);
        $this->assertEquals($namespace, $serviceportaddress->attributes()->location);
    }

    /**
     * Test output WSDL with complex type added.
     */
    public function test_add_complex_type(): void {
        $this->resetAfterTest();

        $classname = 'testcomplextype';
        $classattrs = array(
            'doubleparam' => array(
                'type' => 'double',
                'nillable' => true
            ),
            'stringparam' => array(
                'type' => 'string',
                'nillable' => true
            ),
            'intparam' => array(
                'type' => 'int',
                'nillable' => true
            ),
            'boolparam' => array(
                'type' => 'int',
                'nillable' => true
            ),
            'classparam' => array(
                'type' => 'teststruct'
            ),
            'arrayparam' => array(
                'type' => 'array',
                'nillable' => true
            ),
        );

        $serviceclass = 'testserviceclass';
        $namespace = 'testnamespace';
        $wsdl = new wsdl($serviceclass, $namespace);
        $wsdl->add_complex_type($classname, $classattrs);

        $definitions = new \SimpleXMLElement($wsdl->to_xml());

        // Test types node and attributes.
        $this->assertNotNull($definitions->types);
        $this->assertEquals($namespace, $definitions->types->children('xsd', true)->schema->attributes()->targetNamespace);
        $complextype = $definitions->types->children('xsd', true)->schema->children('xsd', true);
        $this->assertNotEmpty($complextype);

        // Test the complex type's attributes.
        foreach ($complextype->children('xsd', true)->all->children('xsd', true) as $element) {
            foreach ($classattrs as $name => $options) {
                if (strcmp($name, $element->attributes()->name) != 0) {
                    continue;
                }
                switch ($options['type']) {
                    case 'double':
                    case 'int':
                    case 'string':
                        $this->assertEquals('xsd:' . $options['type'], $element->attributes()->type);
                        break;
                    case 'array':
                        $this->assertEquals('soap-enc:' . ucfirst($options['type']), $element->attributes()->type);
                        break;
                    default:
                        $this->assertEquals('tns:' . $options['type'], $element->attributes()->type);
                        break;
                }
                if (!empty($options['nillable'])) {
                    $this->assertEquals('true', $element->attributes()->nillable);
                }
                break;
            }
        }
    }

    /**
     * Test output WSDL when registering a web service function.
     */
    public function test_register(): void {
        $this->resetAfterTest();

        $serviceclass = 'testserviceclass';
        $namespace = 'testnamespace';
        $wsdl = new wsdl($serviceclass, $namespace);

        $functionname = 'testfunction';
        $documentation = 'This is a test function';
        $in = array(
            'doubleparam' => array(
                'type' => 'double'
            ),
            'stringparam' => array(
                'type' => 'string'
            ),
            'intparam' => array(
                'type' => 'int'
            ),
            'boolparam' => array(
                'type' => 'int'
            ),
            'classparam' => array(
                'type' => 'teststruct'
            ),
            'arrayparam' => array(
                'type' => 'array'
            )
        );
        $out = array(
            'doubleparam' => array(
                'type' => 'double'
            ),
            'stringparam' => array(
                'type' => 'string'
            ),
            'intparam' => array(
                'type' => 'int'
            ),
            'boolparam' => array(
                'type' => 'int'
            ),
            'classparam' => array(
                'type' => 'teststruct'
            ),
            'arrayparam' => array(
                'type' => 'array'
            ),
            'return' => array(
                'type' => 'teststruct2'
            )
        );
        $wsdl->register($functionname, $in, $out, $documentation);

        $definitions = new \SimpleXMLElement($wsdl->to_xml());

        // Test portType operation node.
        $porttypeoperation = $definitions->portType->operation;
        $this->assertEquals($documentation, $porttypeoperation->documentation);
        $this->assertEquals('tns:' . $functionname . wsdl::IN, $porttypeoperation->input->attributes()->message);
        $this->assertEquals('tns:' . $functionname . wsdl::OUT, $porttypeoperation->output->attributes()->message);

        // Test binding operation nodes.
        $bindingoperation = $definitions->binding->operation;
        $soapoperation = $bindingoperation->children('soap', true)->operation;
        $this->assertEquals($namespace . '#' . $functionname, $soapoperation->attributes()->soapAction);
        $inputbody = $bindingoperation->input->children('soap', true);
        $this->assertEquals('encoded', $inputbody->attributes()->use);
        $this->assertEquals(wsdl::NS_SOAP_ENC, $inputbody->attributes()->encodingStyle);
        $this->assertEquals($namespace, $inputbody->attributes()->namespace);
        $outputbody = $bindingoperation->output->children('soap', true);
        $this->assertEquals('encoded', $outputbody->attributes()->use);
        $this->assertEquals(wsdl::NS_SOAP_ENC, $outputbody->attributes()->encodingStyle);
        $this->assertEquals($namespace, $outputbody->attributes()->namespace);

        // Test messages.
        $messagein = $definitions->message[0];
        $this->assertEquals($functionname . wsdl::IN, $messagein->attributes()->name);
        foreach ($messagein->children() as $part) {
            foreach ($in as $name => $options) {
                if (strcmp($name, $part->attributes()->name) != 0) {
                    continue;
                }
                switch ($options['type']) {
                    case 'double':
                    case 'int':
                    case 'string':
                        $this->assertEquals('xsd:' . $options['type'], $part->attributes()->type);
                        break;
                    case 'array':
                        $this->assertEquals('soap-enc:' . ucfirst($options['type']), $part->attributes()->type);
                        break;
                    default:
                        $this->assertEquals('tns:' . $options['type'], $part->attributes()->type);
                        break;
                }
                break;
            }
        }
        $messageout = $definitions->message[1];
        $this->assertEquals($functionname . wsdl::OUT, $messageout->attributes()->name);
        foreach ($messageout->children() as $part) {
            foreach ($out as $name => $options) {
                if (strcmp($name, $part->attributes()->name) != 0) {
                    continue;
                }
                switch ($options['type']) {
                    case 'double':
                    case 'int':
                    case 'string':
                        $this->assertEquals('xsd:' . $options['type'], $part->attributes()->type);
                        break;
                    case 'array':
                        $this->assertEquals('soap-enc:' . ucfirst($options['type']), $part->attributes()->type);
                        break;
                    default:
                        $this->assertEquals('tns:' . $options['type'], $part->attributes()->type);
                        break;
                }
                break;
            }
        }
    }

    /**
     * Test output WSDL when registering a web service function with no input parameters.
     */
    public function test_register_without_input(): void {
        $this->resetAfterTest();

        $serviceclass = 'testserviceclass';
        $namespace = 'testnamespace';
        $wsdl = new wsdl($serviceclass, $namespace);

        $functionname = 'testfunction';
        $documentation = 'This is a test function';

        $out = array(
            'return' => array(
                'type' => 'teststruct2'
            )
        );
        $wsdl->register($functionname, null, $out, $documentation);

        $definitions = new \SimpleXMLElement($wsdl->to_xml());

        // Test portType operation node.
        $porttypeoperation = $definitions->portType->operation;
        $this->assertEquals($documentation, $porttypeoperation->documentation);
        $this->assertFalse(isset($porttypeoperation->input));
        $this->assertTrue(isset($porttypeoperation->output));

        // Test binding operation nodes.
        $bindingoperation = $definitions->binding->operation;
        // Confirm that there is no input node.
        $this->assertFalse(isset($bindingoperation->input));
        $this->assertTrue(isset($bindingoperation->output));

        // Test messages.
        // Assert there's only the output message node.
        $this->assertEquals(1, count($definitions->message));
        $messageout = $definitions->message[0];
        $this->assertEquals($functionname . wsdl::OUT, $messageout->attributes()->name);

    }

    /**
     * Test output WSDL when registering a web service function with no output parameters.
     */
    public function test_register_without_output(): void {
        $this->resetAfterTest();

        $serviceclass = 'testserviceclass';
        $namespace = 'testnamespace';
        $wsdl = new wsdl($serviceclass, $namespace);

        $functionname = 'testfunction';
        $documentation = 'This is a test function';

        $in = array(
            'return' => array(
                'type' => 'teststruct2'
            )
        );
        $wsdl->register($functionname, $in, null, $documentation);

        $definitions = new \SimpleXMLElement($wsdl->to_xml());

        // Test portType operation node.
        $porttypeoperation = $definitions->portType->operation;
        $this->assertEquals($documentation, $porttypeoperation->documentation);
        $this->assertTrue(isset($porttypeoperation->input));
        $this->assertFalse(isset($porttypeoperation->output));

        // Test binding operation nodes.
        $bindingoperation = $definitions->binding->operation;
        // Confirm that there is no input node.
        $this->assertTrue(isset($bindingoperation->input));
        $this->assertFalse(isset($bindingoperation->output));

        // Test messages.
        // Assert there's only the output message node.
        $this->assertEquals(1, count($definitions->message));
        $messagein = $definitions->message[0];
        $this->assertEquals($functionname . wsdl::IN, $messagein->attributes()->name);

    }
}
