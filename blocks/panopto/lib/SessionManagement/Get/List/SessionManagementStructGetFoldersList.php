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
 * File for class SessionManagementStructGetFoldersList
 * @package SessionManagement
 * @subpackage Structs
 * @author Panopto
 * @version 20150429-01
 * @date 2017-01-19
 */
/**
 * This class stands for SessionManagementStructGetFoldersList originally named GetFoldersList
 * Meta informations extracted from the WSDL
 * - from schema : {@link http://demo.hosted.panopto.com/Panopto/PublicAPI/4.6/SessionManagement.svc?xsd=xsd0}
 * @package SessionManagement
 * @subpackage Structs
 * @author Panopto
 * @version 20150429-01
 * @date 2017-01-19
 */
class SessionManagementStructGetFoldersList extends SessionManagementWsdlClass
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
     * The request
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var SessionManagementStructListFoldersRequest
     */
    public $request;
    /**
     * The searchQuery
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $searchQuery;
    /**
     * Constructor method for GetFoldersList
     * @see parent::__construct()
     * @param SessionManagementStructAuthenticationInfo $_auth
     * @param SessionManagementStructListFoldersRequest $_request
     * @param string $_searchQuery
     * @return SessionManagementStructGetFoldersList
     */
    public function __construct($_auth = NULL,$_request = NULL,$_searchQuery = NULL)
    {
        parent::__construct(array('auth'=>$_auth,'request'=>$_request,'searchQuery'=>$_searchQuery),false);
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
     * Get request value
     * @return SessionManagementStructListFoldersRequest|null
     */
    public function getRequest()
    {
        return $this->request;
    }
    /**
     * Set request value
     * @param SessionManagementStructListFoldersRequest $_request the request
     * @return SessionManagementStructListFoldersRequest
     */
    public function setRequest($_request)
    {
        return ($this->request = $_request);
    }
    /**
     * Get searchQuery value
     * @return string|null
     */
    public function getSearchQuery()
    {
        return $this->searchQuery;
    }
    /**
     * Set searchQuery value
     * @param string $_searchQuery the searchQuery
     * @return string
     */
    public function setSearchQuery($_searchQuery)
    {
        return ($this->searchQuery = $_searchQuery);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see SessionManagementWsdlClass::__set_state()
     * @uses SessionManagementWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return SessionManagementStructGetFoldersList
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
