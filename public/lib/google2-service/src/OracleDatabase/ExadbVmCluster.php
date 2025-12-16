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

class ExadbVmCluster extends \Google\Model
{
  /**
   * Required. Immutable. The name of the backup OdbSubnet associated with the
   * ExadbVmCluster. Format: projects/{project}/locations/{location}/odbNetworks
   * /{odb_network}/odbSubnets/{odb_subnet}
   *
   * @var string
   */
  public $backupOdbSubnet;
  /**
   * Output only. The date and time that the ExadbVmCluster was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Required. Immutable. The display name for the ExadbVmCluster. The name does
   * not have to be unique within your project. The name must be 1-255
   * characters long and can only contain alphanumeric characters.
   *
   * @var string
   */
  public $displayName;
  /**
   * Output only. The ID of the subscription entitlement associated with the
   * ExadbVmCluster.
   *
   * @var string
   */
  public $entitlementId;
  /**
   * Output only. Immutable. The GCP Oracle zone where Oracle ExadbVmCluster is
   * hosted. Example: us-east4-b-r2. During creation, the system will pick the
   * zone assigned to the ExascaleDbStorageVault.
   *
   * @var string
   */
  public $gcpOracleZone;
  /**
   * Optional. The labels or tags associated with the ExadbVmCluster.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Identifier. The name of the ExadbVmCluster resource in the following
   * format:
   * projects/{project}/locations/{region}/exadbVmClusters/{exadb_vm_cluster}
   *
   * @var string
   */
  public $name;
  /**
   * Optional. Immutable. The name of the OdbNetwork associated with the
   * ExadbVmCluster. Format:
   * projects/{project}/locations/{location}/odbNetworks/{odb_network} It is
   * optional but if specified, this should match the parent ODBNetwork of the
   * OdbSubnet.
   *
   * @var string
   */
  public $odbNetwork;
  /**
   * Required. Immutable. The name of the OdbSubnet associated with the
   * ExadbVmCluster for IP allocation. Format: projects/{project}/locations/{loc
   * ation}/odbNetworks/{odb_network}/odbSubnets/{odb_subnet}
   *
   * @var string
   */
  public $odbSubnet;
  protected $propertiesType = ExadbVmClusterProperties::class;
  protected $propertiesDataType = '';

  /**
   * Required. Immutable. The name of the backup OdbSubnet associated with the
   * ExadbVmCluster. Format: projects/{project}/locations/{location}/odbNetworks
   * /{odb_network}/odbSubnets/{odb_subnet}
   *
   * @param string $backupOdbSubnet
   */
  public function setBackupOdbSubnet($backupOdbSubnet)
  {
    $this->backupOdbSubnet = $backupOdbSubnet;
  }
  /**
   * @return string
   */
  public function getBackupOdbSubnet()
  {
    return $this->backupOdbSubnet;
  }
  /**
   * Output only. The date and time that the ExadbVmCluster was created.
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
   * Required. Immutable. The display name for the ExadbVmCluster. The name does
   * not have to be unique within your project. The name must be 1-255
   * characters long and can only contain alphanumeric characters.
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
   * ExadbVmCluster.
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
   * Output only. Immutable. The GCP Oracle zone where Oracle ExadbVmCluster is
   * hosted. Example: us-east4-b-r2. During creation, the system will pick the
   * zone assigned to the ExascaleDbStorageVault.
   *
   * @param string $gcpOracleZone
   */
  public function setGcpOracleZone($gcpOracleZone)
  {
    $this->gcpOracleZone = $gcpOracleZone;
  }
  /**
   * @return string
   */
  public function getGcpOracleZone()
  {
    return $this->gcpOracleZone;
  }
  /**
   * Optional. The labels or tags associated with the ExadbVmCluster.
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
   * Identifier. The name of the ExadbVmCluster resource in the following
   * format:
   * projects/{project}/locations/{region}/exadbVmClusters/{exadb_vm_cluster}
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
   * Optional. Immutable. The name of the OdbNetwork associated with the
   * ExadbVmCluster. Format:
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
   * Required. Immutable. The name of the OdbSubnet associated with the
   * ExadbVmCluster for IP allocation. Format: projects/{project}/locations/{loc
   * ation}/odbNetworks/{odb_network}/odbSubnets/{odb_subnet}
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
   * Required. The properties of the ExadbVmCluster.
   *
   * @param ExadbVmClusterProperties $properties
   */
  public function setProperties(ExadbVmClusterProperties $properties)
  {
    $this->properties = $properties;
  }
  /**
   * @return ExadbVmClusterProperties
   */
  public function getProperties()
  {
    return $this->properties;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ExadbVmCluster::class, 'Google_Service_OracleDatabase_ExadbVmCluster');
