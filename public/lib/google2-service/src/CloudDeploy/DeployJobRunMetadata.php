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

class DeployJobRunMetadata extends \Google\Model
{
  protected $cloudRunType = CloudRunMetadata::class;
  protected $cloudRunDataType = '';
  protected $customType = CustomMetadata::class;
  protected $customDataType = '';
  protected $customTargetType = CustomTargetDeployMetadata::class;
  protected $customTargetDataType = '';

  /**
   * Output only. The name of the Cloud Run Service that is associated with a
   * `DeployJobRun`.
   *
   * @param CloudRunMetadata $cloudRun
   */
  public function setCloudRun(CloudRunMetadata $cloudRun)
  {
    $this->cloudRun = $cloudRun;
  }
  /**
   * @return CloudRunMetadata
   */
  public function getCloudRun()
  {
    return $this->cloudRun;
  }
  /**
   * Output only. Custom metadata provided by user-defined deploy operation.
   *
   * @param CustomMetadata $custom
   */
  public function setCustom(CustomMetadata $custom)
  {
    $this->custom = $custom;
  }
  /**
   * @return CustomMetadata
   */
  public function getCustom()
  {
    return $this->custom;
  }
  /**
   * Output only. Custom Target metadata associated with a `DeployJobRun`.
   *
   * @param CustomTargetDeployMetadata $customTarget
   */
  public function setCustomTarget(CustomTargetDeployMetadata $customTarget)
  {
    $this->customTarget = $customTarget;
  }
  /**
   * @return CustomTargetDeployMetadata
   */
  public function getCustomTarget()
  {
    return $this->customTarget;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DeployJobRunMetadata::class, 'Google_Service_CloudDeploy_DeployJobRunMetadata');
