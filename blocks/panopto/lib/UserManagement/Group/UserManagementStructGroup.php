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
 * File for class UserManagementStructGroup
 * @package UserManagement
 * @subpackage Structs
 * @author Panopto
 * @version 20150429-01
 * @date 2017-01-19
 */
/**
 * This class stands for UserManagementStructGroup originally named Group
 * Meta informations extracted from the WSDL
 * - from schema : {@link http://demo.hosted.panopto.com/Panopto/PublicAPI/4.6/UserManagement.svc?xsd=xsd2}
 * @package UserManagement
 * @subpackage Structs
 * @author Panopto
 * @version 20150429-01
 * @date 2017-01-19
 */
class UserManagementStructGroup extends UserManagementWsdlClass
{
    /**
     * The ExternalId
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $ExternalId;
    /**
     * The GroupType
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var UserManagementEnumGroupType
     */
    public $GroupType;
    /**
     * The Id
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - pattern : [\da-fA-F]{8}-[\da-fA-F]{4}-[\da-fA-F]{4}-[\da-fA-F]{4}-[\da-fA-F]{12}
     * @var string
     */
    public $Id;
    /**
     * The MembershipProviderName
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $MembershipProviderName;
    /**
     * The Name
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $Name;
    /**
     * The SystemRole
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var UserManagementEnumSystemRole
     */
    public $SystemRole;
    /**
     * Constructor method for Group
     * @see parent::__construct()
     * @param string $_externalId
     * @param UserManagementEnumGroupType $_groupType
     * @param string $_id
     * @param string $_membershipProviderName
     * @param string $_name
     * @param UserManagementEnumSystemRole $_systemRole
     * @return UserManagementStructGroup
     */
    public function __construct($_externalId = NULL,$_groupType = NULL,$_id = NULL,$_membershipProviderName = NULL,$_name = NULL,$_systemRole = NULL)
    {
        parent::__construct(array('ExternalId'=>$_externalId,'GroupType'=>$_groupType,'Id'=>$_id,'MembershipProviderName'=>$_membershipProviderName,'Name'=>$_name,'SystemRole'=>$_systemRole),false);
    }
    /**
     * Get ExternalId value
     * @return string|null
     */
    public function getExternalId()
    {
        return $this->ExternalId;
    }
    /**
     * Set ExternalId value
     * @param string $_externalId the ExternalId
     * @return string
     */
    public function setExternalId($_externalId)
    {
        return ($this->ExternalId = $_externalId);
    }
    /**
     * Get GroupType value
     * @return UserManagementEnumGroupType|null
     */
    public function getGroupType()
    {
        return $this->GroupType;
    }
    /**
     * Set GroupType value
     * @uses UserManagementEnumGroupType::valueIsValid()
     * @param UserManagementEnumGroupType $_groupType the GroupType
     * @return UserManagementEnumGroupType
     */
    public function setGroupType($_groupType)
    {
        if(!UserManagementEnumGroupType::valueIsValid($_groupType))
        {
            return false;
        }
        return ($this->GroupType = $_groupType);
    }
    /**
     * Get Id value
     * @return string|null
     */
    public function getId()
    {
        return $this->Id;
    }
    /**
     * Set Id value
     * @param string $_id the Id
     * @return string
     */
    public function setId($_id)
    {
        return ($this->Id = $_id);
    }
    /**
     * Get MembershipProviderName value
     * @return string|null
     */
    public function getMembershipProviderName()
    {
        return $this->MembershipProviderName;
    }
    /**
     * Set MembershipProviderName value
     * @param string $_membershipProviderName the MembershipProviderName
     * @return string
     */
    public function setMembershipProviderName($_membershipProviderName)
    {
        return ($this->MembershipProviderName = $_membershipProviderName);
    }
    /**
     * Get Name value
     * @return string|null
     */
    public function getName()
    {
        return $this->Name;
    }
    /**
     * Set Name value
     * @param string $_name the Name
     * @return string
     */
    public function setName($_name)
    {
        return ($this->Name = $_name);
    }
    /**
     * Get SystemRole value
     * @return UserManagementEnumSystemRole|null
     */
    public function getSystemRole()
    {
        return $this->SystemRole;
    }
    /**
     * Set SystemRole value
     * @uses UserManagementEnumSystemRole::valueIsValid()
     * @param UserManagementEnumSystemRole $_systemRole the SystemRole
     * @return UserManagementEnumSystemRole
     */
    public function setSystemRole($_systemRole)
    {
        if(!UserManagementEnumSystemRole::valueIsValid($_systemRole))
        {
            return false;
        }
        return ($this->SystemRole = $_systemRole);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see UserManagementWsdlClass::__set_state()
     * @uses UserManagementWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return UserManagementStructGroup
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
