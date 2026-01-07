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

namespace Google\Service\Datalineage;

class GoogleCloudDatacatalogLineageV1Run extends \Google\Model
{
  /**
   * The state is unknown. The true state may be any of the below or a different
   * state that is not supported here explicitly.
   */
  public const STATE_UNKNOWN = 'UNKNOWN';
  /**
   * The run is still executing.
   */
  public const STATE_STARTED = 'STARTED';
  /**
   * The run completed.
   */
  public const STATE_COMPLETED = 'COMPLETED';
  /**
   * The run failed.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * The run aborted.
   */
  public const STATE_ABORTED = 'ABORTED';
  /**
   * Optional. The attributes of the run. Should only be used for the purpose of
   * non-semantic management (classifying, describing or labeling the run). Up
   * to 100 attributes are allowed.
   *
   * @var array[]
   */
  public $attributes;
  /**
   * @var string
   */
  public $displayName;
  /**
   * Optional. The timestamp of the end of the run.
   *
   * @var string
   */
  public $endTime;
  /**
   * Immutable. The resource name of the run. Format:
   * `projects/{project}/locations/{location}/processes/{process}/runs/{run}`.
   * Can be specified or auto-assigned. {run} must be not longer than 200
   * characters and only contain characters in a set: `a-zA-Z0-9_-:.`
   *
   * @var string
   */
  public $name;
  /**
   * Required. The timestamp of the start of the run.
   *
   * @var string
   */
  public $startTime;
  /**
   * Required. The state of the run.
   *
   * @var string
   */
  public $state;

  /**
   * Optional. The attributes of the run. Should only be used for the purpose of
   * non-semantic management (classifying, describing or labeling the run). Up
   * to 100 attributes are allowed.
   *
   * @param array[] $attributes
   */
  public function setAttributes($attributes)
  {
    $this->attributes = $attributes;
  }
  /**
   * @return array[]
   */
  public function getAttributes()
  {
    return $this->attributes;
  }
  /**
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
   * Optional. The timestamp of the end of the run.
   *
   * @param string $endTime
   */
  public function setEndTime($endTime)
  {
    $this->endTime = $endTime;
  }
  /**
   * @return string
   */
  public function getEndTime()
  {
    return $this->endTime;
  }
  /**
   * Immutable. The resource name of the run. Format:
   * `projects/{project}/locations/{location}/processes/{process}/runs/{run}`.
   * Can be specified or auto-assigned. {run} must be not longer than 200
   * characters and only contain characters in a set: `a-zA-Z0-9_-:.`
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
   * Required. The timestamp of the start of the run.
   *
   * @param string $startTime
   */
  public function setStartTime($startTime)
  {
    $this->startTime = $startTime;
  }
  /**
   * @return string
   */
  public function getStartTime()
  {
    return $this->startTime;
  }
  /**
   * Required. The state of the run.
   *
   * Accepted values: UNKNOWN, STARTED, COMPLETED, FAILED, ABORTED
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
class_alias(GoogleCloudDatacatalogLineageV1Run::class, 'Google_Service_Datalineage_GoogleCloudDatacatalogLineageV1Run');
