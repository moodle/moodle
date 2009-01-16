<?php
/*
 * Generate the Moodle WSDL file
 * This file is not use neither finish but will give you a base to start
 */

require_once('../../config.php');
$wsdl_generator = new wsdl_generator();
$wsdl = $wsdl_generator->generate_wsdl();
echo $wsdl;

/**
 * WORK IN PROGRESS - Generator not working yet
 */
class wsdl_generator {

    private $exceptionlist;

    //private $wsdl;

    function __construct () {
        // The exception list
        // if ever there is some wsapi.php file that are not a web service class, they need to be declared here
        // example: $this->exceptionlist['/home/jerome/Projects/Moodle_HEAD/moodle/mod/scorm/wsapi.php'] = true;
        $this->exceptionlist = array();
    }

    /**
     * Generate the WSDL for Moodle API
     * @global <type> $CFG
     * @return string wsdl xml
     */
    public function generate_wsdl () {
        global $CFG;

     ///initialize different wsdl part
        $wsdlmessage = "";
        $wsdlporttype = "";
        $wsdlbinding = "";
        $wsdlservice = "";

     ///retrieve al api.php file
        $listfiles = array();
        $this->setListApiFiles( $listfiles, $CFG->dirroot);

     ///WSDL header
        $wsdl = <<<EOF
<?xml version ='1.0' encoding ='UTF-8' ?>
    <definitions name='User'
                 targetNamespace='http://example.org/User'
                 xmlns:tns=' http://example.org/User '
                 xmlns:soap='http://schemas.xmlsoap.org/wsdl/soap/'
                 xmlns:xsd='http://www.w3.org/2001/XMLSchema'
                 xmlns:soapenc='http://schemas.xmlsoap.org/soap/encoding/'
                 xmlns:wsdl='http://schemas.xmlsoap.org/wsdl/'
                 xmlns='http://schemas.xmlsoap.org/wsdl/'>

EOF;

       $wsdltypes = <<<EOF

        <types>
            <xsd:schema targetNamespace="http://example.org/User"
                        xmlns="http://www.w3.org/2001/XMLSchema">
                <xsd:complexType name="object">
                </xsd:complexType>
            </xsd:schema>
        </types>


EOF;

        foreach ($listfiles as $fileapipath) {
            require_once($fileapipath);

         ///load the class        
            $classpath = substr($fileapipath,strlen($CFG->dirroot)+1); //remove the dir root + / from the file path
            $classpath = substr($classpath,0,strlen($classpath) - 10); //remove /wsapi.php from the classpath
            $classpath = str_replace('/','_',$classpath); //convert all / into _
            $classname = $classpath."_ws_api";
            $api = new $classname();

             $wsdlporttype .= <<<EOF
        <portType name='{$classpath}PortType'>
EOF;
             $wsdlbinding .= <<<EOF
        <binding name='{$classpath}Binding' type='tns:{$classpath}PortType'>
            <soap:binding style='rpc'
                          transport='http://schemas.xmlsoap.org/soap/http'/>

EOF;
             $wsdlservice .= <<<EOF
        <service name='{$classpath}Service'>
                <port name='{$classpath}Port' binding='{$classpath}Binding'>
                    <soap:address location='{$CFG->wwwroot}/webservice/soap/server.php?classpath={$classpath}'/>
                </port>
        </service>

EOF;
             foreach($api->get_descriptions() as $functionname => $description) {


             $wsdlmessage .= <<<EOF
        <message name="{$functionname}Request">

EOF;
            foreach ($description['wsparams'] as $param => $paramtype) {
                $wsparamtype = $this->converterMoodleParamIntoWsParam($paramtype);
                $wsdlmessage .= <<<EOF
            <part name="{$param}" type="xsd:{$wsparamtype}"/>

EOF;
            }
             $wsdlmessage .= <<<EOF
        </message>
        <message name="{$functionname}Response">

EOF;
            foreach ($description['return'] as $param => $paramtype) {
                $wsparamtype = $this->converterMoodleParamIntoWsParam($paramtype);
                $wsdlmessage .= <<<EOF
            <part name="{$param}" type="xsd:{$wsparamtype}"/>

EOF;
            }
             $wsdlmessage .= <<<EOF
        </message>

EOF;

             $wsdlporttype .= <<<EOF

            <operation name='{$functionname}'>
                <input message='tns:{$functionname}Request'/>
                <output message='tns:{$functionname}Response'/>
            </operation>

EOF;
             $wsdlbinding .= <<<EOF

            <operation name='{$functionname}'>
                <soap:operation soapAction='urn:xmethods-delayed-quotes#{$functionname}'/>
                <input>
                    <soap:body use='encoded' namespace='urn:xmethods-delayed-quotes'
                               encodingStyle='http://schemas.xmlsoap.org/soap/encoding/'/>
                </input>
                <output>
                    <soap:body use='encoded' namespace='urn:xmethods-delayed-quotes'
                               encodingStyle='http://schemas.xmlsoap.org/soap/encoding/'/>
                </output>
           </operation>

EOF;
            }
            $wsdlporttype .= <<<EOF
        </portType>


EOF;
            $wsdlbinding .= <<<EOF
        </binding>


EOF;
        }

     ///write WSDL
        $wsdl .= $wsdltypes;
        $wsdl .= $wsdlmessage;
        $wsdl .= $wsdlporttype;
        $wsdl .= $wsdlbinding;
        $wsdl .= $wsdlservice;

     ///WSDL footer
        $wsdl .= <<<EOF
    </definitions>

EOF;

        $this->wsdl =  $wsdl;
        return $wsdl;
        //$this->writewsdl();
    }


    /**
     * Retrieve all api.php from Moodle (except the one of the exception list)
     * @param <type> $
     * @param <type> $directorypath
     * @return boolean true if n
     */
    private function setListApiFiles( &$files, $directorypath )
    {
        $generatewsdl = true;
        if(is_dir($directorypath)){ //check that we are browsing a folder not a file

            if( $dh = opendir($directorypath))
            {
                while( false !== ($file = readdir($dh)))
                {

                    if( $file == '.' || $file == '..') {   // Skip '.' and '..'
                        continue;
                    }
                    $path = $directorypath . '/' . $file;
                 ///browse the subfolder
                    if( is_dir($path) ) {
                         $this->setListApiFiles($files, $path);
                    }
                 ///retrieve api.php file
                    else if ($file == "wsapi.php" && ! $this->inExceptionList($path)) {
                        $files[] = $path;
                    }
                }
                closedir($dh);

            }
        }

        return $generatewsdl;

    }

    /**
     * Hacky function
     * We need to define if we remove all wsapi.php file from Moodle when they do not really
     * are ws api file for Moodle ws API
     * @param string $path
     * @return boolean true if the path if in the exception list
     */
    private function inExceptionList($path) {
        return (!empty( $this->exceptionlist[$path]));
    }

    /**
     * Convert a Moodle type (PARAM_ALPHA, PARAM_NUMBER,...) as a SOAP type (string, interger,...)
     * @param integer $moodleparam
     * @return string  SOAP type
     */
    private function converterMoodleParamIntoWsParam($moodleparam) {
        switch ($moodleparam) {
            case PARAM_NUMBER:
                return "integer";
                break;
            case PARAM_ALPHANUM:
                return "string";
                break;
            case PARAM_RAW:
                return "string";
                break;
            default:
                return "object";
                break;
        }
    }
/*
    private function writewsdl() {
        $fp = fopen('moodle.wsdl', 'w');
        fwrite($fp, $this->wsdl);
        fclose($fp);
    }
*/

}

?>
