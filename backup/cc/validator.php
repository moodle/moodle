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
 * Provides validation classes used by the imscc converters
 *
 * @package    backup-convert
 * @copyright  2011 Darko Miletic <dmiletic@moodlerooms.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

final class error_messages {
    /**
     *
     * @static error_messages
     */
    private static $instance = null;
    private function __construct(){}
    private function __clone(){}
    /**
     * @return error_messages
     */
    public static function instance() {
        if (empty(self::$instance)) {
            $c = __CLASS__;
            self::$instance = new $c();
        }
        return self::$instance;
    }

    /**
     * @var array
     */
    private $items = array();

    /**
     * @param string $msg
     */
    public function add($msg) {
        if (!empty($msg)) {
            $this->items[] = $msg;
        }
    }

    /**
     * @return array
     */
    public function errors() {
        $this->items;
    }

    /**
     * Empties the error content
     */
    public function reset() {
        $this->items = array();
    }

    /**
     * @param boolean $web
     * @return string
     */
    public function to_string($web = false) {
        $result = '';
        if ($web) {
            $result .= '<ol>'.PHP_EOL;
        }
        foreach ($this->items as $error) {
            if ($web) {
                $result .= '<li>';
            }

            $result .= $error.PHP_EOL;

            if ($web) {
                $result .= '</li>'.PHP_EOL;
            }
        }
        if ($web) {
            $result .= '</ol>'.PHP_EOL;
        }
        return $result;
    }

    /**
     * Casting to string method
     * @return string
     */
    public function __toString() {
        return $this->to_string(false);
    }

}

final class libxml_errors_mgr {
    /**
     * @var boolean
     */
    private $previous = false;

    /**
     * @param boolean $reset
     */
    public function __construct($reset=false){
        if ($reset) {
            error_messages::instance()->reset();
        }
        $this->previous = libxml_use_internal_errors(true);
        libxml_clear_errors();
    }

    private function collect_errors ($filename=''){
        $errors = libxml_get_errors();
        static $error_types = array(
        LIBXML_ERR_ERROR => 'Error'
        ,LIBXML_ERR_FATAL => 'Fatal Error'
        ,LIBXML_ERR_WARNING => 'Warning'
        );
        $result = array();
        foreach($errors as $error){
            $add = '';
            if (!empty($filename)) {
                $add = " in {$filename}";
            } elseif (!empty($error->file)) {
                $add = " in {$error->file}";
            }
            $line = '';
            if (!empty($error->line)) {
                $line = " at line {$error->line}";
            }
            $err = "{$error_types[$error->level]}{$add}: {$error->message}{$line}";
            error_messages::instance()->add($err);
        }
        libxml_clear_errors();
        return $result;
    }

    public function __destruct(){
        $this->collect_errors();
        if (!$this->previous) {
            libxml_use_internal_errors($this->previous);
        }
    }

    public function collect() {
        $this->collect_errors();
    }
}


function validate_xml($xml, $schema){
    $result = false;
    $manifest_file = realpath($xml);
    $schema_file = realpath($schema);
    if (empty($manifest_file) || empty($schema_file)) {
        return false;
    }

    $xml_error = new libxml_errors_mgr();
    $manifest = new DOMDocument();
    $doc->validateOnParse = false;
    $result = $manifest->load($manifest_file, LIBXML_NONET) &&
              $manifest->schemaValidate($schema_file);

    return $result;
}

class cc_validate_type {
    const manifest_validator1   = 'cclibxml2validator.xsd'                       ;
    const assesment_validator1  = '/domainProfile_4/ims_qtiasiv1p2_localised.xsd';
    const discussion_validator1 = '/domainProfile_6/imsdt_v1p0_localised.xsd'    ;
    const weblink_validator1    = '/domainProfile_5/imswl_v1p0_localised.xsd'    ;

    const manifest_validator11   = 'cc11libxml2validator.xsd'    ;
    const blti_validator11       = 'imslticc_v1p0p1.xsd'         ;
    const assesment_validator11  = 'ccv1p1_qtiasiv1p2p1_v1p0.xsd';
    const discussion_validator11 = 'ccv1p1_imsdt_v1p1.xsd'       ;
    const weblink_validator11    = 'ccv1p1_imswl_v1p1.xsd'       ;

    /**
     * @var string
     */
    protected $type = null;

    /**
     * @var string
     */
    protected $location = null;

    public function __construct($type, $location){
        $this->type = $type;
        $this->location = $location;
    }

    /**
     * Validates the item
     * @param  string $element - File path for the xml
     * @return boolean
     */
    public function validate($element) {
        $celement   = realpath($element);
        $cvalidator = realpath($this->location.DIRECTORY_SEPARATOR.$this->type);
        $result = (empty($celement) || empty($cvalidator));
        if (!$result) {
            $xml_error = new libxml_errors_mgr();
            $doc = new DOMDocument();
            $doc->validateOnParse = false;
            $result = $doc->load($celement, LIBXML_NONET) &&
                      $doc->schemaValidate($cvalidator);
        }
        return $result;
    }

}

class manifest_validator extends cc_validate_type {
    public function __construct($location){
        parent::__construct(self::manifest_validator11, $location);
    }
}

class manifest10_validator extends cc_validate_type {
    public function __construct($location){
        parent::__construct(self::manifest_validator1, $location);
    }
}

class blti_validator extends cc_validate_type {
    public function __construct($location){
        parent::__construct(self::blti_validator11, $location);
    }
}

class assesment_validator extends cc_validate_type {
    public function __construct($location){
        parent::__construct(self::assesment_validator11, $location);
    }
}

class discussion_validator extends cc_validate_type {
    public function __construct($location){
        parent::__construct(self::discussion_validator11, $location);
    }
}

class weblink_validator extends cc_validate_type {
    public function __construct($location){
        parent::__construct(self::weblink_validator11, $location);
    }
}

