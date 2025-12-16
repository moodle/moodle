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

class GoogleCloudSaasacceleratorManagementProvidersV1NodeSloMetadata extends \Google\Model
{
  /**
   * The location of the node, if different from instance location.
   *
   * @var string
   */
  public $location;
  /**
   * The id of the node. This should be equal to SaasInstanceNode.node_id.
   *
   * @var string
   */
  public $nodeId;
  protected $perSliEligibilityType = GoogleCloudSaasacceleratorManagementProvidersV1PerSliSloEligibility::class;
  protected $perSliEligibilityDataType = '';

  /**
   * The location of the node, if different from instance location.
   *
   * @param string $location
   */
  public function setLocation($location)
  {
    $this->location = $location;
  }
  /**
   * @return string
   */
  public function getLocation()
  {
    return $this->location;
  }
  /**
   * The id of the node. This should be equal to SaasInstanceNode.node_id.
   *
   * @param string $nodeId
   */
  public function setNodeId($nodeId)
  {
    $this->nodeId = $nodeId;
  }
  /**
   * @return string
   */
  public function getNodeId()
  {
    return $this->nodeId;
  }
  /**
   * If present, this will override eligibility for the node coming from
   * instance or exclusions for specified SLIs.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudSaasacceleratorManagementProvidersV1NodeSloMetadata::class, 'Google_Service_CloudMemorystoreforMemcached_GoogleCloudSaasacceleratorManagementProvidersV1NodeSloMetadata');
