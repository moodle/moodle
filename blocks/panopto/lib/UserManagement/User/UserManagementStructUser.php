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
 * File for class UserManagementStructUser
 * @package UserManagement
 * @subpackage Structs
 * @author Panopto
 * @version 20150429-01
 * @date 2017-01-19
 */
/**
 * This class stands for UserManagementStructUser originally named User
 * Meta informations extracted from the WSDL
 * - from schema : {@link http://demo.hosted.panopto.com/Panopto/PublicAPI/4.6/UserManagement.svc?xsd=xsd2}
 * @package UserManagement
 * @subpackage Structs
 * @author Panopto
 * @version 20150429-01
 * @date 2017-01-19
 */
class UserManagementStructUser extends UserManagementWsdlClass
{
    /**
     * The Email
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $Email;
    /**
     * The EmailSessionNotifications
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var boolean
     */
    public $EmailSessionNotifications;
    /**
     * The FirstName
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $FirstName;
    /**
     * The GroupMemberships
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var UserManagementStructArrayOfguid
     */
    public $GroupMemberships;
    /**
     * The LastName
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $LastName;
    /**
     * The SystemRole
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var UserManagementEnumSystemRole
     */
    public $SystemRole;
    /**
     * The UserBio
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $UserBio;
    /**
     * The UserId
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - pattern : [\da-fA-F]{8}-[\da-fA-F]{4}-[\da-fA-F]{4}-[\da-fA-F]{4}-[\da-fA-F]{12}
     * @var string
     */
    public $UserId;
    /**
     * The UserKey
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $UserKey;
    /**
     * The UserSettingsUrl
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $UserSettingsUrl;
    /**
     * Constructor method for User
     * @see parent::__construct()
     * @param string $_email
     * @param boolean $_emailSessionNotifications
     * @param string $_firstName
     * @param UserManagementStructArrayOfguid $_groupMemberships
     * @param string $_lastName
     * @param UserManagementEnumSystemRole $_systemRole
     * @param string $_userBio
     * @param string $_userId
     * @param string $_userKey
     * @param string $_userSettingsUrl
     * @return UserManagementStructUser
     */
    public function __construct($_email = NULL,$_emailSessionNotifications = NULL,$_firstName = NULL,$_groupMemberships = NULL,$_lastName = NULL,$_systemRole = NULL,$_userBio = NULL,$_userId = NULL,$_userKey = NULL,$_userSettingsUrl = NULL)
    {
        parent::__construct(array('Email'=>$_email,'EmailSessionNotifications'=>$_emailSessionNotifications,'FirstName'=>$_firstName,'GroupMemberships'=>($_groupMemberships instanceof UserManagementStructArrayOfguid)?$_groupMemberships:new UserManagementStructArrayOfguid($_groupMemberships),'LastName'=>$_lastName,'SystemRole'=>$_systemRole,'UserBio'=>$_userBio,'UserId'=>$_userId,'UserKey'=>$_userKey,'UserSettingsUrl'=>$_userSettingsUrl),false);
    }
    /**
     * Get Email value
     * @return string|null
     */
    public function getEmail()
    {
        return $this->Email;
    }
    /**
     * Set Email value
     * @param string $_email the Email
     * @return string
     */
    public function setEmail($_email)
    {
        return ($this->Email = $_email);
    }
    /**
     * Get EmailSessionNotifications value
     * @return boolean|null
     */
    public function getEmailSessionNotifications()
    {
        return $this->EmailSessionNotifications;
    }
    /**
     * Set EmailSessionNotifications value
     * @param boolean $_emailSessionNotifications the EmailSessionNotifications
     * @return boolean
     */
    public function setEmailSessionNotifications($_emailSessionNotifications)
    {
        return ($this->EmailSessionNotifications = $_emailSessionNotifications);
    }
    /**
     * Get FirstName value
     * @return string|null
     */
    public function getFirstName()
    {
        return $this->FirstName;
    }
    /**
     * Set FirstName value
     * @param string $_firstName the FirstName
     * @return string
     */
    public function setFirstName($_firstName)
    {
        return ($this->FirstName = $_firstName);
    }
    /**
     * Get GroupMemberships value
     * @return UserManagementStructArrayOfguid|null
     */
    public function getGroupMemberships()
    {
        return $this->GroupMemberships;
    }
    /**
     * Set GroupMemberships value
     * @param UserManagementStructArrayOfguid $_groupMemberships the GroupMemberships
     * @return UserManagementStructArrayOfguid
     */
    public function setGroupMemberships($_groupMemberships)
    {
        return ($this->GroupMemberships = $_groupMemberships);
    }
    /**
     * Get LastName value
     * @return string|null
     */
    public function getLastName()
    {
        return $this->LastName;
    }
    /**
     * Set LastName value
     * @param string $_lastName the LastName
     * @return string
     */
    public function setLastName($_lastName)
    {
        return ($this->LastName = $_lastName);
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
     * Get UserBio value
     * @return string|null
     */
    public function getUserBio()
    {
        return $this->UserBio;
    }
    /**
     * Set UserBio value
     * @param string $_userBio the UserBio
     * @return string
     */
    public function setUserBio($_userBio)
    {
        return ($this->UserBio = $_userBio);
    }
    /**
     * Get UserId value
     * @return string|null
     */
    public function getUserId()
    {
        return $this->UserId;
    }
    /**
     * Set UserId value
     * @param string $_userId the UserId
     * @return string
     */
    public function setUserId($_userId)
    {
        return ($this->UserId = $_userId);
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
     * Get UserSettingsUrl value
     * @return string|null
     */
    public function getUserSettingsUrl()
    {
        return $this->UserSettingsUrl;
    }
    /**
     * Set UserSettingsUrl value
     * @param string $_userSettingsUrl the UserSettingsUrl
     * @return string
     */
    public function setUserSettingsUrl($_userSettingsUrl)
    {
        return ($this->UserSettingsUrl = $_userSettingsUrl);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see UserManagementWsdlClass::__set_state()
     * @uses UserManagementWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return UserManagementStructUser
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
