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

namespace Google\Service\DeploymentManager;

class QuotaExceededInfo extends \Google\Model
{
  /**
   * ROLLOUT_STATUS_UNSPECIFIED - Rollout status is not specified. The default
   * value.
   */
  public const ROLLOUT_STATUS_ROLLOUT_STATUS_UNSPECIFIED = 'ROLLOUT_STATUS_UNSPECIFIED';
  /**
   * IN_PROGRESS - A rollout is in process which will change the limit value to
   * future limit.
   */
  public const ROLLOUT_STATUS_IN_PROGRESS = 'IN_PROGRESS';
  /**
   * The map holding related quota dimensions.
   *
   * @var string[]
   */
  public $dimensions;
  /**
   * Future quota limit being rolled out. The limit's unit depends on the quota
   * type or metric.
   *
   * @var 
   */
  public $futureLimit;
  /**
   * Current effective quota limit. The limit's unit depends on the quota type
   * or metric.
   *
   * @var 
   */
  public $limit;
  /**
   * The name of the quota limit.
   *
   * @var string
   */
  public $limitName;
  /**
   * The Compute Engine quota metric name.
   *
   * @var string
   */
  public $metricName;
  /**
   * Rollout status of the future quota limit.
   *
   * @var string
   */
  public $rolloutStatus;

  /**
   * The map holding related quota dimensions.
   *
   * @param string[] $dimensions
   */
  public function setDimensions($dimensions)
  {
    $this->dimensions = $dimensions;
  }
  /**
   * @return string[]
   */
  public function getDimensions()
  {
    return $this->dimensions;
  }
  public function setFutureLimit($futureLimit)
  {
    $this->futureLimit = $futureLimit;
  }
  public function getFutureLimit()
  {
    return $this->futureLimit;
  }
  public function setLimit($limit)
  {
    $this->limit = $limit;
  }
  public function getLimit()
  {
    return $this->limit;
  }
  /**
   * The name of the quota limit.
   *
   * @param string $limitName
   */
  public function setLimitName($limitName)
  {
    $this->limitName = $limitName;
  }
  /**
   * @return string
   */
  public function getLimitName()
  {
    return $this->limitName;
  }
  /**
   * The Compute Engine quota metric name.
   *
   * @param string $metricName
   */
  public function setMetricName($metricName)
  {
    $this->metricName = $metricName;
  }
  /**
   * @return string
   */
  public function getMetricName()
  {
    return $this->metricName;
  }
  /**
   * Rollout status of the future quota limit.
   *
   * Accepted values: ROLLOUT_STATUS_UNSPECIFIED, IN_PROGRESS
   *
   * @param self::ROLLOUT_STATUS_* $rolloutStatus
   */
  public function setRolloutStatus($rolloutStatus)
  {
    $this->rolloutStatus = $rolloutStatus;
  }
  /**
   * @return self::ROLLOUT_STATUS_*
   */
  public function getRolloutStatus()
  {
    return $this->rolloutStatus;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(QuotaExceededInfo::class, 'Google_Service_DeploymentManager_QuotaExceededInfo');
