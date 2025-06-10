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
 * File for class SessionManagementStructSetNotesPublic
 * @package SessionManagement
 * @subpackage Structs
 * @author Panopto
 * @version 20150429-01
 * @date 2017-01-19
 */
/**
 * This class stands for SessionManagementStructSetNotesPublic originally named SetNotesPublic
 * Meta informations extracted from the WSDL
 * - from schema : {@link http://demo.hosted.panopto.com/Panopto/PublicAPI/4.6/SessionManagement.svc?xsd=xsd0}
 * @package SessionManagement
 * @subpackage Structs
 * @author Panopto
 * @version 20150429-01
 * @date 2017-01-19
 */
class SessionManagementStructSetNotesPublic extends SessionManagementWsdlClass
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
     * The areNotesPublic
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var boolean
     */
    public $areNotesPublic;
    /**
     * Constructor method for SetNotesPublic
     * @see parent::__construct()
     * @param SessionManagementStructAuthenticationInfo $_auth
     * @param string $_sessionId
     * @param boolean $_areNotesPublic
     * @return SessionManagementStructSetNotesPublic
     */
    public function __construct($_auth = NULL,$_sessionId = NULL,$_areNotesPublic = NULL)
    {
        parent::__construct(array('auth'=>$_auth,'sessionId'=>$_sessionId,'areNotesPublic'=>$_areNotesPublic),false);
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
     * Get areNotesPublic value
     * @return boolean|null
     */
    public function getAreNotesPublic()
    {
        return $this->areNotesPublic;
    }
    /**
     * Set areNotesPublic value
     * @param boolean $_areNotesPublic the areNotesPublic
     * @return boolean
     */
    public function setAreNotesPublic($_areNotesPublic)
    {
        return ($this->areNotesPublic = $_areNotesPublic);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see SessionManagementWsdlClass::__set_state()
     * @uses SessionManagementWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return SessionManagementStructSetNotesPublic
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
