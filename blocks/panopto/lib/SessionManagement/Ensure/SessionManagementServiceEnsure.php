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
 * File for class SessionManagementServiceGet
 * @package SessionManagement
 * @subpackage Services
 * @author Panopto
 * @date 2017-01-19
 */
/**
 * This class stands for SessionManagementServiceGet originally named Get
 * @package SessionManagement
 * @subpackage Services
 * @author Panopto
 * @date 2017-01-19
 */
class SessionManagementServiceEnsure extends SessionManagementWsdlClass
{
    /**
     * Method to call the operation originally named EnsureExternalHierarchyBranch
     * @uses SessionManagementWsdlClass::getSoapClient()
     * @uses SessionManagementWsdlClass::setResult()
     * @uses SessionManagementWsdlClass::saveLastError()
     * @param SessionManagementStructEnsureExternalHierarchyBranch $_sessionManagementStructEnsureExternalHierarchyBranch
     * @return SessionManagementStructEnsureExternalHierarchyBranchResponse
     */
    public function EnsureExternalHierarchyBranch(SessionManagementStructEnsureExternalHierarchyBranch $_sessionManagementStructEnsureExternalHierarchyBranch)
    {
        try
        {
            return $this->setResult(self::getSoapClient()->EnsureExternalHierarchyBranch($_sessionManagementStructEnsureExternalHierarchyBranch));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }

    /**
     * Returns the result
     * @see SessionManagementWsdlClass::getResult()
     * @return SessionManagementStructEnsureExternalHierarchyBranchResponse
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
