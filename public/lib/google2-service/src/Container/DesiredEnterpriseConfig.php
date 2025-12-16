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

namespace Google\Service\Container;

class DesiredEnterpriseConfig extends \Google\Model
{
  /**
   * CLUSTER_TIER_UNSPECIFIED is when cluster_tier is not set.
   */
  public const DESIRED_TIER_CLUSTER_TIER_UNSPECIFIED = 'CLUSTER_TIER_UNSPECIFIED';
  /**
   * STANDARD indicates a standard GKE cluster.
   */
  public const DESIRED_TIER_STANDARD = 'STANDARD';
  /**
   * ENTERPRISE indicates a GKE Enterprise cluster.
   */
  public const DESIRED_TIER_ENTERPRISE = 'ENTERPRISE';
  /**
   * desired_tier specifies the desired tier of the cluster.
   *
   * @var string
   */
  public $desiredTier;

  /**
   * desired_tier specifies the desired tier of the cluster.
   *
   * Accepted values: CLUSTER_TIER_UNSPECIFIED, STANDARD, ENTERPRISE
   *
   * @param self::DESIRED_TIER_* $desiredTier
   */
  public function setDesiredTier($desiredTier)
  {
    $this->desiredTier = $desiredTier;
  }
  /**
   * @return self::DESIRED_TIER_*
   */
  public function getDesiredTier()
  {
    return $this->desiredTier;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DesiredEnterpriseConfig::class, 'Google_Service_Container_DesiredEnterpriseConfig');
