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
 * File for class SessionManagementStructEnsureExternalHierarchyBranch
 * @package SessionManagement
 * @subpackage Structs
 * @author Panopto
 * @date 2017-01-19
 */
/**
 * This class stands for SessionManagementStructEnsureExternalHierarchyBranch originally named EnsureExternalHierarchyBranch
 * Meta informations extracted from the WSDL
 * - from schema : {@link http://demo.hosted.panopto.com/Panopto/PublicAPI/4.6/SessionManagement.svc?xsd=xsd0}
 * @package SessionManagement
 * @subpackage Structs
 * @author Panopto
 * @date 2017-01-19
 */
class SessionManagementStructEnsureExternalHierarchyBranch extends SessionManagementWsdlClass
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
     * The externalHierarchyBranch
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var SessionManagementStructArrayOfExternalHierarchyInfo
     */
    public $externalHierarchyBranch;
    /**
     * Constructor method for EnsureExternalHierarchyBranch
     * @see parent::__construct()
     * @param SessionManagementStructAuthenticationInfo $_auth
     * @param SessionManagementStructArrayOfExternalHierarchyInfo $_externalHierarchyBranch
     * @return SessionManagementStructEnsureExternalHierarchyBranch
     */
    public function __construct($_auth = NULL,$_externalHierarchyBranch = NULL)
    {
        parent::__construct(array('auth'=>$_auth,'externalHierarchyBranch'=>($_externalHierarchyBranch instanceof SessionManagementStructArrayOfExternalHierarchyInfo)?$_externalHierarchyBranch:new SessionManagementStructArrayOfExternalHierarchyInfo($_externalHierarchyBranch)),false);
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
     * Get externalHierarchyBranch value
     * @return SessionManagementStructArrayOfExternalHierarchyInfo|null
     */
    public function getExternalHierarchyBranch()
    {
        return $this->externalHierarchyBranch;
    }
    /**
     * Set externalHierarchyBranch value
     * @param SessionManagementStructArrayOfExternalHierarchyInfo $_externalHierarchyBranch the externalHierarchyBranch
     * @return SessionManagementStructArrayOfExternalHierarchyInfo
     */
    public function setExternalHierarchyBranch($_externalHierarchyBranch)
    {
        return ($this->externalHierarchyBranch = $_externalHierarchyBranch);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see SessionManagementWsdlClass::__set_state()
     * @uses SessionManagementWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return SessionManagementStructEnsureExternalHierarchyBranch
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
