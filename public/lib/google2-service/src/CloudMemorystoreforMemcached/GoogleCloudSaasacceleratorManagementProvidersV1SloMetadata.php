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

namespace Google\Service\CloudMemorystoreforMemcached;

class GoogleCloudSaasacceleratorManagementProvidersV1SloMetadata extends \Google\Collection
{
  protected $collection_key = 'nodes';
  protected $nodesType = GoogleCloudSaasacceleratorManagementProvidersV1NodeSloMetadata::class;
  protected $nodesDataType = 'array';
  protected $perSliEligibilityType = GoogleCloudSaasacceleratorManagementProvidersV1PerSliSloEligibility::class;
  protected $perSliEligibilityDataType = '';
  /**
   * Name of the SLO tier the Instance belongs to. This name will be expected to
   * match the tiers specified in the service SLO configuration. Field is
   * mandatory and must not be empty.
   *
   * @var string
   */
  public $tier;

  /**
   * Optional. List of nodes. Some producers need to use per-node metadata to
   * calculate SLO. This field allows such producers to publish per-node SLO
   * meta data, which will be consumed by SSA Eligibility Exporter and published
   * in the form of per node metric to Monarch.
   *
   * @param GoogleCloudSaasacceleratorManagementProvidersV1NodeSloMetadata[] $nodes
   */
  public function setNodes($nodes)
  {
    $this->nodes = $nodes;
  }
  /**
   * @return GoogleCloudSaasacceleratorManagementProvidersV1NodeSloMetadata[]
   */
  public function getNodes()
  {
    return $this->nodes;
  }
  /**
   * Optional. Multiple per-instance SLI eligibilities which apply for
   * individual SLIs.
   *
   * @param GoogleCloudSaasacceleratorManagementProvidersV1PerSliSloEligibility $perSliEligibility
   */
  public function setPerSliEligibility(GoogleCloudSaasacceleratorManagementProvidersV1PerSliSloEligibility $perSliEligibility)
  {
    $this->perSliEligibility = $perSliEligibility;
  }
  /**
   * @return GoogleCloudSaasacceleratorManagementProvidersV1PerSliSloEligibility
   */
  public function getPerSliEligibility()
  {
    return $this->perSliEligibility;
  }
  /**
   * Name of the SLO tier the Instance belongs to. This name will be expected to
   * match the tiers specified in the service SLO configuration. Field is
   * mandatory and must not be empty.
   *
   * @param string $tier
   */
  public function setTier($tier)
  {
    $this->tier = $tier;
  }
  /**
   * @return string
   */
  public function getTier()
  {
    return $this->tier;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudSaasacceleratorManagementProvidersV1SloMetadata::class, 'Google_Service_CloudMemorystoreforMemcached_GoogleCloudSaasacceleratorManagementProvidersV1SloMetadata');
