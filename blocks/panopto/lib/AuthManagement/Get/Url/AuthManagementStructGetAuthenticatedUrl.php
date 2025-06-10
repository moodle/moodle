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
 *
 * @package block_panopto
 * @copyright Panopto 2020
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
 /**
 * File for class AuthManagementStructGetAuthenticatedUrl
 * @package AuthManagement
 * @subpackage Structs
 * @author Panopto
 * @version 20150429-01
 * @date 2017-05-25
 */
/**
 * This class stands for AuthManagementStructGetAuthenticatedUrl originally named GetAuthenticatedUrl
 * Meta informations extracted from the WSDL
 * - from schema : {@link http://demo.hosted.panopto.com/Panopto/PublicAPI/4.2/Auth.svc?xsd=xsd0}
 * @package AuthManagement
 * @subpackage Structs
 * @author Panopto
 * @version 20150429-01
 * @date 2017-05-25
 */
class AuthManagementStructGetAuthenticatedUrl extends AuthManagementWsdlClass
{
    /**
     * The auth
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var AuthManagementStructAuthenticationInfo
     */
    public $auth;
    /**
     * The targetUrl
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $targetUrl;
    /**
     * Constructor method for GetAuthenticatedUrl
     * @see parent::__construct()
     * @param AuthManagementStructAuthenticationInfo $_auth
     * @param string $_targetUrl
     * @return AuthManagementStructGetAuthenticatedUrl
     */
    public function __construct($_auth = NULL,$_targetUrl = NULL)
    {
        parent::__construct(array('auth'=>$_auth,'targetUrl'=>$_targetUrl),false);
    }
    /**
     * Get auth value
     * @return AuthManagementStructAuthenticationInfo|null
     */
    public function getAuth()
    {
        return $this->auth;
    }
    /**
     * Set auth value
     * @param AuthManagementStructAuthenticationInfo $_auth the auth
     * @return AuthManagementStructAuthenticationInfo
     */
    public function setAuth($_auth)
    {
        return ($this->auth = $_auth);
    }
    /**
     * Get targetUrl value
     * @return string|null
     */
    public function getTargetUrl()
    {
        return $this->targetUrl;
    }
    /**
     * Set targetUrl value
     * @param string $_targetUrl the targetUrl
     * @return string
     */
    public function setTargetUrl($_targetUrl)
    {
        return ($this->targetUrl = $_targetUrl);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see AuthManagementWsdlClass::__set_state()
     * @uses AuthManagementWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return AuthManagementStructGetAuthenticatedUrl
     */
    public static function __set_state(array $_array)
    {
        return parent::__set_state($_array);
    }
    /**
     * Method returning the class name
     * @return string __CLASS__
     */
    public function __toString()
    {
        return __CLASS__;
    }
}
