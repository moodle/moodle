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

namespace Google\Service\ServiceConsumerManagement;

class PolicyBinding extends \Google\Collection
{
  protected $collection_key = 'members';
  /**
   * Uses the same format as in IAM policy. `member` must include both a prefix
   * and ID. For example, `user:{emailId}`, `serviceAccount:{emailId}`,
   * `group:{emailId}`.
   *
   * @var string[]
   */
  public $members;
  /**
   * Role. (https://cloud.google.com/iam/docs/understanding-roles) For example,
   * `roles/viewer`, `roles/editor`, or `roles/owner`.
   *
   * @var string
   */
  public $role;

  /**
   * Uses the same format as in IAM policy. `member` must include both a prefix
   * and ID. For example, `user:{emailId}`, `serviceAccount:{emailId}`,
   * `group:{emailId}`.
   *
   * @param string[] $members
   */
  public function setMembers($members)
  {
    $this->members = $members;
  }
  /**
   * @return string[]
   */
  public function getMembers()
  {
    return $this->members;
  }
  /**
   * Role. (https://cloud.google.com/iam/docs/understanding-roles) For example,
   * `roles/viewer`, `roles/editor`, or `roles/owner`.
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
class_alias(PolicyBinding::class, 'Google_Service_ServiceConsumerManagement_PolicyBinding');
