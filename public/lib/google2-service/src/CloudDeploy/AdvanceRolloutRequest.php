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

namespace Google\Service\CloudDeploy;

class AdvanceRolloutRequest extends \Google\Collection
{
  protected $collection_key = 'overrideDeployPolicy';
  /**
   * Optional. Deploy policies to override. Format is
   * `projects/{project}/locations/{location}/deployPolicies/{deployPolicy}`.
   *
   * @var string[]
   */
  public $overrideDeployPolicy;
  /**
   * Required. The phase ID to advance the `Rollout` to.
   *
   * @var string
   */
  public $phaseId;

  /**
   * Optional. Deploy policies to override. Format is
   * `projects/{project}/locations/{location}/deployPolicies/{deployPolicy}`.
   *
   * @param string[] $overrideDeployPolicy
   */
  public function setOverrideDeployPolicy($overrideDeployPolicy)
  {
    $this->overrideDeployPolicy = $overrideDeployPolicy;
  }
  /**
   * @return string[]
   */
  public function getOverrideDeployPolicy()
  {
    return $this->overrideDeployPolicy;
  }
  /**
   * Required. The phase ID to advance the `Rollout` to.
   *
   * @param string $phaseId
   */
  public function setPhaseId($phaseId)
  {
    $this->phaseId = $phaseId;
  }
  /**
   * @return string
   */
  public function getPhaseId()
  {
    return $this->phaseId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AdvanceRolloutRequest::class, 'Google_Service_CloudDeploy_AdvanceRolloutRequest');
