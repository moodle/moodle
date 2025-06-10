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
 * @version 20150429-01
 * @date 2017-01-19
 */
/**
 * This class stands for SessionManagementServiceGet originally named Get
 * @package SessionManagement
 * @subpackage Services
 * @author Panopto
 * @version 20150429-01
 * @date 2017-01-19
 */
class SessionManagementServiceGet extends SessionManagementWsdlClass
{
    /**
     * Method to call the operation originally named GetFoldersById
     * @uses SessionManagementWsdlClass::getSoapClient()
     * @uses SessionManagementWsdlClass::setResult()
     * @uses SessionManagementWsdlClass::saveLastError()
     * @param SessionManagementStructGetFoldersById $_sessionManagementStructGetFoldersById
     * @return SessionManagementStructGetFoldersByIdResponse
     */
    public function GetFoldersById(SessionManagementStructGetFoldersById $_sessionManagementStructGetFoldersById)
    {
        try
        {
            return $this->setResult(self::getSoapClient()->GetFoldersById($_sessionManagementStructGetFoldersById));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Method to call the operation originally named GetFoldersWithExternalContextById
     * @uses SessionManagementWsdlClass::getSoapClient()
     * @uses SessionManagementWsdlClass::setResult()
     * @uses SessionManagementWsdlClass::saveLastError()
     * @param SessionManagementStructGetFoldersWithExternalContextById $_sessionManagementStructGetFoldersWithExternalContextById
     * @return SessionManagementStructGetFoldersWithExternalContextByIdResponse
     */
    public function GetFoldersWithExternalContextById(SessionManagementStructGetFoldersWithExternalContextById $_sessionManagementStructGetFoldersWithExternalContextById)
    {
        try
        {
            return $this->setResult(self::getSoapClient()->GetFoldersWithExternalContextById($_sessionManagementStructGetFoldersWithExternalContextById));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Method to call the operation originally named GetFoldersByExternalId
     * @uses SessionManagementWsdlClass::getSoapClient()
     * @uses SessionManagementWsdlClass::setResult()
     * @uses SessionManagementWsdlClass::saveLastError()
     * @param SessionManagementStructGetFoldersByExternalId $_sessionManagementStructGetFoldersByExternalId
     * @return SessionManagementStructGetFoldersByExternalIdResponse
     */
    public function GetFoldersByExternalId(SessionManagementStructGetFoldersByExternalId $_sessionManagementStructGetFoldersByExternalId)
    {
        try
        {
            return $this->setResult(self::getSoapClient()->GetFoldersByExternalId($_sessionManagementStructGetFoldersByExternalId));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Method to call the operation originally named GetFoldersWithExternalContextByExternalId
     * @uses SessionManagementWsdlClass::getSoapClient()
     * @uses SessionManagementWsdlClass::setResult()
     * @uses SessionManagementWsdlClass::saveLastError()
     * @param SessionManagementStructGetFoldersWithExternalContextByExternalId $_sessionManagementStructGetFoldersWithExternalContextByExternalId
     * @return SessionManagementStructGetFoldersWithExternalContextByExternalIdResponse
     */
    public function GetFoldersWithExternalContextByExternalId(SessionManagementStructGetFoldersWithExternalContextByExternalId $_sessionManagementStructGetFoldersWithExternalContextByExternalId)
    {
        try
        {
            return $this->setResult(self::getSoapClient()->GetFoldersWithExternalContextByExternalId($_sessionManagementStructGetFoldersWithExternalContextByExternalId));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Method to call the operation originally named GetAllFoldersByExternalId
     * @uses SessionManagementWsdlClass::getSoapClient()
     * @uses SessionManagementWsdlClass::setResult()
     * @uses SessionManagementWsdlClass::saveLastError()
     * @param SessionManagementStructGetAllFoldersByExternalId $_sessionManagementStructGetAllFoldersByExternalId
     * @return SessionManagementStructGetAllFoldersByExternalIdResponse
     */
    public function GetAllFoldersByExternalId(SessionManagementStructGetAllFoldersByExternalId $_sessionManagementStructGetAllFoldersByExternalId)
    {
        try
        {
            return $this->setResult(self::getSoapClient()->GetAllFoldersByExternalId($_sessionManagementStructGetAllFoldersByExternalId));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Method to call the operation originally named GetAllFoldersWithExternalContextByExternalId
     * @uses SessionManagementWsdlClass::getSoapClient()
     * @uses SessionManagementWsdlClass::setResult()
     * @uses SessionManagementWsdlClass::saveLastError()
     * @param SessionManagementStructGetAllFoldersWithExternalContextByExternalId $_sessionManagementStructGetAllFoldersWithExternalContextByExternalId
     * @return SessionManagementStructGetAllFoldersWithExternalContextByExternalIdResponse
     */
    public function GetAllFoldersWithExternalContextByExternalId(SessionManagementStructGetAllFoldersWithExternalContextByExternalId $_sessionManagementStructGetAllFoldersWithExternalContextByExternalId)
    {
        try
        {
            return $this->setResult(self::getSoapClient()->GetAllFoldersWithExternalContextByExternalId($_sessionManagementStructGetAllFoldersWithExternalContextByExternalId));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Method to call the operation originally named GetSessionsById
     * @uses SessionManagementWsdlClass::getSoapClient()
     * @uses SessionManagementWsdlClass::setResult()
     * @uses SessionManagementWsdlClass::saveLastError()
     * @param SessionManagementStructGetSessionsById $_sessionManagementStructGetSessionsById
     * @return SessionManagementStructGetSessionsByIdResponse
     */
    public function GetSessionsById(SessionManagementStructGetSessionsById $_sessionManagementStructGetSessionsById)
    {
        try
        {
            return $this->setResult(self::getSoapClient()->GetSessionsById($_sessionManagementStructGetSessionsById));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Method to call the operation originally named GetSessionsByExternalId
     * @uses SessionManagementWsdlClass::getSoapClient()
     * @uses SessionManagementWsdlClass::setResult()
     * @uses SessionManagementWsdlClass::saveLastError()
     * @param SessionManagementStructGetSessionsByExternalId $_sessionManagementStructGetSessionsByExternalId
     * @return SessionManagementStructGetSessionsByExternalIdResponse
     */
    public function GetSessionsByExternalId(SessionManagementStructGetSessionsByExternalId $_sessionManagementStructGetSessionsByExternalId)
    {
        try
        {
            return $this->setResult(self::getSoapClient()->GetSessionsByExternalId($_sessionManagementStructGetSessionsByExternalId));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Method to call the operation originally named GetSessionsList
     * @uses SessionManagementWsdlClass::getSoapClient()
     * @uses SessionManagementWsdlClass::setResult()
     * @uses SessionManagementWsdlClass::saveLastError()
     * @param SessionManagementStructGetSessionsList $_sessionManagementStructGetSessionsList
     * @return SessionManagementStructGetSessionsListResponse
     */
    public function GetSessionsList(SessionManagementStructGetSessionsList $_sessionManagementStructGetSessionsList)
    {
        try
        {
            return $this->setResult(self::getSoapClient()->GetSessionsList($_sessionManagementStructGetSessionsList));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Method to call the operation originally named GetFoldersList
     * @uses SessionManagementWsdlClass::getSoapClient()
     * @uses SessionManagementWsdlClass::setResult()
     * @uses SessionManagementWsdlClass::saveLastError()
     * @param SessionManagementStructGetFoldersList $_sessionManagementStructGetFoldersList
     * @return SessionManagementStructGetFoldersListResponse
     */
    public function GetFoldersList(SessionManagementStructGetFoldersList $_sessionManagementStructGetFoldersList)
    {
        try
        {
            return $this->setResult(self::getSoapClient()->GetFoldersList($_sessionManagementStructGetFoldersList));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Method to call the operation originally named GetFoldersWithExternalContextList
     * @uses SessionManagementWsdlClass::getSoapClient()
     * @uses SessionManagementWsdlClass::setResult()
     * @uses SessionManagementWsdlClass::saveLastError()
     * @param SessionManagementStructGetFoldersWithExternalContextList $_sessionManagementStructGetFoldersWithExternalContextList
     * @return SessionManagementStructGetFoldersWithExternalContextListResponse
     */
    public function GetFoldersWithExternalContextList(SessionManagementStructGetFoldersWithExternalContextList $_sessionManagementStructGetFoldersWithExternalContextList)
    {
        try
        {
            return $this->setResult(self::getSoapClient()->GetFoldersWithExternalContextList($_sessionManagementStructGetFoldersWithExternalContextList));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Method to call the operation originally named GetCreatorFoldersList
     * @uses SessionManagementWsdlClass::getSoapClient()
     * @uses SessionManagementWsdlClass::setResult()
     * @uses SessionManagementWsdlClass::saveLastError()
     * @param SessionManagementStructGetCreatorFoldersList $_sessionManagementStructGetCreatorFoldersList
     * @return SessionManagementStructGetCreatorFoldersListResponse
     */
    public function GetCreatorFoldersList(SessionManagementStructGetCreatorFoldersList $_sessionManagementStructGetCreatorFoldersList)
    {
        try
        {
            return $this->setResult(self::getSoapClient()->GetCreatorFoldersList($_sessionManagementStructGetCreatorFoldersList));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Method to call the operation originally named GetExtendedCreatorFoldersList
     * @uses SessionManagementWsdlClass::getSoapClient()
     * @uses SessionManagementWsdlClass::setResult()
     * @uses SessionManagementWsdlClass::saveLastError()
     * @param SessionManagementStructGetExtendedCreatorFoldersList $_sessionManagementStructGetExtendedCreatorFoldersList
     * @return SessionManagementStructGetExtendedCreatorFoldersListResponse
     */
    public function GetExtendedCreatorFoldersList(SessionManagementStructGetExtendedCreatorFoldersList $_sessionManagementStructGetExtendedCreatorFoldersList)
    {
        try
        {
            return $this->setResult(self::getSoapClient()->GetExtendedCreatorFoldersList($_sessionManagementStructGetExtendedCreatorFoldersList));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Method to call the operation originally named GetCreatorFoldersWithExternalContextList
     * @uses SessionManagementWsdlClass::getSoapClient()
     * @uses SessionManagementWsdlClass::setResult()
     * @uses SessionManagementWsdlClass::saveLastError()
     * @param SessionManagementStructGetCreatorFoldersWithExternalContextList $_sessionManagementStructGetCreatorFoldersWithExternalContextList
     * @return SessionManagementStructGetCreatorFoldersWithExternalContextListResponse
     */
    public function GetCreatorFoldersWithExternalContextList(SessionManagementStructGetCreatorFoldersWithExternalContextList $_sessionManagementStructGetCreatorFoldersWithExternalContextList)
    {
        try
        {
            return $this->setResult(self::getSoapClient()->GetCreatorFoldersWithExternalContextList($_sessionManagementStructGetCreatorFoldersWithExternalContextList));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Method to call the operation originally named GetRecorderDownloadUrls
     * @uses SessionManagementWsdlClass::getSoapClient()
     * @uses SessionManagementWsdlClass::setResult()
     * @uses SessionManagementWsdlClass::saveLastError()
     * @param SessionManagementStructGetRecorderDownloadUrls $_sessionManagementStructGetRecorderDownloadUrls
     * @return SessionManagementStructGetRecorderDownloadUrlsResponse
     */
    public function GetRecorderDownloadUrls()
    {
        try
        {
            return $this->setResult(self::getSoapClient()->GetRecorderDownloadUrls());
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Method to call the operation originally named GetNote
     * @uses SessionManagementWsdlClass::getSoapClient()
     * @uses SessionManagementWsdlClass::setResult()
     * @uses SessionManagementWsdlClass::saveLastError()
     * @param SessionManagementStructGetNote $_sessionManagementStructGetNote
     * @return SessionManagementStructGetNoteResponse
     */
    public function GetNote(SessionManagementStructGetNote $_sessionManagementStructGetNote)
    {
        try
        {
            return $this->setResult(self::getSoapClient()->GetNote($_sessionManagementStructGetNote));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Method to call the operation originally named GetFoldersAvailabilitySettings
     * @uses SessionManagementWsdlClass::getSoapClient()
     * @uses SessionManagementWsdlClass::setResult()
     * @uses SessionManagementWsdlClass::saveLastError()
     * @param SessionManagementStructGetFoldersAvailabilitySettings $_sessionManagementStructGetFoldersAvailabilitySettings
     * @return SessionManagementStructGetFoldersAvailabilitySettingsResponse
     */
    public function GetFoldersAvailabilitySettings(SessionManagementStructGetFoldersAvailabilitySettings $_sessionManagementStructGetFoldersAvailabilitySettings)
    {
        try
        {
            return $this->setResult(self::getSoapClient()->GetFoldersAvailabilitySettings($_sessionManagementStructGetFoldersAvailabilitySettings));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Method to call the operation originally named GetSessionsAvailabilitySettings
     * @uses SessionManagementWsdlClass::getSoapClient()
     * @uses SessionManagementWsdlClass::setResult()
     * @uses SessionManagementWsdlClass::saveLastError()
     * @param SessionManagementStructGetSessionsAvailabilitySettings $_sessionManagementStructGetSessionsAvailabilitySettings
     * @return SessionManagementStructGetSessionsAvailabilitySettingsResponse
     */
    public function GetSessionsAvailabilitySettings(SessionManagementStructGetSessionsAvailabilitySettings $_sessionManagementStructGetSessionsAvailabilitySettings)
    {
        try
        {
            return $this->setResult(self::getSoapClient()->GetSessionsAvailabilitySettings($_sessionManagementStructGetSessionsAvailabilitySettings));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Method to call the operation originally named GetPersonalFolderForUser
     * @uses SessionManagementWsdlClass::getSoapClient()
     * @uses SessionManagementWsdlClass::setResult()
     * @uses SessionManagementWsdlClass::saveLastError()
     * @param SessionManagementStructGetPersonalFolderForUser $_sessionManagementStructGetPersonalFolderForUser
     * @return SessionManagementStructGetPersonalFolderForUserResponse
     */
    public function GetPersonalFolderForUser(SessionManagementStructGetPersonalFolderForUser $_sessionManagementStructGetPersonalFolderForUser)
    {
        try
        {
            return $this->setResult(self::getSoapClient()->GetPersonalFolderForUser($_sessionManagementStructGetPersonalFolderForUser));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Returns the result
     * @see SessionManagementWsdlClass::getResult()
     * @return SessionManagementStructGetAllFoldersByExternalIdResponse|SessionManagementStructGetAllFoldersWithExternalContextByExternalIdResponse|SessionManagementStructGetCreatorFoldersListResponse|SessionManagementStructGetCreatorFoldersWithExternalContextListResponse|SessionManagementStructGetFoldersAvailabilitySettingsResponse|SessionManagementStructGetFoldersByExternalIdResponse|SessionManagementStructGetFoldersByIdResponse|SessionManagementStructGetFoldersListResponse|SessionManagementStructGetFoldersWithExternalContextByExternalIdResponse|SessionManagementStructGetFoldersWithExternalContextByIdResponse|SessionManagementStructGetFoldersWithExternalContextListResponse|SessionManagementStructGetNoteResponse|SessionManagementStructGetPersonalFolderForUserResponse|SessionManagementStructGetRecorderDownloadUrlsResponse|SessionManagementStructGetSessionsAvailabilitySettingsResponse|SessionManagementStructGetSessionsByExternalIdResponse|SessionManagementStructGetSessionsByIdResponse|SessionManagementStructGetSessionsListResponse|SessionManagementStructGetExtendedCreatorFoldersListResponse
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
