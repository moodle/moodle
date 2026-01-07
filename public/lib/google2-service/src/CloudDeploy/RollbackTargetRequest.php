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

class RollbackTargetRequest extends \Google\Collection
{
  protected $collection_key = 'overrideDeployPolicy';
  /**
   * Optional. Deploy policies to override. Format is
   * `projects/{project}/locations/{location}/deployPolicies/{deploy_policy}`.
   *
   * @var string[]
   */
  public $overrideDeployPolicy;
  /**
   * Optional. ID of the `Release` to roll back to. If this isn't specified, the
   * previous successful `Rollout` to the specified target will be used to
   * determine the `Release`.
   *
   * @var string
   */
  public $releaseId;
  protected $rollbackConfigType = RollbackTargetConfig::class;
  protected $rollbackConfigDataType = '';
  /**
   * Required. ID of the rollback `Rollout` to create.
   *
   * @var string
   */
  public $rolloutId;
  /**
   * Optional. If provided, this must be the latest `Rollout` that is on the
   * `Target`.
   *
   * @var string
   */
  public $rolloutToRollBack;
  /**
   * Required. ID of the `Target` that is being rolled back.
   *
   * @var string
   */
  public $targetId;
  /**
   * Optional. If set to true, the request is validated and the user is provided
   * with a `RollbackTargetResponse`.
   *
   * @var bool
   */
  public $validateOnly;

  /**
   * Optional. Deploy policies to override. Format is
   * `projects/{project}/locations/{location}/deployPolicies/{deploy_policy}`.
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
   * Optional. ID of the `Release` to roll back to. If this isn't specified, the
   * previous successful `Rollout` to the specified target will be used to
   * determine the `Release`.
   *
   * @param string $releaseId
   */
  public function setReleaseId($releaseId)
  {
    $this->releaseId = $releaseId;
  }
  /**
   * @return string
   */
  public function getReleaseId()
  {
    return $this->releaseId;
  }
  /**
   * Optional. Configs for the rollback `Rollout`.
   *
   * @param RollbackTargetConfig $rollbackConfig
   */
  public function setRollbackConfig(RollbackTargetConfig $rollbackConfig)
  {
    $this->rollbackConfig = $rollbackConfig;
  }
  /**
   * @return RollbackTargetConfig
   */
  public function getRollbackConfig()
  {
    return $this->rollbackConfig;
  }
  /**
   * Required. ID of the rollback `Rollout` to create.
   *
   * @param string $rolloutId
   */
  public function setRolloutId($rolloutId)
  {
    $this->rolloutId = $rolloutId;
  }
  /**
   * @return string
   */
  public function getRolloutId()
  {
    return $this->rolloutId;
  }
  /**
   * Optional. If provided, this must be the latest `Rollout` that is on the
   * `Target`.
   *
   * @param string $rolloutToRollBack
   */
  public function setRolloutToRollBack($rolloutToRollBack)
  {
    $this->rolloutToRollBack = $rolloutToRollBack;
  }
  /**
   * @return string
   */
  public function getRolloutToRollBack()
  {
    return $this->rolloutToRollBack;
  }
  /**
   * Required. ID of the `Target` that is being rolled back.
   *
   * @param string $targetId
   */
  public function setTargetId($targetId)
  {
    $this->targetId = $targetId;
  }
  /**
   * @return string
   */
  public function getTargetId()
  {
    return $this->targetId;
  }
  /**
   * Optional. If set to true, the request is validated and the user is provided
   * with a `RollbackTargetResponse`.
   *
   * @param bool $validateOnly
   */
  public function setValidateOnly($validateOnly)
  {
    $this->validateOnly = $validateOnly;
  }
  /**
   * @return bool
   */
  public function getValidateOnly()
  {
    return $this->validateOnly;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RollbackTargetRequest::class, 'Google_Service_CloudDeploy_RollbackTargetRequest');
