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

class Metadata extends \Google\Model
{
  protected $automationType = AutomationRolloutMetadata::class;
  protected $automationDataType = '';
  protected $cloudRunType = CloudRunMetadata::class;
  protected $cloudRunDataType = '';
  protected $customType = CustomMetadata::class;
  protected $customDataType = '';

  /**
   * Output only. AutomationRolloutMetadata contains the information about the
   * interactions between Automation service and this rollout.
   *
   * @param AutomationRolloutMetadata $automation
   */
  public function setAutomation(AutomationRolloutMetadata $automation)
  {
    $this->automation = $automation;
  }
  /**
   * @return AutomationRolloutMetadata
   */
  public function getAutomation()
  {
    return $this->automation;
  }
  /**
   * Output only. The name of the Cloud Run Service that is associated with a
   * `Rollout`.
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
   * Output only. Custom metadata provided by user-defined `Rollout` operations.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Metadata::class, 'Google_Service_CloudDeploy_Metadata');
