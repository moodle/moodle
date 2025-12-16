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

namespace Google\Service\APIManagement;

class ObservationJob extends \Google\Collection
{
  /**
   * Unspecified state
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Job is in the creating state
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * Job is in the enabling state
   */
  public const STATE_ENABLING = 'ENABLING';
  /**
   * Job is enabled
   */
  public const STATE_ENABLED = 'ENABLED';
  /**
   * Job is in the disabling state
   */
  public const STATE_DISABLING = 'DISABLING';
  /**
   * Job is disabled
   */
  public const STATE_DISABLED = 'DISABLED';
  /**
   * Job is being deleted
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * Job is in an error state
   */
  public const STATE_ERROR = 'ERROR';
  protected $collection_key = 'sources';
  /**
   * Output only. [Output only] Create time stamp
   *
   * @var string
   */
  public $createTime;
  /**
   * Identifier. name of resource Format:
   * projects/{project}/locations/{location}/observationJobs/{observation_job}
   *
   * @var string
   */
  public $name;
  /**
   * Optional. These should be of the same kind of source.
   *
   * @var string[]
   */
  public $sources;
  /**
   * Output only. The observation job state
   *
   * @var string
   */
  public $state;
  /**
   * Output only. [Output only] Update time stamp
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. [Output only] Create time stamp
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Identifier. name of resource Format:
   * projects/{project}/locations/{location}/observationJobs/{observation_job}
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
   * Optional. These should be of the same kind of source.
   *
   * @param string[] $sources
   */
  public function setSources($sources)
  {
    $this->sources = $sources;
  }
  /**
   * @return string[]
   */
  public function getSources()
  {
    return $this->sources;
  }
  /**
   * Output only. The observation job state
   *
   * Accepted values: STATE_UNSPECIFIED, CREATING, ENABLING, ENABLED, DISABLING,
   * DISABLED, DELETING, ERROR
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
  /**
   * Output only. [Output only] Update time stamp
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ObservationJob::class, 'Google_Service_APIManagement_ObservationJob');
