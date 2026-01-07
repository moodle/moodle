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

namespace Google\Service\Apigee;

class GoogleCloudApigeeV1EnvironmentGroup extends \Google\Collection
{
  /**
   * Resource is in an unspecified state.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Resource is being created.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * Resource is provisioned and ready to use.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * The resource is being deleted.
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * The resource is being updated.
   */
  public const STATE_UPDATING = 'UPDATING';
  protected $collection_key = 'hostnames';
  /**
   * Output only. The time at which the environment group was created as
   * milliseconds since epoch.
   *
   * @var string
   */
  public $createdAt;
  /**
   * Required. Host names for this environment group.
   *
   * @var string[]
   */
  public $hostnames;
  /**
   * Output only. The time at which the environment group was last updated as
   * milliseconds since epoch.
   *
   * @var string
   */
  public $lastModifiedAt;
  /**
   * ID of the environment group.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. State of the environment group. Values other than ACTIVE means
   * the resource is not ready to use.
   *
   * @var string
   */
  public $state;

  /**
   * Output only. The time at which the environment group was created as
   * milliseconds since epoch.
   *
   * @param string $createdAt
   */
  public function setCreatedAt($createdAt)
  {
    $this->createdAt = $createdAt;
  }
  /**
   * @return string
   */
  public function getCreatedAt()
  {
    return $this->createdAt;
  }
  /**
   * Required. Host names for this environment group.
   *
   * @param string[] $hostnames
   */
  public function setHostnames($hostnames)
  {
    $this->hostnames = $hostnames;
  }
  /**
   * @return string[]
   */
  public function getHostnames()
  {
    return $this->hostnames;
  }
  /**
   * Output only. The time at which the environment group was last updated as
   * milliseconds since epoch.
   *
   * @param string $lastModifiedAt
   */
  public function setLastModifiedAt($lastModifiedAt)
  {
    $this->lastModifiedAt = $lastModifiedAt;
  }
  /**
   * @return string
   */
  public function getLastModifiedAt()
  {
    return $this->lastModifiedAt;
  }
  /**
   * ID of the environment group.
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
   * Output only. State of the environment group. Values other than ACTIVE means
   * the resource is not ready to use.
   *
   * Accepted values: STATE_UNSPECIFIED, CREATING, ACTIVE, DELETING, UPDATING
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1EnvironmentGroup::class, 'Google_Service_Apigee_GoogleCloudApigeeV1EnvironmentGroup');
