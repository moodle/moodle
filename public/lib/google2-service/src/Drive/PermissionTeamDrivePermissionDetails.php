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

class PermissionTeamDrivePermissionDetails extends \Google\Model
{
  /**
   * Deprecated: Output only. Use `permissionDetails/inherited` instead.
   *
   * @deprecated
   * @var bool
   */
  public $inherited;
  /**
   * Deprecated: Output only. Use `permissionDetails/inheritedFrom` instead.
   *
   * @deprecated
   * @var string
   */
  public $inheritedFrom;
  /**
   * Deprecated: Output only. Use `permissionDetails/role` instead.
   *
   * @deprecated
   * @var string
   */
  public $role;
  /**
   * Deprecated: Output only. Use `permissionDetails/permissionType` instead.
   *
   * @deprecated
   * @var string
   */
  public $teamDrivePermissionType;

  /**
   * Deprecated: Output only. Use `permissionDetails/inherited` instead.
   *
   * @deprecated
   * @param bool $inherited
   */
  public function setInherited($inherited)
  {
    $this->inherited = $inherited;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getInherited()
  {
    return $this->inherited;
  }
  /**
   * Deprecated: Output only. Use `permissionDetails/inheritedFrom` instead.
   *
   * @deprecated
   * @param string $inheritedFrom
   */
  public function setInheritedFrom($inheritedFrom)
  {
    $this->inheritedFrom = $inheritedFrom;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getInheritedFrom()
  {
    return $this->inheritedFrom;
  }
  /**
   * Deprecated: Output only. Use `permissionDetails/role` instead.
   *
   * @deprecated
   * @param string $role
   */
  public function setRole($role)
  {
    $this->role = $role;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getRole()
  {
    return $this->role;
  }
  /**
   * Deprecated: Output only. Use `permissionDetails/permissionType` instead.
   *
   * @deprecated
   * @param string $teamDrivePermissionType
   */
  public function setTeamDrivePermissionType($teamDrivePermissionType)
  {
    $this->teamDrivePermissionType = $teamDrivePermissionType;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getTeamDrivePermissionType()
  {
    return $this->teamDrivePermissionType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PermissionTeamDrivePermissionDetails::class, 'Google_Service_Drive_PermissionTeamDrivePermissionDetails');
