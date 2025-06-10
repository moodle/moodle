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
 * File for class SessionManagementStructListNotes
 * @package SessionManagement
 * @subpackage Structs
 * @author Panopto
 * @version 20150429-01
 * @date 2017-01-19
 */
/**
 * This class stands for SessionManagementStructListNotes originally named ListNotes
 * Meta informations extracted from the WSDL
 * - from schema : {@link http://demo.hosted.panopto.com/Panopto/PublicAPI/4.6/SessionManagement.svc?xsd=xsd0}
 * @package SessionManagement
 * @subpackage Structs
 * @author Panopto
 * @version 20150429-01
 * @date 2017-01-19
 */
class SessionManagementStructListNotes extends SessionManagementWsdlClass
{
    /**
     * The auth
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var SessionManagementStructAuthenticationInfo
     */
    public $auth;
    /**
     * The sessionId
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - pattern : [\da-fA-F]{8}-[\da-fA-F]{4}-[\da-fA-F]{4}-[\da-fA-F]{4}-[\da-fA-F]{12}
     * @var string
     */
    public $sessionId;
    /**
     * The pagination
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var SessionManagementStructPagination
     */
    public $pagination;
    /**
     * The creatorId
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * - pattern : [\da-fA-F]{8}-[\da-fA-F]{4}-[\da-fA-F]{4}-[\da-fA-F]{4}-[\da-fA-F]{12}
     * @var string
     */
    public $creatorId;
    /**
     * The channel
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $channel;
    /**
     * The searchQuery
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $searchQuery;
    /**
     * Constructor method for ListNotes
     * @see parent::__construct()
     * @param SessionManagementStructAuthenticationInfo $_auth
     * @param string $_sessionId
     * @param SessionManagementStructPagination $_pagination
     * @param string $_creatorId
     * @param string $_channel
     * @param string $_searchQuery
     * @return SessionManagementStructListNotes
     */
    public function __construct($_auth = NULL,$_sessionId = NULL,$_pagination = NULL,$_creatorId = NULL,$_channel = NULL,$_searchQuery = NULL)
    {
        parent::__construct(array('auth'=>$_auth,'sessionId'=>$_sessionId,'pagination'=>$_pagination,'creatorId'=>$_creatorId,'channel'=>$_channel,'searchQuery'=>$_searchQuery),false);
    }
    /**
     * Get auth value
     * @return SessionManagementStructAuthenticationInfo|null
     */
    public function getAuth()
    {
        return $this->auth;
    }
    /**
     * Set auth value
     * @param SessionManagementStructAuthenticationInfo $_auth the auth
     * @return SessionManagementStructAuthenticationInfo
     */
    public function setAuth($_auth)
    {
        return ($this->auth = $_auth);
    }
    /**
     * Get sessionId value
     * @return string|null
     */
    public function getSessionId()
    {
        return $this->sessionId;
    }
    /**
     * Set sessionId value
     * @param string $_sessionId the sessionId
     * @return string
     */
    public function setSessionId($_sessionId)
    {
        return ($this->sessionId = $_sessionId);
    }
    /**
     * Get pagination value
     * @return SessionManagementStructPagination|null
     */
    public function getPagination()
    {
        return $this->pagination;
    }
    /**
     * Set pagination value
     * @param SessionManagementStructPagination $_pagination the pagination
     * @return SessionManagementStructPagination
     */
    public function setPagination($_pagination)
    {
        return ($this->pagination = $_pagination);
    }
    /**
     * Get creatorId value
     * @return string|null
     */
    public function getCreatorId()
    {
        return $this->creatorId;
    }
    /**
     * Set creatorId value
     * @param string $_creatorId the creatorId
     * @return string
     */
    public function setCreatorId($_creatorId)
    {
        return ($this->creatorId = $_creatorId);
    }
    /**
     * Get channel value
     * @return string|null
     */
    public function getChannel()
    {
        return $this->channel;
    }
    /**
     * Set channel value
     * @param string $_channel the channel
     * @return string
     */
    public function setChannel($_channel)
    {
        return ($this->channel = $_channel);
    }
    /**
     * Get searchQuery value
     * @return string|null
     */
    public function getSearchQuery()
    {
        return $this->searchQuery;
    }
    /**
     * Set searchQuery value
     * @param string $_searchQuery the searchQuery
     * @return string
     */
    public function setSearchQuery($_searchQuery)
    {
        return ($this->searchQuery = $_searchQuery);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see SessionManagementWsdlClass::__set_state()
     * @uses SessionManagementWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return SessionManagementStructListNotes
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
