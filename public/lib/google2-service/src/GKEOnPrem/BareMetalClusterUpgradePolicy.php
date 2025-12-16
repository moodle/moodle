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

namespace Google\Service\GKEOnPrem;

class BareMetalClusterUpgradePolicy extends \Google\Model
{
  /**
   * No upgrade policy selected.
   */
  public const POLICY_NODE_POOL_POLICY_UNSPECIFIED = 'NODE_POOL_POLICY_UNSPECIFIED';
  /**
   * Upgrade worker node pools sequentially.
   */
  public const POLICY_SERIAL = 'SERIAL';
  /**
   * Upgrade all worker node pools in parallel.
   */
  public const POLICY_CONCURRENT = 'CONCURRENT';
  /**
   * Output only. Pause is used to show the upgrade pause status. It's view only
   * for now.
   *
   * @var bool
   */
  public $pause;
  /**
   * Specifies which upgrade policy to use.
   *
   * @var string
   */
  public $policy;

  /**
   * Output only. Pause is used to show the upgrade pause status. It's view only
   * for now.
   *
   * @param bool $pause
   */
  public function setPause($pause)
  {
    $this->pause = $pause;
  }
  /**
   * @return bool
   */
  public function getPause()
  {
    return $this->pause;
  }
  /**
   * Specifies which upgrade policy to use.
   *
   * Accepted values: NODE_POOL_POLICY_UNSPECIFIED, SERIAL, CONCURRENT
   *
   * @param self::POLICY_* $policy
   */
  public function setPolicy($policy)
  {
    $this->policy = $policy;
  }
  /**
   * @return self::POLICY_*
   */
  public function getPolicy()
  {
    return $this->policy;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BareMetalClusterUpgradePolicy::class, 'Google_Service_GKEOnPrem_BareMetalClusterUpgradePolicy');
