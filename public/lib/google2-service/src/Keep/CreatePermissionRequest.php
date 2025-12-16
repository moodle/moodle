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

namespace Google\Service\Keep;

class CreatePermissionRequest extends \Google\Model
{
  /**
   * Required. The parent note where this permission will be created. Format:
   * `notes/{note}`
   *
   * @var string
   */
  public $parent;
  protected $permissionType = Permission::class;
  protected $permissionDataType = '';

  /**
   * Required. The parent note where this permission will be created. Format:
   * `notes/{note}`
   *
   * @param string $parent
   */
  public function setParent($parent)
  {
    $this->parent = $parent;
  }
  /**
   * @return string
   */
  public function getParent()
  {
    return $this->parent;
  }
  /**
   * Required. The permission to create. One of Permission.email, User.email or
   * Group.email must be supplied.
   *
   * @param Permission $permission
   */
  public function setPermission(Permission $permission)
  {
    $this->permission = $permission;
  }
  /**
   * @return Permission
   */
  public function getPermission()
  {
    return $this->permission;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CreatePermissionRequest::class, 'Google_Service_Keep_CreatePermissionRequest');
