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
 * File for class UserManagementStructListGroupsResponse
 * @package UserManagement
 * @subpackage Structs
 * @author Panopto
 * @version 20150429-01
 * @date 2017-01-19
 */
/**
 * This class stands for UserManagementStructListGroupsResponse originally named ListGroupsResponse
 * Meta informations extracted from the WSDL
 * - from schema : {@link http://demo.hosted.panopto.com/Panopto/PublicAPI/4.6/UserManagement.svc?xsd=xsd0}
 * @package UserManagement
 * @subpackage Structs
 * @author Panopto
 * @version 20150429-01
 * @date 2017-01-19
 */
class UserManagementStructListGroupsResponse extends UserManagementWsdlClass
{
    /**
     * The PagedResults
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var UserManagementStructArrayOfGroup
     */
    public $PagedResults;
    /**
     * The TotalResultCount
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var int
     */
    public $TotalResultCount;
    /**
     * The ListGroupsResult
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * - from schema : {@link http://demo.hosted.panopto.com/Panopto/PublicAPI/4.6/UserManagement.svc?xsd=xsd0}
     * @var ListGroupsResponse
     */
    public $ListGroupsResult;
    /**
     * Constructor method for ListGroupsResponse
     * @see parent::__construct()
     * @param UserManagementStructArrayOfGroup $_pagedResults
     * @param int $_totalResultCount
     * @param ListGroupsResponse $_listGroupsResult
     * @return UserManagementStructListGroupsResponse
     */
    public function __construct($_pagedResults = NULL,$_totalResultCount = NULL,$_listGroupsResult = NULL)
    {
        parent::__construct(array('PagedResults'=>($_pagedResults instanceof UserManagementStructArrayOfGroup)?$_pagedResults:new UserManagementStructArrayOfGroup($_pagedResults),'TotalResultCount'=>$_totalResultCount,'ListGroupsResult'=>$_listGroupsResult),false);
    }
    /**
     * Get PagedResults value
     * @return UserManagementStructArrayOfGroup|null
     */
    public function getPagedResults()
    {
        return $this->PagedResults;
    }
    /**
     * Set PagedResults value
     * @param UserManagementStructArrayOfGroup $_pagedResults the PagedResults
     * @return UserManagementStructArrayOfGroup
     */
    public function setPagedResults($_pagedResults)
    {
        return ($this->PagedResults = $_pagedResults);
    }
    /**
     * Get TotalResultCount value
     * @return int|null
     */
    public function getTotalResultCount()
    {
        return $this->TotalResultCount;
    }
    /**
     * Set TotalResultCount value
     * @param int $_totalResultCount the TotalResultCount
     * @return int
     */
    public function setTotalResultCount($_totalResultCount)
    {
        return ($this->TotalResultCount = $_totalResultCount);
    }
    /**
     * Get ListGroupsResult value
     * @return ListGroupsResponse|null
     */
    public function getListGroupsResult()
    {
        return $this->ListGroupsResult;
    }
    /**
     * Set ListGroupsResult value
     * @param ListGroupsResponse $_listGroupsResult the ListGroupsResult
     * @return ListGroupsResponse
     */
    public function setListGroupsResult($_listGroupsResult)
    {
        return ($this->ListGroupsResult = $_listGroupsResult);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see UserManagementWsdlClass::__set_state()
     * @uses UserManagementWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return UserManagementStructListGroupsResponse
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
