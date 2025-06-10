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
 * File for the class which returns the class map definition
 * @package SessionManagement
 * @author Panopto
 * @version 20150429-01
 * @date 2017-01-19
 */
/**
 * Class which returns the class map definition by the static method SessionManagementClassMap::classMap()
 * @package SessionManagement
 * @author Panopto
 * @version 20150429-01
 * @date 2017-01-19
 */
class SessionManagementClassMap
{
    /**
     * This method returns the array containing the mapping between WSDL structs and generated classes
     * This array is sent to the SoapClient when calling the WS
     * @return array
     */
    final public static function classMap()
    {
        return array (
  'AccessRole' => 'SessionManagementEnumAccessRole',
  'AddFolder' => 'SessionManagementStructAddFolder',
  'AddFolderResponse' => 'SessionManagementStructAddFolderResponse',
  'AddSession' => 'SessionManagementStructAddSession',
  'AddSessionResponse' => 'SessionManagementStructAddSessionResponse',
  'AreUsersNotesPublic' => 'SessionManagementStructAreUsersNotesPublic',
  'AreUsersNotesPublicResponse' => 'SessionManagementStructAreUsersNotesPublicResponse',
  'ArrayOfAccessRole' => 'SessionManagementStructArrayOfAccessRole',
  'ArrayOfFolder' => 'SessionManagementStructArrayOfFolder',
  'ArrayOfExtendedFolder' => 'SessionManagementStructArrayOfExtendedFolder',
  'ArrayOfFolderAvailabilitySettings' => 'SessionManagementStructArrayOfFolderAvailabilitySettings',
  'ArrayOfFolderWithExternalContext' => 'SessionManagementStructArrayOfFolderWithExternalContext',
  'ArrayOfNote' => 'SessionManagementStructArrayOfNote',
  'ArrayOfSession' => 'SessionManagementStructArrayOfSession',
  'ArrayOfSessionAvailabilitySettings' => 'SessionManagementStructArrayOfSessionAvailabilitySettings',
  'ArrayOfSessionState' => 'SessionManagementStructArrayOfSessionState',
  'ArrayOfguid' => 'SessionManagementStructArrayOfguid',
  'ArrayOfstring' => 'SessionManagementStructArrayOfstring',
  'AuthenticationInfo' => 'SessionManagementStructAuthenticationInfo',
  'CreateCaptionByAbsoluteTime' => 'SessionManagementStructCreateCaptionByAbsoluteTime',
  'CreateCaptionByAbsoluteTimeResponse' => 'SessionManagementStructCreateCaptionByAbsoluteTimeResponse',
  'CreateCaptionByRelativeTime' => 'SessionManagementStructCreateCaptionByRelativeTime',
  'CreateCaptionByRelativeTimeResponse' => 'SessionManagementStructCreateCaptionByRelativeTimeResponse',
  'CreateNoteByAbsoluteTime' => 'SessionManagementStructCreateNoteByAbsoluteTime',
  'CreateNoteByAbsoluteTimeResponse' => 'SessionManagementStructCreateNoteByAbsoluteTimeResponse',
  'CreateNoteByRelativeTime' => 'SessionManagementStructCreateNoteByRelativeTime',
  'CreateNoteByRelativeTimeResponse' => 'SessionManagementStructCreateNoteByRelativeTimeResponse',
  'DateTimeOffset' => 'SessionManagementStructDateTimeOffset',
  'DeleteFolders' => 'SessionManagementStructDeleteFolders',
  'DeleteFoldersResponse' => 'SessionManagementStructDeleteFoldersResponse',
  'DeleteNote' => 'SessionManagementStructDeleteNote',
  'DeleteNoteResponse' => 'SessionManagementStructDeleteNoteResponse',
  'DeleteSessions' => 'SessionManagementStructDeleteSessions',
  'DeleteSessionsResponse' => 'SessionManagementStructDeleteSessionsResponse',
  'EditNote' => 'SessionManagementStructEditNote',
  'EditNoteResponse' => 'SessionManagementStructEditNoteResponse',
  'Folder' => 'SessionManagementStructFolder',
  'ExtendedFolder' => 'SessionManagementStructExtendedFolder',
  'FolderAvailabilitySettings' => 'SessionManagementStructFolderAvailabilitySettings',
  'FolderBase' => 'SessionManagementStructFolderBase',
  'FolderEndSettingType' => 'SessionManagementEnumFolderEndSettingType',
  'FolderSortField' => 'SessionManagementEnumFolderSortField',
  'FolderStartSettingType' => 'SessionManagementEnumFolderStartSettingType',
  'FolderWithExternalContext' => 'SessionManagementStructFolderWithExternalContext',
  'FoldersWithAvailabilitySettings' => 'SessionManagementStructFoldersWithAvailabilitySettings',
  'GetAllFoldersByExternalId' => 'SessionManagementStructGetAllFoldersByExternalId',
  'GetAllFoldersByExternalIdResponse' => 'SessionManagementStructGetAllFoldersByExternalIdResponse',
  'GetAllFoldersWithExternalContextByExternalId' => 'SessionManagementStructGetAllFoldersWithExternalContextByExternalId',
  'GetAllFoldersWithExternalContextByExternalIdResponse' => 'SessionManagementStructGetAllFoldersWithExternalContextByExternalIdResponse',
  'GetCreatorFoldersList' => 'SessionManagementStructGetCreatorFoldersList',
  'GetExtendedCreatorFoldersList' => 'SessionManagementStructGetExtendedCreatorFoldersList',
  'GetCreatorFoldersListResponse' => 'SessionManagementStructGetCreatorFoldersListResponse',
  'GetExtendedCreatorFoldersListResponse' => 'SessionManagementStructGetExtendedCreatorFoldersListResponse',
  'GetCreatorFoldersWithExternalContextList' => 'SessionManagementStructGetCreatorFoldersWithExternalContextList',
  'GetCreatorFoldersWithExternalContextListResponse' => 'SessionManagementStructGetCreatorFoldersWithExternalContextListResponse',
  'GetFoldersAvailabilitySettings' => 'SessionManagementStructGetFoldersAvailabilitySettings',
  'GetFoldersAvailabilitySettingsResponse' => 'SessionManagementStructGetFoldersAvailabilitySettingsResponse',
  'GetFoldersByExternalId' => 'SessionManagementStructGetFoldersByExternalId',
  'GetFoldersByExternalIdResponse' => 'SessionManagementStructGetFoldersByExternalIdResponse',
  'GetFoldersById' => 'SessionManagementStructGetFoldersById',
  'GetFoldersByIdResponse' => 'SessionManagementStructGetFoldersByIdResponse',
  'GetFoldersList' => 'SessionManagementStructGetFoldersList',
  'GetFoldersListResponse' => 'SessionManagementStructGetFoldersListResponse',
  'GetFoldersWithExternalContextByExternalId' => 'SessionManagementStructGetFoldersWithExternalContextByExternalId',
  'GetFoldersWithExternalContextByExternalIdResponse' => 'SessionManagementStructGetFoldersWithExternalContextByExternalIdResponse',
  'GetFoldersWithExternalContextById' => 'SessionManagementStructGetFoldersWithExternalContextById',
  'GetFoldersWithExternalContextByIdResponse' => 'SessionManagementStructGetFoldersWithExternalContextByIdResponse',
  'GetFoldersWithExternalContextList' => 'SessionManagementStructGetFoldersWithExternalContextList',
  'GetFoldersWithExternalContextListResponse' => 'SessionManagementStructGetFoldersWithExternalContextListResponse',
  'GetNote' => 'SessionManagementStructGetNote',
  'GetNoteResponse' => 'SessionManagementStructGetNoteResponse',
  'GetPersonalFolderForUser' => 'SessionManagementStructGetPersonalFolderForUser',
  'GetPersonalFolderForUserResponse' => 'SessionManagementStructGetPersonalFolderForUserResponse',
  'GetRecorderDownloadUrls' => 'SessionManagementStructGetRecorderDownloadUrls',
  'GetRecorderDownloadUrlsResponse' => 'SessionManagementStructGetRecorderDownloadUrlsResponse',
  'GetSessionsAvailabilitySettings' => 'SessionManagementStructGetSessionsAvailabilitySettings',
  'GetSessionsAvailabilitySettingsResponse' => 'SessionManagementStructGetSessionsAvailabilitySettingsResponse',
  'GetSessionsByExternalId' => 'SessionManagementStructGetSessionsByExternalId',
  'GetSessionsByExternalIdResponse' => 'SessionManagementStructGetSessionsByExternalIdResponse',
  'GetSessionsById' => 'SessionManagementStructGetSessionsById',
  'GetSessionsByIdResponse' => 'SessionManagementStructGetSessionsByIdResponse',
  'GetSessionsList' => 'SessionManagementStructGetSessionsList',
  'GetSessionsListResponse' => 'SessionManagementStructGetSessionsListResponse',
  'IsDropbox' => 'SessionManagementStructIsDropbox',
  'IsDropboxResponse' => 'SessionManagementStructIsDropboxResponse',
  'ListFoldersRequest' => 'SessionManagementStructListFoldersRequest',
  'ListFoldersResponse' => 'SessionManagementStructListFoldersResponse',
  'ListExtendedFoldersResponse' => 'SessionManagementStructListExtendedFoldersResponse',
  'ListFoldersResponseWithExternalContext' => 'SessionManagementStructListFoldersResponseWithExternalContext',
  'ListNotes' => 'SessionManagementStructListNotes',
  'ListNotesResponse' => 'SessionManagementStructListNotesResponse',
  'ListSessionsRequest' => 'SessionManagementStructListSessionsRequest',
  'ListSessionsResponse' => 'SessionManagementStructListSessionsResponse',
  'MoveSessions' => 'SessionManagementStructMoveSessions',
  'MoveSessionsResponse' => 'SessionManagementStructMoveSessionsResponse',
  'Note' => 'SessionManagementStructNote',
  'Pagination' => 'SessionManagementStructPagination',
  'ProvisionExternalCourse' => 'SessionManagementStructProvisionExternalCourse',
  'ProvisionExternalCourseResponse' => 'SessionManagementStructProvisionExternalCourseResponse',
  'ProvisionExternalCourseWithRoles' => 'SessionManagementStructProvisionExternalCourseWithRoles',
  'ProvisionExternalCourseWithRolesResponse' => 'SessionManagementStructProvisionExternalCourseWithRolesResponse',
  'UnprovisionExternalCourse' => 'SessionManagementStructUnprovisionExternalCourse',
  'UnprovisionExternalCourseResponse' => 'SessionManagementStructUnprovisionExternalCourseResponse',
  'RecorderDownloadUrlResponse' => 'SessionManagementStructRecorderDownloadUrlResponse',
  'Session' => 'SessionManagementStructSession',
  'SessionAvailabilitySettings' => 'SessionManagementStructSessionAvailabilitySettings',
  'SessionEndSettingType' => 'SessionManagementEnumSessionEndSettingType',
  'SessionSortField' => 'SessionManagementEnumSessionSortField',
  'SessionStartSettingType' => 'SessionManagementEnumSessionStartSettingType',
  'SessionState' => 'SessionManagementEnumSessionState',
  'SessionsWithAvailabilitySettings' => 'SessionManagementStructSessionsWithAvailabilitySettings',
  'SetCopiedExternalCourseAccess' => 'SessionManagementStructSetCopiedExternalCourseAccess',
  'SetCopiedExternalCourseAccessForRoles' => 'SessionManagementStructSetCopiedExternalCourseAccessForRoles',
  'SetCopiedExternalCourseAccessForRolesResponse' => 'SessionManagementStructSetCopiedExternalCourseAccessForRolesResponse',
  'SetCopiedExternalCourseAccessResponse' => 'SessionManagementStructSetCopiedExternalCourseAccessResponse',
  'SetExternalCourseAccess' => 'SessionManagementStructSetExternalCourseAccess',
  'SetExternalCourseAccessForRoles' => 'SessionManagementStructSetExternalCourseAccessForRoles',
  'SetExternalCourseAccessForRolesResponse' => 'SessionManagementStructSetExternalCourseAccessForRolesResponse',
  'SetExternalCourseAccessResponse' => 'SessionManagementStructSetExternalCourseAccessResponse',
  'SetNotesPublic' => 'SessionManagementStructSetNotesPublic',
  'SetNotesPublicResponse' => 'SessionManagementStructSetNotesPublicResponse',
  'UpdateFolderAllowPublicNotes' => 'SessionManagementStructUpdateFolderAllowPublicNotes',
  'UpdateFolderAllowPublicNotesResponse' => 'SessionManagementStructUpdateFolderAllowPublicNotesResponse',
  'UpdateFolderAllowSessionDownload' => 'SessionManagementStructUpdateFolderAllowSessionDownload',
  'UpdateFolderAllowSessionDownloadResponse' => 'SessionManagementStructUpdateFolderAllowSessionDownloadResponse',
  'UpdateFolderDescription' => 'SessionManagementStructUpdateFolderDescription',
  'UpdateFolderDescriptionResponse' => 'SessionManagementStructUpdateFolderDescriptionResponse',
  'UpdateFolderEnablePodcast' => 'SessionManagementStructUpdateFolderEnablePodcast',
  'UpdateFolderEnablePodcastResponse' => 'SessionManagementStructUpdateFolderEnablePodcastResponse',
  'UpdateFolderExternalId' => 'SessionManagementStructUpdateFolderExternalId',
  'UpdateFolderExternalIdResponse' => 'SessionManagementStructUpdateFolderExternalIdResponse',
  'UpdateFolderExternalIdWithProvider' => 'SessionManagementStructUpdateFolderExternalIdWithProvider',
  'UpdateFolderExternalIdWithProviderResponse' => 'SessionManagementStructUpdateFolderExternalIdWithProviderResponse',
  'UpdateFolderName' => 'SessionManagementStructUpdateFolderName',
  'UpdateFolderNameResponse' => 'SessionManagementStructUpdateFolderNameResponse',
  'UpdateFolderParent' => 'SessionManagementStructUpdateFolderParent',
  'UpdateFolderParentResponse' => 'SessionManagementStructUpdateFolderParentResponse',
  'UpdateFoldersAvailabilityEndSettings' => 'SessionManagementStructUpdateFoldersAvailabilityEndSettings',
  'UpdateFoldersAvailabilityEndSettingsResponse' => 'SessionManagementStructUpdateFoldersAvailabilityEndSettingsResponse',
  'UpdateFoldersAvailabilityStartSettings' => 'SessionManagementStructUpdateFoldersAvailabilityStartSettings',
  'UpdateFoldersAvailabilityStartSettingsResponse' => 'SessionManagementStructUpdateFoldersAvailabilityStartSettingsResponse',
  'UpdateSessionDescription' => 'SessionManagementStructUpdateSessionDescription',
  'UpdateSessionDescriptionResponse' => 'SessionManagementStructUpdateSessionDescriptionResponse',
  'UpdateSessionExternalId' => 'SessionManagementStructUpdateSessionExternalId',
  'UpdateSessionExternalIdResponse' => 'SessionManagementStructUpdateSessionExternalIdResponse',
  'UpdateSessionIsBroadcast' => 'SessionManagementStructUpdateSessionIsBroadcast',
  'UpdateSessionIsBroadcastResponse' => 'SessionManagementStructUpdateSessionIsBroadcastResponse',
  'UpdateSessionName' => 'SessionManagementStructUpdateSessionName',
  'UpdateSessionNameResponse' => 'SessionManagementStructUpdateSessionNameResponse',
  'UpdateSessionOwner' => 'SessionManagementStructUpdateSessionOwner',
  'UpdateSessionOwnerResponse' => 'SessionManagementStructUpdateSessionOwnerResponse',
  'UpdateSessionsAvailabilityEndSettings' => 'SessionManagementStructUpdateSessionsAvailabilityEndSettings',
  'UpdateSessionsAvailabilityEndSettingsResponse' => 'SessionManagementStructUpdateSessionsAvailabilityEndSettingsResponse',
  'UpdateSessionsAvailabilityStartSettings' => 'SessionManagementStructUpdateSessionsAvailabilityStartSettings',
  'UpdateSessionsAvailabilityStartSettingsResponse' => 'SessionManagementStructUpdateSessionsAvailabilityStartSettingsResponse',
  'UploadTranscript' => 'SessionManagementStructUploadTranscript',
  'UploadTranscriptResponse' => 'SessionManagementStructUploadTranscriptResponse',
);
    }
}
