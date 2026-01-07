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

namespace Google\Service\Batch;

class ComputeResource extends \Google\Model
{
  /**
   * Extra boot disk size in MiB for each task.
   *
   * @var string
   */
  public $bootDiskMib;
  /**
   * The milliCPU count. `cpuMilli` defines the amount of CPU resources per task
   * in milliCPU units. For example, `1000` corresponds to 1 vCPU per task. If
   * undefined, the default value is `2000`. If you also define the VM's machine
   * type using the `machineType` in [InstancePolicy](https://cloud.google.com/b
   * atch/docs/reference/rest/v1/projects.locations.jobs#instancepolicy) field
   * or inside the `instanceTemplate` in the [InstancePolicyOrTemplate](https://
   * cloud.google.com/batch/docs/reference/rest/v1/projects.locations.jobs#insta
   * ncepolicyortemplate) field, make sure the CPU resources for both fields are
   * compatible with each other and with how many tasks you want to allow to run
   * on the same VM at the same time. For example, if you specify the
   * `n2-standard-2` machine type, which has 2 vCPUs each, you are recommended
   * to set `cpuMilli` no more than `2000`, or you are recommended to run two
   * tasks on the same VM if you set `cpuMilli` to `1000` or less.
   *
   * @var string
   */
  public $cpuMilli;
  /**
   * Memory in MiB. `memoryMib` defines the amount of memory per task in MiB
   * units. If undefined, the default value is `2000`. If you also define the
   * VM's machine type using the `machineType` in [InstancePolicy](https://cloud
   * .google.com/batch/docs/reference/rest/v1/projects.locations.jobs#instancepo
   * licy) field or inside the `instanceTemplate` in the [InstancePolicyOrTempla
   * te](https://cloud.google.com/batch/docs/reference/rest/v1/projects.location
   * s.jobs#instancepolicyortemplate) field, make sure the memory resources for
   * both fields are compatible with each other and with how many tasks you want
   * to allow to run on the same VM at the same time. For example, if you
   * specify the `n2-standard-2` machine type, which has 8 GiB each, you are
   * recommended to set `memoryMib` to no more than `8192`, or you are
   * recommended to run two tasks on the same VM if you set `memoryMib` to
   * `4096` or less.
   *
   * @var string
   */
  public $memoryMib;

  /**
   * Extra boot disk size in MiB for each task.
   *
   * @param string $bootDiskMib
   */
  public function setBootDiskMib($bootDiskMib)
  {
    $this->bootDiskMib = $bootDiskMib;
  }
  /**
   * @return string
   */
  public function getBootDiskMib()
  {
    return $this->bootDiskMib;
  }
  /**
   * The milliCPU count. `cpuMilli` defines the amount of CPU resources per task
   * in milliCPU units. For example, `1000` corresponds to 1 vCPU per task. If
   * undefined, the default value is `2000`. If you also define the VM's machine
   * type using the `machineType` in [InstancePolicy](https://cloud.google.com/b
   * atch/docs/reference/rest/v1/projects.locations.jobs#instancepolicy) field
   * or inside the `instanceTemplate` in the [InstancePolicyOrTemplate](https://
   * cloud.google.com/batch/docs/reference/rest/v1/projects.locations.jobs#insta
   * ncepolicyortemplate) field, make sure the CPU resources for both fields are
   * compatible with each other and with how many tasks you want to allow to run
   * on the same VM at the same time. For example, if you specify the
   * `n2-standard-2` machine type, which has 2 vCPUs each, you are recommended
   * to set `cpuMilli` no more than `2000`, or you are recommended to run two
   * tasks on the same VM if you set `cpuMilli` to `1000` or less.
   *
   * @param string $cpuMilli
   */
  public function setCpuMilli($cpuMilli)
  {
    $this->cpuMilli = $cpuMilli;
  }
  /**
   * @return string
   */
  public function getCpuMilli()
  {
    return $this->cpuMilli;
  }
  /**
   * Memory in MiB. `memoryMib` defines the amount of memory per task in MiB
   * units. If undefined, the default value is `2000`. If you also define the
   * VM's machine type using the `machineType` in [InstancePolicy](https://cloud
   * .google.com/batch/docs/reference/rest/v1/projects.locations.jobs#instancepo
   * licy) field or inside the `instanceTemplate` in the [InstancePolicyOrTempla
   * te](https://cloud.google.com/batch/docs/reference/rest/v1/projects.location
   * s.jobs#instancepolicyortemplate) field, make sure the memory resources for
   * both fields are compatible with each other and with how many tasks you want
   * to allow to run on the same VM at the same time. For example, if you
   * specify the `n2-standard-2` machine type, which has 8 GiB each, you are
   * recommended to set `memoryMib` to no more than `8192`, or you are
   * recommended to run two tasks on the same VM if you set `memoryMib` to
   * `4096` or less.
   *
   * @param string $memoryMib
   */
  public function setMemoryMib($memoryMib)
  {
    $this->memoryMib = $memoryMib;
  }
  /**
   * @return string
   */
  public function getMemoryMib()
  {
    return $this->memoryMib;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ComputeResource::class, 'Google_Service_Batch_ComputeResource');
