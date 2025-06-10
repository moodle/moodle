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
 * File for class SessionManagementStructAddFolder
 * @package SessionManagement
 * @subpackage Structs
 * @author Panopto
 * @version 20150429-01
 * @date 2017-01-19
 */
/**
 * This class stands for SessionManagementStructAddFolder originally named AddFolder
 * Meta informations extracted from the WSDL
 * - from schema : {@link http://demo.hosted.panopto.com/Panopto/PublicAPI/4.6/SessionManagement.svc?xsd=xsd0}
 * @package SessionManagement
 * @subpackage Structs
 * @author Panopto
 * @version 20150429-01
 * @date 2017-01-19
 */
class SessionManagementStructAddFolder extends SessionManagementWsdlClass
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
     * The parentFolder
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * - pattern : [\da-fA-F]{8}-[\da-fA-F]{4}-[\da-fA-F]{4}-[\da-fA-F]{4}-[\da-fA-F]{12}
     * @var string
     */
    public $parentFolder;
    /**
     * The isPublic
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var boolean
     */
    public $isPublic;
    /**
     * Constructor method for AddFolder
     * @see parent::__construct()
     * @param SessionManagementStructAuthenticationInfo $_auth
     * @param string $_name
     * @param string $_parentFolder
     * @param boolean $_isPublic
     * @return SessionManagementStructAddFolder
     */
    public function __construct($_auth = NULL,$_name = NULL,$_parentFolder = NULL,$_isPublic = NULL)
    {
        parent::__construct(array('auth'=>$_auth,'name'=>$_name,'parentFolder'=>$_parentFolder,'isPublic'=>$_isPublic),false);
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
     * Get parentFolder value
     * @return string|null
     */
    public function getParentFolder()
    {
        return $this->parentFolder;
    }
    /**
     * Set parentFolder value
     * @param string $_parentFolder the parentFolder
     * @return string
     */
    public function setParentFolder($_parentFolder)
    {
        return ($this->parentFolder = $_parentFolder);
    }
    /**
     * Get isPublic value
     * @return boolean|null
     */
    public function getIsPublic()
    {
        return $this->isPublic;
    }
    /**
     * Set isPublic value
     * @param boolean $_isPublic the isPublic
     * @return boolean
     */
    public function setIsPublic($_isPublic)
    {
        return ($this->isPublic = $_isPublic);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see SessionManagementWsdlClass::__set_state()
     * @uses SessionManagementWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return SessionManagementStructAddFolder
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
