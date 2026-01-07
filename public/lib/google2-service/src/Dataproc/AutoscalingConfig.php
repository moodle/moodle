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

namespace Google\Service\Dataproc;

class AutoscalingConfig extends \Google\Model
{
  /**
   * Optional. The autoscaling policy used by the cluster.Only resource names
   * including projectid and location (region) are valid. Examples: https://www.
   * googleapis.com/compute/v1/projects/[project_id]/locations/[dataproc_region]
   * /autoscalingPolicies/[policy_id] projects/[project_id]/locations/[dataproc_
   * region]/autoscalingPolicies/[policy_id]Note that the policy must be in the
   * same project and Dataproc region.
   *
   * @var string
   */
  public $policyUri;

  /**
   * Optional. The autoscaling policy used by the cluster.Only resource names
   * including projectid and location (region) are valid. Examples: https://www.
   * googleapis.com/compute/v1/projects/[project_id]/locations/[dataproc_region]
   * /autoscalingPolicies/[policy_id] projects/[project_id]/locations/[dataproc_
   * region]/autoscalingPolicies/[policy_id]Note that the policy must be in the
   * same project and Dataproc region.
   *
   * @param string $policyUri
   */
  public function setPolicyUri($policyUri)
  {
    $this->policyUri = $policyUri;
  }
  /**
   * @return string
   */
  public function getPolicyUri()
  {
    return $this->policyUri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AutoscalingConfig::class, 'Google_Service_Dataproc_AutoscalingConfig');
