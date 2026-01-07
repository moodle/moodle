<?php
/*
 * Copyright 2014 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 */

namespace Google\Service\Drive;

class Permission extends \Google\Collection
{
  protected $collection_key = 'teamDrivePermissionDetails';
  /**
   * Whether the permission allows the file to be discovered through search.
   * This is only applicable for permissions of type `domain` or `anyone`.
   *
   * @var bool
   */
  public $allowFileDiscovery;
  /**
   * Output only. Whether the account associated with this permission has been
   * deleted. This field only pertains to permissions of type `user` or `group`.
   *
   * @var bool
   */
  public $deleted;
  /**
   * Output only. The "pretty" name of the value of the permission. The
   * following is a list of examples for each type of permission: * `user` -
   * User's full name, as defined for their Google Account, such as "Dana A." *
   * `group` - Name of the Google Group, such as "The Company Administrators." *
   * `domain` - String domain name, such as "cymbalgroup.com." * `anyone` - No
   * `displayName` is present.
   *
   * @var string
   */
  public $displayName;
  /**
   * The domain to which this permission refers.
   *
   * @var string
   */
  public $domain;
  /**
   * The email address of the user or group to which this permission refers.
   *
   * @var string
   */
  public $emailAddress;
  /**
   * The time at which this permission will expire (RFC 3339 date-time).
   * Expiration times have the following restrictions: - They can only be set on
   * user and group permissions - The time must be in the future - The time
   * cannot be more than a year in the future
   *
   * @var string
   */
  public $expirationTime;
  /**
   * Output only. The ID of this permission. This is a unique identifier for the
   * grantee, and is published in the [User resource](https://developers.google.
   * com/workspace/drive/api/reference/rest/v3/User) as `permissionId`. IDs
   * should be treated as opaque values.
   *
   * @var string
   */
  public $id;
  /**
   * When `true`, only organizers, owners, and users with permissions added
   * directly on the item can access it.
   *
   * @var bool
   */
  public $inheritedPermissionsDisabled;
  /**
   * Output only. Identifies what kind of resource this is. Value: the fixed
   * string `"drive#permission"`.
   *
   * @var string
   */
  public $kind;
  /**
   * Whether the account associated with this permission is a pending owner.
   * Only populated for permissions of type `user` for files that aren't in a
   * shared drive.
   *
   * @var bool
   */
  public $pendingOwner;
  protected $permissionDetailsType = PermissionPermissionDetails::class;
  protected $permissionDetailsDataType = 'array';
  /**
   * Output only. A link to the user's profile photo, if available.
   *
   * @var string
   */
  public $photoLink;
  /**
   * The role granted by this permission. Supported values include: * `owner` *
   * `organizer` * `fileOrganizer` * `writer` * `commenter` * `reader` For more
   * information, see [Roles and
   * permissions](https://developers.google.com/workspace/drive/api/guides/ref-
   * roles).
   *
   * @var string
   */
  public $role;
  protected $teamDrivePermissionDetailsType = PermissionTeamDrivePermissionDetails::class;
  protected $teamDrivePermissionDetailsDataType = 'array';
  /**
   * The type of the grantee. Supported values include: * `user` * `group` *
   * `domain` * `anyone` When creating a permission, if `type` is `user` or
   * `group`, you must provide an `emailAddress` for the user or group. If
   * `type` is `domain`, you must provide a `domain`. If `type` is `anyone`, no
   * extra information is required.
   *
   * @var string
   */
  public $type;
  /**
   * Indicates the view for this permission. Only populated for permissions that
   * belong to a view. The only supported values are `published` and `metadata`:
   * * `published`: The permission's role is `publishedReader`. * `metadata`:
   * The item is only visible to the `metadata` view because the item has
   * limited access and the scope has at least read access to the parent. The
   * `metadata` view is only supported on folders. For more information, see
   * [Views](https://developers.google.com/workspace/drive/api/guides/ref-
   * roles#views).
   *
   * @var string
   */
  public $view;

  /**
   * Whether the permission allows the file to be discovered through search.
   * This is only applicable for permissions of type `domain` or `anyone`.
   *
   * @param bool $allowFileDiscovery
   */
  public function setAllowFileDiscovery($allowFileDiscovery)
  {
    $this->allowFileDiscovery = $allowFileDiscovery;
  }
  /**
   * @return bool
   */
  public function getAllowFileDiscovery()
  {
    return $this->allowFileDiscovery;
  }
  /**
   * Output only. Whether the account associated with this permission has been
   * deleted. This field only pertains to permissions of type `user` or `group`.
   *
   * @param bool $deleted
   */
  public function setDeleted($deleted)
  {
    $this->deleted = $deleted;
  }
  /**
   * @return bool
   */
  public function getDeleted()
  {
    return $this->deleted;
  }
  /**
   * Output only. The "pretty" name of the value of the permission. The
   * following is a list of examples for each type of permission: * `user` -
   * User's full name, as defined for their Google Account, such as "Dana A." *
   * `group` - Name of the Google Group, such as "The Company Administrators." *
   * `domain` - String domain name, such as "cymbalgroup.com." * `anyone` - No
   * `displayName` is present.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * The domain to which this permission refers.
   *
   * @param string $domain
   */
  public function setDomain($domain)
  {
    $this->domain = $domain;
  }
  /**
   * @return string
   */
  public function getDomain()
  {
    return $this->domain;
  }
  /**
   * The email address of the user or group to which this permission refers.
   *
   * @param string $emailAddress
   */
  public function setEmailAddress($emailAddress)
  {
    $this->emailAddress = $emailAddress;
  }
  /**
   * @return string
   */
  public function getEmailAddress()
  {
    return $this->emailAddress;
  }
  /**
   * The time at which this permission will expire (RFC 3339 date-time).
   * Expiration times have the following restrictions: - They can only be set on
   * user and group permissions - The time must be in the future - The time
   * cannot be more than a year in the future
   *
   * @param string $expirationTime
   */
  public function setExpirationTime($expirationTime)
  {
    $this->expirationTime = $expirationTime;
  }
  /**
   * @return string
   */
  public function getExpirationTime()
  {
    return $this->expirationTime;
  }
  /**
   * Output only. The ID of this permission. This is a unique identifier for the
   * grantee, and is published in the [User resource](https://developers.google.
   * com/workspace/drive/api/reference/rest/v3/User) as `permissionId`. IDs
   * should be treated as opaque values.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * When `true`, only organizers, owners, and users with permissions added
   * directly on the item can access it.
   *
   * @param bool $inheritedPermissionsDisabled
   */
  public function setInheritedPermissionsDisabled($inheritedPermissionsDisabled)
  {
    $this->inheritedPermissionsDisabled = $inheritedPermissionsDisabled;
  }
  /**
   * @return bool
   */
  public function getInheritedPermissionsDisabled()
  {
    return $this->inheritedPermissionsDisabled;
  }
  /**
   * Output only. Identifies what kind of resource this is. Value: the fixed
   * string `"drive#permission"`.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * Whether the account associated with this permission is a pending owner.
   * Only populated for permissions of type `user` for files that aren't in a
   * shared drive.
   *
   * @param bool $pendingOwner
   */
  public function setPendingOwner($pendingOwner)
  {
    $this->pendingOwner = $pendingOwner;
  }
  /**
   * @return bool
   */
  public function getPendingOwner()
  {
    return $this->pendingOwner;
  }
  /**
   * Output only. Details of whether the permissions on this item are inherited
   * or are directly on this item.
   *
   * @param PermissionPermissionDetails[] $permissionDetails
   */
  public function setPermissionDetails($permissionDetails)
  {
    $this->permissionDetails = $permissionDetails;
  }
  /**
   * @return PermissionPermissionDetails[]
   */
  public function getPermissionDetails()
  {
    return $this->permissionDetails;
  }
  /**
   * Output only. A link to the user's profile photo, if available.
   *
   * @param string $photoLink
   */
  public function setPhotoLink($photoLink)
  {
    $this->photoLink = $photoLink;
  }
  /**
   * @return string
   */
  public function getPhotoLink()
  {
    return $this->photoLink;
  }
  /**
   * The role granted by this permission. Supported values include: * `owner` *
   * `organizer` * `fileOrganizer` * `writer` * `commenter` * `reader` For more
   * information, see [Roles and
   * permissions](https://developers.google.com/workspace/drive/api/guides/ref-
   * roles).
   *
   * @param string $role
   */
  public function setRole($role)
  {
    $this->role = $role;
  }
  /**
   * @return string
   */
  public function getRole()
  {
    return $this->role;
  }
  /**
   * Output only. Deprecated: Output only. Use `permissionDetails` instead.
   *
   * @deprecated
   * @param PermissionTeamDrivePermissionDetails[] $teamDrivePermissionDetails
   */
  public function setTeamDrivePermissionDetails($teamDrivePermissionDetails)
  {
    $this->teamDrivePermissionDetails = $teamDrivePermissionDetails;
  }
  /**
   * @deprecated
   * @return PermissionTeamDrivePermissionDetails[]
   */
  public function getTeamDrivePermissionDetails()
  {
    return $this->teamDrivePermissionDetails;
  }
  /**
   * The type of the grantee. Supported values include: * `user` * `group` *
   * `domain` * `anyone` When creating a permission, if `type` is `user` or
   * `group`, you must provide an `emailAddress` for the user or group. If
   * `type` is `domain`, you must provide a `domain`. If `type` is `anyone`, no
   * extra information is required.
   *
   * @param string $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }
  /**
   * Indicates the view for this permission. Only populated for permissions that
   * belong to a view. The only supported values are `published` and `metadata`:
   * * `published`: The permission's role is `publishedReader`. * `metadata`:
   * The item is only visible to the `metadata` view because the item has
   * limited access and the scope has at least read access to the parent. The
   * `metadata` view is only supported on folders. For more information, see
   * [Views](https://developers.google.com/workspace/drive/api/guides/ref-
   * roles#views).
   *
   * @param string $view
   */
  public function setView($view)
  {
    $this->view = $view;
  }
  /**
   * @return string
   */
  public function getView()
  {
    return $this->view;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Permission::class, 'Google_Service_Drive_Permission');
