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

namespace Google\Service\Analytics;

class EntityUserLinkPermissions extends \Google\Collection
{
  protected $collection_key = 'local';
  /**
   * Effective permissions represent all the permissions that a user has for
   * this entity. These include any implied permissions (e.g., EDIT implies
   * VIEW) or inherited permissions from the parent entity. Effective
   * permissions are read-only.
   *
   * @var string[]
   */
  public $effective;
  /**
   * Permissions that a user has been assigned at this very level. Does not
   * include any implied or inherited permissions. Local permissions are
   * modifiable.
   *
   * @var string[]
   */
  public $local;

  /**
   * Effective permissions represent all the permissions that a user has for
   * this entity. These include any implied permissions (e.g., EDIT implies
   * VIEW) or inherited permissions from the parent entity. Effective
   * permissions are read-only.
   *
   * @param string[] $effective
   */
  public function setEffective($effective)
  {
    $this->effective = $effective;
  }
  /**
   * @return string[]
   */
  public function getEffective()
  {
    return $this->effective;
  }
  /**
   * Permissions that a user has been assigned at this very level. Does not
   * include any implied or inherited permissions. Local permissions are
   * modifiable.
   *
   * @param string[] $local
   */
  public function setLocal($local)
  {
    $this->local = $local;
  }
  /**
   * @return string[]
   */
  public function getLocal()
  {
    return $this->local;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EntityUserLinkPermissions::class, 'Google_Service_Analytics_EntityUserLinkPermissions');
