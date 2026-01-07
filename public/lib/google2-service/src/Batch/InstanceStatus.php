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

class InstanceStatus extends \Google\Model
{
  /**
   * Unspecified.
   */
  public const PROVISIONING_MODEL_PROVISIONING_MODEL_UNSPECIFIED = 'PROVISIONING_MODEL_UNSPECIFIED';
  /**
   * Standard VM.
   */
  public const PROVISIONING_MODEL_STANDARD = 'STANDARD';
  /**
   * SPOT VM.
   */
  public const PROVISIONING_MODEL_SPOT = 'SPOT';
  /**
   * Preemptible VM (PVM). Above SPOT VM is the preferable model for preemptible
   * VM instances: the old preemptible VM model (indicated by this field) is the
   * older model, and has been migrated to use the SPOT model as the underlying
   * technology. This old model will still be supported.
   *
   * @deprecated
   */
  public const PROVISIONING_MODEL_PREEMPTIBLE = 'PREEMPTIBLE';
  /**
   * Bound to the lifecycle of the reservation in which it is provisioned.
   */
  public const PROVISIONING_MODEL_RESERVATION_BOUND = 'RESERVATION_BOUND';
  /**
   * Instance is provisioned with DWS Flex Start and has limited max run
   * duration.
   */
  public const PROVISIONING_MODEL_FLEX_START = 'FLEX_START';
  protected $bootDiskType = Disk::class;
  protected $bootDiskDataType = '';
  /**
   * The Compute Engine machine type.
   *
   * @var string
   */
  public $machineType;
  /**
   * The VM instance provisioning model.
   *
   * @var string
   */
  public $provisioningModel;
  /**
   * The max number of tasks can be assigned to this instance type.
   *
   * @var string
   */
  public $taskPack;

  /**
   * The VM boot disk.
   *
   * @param Disk $bootDisk
   */
  public function setBootDisk(Disk $bootDisk)
  {
    $this->bootDisk = $bootDisk;
  }
  /**
   * @return Disk
   */
  public function getBootDisk()
  {
    return $this->bootDisk;
  }
  /**
   * The Compute Engine machine type.
   *
   * @param string $machineType
   */
  public function setMachineType($machineType)
  {
    $this->machineType = $machineType;
  }
  /**
   * @return string
   */
  public function getMachineType()
  {
    return $this->machineType;
  }
  /**
   * The VM instance provisioning model.
   *
   * Accepted values: PROVISIONING_MODEL_UNSPECIFIED, STANDARD, SPOT,
   * PREEMPTIBLE, RESERVATION_BOUND, FLEX_START
   *
   * @param self::PROVISIONING_MODEL_* $provisioningModel
   */
  public function setProvisioningModel($provisioningModel)
  {
    $this->provisioningModel = $provisioningModel;
  }
  /**
   * @return self::PROVISIONING_MODEL_*
   */
  public function getProvisioningModel()
  {
    return $this->provisioningModel;
  }
  /**
   * The max number of tasks can be assigned to this instance type.
   *
   * @param string $taskPack
   */
  public function setTaskPack($taskPack)
  {
    $this->taskPack = $taskPack;
  }
  /**
   * @return string
   */
  public function getTaskPack()
  {
    return $this->taskPack;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InstanceStatus::class, 'Google_Service_Batch_InstanceStatus');
