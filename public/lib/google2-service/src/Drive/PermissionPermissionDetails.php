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

class PermissionPermissionDetails extends \Google\Model
{
  /**
   * Output only. Whether this permission is inherited. This field is always
   * populated. This is an output-only field.
   *
   * @var bool
   */
  public $inherited;
  /**
   * Output only. The ID of the item from which this permission is inherited.
   * This is only populated for items in shared drives.
   *
   * @var string
   */
  public $inheritedFrom;
  /**
   * Output only. The permission type for this user. Supported values include: *
   * `file` * `member`
   *
   * @var string
   */
  public $permissionType;
  /**
   * Output only. The primary role for this user. Supported values include: *
   * `owner` * `organizer` * `fileOrganizer` * `writer` * `commenter` * `reader`
   * For more information, see [Roles and
   * permissions](https://developers.google.com/workspace/drive/api/guides/ref-
   * roles).
   *
   * @var string
   */
  public $role;

  /**
   * Output only. Whether this permission is inherited. This field is always
   * populated. This is an output-only field.
   *
   * @param bool $inherited
   */
  public function setInherited($inherited)
  {
    $this->inherited = $inherited;
  }
  /**
   * @return bool
   */
  public function getInherited()
  {
    return $this->inherited;
  }
  /**
   * Output only. The ID of the item from which this permission is inherited.
   * This is only populated for items in shared drives.
   *
   * @param string $inheritedFrom
   */
  public function setInheritedFrom($inheritedFrom)
  {
    $this->inheritedFrom = $inheritedFrom;
  }
  /**
   * @return string
   */
  public function getInheritedFrom()
  {
    return $this->inheritedFrom;
  }
  /**
   * Output only. The permission type for this user. Supported values include: *
   * `file` * `member`
   *
   * @param string $permissionType
   */
  public function setPermissionType($permissionType)
  {
    $this->permissionType = $permissionType;
  }
  /**
   * @return string
   */
  public function getPermissionType()
  {
    return $this->permissionType;
  }
  /**
   * Output only. The primary role for this user. Supported values include: *
   * `owner` * `organizer` * `fileOrganizer` * `writer` * `commenter` * `reader`
   * For more information, see [Roles and
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PermissionPermissionDetails::class, 'Google_Service_Drive_PermissionPermissionDetails');
