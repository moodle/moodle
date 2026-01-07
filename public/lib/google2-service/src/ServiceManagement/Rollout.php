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

namespace Google\Service\ServiceManagement;

class Rollout extends \Google\Model
{
  /**
   * No status specified.
   */
  public const STATUS_ROLLOUT_STATUS_UNSPECIFIED = 'ROLLOUT_STATUS_UNSPECIFIED';
  /**
   * The Rollout is in progress.
   */
  public const STATUS_IN_PROGRESS = 'IN_PROGRESS';
  /**
   * The Rollout has completed successfully.
   */
  public const STATUS_SUCCESS = 'SUCCESS';
  /**
   * The Rollout has been cancelled. This can happen if you have overlapping
   * Rollout pushes, and the previous ones will be cancelled.
   */
  public const STATUS_CANCELLED = 'CANCELLED';
  /**
   * The Rollout has failed and the rollback attempt has failed too.
   */
  public const STATUS_FAILED = 'FAILED';
  /**
   * The Rollout has not started yet and is pending for execution.
   */
  public const STATUS_PENDING = 'PENDING';
  /**
   * The Rollout has failed and rolled back to the previous successful Rollout.
   */
  public const STATUS_FAILED_ROLLED_BACK = 'FAILED_ROLLED_BACK';
  /**
   * Creation time of the rollout. Readonly.
   *
   * @var string
   */
  public $createTime;
  /**
   * The user who created the Rollout. Readonly.
   *
   * @var string
   */
  public $createdBy;
  protected $deleteServiceStrategyType = DeleteServiceStrategy::class;
  protected $deleteServiceStrategyDataType = '';
  /**
   * Optional. Unique identifier of this Rollout. Must be no longer than 63
   * characters and only lower case letters, digits, '.', '_' and '-' are
   * allowed. If not specified by client, the server will generate one. The
   * generated id will have the form of , where "date" is the create date in ISO
   * 8601 format. "revision number" is a monotonically increasing positive
   * number that is reset every day for each service. An example of the
   * generated rollout_id is '2016-02-16r1'
   *
   * @var string
   */
  public $rolloutId;
  /**
   * The name of the service associated with this Rollout.
   *
   * @var string
   */
  public $serviceName;
  /**
   * The status of this rollout. Readonly. In case of a failed rollout, the
   * system will automatically rollback to the current Rollout version.
   * Readonly.
   *
   * @var string
   */
  public $status;
  protected $trafficPercentStrategyType = TrafficPercentStrategy::class;
  protected $trafficPercentStrategyDataType = '';

  /**
   * Creation time of the rollout. Readonly.
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
   * The user who created the Rollout. Readonly.
   *
   * @param string $createdBy
   */
  public function setCreatedBy($createdBy)
  {
    $this->createdBy = $createdBy;
  }
  /**
   * @return string
   */
  public function getCreatedBy()
  {
    return $this->createdBy;
  }
  /**
   * The strategy associated with a rollout to delete a `ManagedService`.
   * Readonly.
   *
   * @param DeleteServiceStrategy $deleteServiceStrategy
   */
  public function setDeleteServiceStrategy(DeleteServiceStrategy $deleteServiceStrategy)
  {
    $this->deleteServiceStrategy = $deleteServiceStrategy;
  }
  /**
   * @return DeleteServiceStrategy
   */
  public function getDeleteServiceStrategy()
  {
    return $this->deleteServiceStrategy;
  }
  /**
   * Optional. Unique identifier of this Rollout. Must be no longer than 63
   * characters and only lower case letters, digits, '.', '_' and '-' are
   * allowed. If not specified by client, the server will generate one. The
   * generated id will have the form of , where "date" is the create date in ISO
   * 8601 format. "revision number" is a monotonically increasing positive
   * number that is reset every day for each service. An example of the
   * generated rollout_id is '2016-02-16r1'
   *
   * @param string $rolloutId
   */
  public function setRolloutId($rolloutId)
  {
    $this->rolloutId = $rolloutId;
  }
  /**
   * @return string
   */
  public function getRolloutId()
  {
    return $this->rolloutId;
  }
  /**
   * The name of the service associated with this Rollout.
   *
   * @param string $serviceName
   */
  public function setServiceName($serviceName)
  {
    $this->serviceName = $serviceName;
  }
  /**
   * @return string
   */
  public function getServiceName()
  {
    return $this->serviceName;
  }
  /**
   * The status of this rollout. Readonly. In case of a failed rollout, the
   * system will automatically rollback to the current Rollout version.
   * Readonly.
   *
   * Accepted values: ROLLOUT_STATUS_UNSPECIFIED, IN_PROGRESS, SUCCESS,
   * CANCELLED, FAILED, PENDING, FAILED_ROLLED_BACK
   *
   * @param self::STATUS_* $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return self::STATUS_*
   */
  public function getStatus()
  {
    return $this->status;
  }
  /**
   * Google Service Control selects service configurations based on traffic
   * percentage.
   *
   * @param TrafficPercentStrategy $trafficPercentStrategy
   */
  public function setTrafficPercentStrategy(TrafficPercentStrategy $trafficPercentStrategy)
  {
    $this->trafficPercentStrategy = $trafficPercentStrategy;
  }
  /**
   * @return TrafficPercentStrategy
   */
  public function getTrafficPercentStrategy()
  {
    return $this->trafficPercentStrategy;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Rollout::class, 'Google_Service_ServiceManagement_Rollout');
