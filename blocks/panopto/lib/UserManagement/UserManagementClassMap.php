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
 * @package UserManagement
 * @author Panopto
 * @version 20150429-01
 * @date 2017-01-19
 */
/**
 * Class which returns the class map definition by the static method UserManagementClassMap::classMap()
 * @package UserManagement
 * @author Panopto
 * @version 20150429-01
 * @date 2017-01-19
 */
class UserManagementClassMap
{
    /**
     * This method returns the array containing the mapping between WSDL structs and generated classes
     * This array is sent to the SoapClient when calling the WS
     * @return array
     */
    final public static function classMap()
    {
        return array (
  'AddMembersToExternalGroup' => 'UserManagementStructAddMembersToExternalGroup',
  'AddMembersToExternalGroupResponse' => 'UserManagementStructAddMembersToExternalGroupResponse',
  'AddMembersToInternalGroup' => 'UserManagementStructAddMembersToInternalGroup',
  'AddMembersToInternalGroupResponse' => 'UserManagementStructAddMembersToInternalGroupResponse',
  'ArrayOfGroup' => 'UserManagementStructArrayOfGroup',
  'ArrayOfUser' => 'UserManagementStructArrayOfUser',
  'ArrayOfguid' => 'UserManagementStructArrayOfguid',
  'ArrayOfstring' => 'UserManagementStructArrayOfstring',
  'AuthenticationInfo' => 'UserManagementStructAuthenticationInfo',
  'CreateExternalGroup' => 'UserManagementStructCreateExternalGroup',
  'CreateExternalGroupResponse' => 'UserManagementStructCreateExternalGroupResponse',
  'CreateInternalGroup' => 'UserManagementStructCreateInternalGroup',
  'CreateInternalGroupResponse' => 'UserManagementStructCreateInternalGroupResponse',
  'CreateUser' => 'UserManagementStructCreateUser',
  'CreateUserResponse' => 'UserManagementStructCreateUserResponse',
  'CreateUsers' => 'UserManagementStructCreateUsers',
  'CreateUsersResponse' => 'UserManagementStructCreateUsersResponse',
  'DeleteGroup' => 'UserManagementStructDeleteGroup',
  'DeleteGroupResponse' => 'UserManagementStructDeleteGroupResponse',
  'DeleteUsers' => 'UserManagementStructDeleteUsers',
  'DeleteUsersResponse' => 'UserManagementStructDeleteUsersResponse',
  'GetGroup' => 'UserManagementStructGetGroup',
  'GetGroupIsPublic' => 'UserManagementStructGetGroupIsPublic',
  'GetGroupIsPublicResponse' => 'UserManagementStructGetGroupIsPublicResponse',
  'GetGroupResponse' => 'UserManagementStructGetGroupResponse',
  'GetGroupsByName' => 'UserManagementStructGetGroupsByName',
  'GetGroupsByNameResponse' => 'UserManagementStructGetGroupsByNameResponse',
  'GetUserByKey' => 'UserManagementStructGetUserByKey',
  'GetUserByKeyResponse' => 'UserManagementStructGetUserByKeyResponse',
  'GetUsers' => 'UserManagementStructGetUsers',
  'GetUsersInGroup' => 'UserManagementStructGetUsersInGroup',
  'GetUsersInGroupResponse' => 'UserManagementStructGetUsersInGroupResponse',
  'GetUsersResponse' => 'UserManagementStructGetUsersResponse',
  'Group' => 'UserManagementStructGroup',
  'GroupType' => 'UserManagementEnumGroupType',
  'ListGroups' => 'UserManagementStructListGroups',
  'ListGroupsResponse' => 'UserManagementStructListGroupsResponse',
  'ListUsers' => 'UserManagementStructListUsers',
  'ListUsersRequest' => 'UserManagementStructListUsersRequest',
  'ListUsersResponse' => 'UserManagementStructListUsersResponse',
  'Pagination' => 'UserManagementStructPagination',
  'RemoveMembersFromExternalGroup' => 'UserManagementStructRemoveMembersFromExternalGroup',
  'RemoveMembersFromExternalGroupResponse' => 'UserManagementStructRemoveMembersFromExternalGroupResponse',
  'RemoveMembersFromInternalGroup' => 'UserManagementStructRemoveMembersFromInternalGroup',
  'RemoveMembersFromInternalGroupResponse' => 'UserManagementStructRemoveMembersFromInternalGroupResponse',
  'ResetPassword' => 'UserManagementStructResetPassword',
  'ResetPasswordResponse' => 'UserManagementStructResetPasswordResponse',
  'SetGroupIsPublic' => 'UserManagementStructSetGroupIsPublic',
  'SetGroupIsPublicResponse' => 'UserManagementStructSetGroupIsPublicResponse',
  'SetSystemRole' => 'UserManagementStructSetSystemRole',
  'SetSystemRoleResponse' => 'UserManagementStructSetSystemRoleResponse',
  'SyncExternalUser' => 'UserManagementStructSyncExternalUser',
  'SyncExternalUserResponse' => 'UserManagementStructSyncExternalUserResponse',
  'SystemRole' => 'UserManagementEnumSystemRole',
  'UnlockAccount' => 'UserManagementStructUnlockAccount',
  'UnlockAccountResponse' => 'UserManagementStructUnlockAccountResponse',
  'UpdateContactInfo' => 'UserManagementStructUpdateContactInfo',
  'UpdateContactInfoResponse' => 'UserManagementStructUpdateContactInfoResponse',
  'UpdatePassword' => 'UserManagementStructUpdatePassword',
  'UpdatePasswordResponse' => 'UserManagementStructUpdatePasswordResponse',
  'UpdateUserBio' => 'UserManagementStructUpdateUserBio',
  'UpdateUserBioResponse' => 'UserManagementStructUpdateUserBioResponse',
  'User' => 'UserManagementStructUser',
  'UserSortField' => 'UserManagementEnumUserSortField',
);
    }
}
