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
 * File to load generated classes once at once time
 * @package SessionManagement
 * @author Panopto
 * @version 20150429-01
 * @date 2017-01-19
 */
/**
 * Includes for all generated classes files
 * @author Panopto
 * @version 20150429-01
 * @date 2017-01-19
 */
require_once dirname(__FILE__) . '/SessionManagementWsdlClass.php';
require_once dirname(__FILE__) . '/Folder/Base/SessionManagementStructFolderBase.php';
require_once dirname(__FILE__) . '/ExternalHierarchyInfo/SessionManagementStructExternalHierarchyInfo.php';
require_once dirname(__FILE__) . '/Array/OfExternalHierarchyInfo/SessionManagementStructArrayOfExternalHierarchyInfo.php';
require_once dirname(__FILE__) . '/Ensure/Branch/SessionManagementStructEnsureExternalHierarchyBranch.php';
require_once dirname(__FILE__) . '/Ensure/Response/SessionManagementStructEnsureExternalHierarchyBranchResponse.php';
require_once dirname(__FILE__) . '/Ensure/SessionManagementServiceEnsure.php';
require_once dirname(__FILE__) . '/Provision/Course/SessionManagementStructProvisionExternalCourse.php';
require_once dirname(__FILE__) . '/Provision/Response/SessionManagementStructProvisionExternalCourseResponse.php';
require_once dirname(__FILE__) . '/Provision/Roles/SessionManagementStructProvisionExternalCourseWithRoles.php';
require_once dirname(__FILE__) . '/Delete/Response/SessionManagementStructDeleteFoldersResponse.php';
require_once dirname(__FILE__) . '/Delete/Folders/SessionManagementStructDeleteFolders.php';
require_once dirname(__FILE__) . '/Delete/Sessions/SessionManagementStructDeleteSessions.php';
require_once dirname(__FILE__) . '/Delete/Response/SessionManagementStructDeleteSessionsResponse.php';
require_once dirname(__FILE__) . '/Provision/Response/SessionManagementStructProvisionExternalCourseWithRolesResponse.php';
require_once dirname(__FILE__) . '/Unprovision/Course/SessionManagementStructUnprovisionExternalCourse.php';
require_once dirname(__FILE__) . '/Unprovision/Response/SessionManagementStructUnprovisionExternalCourseResponse.php';
require_once dirname(__FILE__) . '/Unprovision/SessionManagementServiceUnprovision.php';
require_once dirname(__FILE__) . '/Set/Access/SessionManagementStructSetExternalCourseAccess.php';
require_once dirname(__FILE__) . '/Set/Roles/SessionManagementStructSetCopiedExternalCourseAccessForRoles.php';
require_once dirname(__FILE__) . '/Set/Response/SessionManagementStructSetCopiedExternalCourseAccessForRolesResponse.php';
require_once dirname(__FILE__) . '/Get/Urls/SessionManagementStructGetRecorderDownloadUrls.php';
require_once dirname(__FILE__) . '/Set/Response/SessionManagementStructSetCopiedExternalCourseAccessResponse.php';
require_once dirname(__FILE__) . '/Set/Access/SessionManagementStructSetCopiedExternalCourseAccess.php';
require_once dirname(__FILE__) . '/Set/Response/SessionManagementStructSetExternalCourseAccessResponse.php';
require_once dirname(__FILE__) . '/Set/Roles/SessionManagementStructSetExternalCourseAccessForRoles.php';
require_once dirname(__FILE__) . '/Set/Response/SessionManagementStructSetExternalCourseAccessForRolesResponse.php';
require_once dirname(__FILE__) . '/Update/Response/SessionManagementStructUpdateFolderExternalIdWithProviderResponse.php';
require_once dirname(__FILE__) . '/Update/Provider/SessionManagementStructUpdateFolderExternalIdWithProvider.php';
require_once dirname(__FILE__) . '/Update/Response/SessionManagementStructUpdateFolderNameResponse.php';
require_once dirname(__FILE__) . '/Update/Description/SessionManagementStructUpdateFolderDescription.php';
require_once dirname(__FILE__) . '/Update/Response/SessionManagementStructUpdateFolderDescriptionResponse.php';
require_once dirname(__FILE__) . '/Update/Name/SessionManagementStructUpdateFolderName.php';
require_once dirname(__FILE__) . '/Update/Response/SessionManagementStructUpdateSessionExternalIdResponse.php';
require_once dirname(__FILE__) . '/Move/Sessions/SessionManagementStructMoveSessions.php';
require_once dirname(__FILE__) . '/Move/Response/SessionManagementStructMoveSessionsResponse.php';
require_once dirname(__FILE__) . '/Update/Id/SessionManagementStructUpdateSessionExternalId.php';
require_once dirname(__FILE__) . '/Update/Podcast/SessionManagementStructUpdateFolderEnablePodcast.php';
require_once dirname(__FILE__) . '/Update/Response/SessionManagementStructUpdateFolderEnablePodcastResponse.php';
require_once dirname(__FILE__) . '/Update/Response/SessionManagementStructUpdateFolderParentResponse.php';
require_once dirname(__FILE__) . '/Update/Id/SessionManagementStructUpdateFolderExternalId.php';
require_once dirname(__FILE__) . '/Update/Response/SessionManagementStructUpdateFolderExternalIdResponse.php';
require_once dirname(__FILE__) . '/Update/Parent/SessionManagementStructUpdateFolderParent.php';
require_once dirname(__FILE__) . '/Update/Response/SessionManagementStructUpdateFolderAllowSessionDownloadResponse.php';
require_once dirname(__FILE__) . '/Update/Notes/SessionManagementStructUpdateFolderAllowPublicNotes.php';
require_once dirname(__FILE__) . '/Update/Response/SessionManagementStructUpdateFolderAllowPublicNotesResponse.php';
require_once dirname(__FILE__) . '/Update/Download/SessionManagementStructUpdateFolderAllowSessionDownload.php';
require_once dirname(__FILE__) . '/Get/Response/SessionManagementStructGetRecorderDownloadUrlsResponse.php';
require_once dirname(__FILE__) . '/Create/Time/SessionManagementStructCreateNoteByRelativeTime.php';
require_once dirname(__FILE__) . '/Get/Settings/SessionManagementStructGetSessionsAvailabilitySettings.php';
require_once dirname(__FILE__) . '/Get/Response/SessionManagementStructGetSessionsAvailabilitySettingsResponse.php';
require_once dirname(__FILE__) . '/Update/Settings/SessionManagementStructUpdateFoldersAvailabilityStartSettings.php';
require_once dirname(__FILE__) . '/Get/Response/SessionManagementStructGetFoldersAvailabilitySettingsResponse.php';
require_once dirname(__FILE__) . '/Get/Settings/SessionManagementStructGetFoldersAvailabilitySettings.php';
require_once dirname(__FILE__) . '/Create/Response/SessionManagementStructCreateCaptionByAbsoluteTimeResponse.php';
require_once dirname(__FILE__) . '/Upload/Transcript/SessionManagementStructUploadTranscript.php';
require_once dirname(__FILE__) . '/Upload/Response/SessionManagementStructUploadTranscriptResponse.php';
require_once dirname(__FILE__) . '/Update/Response/SessionManagementStructUpdateFoldersAvailabilityStartSettingsResponse.php';
require_once dirname(__FILE__) . '/Update/Settings/SessionManagementStructUpdateFoldersAvailabilityEndSettings.php';
require_once dirname(__FILE__) . '/Update/Response/SessionManagementStructUpdateSessionsAvailabilityEndSettingsResponse.php';
require_once dirname(__FILE__) . '/Get/User/SessionManagementStructGetPersonalFolderForUser.php';
require_once dirname(__FILE__) . '/Get/Response/SessionManagementStructGetPersonalFolderForUserResponse.php';
require_once dirname(__FILE__) . '/Update/Settings/SessionManagementStructUpdateSessionsAvailabilityEndSettings.php';
require_once dirname(__FILE__) . '/Update/Response/SessionManagementStructUpdateSessionsAvailabilityStartSettingsResponse.php';
require_once dirname(__FILE__) . '/Update/Response/SessionManagementStructUpdateFoldersAvailabilityEndSettingsResponse.php';
require_once dirname(__FILE__) . '/Update/Settings/SessionManagementStructUpdateSessionsAvailabilityStartSettings.php';
require_once dirname(__FILE__) . '/Create/Time/SessionManagementStructCreateCaptionByAbsoluteTime.php';
require_once dirname(__FILE__) . '/Create/Response/SessionManagementStructCreateCaptionByRelativeTimeResponse.php';
require_once dirname(__FILE__) . '/Delete/Note/SessionManagementStructDeleteNote.php';
require_once dirname(__FILE__) . '/Delete/Response/SessionManagementStructDeleteNoteResponse.php';
require_once dirname(__FILE__) . '/Get/Note/SessionManagementStructGetNote.php';
require_once dirname(__FILE__) . '/Edit/Response/SessionManagementStructEditNoteResponse.php';
require_once dirname(__FILE__) . '/Edit/Note/SessionManagementStructEditNote.php';
require_once dirname(__FILE__) . '/Create/Response/SessionManagementStructCreateNoteByRelativeTimeResponse.php';
require_once dirname(__FILE__) . '/Create/Time/SessionManagementStructCreateNoteByAbsoluteTime.php';
require_once dirname(__FILE__) . '/Create/Response/SessionManagementStructCreateNoteByAbsoluteTimeResponse.php';
require_once dirname(__FILE__) . '/Get/Response/SessionManagementStructGetNoteResponse.php';
require_once dirname(__FILE__) . '/List/Notes/SessionManagementStructListNotes.php';
require_once dirname(__FILE__) . '/Is/Dropbox/SessionManagementStructIsDropbox.php';
require_once dirname(__FILE__) . '/Is/Response/SessionManagementStructIsDropboxResponse.php';
require_once dirname(__FILE__) . '/Create/Time/SessionManagementStructCreateCaptionByRelativeTime.php';
require_once dirname(__FILE__) . '/Set/Response/SessionManagementStructSetNotesPublicResponse.php';
require_once dirname(__FILE__) . '/Set/Public/SessionManagementStructSetNotesPublic.php';
require_once dirname(__FILE__) . '/Are/Public/SessionManagementStructAreUsersNotesPublic.php';
require_once dirname(__FILE__) . '/Are/Response/SessionManagementStructAreUsersNotesPublicResponse.php';
require_once dirname(__FILE__) . '/Update/Response/SessionManagementStructUpdateSessionOwnerResponse.php';
require_once dirname(__FILE__) . '/Update/Owner/SessionManagementStructUpdateSessionOwner.php';
require_once dirname(__FILE__) . '/Array/Folder/SessionManagementStructArrayOfFolder.php';
require_once dirname(__FILE__) . '/Array/Folder/SessionManagementStructArrayOfExtendedFolder.php';
require_once dirname(__FILE__) . '/List/Response/SessionManagementStructListSessionsResponse.php';
require_once dirname(__FILE__) . '/List/Response/SessionManagementStructListFoldersResponse.php';
require_once dirname(__FILE__) . '/List/Response/SessionManagementStructListExtendedFoldersResponse.php';
require_once dirname(__FILE__) . '/Folder/SessionManagementStructFolder.php';
require_once dirname(__FILE__) . '/Folder/SessionManagementStructExtendedFolder.php';
require_once dirname(__FILE__) . '/Date/Offset/SessionManagementStructDateTimeOffset.php';
require_once dirname(__FILE__) . '/Array/Session/SessionManagementStructArrayOfSession.php';
require_once dirname(__FILE__) . '/Array/Context/SessionManagementStructArrayOfFolderWithExternalContext.php';
require_once dirname(__FILE__) . '/Folder/Context/SessionManagementStructFolderWithExternalContext.php';
require_once dirname(__FILE__) . '/List/Context/SessionManagementStructListFoldersResponseWithExternalContext.php';
require_once dirname(__FILE__) . '/Folders/Settings/SessionManagementStructFoldersWithAvailabilitySettings.php';
require_once dirname(__FILE__) . '/Sessions/Settings/SessionManagementStructSessionsWithAvailabilitySettings.php';
require_once dirname(__FILE__) . '/Array/Settings/SessionManagementStructArrayOfSessionAvailabilitySettings.php';
require_once dirname(__FILE__) . '/Session/Settings/SessionManagementStructSessionAvailabilitySettings.php';
require_once dirname(__FILE__) . '/Folder/Type/SessionManagementEnumFolderStartSettingType.php';
require_once dirname(__FILE__) . '/Folder/Type/SessionManagementEnumFolderEndSettingType.php';
require_once dirname(__FILE__) . '/Array/Settings/SessionManagementStructArrayOfFolderAvailabilitySettings.php';
require_once dirname(__FILE__) . '/Folder/Settings/SessionManagementStructFolderAvailabilitySettings.php';
require_once dirname(__FILE__) . '/Session/SessionManagementStructSession.php';
require_once dirname(__FILE__) . '/Array/Ofstring/SessionManagementStructArrayOfstring.php';
require_once dirname(__FILE__) . '/Pagination/SessionManagementStructPagination.php';
require_once dirname(__FILE__) . '/Session/Field/SessionManagementEnumSessionSortField.php';
require_once dirname(__FILE__) . '/Array/State/SessionManagementStructArrayOfSessionState.php';
require_once dirname(__FILE__) . '/List/Request/SessionManagementStructListSessionsRequest.php';
require_once dirname(__FILE__) . '/Session/State/SessionManagementEnumSessionState.php';
require_once dirname(__FILE__) . '/Authentication/Info/SessionManagementStructAuthenticationInfo.php';
require_once dirname(__FILE__) . '/List/Request/SessionManagementStructListFoldersRequest.php';
require_once dirname(__FILE__) . '/Folder/Field/SessionManagementEnumFolderSortField.php';
require_once dirname(__FILE__) . '/List/Response/SessionManagementStructListNotesResponse.php';
require_once dirname(__FILE__) . '/Array/Note/SessionManagementStructArrayOfNote.php';
require_once dirname(__FILE__) . '/Array/Ofguid/SessionManagementStructArrayOfguid.php';
require_once dirname(__FILE__) . '/Note/SessionManagementStructNote.php';
require_once dirname(__FILE__) . '/Recorder/Response/SessionManagementStructRecorderDownloadUrlResponse.php';
require_once dirname(__FILE__) . '/Array/Role/SessionManagementStructArrayOfAccessRole.php';
require_once dirname(__FILE__) . '/Access/Role/SessionManagementEnumAccessRole.php';
require_once dirname(__FILE__) . '/Session/Type/SessionManagementEnumSessionEndSettingType.php';
require_once dirname(__FILE__) . '/Session/Type/SessionManagementEnumSessionStartSettingType.php';
require_once dirname(__FILE__) . '/Get/List/SessionManagementStructGetFoldersWithExternalContextList.php';
require_once dirname(__FILE__) . '/Get/Response/SessionManagementStructGetFoldersWithExternalContextListResponse.php';
require_once dirname(__FILE__) . '/Get/List/SessionManagementStructGetCreatorFoldersList.php';
require_once dirname(__FILE__) . '/Get/List/SessionManagementStructGetExtendedCreatorFoldersList.php';
require_once dirname(__FILE__) . '/Get/Response/SessionManagementStructGetFoldersListResponse.php';
require_once dirname(__FILE__) . '/Get/List/SessionManagementStructGetFoldersList.php';
require_once dirname(__FILE__) . '/Get/Response/SessionManagementStructGetSessionsByExternalIdResponse.php';
require_once dirname(__FILE__) . '/Get/List/SessionManagementStructGetSessionsList.php';
require_once dirname(__FILE__) . '/Get/Response/SessionManagementStructGetSessionsListResponse.php';
require_once dirname(__FILE__) . '/Get/Response/SessionManagementStructGetCreatorFoldersListResponse.php';
require_once dirname(__FILE__) . '/Get/Response/SessionManagementStructGetExtendedCreatorFoldersListResponse.php';
require_once dirname(__FILE__) . '/Get/List/SessionManagementStructGetCreatorFoldersWithExternalContextList.php';
require_once dirname(__FILE__) . '/Update/Response/SessionManagementStructUpdateSessionDescriptionResponse.php';
require_once dirname(__FILE__) . '/Update/Broadcast/SessionManagementStructUpdateSessionIsBroadcast.php';
require_once dirname(__FILE__) . '/Update/Response/SessionManagementStructUpdateSessionIsBroadcastResponse.php';
require_once dirname(__FILE__) . '/Update/Description/SessionManagementStructUpdateSessionDescription.php';
require_once dirname(__FILE__) . '/Update/Response/SessionManagementStructUpdateSessionNameResponse.php';
require_once dirname(__FILE__) . '/Get/Response/SessionManagementStructGetCreatorFoldersWithExternalContextListResponse.php';
require_once dirname(__FILE__) . '/Update/Name/SessionManagementStructUpdateSessionName.php';
require_once dirname(__FILE__) . '/Get/Id/SessionManagementStructGetSessionsByExternalId.php';
require_once dirname(__FILE__) . '/Get/Response/SessionManagementStructGetSessionsByIdResponse.php';
require_once dirname(__FILE__) . '/Get/Response/SessionManagementStructGetFoldersByIdResponse.php';
require_once dirname(__FILE__) . '/Get/Id/SessionManagementStructGetFoldersWithExternalContextById.php';
require_once dirname(__FILE__) . '/Get/Response/SessionManagementStructGetFoldersWithExternalContextByIdResponse.php';
require_once dirname(__FILE__) . '/Get/Id/SessionManagementStructGetFoldersById.php';
require_once dirname(__FILE__) . '/Add/Response/SessionManagementStructAddSessionResponse.php';
require_once dirname(__FILE__) . '/Add/Folder/SessionManagementStructAddFolder.php';
require_once dirname(__FILE__) . '/Add/Response/SessionManagementStructAddFolderResponse.php';
require_once dirname(__FILE__) . '/Add/Session/SessionManagementStructAddSession.php';
require_once dirname(__FILE__) . '/Get/Id/SessionManagementStructGetFoldersByExternalId.php';
require_once dirname(__FILE__) . '/Get/Response/SessionManagementStructGetFoldersByExternalIdResponse.php';
require_once dirname(__FILE__) . '/Get/Id/SessionManagementStructGetAllFoldersWithExternalContextByExternalId.php';
require_once dirname(__FILE__) . '/Get/Response/SessionManagementStructGetAllFoldersWithExternalContextByExternalIdResponse.php';
require_once dirname(__FILE__) . '/Get/Id/SessionManagementStructGetSessionsById.php';
require_once dirname(__FILE__) . '/Get/Response/SessionManagementStructGetAllFoldersByExternalIdResponse.php';
require_once dirname(__FILE__) . '/Get/Id/SessionManagementStructGetAllFoldersByExternalId.php';
require_once dirname(__FILE__) . '/Get/Id/SessionManagementStructGetFoldersWithExternalContextByExternalId.php';
require_once dirname(__FILE__) . '/Get/Response/SessionManagementStructGetFoldersWithExternalContextByExternalIdResponse.php';
require_once dirname(__FILE__) . '/Add/SessionManagementServiceAdd.php';
require_once dirname(__FILE__) . '/Get/SessionManagementServiceGet.php';
require_once dirname(__FILE__) . '/Update/SessionManagementServiceUpdate.php';
require_once dirname(__FILE__) . '/Move/SessionManagementServiceMove.php';
require_once dirname(__FILE__) . '/Delete/SessionManagementServiceDelete.php';
require_once dirname(__FILE__) . '/Provision/SessionManagementServiceProvision.php';
require_once dirname(__FILE__) . '/Set/SessionManagementServiceSet.php';
require_once dirname(__FILE__) . '/Create/SessionManagementServiceCreate.php';
require_once dirname(__FILE__) . '/Edit/SessionManagementServiceEdit.php';
require_once dirname(__FILE__) . '/List/SessionManagementServiceList.php';
require_once dirname(__FILE__) . '/Are/SessionManagementServiceAre.php';
require_once dirname(__FILE__) . '/Is/SessionManagementServiceIs.php';
require_once dirname(__FILE__) . '/Upload/SessionManagementServiceUpload.php';
require_once dirname(__FILE__) . '/SessionManagementClassMap.php';
