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
 * The user soap client for Panopto
 *
 * @package block_panopto
 * @copyright Panopto 2009 - 2016
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * The user soap client for Panopto
 *
 * @copyright Panopto 2009 - 2016
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(dirname(__FILE__) . '/UserManagement/UserManagementAutoload.php');
require_once(dirname(__FILE__) . '/panopto_data.php');
require_once(dirname(__FILE__) . '/block_panopto_lib.php');
require_once(dirname(__FILE__) . '/panopto_timeout_soap_client.php');

/**
 * Panopto user SOAP client
 *
 * @package block_panopto
 * @copyright  Panopto 2009 - 2015
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class panopto_user_soap_client extends PanoptoTimeoutSoapClient {
    /**
     * @var array $authparam
     */
    public $authparam;

    /**
     * @var array $serviceparams the url used to get the service wsdl, as well as optional proxy options
     */
    private $serviceparams;

    /**
     * @var UserManagementServiceSync object used to call the user sync service
     */
    private $usermanagementservicesync;

    /**
     * @var UserManagementServiceGet object used to call the user get service
     */
    private $usermanagementserviceget;

    /**
     * @var UserManagementServiceCreate object used to call the user create service
     */
    private $usermanagementservicecreate;

    /**
     * @var UserManagementServiceUpdate object used to call the user update service
     */
    private $usermanagementserviceupdate;

    /**
     * @var UserManagementServiceDelete object used to call the user delete service
     */
    private $usermanagementservicedelete;

    /**
     * Main constructor
     *
     * @param string $servername
     * @param string $apiuseruserkey
     * @param string $apiuserauthcode
     */
    public function __construct($servername, $apiuseruserkey, $apiuserauthcode) {

        // Cache web service credentials for all calls requiring authentication.
        $this->authparam = new UserManagementStructAuthenticationInfo(
            $apiuserauthcode,
            null,
            $apiuseruserkey);

        $this->serviceparams = panopto_generate_wsdl_service_params(
            'https://'. $servername . '/Panopto/PublicAPI/4.6/UserManagement.svc?singlewsdl'
        );

        // We need to make sure the UpdateContactInfo call succeeded so we need to ensure SOAP_WAIT_ONE_WAY_CALLS is set.
        $this->serviceparams['wsdl_features'] = SOAP_WAIT_ONE_WAY_CALLS | SOAP_SINGLE_ELEMENT_ARRAYS | SOAP_USE_XSI_ARRAY_TYPE;

    }

    /**
     * Syncs a user with all of the listed groups, the user will be removed from any unlisted groups
     *
     * @param string $firstname user first name
     * @param string $lastname user last name
     * @param string $email user email address
     * @param array $externalgroupids array of group ids the user needs to be in
     * @param string $username panopto username
     */
    public function sync_external_user($firstname, $lastname, $email, $externalgroupids, $username = "") {

        // Get user from panopto, and send notifications status.
        $instancename = \get_config('block_panopto', 'instance_name');
        $panoptouser = $this->get_user_by_key($instancename . '\\' . $username);
        $sendemailnotifications = $panoptouser->EmailSessionNotifications ?? false;

        if (!isset($this->usermanagementservicesync)) {
            $this->usermanagementservicesync = new UserManagementServiceSync($this->serviceparams);
        }

        $syncparamsobject = new UserManagementStructSyncExternalUser(
            $this->authparam,
            $firstname,
            $lastname,
            $email,
            $sendemailnotifications,
            $externalgroupids
        );

        // Returns false if the call failed.
        if (!$this->usermanagementservicesync->SyncExternalUser($syncparamsobject)) {
            \panopto_data::print_log(var_export($this->usermanagementservicesync->getLastError(), true));
        }
    }

    /**
     * Searches for an existing panopto user by its username/userkey and returns it if found, returns null if not found
     *
     * @param string $userkey the username/key being searched.
     */
    public function get_user_by_key($userkey) {
        $result = false;

        if (!isset($this->usermanagementserviceget)) {
            $this->usermanagementserviceget = new UserManagementServiceGet($this->serviceparams);
        }

        $getuserbykeyparams = new UserManagementStructGetUserByKey(
            $this->authparam,
            $userkey
        );

        // Throws a soapfault if the call failed.
        if ($this->usermanagementserviceget->GetUserByKey($getuserbykeyparams)) {
            $result = $this->usermanagementserviceget->getResult()->GetUserByKeyResult;
        } else {
            // Try again in case if username is unified i.e unified\user, but user from moodle DB is still server\user.
            $username = preg_replace('/^[^\\\\]*\\\\/', 'unified\\', $userkey);
            $getuserbykeyparams = new UserManagementStructGetUserByKey(
                $this->authparam,
                $username
            );
            if ($this->usermanagementserviceget->GetUserByKey($getuserbykeyparams)) {
                $result = $this->usermanagementserviceget->getResult()->GetUserByKeyResult;
            } else {
                $lasterror = $this->usermanagementserviceget->getLastError()['UserManagementServiceGet::GetUserByKey'];
                \panopto_data::print_log(var_export($lasterror, true));
                throw $lasterror;
            }
        }
        return $result;
    }

    /**
     * Creates a new Panopto user
     *
     * @param string $email user email address
     * @param boolean $emailsessionnotifications  tells Panopto whether to send emails on session notifications
     * @param string $firstname user first name
     * @param string $groupmemberships any group memberships to give the new user
     * @param string $lastname user last name
     * @param string $systemrole any system role to give the new user
     * @param string $userbio a new user information
     * @param string $userid the target id of the new user
     * @param string $userkey the target username/key of the new user
     * @param string $usersettingsurl
     * @param string $password the password for the new user
     */
    public function create_user($email, $emailsessionnotifications, $firstname, $groupmemberships,
                                $lastname, $systemrole, $userbio, $userid, $userkey, $usersettingsurl, $password) {
        $result = false;

        if (!isset($this->usermanagementservicecreate)) {
            $this->usermanagementservicecreate = new UserManagementServiceCreate($this->serviceparams);
        }

        $decoratedgroupmemberships = new UserManagementStructArrayOfguid($groupmemberships);
        $userparamobject = new UserManagementStructUser(
            $email,
            $emailsessionnotifications,
            $firstname,
            $decoratedgroupmemberships,
            $lastname,
            $systemrole,
            $userbio,
            $userid,
            $userkey,
            $usersettingsurl
        );

        $createuserparams = new UserManagementStructCreateUser(
            $this->authparam,
            $userparamobject,
            $password
        );

        // Returns false if the call failed.
        if ($this->usermanagementservicecreate->CreateUser($createuserparams)) {
            $result = $this->usermanagementservicecreate->getResult();
        } else {
            \panopto_data::print_log(var_export($this->usermanagementservicecreate->getLastError(), true));
        }

        return $result;
    }

    /**
     * Updates an existing users email
     *
     * @param string $userid The Guid id of the user on Panopto
     * @param string $firstname user first name
     * @param string $lastname user last name
     * @param string $email the email of the user
     * @param boolean $sendemailnotifications to say whether we should sent emails to the user on notificationsr
     */
    public function update_contact_info($userid, $firstname, $lastname, $email, $sendemailnotifications) {
        $result = false;

        if (!isset($this->usermanagementserviceupdate)) {
            $this->usermanagementserviceupdate = new UserManagementServiceUpdate($this->serviceparams);
        }

        $updateuserparams = new UserManagementStructUpdateContactInfo(
            $this->authparam,
            $userid,
            $firstname,
            $lastname,
            $email,
            $sendemailnotifications
        );

        // Throw the soap fault if the call failed.
        if ($this->usermanagementserviceupdate->UpdateContactInfo($updateuserparams)) {
            $result = $this->usermanagementserviceupdate->getResult();
        } else {
            $lasterror = $this->usermanagementserviceupdate->getLastError()['UserManagementServiceUpdate::UpdateContactInfo'];
            \panopto_data::print_log(var_export($lasterror, true));
            throw $lasterror;
        }

        return $result;
    }

    /**
     * Attempts to delete a list of users by their Guid Id
     *
     * @param string $userids an array of Guid ids to users we are trying to delete.
     */
    public function delete_users($userids) {
        $result = false;

        if (!isset($this->usermanagementservicedelete)) {
            $this->usermanagementservicedelete = new UserManagementServiceDelete($this->serviceparams);
        }

        $arrayofuserids = new UserManagementStructArrayOfguid($userids);
        $deleteusersparams = new UserManagementStructDeleteUsers(
            $this->authparam,
            $arrayofuserids
        );

        if ($this->usermanagementservicedelete->DeleteUsers($deleteusersparams)) {
            $result = $this->usermanagementservicedelete->getResult();
        } else {
            \panopto_data::print_log(var_export($this->usermanagementservicedelete->getLastError(), true));
        }

        return $result;
    }
}

/* End of file panopto_user_soap_client.php */
