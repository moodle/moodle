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
 * File for class UserManagementStructAuthenticationInfo
 * @package UserManagement
 * @subpackage Structs
 * @author Panopto
 * @version 20150429-01
 * @date 2017-01-19
 */
/**
 * This class stands for UserManagementStructAuthenticationInfo originally named AuthenticationInfo
 * Meta informations extracted from the WSDL
 * - from schema : {@link http://demo.hosted.panopto.com/Panopto/PublicAPI/4.6/UserManagement.svc?xsd=xsd2}
 * @package UserManagement
 * @subpackage Structs
 * @author Panopto
 * @version 20150429-01
 * @date 2017-01-19
 */
class UserManagementStructAuthenticationInfo extends UserManagementWsdlClass
{
    /**
     * The AuthCode
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $AuthCode;
    /**
     * The Password
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $Password;
    /**
     * The UserKey
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $UserKey;
    /**
     * Constructor method for AuthenticationInfo
     * @see parent::__construct()
     * @param string $_authCode
     * @param string $_password
     * @param string $_userKey
     * @return UserManagementStructAuthenticationInfo
     */
    public function __construct($_authCode = NULL,$_password = NULL,$_userKey = NULL)
    {
        parent::__construct(array('AuthCode'=>$_authCode,'Password'=>$_password,'UserKey'=>$_userKey),false);
    }
    /**
     * Get AuthCode value
     * @return string|null
     */
    public function getAuthCode()
    {
        return $this->AuthCode;
    }
    /**
     * Set AuthCode value
     * @param string $_authCode the AuthCode
     * @return string
     */
    public function setAuthCode($_authCode)
    {
        return ($this->AuthCode = $_authCode);
    }
    /**
     * Get Password value
     * @return string|null
     */
    public function getPassword()
    {
        return $this->Password;
    }
    /**
     * Set Password value
     * @param string $_password the Password
     * @return string
     */
    public function setPassword($_password)
    {
        return ($this->Password = $_password);
    }
    /**
     * Get UserKey value
     * @return string|null
     */
    public function getUserKey()
    {
        return $this->UserKey;
    }
    /**
     * Set UserKey value
     * @param string $_userKey the UserKey
     * @return string
     */
    public function setUserKey($_userKey)
    {
        return ($this->UserKey = $_userKey);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see UserManagementWsdlClass::__set_state()
     * @uses UserManagementWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return UserManagementStructAuthenticationInfo
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
