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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1ReasoningEngineSpecDeploymentSpec extends \Google\Collection
{
  protected $collection_key = 'secretEnv';
  /**
   * Optional. Concurrency for each container and agent server. Recommended
   * value: 2 * cpu + 1. Defaults to 9.
   *
   * @var int
   */
  public $containerConcurrency;
  protected $envType = GoogleCloudAiplatformV1EnvVar::class;
  protected $envDataType = 'array';
  /**
   * Optional. The maximum number of application instances that can be launched
   * to handle increased traffic. Defaults to 100. Range: [1, 1000]. If VPC-SC
   * or PSC-I is enabled, the acceptable range is [1, 100].
   *
   * @var int
   */
  public $maxInstances;
  /**
   * Optional. The minimum number of application instances that will be kept
   * running at all times. Defaults to 1. Range: [0, 10].
   *
   * @var int
   */
  public $minInstances;
  protected $pscInterfaceConfigType = GoogleCloudAiplatformV1PscInterfaceConfig::class;
  protected $pscInterfaceConfigDataType = '';
  /**
   * Optional. Resource limits for each container. Only 'cpu' and 'memory' keys
   * are supported. Defaults to {"cpu": "4", "memory": "4Gi"}. * The only
   * supported values for CPU are '1', '2', '4', '6' and '8'. For more
   * information, go to https://cloud.google.com/run/docs/configuring/cpu. * The
   * only supported values for memory are '1Gi', '2Gi', ... '32 Gi'. * For
   * required cpu on different memory values, go to
   * https://cloud.google.com/run/docs/configuring/memory-limits
   *
   * @var string[]
   */
  public $resourceLimits;
  protected $secretEnvType = GoogleCloudAiplatformV1SecretEnvVar::class;
  protected $secretEnvDataType = 'array';

  /**
   * Optional. Concurrency for each container and agent server. Recommended
   * value: 2 * cpu + 1. Defaults to 9.
   *
   * @param int $containerConcurrency
   */
  public function setContainerConcurrency($containerConcurrency)
  {
    $this->containerConcurrency = $containerConcurrency;
  }
  /**
   * @return int
   */
  public function getContainerConcurrency()
  {
    return $this->containerConcurrency;
  }
  /**
   * Optional. Environment variables to be set with the Reasoning Engine
   * deployment. The environment variables can be updated through the
   * UpdateReasoningEngine API.
   *
   * @param GoogleCloudAiplatformV1EnvVar[] $env
   */
  public function setEnv($env)
  {
    $this->env = $env;
  }
  /**
   * @return GoogleCloudAiplatformV1EnvVar[]
   */
  public function getEnv()
  {
    return $this->env;
  }
  /**
   * Optional. The maximum number of application instances that can be launched
   * to handle increased traffic. Defaults to 100. Range: [1, 1000]. If VPC-SC
   * or PSC-I is enabled, the acceptable range is [1, 100].
   *
   * @param int $maxInstances
   */
  public function setMaxInstances($maxInstances)
  {
    $this->maxInstances = $maxInstances;
  }
  /**
   * @return int
   */
  public function getMaxInstances()
  {
    return $this->maxInstances;
  }
  /**
   * Optional. The minimum number of application instances that will be kept
   * running at all times. Defaults to 1. Range: [0, 10].
   *
   * @param int $minInstances
   */
  public function setMinInstances($minInstances)
  {
    $this->minInstances = $minInstances;
  }
  /**
   * @return int
   */
  public function getMinInstances()
  {
    return $this->minInstances;
  }
  /**
   * Optional. Configuration for PSC-I.
   *
   * @param GoogleCloudAiplatformV1PscInterfaceConfig $pscInterfaceConfig
   */
  public function setPscInterfaceConfig(GoogleCloudAiplatformV1PscInterfaceConfig $pscInterfaceConfig)
  {
    $this->pscInterfaceConfig = $pscInterfaceConfig;
  }
  /**
   * @return GoogleCloudAiplatformV1PscInterfaceConfig
   */
  public function getPscInterfaceConfig()
  {
    return $this->pscInterfaceConfig;
  }
  /**
   * Optional. Resource limits for each container. Only 'cpu' and 'memory' keys
   * are supported. Defaults to {"cpu": "4", "memory": "4Gi"}. * The only
   * supported values for CPU are '1', '2', '4', '6' and '8'. For more
   * information, go to https://cloud.google.com/run/docs/configuring/cpu. * The
   * only supported values for memory are '1Gi', '2Gi', ... '32 Gi'. * For
   * required cpu on different memory values, go to
   * https://cloud.google.com/run/docs/configuring/memory-limits
   *
   * @param string[] $resourceLimits
   */
  public function setResourceLimits($resourceLimits)
  {
    $this->resourceLimits = $resourceLimits;
  }
  /**
   * @return string[]
   */
  public function getResourceLimits()
  {
    return $this->resourceLimits;
  }
  /**
   * Optional. Environment variables where the value is a secret in Cloud Secret
   * Manager. To use this feature, add 'Secret Manager Secret Accessor' role
   * (roles/secretmanager.secretAccessor) to AI Platform Reasoning Engine
   * Service Agent.
   *
   * @param GoogleCloudAiplatformV1SecretEnvVar[] $secretEnv
   */
  public function setSecretEnv($secretEnv)
  {
    $this->secretEnv = $secretEnv;
  }
  /**
   * @return GoogleCloudAiplatformV1SecretEnvVar[]
   */
  public function getSecretEnv()
  {
    return $this->secretEnv;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1ReasoningEngineSpecDeploymentSpec::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1ReasoningEngineSpecDeploymentSpec');
