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
 * File for class SessionManagementEnumAccessRole
 * @package SessionManagement
 * @subpackage Enumerations
 * @author Panopto
 * @version 20150429-01
 * @date 2017-01-19
 */
/**
 * This class stands for SessionManagementEnumAccessRole originally named AccessRole
 * Meta informations extracted from the WSDL
 * - from schema : {@link http://demo.hosted.panopto.com/Panopto/PublicAPI/4.6/SessionManagement.svc?xsd=xsd2}
 * @package SessionManagement
 * @subpackage Enumerations
 * @author Panopto
 * @version 20150429-01
 * @date 2017-01-19
 */
class SessionManagementEnumAccessRole extends SessionManagementWsdlClass
{
    /**
     * Constant for value 'Creator'
     * @return string 'Creator'
     */
    const VALUE_CREATOR = 'Creator';
    /**
     * Constant for value 'Viewer'
     * @return string 'Viewer'
     */
    const VALUE_VIEWER = 'Viewer';
    /**
     * Constant for value 'ViewerWithLink'
     * @return string 'ViewerWithLink'
     */
    const VALUE_VIEWERWITHLINK = 'ViewerWithLink';
    /**
     * Constant for value 'Publisher'
     * @return string 'Publisher'
     */
    const VALUE_PUBLISHER = 'Publisher';
    /**
     * Return true if value is allowed
     * @uses SessionManagementEnumAccessRole::VALUE_CREATOR
     * @uses SessionManagementEnumAccessRole::VALUE_VIEWER
     * @uses SessionManagementEnumAccessRole::VALUE_VIEWERWITHLINK
     * @uses SessionManagementEnumAccessRole::VALUE_PUBLISHER
     * @param mixed $_value value
     * @return bool true|false
     */
    public static function valueIsValid($_value)
    {
        return in_array($_value,array(SessionManagementEnumAccessRole::VALUE_CREATOR,SessionManagementEnumAccessRole::VALUE_VIEWER,SessionManagementEnumAccessRole::VALUE_VIEWERWITHLINK,SessionManagementEnumAccessRole::VALUE_PUBLISHER));
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
