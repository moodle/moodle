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
 * File for class SessionManagementStructProvisionExternalCourseWithRoles
 * @package SessionManagement
 * @subpackage Structs
 * @author Panopto
 * @version 20150429-01
 * @date 2017-01-19
 */
/**
 * This class stands for SessionManagementStructProvisionExternalCourseWithRoles originally named ProvisionExternalCourseWithRoles
 * Meta informations extracted from the WSDL
 * - from schema : {@link http://demo.hosted.panopto.com/Panopto/PublicAPI/4.6/SessionManagement.svc?xsd=xsd0}
 * @package SessionManagement
 * @subpackage Structs
 * @author Panopto
 * @version 20150429-01
 * @date 2017-01-19
 */
class SessionManagementStructProvisionExternalCourseWithRoles extends SessionManagementWsdlClass
{
    /**
     * The auth
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var SessionManagementStructAuthenticationInfo
     */
    public $auth;
    /**
     * The name
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $name;
    /**
     * The externalId
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $externalId;
    /**
     * The roles
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var SessionManagementStructArrayOfAccessRole
     */
    public $roles;
    /**
     * Constructor method for ProvisionExternalCourseWithRoles
     * @see parent::__construct()
     * @param SessionManagementStructAuthenticationInfo $_auth
     * @param string $_name
     * @param string $_externalId
     * @param SessionManagementStructArrayOfAccessRole $_roles
     * @return SessionManagementStructProvisionExternalCourseWithRoles
     */
    public function __construct($_auth = NULL,$_name = NULL,$_externalId = NULL,$_roles = NULL)
    {
        parent::__construct(array('auth'=>$_auth,'name'=>$_name,'externalId'=>$_externalId,'roles'=>($_roles instanceof SessionManagementStructArrayOfAccessRole)?$_roles:new SessionManagementStructArrayOfAccessRole($_roles)),false);
    }
    /**
     * Get auth value
     * @return SessionManagementStructAuthenticationInfo|null
     */
    public function getAuth()
    {
        return $this->auth;
    }
    /**
     * Set auth value
     * @param SessionManagementStructAuthenticationInfo $_auth the auth
     * @return SessionManagementStructAuthenticationInfo
     */
    public function setAuth($_auth)
    {
        return ($this->auth = $_auth);
    }
    /**
     * Get name value
     * @return string|null
     */
    public function getName()
    {
        return $this->name;
    }
    /**
     * Set name value
     * @param string $_name the name
     * @return string
     */
    public function setName($_name)
    {
        return ($this->name = $_name);
    }
    /**
     * Get externalId value
     * @return string|null
     */
    public function getExternalId()
    {
        return $this->externalId;
    }
    /**
     * Set externalId value
     * @param string $_externalId the externalId
     * @return string
     */
    public function setExternalId($_externalId)
    {
        return ($this->externalId = $_externalId);
    }
    /**
     * Get roles value
     * @return SessionManagementStructArrayOfAccessRole|null
     */
    public function getRoles()
    {
        return $this->roles;
    }
    /**
     * Set roles value
     * @param SessionManagementStructArrayOfAccessRole $_roles the roles
     * @return SessionManagementStructArrayOfAccessRole
     */
    public function setRoles($_roles)
    {
        return ($this->roles = $_roles);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see SessionManagementWsdlClass::__set_state()
     * @uses SessionManagementWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return SessionManagementStructProvisionExternalCourseWithRoles
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
