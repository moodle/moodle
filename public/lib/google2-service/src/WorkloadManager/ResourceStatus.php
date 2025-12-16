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

namespace Google\Service\WorkloadManager;

class ResourceStatus extends \Google\Collection
{
  /**
   * The state has not been populated in this message.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Resource has an active Create operation.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * Resource has no outstanding operations on it or has active Update
   * operations.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * Resource has an active Delete operation.
   */
  public const STATE_DELETING = 'DELETING';
  protected $collection_key = 'rulesNewerVersions';
  /**
   * Historical: Used before 2023-05-22 the new version of rule id if exists
   *
   * @deprecated
   * @var string[]
   */
  public $rulesNewerVersions;
  /**
   * State of the resource
   *
   * @var string
   */
  public $state;

  /**
   * Historical: Used before 2023-05-22 the new version of rule id if exists
   *
   * @deprecated
   * @param string[] $rulesNewerVersions
   */
  public function setRulesNewerVersions($rulesNewerVersions)
  {
    $this->rulesNewerVersions = $rulesNewerVersions;
  }
  /**
   * @deprecated
   * @return string[]
   */
  public function getRulesNewerVersions()
  {
    return $this->rulesNewerVersions;
  }
  /**
   * State of the resource
   *
   * Accepted values: STATE_UNSPECIFIED, CREATING, ACTIVE, DELETING
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
class_alias(ResourceStatus::class, 'Google_Service_WorkloadManager_ResourceStatus');
