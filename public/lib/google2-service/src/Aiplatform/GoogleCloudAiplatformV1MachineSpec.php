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

class GoogleCloudAiplatformV1MachineSpec extends \Google\Model
{
  /**
   * Unspecified accelerator type, which means no accelerator.
   */
  public const ACCELERATOR_TYPE_ACCELERATOR_TYPE_UNSPECIFIED = 'ACCELERATOR_TYPE_UNSPECIFIED';
  /**
   * Deprecated: Nvidia Tesla K80 GPU has reached end of support, see
   * https://cloud.google.com/compute/docs/eol/k80-eol.
   *
   * @deprecated
   */
  public const ACCELERATOR_TYPE_NVIDIA_TESLA_K80 = 'NVIDIA_TESLA_K80';
  /**
   * Nvidia Tesla P100 GPU.
   */
  public const ACCELERATOR_TYPE_NVIDIA_TESLA_P100 = 'NVIDIA_TESLA_P100';
  /**
   * Nvidia Tesla V100 GPU.
   */
  public const ACCELERATOR_TYPE_NVIDIA_TESLA_V100 = 'NVIDIA_TESLA_V100';
  /**
   * Nvidia Tesla P4 GPU.
   */
  public const ACCELERATOR_TYPE_NVIDIA_TESLA_P4 = 'NVIDIA_TESLA_P4';
  /**
   * Nvidia Tesla T4 GPU.
   */
  public const ACCELERATOR_TYPE_NVIDIA_TESLA_T4 = 'NVIDIA_TESLA_T4';
  /**
   * Nvidia Tesla A100 GPU.
   */
  public const ACCELERATOR_TYPE_NVIDIA_TESLA_A100 = 'NVIDIA_TESLA_A100';
  /**
   * Nvidia A100 80GB GPU.
   */
  public const ACCELERATOR_TYPE_NVIDIA_A100_80GB = 'NVIDIA_A100_80GB';
  /**
   * Nvidia L4 GPU.
   */
  public const ACCELERATOR_TYPE_NVIDIA_L4 = 'NVIDIA_L4';
  /**
   * Nvidia H100 80Gb GPU.
   */
  public const ACCELERATOR_TYPE_NVIDIA_H100_80GB = 'NVIDIA_H100_80GB';
  /**
   * Nvidia H100 Mega 80Gb GPU.
   */
  public const ACCELERATOR_TYPE_NVIDIA_H100_MEGA_80GB = 'NVIDIA_H100_MEGA_80GB';
  /**
   * Nvidia H200 141Gb GPU.
   */
  public const ACCELERATOR_TYPE_NVIDIA_H200_141GB = 'NVIDIA_H200_141GB';
  /**
   * Nvidia B200 GPU.
   */
  public const ACCELERATOR_TYPE_NVIDIA_B200 = 'NVIDIA_B200';
  /**
   * Nvidia GB200 GPU.
   */
  public const ACCELERATOR_TYPE_NVIDIA_GB200 = 'NVIDIA_GB200';
  /**
   * Nvidia RTX Pro 6000 GPU.
   */
  public const ACCELERATOR_TYPE_NVIDIA_RTX_PRO_6000 = 'NVIDIA_RTX_PRO_6000';
  /**
   * TPU v2.
   */
  public const ACCELERATOR_TYPE_TPU_V2 = 'TPU_V2';
  /**
   * TPU v3.
   */
  public const ACCELERATOR_TYPE_TPU_V3 = 'TPU_V3';
  /**
   * TPU v4.
   */
  public const ACCELERATOR_TYPE_TPU_V4_POD = 'TPU_V4_POD';
  /**
   * TPU v5.
   */
  public const ACCELERATOR_TYPE_TPU_V5_LITEPOD = 'TPU_V5_LITEPOD';
  /**
   * The number of accelerators to attach to the machine. For accelerator
   * optimized machine types (https://cloud.google.com/compute/docs/accelerator-
   * optimized-machines), One may set the accelerator_count from 1 to N for
   * machine with N GPUs. If accelerator_count is less than or equal to N / 2,
   * Vertex will co-schedule the replicas of the model into the same VM to save
   * cost. For example, if the machine type is a3-highgpu-8g, which has 8 H100
   * GPUs, one can set accelerator_count to 1 to 8. If accelerator_count is 1,
   * 2, 3, or 4, Vertex will co-schedule 8, 4, 2, or 2 replicas of the model
   * into the same VM to save cost. When co-scheduling, CPU, memory and storage
   * on the VM will be distributed to replicas on the VM. For example, one can
   * expect a co-scheduled replica requesting 2 GPUs out of a 8-GPU VM will
   * receive 25% of the CPU, memory and storage of the VM. Note that the feature
   * is not compatible with multihost_gpu_node_count. When
   * multihost_gpu_node_count is set, the co-scheduling will not be enabled.
   *
   * @var int
   */
  public $acceleratorCount;
  /**
   * Immutable. The type of accelerator(s) that may be attached to the machine
   * as per accelerator_count.
   *
   * @var string
   */
  public $acceleratorType;
  /**
   * Optional. Immutable. The Nvidia GPU partition size. When specified, the
   * requested accelerators will be partitioned into smaller GPU partitions. For
   * example, if the request is for 8 units of NVIDIA A100 GPUs, and
   * gpu_partition_size="1g.10gb", the service will create 8 * 7 = 56
   * partitioned MIG instances. The partition size must be a value supported by
   * the requested accelerator. Refer to [Nvidia GPU
   * Partitioning](https://cloud.google.com/kubernetes-engine/docs/how-to/gpus-
   * multi#multi-instance_gpu_partitions) for the available partition sizes. If
   * set, the accelerator_count should be set to 1.
   *
   * @var string
   */
  public $gpuPartitionSize;
  /**
   * Immutable. The type of the machine. See the [list of machine types
   * supported for prediction](https://cloud.google.com/vertex-
   * ai/docs/predictions/configure-compute#machine-types) See the [list of
   * machine types supported for custom
   * training](https://cloud.google.com/vertex-ai/docs/training/configure-
   * compute#machine-types). For DeployedModel this field is optional, and the
   * default value is `n1-standard-2`. For BatchPredictionJob or as part of
   * WorkerPoolSpec this field is required.
   *
   * @var string
   */
  public $machineType;
  protected $reservationAffinityType = GoogleCloudAiplatformV1ReservationAffinity::class;
  protected $reservationAffinityDataType = '';
  /**
   * Immutable. The topology of the TPUs. Corresponds to the TPU topologies
   * available from GKE. (Example: tpu_topology: "2x2x1").
   *
   * @var string
   */
  public $tpuTopology;

  /**
   * The number of accelerators to attach to the machine. For accelerator
   * optimized machine types (https://cloud.google.com/compute/docs/accelerator-
   * optimized-machines), One may set the accelerator_count from 1 to N for
   * machine with N GPUs. If accelerator_count is less than or equal to N / 2,
   * Vertex will co-schedule the replicas of the model into the same VM to save
   * cost. For example, if the machine type is a3-highgpu-8g, which has 8 H100
   * GPUs, one can set accelerator_count to 1 to 8. If accelerator_count is 1,
   * 2, 3, or 4, Vertex will co-schedule 8, 4, 2, or 2 replicas of the model
   * into the same VM to save cost. When co-scheduling, CPU, memory and storage
   * on the VM will be distributed to replicas on the VM. For example, one can
   * expect a co-scheduled replica requesting 2 GPUs out of a 8-GPU VM will
   * receive 25% of the CPU, memory and storage of the VM. Note that the feature
   * is not compatible with multihost_gpu_node_count. When
   * multihost_gpu_node_count is set, the co-scheduling will not be enabled.
   *
   * @param int $acceleratorCount
   */
  public function setAcceleratorCount($acceleratorCount)
  {
    $this->acceleratorCount = $acceleratorCount;
  }
  /**
   * @return int
   */
  public function getAcceleratorCount()
  {
    return $this->acceleratorCount;
  }
  /**
   * Immutable. The type of accelerator(s) that may be attached to the machine
   * as per accelerator_count.
   *
   * Accepted values: ACCELERATOR_TYPE_UNSPECIFIED, NVIDIA_TESLA_K80,
   * NVIDIA_TESLA_P100, NVIDIA_TESLA_V100, NVIDIA_TESLA_P4, NVIDIA_TESLA_T4,
   * NVIDIA_TESLA_A100, NVIDIA_A100_80GB, NVIDIA_L4, NVIDIA_H100_80GB,
   * NVIDIA_H100_MEGA_80GB, NVIDIA_H200_141GB, NVIDIA_B200, NVIDIA_GB200,
   * NVIDIA_RTX_PRO_6000, TPU_V2, TPU_V3, TPU_V4_POD, TPU_V5_LITEPOD
   *
   * @param self::ACCELERATOR_TYPE_* $acceleratorType
   */
  public function setAcceleratorType($acceleratorType)
  {
    $this->acceleratorType = $acceleratorType;
  }
  /**
   * @return self::ACCELERATOR_TYPE_*
   */
  public function getAcceleratorType()
  {
    return $this->acceleratorType;
  }
  /**
   * Optional. Immutable. The Nvidia GPU partition size. When specified, the
   * requested accelerators will be partitioned into smaller GPU partitions. For
   * example, if the request is for 8 units of NVIDIA A100 GPUs, and
   * gpu_partition_size="1g.10gb", the service will create 8 * 7 = 56
   * partitioned MIG instances. The partition size must be a value supported by
   * the requested accelerator. Refer to [Nvidia GPU
   * Partitioning](https://cloud.google.com/kubernetes-engine/docs/how-to/gpus-
   * multi#multi-instance_gpu_partitions) for the available partition sizes. If
   * set, the accelerator_count should be set to 1.
   *
   * @param string $gpuPartitionSize
   */
  public function setGpuPartitionSize($gpuPartitionSize)
  {
    $this->gpuPartitionSize = $gpuPartitionSize;
  }
  /**
   * @return string
   */
  public function getGpuPartitionSize()
  {
    return $this->gpuPartitionSize;
  }
  /**
   * Immutable. The type of the machine. See the [list of machine types
   * supported for prediction](https://cloud.google.com/vertex-
   * ai/docs/predictions/configure-compute#machine-types) See the [list of
   * machine types supported for custom
   * training](https://cloud.google.com/vertex-ai/docs/training/configure-
   * compute#machine-types). For DeployedModel this field is optional, and the
   * default value is `n1-standard-2`. For BatchPredictionJob or as part of
   * WorkerPoolSpec this field is required.
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
   * Optional. Immutable. Configuration controlling how this resource pool
   * consumes reservation.
   *
   * @param GoogleCloudAiplatformV1ReservationAffinity $reservationAffinity
   */
  public function setReservationAffinity(GoogleCloudAiplatformV1ReservationAffinity $reservationAffinity)
  {
    $this->reservationAffinity = $reservationAffinity;
  }
  /**
   * @return GoogleCloudAiplatformV1ReservationAffinity
   */
  public function getReservationAffinity()
  {
    return $this->reservationAffinity;
  }
  /**
   * Immutable. The topology of the TPUs. Corresponds to the TPU topologies
   * available from GKE. (Example: tpu_topology: "2x2x1").
   *
   * @param string $tpuTopology
   */
  public function setTpuTopology($tpuTopology)
  {
    $this->tpuTopology = $tpuTopology;
  }
  /**
   * @return string
   */
  public function getTpuTopology()
  {
    return $this->tpuTopology;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1MachineSpec::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1MachineSpec');
