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

namespace Google\Service\HangoutsChat;

class PermissionSetting extends \Google\Model
{
  /**
   * Optional. Whether space managers `ROLE_ASSISTANT_MANAGER`) have this
   * permission.
   *
   * @var bool
   */
  public $assistantManagersAllowed;
  /**
   * Optional. Whether space owners (`ROLE_MANAGER`) have this permission.
   *
   * @var bool
   */
  public $managersAllowed;
  /**
   * Optional. Whether basic space members (`ROLE_MEMBER`) have this permission.
   *
   * @var bool
   */
  public $membersAllowed;

  /**
   * Optional. Whether space managers `ROLE_ASSISTANT_MANAGER`) have this
   * permission.
   *
   * @param bool $assistantManagersAllowed
   */
  public function setAssistantManagersAllowed($assistantManagersAllowed)
  {
    $this->assistantManagersAllowed = $assistantManagersAllowed;
  }
  /**
   * @return bool
   */
  public function getAssistantManagersAllowed()
  {
    return $this->assistantManagersAllowed;
  }
  /**
   * Optional. Whether space owners (`ROLE_MANAGER`) have this permission.
   *
   * @param bool $managersAllowed
   */
  public function setManagersAllowed($managersAllowed)
  {
    $this->managersAllowed = $managersAllowed;
  }
  /**
   * @return bool
   */
  public function getManagersAllowed()
  {
    return $this->managersAllowed;
  }
  /**
   * Optional. Whether basic space members (`ROLE_MEMBER`) have this permission.
   *
   * @param bool $membersAllowed
   */
  public function setMembersAllowed($membersAllowed)
  {
    $this->membersAllowed = $membersAllowed;
  }
  /**
   * @return bool
   */
  public function getMembersAllowed()
  {
    return $this->membersAllowed;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PermissionSetting::class, 'Google_Service_HangoutsChat_PermissionSetting');
