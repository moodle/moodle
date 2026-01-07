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

class ProjectsSetCloudArmorTierRequest extends \Google\Model
{
  /**
   * Enterprise tier protection billed annually.
   */
  public const CLOUD_ARMOR_TIER_CA_ENTERPRISE_ANNUAL = 'CA_ENTERPRISE_ANNUAL';
  /**
   * Enterprise tier protection billed monthly.
   */
  public const CLOUD_ARMOR_TIER_CA_ENTERPRISE_PAYGO = 'CA_ENTERPRISE_PAYGO';
  /**
   * Standard protection.
   */
  public const CLOUD_ARMOR_TIER_CA_STANDARD = 'CA_STANDARD';
  /**
   * Managed protection tier to be set.
   *
   * @var string
   */
  public $cloudArmorTier;

  /**
   * Managed protection tier to be set.
   *
   * Accepted values: CA_ENTERPRISE_ANNUAL, CA_ENTERPRISE_PAYGO, CA_STANDARD
   *
   * @param self::CLOUD_ARMOR_TIER_* $cloudArmorTier
   */
  public function setCloudArmorTier($cloudArmorTier)
  {
    $this->cloudArmorTier = $cloudArmorTier;
  }
  /**
   * @return self::CLOUD_ARMOR_TIER_*
   */
  public function getCloudArmorTier()
  {
    return $this->cloudArmorTier;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsSetCloudArmorTierRequest::class, 'Google_Service_Compute_ProjectsSetCloudArmorTierRequest');
