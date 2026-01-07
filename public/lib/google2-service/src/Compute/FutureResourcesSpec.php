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

class FutureResourcesSpec extends \Google\Model
{
  /**
   * The reserved capacity is made up of densely deployed reservation blocks.
   */
  public const DEPLOYMENT_TYPE_DENSE = 'DENSE';
  public const DEPLOYMENT_TYPE_DEPLOYMENT_TYPE_UNSPECIFIED = 'DEPLOYMENT_TYPE_UNSPECIFIED';
  /**
   * Indicates if the reservation allocation strategy is static (DENSE) or
   * dynamic (STANDARD). Defaults to DENSE.
   *
   * @var string
   */
  public $deploymentType;
  protected $locationPolicyType = FutureResourcesSpecLocationPolicy::class;
  protected $locationPolicyDataType = '';
  protected $targetResourcesType = FutureResourcesSpecTargetResources::class;
  protected $targetResourcesDataType = '';
  protected $timeRangeSpecType = FlexibleTimeRange::class;
  protected $timeRangeSpecDataType = '';

  /**
   * Indicates if the reservation allocation strategy is static (DENSE) or
   * dynamic (STANDARD). Defaults to DENSE.
   *
   * Accepted values: DENSE, DEPLOYMENT_TYPE_UNSPECIFIED
   *
   * @param self::DEPLOYMENT_TYPE_* $deploymentType
   */
  public function setDeploymentType($deploymentType)
  {
    $this->deploymentType = $deploymentType;
  }
  /**
   * @return self::DEPLOYMENT_TYPE_*
   */
  public function getDeploymentType()
  {
    return $this->deploymentType;
  }
  /**
   * Optional location policy allowing to exclude some zone(s) in which the
   * resources must not be created.
   *
   * @param FutureResourcesSpecLocationPolicy $locationPolicy
   */
  public function setLocationPolicy(FutureResourcesSpecLocationPolicy $locationPolicy)
  {
    $this->locationPolicy = $locationPolicy;
  }
  /**
   * @return FutureResourcesSpecLocationPolicy
   */
  public function getLocationPolicy()
  {
    return $this->locationPolicy;
  }
  /**
   * Specification of the reserved resources.
   *
   * @param FutureResourcesSpecTargetResources $targetResources
   */
  public function setTargetResources(FutureResourcesSpecTargetResources $targetResources)
  {
    $this->targetResources = $targetResources;
  }
  /**
   * @return FutureResourcesSpecTargetResources
   */
  public function getTargetResources()
  {
    return $this->targetResources;
  }
  /**
   * Specification of a time range in which the resources may be created. The
   * time range specifies start of resource use and planned end of resource use.
   *
   * @param FlexibleTimeRange $timeRangeSpec
   */
  public function setTimeRangeSpec(FlexibleTimeRange $timeRangeSpec)
  {
    $this->timeRangeSpec = $timeRangeSpec;
  }
  /**
   * @return FlexibleTimeRange
   */
  public function getTimeRangeSpec()
  {
    return $this->timeRangeSpec;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FutureResourcesSpec::class, 'Google_Service_Compute_FutureResourcesSpec');
