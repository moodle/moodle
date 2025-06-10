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
 * File for class SessionManagementStructGetAllFoldersWithExternalContextByExternalId
 * @package SessionManagement
 * @subpackage Structs
 * @author Panopto
 * @version 20150429-01
 * @date 2017-01-19
 */
/**
 * This class stands for SessionManagementStructGetAllFoldersWithExternalContextByExternalId originally named GetAllFoldersWithExternalContextByExternalId
 * Meta informations extracted from the WSDL
 * - from schema : {@link http://demo.hosted.panopto.com/Panopto/PublicAPI/4.6/SessionManagement.svc?xsd=xsd0}
 * @package SessionManagement
 * @subpackage Structs
 * @author Panopto
 * @version 20150429-01
 * @date 2017-01-19
 */
class SessionManagementStructGetAllFoldersWithExternalContextByExternalId extends SessionManagementWsdlClass
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
     * The folderExternalIds
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var SessionManagementStructArrayOfstring
     */
    public $folderExternalIds;
    /**
     * The providerNames
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var SessionManagementStructArrayOfstring
     */
    public $providerNames;
    /**
     * Constructor method for GetAllFoldersWithExternalContextByExternalId
     * @see parent::__construct()
     * @param SessionManagementStructAuthenticationInfo $_auth
     * @param SessionManagementStructArrayOfstring $_folderExternalIds
     * @param SessionManagementStructArrayOfstring $_providerNames
     * @return SessionManagementStructGetAllFoldersWithExternalContextByExternalId
     */
    public function __construct($_auth = NULL,$_folderExternalIds = NULL,$_providerNames = NULL)
    {
        parent::__construct(array('auth'=>$_auth,'folderExternalIds'=>($_folderExternalIds instanceof SessionManagementStructArrayOfstring)?$_folderExternalIds:new SessionManagementStructArrayOfstring($_folderExternalIds),'providerNames'=>($_providerNames instanceof SessionManagementStructArrayOfstring)?$_providerNames:new SessionManagementStructArrayOfstring($_providerNames)),false);
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
     * Get folderExternalIds value
     * @return SessionManagementStructArrayOfstring|null
     */
    public function getFolderExternalIds()
    {
        return $this->folderExternalIds;
    }
    /**
     * Set folderExternalIds value
     * @param SessionManagementStructArrayOfstring $_folderExternalIds the folderExternalIds
     * @return SessionManagementStructArrayOfstring
     */
    public function setFolderExternalIds($_folderExternalIds)
    {
        return ($this->folderExternalIds = $_folderExternalIds);
    }
    /**
     * Get providerNames value
     * @return SessionManagementStructArrayOfstring|null
     */
    public function getProviderNames()
    {
        return $this->providerNames;
    }
    /**
     * Set providerNames value
     * @param SessionManagementStructArrayOfstring $_providerNames the providerNames
     * @return SessionManagementStructArrayOfstring
     */
    public function setProviderNames($_providerNames)
    {
        return ($this->providerNames = $_providerNames);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see SessionManagementWsdlClass::__set_state()
     * @uses SessionManagementWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return SessionManagementStructGetAllFoldersWithExternalContextByExternalId
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
