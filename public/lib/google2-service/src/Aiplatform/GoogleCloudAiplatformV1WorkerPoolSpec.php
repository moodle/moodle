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

class GoogleCloudAiplatformV1WorkerPoolSpec extends \Google\Collection
{
  protected $collection_key = 'nfsMounts';
  protected $containerSpecType = GoogleCloudAiplatformV1ContainerSpec::class;
  protected $containerSpecDataType = '';
  protected $diskSpecType = GoogleCloudAiplatformV1DiskSpec::class;
  protected $diskSpecDataType = '';
  protected $lustreMountsType = GoogleCloudAiplatformV1LustreMount::class;
  protected $lustreMountsDataType = 'array';
  protected $machineSpecType = GoogleCloudAiplatformV1MachineSpec::class;
  protected $machineSpecDataType = '';
  protected $nfsMountsType = GoogleCloudAiplatformV1NfsMount::class;
  protected $nfsMountsDataType = 'array';
  protected $pythonPackageSpecType = GoogleCloudAiplatformV1PythonPackageSpec::class;
  protected $pythonPackageSpecDataType = '';
  /**
   * Optional. The number of worker replicas to use for this worker pool.
   *
   * @var string
   */
  public $replicaCount;

  /**
   * The custom container task.
   *
   * @param GoogleCloudAiplatformV1ContainerSpec $containerSpec
   */
  public function setContainerSpec(GoogleCloudAiplatformV1ContainerSpec $containerSpec)
  {
    $this->containerSpec = $containerSpec;
  }
  /**
   * @return GoogleCloudAiplatformV1ContainerSpec
   */
  public function getContainerSpec()
  {
    return $this->containerSpec;
  }
  /**
   * Disk spec.
   *
   * @param GoogleCloudAiplatformV1DiskSpec $diskSpec
   */
  public function setDiskSpec(GoogleCloudAiplatformV1DiskSpec $diskSpec)
  {
    $this->diskSpec = $diskSpec;
  }
  /**
   * @return GoogleCloudAiplatformV1DiskSpec
   */
  public function getDiskSpec()
  {
    return $this->diskSpec;
  }
  /**
   * Optional. List of Lustre mounts.
   *
   * @param GoogleCloudAiplatformV1LustreMount[] $lustreMounts
   */
  public function setLustreMounts($lustreMounts)
  {
    $this->lustreMounts = $lustreMounts;
  }
  /**
   * @return GoogleCloudAiplatformV1LustreMount[]
   */
  public function getLustreMounts()
  {
    return $this->lustreMounts;
  }
  /**
   * Optional. Immutable. The specification of a single machine.
   *
   * @param GoogleCloudAiplatformV1MachineSpec $machineSpec
   */
  public function setMachineSpec(GoogleCloudAiplatformV1MachineSpec $machineSpec)
  {
    $this->machineSpec = $machineSpec;
  }
  /**
   * @return GoogleCloudAiplatformV1MachineSpec
   */
  public function getMachineSpec()
  {
    return $this->machineSpec;
  }
  /**
   * Optional. List of NFS mount spec.
   *
   * @param GoogleCloudAiplatformV1NfsMount[] $nfsMounts
   */
  public function setNfsMounts($nfsMounts)
  {
    $this->nfsMounts = $nfsMounts;
  }
  /**
   * @return GoogleCloudAiplatformV1NfsMount[]
   */
  public function getNfsMounts()
  {
    return $this->nfsMounts;
  }
  /**
   * The Python packaged task.
   *
   * @param GoogleCloudAiplatformV1PythonPackageSpec $pythonPackageSpec
   */
  public function setPythonPackageSpec(GoogleCloudAiplatformV1PythonPackageSpec $pythonPackageSpec)
  {
    $this->pythonPackageSpec = $pythonPackageSpec;
  }
  /**
   * @return GoogleCloudAiplatformV1PythonPackageSpec
   */
  public function getPythonPackageSpec()
  {
    return $this->pythonPackageSpec;
  }
  /**
   * Optional. The number of worker replicas to use for this worker pool.
   *
   * @param string $replicaCount
   */
  public function setReplicaCount($replicaCount)
  {
    $this->replicaCount = $replicaCount;
  }
  /**
   * @return string
   */
  public function getReplicaCount()
  {
    return $this->replicaCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1WorkerPoolSpec::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1WorkerPoolSpec');
