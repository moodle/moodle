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

namespace Google\Service\CloudWorkstations;

class BoostConfig extends \Google\Collection
{
  protected $collection_key = 'accelerators';
  protected $acceleratorsType = Accelerator::class;
  protected $acceleratorsDataType = 'array';
  /**
   * Optional. The size of the boot disk for the VM in gigabytes (GB). The
   * minimum boot disk size is `30` GB. Defaults to `50` GB.
   *
   * @var int
   */
  public $bootDiskSizeGb;
  /**
   * Optional. Whether to enable nested virtualization on boosted Cloud
   * Workstations VMs running using this boost configuration. Defaults to false.
   * Nested virtualization lets you run virtual machine (VM) instances inside
   * your workstation. Before enabling nested virtualization, consider the
   * following important considerations. Cloud Workstations instances are
   * subject to the [same restrictions as Compute Engine
   * instances](https://cloud.google.com/compute/docs/instances/nested-
   * virtualization/overview#restrictions): * **Organization policy**: projects,
   * folders, or organizations may be restricted from creating nested VMs if the
   * **Disable VM nested virtualization** constraint is enforced in the
   * organization policy. For more information, see the Compute Engine section,
   * [Checking whether nested virtualization is
   * allowed](https://cloud.google.com/compute/docs/instances/nested-
   * virtualization/managing-
   * constraint#checking_whether_nested_virtualization_is_allowed). *
   * **Performance**: nested VMs might experience a 10% or greater decrease in
   * performance for workloads that are CPU-bound and possibly greater than a
   * 10% decrease for workloads that are input/output bound. * **Machine Type**:
   * nested virtualization can only be enabled on boost configurations that
   * specify a machine_type in the N1 or N2 machine series.
   *
   * @var bool
   */
  public $enableNestedVirtualization;
  /**
   * Required. The ID to be used for the boost configuration.
   *
   * @var string
   */
  public $id;
  /**
   * Optional. The type of machine that boosted VM instances will use—for
   * example, `e2-standard-4`. For more information about machine types that
   * Cloud Workstations supports, see the list of [available machine
   * types](https://cloud.google.com/workstations/docs/available-machine-types).
   * Defaults to `e2-standard-4`.
   *
   * @var string
   */
  public $machineType;
  /**
   * Optional. The number of boost VMs that the system should keep idle so that
   * workstations can be boosted quickly. Defaults to `0`.
   *
   * @var int
   */
  public $poolSize;

  /**
   * Optional. A list of the type and count of accelerator cards attached to the
   * boost instance. Defaults to `none`.
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
   * Optional. The size of the boot disk for the VM in gigabytes (GB). The
   * minimum boot disk size is `30` GB. Defaults to `50` GB.
   *
   * @param int $bootDiskSizeGb
   */
  public function setBootDiskSizeGb($bootDiskSizeGb)
  {
    $this->bootDiskSizeGb = $bootDiskSizeGb;
  }
  /**
   * @return int
   */
  public function getBootDiskSizeGb()
  {
    return $this->bootDiskSizeGb;
  }
  /**
   * Optional. Whether to enable nested virtualization on boosted Cloud
   * Workstations VMs running using this boost configuration. Defaults to false.
   * Nested virtualization lets you run virtual machine (VM) instances inside
   * your workstation. Before enabling nested virtualization, consider the
   * following important considerations. Cloud Workstations instances are
   * subject to the [same restrictions as Compute Engine
   * instances](https://cloud.google.com/compute/docs/instances/nested-
   * virtualization/overview#restrictions): * **Organization policy**: projects,
   * folders, or organizations may be restricted from creating nested VMs if the
   * **Disable VM nested virtualization** constraint is enforced in the
   * organization policy. For more information, see the Compute Engine section,
   * [Checking whether nested virtualization is
   * allowed](https://cloud.google.com/compute/docs/instances/nested-
   * virtualization/managing-
   * constraint#checking_whether_nested_virtualization_is_allowed). *
   * **Performance**: nested VMs might experience a 10% or greater decrease in
   * performance for workloads that are CPU-bound and possibly greater than a
   * 10% decrease for workloads that are input/output bound. * **Machine Type**:
   * nested virtualization can only be enabled on boost configurations that
   * specify a machine_type in the N1 or N2 machine series.
   *
   * @param bool $enableNestedVirtualization
   */
  public function setEnableNestedVirtualization($enableNestedVirtualization)
  {
    $this->enableNestedVirtualization = $enableNestedVirtualization;
  }
  /**
   * @return bool
   */
  public function getEnableNestedVirtualization()
  {
    return $this->enableNestedVirtualization;
  }
  /**
   * Required. The ID to be used for the boost configuration.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Optional. The type of machine that boosted VM instances will use—for
   * example, `e2-standard-4`. For more information about machine types that
   * Cloud Workstations supports, see the list of [available machine
   * types](https://cloud.google.com/workstations/docs/available-machine-types).
   * Defaults to `e2-standard-4`.
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
   * Optional. The number of boost VMs that the system should keep idle so that
   * workstations can be boosted quickly. Defaults to `0`.
   *
   * @param int $poolSize
   */
  public function setPoolSize($poolSize)
  {
    $this->poolSize = $poolSize;
  }
  /**
   * @return int
   */
  public function getPoolSize()
  {
    return $this->poolSize;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BoostConfig::class, 'Google_Service_CloudWorkstations_BoostConfig');
