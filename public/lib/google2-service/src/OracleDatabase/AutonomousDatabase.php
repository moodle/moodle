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

namespace Google\Service\OracleDatabase;

class AutonomousDatabase extends \Google\Collection
{
  protected $collection_key = 'peerAutonomousDatabases';
  /**
   * Optional. The password for the default ADMIN user.
   *
   * @var string
   */
  public $adminPassword;
  /**
   * Optional. The subnet CIDR range for the Autonomous Database.
   *
   * @var string
   */
  public $cidr;
  /**
   * Output only. The date and time that the Autonomous Database was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. The name of the Autonomous Database. The database name must be
   * unique in the project. The name must begin with a letter and can contain a
   * maximum of 30 alphanumeric characters.
   *
   * @var string
   */
  public $database;
  /**
   * Output only. List of supported GCP region to clone the Autonomous Database
   * for disaster recovery. Format: `project/{project}/locations/{location}`.
   *
   * @var string[]
   */
  public $disasterRecoverySupportedLocations;
  /**
   * Optional. The display name for the Autonomous Database. The name does not
   * have to be unique within your project.
   *
   * @var string
   */
  public $displayName;
  /**
   * Output only. The ID of the subscription entitlement associated with the
   * Autonomous Database.
   *
   * @var string
   */
  public $entitlementId;
  /**
   * Optional. The labels or tags associated with the Autonomous Database.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Identifier. The name of the Autonomous Database resource in the following
   * format: projects/{project}/locations/{region}/autonomousDatabases/{autonomo
   * us_database}
   *
   * @var string
   */
  public $name;
  /**
   * Optional. The name of the VPC network used by the Autonomous Database in
   * the following format: projects/{project}/global/networks/{network}
   *
   * @var string
   */
  public $network;
  /**
   * Optional. The name of the OdbNetwork associated with the Autonomous
   * Database. Format:
   * projects/{project}/locations/{location}/odbNetworks/{odb_network} It is
   * optional but if specified, this should match the parent ODBNetwork of the
   * OdbSubnet.
   *
   * @var string
   */
  public $odbNetwork;
  /**
   * Optional. The name of the OdbSubnet associated with the Autonomous
   * Database. Format: projects/{project}/locations/{location}/odbNetworks/{odb_
   * network}/odbSubnets/{odb_subnet}
   *
   * @var string
   */
  public $odbSubnet;
  /**
   * Output only. The peer Autonomous Database names of the given Autonomous
   * Database.
   *
   * @var string[]
   */
  public $peerAutonomousDatabases;
  protected $propertiesType = AutonomousDatabaseProperties::class;
  protected $propertiesDataType = '';
  protected $sourceConfigType = SourceConfig::class;
  protected $sourceConfigDataType = '';

  /**
   * Optional. The password for the default ADMIN user.
   *
   * @param string $adminPassword
   */
  public function setAdminPassword($adminPassword)
  {
    $this->adminPassword = $adminPassword;
  }
  /**
   * @return string
   */
  public function getAdminPassword()
  {
    return $this->adminPassword;
  }
  /**
   * Optional. The subnet CIDR range for the Autonomous Database.
   *
   * @param string $cidr
   */
  public function setCidr($cidr)
  {
    $this->cidr = $cidr;
  }
  /**
   * @return string
   */
  public function getCidr()
  {
    return $this->cidr;
  }
  /**
   * Output only. The date and time that the Autonomous Database was created.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Optional. The name of the Autonomous Database. The database name must be
   * unique in the project. The name must begin with a letter and can contain a
   * maximum of 30 alphanumeric characters.
   *
   * @param string $database
   */
  public function setDatabase($database)
  {
    $this->database = $database;
  }
  /**
   * @return string
   */
  public function getDatabase()
  {
    return $this->database;
  }
  /**
   * Output only. List of supported GCP region to clone the Autonomous Database
   * for disaster recovery. Format: `project/{project}/locations/{location}`.
   *
   * @param string[] $disasterRecoverySupportedLocations
   */
  public function setDisasterRecoverySupportedLocations($disasterRecoverySupportedLocations)
  {
    $this->disasterRecoverySupportedLocations = $disasterRecoverySupportedLocations;
  }
  /**
   * @return string[]
   */
  public function getDisasterRecoverySupportedLocations()
  {
    return $this->disasterRecoverySupportedLocations;
  }
  /**
   * Optional. The display name for the Autonomous Database. The name does not
   * have to be unique within your project.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Output only. The ID of the subscription entitlement associated with the
   * Autonomous Database.
   *
   * @param string $entitlementId
   */
  public function setEntitlementId($entitlementId)
  {
    $this->entitlementId = $entitlementId;
  }
  /**
   * @return string
   */
  public function getEntitlementId()
  {
    return $this->entitlementId;
  }
  /**
   * Optional. The labels or tags associated with the Autonomous Database.
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
   * Identifier. The name of the Autonomous Database resource in the following
   * format: projects/{project}/locations/{region}/autonomousDatabases/{autonomo
   * us_database}
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
   * Optional. The name of the VPC network used by the Autonomous Database in
   * the following format: projects/{project}/global/networks/{network}
   *
   * @param string $network
   */
  public function setNetwork($network)
  {
    $this->network = $network;
  }
  /**
   * @return string
   */
  public function getNetwork()
  {
    return $this->network;
  }
  /**
   * Optional. The name of the OdbNetwork associated with the Autonomous
   * Database. Format:
   * projects/{project}/locations/{location}/odbNetworks/{odb_network} It is
   * optional but if specified, this should match the parent ODBNetwork of the
   * OdbSubnet.
   *
   * @param string $odbNetwork
   */
  public function setOdbNetwork($odbNetwork)
  {
    $this->odbNetwork = $odbNetwork;
  }
  /**
   * @return string
   */
  public function getOdbNetwork()
  {
    return $this->odbNetwork;
  }
  /**
   * Optional. The name of the OdbSubnet associated with the Autonomous
   * Database. Format: projects/{project}/locations/{location}/odbNetworks/{odb_
   * network}/odbSubnets/{odb_subnet}
   *
   * @param string $odbSubnet
   */
  public function setOdbSubnet($odbSubnet)
  {
    $this->odbSubnet = $odbSubnet;
  }
  /**
   * @return string
   */
  public function getOdbSubnet()
  {
    return $this->odbSubnet;
  }
  /**
   * Output only. The peer Autonomous Database names of the given Autonomous
   * Database.
   *
   * @param string[] $peerAutonomousDatabases
   */
  public function setPeerAutonomousDatabases($peerAutonomousDatabases)
  {
    $this->peerAutonomousDatabases = $peerAutonomousDatabases;
  }
  /**
   * @return string[]
   */
  public function getPeerAutonomousDatabases()
  {
    return $this->peerAutonomousDatabases;
  }
  /**
   * Optional. The properties of the Autonomous Database.
   *
   * @param AutonomousDatabaseProperties $properties
   */
  public function setProperties(AutonomousDatabaseProperties $properties)
  {
    $this->properties = $properties;
  }
  /**
   * @return AutonomousDatabaseProperties
   */
  public function getProperties()
  {
    return $this->properties;
  }
  /**
   * Optional. The source Autonomous Database configuration for the standby
   * Autonomous Database. The source Autonomous Database is configured while
   * creating the Peer Autonomous Database and can't be updated after creation.
   *
   * @param SourceConfig $sourceConfig
   */
  public function setSourceConfig(SourceConfig $sourceConfig)
  {
    $this->sourceConfig = $sourceConfig;
  }
  /**
   * @return SourceConfig
   */
  public function getSourceConfig()
  {
    return $this->sourceConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AutonomousDatabase::class, 'Google_Service_OracleDatabase_AutonomousDatabase');
