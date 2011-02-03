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
 * REST web service implementation classes and methods.
 *
 * @package   webservice
 * @copyright 2009 Moodle Pty Ltd (http://moodle.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("$CFG->dirroot/webservice/lib.php");

/**
 * REST service server implementation.
 * @author Petr Skoda (skodak)
 */
class webservice_rest_server extends webservice_base_server {
    /**
     * Contructor
     */
    public function __construct($authmethod) {
        parent::__construct($authmethod);
        $this->wsname = 'rest';
    }

    /**
     * This method parses the $_REQUEST superglobal and looks for
     * the following information:
     *  1/ user authentication - username+password or token (wsusername, wspassword and wstoken parameters)
     *  2/ function name (wsfunction parameter)
     *  3/ function parameters (all other parameters except those above)
     *
     * @return void
     */
    protected function parse_request() {
        if ($this->authmethod == WEBSERVICE_AUTHMETHOD_USERNAME) {
            $this->username = isset($_REQUEST['wsusername']) ? $_REQUEST['wsusername'] : null;
            unset($_REQUEST['wsusername']);

            $this->password = isset($_REQUEST['wspassword']) ? $_REQUEST['wspassword'] : null;
            unset($_REQUEST['wspassword']);

            $this->functionname = isset($_REQUEST['wsfunction']) ? $_REQUEST['wsfunction'] : null;
            unset($_REQUEST['wsfunction']);

            $this->parameters = $_REQUEST;

        } else {
            $this->token = isset($_REQUEST['wstoken']) ? $_REQUEST['wstoken'] : null;
            unset($_REQUEST['wstoken']);

            $this->functionname = isset($_REQUEST['wsfunction']) ? $_REQUEST['wsfunction'] : null;
            unset($_REQUEST['wsfunction']);

            $this->parameters = $_REQUEST;
        }
    }

    /**
     * Send the result of function call to the WS client
     * formatted as XML document.
     * @return void
     */
    protected function send_response() {
        $this->send_headers();
        $xml = '<?xml version="1.0" encoding="UTF-8" ?>'."\n";
        $xml .= '<RESPONSE>'."\n";
        $xml .= self::xmlize_result($this->returns, $this->function->returns_desc);
        $xml .= '</RESPONSE>'."\n";
        echo $xml;
    }

    /**
     * Send the error information to the WS client
     * formatted as XML document.
     * @param exception $ex
     * @return void
     */
    protected function send_error($ex=null) {
        $this->send_headers();
        $xml = '<?xml version="1.0" encoding="UTF-8" ?>'."\n";
        $xml .= '<EXCEPTION class="'.get_class($ex).'">'."\n";
        $xml .= '<MESSAGE>'.htmlentities($ex->getMessage(), ENT_COMPAT, 'UTF-8').'</MESSAGE>'."\n";
        if (debugging() and isset($ex->debuginfo)) {
            $xml .= '<DEBUGINFO>'.htmlentities($ex->debuginfo, ENT_COMPAT, 'UTF-8').'</DEBUGINFO>'."\n";
        }
        $xml .= '</EXCEPTION>'."\n";
        echo $xml;
    }

    /**
     * Internal implementation - sending of page headers.
     * @return void
     */
    protected function send_headers() {
        header('Content-Type: application/xml; charset=utf-8');
        header('Content-Disposition: inline; filename="response.xml"');
        header('Cache-Control: private, must-revalidate, pre-check=0, post-check=0, max-age=0');
        header('Expires: '. gmdate('D, d M Y H:i:s', 0) .' GMT');
        header('Pragma: no-cache');
        header('Accept-Ranges: none');
    }

    /**
     * Internal implementation - recursive function producing XML markup.
     * @param mixed $returns
     * @param $desc
     * @return unknown_type
     */
    protected static function xmlize_result($returns, $desc) {
        if ($desc === null) {
            return '';

        } else if ($desc instanceof external_value) {
            if (is_bool($returns)) {
                // we want 1/0 instead of true/false here
                $returns = (int)$returns;
            }
            if (is_null($returns)) {
                return '<VALUE null="null"/>'."\n";
            } else {
                return '<VALUE>'.htmlentities($returns, ENT_COMPAT, 'UTF-8').'</VALUE>'."\n";
            }

        } else if ($desc instanceof external_multiple_structure) {
            $mult = '<MULTIPLE>'."\n";
            if (!empty($returns)) {
                foreach ($returns as $val) {
                    $mult .= self::xmlize_result($val, $desc->content);
                }
            }
            $mult .= '</MULTIPLE>'."\n";
            return $mult;

        } else if ($desc instanceof external_single_structure) {
            $single = '<SINGLE>'."\n";
            foreach ($desc->keys as $key=>$subdesc) {
                if (!array_key_exists($key, $returns)) {
                    if ($subdesc->required == VALUE_REQUIRED) {
                        $single .= '<ERROR>Missing required key "'.$key.'"</ERROR>';
                        continue;
                    } else {
                        //optional field
                        continue;
                    }
                }
                $single .= '<KEY name="'.$key.'">'.self::xmlize_result($returns[$key], $subdesc).'</KEY>'."\n";
            }
            $single .= '</SINGLE>'."\n";
            return $single;
        }
    }
}


/**
 * REST test client class
 */
class webservice_rest_test_client implements webservice_test_client_interface {
    /**
     * Execute test client WS request
     * @param string $serverurl
     * @param string $function
     * @param array $params
     * @return mixed
     */
    public function simpletest($serverurl, $function, $params) {
        return download_file_content($serverurl.'&wsfunction='.$function, null, $params);
    }
}