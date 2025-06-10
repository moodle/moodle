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

defined('MOODLE_INTERNAL') || die();

require_once(dirname(__FILE__) . '/SessionManagement/SessionManagementAutoload.php');
require_once(dirname(__FILE__) . '/panopto_data.php');
require_once(dirname(__FILE__) . '/block_panopto_lib.php');
require_once(dirname(__FILE__) . '/panopto_timeout_soap_client.php');

/**
 * Panopto session soap client class.
 *
 * @package block_panopto
 * @copyright  Panopto 2009 - 2016
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class panopto_session_soap_client extends PanoptoTimeoutSoapClient {
    /**
     * @var array $authparam auth param needed for all soap calls.
     */
    private $authparam;

    /**
     * @var array $serviceparams the url used to get the service wsdl, as well as optional proxy options
     */
    private $serviceparams;

    /**
     * @var SessionManagementServiceAdd $sessionmanagementserviceadd soap service for add based calls
     */
    private $sessionmanagementserviceadd;

    /**
     * @var SessionManagementServiceProvision $sessionmanagementserviceprovision soap service for provision based calls
     */
    private $sessionmanagementserviceprovision;

    /**
     * @var SessionManagementServiceSet $sessionmanagementserviceset soap service for set calls
     */
    private $sessionmanagementserviceset;

    /**
     * @var SessionManagementServiceGet $sessionmanagementserviceget soap service for get calls
     */
    private $sessionmanagementserviceget;

    /**
     * @var SessionManagementServiceEnsure $sessionmanagementserviceensure soap service for ensure calls
     */
    private $sessionmanagementserviceensure;

    /**
     * @var SessionManagementServiceUnprovision $sessionmanagementserviceunprovision soap service for unprovision calls.
     */
    private $sessionmanagementserviceunprovision;

    /**
     * @var SessionManagementServiceUpdate $sessionmanagementserviceupdate soap service for update calls.
     */
    private $sessionmanagementserviceupdate;




    /**
     * @var string PERSONAL_FOLDER_ERROR const string to return when user attempted to provision/sync a personal folder.
     * This action is not supported.
     */
    const PERSONAL_FOLDER_ERROR = "TARGETED_PERSONAL_FOLDER";

    /**
     * Main constructor
     *
     * @param string $servername
     * @param string $apiuseruserkey
     * @param string $apiuserauthcode
     */
    public function __construct($servername, $apiuseruserkey, $apiuserauthcode) {

        // Cache web service credentials for all calls requiring authentication.
        $this->authparam = new SessionManagementStructAuthenticationInfo(
            $apiuserauthcode,
            null,
            $apiuseruserkey
        );

        $this->serviceparams = panopto_generate_wsdl_service_params(
            'https://'. $servername . '/Panopto/PublicAPI/4.6/SessionManagement.svc?singlewsdl'
        );
    }

    /**
     * Add folder
     * Note: Possibly unneeded since Moodle won't support multiple folders without behavior change
     *
     * @param string $foldername folder name to be created
     * @param array $parentguids parent ids
     * @param bool $ispublic is folder public or not
     */
    public function add_folder($foldername, $parentguids = null, $ispublic = false) {

        if (!isset($this->sessionmanagementserviceadd)) {
            $this->sessionmanagementserviceadd = new SessionManagementServiceAdd($this->serviceparams);
        }

        $folderparams = new SessionManagementStructAddFolder(
            $this->authparam,
            $foldername,
            $parentguids,
            $ispublic
        );

        if ($this->sessionmanagementserviceadd->AddFolder($folderparams)) {
            return $this->sessionmanagementserviceadd->getResult();
        } else {
            return $this->handle_error(
                $this->sessionmanagementserviceadd->getLastError()['SessionManagementServiceAdd::AddFolder']
            );
        }
    }

    /**
     * This function wraps the API call to unprovision a course from Panopto
     *
     * @param int $externalid string the externalId we are finding in Panopto to unmap
     */
    public function unprovision_external_course($externalid) {

        if (!isset($this->sessionmanagementserviceunprovision)) {
            $this->sessionmanagementserviceunprovision = new SessionManagementServiceUnprovision($this->serviceparams);
        }

        $unprovisionexternalcourseparams = new SessionManagementStructUnprovisionExternalCourse(
            $this->authparam,
            $externalid
        );

        if ($this->sessionmanagementserviceunprovision->UnprovisionExternalCourse($unprovisionexternalcourseparams)) {
            return  $this->sessionmanagementserviceunprovision->getResult()->UnprovisionExternalCourseResult;
        } else {
            return $this->handle_error(
                $this->sessionmanagementserviceunprovision
                    ->getLastError()['SessionManagementServiceUnprovision::UnprovisionExternalCourse']
            );
        }
    }

    /**
     * Provision external course with roles
     *
     * @param string $fullname full name of the course
     * @param int $externalcourseid id of the external course
     */
    public function provision_external_course_with_roles($fullname, $externalcourseid) {

        if (!isset($this->sessionmanagementserviceprovision)) {
            $this->sessionmanagementserviceprovision = new SessionManagementServiceProvision($this->serviceparams);
        }

        $rolestoensure = [
            "Viewer",
            "Creator",
            "Publisher",
        ];
        $rolelist = new SessionManagementStructArrayOfAccessRole($rolestoensure);

        $provisionparams = new SessionManagementStructProvisionExternalCourseWithRoles(
            $this->authparam,
            $fullname,
            $externalcourseid,
            $rolelist
        );

        if ($this->sessionmanagementserviceprovision->ProvisionExternalCourseWithRoles($provisionparams)) {
            $retobj = $this->sessionmanagementserviceprovision->getResult();
            return $retobj->ProvisionExternalCourseWithRolesResult;
        } else {
            return $this->handle_error(
                $this->sessionmanagementserviceprovision
                    ->getLastError()['SessionManagementServiceProvision::ProvisionExternalCourseWithRoles']
            );
        }
    }

    /**
     * Set external course access for roles
     *
     * @param string $fullname full name of the course
     * @param int $externalcourseid id of the external course
     * @param array $folderids ids of folders
     */
    public function set_external_course_access_for_roles($fullname, $externalcourseid, $folderids) {

        if (!isset($this->sessionmanagementserviceset)) {
            $this->sessionmanagementserviceset = new SessionManagementServiceSet($this->serviceparams);
        }

        if (!is_array($folderids)) {
            $folderids = [$folderids];
        }

        $folderidlist = new SessionManagementStructArrayOfguid($folderids);

        $rolestoensure = [
            "Viewer",
            "Creator",
            "Publisher",
        ];
        $rolelist = new SessionManagementStructArrayOfAccessRole($rolestoensure);

        $courseaccessparams = new SessionManagementStructSetExternalCourseAccessForRoles(
            $this->authparam,
            $fullname,
            $externalcourseid,
            $folderidlist,
            $rolelist
        );

        if ($this->sessionmanagementserviceset->SetExternalCourseAccessForRoles($courseaccessparams)) {
            $retobj = $this->sessionmanagementserviceset->getResult();
            // We do not support multiple folders per course in Moodle atm so we can assume 1 result.
            return $retobj->SetExternalCourseAccessForRolesResult->Folder[0];
        } else {
            return $this->handle_error(
                $this->sessionmanagementserviceset->getLastError()['SessionManagementServiceSet::SetExternalCourseAccessForRoles']
            );
        }
    }

    /**
     * Set copied external course access for roles
     *
     * @param string $fullname full name of the course
     * @param int $externalcourseid id of the external course
     * @param array $folderids ids of folders
     */
    public function set_copied_external_course_access_for_roles($fullname, $externalcourseid, $folderids) {

        if (!isset($this->sessionmanagementserviceset)) {
            $this->sessionmanagementserviceset = new SessionManagementServiceSet($this->serviceparams);
        }

        if (!is_array($folderids)) {
            $folderids = [$folderids];
        }

        $folderidlist = new SessionManagementStructArrayOfguid($folderids);

        $rolestoensure = [
            "Viewer",
            "Creator",
            "Publisher",
        ];
        $rolelist = new SessionManagementStructArrayOfAccessRole($rolestoensure);

        $copiedaccessparams = new SessionManagementStructSetCopiedExternalCourseAccessForRoles(
            $this->authparam,
            $fullname,
            $externalcourseid,
            $folderidlist,
            $rolelist
        );

        if ($this->sessionmanagementserviceset->SetCopiedExternalCourseAccessForRoles($copiedaccessparams)) {
            $retobj = $this->sessionmanagementserviceset->getResult();
            return $retobj->SetCopiedExternalCourseAccessForRolesResult->Folder[0];
        } else {
            return $this->handle_error(
                $this->sessionmanagementserviceset
                    ->getLastError()['SessionManagementServiceSet::SetCopiedExternalCourseAccessForRoles']
            );
        }
    }

    /**
     * Get folders by id
     *
     * @param array $folderids ids of folders
     */
    public function get_folders_by_id($folderids) {

        if (!isset($this->sessionmanagementserviceget)) {
            $this->sessionmanagementserviceget = new SessionManagementServiceGet($this->serviceparams);
        }

        if (!is_array($folderids)) {
            $folderids = [$folderids];
        }

        $folderidlist = new SessionManagementStructArrayOfguid($folderids);
        $getfolderparams = new SessionManagementStructGetFoldersById($this->authparam, $folderidlist);

        if ($this->sessionmanagementserviceget->GetFoldersById($getfolderparams)) {
            $retobj = $this->sessionmanagementserviceget->getResult();
            return $retobj->GetFoldersByIdResult->Folder[0];
        } else {
            return $this->handle_error(
                $this->sessionmanagementserviceget->getLastError()['SessionManagementServiceGet::GetFoldersById']
            );
        }
    }

    /**
     * Get folders by external id
     *
     * @param array $folderids ids of folders
     */
    public function get_folders_by_external_id($folderids) {

        if (!isset($this->sessionmanagementserviceget)) {
            $this->sessionmanagementserviceget = new SessionManagementServiceGet($this->serviceparams);
        }

        if (!is_array($folderids)) {
            $folderids = [$folderids];
        }

        $folderidlist = new SessionManagementStructArrayOfstring($folderids);

        $getfolderparams = new SessionManagementStructGetFoldersByExternalId(
            $this->authparam,
            $folderidlist
        );

        if ($this->sessionmanagementserviceget->GetFoldersByExternalId($getfolderparams)) {
            $retobj = $this->sessionmanagementserviceget->getResult();
            return $retobj->GetFoldersByExternalIdResult->Folder[0];
        } else {
            return $this->handle_error(
                $this->sessionmanagementserviceget->getLastError()['SessionManagementServiceGet::GetFoldersByExternalId']
            );
        }
    }

    /**
     * Attempts to get all folders the user has creator access to.
     */
    public function get_creator_folders_list() {

        if (!isset($this->sessionmanagementserviceget)) {
            $this->sessionmanagementserviceget = new SessionManagementServiceGet($this->serviceparams);
        }

        $resultsperpage = 1000;
        $currentpage = 0;
        $pagination = new SessionManagementStructPagination($resultsperpage, $currentpage);
        $parentfolderid = null;
        $publiconly = false;
        $sortby = SessionManagementEnumFolderSortField::VALUE_NAME;
        $sortincreasing = true;
        $wildcardsearchnameonly = false;
        $unmappedonly = true;

        $folderlistrequest = new SessionManagementStructListFoldersRequest(
            $pagination,
            $parentfolderid,
            $publiconly,
            $sortby,
            $sortincreasing,
            $wildcardsearchnameonly,
            $unmappedonly
        );
        $searchquery = null;

        $folderlistparams = new SessionManagementStructGetCreatorFoldersList(
            $this->authparam,
            $folderlistrequest,
            $searchquery
        );

        if ($this->sessionmanagementserviceget->GetCreatorFoldersList($folderlistparams)) {
            $retobj = $this->sessionmanagementserviceget->getResult();
            $totalresults = $retobj->GetCreatorFoldersListResult->TotalNumberResults;

            $folderlist = $retobj->GetCreatorFoldersListResult->Results->Folder;

            if ($totalresults > $resultsperpage) {

                $folderstoget = $totalresults - $resultsperpage;
                ++$currentpage;
                while ($folderstoget > 0) {
                    $pagination = new SessionManagementStructPagination($resultsperpage, $currentpage);

                    $folderlistrequest = new SessionManagementStructListFoldersRequest(
                        $pagination,
                        $parentfolderid,
                        $publiconly,
                        $sortby,
                        $sortincreasing,
                        $wildcardsearchnameonly
                    );

                    $folderlistparams = new SessionManagementStructGetCreatorFoldersList(
                        $this->authparam,
                        $folderlistrequest,
                        $searchquery
                    );

                    if ($this->sessionmanagementserviceget->GetCreatorFoldersList($folderlistparams)) {
                        $retobj = $this->sessionmanagementserviceget->getResult();
                        $folderlist = array_merge($folderlist, $retobj->GetCreatorFoldersListResult->Results->Folder);
                    } else {
                        return $this->handle_error(
                            $this->sessionmanagementserviceget->getLastError()['SessionManagementServiceGet::GetCreatorFoldersList']
                        );
                    }

                    ++$currentpage;
                    $folderstoget -= $resultsperpage;
                }
            } else if ($totalresults === 0) {
                // In this case folderlist will be null but that is handled poorly in the UI.
                $folderlist = [];
            }

            return $folderlist;
        } else {
            return $this->handle_error(
                $this->sessionmanagementserviceget->getLastError()['SessionManagementServiceGet::GetCreatorFoldersList']
            );
        }
    }

    /**
     * Attempts to get all extended folders the user has creator access to.
     */
    public function get_extended_creator_folders_list() {

        if (!isset($this->sessionmanagementserviceget)) {
            $this->sessionmanagementserviceget = new SessionManagementServiceGet($this->serviceparams);
        }

        $resultsperpage = 1000;
        $currentpage = 0;
        $pagination = new SessionManagementStructPagination($resultsperpage, $currentpage);
        $parentfolderid = null;
        $publiconly = false;
        $sortby = SessionManagementEnumFolderSortField::VALUE_NAME;
        $sortincreasing = true;
        $wildcardsearchnameonly = false;
        $unmappedonly = true;

        $folderlistrequest = new SessionManagementStructListFoldersRequest(
            $pagination,
            $parentfolderid,
            $publiconly,
            $sortby,
            $sortincreasing,
            $wildcardsearchnameonly,
            $unmappedonly
        );
        $searchquery = null;

        $folderlistparams = new SessionManagementStructGetExtendedCreatorFoldersList(
            $this->authparam,
            $folderlistrequest,
            $searchquery
        );

        if ($this->sessionmanagementserviceget->GetExtendedCreatorFoldersList($folderlistparams)) {
            $retobj = $this->sessionmanagementserviceget->getResult();
            $totalresults = $retobj->GetExtendedCreatorFoldersListResult->TotalNumberResults;

            $folderlist = $retobj->GetExtendedCreatorFoldersListResult->Results->ExtendedFolder;

            if ($totalresults > $resultsperpage) {

                $folderstoget = $totalresults - $resultsperpage;
                ++$currentpage;
                while ($folderstoget > 0) {
                    $pagination = new SessionManagementStructPagination($resultsperpage, $currentpage);

                    $folderlistrequest = new SessionManagementStructListFoldersRequest(
                        $pagination,
                        $parentfolderid,
                        $publiconly,
                        $sortby,
                        $sortincreasing,
                        $wildcardsearchnameonly
                    );

                    $folderlistparams = new SessionManagementStructGetExtendedCreatorFoldersList(
                        $this->authparam,
                        $folderlistrequest,
                        $searchquery
                    );

                    if ($this->sessionmanagementserviceget->GetExtendedCreatorFoldersList($folderlistparams)) {
                        $retobj = $this->sessionmanagementserviceget->getResult();
                        $folderlist =
                            array_merge($folderlist, $retobj->GetExtendedCreatorFoldersListResult->Results->ExtendedFolder);
                    } else {
                        return $this->handle_error(
                            $this->sessionmanagementserviceget->getLastError()['SessionManagementServiceGet::GetCreatorFoldersList']
                        );
                    }

                    ++$currentpage;
                    $folderstoget -= $resultsperpage;
                }
            } else if ($totalresults === 0) {
                // In this case folderlist will be null but that is handled poorly in the UI.
                $folderlist = [];
            }

            return $folderlist;
        } else {
            return $this->handle_error(
                $this->sessionmanagementserviceget->getLastError()['SessionManagementServiceGet::GetCreatorFoldersList']
            );
        }
    }

    /**
     * Attempts to get all folders the user has access to.
     */
    public function get_folders_list() {

        if (!isset($this->sessionmanagementserviceget)) {
            $this->sessionmanagementserviceget = new SessionManagementServiceGet($this->serviceparams);
        }

        $resultsperpage = 1000;
        $currentpage = 0;
        $pagination = new SessionManagementStructPagination($resultsperpage, $currentpage);
        $parentfolderid = null;
        $publiconly = false;
        $sortby = SessionManagementEnumFolderSortField::VALUE_NAME;
        $sortincreasing = true;
        $wildcardsearchnameonly = false;

        $folderlistrequest = new SessionManagementStructListFoldersRequest(
            $pagination,
            $parentfolderid,
            $publiconly,
            $sortby,
            $sortincreasing,
            $wildcardsearchnameonly
        );
        $searchquery = null;

        $folderlistparams = new SessionManagementStructGetFoldersList(
            $this->authparam,
            $folderlistrequest,
            $searchquery
        );

        if ($this->sessionmanagementserviceget->GetFoldersList($folderlistparams)) {
            $retobj = $this->sessionmanagementserviceget->getResult();
            $totalresults = $retobj->GetFoldersListResult->TotalNumberResults;

            $folderlist = $retobj->GetFoldersListResult->Results->Folder;

            if ($totalresults > $resultsperpage) {

                $folderstoget = $totalresults - $resultsperpage;
                ++$currentpage;
                while ($folderstoget > 0) {
                    $pagination = new SessionManagementStructPagination($resultsperpage, $currentpage);

                    $folderlistrequest = new SessionManagementStructListFoldersRequest(
                        $pagination,
                        $parentfolderid,
                        $publiconly,
                        $sortby,
                        $sortincreasing,
                        $wildcardsearchnameonly
                    );

                    $folderlistparams = new SessionManagementStructGetFoldersList(
                        $this->authparam,
                        $folderlistrequest,
                        $searchquery
                    );

                    if ($this->sessionmanagementserviceget->GetFoldersList($folderlistparams)) {
                        $retobj = $this->sessionmanagementserviceget->getResult();
                        $folderlist = array_merge($folderlist, $retobj->GetFoldersListResult->Results->Folder);
                    } else {
                        return $this->handle_error(
                            $this->sessionmanagementserviceget->getLastError()['SessionManagementServiceGet::GetFoldersList']
                        );
                    }

                    ++$currentpage;
                    $folderstoget -= $resultsperpage;
                }
            }

            return $folderlist;
        } else {
            return $this->handle_error(
                $this->sessionmanagementserviceget->getLastError()['SessionManagementServiceGet::GetFoldersList']
            );
        }
    }

    /**
     * Get session list
     *
     * @param int $folderid id of the folder
     * @param bool $sessionshavespecificorder order of the sessions
     */
    public function get_session_list($folderid, $sessionshavespecificorder) {

        if (!isset($this->sessionmanagementserviceget)) {
            $this->sessionmanagementserviceget = new SessionManagementServiceGet($this->serviceparams);
        }

        $startdate = null;
        $enddate = null;
        $pagination = new SessionManagementStructPagination(100, 0);
        $remoterecorderid = null;

        $sortby = $sessionshavespecificorder
            ? SessionManagementEnumSessionSortField::VALUE_ORDER
            : SessionManagementEnumSessionSortField::VALUE_DATE;
        $sortincreasing = $sessionshavespecificorder;
        $states = new SessionManagementStructArrayOfSessionState(
            [
                SessionManagementEnumSessionState::VALUE_BROADCASTING,
                SessionManagementEnumSessionState::VALUE_COMPLETE,
                SessionManagementEnumSessionState::VALUE_RECORDING,
            ]
        );

        $sessionrequest = new SessionManagementStructListSessionsRequest(
            $enddate,
            $folderid,
            $pagination,
            $remoterecorderid,
            $sortby,
            $sortincreasing,
            $startdate,
            $states
        );
        $searchquery = null;

        $getsessionlistparams = new SessionManagementStructGetSessionsList(
            $this->authparam,
            $sessionrequest,
            $searchquery
        );

        if ($this->sessionmanagementserviceget->GetSessionsList($getsessionlistparams)) {
            return $this->sessionmanagementserviceget->getResult()->GetSessionsListResult->Results->Session;
        } else {
            return $this->handle_error(
                $this->sessionmanagementserviceget->getLastError()['SessionManagementServiceGet::GetSessionsList']
            );
        }
    }

    /**
     * Ensure external hierarchy by branch
     *
     * @param object $categorybranchinfo category branch info
     */
    public function ensure_category_branch($categorybranchinfo) {

        if (!isset($this->sessionmanagementserviceensure)) {
            $this->sessionmanagementserviceensure = new SessionManagementServiceEnsure($this->serviceparams);
        }

        $brancharrayofcategoryinfos = new SessionManagementStructArrayOfExternalHierarchyInfo($categorybranchinfo);
        $ensurecategorybranchparams = new SessionManagementStructEnsureExternalHierarchyBranch(
            $this->authparam,
            $brancharrayofcategoryinfos
        );

        if ($this->sessionmanagementserviceensure->EnsureExternalHierarchyBranch($ensurecategorybranchparams)) {
            return $this->sessionmanagementserviceensure->getResult()->EnsureExternalHierarchyBranchResult;
        } else {
            return $this->handle_error(
                $this->sessionmanagementserviceensure
                    ->getLastError()['SessionManagementServiceEnsure::EnsureExternalHierarchyBranch']
            );
        }
    }

    /**
     * Update folder parent
     *
     * @param int $folderid id of the folder
     * @param int $newparentid id of the new folder parent
     */
    public function update_folder_parent($folderid, $newparentid) {

        if (!isset($this->sessionmanagementserviceupdate)) {
            $this->sessionmanagementserviceupdate = new SessionManagementServiceUpdate($this->serviceparams);
        }

        $updatefolderparentparams = new SessionManagementStructUpdateFolderParent(
            $this->authparam,
            $folderid,
            $newparentid
        );

        if ($this->sessionmanagementserviceupdate->UpdateFolderParent($updatefolderparentparams)) {
            return true;
        } else {
            return $this->handle_error(
                $this->sessionmanagementserviceupdate->getLastError()['SessionManagementServiceUpdate::UpdateFolderParent']
            );
        }
    }

    /**
     * Update folder external id with provider
     *
     * @param int $folderid id of the folder
     * @param int $externalid external folder id
     * @param int $providername provider name
     */
    public function update_folder_external_id_with_provider($folderid, $externalid, $providername) {

        if (!isset($this->sessionmanagementserviceupdate)) {
            $this->sessionmanagementserviceupdate = new SessionManagementServiceUpdate($this->serviceparams);
        }

        $updatefolderparams = new SessionManagementStructUpdateFolderExternalIdWithProvider(
            $this->authparam,
            $folderid,
            $externalid,
            $providername
        );

        if ($this->sessionmanagementserviceupdate->UpdateFolderExternalIdWithProvider($updatefolderparams)) {
            return true;
        } else {
            return $this->handle_error(
                $this->sessionmanagementserviceupdate
                    ->getLastError()['SessionManagementServiceUpdate::UpdateFolderExternalIdWithProvider']
            );
        }
    }

    /**
     * Update folder name
     *
     * @param int $folderid id of the folder
     * @param int $newname new name of the folder
     */
    public function update_folder_name($folderid, $newname) {

        if (!isset($this->sessionmanagementserviceupdate)) {
            $this->sessionmanagementserviceupdate = new SessionManagementServiceUpdate($this->serviceparams);
        }

        $updatefoldernameparams = new SessionManagementStructUpdateFolderName(
            $this->authparam,
            $folderid,
            $newname
        );

        if ($this->sessionmanagementserviceupdate->UpdateFolderName($updatefoldernameparams)) {
            return true;
        } else {
            $this->handle_error(
                $this->sessionmanagementserviceupdate->getLastError()['SessionManagementServiceUpdate::UpdateFolderName']
            );
            return false;
        }
    }

    /**
     * Get recorder download urls
     *
     * @return array
     */
    public function get_recorder_download_urls() {
        if (!isset($this->sessionmanagementserviceget)) {
            $this->sessionmanagementserviceget = new SessionManagementServiceGet($this->serviceparams);
        }

        if ($this->sessionmanagementserviceget->GetRecorderDownloadUrls()) {
            return $this->sessionmanagementserviceget->getResult()->GetRecorderDownloadUrlsResult;
        } else {
            return $this->handle_error(
                $this->sessionmanagementserviceget->getLastError()['SessionManagementServiceGet::GetRecorderDownloadUrls']
            );
        }
    }

    /**
     * Handle error
     *
     * @param object $lasterror last error message
     */
    private function handle_error($lasterror) {
        $ret = new stdClass;
        $ret->errormessage = $lasterror->getMessage();

        if (!empty($ret->errormessage)) {
            if (strpos($ret->errormessage, 'not found') !== false) {
                $ret->notfound = true;
            }

            if (strpos($ret->errormessage, 'not have access') !== false) {
                $ret->noaccess = true;
            }

            \panopto_data::print_log($ret->errormessage);
        } else {
            \panopto_data::print_log(var_export($lasterror, true));
        }

        return $ret;
    }
}

/* End of file panopto_user_soap_client.php */
