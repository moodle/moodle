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
 * Test with SessionManagement for 'https://demo.hosted.panopto.com/Panopto/PublicAPI/4.6/SessionManagement.svc?singlewsdl'
 * @package SessionManagement
 * @author Panopto
 * @version 20150429-01
 * @date 2017-01-19
 */
ini_set('memory_limit','512M');
ini_set('display_errors',true);
error_reporting(-1);
/**
 * Load autoload
 */
require_once dirname(__FILE__) . '/SessionManagementAutoload.php';
/**
 * Wsdl instanciation infos. By default, nothing has to be set.
 * If you wish to override the SoapClient's options, please refer to the sample below.
 *
 * This is an associative array as:
 * - the key must be a SessionManagementWsdlClass constant beginning with WSDL_
 * - the value must be the corresponding key value
 * Each option matches the {@link http://www.php.net/manual/en/soapclient.soapclient.php} options
 *
 * Here is below an example of how you can set the array:
 * $wsdl = array();
 * $wsdl[SessionManagementWsdlClass::WSDL_URL] = 'https://demo.hosted.panopto.com/Panopto/PublicAPI/4.6/SessionManagement.svc?singlewsdl';
 * $wsdl[SessionManagementWsdlClass::WSDL_CACHE_WSDL] = WSDL_CACHE_NONE;
 * $wsdl[SessionManagementWsdlClass::WSDL_TRACE] = true;
 * $wsdl[SessionManagementWsdlClass::WSDL_LOGIN] = 'myLogin';
 * $wsdl[SessionManagementWsdlClass::WSDL_PASSWD] = '**********';
 * etc....
 * Then instantiate the Service class as:
 * - $wsdlObject = new SessionManagementWsdlClass($wsdl);
 */
/**
 * Examples
 */


/*****************************************
 * Example for SessionManagementServiceAdd
 */
$sessionManagementServiceAdd = new SessionManagementServiceAdd();
// sample call for SessionManagementServiceAdd::AddFolder()
if($sessionManagementServiceAdd->AddFolder(new SessionManagementStructAddFolder(/*** update parameters list ***/)))
    print_r($sessionManagementServiceAdd->getResult());
else
    print_r($sessionManagementServiceAdd->getLastError());
// sample call for SessionManagementServiceAdd::AddSession()
if($sessionManagementServiceAdd->AddSession(new SessionManagementStructAddSession(/*** update parameters list ***/)))
    print_r($sessionManagementServiceAdd->getResult());
else
    print_r($sessionManagementServiceAdd->getLastError());

/*****************************************
 * Example for SessionManagementServiceGet
 */
$sessionManagementServiceGet = new SessionManagementServiceGet();
// sample call for SessionManagementServiceGet::GetFoldersById()
if($sessionManagementServiceGet->GetFoldersById(new SessionManagementStructGetFoldersById(/*** update parameters list ***/)))
    print_r($sessionManagementServiceGet->getResult());
else
    print_r($sessionManagementServiceGet->getLastError());
// sample call for SessionManagementServiceGet::GetFoldersWithExternalContextById()
if($sessionManagementServiceGet->GetFoldersWithExternalContextById(new SessionManagementStructGetFoldersWithExternalContextById(/*** update parameters list ***/)))
    print_r($sessionManagementServiceGet->getResult());
else
    print_r($sessionManagementServiceGet->getLastError());
// sample call for SessionManagementServiceGet::GetFoldersByExternalId()
if($sessionManagementServiceGet->GetFoldersByExternalId(new SessionManagementStructGetFoldersByExternalId(/*** update parameters list ***/)))
    print_r($sessionManagementServiceGet->getResult());
else
    print_r($sessionManagementServiceGet->getLastError());
// sample call for SessionManagementServiceGet::GetFoldersWithExternalContextByExternalId()
if($sessionManagementServiceGet->GetFoldersWithExternalContextByExternalId(new SessionManagementStructGetFoldersWithExternalContextByExternalId(/*** update parameters list ***/)))
    print_r($sessionManagementServiceGet->getResult());
else
    print_r($sessionManagementServiceGet->getLastError());
// sample call for SessionManagementServiceGet::GetAllFoldersByExternalId()
if($sessionManagementServiceGet->GetAllFoldersByExternalId(new SessionManagementStructGetAllFoldersByExternalId(/*** update parameters list ***/)))
    print_r($sessionManagementServiceGet->getResult());
else
    print_r($sessionManagementServiceGet->getLastError());
// sample call for SessionManagementServiceGet::GetAllFoldersWithExternalContextByExternalId()
if($sessionManagementServiceGet->GetAllFoldersWithExternalContextByExternalId(new SessionManagementStructGetAllFoldersWithExternalContextByExternalId(/*** update parameters list ***/)))
    print_r($sessionManagementServiceGet->getResult());
else
    print_r($sessionManagementServiceGet->getLastError());
// sample call for SessionManagementServiceGet::GetSessionsById()
if($sessionManagementServiceGet->GetSessionsById(new SessionManagementStructGetSessionsById(/*** update parameters list ***/)))
    print_r($sessionManagementServiceGet->getResult());
else
    print_r($sessionManagementServiceGet->getLastError());
// sample call for SessionManagementServiceGet::GetSessionsByExternalId()
if($sessionManagementServiceGet->GetSessionsByExternalId(new SessionManagementStructGetSessionsByExternalId(/*** update parameters list ***/)))
    print_r($sessionManagementServiceGet->getResult());
else
    print_r($sessionManagementServiceGet->getLastError());
// sample call for SessionManagementServiceGet::GetSessionsList()
if($sessionManagementServiceGet->GetSessionsList(new SessionManagementStructGetSessionsList(/*** update parameters list ***/)))
    print_r($sessionManagementServiceGet->getResult());
else
    print_r($sessionManagementServiceGet->getLastError());
// sample call for SessionManagementServiceGet::GetFoldersList()
if($sessionManagementServiceGet->GetFoldersList(new SessionManagementStructGetFoldersList(/*** update parameters list ***/)))
    print_r($sessionManagementServiceGet->getResult());
else
    print_r($sessionManagementServiceGet->getLastError());
// sample call for SessionManagementServiceGet::GetFoldersWithExternalContextList()
if($sessionManagementServiceGet->GetFoldersWithExternalContextList(new SessionManagementStructGetFoldersWithExternalContextList(/*** update parameters list ***/)))
    print_r($sessionManagementServiceGet->getResult());
else
    print_r($sessionManagementServiceGet->getLastError());
// sample call for SessionManagementServiceGet::GetCreatorFoldersList()
if($sessionManagementServiceGet->GetCreatorFoldersList(new SessionManagementStructGetCreatorFoldersList(/*** update parameters list ***/)))
    print_r($sessionManagementServiceGet->getResult());
else
    print_r($sessionManagementServiceGet->getLastError());
// sample call for SessionManagementServiceGet::GetCreatorFoldersWithExternalContextList()
if($sessionManagementServiceGet->GetCreatorFoldersWithExternalContextList(new SessionManagementStructGetCreatorFoldersWithExternalContextList(/*** update parameters list ***/)))
    print_r($sessionManagementServiceGet->getResult());
else
    print_r($sessionManagementServiceGet->getLastError());
// sample call for SessionManagementServiceGet::GetRecorderDownloadUrls()
if($sessionManagementServiceGet->GetRecorderDownloadUrls())
    print_r($sessionManagementServiceGet->getResult());
else
    print_r($sessionManagementServiceGet->getLastError());
// sample call for SessionManagementServiceGet::GetNote()
if($sessionManagementServiceGet->GetNote(new SessionManagementStructGetNote(/*** update parameters list ***/)))
    print_r($sessionManagementServiceGet->getResult());
else
    print_r($sessionManagementServiceGet->getLastError());
// sample call for SessionManagementServiceGet::GetFoldersAvailabilitySettings()
if($sessionManagementServiceGet->GetFoldersAvailabilitySettings(new SessionManagementStructGetFoldersAvailabilitySettings(/*** update parameters list ***/)))
    print_r($sessionManagementServiceGet->getResult());
else
    print_r($sessionManagementServiceGet->getLastError());
// sample call for SessionManagementServiceGet::GetSessionsAvailabilitySettings()
if($sessionManagementServiceGet->GetSessionsAvailabilitySettings(new SessionManagementStructGetSessionsAvailabilitySettings(/*** update parameters list ***/)))
    print_r($sessionManagementServiceGet->getResult());
else
    print_r($sessionManagementServiceGet->getLastError());
// sample call for SessionManagementServiceGet::GetPersonalFolderForUser()
if($sessionManagementServiceGet->GetPersonalFolderForUser(new SessionManagementStructGetPersonalFolderForUser(/*** update parameters list ***/)))
    print_r($sessionManagementServiceGet->getResult());
else
    print_r($sessionManagementServiceGet->getLastError());

/********************************************
 * Example for SessionManagementServiceUpdate
 */
$sessionManagementServiceUpdate = new SessionManagementServiceUpdate();
// sample call for SessionManagementServiceUpdate::UpdateSessionName()
if($sessionManagementServiceUpdate->UpdateSessionName(new SessionManagementStructUpdateSessionName(/*** update parameters list ***/)))
    print_r($sessionManagementServiceUpdate->getResult());
else
    print_r($sessionManagementServiceUpdate->getLastError());
// sample call for SessionManagementServiceUpdate::UpdateSessionDescription()
if($sessionManagementServiceUpdate->UpdateSessionDescription(new SessionManagementStructUpdateSessionDescription(/*** update parameters list ***/)))
    print_r($sessionManagementServiceUpdate->getResult());
else
    print_r($sessionManagementServiceUpdate->getLastError());
// sample call for SessionManagementServiceUpdate::UpdateSessionIsBroadcast()
if($sessionManagementServiceUpdate->UpdateSessionIsBroadcast(new SessionManagementStructUpdateSessionIsBroadcast(/*** update parameters list ***/)))
    print_r($sessionManagementServiceUpdate->getResult());
else
    print_r($sessionManagementServiceUpdate->getLastError());
// sample call for SessionManagementServiceUpdate::UpdateSessionOwner()
if($sessionManagementServiceUpdate->UpdateSessionOwner(new SessionManagementStructUpdateSessionOwner(/*** update parameters list ***/)))
    print_r($sessionManagementServiceUpdate->getResult());
else
    print_r($sessionManagementServiceUpdate->getLastError());
// sample call for SessionManagementServiceUpdate::UpdateSessionExternalId()
if($sessionManagementServiceUpdate->UpdateSessionExternalId(new SessionManagementStructUpdateSessionExternalId(/*** update parameters list ***/)))
    print_r($sessionManagementServiceUpdate->getResult());
else
    print_r($sessionManagementServiceUpdate->getLastError());
// sample call for SessionManagementServiceUpdate::UpdateFolderName()
if($sessionManagementServiceUpdate->UpdateFolderName(new SessionManagementStructUpdateFolderName(/*** update parameters list ***/)))
    print_r($sessionManagementServiceUpdate->getResult());
else
    print_r($sessionManagementServiceUpdate->getLastError());
// sample call for SessionManagementServiceUpdate::UpdateFolderDescription()
if($sessionManagementServiceUpdate->UpdateFolderDescription(new SessionManagementStructUpdateFolderDescription(/*** update parameters list ***/)))
    print_r($sessionManagementServiceUpdate->getResult());
else
    print_r($sessionManagementServiceUpdate->getLastError());
// sample call for SessionManagementServiceUpdate::UpdateFolderEnablePodcast()
if($sessionManagementServiceUpdate->UpdateFolderEnablePodcast(new SessionManagementStructUpdateFolderEnablePodcast(/*** update parameters list ***/)))
    print_r($sessionManagementServiceUpdate->getResult());
else
    print_r($sessionManagementServiceUpdate->getLastError());
// sample call for SessionManagementServiceUpdate::UpdateFolderAllowPublicNotes()
if($sessionManagementServiceUpdate->UpdateFolderAllowPublicNotes(new SessionManagementStructUpdateFolderAllowPublicNotes(/*** update parameters list ***/)))
    print_r($sessionManagementServiceUpdate->getResult());
else
    print_r($sessionManagementServiceUpdate->getLastError());
// sample call for SessionManagementServiceUpdate::UpdateFolderAllowSessionDownload()
if($sessionManagementServiceUpdate->UpdateFolderAllowSessionDownload(new SessionManagementStructUpdateFolderAllowSessionDownload(/*** update parameters list ***/)))
    print_r($sessionManagementServiceUpdate->getResult());
else
    print_r($sessionManagementServiceUpdate->getLastError());
// sample call for SessionManagementServiceUpdate::UpdateFolderParent()
if($sessionManagementServiceUpdate->UpdateFolderParent(new SessionManagementStructUpdateFolderParent(/*** update parameters list ***/)))
    print_r($sessionManagementServiceUpdate->getResult());
else
    print_r($sessionManagementServiceUpdate->getLastError());
// sample call for SessionManagementServiceUpdate::UpdateFolderExternalId()
if($sessionManagementServiceUpdate->UpdateFolderExternalId(new SessionManagementStructUpdateFolderExternalId(/*** update parameters list ***/)))
    print_r($sessionManagementServiceUpdate->getResult());
else
    print_r($sessionManagementServiceUpdate->getLastError());
// sample call for SessionManagementServiceUpdate::UpdateFolderExternalIdWithProvider()
if($sessionManagementServiceUpdate->UpdateFolderExternalIdWithProvider(new SessionManagementStructUpdateFolderExternalIdWithProvider(/*** update parameters list ***/)))
    print_r($sessionManagementServiceUpdate->getResult());
else
    print_r($sessionManagementServiceUpdate->getLastError());
// sample call for SessionManagementServiceUpdate::UpdateFoldersAvailabilityStartSettings()
if($sessionManagementServiceUpdate->UpdateFoldersAvailabilityStartSettings(new SessionManagementStructUpdateFoldersAvailabilityStartSettings(/*** update parameters list ***/)))
    print_r($sessionManagementServiceUpdate->getResult());
else
    print_r($sessionManagementServiceUpdate->getLastError());
// sample call for SessionManagementServiceUpdate::UpdateFoldersAvailabilityEndSettings()
if($sessionManagementServiceUpdate->UpdateFoldersAvailabilityEndSettings(new SessionManagementStructUpdateFoldersAvailabilityEndSettings(/*** update parameters list ***/)))
    print_r($sessionManagementServiceUpdate->getResult());
else
    print_r($sessionManagementServiceUpdate->getLastError());
// sample call for SessionManagementServiceUpdate::UpdateSessionsAvailabilityStartSettings()
if($sessionManagementServiceUpdate->UpdateSessionsAvailabilityStartSettings(new SessionManagementStructUpdateSessionsAvailabilityStartSettings(/*** update parameters list ***/)))
    print_r($sessionManagementServiceUpdate->getResult());
else
    print_r($sessionManagementServiceUpdate->getLastError());
// sample call for SessionManagementServiceUpdate::UpdateSessionsAvailabilityEndSettings()
if($sessionManagementServiceUpdate->UpdateSessionsAvailabilityEndSettings(new SessionManagementStructUpdateSessionsAvailabilityEndSettings(/*** update parameters list ***/)))
    print_r($sessionManagementServiceUpdate->getResult());
else
    print_r($sessionManagementServiceUpdate->getLastError());

/******************************************
 * Example for SessionManagementServiceMove
 */
$sessionManagementServiceMove = new SessionManagementServiceMove();
// sample call for SessionManagementServiceMove::MoveSessions()
if($sessionManagementServiceMove->MoveSessions(new SessionManagementStructMoveSessions(/*** update parameters list ***/)))
    print_r($sessionManagementServiceMove->getResult());
else
    print_r($sessionManagementServiceMove->getLastError());

/********************************************
 * Example for SessionManagementServiceDelete
 */
$sessionManagementServiceDelete = new SessionManagementServiceDelete();
// sample call for SessionManagementServiceDelete::DeleteSessions()
if($sessionManagementServiceDelete->DeleteSessions(new SessionManagementStructDeleteSessions(/*** update parameters list ***/)))
    print_r($sessionManagementServiceDelete->getResult());
else
    print_r($sessionManagementServiceDelete->getLastError());
// sample call for SessionManagementServiceDelete::DeleteFolders()
if($sessionManagementServiceDelete->DeleteFolders(new SessionManagementStructDeleteFolders(/*** update parameters list ***/)))
    print_r($sessionManagementServiceDelete->getResult());
else
    print_r($sessionManagementServiceDelete->getLastError());
// sample call for SessionManagementServiceDelete::DeleteNote()
if($sessionManagementServiceDelete->DeleteNote(new SessionManagementStructDeleteNote(/*** update parameters list ***/)))
    print_r($sessionManagementServiceDelete->getResult());
else
    print_r($sessionManagementServiceDelete->getLastError());

/***********************************************
 * Example for SessionManagementServiceProvision
 */
$sessionManagementServiceProvision = new SessionManagementServiceProvision();
// sample call for SessionManagementServiceProvision::ProvisionExternalCourse()
if($sessionManagementServiceProvision->ProvisionExternalCourse(new SessionManagementStructProvisionExternalCourse(/*** update parameters list ***/)))
    print_r($sessionManagementServiceProvision->getResult());
else
    print_r($sessionManagementServiceProvision->getLastError());
// sample call for SessionManagementServiceProvision::ProvisionExternalCourseWithRoles()
if($sessionManagementServiceProvision->ProvisionExternalCourseWithRoles(new SessionManagementStructProvisionExternalCourseWithRoles(/*** update parameters list ***/)))
    print_r($sessionManagementServiceProvision->getResult());
else
    print_r($sessionManagementServiceProvision->getLastError());

/*****************************************
 * Example for SessionManagementServiceSet
 */
$sessionManagementServiceSet = new SessionManagementServiceSet();
// sample call for SessionManagementServiceSet::SetExternalCourseAccess()
if($sessionManagementServiceSet->SetExternalCourseAccess(new SessionManagementStructSetExternalCourseAccess(/*** update parameters list ***/)))
    print_r($sessionManagementServiceSet->getResult());
else
    print_r($sessionManagementServiceSet->getLastError());
// sample call for SessionManagementServiceSet::SetExternalCourseAccessForRoles()
if($sessionManagementServiceSet->SetExternalCourseAccessForRoles(new SessionManagementStructSetExternalCourseAccessForRoles(/*** update parameters list ***/)))
    print_r($sessionManagementServiceSet->getResult());
else
    print_r($sessionManagementServiceSet->getLastError());
// sample call for SessionManagementServiceSet::SetCopiedExternalCourseAccess()
if($sessionManagementServiceSet->SetCopiedExternalCourseAccess(new SessionManagementStructSetCopiedExternalCourseAccess(/*** update parameters list ***/)))
    print_r($sessionManagementServiceSet->getResult());
else
    print_r($sessionManagementServiceSet->getLastError());
// sample call for SessionManagementServiceSet::SetCopiedExternalCourseAccessForRoles()
if($sessionManagementServiceSet->SetCopiedExternalCourseAccessForRoles(new SessionManagementStructSetCopiedExternalCourseAccessForRoles(/*** update parameters list ***/)))
    print_r($sessionManagementServiceSet->getResult());
else
    print_r($sessionManagementServiceSet->getLastError());
// sample call for SessionManagementServiceSet::SetNotesPublic()
if($sessionManagementServiceSet->SetNotesPublic(new SessionManagementStructSetNotesPublic(/*** update parameters list ***/)))
    print_r($sessionManagementServiceSet->getResult());
else
    print_r($sessionManagementServiceSet->getLastError());

/********************************************
 * Example for SessionManagementServiceCreate
 */
$sessionManagementServiceCreate = new SessionManagementServiceCreate();
// sample call for SessionManagementServiceCreate::CreateNoteByRelativeTime()
if($sessionManagementServiceCreate->CreateNoteByRelativeTime(new SessionManagementStructCreateNoteByRelativeTime(/*** update parameters list ***/)))
    print_r($sessionManagementServiceCreate->getResult());
else
    print_r($sessionManagementServiceCreate->getLastError());
// sample call for SessionManagementServiceCreate::CreateNoteByAbsoluteTime()
if($sessionManagementServiceCreate->CreateNoteByAbsoluteTime(new SessionManagementStructCreateNoteByAbsoluteTime(/*** update parameters list ***/)))
    print_r($sessionManagementServiceCreate->getResult());
else
    print_r($sessionManagementServiceCreate->getLastError());
// sample call for SessionManagementServiceCreate::CreateCaptionByRelativeTime()
if($sessionManagementServiceCreate->CreateCaptionByRelativeTime(new SessionManagementStructCreateCaptionByRelativeTime(/*** update parameters list ***/)))
    print_r($sessionManagementServiceCreate->getResult());
else
    print_r($sessionManagementServiceCreate->getLastError());
// sample call for SessionManagementServiceCreate::CreateCaptionByAbsoluteTime()
if($sessionManagementServiceCreate->CreateCaptionByAbsoluteTime(new SessionManagementStructCreateCaptionByAbsoluteTime(/*** update parameters list ***/)))
    print_r($sessionManagementServiceCreate->getResult());
else
    print_r($sessionManagementServiceCreate->getLastError());

/******************************************
 * Example for SessionManagementServiceEdit
 */
$sessionManagementServiceEdit = new SessionManagementServiceEdit();
// sample call for SessionManagementServiceEdit::EditNote()
if($sessionManagementServiceEdit->EditNote(new SessionManagementStructEditNote(/*** update parameters list ***/)))
    print_r($sessionManagementServiceEdit->getResult());
else
    print_r($sessionManagementServiceEdit->getLastError());

/******************************************
 * Example for SessionManagementServiceList
 */
$sessionManagementServiceList = new SessionManagementServiceList();
// sample call for SessionManagementServiceList::ListNotes()
if($sessionManagementServiceList->ListNotes(new SessionManagementStructListNotes(/*** update parameters list ***/)))
    print_r($sessionManagementServiceList->getResult());
else
    print_r($sessionManagementServiceList->getLastError());

/*****************************************
 * Example for SessionManagementServiceAre
 */
$sessionManagementServiceAre = new SessionManagementServiceAre();
// sample call for SessionManagementServiceAre::AreUsersNotesPublic()
if($sessionManagementServiceAre->AreUsersNotesPublic(new SessionManagementStructAreUsersNotesPublic(/*** update parameters list ***/)))
    print_r($sessionManagementServiceAre->getResult());
else
    print_r($sessionManagementServiceAre->getLastError());

/****************************************
 * Example for SessionManagementServiceIs
 */
$sessionManagementServiceIs = new SessionManagementServiceIs();
// sample call for SessionManagementServiceIs::IsDropbox()
if($sessionManagementServiceIs->IsDropbox(new SessionManagementStructIsDropbox(/*** update parameters list ***/)))
    print_r($sessionManagementServiceIs->getResult());
else
    print_r($sessionManagementServiceIs->getLastError());

/********************************************
 * Example for SessionManagementServiceUpload
 */
$sessionManagementServiceUpload = new SessionManagementServiceUpload();
// sample call for SessionManagementServiceUpload::UploadTranscript()
if($sessionManagementServiceUpload->UploadTranscript(new SessionManagementStructUploadTranscript(/*** update parameters list ***/)))
    print_r($sessionManagementServiceUpload->getResult());
else
    print_r($sessionManagementServiceUpload->getLastError());
