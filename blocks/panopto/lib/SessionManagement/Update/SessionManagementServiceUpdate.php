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
 * File for class SessionManagementServiceUpdate
 * @package SessionManagement
 * @subpackage Services
 * @author Panopto
 * @version 20150429-01
 * @date 2017-01-19
 */
/**
 * This class stands for SessionManagementServiceUpdate originally named Update
 * @package SessionManagement
 * @subpackage Services
 * @author Panopto
 * @version 20150429-01
 * @date 2017-01-19
 */
class SessionManagementServiceUpdate extends SessionManagementWsdlClass
{
    /**
     * Method to call the operation originally named UpdateSessionName
     * @uses SessionManagementWsdlClass::getSoapClient()
     * @uses SessionManagementWsdlClass::setResult()
     * @uses SessionManagementWsdlClass::saveLastError()
     * @param SessionManagementStructUpdateSessionName $_sessionManagementStructUpdateSessionName
     * @return SessionManagementStructUpdateSessionNameResponse
     */
    public function UpdateSessionName(SessionManagementStructUpdateSessionName $_sessionManagementStructUpdateSessionName)
    {
        try
        {
            return $this->setResult(self::getSoapClient()->UpdateSessionName($_sessionManagementStructUpdateSessionName));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Method to call the operation originally named UpdateSessionDescription
     * @uses SessionManagementWsdlClass::getSoapClient()
     * @uses SessionManagementWsdlClass::setResult()
     * @uses SessionManagementWsdlClass::saveLastError()
     * @param SessionManagementStructUpdateSessionDescription $_sessionManagementStructUpdateSessionDescription
     * @return SessionManagementStructUpdateSessionDescriptionResponse
     */
    public function UpdateSessionDescription(SessionManagementStructUpdateSessionDescription $_sessionManagementStructUpdateSessionDescription)
    {
        try
        {
            return $this->setResult(self::getSoapClient()->UpdateSessionDescription($_sessionManagementStructUpdateSessionDescription));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Method to call the operation originally named UpdateSessionIsBroadcast
     * @uses SessionManagementWsdlClass::getSoapClient()
     * @uses SessionManagementWsdlClass::setResult()
     * @uses SessionManagementWsdlClass::saveLastError()
     * @param SessionManagementStructUpdateSessionIsBroadcast $_sessionManagementStructUpdateSessionIsBroadcast
     * @return SessionManagementStructUpdateSessionIsBroadcastResponse
     */
    public function UpdateSessionIsBroadcast(SessionManagementStructUpdateSessionIsBroadcast $_sessionManagementStructUpdateSessionIsBroadcast)
    {
        try
        {
            return $this->setResult(self::getSoapClient()->UpdateSessionIsBroadcast($_sessionManagementStructUpdateSessionIsBroadcast));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Method to call the operation originally named UpdateSessionOwner
     * @uses SessionManagementWsdlClass::getSoapClient()
     * @uses SessionManagementWsdlClass::setResult()
     * @uses SessionManagementWsdlClass::saveLastError()
     * @param SessionManagementStructUpdateSessionOwner $_sessionManagementStructUpdateSessionOwner
     * @return SessionManagementStructUpdateSessionOwnerResponse
     */
    public function UpdateSessionOwner(SessionManagementStructUpdateSessionOwner $_sessionManagementStructUpdateSessionOwner)
    {
        try
        {
            return $this->setResult(self::getSoapClient()->UpdateSessionOwner($_sessionManagementStructUpdateSessionOwner));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Method to call the operation originally named UpdateSessionExternalId
     * @uses SessionManagementWsdlClass::getSoapClient()
     * @uses SessionManagementWsdlClass::setResult()
     * @uses SessionManagementWsdlClass::saveLastError()
     * @param SessionManagementStructUpdateSessionExternalId $_sessionManagementStructUpdateSessionExternalId
     * @return SessionManagementStructUpdateSessionExternalIdResponse
     */
    public function UpdateSessionExternalId(SessionManagementStructUpdateSessionExternalId $_sessionManagementStructUpdateSessionExternalId)
    {
        try
        {
            return $this->setResult(self::getSoapClient()->UpdateSessionExternalId($_sessionManagementStructUpdateSessionExternalId));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Method to call the operation originally named UpdateFolderName
     * @uses SessionManagementWsdlClass::getSoapClient()
     * @uses SessionManagementWsdlClass::setResult()
     * @uses SessionManagementWsdlClass::saveLastError()
     * @param SessionManagementStructUpdateFolderName $_sessionManagementStructUpdateFolderName
     * @return SessionManagementStructUpdateFolderNameResponse
     */
    public function UpdateFolderName(SessionManagementStructUpdateFolderName $_sessionManagementStructUpdateFolderName)
    {
        try
        {
            return $this->setResult(self::getSoapClient()->UpdateFolderName($_sessionManagementStructUpdateFolderName));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Method to call the operation originally named UpdateFolderDescription
     * @uses SessionManagementWsdlClass::getSoapClient()
     * @uses SessionManagementWsdlClass::setResult()
     * @uses SessionManagementWsdlClass::saveLastError()
     * @param SessionManagementStructUpdateFolderDescription $_sessionManagementStructUpdateFolderDescription
     * @return SessionManagementStructUpdateFolderDescriptionResponse
     */
    public function UpdateFolderDescription(SessionManagementStructUpdateFolderDescription $_sessionManagementStructUpdateFolderDescription)
    {
        try
        {
            return $this->setResult(self::getSoapClient()->UpdateFolderDescription($_sessionManagementStructUpdateFolderDescription));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Method to call the operation originally named UpdateFolderEnablePodcast
     * @uses SessionManagementWsdlClass::getSoapClient()
     * @uses SessionManagementWsdlClass::setResult()
     * @uses SessionManagementWsdlClass::saveLastError()
     * @param SessionManagementStructUpdateFolderEnablePodcast $_sessionManagementStructUpdateFolderEnablePodcast
     * @return SessionManagementStructUpdateFolderEnablePodcastResponse
     */
    public function UpdateFolderEnablePodcast(SessionManagementStructUpdateFolderEnablePodcast $_sessionManagementStructUpdateFolderEnablePodcast)
    {
        try
        {
            return $this->setResult(self::getSoapClient()->UpdateFolderEnablePodcast($_sessionManagementStructUpdateFolderEnablePodcast));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Method to call the operation originally named UpdateFolderAllowPublicNotes
     * @uses SessionManagementWsdlClass::getSoapClient()
     * @uses SessionManagementWsdlClass::setResult()
     * @uses SessionManagementWsdlClass::saveLastError()
     * @param SessionManagementStructUpdateFolderAllowPublicNotes $_sessionManagementStructUpdateFolderAllowPublicNotes
     * @return SessionManagementStructUpdateFolderAllowPublicNotesResponse
     */
    public function UpdateFolderAllowPublicNotes(SessionManagementStructUpdateFolderAllowPublicNotes $_sessionManagementStructUpdateFolderAllowPublicNotes)
    {
        try
        {
            return $this->setResult(self::getSoapClient()->UpdateFolderAllowPublicNotes($_sessionManagementStructUpdateFolderAllowPublicNotes));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Method to call the operation originally named UpdateFolderAllowSessionDownload
     * @uses SessionManagementWsdlClass::getSoapClient()
     * @uses SessionManagementWsdlClass::setResult()
     * @uses SessionManagementWsdlClass::saveLastError()
     * @param SessionManagementStructUpdateFolderAllowSessionDownload $_sessionManagementStructUpdateFolderAllowSessionDownload
     * @return SessionManagementStructUpdateFolderAllowSessionDownloadResponse
     */
    public function UpdateFolderAllowSessionDownload(SessionManagementStructUpdateFolderAllowSessionDownload $_sessionManagementStructUpdateFolderAllowSessionDownload)
    {
        try
        {
            return $this->setResult(self::getSoapClient()->UpdateFolderAllowSessionDownload($_sessionManagementStructUpdateFolderAllowSessionDownload));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Method to call the operation originally named UpdateFolderParent
     * @uses SessionManagementWsdlClass::getSoapClient()
     * @uses SessionManagementWsdlClass::setResult()
     * @uses SessionManagementWsdlClass::saveLastError()
     * @param SessionManagementStructUpdateFolderParent $_sessionManagementStructUpdateFolderParent
     * @return SessionManagementStructUpdateFolderParentResponse
     */
    public function UpdateFolderParent(SessionManagementStructUpdateFolderParent $_sessionManagementStructUpdateFolderParent)
    {
        try
        {
            return $this->setResult(self::getSoapClient()->UpdateFolderParent($_sessionManagementStructUpdateFolderParent));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Method to call the operation originally named UpdateFolderExternalId
     * @uses SessionManagementWsdlClass::getSoapClient()
     * @uses SessionManagementWsdlClass::setResult()
     * @uses SessionManagementWsdlClass::saveLastError()
     * @param SessionManagementStructUpdateFolderExternalId $_sessionManagementStructUpdateFolderExternalId
     * @return SessionManagementStructUpdateFolderExternalIdResponse
     */
    public function UpdateFolderExternalId(SessionManagementStructUpdateFolderExternalId $_sessionManagementStructUpdateFolderExternalId)
    {
        try
        {
            return $this->setResult(self::getSoapClient()->UpdateFolderExternalId($_sessionManagementStructUpdateFolderExternalId));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Method to call the operation originally named UpdateFolderExternalIdWithProvider
     * @uses SessionManagementWsdlClass::getSoapClient()
     * @uses SessionManagementWsdlClass::setResult()
     * @uses SessionManagementWsdlClass::saveLastError()
     * @param SessionManagementStructUpdateFolderExternalIdWithProvider $_sessionManagementStructUpdateFolderExternalIdWithProvider
     * @return SessionManagementStructUpdateFolderExternalIdWithProviderResponse
     */
    public function UpdateFolderExternalIdWithProvider(SessionManagementStructUpdateFolderExternalIdWithProvider $_sessionManagementStructUpdateFolderExternalIdWithProvider)
    {
        try
        {
            return $this->setResult(self::getSoapClient()->UpdateFolderExternalIdWithProvider($_sessionManagementStructUpdateFolderExternalIdWithProvider));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Method to call the operation originally named UpdateFoldersAvailabilityStartSettings
     * @uses SessionManagementWsdlClass::getSoapClient()
     * @uses SessionManagementWsdlClass::setResult()
     * @uses SessionManagementWsdlClass::saveLastError()
     * @param SessionManagementStructUpdateFoldersAvailabilityStartSettings $_sessionManagementStructUpdateFoldersAvailabilityStartSettings
     * @return SessionManagementStructUpdateFoldersAvailabilityStartSettingsResponse
     */
    public function UpdateFoldersAvailabilityStartSettings(SessionManagementStructUpdateFoldersAvailabilityStartSettings $_sessionManagementStructUpdateFoldersAvailabilityStartSettings)
    {
        try
        {
            return $this->setResult(self::getSoapClient()->UpdateFoldersAvailabilityStartSettings($_sessionManagementStructUpdateFoldersAvailabilityStartSettings));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Method to call the operation originally named UpdateFoldersAvailabilityEndSettings
     * @uses SessionManagementWsdlClass::getSoapClient()
     * @uses SessionManagementWsdlClass::setResult()
     * @uses SessionManagementWsdlClass::saveLastError()
     * @param SessionManagementStructUpdateFoldersAvailabilityEndSettings $_sessionManagementStructUpdateFoldersAvailabilityEndSettings
     * @return SessionManagementStructUpdateFoldersAvailabilityEndSettingsResponse
     */
    public function UpdateFoldersAvailabilityEndSettings(SessionManagementStructUpdateFoldersAvailabilityEndSettings $_sessionManagementStructUpdateFoldersAvailabilityEndSettings)
    {
        try
        {
            return $this->setResult(self::getSoapClient()->UpdateFoldersAvailabilityEndSettings($_sessionManagementStructUpdateFoldersAvailabilityEndSettings));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Method to call the operation originally named UpdateSessionsAvailabilityStartSettings
     * @uses SessionManagementWsdlClass::getSoapClient()
     * @uses SessionManagementWsdlClass::setResult()
     * @uses SessionManagementWsdlClass::saveLastError()
     * @param SessionManagementStructUpdateSessionsAvailabilityStartSettings $_sessionManagementStructUpdateSessionsAvailabilityStartSettings
     * @return SessionManagementStructUpdateSessionsAvailabilityStartSettingsResponse
     */
    public function UpdateSessionsAvailabilityStartSettings(SessionManagementStructUpdateSessionsAvailabilityStartSettings $_sessionManagementStructUpdateSessionsAvailabilityStartSettings)
    {
        try
        {
            return $this->setResult(self::getSoapClient()->UpdateSessionsAvailabilityStartSettings($_sessionManagementStructUpdateSessionsAvailabilityStartSettings));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Method to call the operation originally named UpdateSessionsAvailabilityEndSettings
     * @uses SessionManagementWsdlClass::getSoapClient()
     * @uses SessionManagementWsdlClass::setResult()
     * @uses SessionManagementWsdlClass::saveLastError()
     * @param SessionManagementStructUpdateSessionsAvailabilityEndSettings $_sessionManagementStructUpdateSessionsAvailabilityEndSettings
     * @return SessionManagementStructUpdateSessionsAvailabilityEndSettingsResponse
     */
    public function UpdateSessionsAvailabilityEndSettings(SessionManagementStructUpdateSessionsAvailabilityEndSettings $_sessionManagementStructUpdateSessionsAvailabilityEndSettings)
    {
        try
        {
            return $this->setResult(self::getSoapClient()->UpdateSessionsAvailabilityEndSettings($_sessionManagementStructUpdateSessionsAvailabilityEndSettings));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Returns the result
     * @see SessionManagementWsdlClass::getResult()
     * @return SessionManagementStructUpdateFolderAllowPublicNotesResponse|SessionManagementStructUpdateFolderAllowSessionDownloadResponse|SessionManagementStructUpdateFolderDescriptionResponse|SessionManagementStructUpdateFolderEnablePodcastResponse|SessionManagementStructUpdateFolderExternalIdResponse|SessionManagementStructUpdateFolderExternalIdWithProviderResponse|SessionManagementStructUpdateFolderNameResponse|SessionManagementStructUpdateFolderParentResponse|SessionManagementStructUpdateFoldersAvailabilityEndSettingsResponse|SessionManagementStructUpdateFoldersAvailabilityStartSettingsResponse|SessionManagementStructUpdateSessionDescriptionResponse|SessionManagementStructUpdateSessionExternalIdResponse|SessionManagementStructUpdateSessionIsBroadcastResponse|SessionManagementStructUpdateSessionNameResponse|SessionManagementStructUpdateSessionOwnerResponse|SessionManagementStructUpdateSessionsAvailabilityEndSettingsResponse|SessionManagementStructUpdateSessionsAvailabilityStartSettingsResponse
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
