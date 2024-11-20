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
 * WSDL generator for the SOAP web service.
 *
 * @package    webservice_soap
 * @copyright  2016 Jun Pataleta
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace webservice_soap;

/**
 * WSDL generator for the SOAP web service.
 *
 * @package    webservice_soap
 * @copyright  2016 Jun Pataleta
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class wsdl {
    /** Namespace URI for the WSDL framework. */
    const NS_WSDL = 'http://schemas.xmlsoap.org/wsdl/';

    /** Encoding namespace URI as defined by SOAP 1.1 */
    const NS_SOAP_ENC = 'http://schemas.xmlsoap.org/soap/encoding/';

    /** Namespace URI for the WSDL SOAP binding. */
    const NS_SOAP = 'http://schemas.xmlsoap.org/wsdl/soap/';

    /** Schema namespace URI as defined by XSD. */
    const NS_XSD = 'http://www.w3.org/2001/XMLSchema';

    /** WSDL namespace for the WSDL HTTP GET and POST binding. */
    const NS_SOAP_TRANSPORT = 'http://schemas.xmlsoap.org/soap/http';

    /** BINDING - string constant attached to the service class name to identify binding nodes. */
    const BINDING = 'Binding';

    /** IN - string constant attached to the function name to identify input nodes. */
    const IN = 'In';

    /** OUT - string constant attached to the function name to identify output nodes. */
    const OUT = 'Out';

    /** PORT - string constant attached to the service class name to identify port nodes. */
    const PORT = 'Port';

    /** SERVICE string constant attached to the service class name to identify service nodes. */
    const SERVICE = 'Service';

    /** @var string The name of the service class. */
    private $serviceclass;

    /** @var string The WSDL namespace. */
    private $namespace;

    /** @var array The WSDL's message nodes. */
    private $messagenodes;

    /** @var \SimpleXMLElement The WSDL's binding node. */
    private $nodebinding;

    /** @var \SimpleXMLElement The WSDL's definitions node. */
    private $nodedefinitions;

    /** @var \SimpleXMLElement The WSDL's portType node. */
    private $nodeporttype;

    /** @var \SimpleXMLElement The WSDL's service node. */
    private $nodeservice;

    /** @var \SimpleXMLElement The WSDL's types node. */
    private $nodetypes;

    /**
     * webservice_soap_wsdl constructor.
     *
     * @param string $serviceclass The service class' name.
     * @param string $namespace The WSDL namespace.
     */
    public function __construct($serviceclass, $namespace) {
        $this->serviceclass = $serviceclass;
        $this->namespace = $namespace;

        // Initialise definitions node.
        $this->nodedefinitions = new \SimpleXMLElement('<definitions />');
        $this->nodedefinitions->addAttribute('xmlns', self::NS_WSDL);
        $this->nodedefinitions->addAttribute('x:xmlns:tns', $namespace);
        $this->nodedefinitions->addAttribute('x:xmlns:soap', self::NS_SOAP);
        $this->nodedefinitions->addAttribute('x:xmlns:xsd', self::NS_XSD);
        $this->nodedefinitions->addAttribute('x:xmlns:soap-enc', self::NS_SOAP_ENC);
        $this->nodedefinitions->addAttribute('x:xmlns:wsdl', self::NS_WSDL);
        $this->nodedefinitions->addAttribute('name', $serviceclass);
        $this->nodedefinitions->addAttribute('targetNamespace', $namespace);

        // Initialise types node.
        $this->nodetypes = $this->nodedefinitions->addChild('types');
        $typeschema = $this->nodetypes->addChild('x:xsd:schema');
        $typeschema->addAttribute('targetNamespace', $namespace);

        // Initialise the portType node.
        $this->nodeporttype = $this->nodedefinitions->addChild('portType');
        $this->nodeporttype->addAttribute('name', $serviceclass . self::PORT);

        // Initialise the binding node.
        $this->nodebinding = $this->nodedefinitions->addChild('binding');
        $this->nodebinding->addAttribute('name', $serviceclass . self::BINDING);
        $this->nodebinding->addAttribute('type', 'tns:' . $serviceclass . self::PORT);
        $soapbinding = $this->nodebinding->addChild('x:soap:binding');
        $soapbinding->addAttribute('style', 'rpc');
        $soapbinding->addAttribute('transport', self::NS_SOAP_TRANSPORT);

        // Initialise the service node.
        $this->nodeservice = $this->nodedefinitions->addChild('service');
        $this->nodeservice->addAttribute('name', $serviceclass . self::SERVICE);
        $serviceport = $this->nodeservice->addChild('port');
        $serviceport->addAttribute('name', $serviceclass . self::PORT);
        $serviceport->addAttribute('binding', 'tns:' . $serviceclass . self::BINDING);
        $soapaddress = $serviceport->addChild('x:soap:address');
        $soapaddress->addAttribute('location', $namespace);

        // Initialise message nodes.
        $this->messagenodes = array();
    }

    /**
     * Adds a complex type to the WSDL.
     *
     * @param string $classname The complex type's class name.
     * @param array $properties An associative array containing the properties of the complex type class.
     */
    public function add_complex_type($classname, $properties) {
        $typeschema = $this->nodetypes->children();
        // Append the complex type.
        $complextype = $typeschema->addChild('x:xsd:complexType');
        $complextype->addAttribute('name', $classname);
        $child = $complextype->addChild('x:xsd:all');
        foreach ($properties as $name => $options) {
            $param = $child->addChild('x:xsd:element');
            $param->addAttribute('name', $name);
            $param->addAttribute('type', $this->get_soap_type($options['type']));
            if (!empty($options['nillable'])) {
                $param->addAttribute('nillable', 'true');
            }
        }
    }

    /**
     * Registers the external service method to the WSDL.
     *
     * @param string $functionname The name of the web service function to be registered.
     * @param array $inputparams Contains the function's input parameters with their associated types.
     * @param array $outputparams Contains the function's output parameters with their associated types.
     * @param string $documentation The function's description.
     */
    public function register($functionname, $inputparams = array(), $outputparams = array(), $documentation = '') {
        // Process portType operation nodes.
        $porttypeoperation = $this->nodeporttype->addChild('operation');
        $porttypeoperation->addAttribute('name', $functionname);
        // Documentation node.
        $porttypeoperation->addChild('documentation', $documentation);

        // Process binding operation nodes.
        $bindingoperation = $this->nodebinding->addChild('operation');
        $bindingoperation->addAttribute('name', $functionname);
        $soapoperation = $bindingoperation->addChild('x:soap:operation');
        $soapoperation->addAttribute('soapAction', $this->namespace . '#' . $functionname);

        // Input nodes.
        $this->process_params($functionname, $porttypeoperation, $bindingoperation, $inputparams);

        // Output nodes.
        $this->process_params($functionname, $porttypeoperation, $bindingoperation, $outputparams, true);
    }

    /**
     * Outputs the WSDL in XML format.
     *
     * @return mixed The string value of the WSDL in XML format. False, otherwise.
     */
    public function to_xml() {
        // Return WSDL in XML format.
        return $this->nodedefinitions->asXML();
    }

    /**
     * Utility method that returns the encoded SOAP type based on the given type string.
     *
     * @param string $type The input type string.
     * @return string The encoded type for the WSDL.
     */
    private function get_soap_type($type) {
        switch($type) {
            case 'int':
            case 'double':
            case 'string':
                return 'xsd:' . $type;
            case 'array':
                return 'soap-enc:Array';
            default:
                return 'tns:' . $type;
        }
    }

    /**
     * Utility method that creates input/output nodes from input/output params.
     *
     * @param string $functionname The name of the function being registered.
     * @param \SimpleXMLElement $porttypeoperation The port type operation node.
     * @param \SimpleXMLElement $bindingoperation The binding operation node.
     * @param array $params The function's input/output parameters.
     * @param bool $isoutput Flag to indicate if the nodes to be generated are for input or for output.
     */
    private function process_params($functionname, \SimpleXMLElement $porttypeoperation, \SimpleXMLElement $bindingoperation,
                                    ?array $params = null, $isoutput = false) {
        // Do nothing if parameter array is empty.
        if (empty($params)) {
            return;
        }

        $postfix = self::IN;
        $childtype = 'input';
        if ($isoutput) {
            $postfix = self::OUT;
            $childtype = 'output';
        }

        // For portType operation node.
        $child = $porttypeoperation->addChild($childtype);
        $child->addAttribute('message', 'tns:' . $functionname . $postfix);

        // For binding operation node.
        $child = $bindingoperation->addChild($childtype);
        $soapbody = $child->addChild('x:soap:body');
        $soapbody->addAttribute('use', 'encoded');
        $soapbody->addAttribute('encodingStyle', self::NS_SOAP_ENC);
        $soapbody->addAttribute('namespace', $this->namespace);

        // Process message nodes.
        $messagein = $this->nodedefinitions->addChild('message');
        $messagein->addAttribute('name', $functionname . $postfix);
        foreach ($params as $name => $options) {
            $part = $messagein->addChild('part');
            $part->addAttribute('name', $name);
            $part->addAttribute('type', $this->get_soap_type($options['type']));
        }
    }
}
