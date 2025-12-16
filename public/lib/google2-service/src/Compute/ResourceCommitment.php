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

namespace Google\Service\Compute;

class ResourceCommitment extends \Google\Model
{
  public const TYPE_ACCELERATOR = 'ACCELERATOR';
  public const TYPE_LOCAL_SSD = 'LOCAL_SSD';
  public const TYPE_MEMORY = 'MEMORY';
  public const TYPE_UNSPECIFIED = 'UNSPECIFIED';
  public const TYPE_VCPU = 'VCPU';
  /**
   * Name of the accelerator type or GPU resource. Specify this field only when
   * the type of hardware resource is ACCELERATOR.
   *
   * @var string
   */
  public $acceleratorType;
  /**
   * The quantity of the hardware resource that you want to commit to purchasing
   * (in a type-dependent unit).        - For vCPUs, you must specify an integer
   * value.    - For memory, you specify the amount of MB that you want. The
   * value you    specify must be a multiple of 256 MB, with up to 6.5 GB of
   * memory per every vCPU.    - For GPUs, you must specify an integer value.
   * - For Local SSD disks, you must specify the amount in GB. The size of a
   * single Local SSD disk is 375 GB.
   *
   * @var string
   */
  public $amount;
  /**
   * The type of hardware resource that you want to specify. You can specify any
   * of the following values:        - VCPU    - MEMORY    - LOCAL_SSD    -
   * ACCELERATOR
   *
   * Specify as a separate entry in the list for each individual resource type.
   *
   * @var string
   */
  public $type;

  /**
   * Name of the accelerator type or GPU resource. Specify this field only when
   * the type of hardware resource is ACCELERATOR.
   *
   * @param string $acceleratorType
   */
  public function setAcceleratorType($acceleratorType)
  {
    $this->acceleratorType = $acceleratorType;
  }
  /**
   * @return string
   */
  public function getAcceleratorType()
  {
    return $this->acceleratorType;
  }
  /**
   * The quantity of the hardware resource that you want to commit to purchasing
   * (in a type-dependent unit).        - For vCPUs, you must specify an integer
   * value.    - For memory, you specify the amount of MB that you want. The
   * value you    specify must be a multiple of 256 MB, with up to 6.5 GB of
   * memory per every vCPU.    - For GPUs, you must specify an integer value.
   * - For Local SSD disks, you must specify the amount in GB. The size of a
   * single Local SSD disk is 375 GB.
   *
   * @param string $amount
   */
  public function setAmount($amount)
  {
    $this->amount = $amount;
  }
  /**
   * @return string
   */
  public function getAmount()
  {
    return $this->amount;
  }
  /**
   * The type of hardware resource that you want to specify. You can specify any
   * of the following values:        - VCPU    - MEMORY    - LOCAL_SSD    -
   * ACCELERATOR
   *
   * Specify as a separate entry in the list for each individual resource type.
   *
   * Accepted values: ACCELERATOR, LOCAL_SSD, MEMORY, UNSPECIFIED, VCPU
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ResourceCommitment::class, 'Google_Service_Compute_ResourceCommitment');
