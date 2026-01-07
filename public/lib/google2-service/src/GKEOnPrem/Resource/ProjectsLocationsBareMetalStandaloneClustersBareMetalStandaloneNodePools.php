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

namespace Google\Service\GKEOnPrem\Resource;

use Google\Service\GKEOnPrem\EnrollBareMetalStandaloneNodePoolRequest;
use Google\Service\GKEOnPrem\Operation;

/**
 * The "bareMetalStandaloneNodePools" collection of methods.
 * Typical usage is:
 *  <code>
 *   $gkeonpremService = new Google\Service\GKEOnPrem(...);
 *   $bareMetalStandaloneNodePools = $gkeonpremService->projects_locations_bareMetalStandaloneClusters_bareMetalStandaloneNodePools;
 *  </code>
 */
class ProjectsLocationsBareMetalStandaloneClustersBareMetalStandaloneNodePools extends \Google\Service\Resource
{
  /**
   * Enrolls an existing bare metal standalone node pool to the Anthos On-Prem API
   * within a given project and location. Through enrollment, an existing
   * standalone node pool will become Anthos On-Prem API managed. The
   * corresponding GCP resources will be created.
   * (bareMetalStandaloneNodePools.enroll)
   *
   * @param string $parent Required. The parent resource where this node pool will
   * be created.
   * projects/{project}/locations/{location}/bareMetalStandaloneClusters/{cluster}
   * @param EnrollBareMetalStandaloneNodePoolRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   */
  public function enroll($parent, EnrollBareMetalStandaloneNodePoolRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('enroll', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsBareMetalStandaloneClustersBareMetalStandaloneNodePools::class, 'Google_Service_GKEOnPrem_Resource_ProjectsLocationsBareMetalStandaloneClustersBareMetalStandaloneNodePools');
