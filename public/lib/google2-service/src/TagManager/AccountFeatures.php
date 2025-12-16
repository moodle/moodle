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

namespace Google\Service\TagManager;

class AccountFeatures extends \Google\Model
{
  /**
   * Whether this Account supports multiple Containers.
   *
   * @var bool
   */
  public $supportMultipleContainers;
  /**
   * Whether this Account supports user permissions managed by GTM.
   *
   * @var bool
   */
  public $supportUserPermissions;

  /**
   * Whether this Account supports multiple Containers.
   *
   * @param bool $supportMultipleContainers
   */
  public function setSupportMultipleContainers($supportMultipleContainers)
  {
    $this->supportMultipleContainers = $supportMultipleContainers;
  }
  /**
   * @return bool
   */
  public function getSupportMultipleContainers()
  {
    return $this->supportMultipleContainers;
  }
  /**
   * Whether this Account supports user permissions managed by GTM.
   *
   * @param bool $supportUserPermissions
   */
  public function setSupportUserPermissions($supportUserPermissions)
  {
    $this->supportUserPermissions = $supportUserPermissions;
  }
  /**
   * @return bool
   */
  public function getSupportUserPermissions()
  {
    return $this->supportUserPermissions;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AccountFeatures::class, 'Google_Service_TagManager_AccountFeatures');
