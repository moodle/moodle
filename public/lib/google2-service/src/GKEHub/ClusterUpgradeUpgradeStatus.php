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

namespace Google\Service\GKEHub;

class ClusterUpgradeUpgradeStatus extends \Google\Model
{
  /**
   * Required by https://linter.aip.dev/126/unspecified.
   */
  public const CODE_CODE_UNSPECIFIED = 'CODE_UNSPECIFIED';
  /**
   * The upgrade is ineligible. At the scope level, this means the upgrade is
   * ineligible for all the clusters in the scope.
   */
  public const CODE_INELIGIBLE = 'INELIGIBLE';
  /**
   * The upgrade is pending. At the scope level, this means the upgrade is
   * pending for all the clusters in the scope.
   */
  public const CODE_PENDING = 'PENDING';
  /**
   * The upgrade is in progress. At the scope level, this means the upgrade is
   * in progress for at least one cluster in the scope.
   */
  public const CODE_IN_PROGRESS = 'IN_PROGRESS';
  /**
   * The upgrade has finished and is soaking until the soaking time is up. At
   * the scope level, this means at least one cluster is in soaking while the
   * rest are either soaking or complete.
   */
  public const CODE_SOAKING = 'SOAKING';
  /**
   * A cluster will be forced to enter soaking if an upgrade doesn't finish
   * within a certain limit, despite it's actual status.
   */
  public const CODE_FORCED_SOAKING = 'FORCED_SOAKING';
  /**
   * The upgrade has passed all post conditions (soaking). At the scope level,
   * this means all eligible clusters are in COMPLETE status.
   */
  public const CODE_COMPLETE = 'COMPLETE';
  /**
   * Status code of the upgrade.
   *
   * @var string
   */
  public $code;
  /**
   * Reason for this status.
   *
   * @var string
   */
  public $reason;
  /**
   * Last timestamp the status was updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Status code of the upgrade.
   *
   * Accepted values: CODE_UNSPECIFIED, INELIGIBLE, PENDING, IN_PROGRESS,
   * SOAKING, FORCED_SOAKING, COMPLETE
   *
   * @param self::CODE_* $code
   */
  public function setCode($code)
  {
    $this->code = $code;
  }
  /**
   * @return self::CODE_*
   */
  public function getCode()
  {
    return $this->code;
  }
  /**
   * Reason for this status.
   *
   * @param string $reason
   */
  public function setReason($reason)
  {
    $this->reason = $reason;
  }
  /**
   * @return string
   */
  public function getReason()
  {
    return $this->reason;
  }
  /**
   * Last timestamp the status was updated.
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
class_alias(ClusterUpgradeUpgradeStatus::class, 'Google_Service_GKEHub_ClusterUpgradeUpgradeStatus');
