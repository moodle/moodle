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
 * File for class SessionManagementServiceSet
 * @package SessionManagement
 * @subpackage Services
 * @author Panopto
 * @version 20150429-01
 * @date 2017-01-19
 */
/**
 * This class stands for SessionManagementServiceSet originally named Set
 * @package SessionManagement
 * @subpackage Services
 * @author Panopto
 * @version 20150429-01
 * @date 2017-01-19
 */
class SessionManagementServiceSet extends SessionManagementWsdlClass
{
    /**
     * Method to call the operation originally named SetExternalCourseAccess
     * @uses SessionManagementWsdlClass::getSoapClient()
     * @uses SessionManagementWsdlClass::setResult()
     * @uses SessionManagementWsdlClass::saveLastError()
     * @param SessionManagementStructSetExternalCourseAccess $_sessionManagementStructSetExternalCourseAccess
     * @return SessionManagementStructSetExternalCourseAccessResponse
     */
    public function SetExternalCourseAccess(SessionManagementStructSetExternalCourseAccess $_sessionManagementStructSetExternalCourseAccess)
    {
        try
        {
            return $this->setResult(self::getSoapClient()->SetExternalCourseAccess($_sessionManagementStructSetExternalCourseAccess));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Method to call the operation originally named SetExternalCourseAccessForRoles
     * @uses SessionManagementWsdlClass::getSoapClient()
     * @uses SessionManagementWsdlClass::setResult()
     * @uses SessionManagementWsdlClass::saveLastError()
     * @param SessionManagementStructSetExternalCourseAccessForRoles $_sessionManagementStructSetExternalCourseAccessForRoles
     * @return SessionManagementStructSetExternalCourseAccessForRolesResponse
     */
    public function SetExternalCourseAccessForRoles(SessionManagementStructSetExternalCourseAccessForRoles $_sessionManagementStructSetExternalCourseAccessForRoles)
    {
        try
        {
            return $this->setResult(self::getSoapClient()->SetExternalCourseAccessForRoles($_sessionManagementStructSetExternalCourseAccessForRoles));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Method to call the operation originally named SetCopiedExternalCourseAccess
     * @uses SessionManagementWsdlClass::getSoapClient()
     * @uses SessionManagementWsdlClass::setResult()
     * @uses SessionManagementWsdlClass::saveLastError()
     * @param SessionManagementStructSetCopiedExternalCourseAccess $_sessionManagementStructSetCopiedExternalCourseAccess
     * @return SessionManagementStructSetCopiedExternalCourseAccessResponse
     */
    public function SetCopiedExternalCourseAccess(SessionManagementStructSetCopiedExternalCourseAccess $_sessionManagementStructSetCopiedExternalCourseAccess)
    {
        try
        {
            return $this->setResult(self::getSoapClient()->SetCopiedExternalCourseAccess($_sessionManagementStructSetCopiedExternalCourseAccess));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Method to call the operation originally named SetCopiedExternalCourseAccessForRoles
     * @uses SessionManagementWsdlClass::getSoapClient()
     * @uses SessionManagementWsdlClass::setResult()
     * @uses SessionManagementWsdlClass::saveLastError()
     * @param SessionManagementStructSetCopiedExternalCourseAccessForRoles $_sessionManagementStructSetCopiedExternalCourseAccessForRoles
     * @return SessionManagementStructSetCopiedExternalCourseAccessForRolesResponse
     */
    public function SetCopiedExternalCourseAccessForRoles(SessionManagementStructSetCopiedExternalCourseAccessForRoles $_sessionManagementStructSetCopiedExternalCourseAccessForRoles)
    {
        try
        {
            return $this->setResult(self::getSoapClient()->SetCopiedExternalCourseAccessForRoles($_sessionManagementStructSetCopiedExternalCourseAccessForRoles));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Method to call the operation originally named SetNotesPublic
     * @uses SessionManagementWsdlClass::getSoapClient()
     * @uses SessionManagementWsdlClass::setResult()
     * @uses SessionManagementWsdlClass::saveLastError()
     * @param SessionManagementStructSetNotesPublic $_sessionManagementStructSetNotesPublic
     * @return SessionManagementStructSetNotesPublicResponse
     */
    public function SetNotesPublic(SessionManagementStructSetNotesPublic $_sessionManagementStructSetNotesPublic)
    {
        try
        {
            return $this->setResult(self::getSoapClient()->SetNotesPublic($_sessionManagementStructSetNotesPublic));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Returns the result
     * @see SessionManagementWsdlClass::getResult()
     * @return SessionManagementStructSetCopiedExternalCourseAccessForRolesResponse|SessionManagementStructSetCopiedExternalCourseAccessResponse|SessionManagementStructSetExternalCourseAccessForRolesResponse|SessionManagementStructSetExternalCourseAccessResponse|SessionManagementStructSetNotesPublicResponse
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
