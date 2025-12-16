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

namespace Google\Service\Dialogflow;

class GoogleCloudDialogflowCxV3beta1Environment extends \Google\Collection
{
  protected $collection_key = 'versionConfigs';
  /**
   * The human-readable description of the environment. The maximum length is
   * 500 characters. If exceeded, the request is rejected.
   *
   * @var string
   */
  public $description;
  /**
   * Required. The human-readable name of the environment (unique in an agent).
   * Limit of 64 characters.
   *
   * @var string
   */
  public $displayName;
  /**
   * The name of the environment. Format:
   * `projects//locations//agents//environments/`.
   *
   * @var string
   */
  public $name;
  protected $testCasesConfigType = GoogleCloudDialogflowCxV3beta1EnvironmentTestCasesConfig::class;
  protected $testCasesConfigDataType = '';
  /**
   * Output only. Update time of this environment.
   *
   * @var string
   */
  public $updateTime;
  protected $versionConfigsType = GoogleCloudDialogflowCxV3beta1EnvironmentVersionConfig::class;
  protected $versionConfigsDataType = 'array';
  protected $webhookConfigType = GoogleCloudDialogflowCxV3beta1EnvironmentWebhookConfig::class;
  protected $webhookConfigDataType = '';

  /**
   * The human-readable description of the environment. The maximum length is
   * 500 characters. If exceeded, the request is rejected.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Required. The human-readable name of the environment (unique in an agent).
   * Limit of 64 characters.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * The name of the environment. Format:
   * `projects//locations//agents//environments/`.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * The test cases config for continuous tests of this environment.
   *
   * @param GoogleCloudDialogflowCxV3beta1EnvironmentTestCasesConfig $testCasesConfig
   */
  public function setTestCasesConfig(GoogleCloudDialogflowCxV3beta1EnvironmentTestCasesConfig $testCasesConfig)
  {
    $this->testCasesConfig = $testCasesConfig;
  }
  /**
   * @return GoogleCloudDialogflowCxV3beta1EnvironmentTestCasesConfig
   */
  public function getTestCasesConfig()
  {
    return $this->testCasesConfig;
  }
  /**
   * Output only. Update time of this environment.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
  /**
   * A list of configurations for flow versions. You should include version
   * configs for all flows that are reachable from `Start Flow` in the agent.
   * Otherwise, an error will be returned.
   *
   * @param GoogleCloudDialogflowCxV3beta1EnvironmentVersionConfig[] $versionConfigs
   */
  public function setVersionConfigs($versionConfigs)
  {
    $this->versionConfigs = $versionConfigs;
  }
  /**
   * @return GoogleCloudDialogflowCxV3beta1EnvironmentVersionConfig[]
   */
  public function getVersionConfigs()
  {
    return $this->versionConfigs;
  }
  /**
   * The webhook configuration for this environment.
   *
   * @param GoogleCloudDialogflowCxV3beta1EnvironmentWebhookConfig $webhookConfig
   */
  public function setWebhookConfig(GoogleCloudDialogflowCxV3beta1EnvironmentWebhookConfig $webhookConfig)
  {
    $this->webhookConfig = $webhookConfig;
  }
  /**
   * @return GoogleCloudDialogflowCxV3beta1EnvironmentWebhookConfig
   */
  public function getWebhookConfig()
  {
    return $this->webhookConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3beta1Environment::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3beta1Environment');
