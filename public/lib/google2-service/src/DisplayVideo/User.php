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

namespace Google\Service\DisplayVideo;

class User extends \Google\Collection
{
  protected $collection_key = 'assignedUserRoles';
  protected $assignedUserRolesType = AssignedUserRole::class;
  protected $assignedUserRolesDataType = 'array';
  /**
   * Required. The display name of the user. Must be UTF-8 encoded with a
   * maximum size of 240 bytes.
   *
   * @var string
   */
  public $displayName;
  /**
   * Required. Immutable. The email address used to identify the user.
   *
   * @var string
   */
  public $email;
  /**
   * Output only. The timestamp when the user last logged in DV360 UI.
   *
   * @var string
   */
  public $lastLoginTime;
  /**
   * Output only. The resource name of the user.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The unique ID of the user. Assigned by the system.
   *
   * @var string
   */
  public $userId;

  /**
   * The assigned user roles. Required in CreateUser. Output only in UpdateUser.
   * Can only be updated through BulkEditAssignedUserRoles.
   *
   * @param AssignedUserRole[] $assignedUserRoles
   */
  public function setAssignedUserRoles($assignedUserRoles)
  {
    $this->assignedUserRoles = $assignedUserRoles;
  }
  /**
   * @return AssignedUserRole[]
   */
  public function getAssignedUserRoles()
  {
    return $this->assignedUserRoles;
  }
  /**
   * Required. The display name of the user. Must be UTF-8 encoded with a
   * maximum size of 240 bytes.
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
   * Required. Immutable. The email address used to identify the user.
   *
   * @param string $email
   */
  public function setEmail($email)
  {
    $this->email = $email;
  }
  /**
   * @return string
   */
  public function getEmail()
  {
    return $this->email;
  }
  /**
   * Output only. The timestamp when the user last logged in DV360 UI.
   *
   * @param string $lastLoginTime
   */
  public function setLastLoginTime($lastLoginTime)
  {
    $this->lastLoginTime = $lastLoginTime;
  }
  /**
   * @return string
   */
  public function getLastLoginTime()
  {
    return $this->lastLoginTime;
  }
  /**
   * Output only. The resource name of the user.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Output only. The unique ID of the user. Assigned by the system.
   *
   * @param string $userId
   */
  public function setUserId($userId)
  {
    $this->userId = $userId;
  }
  /**
   * @return string
   */
  public function getUserId()
  {
    return $this->userId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(User::class, 'Google_Service_DisplayVideo_User');
