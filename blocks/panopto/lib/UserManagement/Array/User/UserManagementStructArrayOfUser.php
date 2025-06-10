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
 * File for class UserManagementStructArrayOfUser
 * @package UserManagement
 * @subpackage Structs
 * @author Panopto
 * @version 20150429-01
 * @date 2017-01-19
 */
/**
 * This class stands for UserManagementStructArrayOfUser originally named ArrayOfUser
 * Meta informations extracted from the WSDL
 * - from schema : {@link http://demo.hosted.panopto.com/Panopto/PublicAPI/4.6/UserManagement.svc?xsd=xsd2}
 * @package UserManagement
 * @subpackage Structs
 * @author Panopto
 * @version 20150429-01
 * @date 2017-01-19
 */
class UserManagementStructArrayOfUser extends UserManagementWsdlClass
{
    /**
     * The User
     * Meta informations extracted from the WSDL
     * - maxOccurs : unbounded
     * - minOccurs : 0
     * - nillable : true
     * @var UserManagementStructUser
     */
    public $User;
    /**
     * Constructor method for ArrayOfUser
     * @see parent::__construct()
     * @param UserManagementStructUser $_user
     * @return UserManagementStructArrayOfUser
     */
    public function __construct($_user = NULL)
    {
        parent::__construct(array('User'=>$_user),false);
    }
    /**
     * Get User value
     * @return UserManagementStructUser|null
     */
    public function getUser()
    {
        return $this->User;
    }
    /**
     * Set User value
     * @param UserManagementStructUser $_user the User
     * @return UserManagementStructUser
     */
    public function setUser($_user)
    {
        return ($this->User = $_user);
    }
    /**
     * Returns the current element
     * @see UserManagementWsdlClass::current()
     * @return mixed
     */
    #[\ReturnTypeWillChange]
    public function current()
    {
        return parent::current();
    }
    /**
     * Returns the indexed element
     * @see UserManagementWsdlClass::item()
     * @param int $_index
     * @return UserManagementStructUser
     */
    public function item($_index)
    {
        return parent::item($_index);
    }
    /**
     * Returns the first element
     * @see UserManagementWsdlClass::first()
     * @return UserManagementStructUser
     */
    public function first()
    {
        return parent::first();
    }
    /**
     * Returns the last element
     * @see UserManagementWsdlClass::last()
     * @return UserManagementStructUser
     */
    public function last()
    {
        return parent::last();
    }
    /**
     * Returns the element at the offset
     * @see UserManagementWsdlClass::last()
     * @param int $_offset
     * @return mixed
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($_offset)
    {
        return parent::offsetGet($_offset);
    }
    /**
     * Returns the attribute name
     * @see UserManagementWsdlClass::getAttributeName()
     * @return string User
     */
    public function getAttributeName()
    {
        return 'User';
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see UserManagementWsdlClass::__set_state()
     * @uses UserManagementWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return UserManagementStructArrayOfUser
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
