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
 * File for class SessionManagementStructFolderAvailabilitySettings
 * @package SessionManagement
 * @subpackage Structs
 * @author Panopto
 * @version 20150429-01
 * @date 2017-01-19
 */
/**
 * This class stands for SessionManagementStructFolderAvailabilitySettings originally named FolderAvailabilitySettings
 * Meta informations extracted from the WSDL
 * - from schema : {@link http://demo.hosted.panopto.com/Panopto/PublicAPI/4.6/SessionManagement.svc?xsd=xsd3}
 * @package SessionManagement
 * @subpackage Structs
 * @author Panopto
 * @version 20150429-01
 * @date 2017-01-19
 */
class SessionManagementStructFolderAvailabilitySettings extends SessionManagementWsdlClass
{
    /**
     * The EndSettingDate
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var SessionManagementStructDateTimeOffset
     */
    public $EndSettingDate;
    /**
     * The EndSettingType
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var SessionManagementEnumFolderEndSettingType
     */
    public $EndSettingType;
    /**
     * The FolderId
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - pattern : [\da-fA-F]{8}-[\da-fA-F]{4}-[\da-fA-F]{4}-[\da-fA-F]{4}-[\da-fA-F]{12}
     * @var string
     */
    public $FolderId;
    /**
     * The StartSettingDate
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var SessionManagementStructDateTimeOffset
     */
    public $StartSettingDate;
    /**
     * The StartSettingType
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var SessionManagementEnumFolderStartSettingType
     */
    public $StartSettingType;
    /**
     * Constructor method for FolderAvailabilitySettings
     * @see parent::__construct()
     * @param SessionManagementStructDateTimeOffset $_endSettingDate
     * @param SessionManagementEnumFolderEndSettingType $_endSettingType
     * @param string $_folderId
     * @param SessionManagementStructDateTimeOffset $_startSettingDate
     * @param SessionManagementEnumFolderStartSettingType $_startSettingType
     * @return SessionManagementStructFolderAvailabilitySettings
     */
    public function __construct($_endSettingDate = NULL,$_endSettingType = NULL,$_folderId = NULL,$_startSettingDate = NULL,$_startSettingType = NULL)
    {
        parent::__construct(array('EndSettingDate'=>$_endSettingDate,'EndSettingType'=>$_endSettingType,'FolderId'=>$_folderId,'StartSettingDate'=>$_startSettingDate,'StartSettingType'=>$_startSettingType),false);
    }
    /**
     * Get EndSettingDate value
     * @return SessionManagementStructDateTimeOffset|null
     */
    public function getEndSettingDate()
    {
        return $this->EndSettingDate;
    }
    /**
     * Set EndSettingDate value
     * @param SessionManagementStructDateTimeOffset $_endSettingDate the EndSettingDate
     * @return SessionManagementStructDateTimeOffset
     */
    public function setEndSettingDate($_endSettingDate)
    {
        return ($this->EndSettingDate = $_endSettingDate);
    }
    /**
     * Get EndSettingType value
     * @return SessionManagementEnumFolderEndSettingType|null
     */
    public function getEndSettingType()
    {
        return $this->EndSettingType;
    }
    /**
     * Set EndSettingType value
     * @uses SessionManagementEnumFolderEndSettingType::valueIsValid()
     * @param SessionManagementEnumFolderEndSettingType $_endSettingType the EndSettingType
     * @return SessionManagementEnumFolderEndSettingType
     */
    public function setEndSettingType($_endSettingType)
    {
        if(!SessionManagementEnumFolderEndSettingType::valueIsValid($_endSettingType))
        {
            return false;
        }
        return ($this->EndSettingType = $_endSettingType);
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
     * Get StartSettingDate value
     * @return SessionManagementStructDateTimeOffset|null
     */
    public function getStartSettingDate()
    {
        return $this->StartSettingDate;
    }
    /**
     * Set StartSettingDate value
     * @param SessionManagementStructDateTimeOffset $_startSettingDate the StartSettingDate
     * @return SessionManagementStructDateTimeOffset
     */
    public function setStartSettingDate($_startSettingDate)
    {
        return ($this->StartSettingDate = $_startSettingDate);
    }
    /**
     * Get StartSettingType value
     * @return SessionManagementEnumFolderStartSettingType|null
     */
    public function getStartSettingType()
    {
        return $this->StartSettingType;
    }
    /**
     * Set StartSettingType value
     * @uses SessionManagementEnumFolderStartSettingType::valueIsValid()
     * @param SessionManagementEnumFolderStartSettingType $_startSettingType the StartSettingType
     * @return SessionManagementEnumFolderStartSettingType
     */
    public function setStartSettingType($_startSettingType)
    {
        if(!SessionManagementEnumFolderStartSettingType::valueIsValid($_startSettingType))
        {
            return false;
        }
        return ($this->StartSettingType = $_startSettingType);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see SessionManagementWsdlClass::__set_state()
     * @uses SessionManagementWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return SessionManagementStructFolderAvailabilitySettings
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
