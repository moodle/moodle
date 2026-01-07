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

namespace Google\Service\Compute;

class ProjectsSetDefaultNetworkTierRequest extends \Google\Model
{
  /**
   * Public internet quality with fixed bandwidth.
   */
  public const NETWORK_TIER_FIXED_STANDARD = 'FIXED_STANDARD';
  /**
   * High quality, Google-grade network tier, support for all networking
   * products.
   */
  public const NETWORK_TIER_PREMIUM = 'PREMIUM';
  /**
   * Public internet quality, only limited support for other networking
   * products.
   */
  public const NETWORK_TIER_STANDARD = 'STANDARD';
  /**
   * (Output only) Temporary tier for FIXED_STANDARD when fixed standard tier is
   * expired or not configured.
   */
  public const NETWORK_TIER_STANDARD_OVERRIDES_FIXED_STANDARD = 'STANDARD_OVERRIDES_FIXED_STANDARD';
  /**
   * Default network tier to be set.
   *
   * @var string
   */
  public $networkTier;

  /**
   * Default network tier to be set.
   *
   * Accepted values: FIXED_STANDARD, PREMIUM, STANDARD,
   * STANDARD_OVERRIDES_FIXED_STANDARD
   *
   * @param self::NETWORK_TIER_* $networkTier
   */
  public function setNetworkTier($networkTier)
  {
    $this->networkTier = $networkTier;
  }
  /**
   * @return self::NETWORK_TIER_*
   */
  public function getNetworkTier()
  {
    return $this->networkTier;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsSetDefaultNetworkTierRequest::class, 'Google_Service_Compute_ProjectsSetDefaultNetworkTierRequest');
