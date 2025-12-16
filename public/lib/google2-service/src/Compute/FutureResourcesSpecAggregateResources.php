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

class FutureResourcesSpecAggregateResources extends \Google\Model
{
  public const VM_FAMILY_VM_FAMILY_CLOUD_TPU_DEVICE_CT3 = 'VM_FAMILY_CLOUD_TPU_DEVICE_CT3';
  public const VM_FAMILY_VM_FAMILY_CLOUD_TPU_LITE_DEVICE_CT5L = 'VM_FAMILY_CLOUD_TPU_LITE_DEVICE_CT5L';
  public const VM_FAMILY_VM_FAMILY_CLOUD_TPU_LITE_POD_SLICE_CT5LP = 'VM_FAMILY_CLOUD_TPU_LITE_POD_SLICE_CT5LP';
  public const VM_FAMILY_VM_FAMILY_CLOUD_TPU_LITE_POD_SLICE_CT6E = 'VM_FAMILY_CLOUD_TPU_LITE_POD_SLICE_CT6E';
  public const VM_FAMILY_VM_FAMILY_CLOUD_TPU_POD_SLICE_CT3P = 'VM_FAMILY_CLOUD_TPU_POD_SLICE_CT3P';
  public const VM_FAMILY_VM_FAMILY_CLOUD_TPU_POD_SLICE_CT4P = 'VM_FAMILY_CLOUD_TPU_POD_SLICE_CT4P';
  public const VM_FAMILY_VM_FAMILY_CLOUD_TPU_POD_SLICE_CT5P = 'VM_FAMILY_CLOUD_TPU_POD_SLICE_CT5P';
  public const VM_FAMILY_VM_FAMILY_CLOUD_TPU_POD_SLICE_TPU7X = 'VM_FAMILY_CLOUD_TPU_POD_SLICE_TPU7X';
  /**
   * Reserved resources will be optimized for BATCH workloads, such as ML
   * training.
   */
  public const WORKLOAD_TYPE_BATCH = 'BATCH';
  /**
   * Reserved resources will be optimized for SERVING workloads, such as ML
   * inference.
   */
  public const WORKLOAD_TYPE_SERVING = 'SERVING';
  public const WORKLOAD_TYPE_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * Size of the request, in accelerator (chip) count.
   *
   * @var string
   */
  public $acceleratorCount;
  /**
   * The VM family that all instances scheduled against this reservation must
   * belong to. Use for TPU reservations.
   *
   * @var string
   */
  public $vmFamily;
  /**
   * Workload type. Use for TPU reservations.
   *
   * @var string
   */
  public $workloadType;

  /**
   * Size of the request, in accelerator (chip) count.
   *
   * @param string $acceleratorCount
   */
  public function setAcceleratorCount($acceleratorCount)
  {
    $this->acceleratorCount = $acceleratorCount;
  }
  /**
   * @return string
   */
  public function getAcceleratorCount()
  {
    return $this->acceleratorCount;
  }
  /**
   * The VM family that all instances scheduled against this reservation must
   * belong to. Use for TPU reservations.
   *
   * Accepted values: VM_FAMILY_CLOUD_TPU_DEVICE_CT3,
   * VM_FAMILY_CLOUD_TPU_LITE_DEVICE_CT5L,
   * VM_FAMILY_CLOUD_TPU_LITE_POD_SLICE_CT5LP,
   * VM_FAMILY_CLOUD_TPU_LITE_POD_SLICE_CT6E,
   * VM_FAMILY_CLOUD_TPU_POD_SLICE_CT3P, VM_FAMILY_CLOUD_TPU_POD_SLICE_CT4P,
   * VM_FAMILY_CLOUD_TPU_POD_SLICE_CT5P, VM_FAMILY_CLOUD_TPU_POD_SLICE_TPU7X
   *
   * @param self::VM_FAMILY_* $vmFamily
   */
  public function setVmFamily($vmFamily)
  {
    $this->vmFamily = $vmFamily;
  }
  /**
   * @return self::VM_FAMILY_*
   */
  public function getVmFamily()
  {
    return $this->vmFamily;
  }
  /**
   * Workload type. Use for TPU reservations.
   *
   * Accepted values: BATCH, SERVING, UNSPECIFIED
   *
   * @param self::WORKLOAD_TYPE_* $workloadType
   */
  public function setWorkloadType($workloadType)
  {
    $this->workloadType = $workloadType;
  }
  /**
   * @return self::WORKLOAD_TYPE_*
   */
  public function getWorkloadType()
  {
    return $this->workloadType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FutureResourcesSpecAggregateResources::class, 'Google_Service_Compute_FutureResourcesSpecAggregateResources');
