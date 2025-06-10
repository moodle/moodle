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
 * File for class SessionManagementStructSession
 * @package SessionManagement
 * @subpackage Structs
 * @author Panopto
 * @version 20150429-01
 * @date 2017-01-19
 */
/**
 * This class stands for SessionManagementStructSession originally named Session
 * Meta informations extracted from the WSDL
 * - from schema : {@link http://demo.hosted.panopto.com/Panopto/PublicAPI/4.6/SessionManagement.svc?xsd=xsd5}
 * @package SessionManagement
 * @subpackage Structs
 * @author Panopto
 * @version 20150429-01
 * @date 2017-01-19
 */
class SessionManagementStructSession extends SessionManagementWsdlClass
{
    /**
     * The CreatorId
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - pattern : [\da-fA-F]{8}-[\da-fA-F]{4}-[\da-fA-F]{4}-[\da-fA-F]{4}-[\da-fA-F]{12}
     * @var string
     */
    public $CreatorId;
    /**
     * The Description
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $Description;
    /**
     * The Duration
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var double
     */
    public $Duration;
    /**
     * The EditorUrl
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $EditorUrl;
    /**
     * The ExternalId
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $ExternalId;
    /**
     * The FolderId
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - pattern : [\da-fA-F]{8}-[\da-fA-F]{4}-[\da-fA-F]{4}-[\da-fA-F]{4}-[\da-fA-F]{12}
     * @var string
     */
    public $FolderId;
    /**
     * The FolderName
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $FolderName;
    /**
     * The Id
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - pattern : [\da-fA-F]{8}-[\da-fA-F]{4}-[\da-fA-F]{4}-[\da-fA-F]{4}-[\da-fA-F]{12}
     * @var string
     */
    public $Id;
    /**
     * The IosVideoUrl
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $IosVideoUrl;
    /**
     * The IsBroadcast
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var boolean
     */
    public $IsBroadcast;
    /**
     * The IsDownloadable
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var boolean
     */
    public $IsDownloadable;
    /**
     * The MP3Url
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $MP3Url;
    /**
     * The MP4Url
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $MP4Url;
    /**
     * The Name
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $Name;
    /**
     * The NotesURL
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $NotesURL;
    /**
     * The OutputsPageUrl
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $OutputsPageUrl;
    /**
     * The RemoteRecorderIds
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var SessionManagementStructArrayOfguid
     */
    public $RemoteRecorderIds;
    /**
     * The SharePageUrl
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $SharePageUrl;
    /**
     * The StartTime
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var dateTime
     */
    public $StartTime;
    /**
     * The State
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var SessionManagementEnumSessionState
     */
    public $State;
    /**
     * The StatusMessage
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $StatusMessage;
    /**
     * The ThumbUrl
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $ThumbUrl;
    /**
     * The ViewerUrl
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $ViewerUrl;
    /**
     * Constructor method for Session
     * @see parent::__construct()
     * @param string $_creatorId
     * @param string $_description
     * @param double $_duration
     * @param string $_editorUrl
     * @param string $_externalId
     * @param string $_folderId
     * @param string $_folderName
     * @param string $_id
     * @param string $_iosVideoUrl
     * @param boolean $_isBroadcast
     * @param boolean $_isDownloadable
     * @param string $_mP3Url
     * @param string $_mP4Url
     * @param string $_name
     * @param string $_notesURL
     * @param string $_outputsPageUrl
     * @param SessionManagementStructArrayOfguid $_remoteRecorderIds
     * @param string $_sharePageUrl
     * @param dateTime $_startTime
     * @param SessionManagementEnumSessionState $_state
     * @param string $_statusMessage
     * @param string $_thumbUrl
     * @param string $_viewerUrl
     * @return SessionManagementStructSession
     */
    public function __construct($_creatorId = NULL,$_description = NULL,$_duration = NULL,$_editorUrl = NULL,$_externalId = NULL,$_folderId = NULL,$_folderName = NULL,$_id = NULL,$_iosVideoUrl = NULL,$_isBroadcast = NULL,$_isDownloadable = NULL,$_mP3Url = NULL,$_mP4Url = NULL,$_name = NULL,$_notesURL = NULL,$_outputsPageUrl = NULL,$_remoteRecorderIds = NULL,$_sharePageUrl = NULL,$_startTime = NULL,$_state = NULL,$_statusMessage = NULL,$_thumbUrl = NULL,$_viewerUrl = NULL)
    {
        parent::__construct(array('CreatorId'=>$_creatorId,'Description'=>$_description,'Duration'=>$_duration,'EditorUrl'=>$_editorUrl,'ExternalId'=>$_externalId,'FolderId'=>$_folderId,'FolderName'=>$_folderName,'Id'=>$_id,'IosVideoUrl'=>$_iosVideoUrl,'IsBroadcast'=>$_isBroadcast,'IsDownloadable'=>$_isDownloadable,'MP3Url'=>$_mP3Url,'MP4Url'=>$_mP4Url,'Name'=>$_name,'NotesURL'=>$_notesURL,'OutputsPageUrl'=>$_outputsPageUrl,'RemoteRecorderIds'=>($_remoteRecorderIds instanceof SessionManagementStructArrayOfguid)?$_remoteRecorderIds:new SessionManagementStructArrayOfguid($_remoteRecorderIds),'SharePageUrl'=>$_sharePageUrl,'StartTime'=>$_startTime,'State'=>$_state,'StatusMessage'=>$_statusMessage,'ThumbUrl'=>$_thumbUrl,'ViewerUrl'=>$_viewerUrl),false);
    }
    /**
     * Get CreatorId value
     * @return string|null
     */
    public function getCreatorId()
    {
        return $this->CreatorId;
    }
    /**
     * Set CreatorId value
     * @param string $_creatorId the CreatorId
     * @return string
     */
    public function setCreatorId($_creatorId)
    {
        return ($this->CreatorId = $_creatorId);
    }
    /**
     * Get Description value
     * @return string|null
     */
    public function getDescription()
    {
        return $this->Description;
    }
    /**
     * Set Description value
     * @param string $_description the Description
     * @return string
     */
    public function setDescription($_description)
    {
        return ($this->Description = $_description);
    }
    /**
     * Get Duration value
     * @return double|null
     */
    public function getDuration()
    {
        return $this->Duration;
    }
    /**
     * Set Duration value
     * @param double $_duration the Duration
     * @return double
     */
    public function setDuration($_duration)
    {
        return ($this->Duration = $_duration);
    }
    /**
     * Get EditorUrl value
     * @return string|null
     */
    public function getEditorUrl()
    {
        return $this->EditorUrl;
    }
    /**
     * Set EditorUrl value
     * @param string $_editorUrl the EditorUrl
     * @return string
     */
    public function setEditorUrl($_editorUrl)
    {
        return ($this->EditorUrl = $_editorUrl);
    }
    /**
     * Get ExternalId value
     * @return string|null
     */
    public function getExternalId()
    {
        return $this->ExternalId;
    }
    /**
     * Set ExternalId value
     * @param string $_externalId the ExternalId
     * @return string
     */
    public function setExternalId($_externalId)
    {
        return ($this->ExternalId = $_externalId);
    }
    /**
     * Get FolderId value
     * @return string|null
     */
    public function getFolderId()
    {
        return $this->FolderId;
    }
    /**
     * Set FolderId value
     * @param string $_folderId the FolderId
     * @return string
     */
    public function setFolderId($_folderId)
    {
        return ($this->FolderId = $_folderId);
    }
    /**
     * Get FolderName value
     * @return string|null
     */
    public function getFolderName()
    {
        return $this->FolderName;
    }
    /**
     * Set FolderName value
     * @param string $_folderName the FolderName
     * @return string
     */
    public function setFolderName($_folderName)
    {
        return ($this->FolderName = $_folderName);
    }
    /**
     * Get Id value
     * @return string|null
     */
    public function getId()
    {
        return $this->Id;
    }
    /**
     * Set Id value
     * @param string $_id the Id
     * @return string
     */
    public function setId($_id)
    {
        return ($this->Id = $_id);
    }
    /**
     * Get IosVideoUrl value
     * @return string|null
     */
    public function getIosVideoUrl()
    {
        return $this->IosVideoUrl;
    }
    /**
     * Set IosVideoUrl value
     * @param string $_iosVideoUrl the IosVideoUrl
     * @return string
     */
    public function setIosVideoUrl($_iosVideoUrl)
    {
        return ($this->IosVideoUrl = $_iosVideoUrl);
    }
    /**
     * Get IsBroadcast value
     * @return boolean|null
     */
    public function getIsBroadcast()
    {
        return $this->IsBroadcast;
    }
    /**
     * Set IsBroadcast value
     * @param boolean $_isBroadcast the IsBroadcast
     * @return boolean
     */
    public function setIsBroadcast($_isBroadcast)
    {
        return ($this->IsBroadcast = $_isBroadcast);
    }
    /**
     * Get IsDownloadable value
     * @return boolean|null
     */
    public function getIsDownloadable()
    {
        return $this->IsDownloadable;
    }
    /**
     * Set IsDownloadable value
     * @param boolean $_isDownloadable the IsDownloadable
     * @return boolean
     */
    public function setIsDownloadable($_isDownloadable)
    {
        return ($this->IsDownloadable = $_isDownloadable);
    }
    /**
     * Get MP3Url value
     * @return string|null
     */
    public function getMP3Url()
    {
        return $this->MP3Url;
    }
    /**
     * Set MP3Url value
     * @param string $_mP3Url the MP3Url
     * @return string
     */
    public function setMP3Url($_mP3Url)
    {
        return ($this->MP3Url = $_mP3Url);
    }
    /**
     * Get MP4Url value
     * @return string|null
     */
    public function getMP4Url()
    {
        return $this->MP4Url;
    }
    /**
     * Set MP4Url value
     * @param string $_mP4Url the MP4Url
     * @return string
     */
    public function setMP4Url($_mP4Url)
    {
        return ($this->MP4Url = $_mP4Url);
    }
    /**
     * Get Name value
     * @return string|null
     */
    public function getName()
    {
        return $this->Name;
    }
    /**
     * Set Name value
     * @param string $_name the Name
     * @return string
     */
    public function setName($_name)
    {
        return ($this->Name = $_name);
    }
    /**
     * Get NotesURL value
     * @return string|null
     */
    public function getNotesURL()
    {
        return $this->NotesURL;
    }
    /**
     * Set NotesURL value
     * @param string $_notesURL the NotesURL
     * @return string
     */
    public function setNotesURL($_notesURL)
    {
        return ($this->NotesURL = $_notesURL);
    }
    /**
     * Get OutputsPageUrl value
     * @return string|null
     */
    public function getOutputsPageUrl()
    {
        return $this->OutputsPageUrl;
    }
    /**
     * Set OutputsPageUrl value
     * @param string $_outputsPageUrl the OutputsPageUrl
     * @return string
     */
    public function setOutputsPageUrl($_outputsPageUrl)
    {
        return ($this->OutputsPageUrl = $_outputsPageUrl);
    }
    /**
     * Get RemoteRecorderIds value
     * @return SessionManagementStructArrayOfguid|null
     */
    public function getRemoteRecorderIds()
    {
        return $this->RemoteRecorderIds;
    }
    /**
     * Set RemoteRecorderIds value
     * @param SessionManagementStructArrayOfguid $_remoteRecorderIds the RemoteRecorderIds
     * @return SessionManagementStructArrayOfguid
     */
    public function setRemoteRecorderIds($_remoteRecorderIds)
    {
        return ($this->RemoteRecorderIds = $_remoteRecorderIds);
    }
    /**
     * Get SharePageUrl value
     * @return string|null
     */
    public function getSharePageUrl()
    {
        return $this->SharePageUrl;
    }
    /**
     * Set SharePageUrl value
     * @param string $_sharePageUrl the SharePageUrl
     * @return string
     */
    public function setSharePageUrl($_sharePageUrl)
    {
        return ($this->SharePageUrl = $_sharePageUrl);
    }
    /**
     * Get StartTime value
     * @return dateTime|null
     */
    public function getStartTime()
    {
        return $this->StartTime;
    }
    /**
     * Set StartTime value
     * @param dateTime $_startTime the StartTime
     * @return dateTime
     */
    public function setStartTime($_startTime)
    {
        return ($this->StartTime = $_startTime);
    }
    /**
     * Get State value
     * @return SessionManagementEnumSessionState|null
     */
    public function getState()
    {
        return $this->State;
    }
    /**
     * Set State value
     * @uses SessionManagementEnumSessionState::valueIsValid()
     * @param SessionManagementEnumSessionState $_state the State
     * @return SessionManagementEnumSessionState
     */
    public function setState($_state)
    {
        if(!SessionManagementEnumSessionState::valueIsValid($_state))
        {
            return false;
        }
        return ($this->State = $_state);
    }
    /**
     * Get StatusMessage value
     * @return string|null
     */
    public function getStatusMessage()
    {
        return $this->StatusMessage;
    }
    /**
     * Set StatusMessage value
     * @param string $_statusMessage the StatusMessage
     * @return string
     */
    public function setStatusMessage($_statusMessage)
    {
        return ($this->StatusMessage = $_statusMessage);
    }
    /**
     * Get ThumbUrl value
     * @return string|null
     */
    public function getThumbUrl()
    {
        return $this->ThumbUrl;
    }
    /**
     * Set ThumbUrl value
     * @param string $_thumbUrl the ThumbUrl
     * @return string
     */
    public function setThumbUrl($_thumbUrl)
    {
        return ($this->ThumbUrl = $_thumbUrl);
    }
    /**
     * Get ViewerUrl value
     * @return string|null
     */
    public function getViewerUrl()
    {
        return $this->ViewerUrl;
    }
    /**
     * Set ViewerUrl value
     * @param string $_viewerUrl the ViewerUrl
     * @return string
     */
    public function setViewerUrl($_viewerUrl)
    {
        return ($this->ViewerUrl = $_viewerUrl);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see SessionManagementWsdlClass::__set_state()
     * @uses SessionManagementWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return SessionManagementStructSession
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
