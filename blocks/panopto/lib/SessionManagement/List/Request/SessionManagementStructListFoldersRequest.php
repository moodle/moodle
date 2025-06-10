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
 * File for class SessionManagementStructListFoldersRequest
 * @package SessionManagement
 * @subpackage Structs
 * @author Panopto
 * @version 20150429-01
 * @date 2017-01-19
 */
/**
 * This class stands for SessionManagementStructListFoldersRequest originally named ListFoldersRequest
 * Meta informations extracted from the WSDL
 * - from schema : {@link http://demo.hosted.panopto.com/Panopto/PublicAPI/4.6/SessionManagement.svc?xsd=xsd2}
 * @package SessionManagement
 * @subpackage Structs
 * @author Panopto
 * @version 20150429-01
 * @date 2017-01-19
 */
class SessionManagementStructListFoldersRequest extends SessionManagementWsdlClass
{
    /**
     * The Pagination
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var SessionManagementStructPagination
     */
    public $Pagination;
    /**
     * The ParentFolderId
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * - pattern : [\da-fA-F]{8}-[\da-fA-F]{4}-[\da-fA-F]{4}-[\da-fA-F]{4}-[\da-fA-F]{12}
     * @var string
     */
    public $ParentFolderId;
    /**
     * The PublicOnly
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var boolean
     */
    public $PublicOnly;
    /**
     * The SortBy
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var SessionManagementEnumFolderSortField
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
     * The WildcardSearchNameOnly
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var boolean
     */
    public $WildcardSearchNameOnly;
    /**
     * The UnmappedOnly
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var boolean
     */
    public $UnmappedOnly;
    /**
     * Constructor method for ListFoldersRequest
     * @see parent::__construct()
     * @param SessionManagementStructPagination $_pagination
     * @param string $_parentFolderId
     * @param boolean $_publicOnly
     * @param SessionManagementEnumFolderSortField $_sortBy
     * @param boolean $_sortIncreasing
     * @param boolean $_wildcardSearchNameOnly
     * @param boolean $_unmappedOnly
     * @return SessionManagementStructListFoldersRequest
     */
    public function __construct($_pagination = NULL,$_parentFolderId = NULL,$_publicOnly = NULL,$_sortBy = NULL,$_sortIncreasing = NULL,$_wildcardSearchNameOnly = NULL, $__unmappedOnly = NULL)
    {
        parent::__construct(array('Pagination'=>$_pagination,'ParentFolderId'=>$_parentFolderId,'PublicOnly'=>$_publicOnly,'SortBy'=>$_sortBy,'SortIncreasing'=>$_sortIncreasing,'WildcardSearchNameOnly'=>$_wildcardSearchNameOnly,'UnmappedOnly'=>$__unmappedOnly),false);
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
     * Get ParentFolderId value
     * @return string|null
     */
    public function getParentFolderId()
    {
        return $this->ParentFolderId;
    }
    /**
     * Set ParentFolderId value
     * @param string $_parentFolderId the ParentFolderId
     * @return string
     */
    public function setParentFolderId($_parentFolderId)
    {
        return ($this->ParentFolderId = $_parentFolderId);
    }
    /**
     * Get PublicOnly value
     * @return boolean|null
     */
    public function getPublicOnly()
    {
        return $this->PublicOnly;
    }
    /**
     * Set PublicOnly value
     * @param boolean $_publicOnly the PublicOnly
     * @return boolean
     */
    public function setPublicOnly($_publicOnly)
    {
        return ($this->PublicOnly = $_publicOnly);
    }
    /**
     * Get SortBy value
     * @return SessionManagementEnumFolderSortField|null
     */
    public function getSortBy()
    {
        return $this->SortBy;
    }
    /**
     * Set SortBy value
     * @uses SessionManagementEnumFolderSortField::valueIsValid()
     * @param SessionManagementEnumFolderSortField $_sortBy the SortBy
     * @return SessionManagementEnumFolderSortField
     */
    public function setSortBy($_sortBy)
    {
        if(!SessionManagementEnumFolderSortField::valueIsValid($_sortBy))
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
     * Get WildcardSearchNameOnly value
     * @return boolean|null
     */
    public function getWildcardSearchNameOnly()
    {
        return $this->WildcardSearchNameOnly;
    }
    /**
     * Set WildcardSearchNameOnly value
     * @param boolean $_wildcardSearchNameOnly the WildcardSearchNameOnly
     * @return boolean
     */
    public function setWildcardSearchNameOnly($_wildcardSearchNameOnly)
    {
        return ($this->WildcardSearchNameOnly = $_wildcardSearchNameOnly);
    }
    /**
     * Get UnmappedOnly value
     * @return boolean|null
     */
    public function getUnmappedOnly()
    {
        return $this->UnmappedOnly;
    }
    /**
     * Set UnmappedOnly value
     * @param boolean $_unmappedOnly the UnmappedOnly
     * @return boolean
     */
    public function setUnmappedOnly($_unmappedOnly)
    {
        return ($this->UnmappedOnly = $_unmappedOnly);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see SessionManagementWsdlClass::__set_state()
     * @uses SessionManagementWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return SessionManagementStructListFoldersRequest
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
