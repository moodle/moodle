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
 * File for class SessionManagementStructRecorderDownloadUrlResponse
 * @package SessionManagement
 * @subpackage Structs
 * @author Panopto
 * @version 20150429-01
 * @date 2017-01-19
 */
/**
 * This class stands for SessionManagementStructRecorderDownloadUrlResponse originally named RecorderDownloadUrlResponse
 * Meta informations extracted from the WSDL
 * - from schema : {@link http://demo.hosted.panopto.com/Panopto/PublicAPI/4.6/SessionManagement.svc?xsd=xsd2}
 * @package SessionManagement
 * @subpackage Structs
 * @author Panopto
 * @version 20150429-01
 * @date 2017-01-19
 */
class SessionManagementStructRecorderDownloadUrlResponse extends SessionManagementWsdlClass
{
    /**
     * The MacRecorderDownloadUrl
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $MacRecorderDownloadUrl;
    /**
     * The WindowsRecorderDownloadUrl
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $WindowsRecorderDownloadUrl;
    /**
     * The WindowsRemoteRecorderDownloadUrl
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $WindowsRemoteRecorderDownloadUrl;
    /**
     * Constructor method for RecorderDownloadUrlResponse
     * @see parent::__construct()
     * @param string $_macRecorderDownloadUrl
     * @param string $_windowsRecorderDownloadUrl
     * @param string $_windowsRemoteRecorderDownloadUrl
     * @return SessionManagementStructRecorderDownloadUrlResponse
     */
    public function __construct($_macRecorderDownloadUrl = NULL,$_windowsRecorderDownloadUrl = NULL,$_windowsRemoteRecorderDownloadUrl = NULL)
    {
        parent::__construct(array('MacRecorderDownloadUrl'=>$_macRecorderDownloadUrl,'WindowsRecorderDownloadUrl'=>$_windowsRecorderDownloadUrl,'WindowsRemoteRecorderDownloadUrl'=>$_windowsRemoteRecorderDownloadUrl),false);
    }
    /**
     * Get MacRecorderDownloadUrl value
     * @return string|null
     */
    public function getMacRecorderDownloadUrl()
    {
        return $this->MacRecorderDownloadUrl;
    }
    /**
     * Set MacRecorderDownloadUrl value
     * @param string $_macRecorderDownloadUrl the MacRecorderDownloadUrl
     * @return string
     */
    public function setMacRecorderDownloadUrl($_macRecorderDownloadUrl)
    {
        return ($this->MacRecorderDownloadUrl = $_macRecorderDownloadUrl);
    }
    /**
     * Get WindowsRecorderDownloadUrl value
     * @return string|null
     */
    public function getWindowsRecorderDownloadUrl()
    {
        return $this->WindowsRecorderDownloadUrl;
    }
    /**
     * Set WindowsRecorderDownloadUrl value
     * @param string $_windowsRecorderDownloadUrl the WindowsRecorderDownloadUrl
     * @return string
     */
    public function setWindowsRecorderDownloadUrl($_windowsRecorderDownloadUrl)
    {
        return ($this->WindowsRecorderDownloadUrl = $_windowsRecorderDownloadUrl);
    }
    /**
     * Get WindowsRemoteRecorderDownloadUrl value
     * @return string|null
     */
    public function getWindowsRemoteRecorderDownloadUrl()
    {
        return $this->WindowsRemoteRecorderDownloadUrl;
    }
    /**
     * Set WindowsRemoteRecorderDownloadUrl value
     * @param string $_windowsRemoteRecorderDownloadUrl the WindowsRemoteRecorderDownloadUrl
     * @return string
     */
    public function setWindowsRemoteRecorderDownloadUrl($_windowsRemoteRecorderDownloadUrl)
    {
        return ($this->WindowsRemoteRecorderDownloadUrl = $_windowsRemoteRecorderDownloadUrl);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see SessionManagementWsdlClass::__set_state()
     * @uses SessionManagementWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return SessionManagementStructRecorderDownloadUrlResponse
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
