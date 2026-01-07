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

class InstancePolicy extends \Google\Collection
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
  protected $collection_key = 'disks';
  protected $acceleratorsType = Accelerator::class;
  protected $acceleratorsDataType = 'array';
  protected $bootDiskType = Disk::class;
  protected $bootDiskDataType = '';
  protected $disksType = AttachedDisk::class;
  protected $disksDataType = 'array';
  /**
   * The Compute Engine machine type.
   *
   * @var string
   */
  public $machineType;
  /**
   * The minimum CPU platform. See
   * https://cloud.google.com/compute/docs/instances/specify-min-cpu-platform.
   *
   * @var string
   */
  public $minCpuPlatform;
  /**
   * The provisioning model.
   *
   * @var string
   */
  public $provisioningModel;
  /**
   * Optional. If not specified (default), VMs will consume any applicable
   * reservation. If "NO_RESERVATION" is specified, VMs will not consume any
   * reservation. Otherwise, if specified, VMs will consume only the specified
   * reservation.
   *
   * @var string
   */
  public $reservation;

  /**
   * The accelerators attached to each VM instance.
   *
   * @param Accelerator[] $accelerators
   */
  public function setAccelerators($accelerators)
  {
    $this->accelerators = $accelerators;
  }
  /**
   * @return Accelerator[]
   */
  public function getAccelerators()
  {
    return $this->accelerators;
  }
  /**
   * Boot disk to be created and attached to each VM by this InstancePolicy.
   * Boot disk will be deleted when the VM is deleted. Batch API now only
   * supports booting from image.
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
   * Non-boot disks to be attached for each VM created by this InstancePolicy.
   * New disks will be deleted when the VM is deleted. A non-boot disk is a disk
   * that can be of a device with a file system or a raw storage drive that is
   * not ready for data storage and accessing.
   *
   * @param AttachedDisk[] $disks
   */
  public function setDisks($disks)
  {
    $this->disks = $disks;
  }
  /**
   * @return AttachedDisk[]
   */
  public function getDisks()
  {
    return $this->disks;
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
   * The minimum CPU platform. See
   * https://cloud.google.com/compute/docs/instances/specify-min-cpu-platform.
   *
   * @param string $minCpuPlatform
   */
  public function setMinCpuPlatform($minCpuPlatform)
  {
    $this->minCpuPlatform = $minCpuPlatform;
  }
  /**
   * @return string
   */
  public function getMinCpuPlatform()
  {
    return $this->minCpuPlatform;
  }
  /**
   * The provisioning model.
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
   * Optional. If not specified (default), VMs will consume any applicable
   * reservation. If "NO_RESERVATION" is specified, VMs will not consume any
   * reservation. Otherwise, if specified, VMs will consume only the specified
   * reservation.
   *
   * @param string $reservation
   */
  public function setReservation($reservation)
  {
    $this->reservation = $reservation;
  }
  /**
   * @return string
   */
  public function getReservation()
  {
    return $this->reservation;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InstancePolicy::class, 'Google_Service_Batch_InstancePolicy');
