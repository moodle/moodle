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
 * File for class SessionManagementStructArrayOfSessionAvailabilitySettings
 * @package SessionManagement
 * @subpackage Structs
 * @author Panopto
 * @version 20150429-01
 * @date 2017-01-19
 */
/**
 * This class stands for SessionManagementStructArrayOfSessionAvailabilitySettings originally named ArrayOfSessionAvailabilitySettings
 * Meta informations extracted from the WSDL
 * - from schema : {@link http://demo.hosted.panopto.com/Panopto/PublicAPI/4.6/SessionManagement.svc?xsd=xsd3}
 * @package SessionManagement
 * @subpackage Structs
 * @author Panopto
 * @version 20150429-01
 * @date 2017-01-19
 */
class SessionManagementStructArrayOfSessionAvailabilitySettings extends SessionManagementWsdlClass
{
    /**
     * The SessionAvailabilitySettings
     * Meta informations extracted from the WSDL
     * - maxOccurs : unbounded
     * - minOccurs : 0
     * - nillable : true
     * @var SessionManagementStructSessionAvailabilitySettings
     */
    public $SessionAvailabilitySettings;
    /**
     * Constructor method for ArrayOfSessionAvailabilitySettings
     * @see parent::__construct()
     * @param SessionManagementStructSessionAvailabilitySettings $_sessionAvailabilitySettings
     * @return SessionManagementStructArrayOfSessionAvailabilitySettings
     */
    public function __construct($_sessionAvailabilitySettings = NULL)
    {
        parent::__construct(array('SessionAvailabilitySettings'=>$_sessionAvailabilitySettings),false);
    }
    /**
     * Get SessionAvailabilitySettings value
     * @return SessionManagementStructSessionAvailabilitySettings|null
     */
    public function getSessionAvailabilitySettings()
    {
        return $this->SessionAvailabilitySettings;
    }
    /**
     * Set SessionAvailabilitySettings value
     * @param SessionManagementStructSessionAvailabilitySettings $_sessionAvailabilitySettings the SessionAvailabilitySettings
     * @return SessionManagementStructSessionAvailabilitySettings
     */
    public function setSessionAvailabilitySettings($_sessionAvailabilitySettings)
    {
        return ($this->SessionAvailabilitySettings = $_sessionAvailabilitySettings);
    }
    /**
     * Returns the current element
     * @see SessionManagementWsdlClass::current()
     * @return mixed
     */
    #[\ReturnTypeWillChange]
    public function current()
    {
        return parent::current();
    }
    /**
     * Returns the indexed element
     * @see SessionManagementWsdlClass::item()
     * @param int $_index
     * @return SessionManagementStructSessionAvailabilitySettings
     */
    public function item($_index)
    {
        return parent::item($_index);
    }
    /**
     * Returns the first element
     * @see SessionManagementWsdlClass::first()
     * @return SessionManagementStructSessionAvailabilitySettings
     */
    public function first()
    {
        return parent::first();
    }
    /**
     * Returns the last element
     * @see SessionManagementWsdlClass::last()
     * @return SessionManagementStructSessionAvailabilitySettings
     */
    public function last()
    {
        return parent::last();
    }
    /**
     * Returns the element at the offset
     * @see SessionManagementWsdlClass::last()
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
     * @see SessionManagementWsdlClass::getAttributeName()
     * @return string SessionAvailabilitySettings
     */
    public function getAttributeName()
    {
        return 'SessionAvailabilitySettings';
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see SessionManagementWsdlClass::__set_state()
     * @uses SessionManagementWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return SessionManagementStructArrayOfSessionAvailabilitySettings
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
