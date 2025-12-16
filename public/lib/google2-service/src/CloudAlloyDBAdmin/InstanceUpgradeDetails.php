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

namespace Google\Service\CloudAlloyDBAdmin;

class InstanceUpgradeDetails extends \Google\Model
{
  /**
   * The type of the instance is unknown.
   */
  public const INSTANCE_TYPE_INSTANCE_TYPE_UNSPECIFIED = 'INSTANCE_TYPE_UNSPECIFIED';
  /**
   * PRIMARY instances support read and write operations.
   */
  public const INSTANCE_TYPE_PRIMARY = 'PRIMARY';
  /**
   * READ POOL instances support read operations only. Each read pool instance
   * consists of one or more homogeneous nodes. * Read pool of size 1 can only
   * have zonal availability. * Read pools with node count of 2 or more can have
   * regional availability (nodes are present in 2 or more zones in a region).
   */
  public const INSTANCE_TYPE_READ_POOL = 'READ_POOL';
  /**
   * SECONDARY instances support read operations only. SECONDARY instance is a
   * cross-region read replica
   */
  public const INSTANCE_TYPE_SECONDARY = 'SECONDARY';
  /**
   * Unspecified status.
   */
  public const UPGRADE_STATUS_STATUS_UNSPECIFIED = 'STATUS_UNSPECIFIED';
  /**
   * Not started.
   */
  public const UPGRADE_STATUS_NOT_STARTED = 'NOT_STARTED';
  /**
   * In progress.
   */
  public const UPGRADE_STATUS_IN_PROGRESS = 'IN_PROGRESS';
  /**
   * Operation succeeded.
   */
  public const UPGRADE_STATUS_SUCCESS = 'SUCCESS';
  /**
   * Operation failed.
   */
  public const UPGRADE_STATUS_FAILED = 'FAILED';
  /**
   * Operation partially succeeded.
   */
  public const UPGRADE_STATUS_PARTIAL_SUCCESS = 'PARTIAL_SUCCESS';
  /**
   * Cancel is in progress.
   */
  public const UPGRADE_STATUS_CANCEL_IN_PROGRESS = 'CANCEL_IN_PROGRESS';
  /**
   * Cancellation complete.
   */
  public const UPGRADE_STATUS_CANCELLED = 'CANCELLED';
  /**
   * Instance type.
   *
   * @var string
   */
  public $instanceType;
  /**
   * Normalized name of the instance.
   *
   * @var string
   */
  public $name;
  /**
   * Upgrade status of the instance.
   *
   * @var string
   */
  public $upgradeStatus;

  /**
   * Instance type.
   *
   * Accepted values: INSTANCE_TYPE_UNSPECIFIED, PRIMARY, READ_POOL, SECONDARY
   *
   * @param self::INSTANCE_TYPE_* $instanceType
   */
  public function setInstanceType($instanceType)
  {
    $this->instanceType = $instanceType;
  }
  /**
   * @return self::INSTANCE_TYPE_*
   */
  public function getInstanceType()
  {
    return $this->instanceType;
  }
  /**
   * Normalized name of the instance.
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
   * Upgrade status of the instance.
   *
   * Accepted values: STATUS_UNSPECIFIED, NOT_STARTED, IN_PROGRESS, SUCCESS,
   * FAILED, PARTIAL_SUCCESS, CANCEL_IN_PROGRESS, CANCELLED
   *
   * @param self::UPGRADE_STATUS_* $upgradeStatus
   */
  public function setUpgradeStatus($upgradeStatus)
  {
    $this->upgradeStatus = $upgradeStatus;
  }
  /**
   * @return self::UPGRADE_STATUS_*
   */
  public function getUpgradeStatus()
  {
    return $this->upgradeStatus;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InstanceUpgradeDetails::class, 'Google_Service_CloudAlloyDBAdmin_InstanceUpgradeDetails');
