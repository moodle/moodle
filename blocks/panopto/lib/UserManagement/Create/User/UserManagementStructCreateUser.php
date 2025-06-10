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
 * File for class UserManagementStructCreateUser
 * @package UserManagement
 * @subpackage Structs
 * @author Panopto
 * @version 20150429-01
 * @date 2017-01-19
 */
/**
 * This class stands for UserManagementStructCreateUser originally named CreateUser
 * Meta informations extracted from the WSDL
 * - from schema : {@link http://demo.hosted.panopto.com/Panopto/PublicAPI/4.6/UserManagement.svc?xsd=xsd0}
 * @package UserManagement
 * @subpackage Structs
 * @author Panopto
 * @version 20150429-01
 * @date 2017-01-19
 */
class UserManagementStructCreateUser extends UserManagementWsdlClass
{
    /**
     * The auth
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var UserManagementStructAuthenticationInfo
     */
    public $auth;
    /**
     * The user
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var UserManagementStructUser
     */
    public $user;
    /**
     * The initialPassword
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $initialPassword;
    /**
     * Constructor method for CreateUser
     * @see parent::__construct()
     * @param UserManagementStructAuthenticationInfo $_auth
     * @param UserManagementStructUser $_user
     * @param string $_initialPassword
     * @return UserManagementStructCreateUser
     */
    public function __construct($_auth = NULL,$_user = NULL,$_initialPassword = NULL)
    {
        parent::__construct(array('auth'=>$_auth,'user'=>$_user,'initialPassword'=>$_initialPassword),false);
    }
    /**
     * Get auth value
     * @return UserManagementStructAuthenticationInfo|null
     */
    public function getAuth()
    {
        return $this->auth;
    }
    /**
     * Set auth value
     * @param UserManagementStructAuthenticationInfo $_auth the auth
     * @return UserManagementStructAuthenticationInfo
     */
    public function setAuth($_auth)
    {
        return ($this->auth = $_auth);
    }
    /**
     * Get user value
     * @return UserManagementStructUser|null
     */
    public function getUser()
    {
        return $this->user;
    }
    /**
     * Set user value
     * @param UserManagementStructUser $_user the user
     * @return UserManagementStructUser
     */
    public function setUser($_user)
    {
        return ($this->user = $_user);
    }
    /**
     * Get initialPassword value
     * @return string|null
     */
    public function getInitialPassword()
    {
        return $this->initialPassword;
    }
    /**
     * Set initialPassword value
     * @param string $_initialPassword the initialPassword
     * @return string
     */
    public function setInitialPassword($_initialPassword)
    {
        return ($this->initialPassword = $_initialPassword);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see UserManagementWsdlClass::__set_state()
     * @uses UserManagementWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return UserManagementStructCreateUser
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
