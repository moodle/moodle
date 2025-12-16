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

class CloudVmCluster extends \Google\Model
{
  /**
   * Optional. The name of the backup OdbSubnet associated with the VM Cluster.
   * Format: projects/{project}/locations/{location}/odbNetworks/{odb_network}/o
   * dbSubnets/{odb_subnet}
   *
   * @var string
   */
  public $backupOdbSubnet;
  /**
   * Optional. CIDR range of the backup subnet.
   *
   * @var string
   */
  public $backupSubnetCidr;
  /**
   * Optional. Network settings. CIDR to use for cluster IP allocation.
   *
   * @var string
   */
  public $cidr;
  /**
   * Output only. The date and time that the VM cluster was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. User friendly name for this resource.
   *
   * @var string
   */
  public $displayName;
  /**
   * Required. The name of the Exadata Infrastructure resource on which VM
   * cluster resource is created, in the following format: projects/{project}/lo
   * cations/{region}/cloudExadataInfrastuctures/{cloud_extradata_infrastructure
   * }
   *
   * @var string
   */
  public $exadataInfrastructure;
  /**
   * Output only. The GCP Oracle zone where Oracle CloudVmCluster is hosted.
   * This will be the same as the gcp_oracle_zone of the
   * CloudExadataInfrastructure. Example: us-east4-b-r2.
   *
   * @var string
   */
  public $gcpOracleZone;
  protected $identityConnectorType = IdentityConnector::class;
  protected $identityConnectorDataType = '';
  /**
   * Optional. Labels or tags associated with the VM Cluster.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Identifier. The name of the VM Cluster resource with the format:
   * projects/{project}/locations/{region}/cloudVmClusters/{cloud_vm_cluster}
   *
   * @var string
   */
  public $name;
  /**
   * Optional. The name of the VPC network. Format:
   * projects/{project}/global/networks/{network}
   *
   * @var string
   */
  public $network;
  /**
   * Optional. The name of the OdbNetwork associated with the VM Cluster.
   * Format: projects/{project}/locations/{location}/odbNetworks/{odb_network}
   * It is optional but if specified, this should match the parent ODBNetwork of
   * the odb_subnet and backup_odb_subnet.
   *
   * @var string
   */
  public $odbNetwork;
  /**
   * Optional. The name of the OdbSubnet associated with the VM Cluster for IP
   * allocation. Format: projects/{project}/locations/{location}/odbNetworks/{od
   * b_network}/odbSubnets/{odb_subnet}
   *
   * @var string
   */
  public $odbSubnet;
  protected $propertiesType = CloudVmClusterProperties::class;
  protected $propertiesDataType = '';

  /**
   * Optional. The name of the backup OdbSubnet associated with the VM Cluster.
   * Format: projects/{project}/locations/{location}/odbNetworks/{odb_network}/o
   * dbSubnets/{odb_subnet}
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
   * Optional. CIDR range of the backup subnet.
   *
   * @param string $backupSubnetCidr
   */
  public function setBackupSubnetCidr($backupSubnetCidr)
  {
    $this->backupSubnetCidr = $backupSubnetCidr;
  }
  /**
   * @return string
   */
  public function getBackupSubnetCidr()
  {
    return $this->backupSubnetCidr;
  }
  /**
   * Optional. Network settings. CIDR to use for cluster IP allocation.
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
   * Output only. The date and time that the VM cluster was created.
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
   * Optional. User friendly name for this resource.
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
   * Required. The name of the Exadata Infrastructure resource on which VM
   * cluster resource is created, in the following format: projects/{project}/lo
   * cations/{region}/cloudExadataInfrastuctures/{cloud_extradata_infrastructure
   * }
   *
   * @param string $exadataInfrastructure
   */
  public function setExadataInfrastructure($exadataInfrastructure)
  {
    $this->exadataInfrastructure = $exadataInfrastructure;
  }
  /**
   * @return string
   */
  public function getExadataInfrastructure()
  {
    return $this->exadataInfrastructure;
  }
  /**
   * Output only. The GCP Oracle zone where Oracle CloudVmCluster is hosted.
   * This will be the same as the gcp_oracle_zone of the
   * CloudExadataInfrastructure. Example: us-east4-b-r2.
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
   * Output only. The identity connector details which will allow OCI to
   * securely access the resources in the customer project.
   *
   * @param IdentityConnector $identityConnector
   */
  public function setIdentityConnector(IdentityConnector $identityConnector)
  {
    $this->identityConnector = $identityConnector;
  }
  /**
   * @return IdentityConnector
   */
  public function getIdentityConnector()
  {
    return $this->identityConnector;
  }
  /**
   * Optional. Labels or tags associated with the VM Cluster.
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
   * Identifier. The name of the VM Cluster resource with the format:
   * projects/{project}/locations/{region}/cloudVmClusters/{cloud_vm_cluster}
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
   * Optional. The name of the VPC network. Format:
   * projects/{project}/global/networks/{network}
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
   * Optional. The name of the OdbNetwork associated with the VM Cluster.
   * Format: projects/{project}/locations/{location}/odbNetworks/{odb_network}
   * It is optional but if specified, this should match the parent ODBNetwork of
   * the odb_subnet and backup_odb_subnet.
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
   * Optional. The name of the OdbSubnet associated with the VM Cluster for IP
   * allocation. Format: projects/{project}/locations/{location}/odbNetworks/{od
   * b_network}/odbSubnets/{odb_subnet}
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
   * Optional. Various properties of the VM Cluster.
   *
   * @param CloudVmClusterProperties $properties
   */
  public function setProperties(CloudVmClusterProperties $properties)
  {
    $this->properties = $properties;
  }
  /**
   * @return CloudVmClusterProperties
   */
  public function getProperties()
  {
    return $this->properties;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CloudVmCluster::class, 'Google_Service_OracleDatabase_CloudVmCluster');
