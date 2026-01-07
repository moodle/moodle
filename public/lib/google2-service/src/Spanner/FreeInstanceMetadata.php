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

namespace Google\Service\Spanner;

class FreeInstanceMetadata extends \Google\Model
{
  /**
   * Not specified.
   */
  public const EXPIRE_BEHAVIOR_EXPIRE_BEHAVIOR_UNSPECIFIED = 'EXPIRE_BEHAVIOR_UNSPECIFIED';
  /**
   * When the free instance expires, upgrade the instance to a provisioned
   * instance.
   */
  public const EXPIRE_BEHAVIOR_FREE_TO_PROVISIONED = 'FREE_TO_PROVISIONED';
  /**
   * When the free instance expires, disable the instance, and delete it after
   * the grace period passes if it has not been upgraded.
   */
  public const EXPIRE_BEHAVIOR_REMOVE_AFTER_GRACE_PERIOD = 'REMOVE_AFTER_GRACE_PERIOD';
  /**
   * Specifies the expiration behavior of a free instance. The default of
   * ExpireBehavior is `REMOVE_AFTER_GRACE_PERIOD`. This can be modified during
   * or after creation, and before expiration.
   *
   * @var string
   */
  public $expireBehavior;
  /**
   * Output only. Timestamp after which the instance will either be upgraded or
   * scheduled for deletion after a grace period. ExpireBehavior is used to
   * choose between upgrading or scheduling the free instance for deletion. This
   * timestamp is set during the creation of a free instance.
   *
   * @var string
   */
  public $expireTime;
  /**
   * Output only. If present, the timestamp at which the free instance was
   * upgraded to a provisioned instance.
   *
   * @var string
   */
  public $upgradeTime;

  /**
   * Specifies the expiration behavior of a free instance. The default of
   * ExpireBehavior is `REMOVE_AFTER_GRACE_PERIOD`. This can be modified during
   * or after creation, and before expiration.
   *
   * Accepted values: EXPIRE_BEHAVIOR_UNSPECIFIED, FREE_TO_PROVISIONED,
   * REMOVE_AFTER_GRACE_PERIOD
   *
   * @param self::EXPIRE_BEHAVIOR_* $expireBehavior
   */
  public function setExpireBehavior($expireBehavior)
  {
    $this->expireBehavior = $expireBehavior;
  }
  /**
   * @return self::EXPIRE_BEHAVIOR_*
   */
  public function getExpireBehavior()
  {
    return $this->expireBehavior;
  }
  /**
   * Output only. Timestamp after which the instance will either be upgraded or
   * scheduled for deletion after a grace period. ExpireBehavior is used to
   * choose between upgrading or scheduling the free instance for deletion. This
   * timestamp is set during the creation of a free instance.
   *
   * @param string $expireTime
   */
  public function setExpireTime($expireTime)
  {
    $this->expireTime = $expireTime;
  }
  /**
   * @return string
   */
  public function getExpireTime()
  {
    return $this->expireTime;
  }
  /**
   * Output only. If present, the timestamp at which the free instance was
   * upgraded to a provisioned instance.
   *
   * @param string $upgradeTime
   */
  public function setUpgradeTime($upgradeTime)
  {
    $this->upgradeTime = $upgradeTime;
  }
  /**
   * @return string
   */
  public function getUpgradeTime()
  {
    return $this->upgradeTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FreeInstanceMetadata::class, 'Google_Service_Spanner_FreeInstanceMetadata');
