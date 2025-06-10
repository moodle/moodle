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
 * File for class SessionManagementStructListSessionsRequest
 * @package SessionManagement
 * @subpackage Structs
 * @author Panopto
 * @version 20150429-01
 * @date 2017-01-19
 */
/**
 * This class stands for SessionManagementStructListSessionsRequest originally named ListSessionsRequest
 * Meta informations extracted from the WSDL
 * - from schema : {@link http://demo.hosted.panopto.com/Panopto/PublicAPI/4.6/SessionManagement.svc?xsd=xsd2}
 * @package SessionManagement
 * @subpackage Structs
 * @author Panopto
 * @version 20150429-01
 * @date 2017-01-19
 */
class SessionManagementStructListSessionsRequest extends SessionManagementWsdlClass
{
    /**
     * The EndDate
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var dateTime
     */
    public $EndDate;
    /**
     * The FolderId
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * - pattern : [\da-fA-F]{8}-[\da-fA-F]{4}-[\da-fA-F]{4}-[\da-fA-F]{4}-[\da-fA-F]{12}
     * @var string
     */
    public $FolderId;
    /**
     * The Pagination
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var SessionManagementStructPagination
     */
    public $Pagination;
    /**
     * The RemoteRecorderId
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * - pattern : [\da-fA-F]{8}-[\da-fA-F]{4}-[\da-fA-F]{4}-[\da-fA-F]{4}-[\da-fA-F]{12}
     * @var string
     */
    public $RemoteRecorderId;
    /**
     * The SortBy
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var SessionManagementEnumSessionSortField
     */
    public $SortBy;
    /**
     * The SortIncreasing
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var boolean
     */
    public $SortIncreasing;
    /**
     * The StartDate
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var dateTime
     */
    public $StartDate;
    /**
     * The States
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var SessionManagementStructArrayOfSessionState
     */
    public $States;
    /**
     * Constructor method for ListSessionsRequest
     * @see parent::__construct()
     * @param dateTime $_endDate
     * @param string $_folderId
     * @param SessionManagementStructPagination $_pagination
     * @param string $_remoteRecorderId
     * @param SessionManagementEnumSessionSortField $_sortBy
     * @param boolean $_sortIncreasing
     * @param dateTime $_startDate
     * @param SessionManagementStructArrayOfSessionState $_states
     * @return SessionManagementStructListSessionsRequest
     */
    public function __construct($_endDate = NULL,$_folderId = NULL,$_pagination = NULL,$_remoteRecorderId = NULL,$_sortBy = NULL,$_sortIncreasing = NULL,$_startDate = NULL,$_states = NULL)
    {
        parent::__construct(array('EndDate'=>$_endDate,'FolderId'=>$_folderId,'Pagination'=>$_pagination,'RemoteRecorderId'=>$_remoteRecorderId,'SortBy'=>$_sortBy,'SortIncreasing'=>$_sortIncreasing,'StartDate'=>$_startDate,'States'=>($_states instanceof SessionManagementStructArrayOfSessionState)?$_states:new SessionManagementStructArrayOfSessionState($_states)),false);
    }
    /**
     * Get EndDate value
     * @return dateTime|null
     */
    public function getEndDate()
    {
        return $this->EndDate;
    }
    /**
     * Set EndDate value
     * @param dateTime $_endDate the EndDate
     * @return dateTime
     */
    public function setEndDate($_endDate)
    {
        return ($this->EndDate = $_endDate);
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
     * Get Pagination value
     * @return SessionManagementStructPagination|null
     */
    public function getPagination()
    {
        return $this->Pagination;
    }
    /**
     * Set Pagination value
     * @param SessionManagementStructPagination $_pagination the Pagination
     * @return SessionManagementStructPagination
     */
    public function setPagination($_pagination)
    {
        return ($this->Pagination = $_pagination);
    }
    /**
     * Get RemoteRecorderId value
     * @return string|null
     */
    public function getRemoteRecorderId()
    {
        return $this->RemoteRecorderId;
    }
    /**
     * Set RemoteRecorderId value
     * @param string $_remoteRecorderId the RemoteRecorderId
     * @return string
     */
    public function setRemoteRecorderId($_remoteRecorderId)
    {
        return ($this->RemoteRecorderId = $_remoteRecorderId);
    }
    /**
     * Get SortBy value
     * @return SessionManagementEnumSessionSortField|null
     */
    public function getSortBy()
    {
        return $this->SortBy;
    }
    /**
     * Set SortBy value
     * @uses SessionManagementEnumSessionSortField::valueIsValid()
     * @param SessionManagementEnumSessionSortField $_sortBy the SortBy
     * @return SessionManagementEnumSessionSortField
     */
    public function setSortBy($_sortBy)
    {
        if(!SessionManagementEnumSessionSortField::valueIsValid($_sortBy))
        {
            return false;
        }
        return ($this->SortBy = $_sortBy);
    }
    /**
     * Get SortIncreasing value
     * @return boolean|null
     */
    public function getSortIncreasing()
    {
        return $this->SortIncreasing;
    }
    /**
     * Set SortIncreasing value
     * @param boolean $_sortIncreasing the SortIncreasing
     * @return boolean
     */
    public function setSortIncreasing($_sortIncreasing)
    {
        return ($this->SortIncreasing = $_sortIncreasing);
    }
    /**
     * Get StartDate value
     * @return dateTime|null
     */
    public function getStartDate()
    {
        return $this->StartDate;
    }
    /**
     * Set StartDate value
     * @param dateTime $_startDate the StartDate
     * @return dateTime
     */
    public function setStartDate($_startDate)
    {
        return ($this->StartDate = $_startDate);
    }
    /**
     * Get States value
     * @return SessionManagementStructArrayOfSessionState|null
     */
    public function getStates()
    {
        return $this->States;
    }
    /**
     * Set States value
     * @param SessionManagementStructArrayOfSessionState $_states the States
     * @return SessionManagementStructArrayOfSessionState
     */
    public function setStates($_states)
    {
        return ($this->States = $_states);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see SessionManagementWsdlClass::__set_state()
     * @uses SessionManagementWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return SessionManagementStructListSessionsRequest
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
