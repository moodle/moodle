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
 * File for class SessionManagementServiceAdd
 * @package SessionManagement
 * @subpackage Services
 * @author Panopto
 * @version 20150429-01
 * @date 2017-01-19
 */
/**
 * This class stands for SessionManagementServiceAdd originally named Add
 * @package SessionManagement
 * @subpackage Services
 * @author Panopto
 * @version 20150429-01
 * @date 2017-01-19
 */
class SessionManagementServiceAdd extends SessionManagementWsdlClass
{
    /**
     * Method to call the operation originally named AddFolder
     * @uses SessionManagementWsdlClass::getSoapClient()
     * @uses SessionManagementWsdlClass::setResult()
     * @uses SessionManagementWsdlClass::saveLastError()
     * @param SessionManagementStructAddFolder $_sessionManagementStructAddFolder
     * @return SessionManagementStructAddFolderResponse
     */
    public function AddFolder(SessionManagementStructAddFolder $_sessionManagementStructAddFolder)
    {
        try
        {
            return $this->setResult(self::getSoapClient()->AddFolder($_sessionManagementStructAddFolder));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Method to call the operation originally named AddSession
     * @uses SessionManagementWsdlClass::getSoapClient()
     * @uses SessionManagementWsdlClass::setResult()
     * @uses SessionManagementWsdlClass::saveLastError()
     * @param SessionManagementStructAddSession $_sessionManagementStructAddSession
     * @return SessionManagementStructAddSessionResponse
     */
    public function AddSession(SessionManagementStructAddSession $_sessionManagementStructAddSession)
    {
        try
        {
            return $this->setResult(self::getSoapClient()->AddSession($_sessionManagementStructAddSession));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Returns the result
     * @see SessionManagementWsdlClass::getResult()
     * @return SessionManagementStructAddFolderResponse|SessionManagementStructAddSessionResponse
     */
    public function getResult()
    {
        return parent::getResult();
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
