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

class GoogleCloudAiplatformV1NotebookExecutionJobCustomEnvironmentSpec extends \Google\Model
{
  protected $machineSpecType = GoogleCloudAiplatformV1MachineSpec::class;
  protected $machineSpecDataType = '';
  protected $networkSpecType = GoogleCloudAiplatformV1NetworkSpec::class;
  protected $networkSpecDataType = '';
  protected $persistentDiskSpecType = GoogleCloudAiplatformV1PersistentDiskSpec::class;
  protected $persistentDiskSpecDataType = '';

  /**
   * The specification of a single machine for the execution job.
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
   * The network configuration to use for the execution job.
   *
   * @param GoogleCloudAiplatformV1NetworkSpec $networkSpec
   */
  public function setNetworkSpec(GoogleCloudAiplatformV1NetworkSpec $networkSpec)
  {
    $this->networkSpec = $networkSpec;
  }
  /**
   * @return GoogleCloudAiplatformV1NetworkSpec
   */
  public function getNetworkSpec()
  {
    return $this->networkSpec;
  }
  /**
   * The specification of a persistent disk to attach for the execution job.
   *
   * @param GoogleCloudAiplatformV1PersistentDiskSpec $persistentDiskSpec
   */
  public function setPersistentDiskSpec(GoogleCloudAiplatformV1PersistentDiskSpec $persistentDiskSpec)
  {
    $this->persistentDiskSpec = $persistentDiskSpec;
  }
  /**
   * @return GoogleCloudAiplatformV1PersistentDiskSpec
   */
  public function getPersistentDiskSpec()
  {
    return $this->persistentDiskSpec;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1NotebookExecutionJobCustomEnvironmentSpec::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1NotebookExecutionJobCustomEnvironmentSpec');
