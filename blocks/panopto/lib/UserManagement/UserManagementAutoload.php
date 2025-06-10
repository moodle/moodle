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
 * @package UserManagement
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
require_once dirname(__FILE__) . '/UserManagementWsdlClass.php';
require_once dirname(__FILE__) . '/Set/Response/UserManagementStructSetGroupIsPublicResponse.php';
require_once dirname(__FILE__) . '/Set/Public/UserManagementStructSetGroupIsPublic.php';
require_once dirname(__FILE__) . '/Get/Response/UserManagementStructGetGroupIsPublicResponse.php';
require_once dirname(__FILE__) . '/Create/Group/UserManagementStructCreateExternalGroup.php';
require_once dirname(__FILE__) . '/Create/Response/UserManagementStructCreateExternalGroupResponse.php';
require_once dirname(__FILE__) . '/Add/Response/UserManagementStructAddMembersToInternalGroupResponse.php';
require_once dirname(__FILE__) . '/Add/Group/UserManagementStructAddMembersToInternalGroup.php';
require_once dirname(__FILE__) . '/Get/Public/UserManagementStructGetGroupIsPublic.php';
require_once dirname(__FILE__) . '/Create/Response/UserManagementStructCreateInternalGroupResponse.php';
require_once dirname(__FILE__) . '/Set/Role/UserManagementStructSetSystemRole.php';
require_once dirname(__FILE__) . '/Unlock/Response/UserManagementStructUnlockAccountResponse.php';
require_once dirname(__FILE__) . '/Set/Response/UserManagementStructSetSystemRoleResponse.php';
require_once dirname(__FILE__) . '/Delete/Users/UserManagementStructDeleteUsers.php';
require_once dirname(__FILE__) . '/Create/Group/UserManagementStructCreateInternalGroup.php';
require_once dirname(__FILE__) . '/Delete/Response/UserManagementStructDeleteUsersResponse.php';
require_once dirname(__FILE__) . '/Remove/Group/UserManagementStructRemoveMembersFromInternalGroup.php';
require_once dirname(__FILE__) . '/Remove/Response/UserManagementStructRemoveMembersFromInternalGroupResponse.php';
require_once dirname(__FILE__) . '/List/Groups/UserManagementStructListGroups.php';
require_once dirname(__FILE__) . '/Get/Response/UserManagementStructGetGroupResponse.php';
require_once dirname(__FILE__) . '/Get/Group/UserManagementStructGetGroup.php';
require_once dirname(__FILE__) . '/Get/Name/UserManagementStructGetGroupsByName.php';
require_once dirname(__FILE__) . '/Get/Response/UserManagementStructGetGroupsByNameResponse.php';
require_once dirname(__FILE__) . '/Get/Response/UserManagementStructGetUsersInGroupResponse.php';
require_once dirname(__FILE__) . '/Get/Group/UserManagementStructGetUsersInGroup.php';
require_once dirname(__FILE__) . '/Delete/Response/UserManagementStructDeleteGroupResponse.php';
require_once dirname(__FILE__) . '/Delete/Group/UserManagementStructDeleteGroup.php';
require_once dirname(__FILE__) . '/Add/Response/UserManagementStructAddMembersToExternalGroupResponse.php';
require_once dirname(__FILE__) . '/Add/Group/UserManagementStructAddMembersToExternalGroup.php';
require_once dirname(__FILE__) . '/Remove/Group/UserManagementStructRemoveMembersFromExternalGroup.php';
require_once dirname(__FILE__) . '/Remove/Response/UserManagementStructRemoveMembersFromExternalGroupResponse.php';
require_once dirname(__FILE__) . '/Sync/Response/UserManagementStructSyncExternalUserResponse.php';
require_once dirname(__FILE__) . '/Sync/User/UserManagementStructSyncExternalUser.php';
require_once dirname(__FILE__) . '/Unlock/Account/UserManagementStructUnlockAccount.php';
require_once dirname(__FILE__) . '/Reset/Response/UserManagementStructResetPasswordResponse.php';
require_once dirname(__FILE__) . '/User/Field/UserManagementEnumUserSortField.php';
require_once dirname(__FILE__) . '/Pagination/UserManagementStructPagination.php';
require_once dirname(__FILE__) . '/List/Request/UserManagementStructListUsersRequest.php';
require_once dirname(__FILE__) . '/List/Response/UserManagementStructListUsersResponse.php';
require_once dirname(__FILE__) . '/Group/UserManagementStructGroup.php';
require_once dirname(__FILE__) . '/List/Response/UserManagementStructListGroupsResponse.php';
require_once dirname(__FILE__) . '/Group/Type/UserManagementEnumGroupType.php';
require_once dirname(__FILE__) . '/Array/User/UserManagementStructArrayOfUser.php';
require_once dirname(__FILE__) . '/System/Role/UserManagementEnumSystemRole.php';
require_once dirname(__FILE__) . '/Array/Ofguid/UserManagementStructArrayOfguid.php';
require_once dirname(__FILE__) . '/Array/Ofstring/UserManagementStructArrayOfstring.php';
require_once dirname(__FILE__) . '/User/UserManagementStructUser.php';
require_once dirname(__FILE__) . '/Authentication/Info/UserManagementStructAuthenticationInfo.php';
require_once dirname(__FILE__) . '/Array/Group/UserManagementStructArrayOfGroup.php';
require_once dirname(__FILE__) . '/Create/User/UserManagementStructCreateUser.php';
require_once dirname(__FILE__) . '/Update/Bio/UserManagementStructUpdateUserBio.php';
require_once dirname(__FILE__) . '/Update/Response/UserManagementStructUpdateContactInfoResponse.php';
require_once dirname(__FILE__) . '/Update/Info/UserManagementStructUpdateContactInfo.php';
require_once dirname(__FILE__) . '/Update/Response/UserManagementStructUpdateUserBioResponse.php';
require_once dirname(__FILE__) . '/Update/Password/UserManagementStructUpdatePassword.php';
require_once dirname(__FILE__) . '/Reset/Password/UserManagementStructResetPassword.php';
require_once dirname(__FILE__) . '/Update/Response/UserManagementStructUpdatePasswordResponse.php';
require_once dirname(__FILE__) . '/List/Users/UserManagementStructListUsers.php';
require_once dirname(__FILE__) . '/Get/Response/UserManagementStructGetUsersResponse.php';
require_once dirname(__FILE__) . '/Create/Users/UserManagementStructCreateUsers.php';
require_once dirname(__FILE__) . '/Create/Response/UserManagementStructCreateUserResponse.php';
require_once dirname(__FILE__) . '/Create/Response/UserManagementStructCreateUsersResponse.php';
require_once dirname(__FILE__) . '/Get/Key/UserManagementStructGetUserByKey.php';
require_once dirname(__FILE__) . '/Get/Users/UserManagementStructGetUsers.php';
require_once dirname(__FILE__) . '/Get/Response/UserManagementStructGetUserByKeyResponse.php';
require_once dirname(__FILE__) . '/Create/UserManagementServiceCreate.php';
require_once dirname(__FILE__) . '/Get/UserManagementServiceGet.php';
require_once dirname(__FILE__) . '/List/UserManagementServiceList.php';
require_once dirname(__FILE__) . '/Update/UserManagementServiceUpdate.php';
require_once dirname(__FILE__) . '/Reset/UserManagementServiceReset.php';
require_once dirname(__FILE__) . '/Unlock/UserManagementServiceUnlock.php';
require_once dirname(__FILE__) . '/Set/UserManagementServiceSet.php';
require_once dirname(__FILE__) . '/Delete/UserManagementServiceDelete.php';
require_once dirname(__FILE__) . '/Add/UserManagementServiceAdd.php';
require_once dirname(__FILE__) . '/Remove/UserManagementServiceRemove.php';
require_once dirname(__FILE__) . '/Sync/UserManagementServiceSync.php';
require_once dirname(__FILE__) . '/UserManagementClassMap.php';
