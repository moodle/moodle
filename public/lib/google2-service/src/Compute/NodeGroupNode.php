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

class NodeGroupNode extends \Google\Collection
{
  public const CPU_OVERCOMMIT_TYPE_CPU_OVERCOMMIT_TYPE_UNSPECIFIED = 'CPU_OVERCOMMIT_TYPE_UNSPECIFIED';
  public const CPU_OVERCOMMIT_TYPE_ENABLED = 'ENABLED';
  public const CPU_OVERCOMMIT_TYPE_NONE = 'NONE';
  public const STATUS_CREATING = 'CREATING';
  public const STATUS_DELETING = 'DELETING';
  public const STATUS_INVALID = 'INVALID';
  public const STATUS_READY = 'READY';
  public const STATUS_REPAIRING = 'REPAIRING';
  protected $collection_key = 'instances';
  protected $acceleratorsType = AcceleratorConfig::class;
  protected $acceleratorsDataType = 'array';
  protected $consumedResourcesType = InstanceConsumptionInfo::class;
  protected $consumedResourcesDataType = '';
  /**
   * CPU overcommit.
   *
   * @var string
   */
  public $cpuOvercommitType;
  protected $disksType = LocalDisk::class;
  protected $disksDataType = 'array';
  protected $instanceConsumptionDataType = InstanceConsumptionData::class;
  protected $instanceConsumptionDataDataType = 'array';
  /**
   * Instances scheduled on this node.
   *
   * @var string[]
   */
  public $instances;
  /**
   * The name of the node.
   *
   * @var string
   */
  public $name;
  /**
   * The type of this node.
   *
   * @var string
   */
  public $nodeType;
  /**
   * Output only. [Output Only] Reserved for future use.
   *
   * @var bool
   */
  public $satisfiesPzs;
  protected $serverBindingType = ServerBinding::class;
  protected $serverBindingDataType = '';
  /**
   * Server ID associated with this node.
   *
   * @var string
   */
  public $serverId;
  /**
   * @var string
   */
  public $status;
  protected $totalResourcesType = InstanceConsumptionInfo::class;
  protected $totalResourcesDataType = '';
  protected $upcomingMaintenanceType = UpcomingMaintenance::class;
  protected $upcomingMaintenanceDataType = '';

  /**
   * Accelerators for this node.
   *
   * @param AcceleratorConfig[] $accelerators
   */
  public function setAccelerators($accelerators)
  {
    $this->accelerators = $accelerators;
  }
  /**
   * @return AcceleratorConfig[]
   */
  public function getAccelerators()
  {
    return $this->accelerators;
  }
  /**
   * Output only. Node resources that are reserved by all instances.
   *
   * @param InstanceConsumptionInfo $consumedResources
   */
  public function setConsumedResources(InstanceConsumptionInfo $consumedResources)
  {
    $this->consumedResources = $consumedResources;
  }
  /**
   * @return InstanceConsumptionInfo
   */
  public function getConsumedResources()
  {
    return $this->consumedResources;
  }
  /**
   * CPU overcommit.
   *
   * Accepted values: CPU_OVERCOMMIT_TYPE_UNSPECIFIED, ENABLED, NONE
   *
   * @param self::CPU_OVERCOMMIT_TYPE_* $cpuOvercommitType
   */
  public function setCpuOvercommitType($cpuOvercommitType)
  {
    $this->cpuOvercommitType = $cpuOvercommitType;
  }
  /**
   * @return self::CPU_OVERCOMMIT_TYPE_*
   */
  public function getCpuOvercommitType()
  {
    return $this->cpuOvercommitType;
  }
  /**
   * Local disk configurations.
   *
   * @param LocalDisk[] $disks
   */
  public function setDisks($disks)
  {
    $this->disks = $disks;
  }
  /**
   * @return LocalDisk[]
   */
  public function getDisks()
  {
    return $this->disks;
  }
  /**
   * Output only. Instance data that shows consumed resources on the node.
   *
   * @param InstanceConsumptionData[] $instanceConsumptionData
   */
  public function setInstanceConsumptionData($instanceConsumptionData)
  {
    $this->instanceConsumptionData = $instanceConsumptionData;
  }
  /**
   * @return InstanceConsumptionData[]
   */
  public function getInstanceConsumptionData()
  {
    return $this->instanceConsumptionData;
  }
  /**
   * Instances scheduled on this node.
   *
   * @param string[] $instances
   */
  public function setInstances($instances)
  {
    $this->instances = $instances;
  }
  /**
   * @return string[]
   */
  public function getInstances()
  {
    return $this->instances;
  }
  /**
   * The name of the node.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * The type of this node.
   *
   * @param string $nodeType
   */
  public function setNodeType($nodeType)
  {
    $this->nodeType = $nodeType;
  }
  /**
   * @return string
   */
  public function getNodeType()
  {
    return $this->nodeType;
  }
  /**
   * Output only. [Output Only] Reserved for future use.
   *
   * @param bool $satisfiesPzs
   */
  public function setSatisfiesPzs($satisfiesPzs)
  {
    $this->satisfiesPzs = $satisfiesPzs;
  }
  /**
   * @return bool
   */
  public function getSatisfiesPzs()
  {
    return $this->satisfiesPzs;
  }
  /**
   * Binding properties for the physical server.
   *
   * @param ServerBinding $serverBinding
   */
  public function setServerBinding(ServerBinding $serverBinding)
  {
    $this->serverBinding = $serverBinding;
  }
  /**
   * @return ServerBinding
   */
  public function getServerBinding()
  {
    return $this->serverBinding;
  }
  /**
   * Server ID associated with this node.
   *
   * @param string $serverId
   */
  public function setServerId($serverId)
  {
    $this->serverId = $serverId;
  }
  /**
   * @return string
   */
  public function getServerId()
  {
    return $this->serverId;
  }
  /**
   * @param self::STATUS_* $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return self::STATUS_*
   */
  public function getStatus()
  {
    return $this->status;
  }
  /**
   * Output only. Total amount of available resources on the node.
   *
   * @param InstanceConsumptionInfo $totalResources
   */
  public function setTotalResources(InstanceConsumptionInfo $totalResources)
  {
    $this->totalResources = $totalResources;
  }
  /**
   * @return InstanceConsumptionInfo
   */
  public function getTotalResources()
  {
    return $this->totalResources;
  }
  /**
   * Output only. [Output Only] The information about an upcoming maintenance
   * event.
   *
   * @param UpcomingMaintenance $upcomingMaintenance
   */
  public function setUpcomingMaintenance(UpcomingMaintenance $upcomingMaintenance)
  {
    $this->upcomingMaintenance = $upcomingMaintenance;
  }
  /**
   * @return UpcomingMaintenance
   */
  public function getUpcomingMaintenance()
  {
    return $this->upcomingMaintenance;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(NodeGroupNode::class, 'Google_Service_Compute_NodeGroupNode');
