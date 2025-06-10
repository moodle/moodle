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
 * File for class SessionManagementStructUpdateFoldersAvailabilityEndSettingsResponse
 * @package SessionManagement
 * @subpackage Structs
 * @author Panopto
 * @version 20150429-01
 * @date 2017-01-19
 */
/**
 * This class stands for SessionManagementStructUpdateFoldersAvailabilityEndSettingsResponse originally named UpdateFoldersAvailabilityEndSettingsResponse
 * Meta informations extracted from the WSDL
 * - from schema : {@link http://demo.hosted.panopto.com/Panopto/PublicAPI/4.6/SessionManagement.svc?xsd=xsd0}
 * @package SessionManagement
 * @subpackage Structs
 * @author Panopto
 * @version 20150429-01
 * @date 2017-01-19
 */
class SessionManagementStructUpdateFoldersAvailabilityEndSettingsResponse extends SessionManagementWsdlClass
{
    /**
     * The UpdateFoldersAvailabilityEndSettingsResult
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var boolean
     */
    public $UpdateFoldersAvailabilityEndSettingsResult;
    /**
     * Constructor method for UpdateFoldersAvailabilityEndSettingsResponse
     * @see parent::__construct()
     * @param boolean $_updateFoldersAvailabilityEndSettingsResult
     * @return SessionManagementStructUpdateFoldersAvailabilityEndSettingsResponse
     */
    public function __construct($_updateFoldersAvailabilityEndSettingsResult = NULL)
    {
        parent::__construct(array('UpdateFoldersAvailabilityEndSettingsResult'=>$_updateFoldersAvailabilityEndSettingsResult),false);
    }
    /**
     * Get UpdateFoldersAvailabilityEndSettingsResult value
     * @return boolean|null
     */
    public function getUpdateFoldersAvailabilityEndSettingsResult()
    {
        return $this->UpdateFoldersAvailabilityEndSettingsResult;
    }
    /**
     * Set UpdateFoldersAvailabilityEndSettingsResult value
     * @param boolean $_updateFoldersAvailabilityEndSettingsResult the UpdateFoldersAvailabilityEndSettingsResult
     * @return boolean
     */
    public function setUpdateFoldersAvailabilityEndSettingsResult($_updateFoldersAvailabilityEndSettingsResult)
    {
        return ($this->UpdateFoldersAvailabilityEndSettingsResult = $_updateFoldersAvailabilityEndSettingsResult);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see SessionManagementWsdlClass::__set_state()
     * @uses SessionManagementWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return SessionManagementStructUpdateFoldersAvailabilityEndSettingsResponse
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
