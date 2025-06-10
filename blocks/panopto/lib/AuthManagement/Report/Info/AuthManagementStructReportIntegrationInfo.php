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
 * File for class AuthManagementStructReportIntegrationInfo
 * @package AuthManagement
 * @subpackage Structs
 * @author Panopto
 * @version 20150429-01
 * @date 2017-05-25
 */
/**
 * This class stands for AuthManagementStructReportIntegrationInfo originally named ReportIntegrationInfo
 * Meta informations extracted from the WSDL
 * - from schema : {@link http://demo.hosted.panopto.com/Panopto/PublicAPI/4.2/Auth.svc?xsd=xsd0}
 * @package AuthManagement
 * @subpackage Structs
 * @author Panopto
 * @version 20150429-01
 * @date 2017-05-25
 */
class AuthManagementStructReportIntegrationInfo extends AuthManagementWsdlClass
{
    /**
     * The auth
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var AuthManagementStructAuthenticationInfo
     */
    public $auth;
    /**
     * The idProviderName
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $idProviderName;
    /**
     * The moduleVersion
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $moduleVersion;
    /**
     * The targetPlatformVersion
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $targetPlatformVersion;
    /**
     * Constructor method for ReportIntegrationInfo
     * @see parent::__construct()
     * @param AuthManagementStructAuthenticationInfo $_auth
     * @param string $_idProviderName
     * @param string $_moduleVersion
     * @param string $_targetPlatformVersion
     * @return AuthManagementStructReportIntegrationInfo
     */
    public function __construct($_auth = NULL,$_idProviderName = NULL,$_moduleVersion = NULL,$_targetPlatformVersion = NULL)
    {
        parent::__construct(array('auth'=>$_auth,'idProviderName'=>$_idProviderName,'moduleVersion'=>$_moduleVersion,'targetPlatformVersion'=>$_targetPlatformVersion),false);
    }
    /**
     * Get auth value
     * @return AuthManagementStructAuthenticationInfo|null
     */
    public function getAuth()
    {
        return $this->auth;
    }
    /**
     * Set auth value
     * @param AuthManagementStructAuthenticationInfo $_auth the auth
     * @return AuthManagementStructAuthenticationInfo
     */
    public function setAuth($_auth)
    {
        return ($this->auth = $_auth);
    }
    /**
     * Get idProviderName value
     * @return string|null
     */
    public function getIdProviderName()
    {
        return $this->idProviderName;
    }
    /**
     * Set idProviderName value
     * @param string $_idProviderName the idProviderName
     * @return string
     */
    public function setIdProviderName($_idProviderName)
    {
        return ($this->idProviderName = $_idProviderName);
    }
    /**
     * Get moduleVersion value
     * @return string|null
     */
    public function getModuleVersion()
    {
        return $this->moduleVersion;
    }
    /**
     * Set moduleVersion value
     * @param string $_moduleVersion the moduleVersion
     * @return string
     */
    public function setModuleVersion($_moduleVersion)
    {
        return ($this->moduleVersion = $_moduleVersion);
    }
    /**
     * Get targetPlatformVersion value
     * @return string|null
     */
    public function getTargetPlatformVersion()
    {
        return $this->targetPlatformVersion;
    }
    /**
     * Set targetPlatformVersion value
     * @param string $_targetPlatformVersion the targetPlatformVersion
     * @return string
     */
    public function setTargetPlatformVersion($_targetPlatformVersion)
    {
        return ($this->targetPlatformVersion = $_targetPlatformVersion);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see AuthManagementWsdlClass::__set_state()
     * @uses AuthManagementWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return AuthManagementStructReportIntegrationInfo
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
