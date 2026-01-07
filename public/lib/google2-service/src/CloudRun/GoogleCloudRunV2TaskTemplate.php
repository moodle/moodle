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

namespace Google\Service\CloudRun;

class GoogleCloudRunV2TaskTemplate extends \Google\Collection
{
  /**
   * Unspecified
   */
  public const EXECUTION_ENVIRONMENT_EXECUTION_ENVIRONMENT_UNSPECIFIED = 'EXECUTION_ENVIRONMENT_UNSPECIFIED';
  /**
   * Uses the First Generation environment.
   */
  public const EXECUTION_ENVIRONMENT_EXECUTION_ENVIRONMENT_GEN1 = 'EXECUTION_ENVIRONMENT_GEN1';
  /**
   * Uses Second Generation environment.
   */
  public const EXECUTION_ENVIRONMENT_EXECUTION_ENVIRONMENT_GEN2 = 'EXECUTION_ENVIRONMENT_GEN2';
  protected $collection_key = 'volumes';
  protected $containersType = GoogleCloudRunV2Container::class;
  protected $containersDataType = 'array';
  /**
   * A reference to a customer managed encryption key (CMEK) to use to encrypt
   * this container image. For more information, go to
   * https://cloud.google.com/run/docs/securing/using-cmek
   *
   * @var string
   */
  public $encryptionKey;
  /**
   * Optional. The execution environment being used to host this Task.
   *
   * @var string
   */
  public $executionEnvironment;
  /**
   * Optional. True if GPU zonal redundancy is disabled on this task template.
   *
   * @var bool
   */
  public $gpuZonalRedundancyDisabled;
  /**
   * Number of retries allowed per Task, before marking this Task failed.
   * Defaults to 3.
   *
   * @var int
   */
  public $maxRetries;
  protected $nodeSelectorType = GoogleCloudRunV2NodeSelector::class;
  protected $nodeSelectorDataType = '';
  /**
   * Optional. Email address of the IAM service account associated with the Task
   * of a Job. The service account represents the identity of the running task,
   * and determines what permissions the task has. If not provided, the task
   * will use the project's default service account.
   *
   * @var string
   */
  public $serviceAccount;
  /**
   * Optional. Max allowed time duration the Task may be active before the
   * system will actively try to mark it failed and kill associated containers.
   * This applies per attempt of a task, meaning each retry can run for the full
   * timeout. Defaults to 600 seconds.
   *
   * @var string
   */
  public $timeout;
  protected $volumesType = GoogleCloudRunV2Volume::class;
  protected $volumesDataType = 'array';
  protected $vpcAccessType = GoogleCloudRunV2VpcAccess::class;
  protected $vpcAccessDataType = '';

  /**
   * Holds the single container that defines the unit of execution for this
   * task.
   *
   * @param GoogleCloudRunV2Container[] $containers
   */
  public function setContainers($containers)
  {
    $this->containers = $containers;
  }
  /**
   * @return GoogleCloudRunV2Container[]
   */
  public function getContainers()
  {
    return $this->containers;
  }
  /**
   * A reference to a customer managed encryption key (CMEK) to use to encrypt
   * this container image. For more information, go to
   * https://cloud.google.com/run/docs/securing/using-cmek
   *
   * @param string $encryptionKey
   */
  public function setEncryptionKey($encryptionKey)
  {
    $this->encryptionKey = $encryptionKey;
  }
  /**
   * @return string
   */
  public function getEncryptionKey()
  {
    return $this->encryptionKey;
  }
  /**
   * Optional. The execution environment being used to host this Task.
   *
   * Accepted values: EXECUTION_ENVIRONMENT_UNSPECIFIED,
   * EXECUTION_ENVIRONMENT_GEN1, EXECUTION_ENVIRONMENT_GEN2
   *
   * @param self::EXECUTION_ENVIRONMENT_* $executionEnvironment
   */
  public function setExecutionEnvironment($executionEnvironment)
  {
    $this->executionEnvironment = $executionEnvironment;
  }
  /**
   * @return self::EXECUTION_ENVIRONMENT_*
   */
  public function getExecutionEnvironment()
  {
    return $this->executionEnvironment;
  }
  /**
   * Optional. True if GPU zonal redundancy is disabled on this task template.
   *
   * @param bool $gpuZonalRedundancyDisabled
   */
  public function setGpuZonalRedundancyDisabled($gpuZonalRedundancyDisabled)
  {
    $this->gpuZonalRedundancyDisabled = $gpuZonalRedundancyDisabled;
  }
  /**
   * @return bool
   */
  public function getGpuZonalRedundancyDisabled()
  {
    return $this->gpuZonalRedundancyDisabled;
  }
  /**
   * Number of retries allowed per Task, before marking this Task failed.
   * Defaults to 3.
   *
   * @param int $maxRetries
   */
  public function setMaxRetries($maxRetries)
  {
    $this->maxRetries = $maxRetries;
  }
  /**
   * @return int
   */
  public function getMaxRetries()
  {
    return $this->maxRetries;
  }
  /**
   * Optional. The node selector for the task template.
   *
   * @param GoogleCloudRunV2NodeSelector $nodeSelector
   */
  public function setNodeSelector(GoogleCloudRunV2NodeSelector $nodeSelector)
  {
    $this->nodeSelector = $nodeSelector;
  }
  /**
   * @return GoogleCloudRunV2NodeSelector
   */
  public function getNodeSelector()
  {
    return $this->nodeSelector;
  }
  /**
   * Optional. Email address of the IAM service account associated with the Task
   * of a Job. The service account represents the identity of the running task,
   * and determines what permissions the task has. If not provided, the task
   * will use the project's default service account.
   *
   * @param string $serviceAccount
   */
  public function setServiceAccount($serviceAccount)
  {
    $this->serviceAccount = $serviceAccount;
  }
  /**
   * @return string
   */
  public function getServiceAccount()
  {
    return $this->serviceAccount;
  }
  /**
   * Optional. Max allowed time duration the Task may be active before the
   * system will actively try to mark it failed and kill associated containers.
   * This applies per attempt of a task, meaning each retry can run for the full
   * timeout. Defaults to 600 seconds.
   *
   * @param string $timeout
   */
  public function setTimeout($timeout)
  {
    $this->timeout = $timeout;
  }
  /**
   * @return string
   */
  public function getTimeout()
  {
    return $this->timeout;
  }
  /**
   * Optional. A list of Volumes to make available to containers.
   *
   * @param GoogleCloudRunV2Volume[] $volumes
   */
  public function setVolumes($volumes)
  {
    $this->volumes = $volumes;
  }
  /**
   * @return GoogleCloudRunV2Volume[]
   */
  public function getVolumes()
  {
    return $this->volumes;
  }
  /**
   * Optional. VPC Access configuration to use for this Task. For more
   * information, visit
   * https://cloud.google.com/run/docs/configuring/connecting-vpc.
   *
   * @param GoogleCloudRunV2VpcAccess $vpcAccess
   */
  public function setVpcAccess(GoogleCloudRunV2VpcAccess $vpcAccess)
  {
    $this->vpcAccess = $vpcAccess;
  }
  /**
   * @return GoogleCloudRunV2VpcAccess
   */
  public function getVpcAccess()
  {
    return $this->vpcAccess;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRunV2TaskTemplate::class, 'Google_Service_CloudRun_GoogleCloudRunV2TaskTemplate');
