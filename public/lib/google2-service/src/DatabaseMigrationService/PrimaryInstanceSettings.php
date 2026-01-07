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

namespace Google\Service\DatabaseMigrationService;

class PrimaryInstanceSettings extends \Google\Collection
{
  protected $collection_key = 'outboundPublicIpAddresses';
  /**
   * Database flags to pass to AlloyDB when DMS is creating the AlloyDB cluster
   * and instances. See the AlloyDB documentation for how these can be used.
   *
   * @var string[]
   */
  public $databaseFlags;
  /**
   * Required. The ID of the AlloyDB primary instance. The ID must satisfy the
   * regex expression "[a-z0-9-]+".
   *
   * @var string
   */
  public $id;
  protected $instanceNetworkConfigType = InstanceNetworkConfig::class;
  protected $instanceNetworkConfigDataType = '';
  /**
   * Labels for the AlloyDB primary instance created by DMS. An object
   * containing a list of 'key', 'value' pairs.
   *
   * @var string[]
   */
  public $labels;
  protected $machineConfigType = MachineConfig::class;
  protected $machineConfigDataType = '';
  /**
   * Output only. All outbound public IP addresses configured for the instance.
   *
   * @var string[]
   */
  public $outboundPublicIpAddresses;
  /**
   * Output only. The private IP address for the Instance. This is the
   * connection endpoint for an end-user application.
   *
   * @var string
   */
  public $privateIp;

  /**
   * Database flags to pass to AlloyDB when DMS is creating the AlloyDB cluster
   * and instances. See the AlloyDB documentation for how these can be used.
   *
   * @param string[] $databaseFlags
   */
  public function setDatabaseFlags($databaseFlags)
  {
    $this->databaseFlags = $databaseFlags;
  }
  /**
   * @return string[]
   */
  public function getDatabaseFlags()
  {
    return $this->databaseFlags;
  }
  /**
   * Required. The ID of the AlloyDB primary instance. The ID must satisfy the
   * regex expression "[a-z0-9-]+".
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
   * Optional. Metadata related to instance level network configuration.
   *
   * @param InstanceNetworkConfig $instanceNetworkConfig
   */
  public function setInstanceNetworkConfig(InstanceNetworkConfig $instanceNetworkConfig)
  {
    $this->instanceNetworkConfig = $instanceNetworkConfig;
  }
  /**
   * @return InstanceNetworkConfig
   */
  public function getInstanceNetworkConfig()
  {
    return $this->instanceNetworkConfig;
  }
  /**
   * Labels for the AlloyDB primary instance created by DMS. An object
   * containing a list of 'key', 'value' pairs.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Configuration for the machines that host the underlying database engine.
   *
   * @param MachineConfig $machineConfig
   */
  public function setMachineConfig(MachineConfig $machineConfig)
  {
    $this->machineConfig = $machineConfig;
  }
  /**
   * @return MachineConfig
   */
  public function getMachineConfig()
  {
    return $this->machineConfig;
  }
  /**
   * Output only. All outbound public IP addresses configured for the instance.
   *
   * @param string[] $outboundPublicIpAddresses
   */
  public function setOutboundPublicIpAddresses($outboundPublicIpAddresses)
  {
    $this->outboundPublicIpAddresses = $outboundPublicIpAddresses;
  }
  /**
   * @return string[]
   */
  public function getOutboundPublicIpAddresses()
  {
    return $this->outboundPublicIpAddresses;
  }
  /**
   * Output only. The private IP address for the Instance. This is the
   * connection endpoint for an end-user application.
   *
   * @param string $privateIp
   */
  public function setPrivateIp($privateIp)
  {
    $this->privateIp = $privateIp;
  }
  /**
   * @return string
   */
  public function getPrivateIp()
  {
    return $this->privateIp;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PrimaryInstanceSettings::class, 'Google_Service_DatabaseMigrationService_PrimaryInstanceSettings');
