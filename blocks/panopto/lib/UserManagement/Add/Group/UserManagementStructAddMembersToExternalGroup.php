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
 * File for class UserManagementStructAddMembersToExternalGroup
 * @package UserManagement
 * @subpackage Structs
 * @author Panopto
 * @version 20150429-01
 * @date 2017-01-19
 */
/**
 * This class stands for UserManagementStructAddMembersToExternalGroup originally named AddMembersToExternalGroup
 * Meta informations extracted from the WSDL
 * - from schema : {@link http://demo.hosted.panopto.com/Panopto/PublicAPI/4.6/UserManagement.svc?xsd=xsd0}
 * @package UserManagement
 * @subpackage Structs
 * @author Panopto
 * @version 20150429-01
 * @date 2017-01-19
 */
class UserManagementStructAddMembersToExternalGroup extends UserManagementWsdlClass
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
     * The externalProviderName
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $externalProviderName;
    /**
     * The externalGroupId
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $externalGroupId;
    /**
     * The memberIds
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var UserManagementStructArrayOfguid
     */
    public $memberIds;
    /**
     * Constructor method for AddMembersToExternalGroup
     * @see parent::__construct()
     * @param UserManagementStructAuthenticationInfo $_auth
     * @param string $_externalProviderName
     * @param string $_externalGroupId
     * @param UserManagementStructArrayOfguid $_memberIds
     * @return UserManagementStructAddMembersToExternalGroup
     */
    public function __construct($_auth = NULL,$_externalProviderName = NULL,$_externalGroupId = NULL,$_memberIds = NULL)
    {
        parent::__construct(array('auth'=>$_auth,'externalProviderName'=>$_externalProviderName,'externalGroupId'=>$_externalGroupId,'memberIds'=>($_memberIds instanceof UserManagementStructArrayOfguid)?$_memberIds:new UserManagementStructArrayOfguid($_memberIds)),false);
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
     * Get externalProviderName value
     * @return string|null
     */
    public function getExternalProviderName()
    {
        return $this->externalProviderName;
    }
    /**
     * Set externalProviderName value
     * @param string $_externalProviderName the externalProviderName
     * @return string
     */
    public function setExternalProviderName($_externalProviderName)
    {
        return ($this->externalProviderName = $_externalProviderName);
    }
    /**
     * Get externalGroupId value
     * @return string|null
     */
    public function getExternalGroupId()
    {
        return $this->externalGroupId;
    }
    /**
     * Set externalGroupId value
     * @param string $_externalGroupId the externalGroupId
     * @return string
     */
    public function setExternalGroupId($_externalGroupId)
    {
        return ($this->externalGroupId = $_externalGroupId);
    }
    /**
     * Get memberIds value
     * @return UserManagementStructArrayOfguid|null
     */
    public function getMemberIds()
    {
        return $this->memberIds;
    }
    /**
     * Set memberIds value
     * @param UserManagementStructArrayOfguid $_memberIds the memberIds
     * @return UserManagementStructArrayOfguid
     */
    public function setMemberIds($_memberIds)
    {
        return ($this->memberIds = $_memberIds);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see UserManagementWsdlClass::__set_state()
     * @uses UserManagementWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return UserManagementStructAddMembersToExternalGroup
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
