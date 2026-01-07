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

class ClusterAutoscaling extends \Google\Collection
{
  /**
   * No change to autoscaling configuration.
   */
  public const AUTOSCALING_PROFILE_PROFILE_UNSPECIFIED = 'PROFILE_UNSPECIFIED';
  /**
   * Prioritize optimizing utilization of resources.
   */
  public const AUTOSCALING_PROFILE_OPTIMIZE_UTILIZATION = 'OPTIMIZE_UTILIZATION';
  /**
   * Use default (balanced) autoscaling configuration.
   */
  public const AUTOSCALING_PROFILE_BALANCED = 'BALANCED';
  protected $collection_key = 'resourceLimits';
  /**
   * The list of Google Compute Engine
   * [zones](https://cloud.google.com/compute/docs/zones#available) in which the
   * NodePool's nodes can be created by NAP.
   *
   * @var string[]
   */
  public $autoprovisioningLocations;
  protected $autoprovisioningNodePoolDefaultsType = AutoprovisioningNodePoolDefaults::class;
  protected $autoprovisioningNodePoolDefaultsDataType = '';
  /**
   * Defines autoscaling behaviour.
   *
   * @var string
   */
  public $autoscalingProfile;
  protected $defaultComputeClassConfigType = DefaultComputeClassConfig::class;
  protected $defaultComputeClassConfigDataType = '';
  /**
   * Enables automatic node pool creation and deletion.
   *
   * @var bool
   */
  public $enableNodeAutoprovisioning;
  protected $resourceLimitsType = ResourceLimit::class;
  protected $resourceLimitsDataType = 'array';

  /**
   * The list of Google Compute Engine
   * [zones](https://cloud.google.com/compute/docs/zones#available) in which the
   * NodePool's nodes can be created by NAP.
   *
   * @param string[] $autoprovisioningLocations
   */
  public function setAutoprovisioningLocations($autoprovisioningLocations)
  {
    $this->autoprovisioningLocations = $autoprovisioningLocations;
  }
  /**
   * @return string[]
   */
  public function getAutoprovisioningLocations()
  {
    return $this->autoprovisioningLocations;
  }
  /**
   * AutoprovisioningNodePoolDefaults contains defaults for a node pool created
   * by NAP.
   *
   * @param AutoprovisioningNodePoolDefaults $autoprovisioningNodePoolDefaults
   */
  public function setAutoprovisioningNodePoolDefaults(AutoprovisioningNodePoolDefaults $autoprovisioningNodePoolDefaults)
  {
    $this->autoprovisioningNodePoolDefaults = $autoprovisioningNodePoolDefaults;
  }
  /**
   * @return AutoprovisioningNodePoolDefaults
   */
  public function getAutoprovisioningNodePoolDefaults()
  {
    return $this->autoprovisioningNodePoolDefaults;
  }
  /**
   * Defines autoscaling behaviour.
   *
   * Accepted values: PROFILE_UNSPECIFIED, OPTIMIZE_UTILIZATION, BALANCED
   *
   * @param self::AUTOSCALING_PROFILE_* $autoscalingProfile
   */
  public function setAutoscalingProfile($autoscalingProfile)
  {
    $this->autoscalingProfile = $autoscalingProfile;
  }
  /**
   * @return self::AUTOSCALING_PROFILE_*
   */
  public function getAutoscalingProfile()
  {
    return $this->autoscalingProfile;
  }
  /**
   * Default compute class is a configuration for default compute class.
   *
   * @param DefaultComputeClassConfig $defaultComputeClassConfig
   */
  public function setDefaultComputeClassConfig(DefaultComputeClassConfig $defaultComputeClassConfig)
  {
    $this->defaultComputeClassConfig = $defaultComputeClassConfig;
  }
  /**
   * @return DefaultComputeClassConfig
   */
  public function getDefaultComputeClassConfig()
  {
    return $this->defaultComputeClassConfig;
  }
  /**
   * Enables automatic node pool creation and deletion.
   *
   * @param bool $enableNodeAutoprovisioning
   */
  public function setEnableNodeAutoprovisioning($enableNodeAutoprovisioning)
  {
    $this->enableNodeAutoprovisioning = $enableNodeAutoprovisioning;
  }
  /**
   * @return bool
   */
  public function getEnableNodeAutoprovisioning()
  {
    return $this->enableNodeAutoprovisioning;
  }
  /**
   * Contains global constraints regarding minimum and maximum amount of
   * resources in the cluster.
   *
   * @param ResourceLimit[] $resourceLimits
   */
  public function setResourceLimits($resourceLimits)
  {
    $this->resourceLimits = $resourceLimits;
  }
  /**
   * @return ResourceLimit[]
   */
  public function getResourceLimits()
  {
    return $this->resourceLimits;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ClusterAutoscaling::class, 'Google_Service_Container_ClusterAutoscaling');
