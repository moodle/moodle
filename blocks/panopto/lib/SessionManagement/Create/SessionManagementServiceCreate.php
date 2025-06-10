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
 * File for class SessionManagementServiceCreate
 * @package SessionManagement
 * @subpackage Services
 * @author Panopto
 * @version 20150429-01
 * @date 2017-01-19
 */
/**
 * This class stands for SessionManagementServiceCreate originally named Create
 * @package SessionManagement
 * @subpackage Services
 * @author Panopto
 * @version 20150429-01
 * @date 2017-01-19
 */
class SessionManagementServiceCreate extends SessionManagementWsdlClass
{
    /**
     * Method to call the operation originally named CreateNoteByRelativeTime
     * @uses SessionManagementWsdlClass::getSoapClient()
     * @uses SessionManagementWsdlClass::setResult()
     * @uses SessionManagementWsdlClass::saveLastError()
     * @param SessionManagementStructCreateNoteByRelativeTime $_sessionManagementStructCreateNoteByRelativeTime
     * @return SessionManagementStructCreateNoteByRelativeTimeResponse
     */
    public function CreateNoteByRelativeTime(SessionManagementStructCreateNoteByRelativeTime $_sessionManagementStructCreateNoteByRelativeTime)
    {
        try
        {
            return $this->setResult(self::getSoapClient()->CreateNoteByRelativeTime($_sessionManagementStructCreateNoteByRelativeTime));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Method to call the operation originally named CreateNoteByAbsoluteTime
     * @uses SessionManagementWsdlClass::getSoapClient()
     * @uses SessionManagementWsdlClass::setResult()
     * @uses SessionManagementWsdlClass::saveLastError()
     * @param SessionManagementStructCreateNoteByAbsoluteTime $_sessionManagementStructCreateNoteByAbsoluteTime
     * @return SessionManagementStructCreateNoteByAbsoluteTimeResponse
     */
    public function CreateNoteByAbsoluteTime(SessionManagementStructCreateNoteByAbsoluteTime $_sessionManagementStructCreateNoteByAbsoluteTime)
    {
        try
        {
            return $this->setResult(self::getSoapClient()->CreateNoteByAbsoluteTime($_sessionManagementStructCreateNoteByAbsoluteTime));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Method to call the operation originally named CreateCaptionByRelativeTime
     * @uses SessionManagementWsdlClass::getSoapClient()
     * @uses SessionManagementWsdlClass::setResult()
     * @uses SessionManagementWsdlClass::saveLastError()
     * @param SessionManagementStructCreateCaptionByRelativeTime $_sessionManagementStructCreateCaptionByRelativeTime
     * @return SessionManagementStructCreateCaptionByRelativeTimeResponse
     */
    public function CreateCaptionByRelativeTime(SessionManagementStructCreateCaptionByRelativeTime $_sessionManagementStructCreateCaptionByRelativeTime)
    {
        try
        {
            return $this->setResult(self::getSoapClient()->CreateCaptionByRelativeTime($_sessionManagementStructCreateCaptionByRelativeTime));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Method to call the operation originally named CreateCaptionByAbsoluteTime
     * @uses SessionManagementWsdlClass::getSoapClient()
     * @uses SessionManagementWsdlClass::setResult()
     * @uses SessionManagementWsdlClass::saveLastError()
     * @param SessionManagementStructCreateCaptionByAbsoluteTime $_sessionManagementStructCreateCaptionByAbsoluteTime
     * @return SessionManagementStructCreateCaptionByAbsoluteTimeResponse
     */
    public function CreateCaptionByAbsoluteTime(SessionManagementStructCreateCaptionByAbsoluteTime $_sessionManagementStructCreateCaptionByAbsoluteTime)
    {
        try
        {
            return $this->setResult(self::getSoapClient()->CreateCaptionByAbsoluteTime($_sessionManagementStructCreateCaptionByAbsoluteTime));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Returns the result
     * @see SessionManagementWsdlClass::getResult()
     * @return SessionManagementStructCreateCaptionByAbsoluteTimeResponse|SessionManagementStructCreateCaptionByRelativeTimeResponse|SessionManagementStructCreateNoteByAbsoluteTimeResponse|SessionManagementStructCreateNoteByRelativeTimeResponse
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
