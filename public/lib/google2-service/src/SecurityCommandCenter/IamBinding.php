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

namespace Google\Service\SecurityCommandCenter;

class IamBinding extends \Google\Model
{
  /**
   * Unspecified.
   */
  public const ACTION_ACTION_UNSPECIFIED = 'ACTION_UNSPECIFIED';
  /**
   * Addition of a Binding.
   */
  public const ACTION_ADD = 'ADD';
  /**
   * Removal of a Binding.
   */
  public const ACTION_REMOVE = 'REMOVE';
  /**
   * The action that was performed on a Binding.
   *
   * @var string
   */
  public $action;
  /**
   * A single identity requesting access for a Cloud Platform resource, for
   * example, "foo@google.com".
   *
   * @var string
   */
  public $member;
  /**
   * Role that is assigned to "members". For example, "roles/viewer",
   * "roles/editor", or "roles/owner".
   *
   * @var string
   */
  public $role;

  /**
   * The action that was performed on a Binding.
   *
   * Accepted values: ACTION_UNSPECIFIED, ADD, REMOVE
   *
   * @param self::ACTION_* $action
   */
  public function setAction($action)
  {
    $this->action = $action;
  }
  /**
   * @return self::ACTION_*
   */
  public function getAction()
  {
    return $this->action;
  }
  /**
   * A single identity requesting access for a Cloud Platform resource, for
   * example, "foo@google.com".
   *
   * @param string $member
   */
  public function setMember($member)
  {
    $this->member = $member;
  }
  /**
   * @return string
   */
  public function getMember()
  {
    return $this->member;
  }
  /**
   * Role that is assigned to "members". For example, "roles/viewer",
   * "roles/editor", or "roles/owner".
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
class_alias(IamBinding::class, 'Google_Service_SecurityCommandCenter_IamBinding');
