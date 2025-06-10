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
 * File for class AuthManagementServiceReport
 * @package AuthManagement
 * @subpackage Services
 * @author Panopto
 * @version 20150429-01
 * @date 2017-05-25
 */
/**
 * This class stands for AuthManagementServiceReport originally named Report
 * @package AuthManagement
 * @subpackage Services
 * @author Panopto
 * @version 20150429-01
 * @date 2017-05-25
 */
class AuthManagementServiceReport extends AuthManagementWsdlClass
{
    /**
     * Method to call the operation originally named ReportIntegrationInfo
     * @uses AuthManagementWsdlClass::getSoapClient()
     * @uses AuthManagementWsdlClass::setResult()
     * @uses AuthManagementWsdlClass::saveLastError()
     * @param AuthManagementStructReportIntegrationInfo $_authManagementStructReportIntegrationInfo
     * @return AuthManagementStructReportIntegrationInfoResponse
     */
    public function ReportIntegrationInfo(AuthManagementStructReportIntegrationInfo $_authManagementStructReportIntegrationInfo)
    {
        try
        {
            return $this->setResult(self::getSoapClient()->ReportIntegrationInfo($_authManagementStructReportIntegrationInfo));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Returns the result
     * @see AuthManagementWsdlClass::getResult()
     * @return AuthManagementStructReportIntegrationInfoResponse
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
