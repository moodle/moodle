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
 * File for class SessionManagementStructNote
 * @package SessionManagement
 * @subpackage Structs
 * @author Panopto
 * @version 20150429-01
 * @date 2017-01-19
 */
/**
 * This class stands for SessionManagementStructNote originally named Note
 * Meta informations extracted from the WSDL
 * - from schema : {@link http://demo.hosted.panopto.com/Panopto/PublicAPI/4.6/SessionManagement.svc?xsd=xsd2}
 * @package SessionManagement
 * @subpackage Structs
 * @author Panopto
 * @version 20150429-01
 * @date 2017-01-19
 */
class SessionManagementStructNote extends SessionManagementWsdlClass
{
    /**
     * The Channel
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $Channel;
    /**
     * The CreatorId
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - pattern : [\da-fA-F]{8}-[\da-fA-F]{4}-[\da-fA-F]{4}-[\da-fA-F]{4}-[\da-fA-F]{12}
     * @var string
     */
    public $CreatorId;
    /**
     * The ID
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - pattern : [\da-fA-F]{8}-[\da-fA-F]{4}-[\da-fA-F]{4}-[\da-fA-F]{4}-[\da-fA-F]{12}
     * @var string
     */
    public $ID;
    /**
     * The SessionID
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - pattern : [\da-fA-F]{8}-[\da-fA-F]{4}-[\da-fA-F]{4}-[\da-fA-F]{4}-[\da-fA-F]{12}
     * @var string
     */
    public $SessionID;
    /**
     * The Text
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $Text;
    /**
     * The Timestamp
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var double
     */
    public $Timestamp;
    /**
     * Constructor method for Note
     * @see parent::__construct()
     * @param string $_channel
     * @param string $_creatorId
     * @param string $_iD
     * @param string $_sessionID
     * @param string $_text
     * @param double $_timestamp
     * @return SessionManagementStructNote
     */
    public function __construct($_channel = NULL,$_creatorId = NULL,$_iD = NULL,$_sessionID = NULL,$_text = NULL,$_timestamp = NULL)
    {
        parent::__construct(array('Channel'=>$_channel,'CreatorId'=>$_creatorId,'ID'=>$_iD,'SessionID'=>$_sessionID,'Text'=>$_text,'Timestamp'=>$_timestamp),false);
    }
    /**
     * Get Channel value
     * @return string|null
     */
    public function getChannel()
    {
        return $this->Channel;
    }
    /**
     * Set Channel value
     * @param string $_channel the Channel
     * @return string
     */
    public function setChannel($_channel)
    {
        return ($this->Channel = $_channel);
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
     * Get ID value
     * @return string|null
     */
    public function getID()
    {
        return $this->ID;
    }
    /**
     * Set ID value
     * @param string $_iD the ID
     * @return string
     */
    public function setID($_iD)
    {
        return ($this->ID = $_iD);
    }
    /**
     * Get SessionID value
     * @return string|null
     */
    public function getSessionID()
    {
        return $this->SessionID;
    }
    /**
     * Set SessionID value
     * @param string $_sessionID the SessionID
     * @return string
     */
    public function setSessionID($_sessionID)
    {
        return ($this->SessionID = $_sessionID);
    }
    /**
     * Get Text value
     * @return string|null
     */
    public function getText()
    {
        return $this->Text;
    }
    /**
     * Set Text value
     * @param string $_text the Text
     * @return string
     */
    public function setText($_text)
    {
        return ($this->Text = $_text);
    }
    /**
     * Get Timestamp value
     * @return double|null
     */
    public function getTimestamp()
    {
        return $this->Timestamp;
    }
    /**
     * Set Timestamp value
     * @param double $_timestamp the Timestamp
     * @return double
     */
    public function setTimestamp($_timestamp)
    {
        return ($this->Timestamp = $_timestamp);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see SessionManagementWsdlClass::__set_state()
     * @uses SessionManagementWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return SessionManagementStructNote
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
