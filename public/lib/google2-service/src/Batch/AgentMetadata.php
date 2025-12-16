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

class AgentMetadata extends \Google\Model
{
  /**
   * When the VM agent started. Use agent_startup_time instead.
   *
   * @deprecated
   * @var string
   */
  public $creationTime;
  /**
   * Full name of the entity that created this vm. For MIG, this path is:
   * projects/{project}/regions/{region}/InstanceGroupManagers/{igm} The value
   * is retrieved from the vm metadata key of "created-by".
   *
   * @var string
   */
  public $creator;
  /**
   * image version for the VM that this agent is installed on.
   *
   * @var string
   */
  public $imageVersion;
  /**
   * GCP instance name (go/instance-name).
   *
   * @var string
   */
  public $instance;
  /**
   * GCP instance ID (go/instance-id).
   *
   * @var string
   */
  public $instanceId;
  /**
   * If the GCP instance has received preemption notice.
   *
   * @var bool
   */
  public $instancePreemptionNoticeReceived;
  /**
   * Optional. machine type of the VM
   *
   * @var string
   */
  public $machineType;
  /**
   * parsed contents of /etc/os-release
   *
   * @var string[]
   */
  public $osRelease;
  /**
   * agent binary version running on VM
   *
   * @var string
   */
  public $version;
  /**
   * Agent zone.
   *
   * @var string
   */
  public $zone;

  /**
   * When the VM agent started. Use agent_startup_time instead.
   *
   * @deprecated
   * @param string $creationTime
   */
  public function setCreationTime($creationTime)
  {
    $this->creationTime = $creationTime;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getCreationTime()
  {
    return $this->creationTime;
  }
  /**
   * Full name of the entity that created this vm. For MIG, this path is:
   * projects/{project}/regions/{region}/InstanceGroupManagers/{igm} The value
   * is retrieved from the vm metadata key of "created-by".
   *
   * @param string $creator
   */
  public function setCreator($creator)
  {
    $this->creator = $creator;
  }
  /**
   * @return string
   */
  public function getCreator()
  {
    return $this->creator;
  }
  /**
   * image version for the VM that this agent is installed on.
   *
   * @param string $imageVersion
   */
  public function setImageVersion($imageVersion)
  {
    $this->imageVersion = $imageVersion;
  }
  /**
   * @return string
   */
  public function getImageVersion()
  {
    return $this->imageVersion;
  }
  /**
   * GCP instance name (go/instance-name).
   *
   * @param string $instance
   */
  public function setInstance($instance)
  {
    $this->instance = $instance;
  }
  /**
   * @return string
   */
  public function getInstance()
  {
    return $this->instance;
  }
  /**
   * GCP instance ID (go/instance-id).
   *
   * @param string $instanceId
   */
  public function setInstanceId($instanceId)
  {
    $this->instanceId = $instanceId;
  }
  /**
   * @return string
   */
  public function getInstanceId()
  {
    return $this->instanceId;
  }
  /**
   * If the GCP instance has received preemption notice.
   *
   * @param bool $instancePreemptionNoticeReceived
   */
  public function setInstancePreemptionNoticeReceived($instancePreemptionNoticeReceived)
  {
    $this->instancePreemptionNoticeReceived = $instancePreemptionNoticeReceived;
  }
  /**
   * @return bool
   */
  public function getInstancePreemptionNoticeReceived()
  {
    return $this->instancePreemptionNoticeReceived;
  }
  /**
   * Optional. machine type of the VM
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
   * parsed contents of /etc/os-release
   *
   * @param string[] $osRelease
   */
  public function setOsRelease($osRelease)
  {
    $this->osRelease = $osRelease;
  }
  /**
   * @return string[]
   */
  public function getOsRelease()
  {
    return $this->osRelease;
  }
  /**
   * agent binary version running on VM
   *
   * @param string $version
   */
  public function setVersion($version)
  {
    $this->version = $version;
  }
  /**
   * @return string
   */
  public function getVersion()
  {
    return $this->version;
  }
  /**
   * Agent zone.
   *
   * @param string $zone
   */
  public function setZone($zone)
  {
    $this->zone = $zone;
  }
  /**
   * @return string
   */
  public function getZone()
  {
    return $this->zone;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AgentMetadata::class, 'Google_Service_Batch_AgentMetadata');
