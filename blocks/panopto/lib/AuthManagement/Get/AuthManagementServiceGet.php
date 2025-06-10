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
 * File for class AuthManagementServiceGet
 * @package AuthManagement
 * @subpackage Services
 * @author Panopto
 * @version 20150429-01
 * @date 2017-05-25
 */
/**
 * This class stands for AuthManagementServiceGet originally named Get
 * @package AuthManagement
 * @subpackage Services
 * @author Panopto
 * @version 20150429-01
 * @date 2017-05-25
 */
class AuthManagementServiceGet extends AuthManagementWsdlClass
{
    /**
     * Method to call the operation originally named GetServerVersion
     * @uses AuthManagementWsdlClass::getSoapClient()
     * @uses AuthManagementWsdlClass::setResult()
     * @uses AuthManagementWsdlClass::saveLastError()
     * @param AuthManagementStructGetServerVersion $_authManagementStructGetServerVersion
     * @return AuthManagementStructGetServerVersionResponse
     */
    public function GetServerVersion()
    {
        try
        {
            return $this->setResult(self::getSoapClient()->GetServerVersion());
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Method to call the operation originally named GetAuthenticatedUrl
     * @uses AuthManagementWsdlClass::getSoapClient()
     * @uses AuthManagementWsdlClass::setResult()
     * @uses AuthManagementWsdlClass::saveLastError()
     * @param AuthManagementStructGetAuthenticatedUrl $_authManagementStructGetAuthenticatedUrl
     * @return AuthManagementStructGetAuthenticatedUrlResponse
     */
    public function GetAuthenticatedUrl(AuthManagementStructGetAuthenticatedUrl $_authManagementStructGetAuthenticatedUrl)
    {
        try
        {
            return $this->setResult(self::getSoapClient()->GetAuthenticatedUrl($_authManagementStructGetAuthenticatedUrl));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Returns the result
     * @see AuthManagementWsdlClass::getResult()
     * @return AuthManagementStructGetAuthenticatedUrlResponse|AuthManagementStructGetServerVersionResponse
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
