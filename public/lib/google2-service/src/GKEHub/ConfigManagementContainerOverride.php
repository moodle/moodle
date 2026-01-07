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

namespace Google\Service\GKEHub;

class ConfigManagementContainerOverride extends \Google\Model
{
  /**
   * Required. The name of the container.
   *
   * @var string
   */
  public $containerName;
  /**
   * Optional. The cpu limit of the container.
   *
   * @var string
   */
  public $cpuLimit;
  /**
   * Optional. The cpu request of the container.
   *
   * @var string
   */
  public $cpuRequest;
  /**
   * Optional. The memory limit of the container.
   *
   * @var string
   */
  public $memoryLimit;
  /**
   * Optional. The memory request of the container.
   *
   * @var string
   */
  public $memoryRequest;

  /**
   * Required. The name of the container.
   *
   * @param string $containerName
   */
  public function setContainerName($containerName)
  {
    $this->containerName = $containerName;
  }
  /**
   * @return string
   */
  public function getContainerName()
  {
    return $this->containerName;
  }
  /**
   * Optional. The cpu limit of the container.
   *
   * @param string $cpuLimit
   */
  public function setCpuLimit($cpuLimit)
  {
    $this->cpuLimit = $cpuLimit;
  }
  /**
   * @return string
   */
  public function getCpuLimit()
  {
    return $this->cpuLimit;
  }
  /**
   * Optional. The cpu request of the container.
   *
   * @param string $cpuRequest
   */
  public function setCpuRequest($cpuRequest)
  {
    $this->cpuRequest = $cpuRequest;
  }
  /**
   * @return string
   */
  public function getCpuRequest()
  {
    return $this->cpuRequest;
  }
  /**
   * Optional. The memory limit of the container.
   *
   * @param string $memoryLimit
   */
  public function setMemoryLimit($memoryLimit)
  {
    $this->memoryLimit = $memoryLimit;
  }
  /**
   * @return string
   */
  public function getMemoryLimit()
  {
    return $this->memoryLimit;
  }
  /**
   * Optional. The memory request of the container.
   *
   * @param string $memoryRequest
   */
  public function setMemoryRequest($memoryRequest)
  {
    $this->memoryRequest = $memoryRequest;
  }
  /**
   * @return string
   */
  public function getMemoryRequest()
  {
    return $this->memoryRequest;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ConfigManagementContainerOverride::class, 'Google_Service_GKEHub_ConfigManagementContainerOverride');
