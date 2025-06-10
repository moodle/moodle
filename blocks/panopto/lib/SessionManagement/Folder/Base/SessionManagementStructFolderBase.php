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
 * File for class SessionManagementStructFolderBase
 * @package SessionManagement
 * @subpackage Structs
 * @author Panopto
 * @version 20150429-01
 * @date 2017-01-19
 */
/**
 * This class stands for SessionManagementStructFolderBase originally named FolderBase
 * Meta informations extracted from the WSDL
 * - from schema : {@link http://demo.hosted.panopto.com/Panopto/PublicAPI/4.6/SessionManagement.svc?xsd=xsd3}
 * @package SessionManagement
 * @subpackage Structs
 * @author Panopto
 * @version 20150429-01
 * @date 2017-01-19
 */
class SessionManagementStructFolderBase extends SessionManagementWsdlClass
{
    /**
     * The AllowPublicNotes
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var boolean
     */
    public $AllowPublicNotes;
    /**
     * The AllowSessionDownload
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var boolean
     */
    public $AllowSessionDownload;
    /**
     * The AudioPodcastITunesUrl
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $AudioPodcastITunesUrl;
    /**
     * The AudioRssUrl
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $AudioRssUrl;
    /**
     * The ChildFolders
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var SessionManagementStructArrayOfguid
     */
    public $ChildFolders;
    /**
     * The DeliveriesHaveSpecifiedOrder
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var boolean
     */
    public $DeliveriesHaveSpecifiedOrder;
    /**
     * The Description
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $Description;
    /**
     * The EmbedUploaderUrl
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $EmbedUploaderUrl;
    /**
     * The EmbedUrl
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $EmbedUrl;
    /**
     * The EnablePodcast
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var boolean
     */
    public $EnablePodcast;
    /**
     * The Id
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - pattern : [\da-fA-F]{8}-[\da-fA-F]{4}-[\da-fA-F]{4}-[\da-fA-F]{4}-[\da-fA-F]{12}
     * @var string
     */
    public $Id;
    /**
     * The IsPublic
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var boolean
     */
    public $IsPublic;
    /**
     * The ListUrl
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $ListUrl;
    /**
     * The Name
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $Name;
    /**
     * The ParentFolder
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * - pattern : [\da-fA-F]{8}-[\da-fA-F]{4}-[\da-fA-F]{4}-[\da-fA-F]{4}-[\da-fA-F]{12}
     * @var string
     */
    public $ParentFolder;
    /**
     * The Presenters
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var SessionManagementStructArrayOfstring
     */
    public $Presenters;
    /**
     * The Sessions
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var SessionManagementStructArrayOfguid
     */
    public $Sessions;
    /**
     * The SettingsUrl
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $SettingsUrl;
    /**
     * The VideoPodcastITunesUrl
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $VideoPodcastITunesUrl;
    /**
     * The VideoRssUrl
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $VideoRssUrl;
    /**
     * Constructor method for FolderBase
     * @see parent::__construct()
     * @param boolean $_allowPublicNotes
     * @param boolean $_allowSessionDownload
     * @param string $_audioPodcastITunesUrl
     * @param string $_audioRssUrl
     * @param SessionManagementStructArrayOfguid $_childFolders
     * @param boolean $_deliveriesHaveSpecifiedOrder
     * @param string $_description
     * @param string $_embedUploaderUrl
     * @param string $_embedUrl
     * @param boolean $_enablePodcast
     * @param string $_id
     * @param boolean $_isPublic
     * @param string $_listUrl
     * @param string $_name
     * @param string $_parentFolder
     * @param SessionManagementStructArrayOfstring $_presenters
     * @param SessionManagementStructArrayOfguid $_sessions
     * @param string $_settingsUrl
     * @param string $_videoPodcastITunesUrl
     * @param string $_videoRssUrl
     * @return SessionManagementStructFolderBase
     */
    public function __construct($_allowPublicNotes = NULL,$_allowSessionDownload = NULL,$_audioPodcastITunesUrl = NULL,$_audioRssUrl = NULL,$_childFolders = NULL,$_deliveriesHaveSpecifiedOrder = NULL,$_description = NULL,$_embedUploaderUrl = NULL,$_embedUrl = NULL,$_enablePodcast = NULL,$_id = NULL,$_isPublic = NULL,$_listUrl = NULL,$_name = NULL,$_parentFolder = NULL,$_presenters = NULL,$_sessions = NULL,$_settingsUrl = NULL,$_videoPodcastITunesUrl = NULL,$_videoRssUrl = NULL)
    {
        parent::__construct(array('AllowPublicNotes'=>$_allowPublicNotes,'AllowSessionDownload'=>$_allowSessionDownload,'AudioPodcastITunesUrl'=>$_audioPodcastITunesUrl,'AudioRssUrl'=>$_audioRssUrl,'ChildFolders'=>($_childFolders instanceof SessionManagementStructArrayOfguid)?$_childFolders:new SessionManagementStructArrayOfguid($_childFolders),'DeliveriesHaveSpecifiedOrder'=>$_deliveriesHaveSpecifiedOrder,'Description'=>$_description,'EmbedUploaderUrl'=>$_embedUploaderUrl,'EmbedUrl'=>$_embedUrl,'EnablePodcast'=>$_enablePodcast,'Id'=>$_id,'IsPublic'=>$_isPublic,'ListUrl'=>$_listUrl,'Name'=>$_name,'ParentFolder'=>$_parentFolder,'Presenters'=>($_presenters instanceof SessionManagementStructArrayOfstring)?$_presenters:new SessionManagementStructArrayOfstring($_presenters),'Sessions'=>($_sessions instanceof SessionManagementStructArrayOfguid)?$_sessions:new SessionManagementStructArrayOfguid($_sessions),'SettingsUrl'=>$_settingsUrl,'VideoPodcastITunesUrl'=>$_videoPodcastITunesUrl,'VideoRssUrl'=>$_videoRssUrl),false);
    }
    /**
     * Get AllowPublicNotes value
     * @return boolean|null
     */
    public function getAllowPublicNotes()
    {
        return $this->AllowPublicNotes;
    }
    /**
     * Set AllowPublicNotes value
     * @param boolean $_allowPublicNotes the AllowPublicNotes
     * @return boolean
     */
    public function setAllowPublicNotes($_allowPublicNotes)
    {
        return ($this->AllowPublicNotes = $_allowPublicNotes);
    }
    /**
     * Get AllowSessionDownload value
     * @return boolean|null
     */
    public function getAllowSessionDownload()
    {
        return $this->AllowSessionDownload;
    }
    /**
     * Set AllowSessionDownload value
     * @param boolean $_allowSessionDownload the AllowSessionDownload
     * @return boolean
     */
    public function setAllowSessionDownload($_allowSessionDownload)
    {
        return ($this->AllowSessionDownload = $_allowSessionDownload);
    }
    /**
     * Get AudioPodcastITunesUrl value
     * @return string|null
     */
    public function getAudioPodcastITunesUrl()
    {
        return $this->AudioPodcastITunesUrl;
    }
    /**
     * Set AudioPodcastITunesUrl value
     * @param string $_audioPodcastITunesUrl the AudioPodcastITunesUrl
     * @return string
     */
    public function setAudioPodcastITunesUrl($_audioPodcastITunesUrl)
    {
        return ($this->AudioPodcastITunesUrl = $_audioPodcastITunesUrl);
    }
    /**
     * Get AudioRssUrl value
     * @return string|null
     */
    public function getAudioRssUrl()
    {
        return $this->AudioRssUrl;
    }
    /**
     * Set AudioRssUrl value
     * @param string $_audioRssUrl the AudioRssUrl
     * @return string
     */
    public function setAudioRssUrl($_audioRssUrl)
    {
        return ($this->AudioRssUrl = $_audioRssUrl);
    }
    /**
     * Get ChildFolders value
     * @return SessionManagementStructArrayOfguid|null
     */
    public function getChildFolders()
    {
        return $this->ChildFolders;
    }
    /**
     * Set ChildFolders value
     * @param SessionManagementStructArrayOfguid $_childFolders the ChildFolders
     * @return SessionManagementStructArrayOfguid
     */
    public function setChildFolders($_childFolders)
    {
        return ($this->ChildFolders = $_childFolders);
    }
    /**
     * Get DeliveriesHaveSpecifiedOrder value
     * @return boolean|null
     */
    public function getDeliveriesHaveSpecifiedOrder()
    {
        return $this->DeliveriesHaveSpecifiedOrder;
    }
    /**
     * Set DeliveriesHaveSpecifiedOrder value
     * @param boolean $_deliveriesHaveSpecifiedOrder the DeliveriesHaveSpecifiedOrder
     * @return boolean
     */
    public function setDeliveriesHaveSpecifiedOrder($_deliveriesHaveSpecifiedOrder)
    {
        return ($this->DeliveriesHaveSpecifiedOrder = $_deliveriesHaveSpecifiedOrder);
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
     * Get EmbedUploaderUrl value
     * @return string|null
     */
    public function getEmbedUploaderUrl()
    {
        return $this->EmbedUploaderUrl;
    }
    /**
     * Set EmbedUploaderUrl value
     * @param string $_embedUploaderUrl the EmbedUploaderUrl
     * @return string
     */
    public function setEmbedUploaderUrl($_embedUploaderUrl)
    {
        return ($this->EmbedUploaderUrl = $_embedUploaderUrl);
    }
    /**
     * Get EmbedUrl value
     * @return string|null
     */
    public function getEmbedUrl()
    {
        return $this->EmbedUrl;
    }
    /**
     * Set EmbedUrl value
     * @param string $_embedUrl the EmbedUrl
     * @return string
     */
    public function setEmbedUrl($_embedUrl)
    {
        return ($this->EmbedUrl = $_embedUrl);
    }
    /**
     * Get EnablePodcast value
     * @return boolean|null
     */
    public function getEnablePodcast()
    {
        return $this->EnablePodcast;
    }
    /**
     * Set EnablePodcast value
     * @param boolean $_enablePodcast the EnablePodcast
     * @return boolean
     */
    public function setEnablePodcast($_enablePodcast)
    {
        return ($this->EnablePodcast = $_enablePodcast);
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
     * Get IsPublic value
     * @return boolean|null
     */
    public function getIsPublic()
    {
        return $this->IsPublic;
    }
    /**
     * Set IsPublic value
     * @param boolean $_isPublic the IsPublic
     * @return boolean
     */
    public function setIsPublic($_isPublic)
    {
        return ($this->IsPublic = $_isPublic);
    }
    /**
     * Get ListUrl value
     * @return string|null
     */
    public function getListUrl()
    {
        return $this->ListUrl;
    }
    /**
     * Set ListUrl value
     * @param string $_listUrl the ListUrl
     * @return string
     */
    public function setListUrl($_listUrl)
    {
        return ($this->ListUrl = $_listUrl);
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
     * Get ParentFolder value
     * @return string|null
     */
    public function getParentFolder()
    {
        return $this->ParentFolder;
    }
    /**
     * Set ParentFolder value
     * @param string $_parentFolder the ParentFolder
     * @return string
     */
    public function setParentFolder($_parentFolder)
    {
        return ($this->ParentFolder = $_parentFolder);
    }
    /**
     * Get Presenters value
     * @return SessionManagementStructArrayOfstring|null
     */
    public function getPresenters()
    {
        return $this->Presenters;
    }
    /**
     * Set Presenters value
     * @param SessionManagementStructArrayOfstring $_presenters the Presenters
     * @return SessionManagementStructArrayOfstring
     */
    public function setPresenters($_presenters)
    {
        return ($this->Presenters = $_presenters);
    }
    /**
     * Get Sessions value
     * @return SessionManagementStructArrayOfguid|null
     */
    public function getSessions()
    {
        return $this->Sessions;
    }
    /**
     * Set Sessions value
     * @param SessionManagementStructArrayOfguid $_sessions the Sessions
     * @return SessionManagementStructArrayOfguid
     */
    public function setSessions($_sessions)
    {
        return ($this->Sessions = $_sessions);
    }
    /**
     * Get SettingsUrl value
     * @return string|null
     */
    public function getSettingsUrl()
    {
        return $this->SettingsUrl;
    }
    /**
     * Set SettingsUrl value
     * @param string $_settingsUrl the SettingsUrl
     * @return string
     */
    public function setSettingsUrl($_settingsUrl)
    {
        return ($this->SettingsUrl = $_settingsUrl);
    }
    /**
     * Get VideoPodcastITunesUrl value
     * @return string|null
     */
    public function getVideoPodcastITunesUrl()
    {
        return $this->VideoPodcastITunesUrl;
    }
    /**
     * Set VideoPodcastITunesUrl value
     * @param string $_videoPodcastITunesUrl the VideoPodcastITunesUrl
     * @return string
     */
    public function setVideoPodcastITunesUrl($_videoPodcastITunesUrl)
    {
        return ($this->VideoPodcastITunesUrl = $_videoPodcastITunesUrl);
    }
    /**
     * Get VideoRssUrl value
     * @return string|null
     */
    public function getVideoRssUrl()
    {
        return $this->VideoRssUrl;
    }
    /**
     * Set VideoRssUrl value
     * @param string $_videoRssUrl the VideoRssUrl
     * @return string
     */
    public function setVideoRssUrl($_videoRssUrl)
    {
        return ($this->VideoRssUrl = $_videoRssUrl);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see SessionManagementWsdlClass::__set_state()
     * @uses SessionManagementWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return SessionManagementStructFolderBase
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
