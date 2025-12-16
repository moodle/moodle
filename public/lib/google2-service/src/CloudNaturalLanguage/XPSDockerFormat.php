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

namespace Google\Service\CloudNaturalLanguage;

class XPSDockerFormat extends \Google\Model
{
  public const CPU_ARCHITECTURE_CPU_ARCHITECTURE_UNSPECIFIED = 'CPU_ARCHITECTURE_UNSPECIFIED';
  public const CPU_ARCHITECTURE_CPU_ARCHITECTURE_X86_64 = 'CPU_ARCHITECTURE_X86_64';
  public const GPU_ARCHITECTURE_GPU_ARCHITECTURE_UNSPECIFIED = 'GPU_ARCHITECTURE_UNSPECIFIED';
  public const GPU_ARCHITECTURE_GPU_ARCHITECTURE_NVIDIA = 'GPU_ARCHITECTURE_NVIDIA';
  /**
   * Optional. Additional cpu information describing the requirements for the to
   * be exported model files.
   *
   * @var string
   */
  public $cpuArchitecture;
  /**
   * Optional. Additional gpu information describing the requirements for the to
   * be exported model files.
   *
   * @var string
   */
  public $gpuArchitecture;

  /**
   * Optional. Additional cpu information describing the requirements for the to
   * be exported model files.
   *
   * Accepted values: CPU_ARCHITECTURE_UNSPECIFIED, CPU_ARCHITECTURE_X86_64
   *
   * @param self::CPU_ARCHITECTURE_* $cpuArchitecture
   */
  public function setCpuArchitecture($cpuArchitecture)
  {
    $this->cpuArchitecture = $cpuArchitecture;
  }
  /**
   * @return self::CPU_ARCHITECTURE_*
   */
  public function getCpuArchitecture()
  {
    return $this->cpuArchitecture;
  }
  /**
   * Optional. Additional gpu information describing the requirements for the to
   * be exported model files.
   *
   * Accepted values: GPU_ARCHITECTURE_UNSPECIFIED, GPU_ARCHITECTURE_NVIDIA
   *
   * @param self::GPU_ARCHITECTURE_* $gpuArchitecture
   */
  public function setGpuArchitecture($gpuArchitecture)
  {
    $this->gpuArchitecture = $gpuArchitecture;
  }
  /**
   * @return self::GPU_ARCHITECTURE_*
   */
  public function getGpuArchitecture()
  {
    return $this->gpuArchitecture;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(XPSDockerFormat::class, 'Google_Service_CloudNaturalLanguage_XPSDockerFormat');
