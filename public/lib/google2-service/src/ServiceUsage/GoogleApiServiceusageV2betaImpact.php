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

namespace Google\Service\ServiceUsage;

class GoogleApiServiceusageV2betaImpact extends \Google\Model
{
  /**
   * Reserved Blocks (Block n contains codes from 100n to 100(n+1) -1 Block 0 -
   * Special/Admin codes Block 1 - Impact Type of ANALYSIS_TYPE_DEPENDENCY Block
   * 2 - Impact Type of ANALYSIS_TYPE_RESOURCE_USAGE ...
   */
  public const IMPACT_TYPE_IMPACT_TYPE_UNSPECIFIED = 'IMPACT_TYPE_UNSPECIFIED';
  /**
   * Block 1 - Impact Type of ANALYSIS_TYPE_DEPENDENCY
   */
  public const IMPACT_TYPE_DEPENDENCY_MISSING_DEPENDENCIES = 'DEPENDENCY_MISSING_DEPENDENCIES';
  /**
   * Output only. User friendly impact detail in a free form message.
   *
   * @var string
   */
  public $detail;
  /**
   * Output only. The type of impact.
   *
   * @var string
   */
  public $impactType;
  /**
   * Output only. This field will be populated only for the
   * `DEPENDENCY_MISSING_DEPENDENCIES` impact type. Example:
   * `services/compute.googleapis.com`. Impact.detail will be in format :
   * `missing service dependency: {missing_dependency}.`
   *
   * @var string
   */
  public $missingDependency;

  /**
   * Output only. User friendly impact detail in a free form message.
   *
   * @param string $detail
   */
  public function setDetail($detail)
  {
    $this->detail = $detail;
  }
  /**
   * @return string
   */
  public function getDetail()
  {
    return $this->detail;
  }
  /**
   * Output only. The type of impact.
   *
   * Accepted values: IMPACT_TYPE_UNSPECIFIED, DEPENDENCY_MISSING_DEPENDENCIES
   *
   * @param self::IMPACT_TYPE_* $impactType
   */
  public function setImpactType($impactType)
  {
    $this->impactType = $impactType;
  }
  /**
   * @return self::IMPACT_TYPE_*
   */
  public function getImpactType()
  {
    return $this->impactType;
  }
  /**
   * Output only. This field will be populated only for the
   * `DEPENDENCY_MISSING_DEPENDENCIES` impact type. Example:
   * `services/compute.googleapis.com`. Impact.detail will be in format :
   * `missing service dependency: {missing_dependency}.`
   *
   * @param string $missingDependency
   */
  public function setMissingDependency($missingDependency)
  {
    $this->missingDependency = $missingDependency;
  }
  /**
   * @return string
   */
  public function getMissingDependency()
  {
    return $this->missingDependency;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleApiServiceusageV2betaImpact::class, 'Google_Service_ServiceUsage_GoogleApiServiceusageV2betaImpact');
