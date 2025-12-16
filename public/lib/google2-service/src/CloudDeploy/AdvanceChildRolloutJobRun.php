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

class AdvanceChildRolloutJobRun extends \Google\Model
{
  /**
   * Output only. Name of the `ChildRollout`. Format is `projects/{project}/loca
   * tions/{location}/deliveryPipelines/{deliveryPipeline}/releases/{release}/ro
   * llouts/{rollout}`.
   *
   * @var string
   */
  public $rollout;
  /**
   * Output only. the ID of the ChildRollout's Phase.
   *
   * @var string
   */
  public $rolloutPhaseId;

  /**
   * Output only. Name of the `ChildRollout`. Format is `projects/{project}/loca
   * tions/{location}/deliveryPipelines/{deliveryPipeline}/releases/{release}/ro
   * llouts/{rollout}`.
   *
   * @param string $rollout
   */
  public function setRollout($rollout)
  {
    $this->rollout = $rollout;
  }
  /**
   * @return string
   */
  public function getRollout()
  {
    return $this->rollout;
  }
  /**
   * Output only. the ID of the ChildRollout's Phase.
   *
   * @param string $rolloutPhaseId
   */
  public function setRolloutPhaseId($rolloutPhaseId)
  {
    $this->rolloutPhaseId = $rolloutPhaseId;
  }
  /**
   * @return string
   */
  public function getRolloutPhaseId()
  {
    return $this->rolloutPhaseId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AdvanceChildRolloutJobRun::class, 'Google_Service_CloudDeploy_AdvanceChildRolloutJobRun');
