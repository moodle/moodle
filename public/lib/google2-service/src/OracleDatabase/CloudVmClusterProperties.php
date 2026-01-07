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

class CloudVmClusterProperties extends \Google\Collection
{
  /**
   * Unspecified compute model.
   */
  public const COMPUTE_MODEL_COMPUTE_MODEL_UNSPECIFIED = 'COMPUTE_MODEL_UNSPECIFIED';
  /**
   * Abstract measure of compute resources. ECPUs are based on the number of
   * cores elastically allocated from a pool of compute and storage servers.
   */
  public const COMPUTE_MODEL_COMPUTE_MODEL_ECPU = 'COMPUTE_MODEL_ECPU';
  /**
   * Physical measure of compute resources. OCPUs are based on the physical core
   * of a processor.
   */
  public const COMPUTE_MODEL_COMPUTE_MODEL_OCPU = 'COMPUTE_MODEL_OCPU';
  /**
   * Unspecified.
   */
  public const DISK_REDUNDANCY_DISK_REDUNDANCY_UNSPECIFIED = 'DISK_REDUNDANCY_UNSPECIFIED';
  /**
   * High - 3 way mirror.
   */
  public const DISK_REDUNDANCY_HIGH = 'HIGH';
  /**
   * Normal - 2 way mirror.
   */
  public const DISK_REDUNDANCY_NORMAL = 'NORMAL';
  /**
   * Unspecified
   */
  public const LICENSE_TYPE_LICENSE_TYPE_UNSPECIFIED = 'LICENSE_TYPE_UNSPECIFIED';
  /**
   * License included part of offer
   */
  public const LICENSE_TYPE_LICENSE_INCLUDED = 'LICENSE_INCLUDED';
  /**
   * Bring your own license
   */
  public const LICENSE_TYPE_BRING_YOUR_OWN_LICENSE = 'BRING_YOUR_OWN_LICENSE';
  /**
   * Default unspecified value.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Indicates that the resource is in provisioning state.
   */
  public const STATE_PROVISIONING = 'PROVISIONING';
  /**
   * Indicates that the resource is in available state.
   */
  public const STATE_AVAILABLE = 'AVAILABLE';
  /**
   * Indicates that the resource is in updating state.
   */
  public const STATE_UPDATING = 'UPDATING';
  /**
   * Indicates that the resource is in terminating state.
   */
  public const STATE_TERMINATING = 'TERMINATING';
  /**
   * Indicates that the resource is in terminated state.
   */
  public const STATE_TERMINATED = 'TERMINATED';
  /**
   * Indicates that the resource is in failed state.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * Indicates that the resource is in maintenance in progress state.
   */
  public const STATE_MAINTENANCE_IN_PROGRESS = 'MAINTENANCE_IN_PROGRESS';
  protected $collection_key = 'sshPublicKeys';
  /**
   * Optional. OCI Cluster name.
   *
   * @var string
   */
  public $clusterName;
  /**
   * Output only. Compartment ID of cluster.
   *
   * @var string
   */
  public $compartmentId;
  /**
   * Output only. The compute model of the VM Cluster.
   *
   * @var string
   */
  public $computeModel;
  /**
   * Required. Number of enabled CPU cores.
   *
   * @var int
   */
  public $cpuCoreCount;
  /**
   * Optional. The data disk group size to be allocated in TBs.
   *
   * @var 
   */
  public $dataStorageSizeTb;
  /**
   * Optional. Local storage per VM.
   *
   * @var int
   */
  public $dbNodeStorageSizeGb;
  /**
   * Optional. OCID of database servers.
   *
   * @var string[]
   */
  public $dbServerOcids;
  protected $diagnosticsDataCollectionOptionsType = DataCollectionOptions::class;
  protected $diagnosticsDataCollectionOptionsDataType = '';
  /**
   * Optional. The type of redundancy.
   *
   * @var string
   */
  public $diskRedundancy;
  /**
   * Output only. DNS listener IP.
   *
   * @var string
   */
  public $dnsListenerIp;
  /**
   * Output only. Parent DNS domain where SCAN DNS and hosts names are
   * qualified. ex: ocispdelegated.ocisp10jvnet.oraclevcn.com
   *
   * @var string
   */
  public $domain;
  /**
   * Optional. Grid Infrastructure Version.
   *
   * @var string
   */
  public $giVersion;
  /**
   * Output only. host name without domain. format: "-" with some suffix. ex:
   * sp2-yi0xq where "sp2" is the hostname_prefix.
   *
   * @var string
   */
  public $hostname;
  /**
   * Optional. Prefix for VM cluster host names.
   *
   * @var string
   */
  public $hostnamePrefix;
  /**
   * Required. License type of VM Cluster.
   *
   * @var string
   */
  public $licenseType;
  /**
   * Optional. Use local backup.
   *
   * @var bool
   */
  public $localBackupEnabled;
  /**
   * Optional. Memory allocated in GBs.
   *
   * @var int
   */
  public $memorySizeGb;
  /**
   * Optional. Number of database servers.
   *
   * @var int
   */
  public $nodeCount;
  /**
   * Output only. Deep link to the OCI console to view this resource.
   *
   * @var string
   */
  public $ociUrl;
  /**
   * Output only. Oracle Cloud Infrastructure ID of VM Cluster.
   *
   * @var string
   */
  public $ocid;
  /**
   * Optional. OCPU count per VM. Minimum is 0.1.
   *
   * @var float
   */
  public $ocpuCount;
  /**
   * Output only. SCAN DNS name. ex: sp2-yi0xq-
   * scan.ocispdelegated.ocisp10jvnet.oraclevcn.com
   *
   * @var string
   */
  public $scanDns;
  /**
   * Output only. OCID of scan DNS record.
   *
   * @var string
   */
  public $scanDnsRecordId;
  /**
   * Output only. OCIDs of scan IPs.
   *
   * @var string[]
   */
  public $scanIpIds;
  /**
   * Output only. SCAN listener port - TCP
   *
   * @var int
   */
  public $scanListenerPortTcp;
  /**
   * Output only. SCAN listener port - TLS
   *
   * @var int
   */
  public $scanListenerPortTcpSsl;
  /**
   * Output only. Shape of VM Cluster.
   *
   * @var string
   */
  public $shape;
  /**
   * Optional. Use exadata sparse snapshots.
   *
   * @var bool
   */
  public $sparseDiskgroupEnabled;
  /**
   * Optional. SSH public keys to be stored with cluster.
   *
   * @var string[]
   */
  public $sshPublicKeys;
  /**
   * Output only. State of the cluster.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. The storage allocation for the disk group, in gigabytes (GB).
   *
   * @var int
   */
  public $storageSizeGb;
  /**
   * Optional. Operating system version of the image.
   *
   * @var string
   */
  public $systemVersion;
  protected $timeZoneType = TimeZone::class;
  protected $timeZoneDataType = '';

  /**
   * Optional. OCI Cluster name.
   *
   * @param string $clusterName
   */
  public function setClusterName($clusterName)
  {
    $this->clusterName = $clusterName;
  }
  /**
   * @return string
   */
  public function getClusterName()
  {
    return $this->clusterName;
  }
  /**
   * Output only. Compartment ID of cluster.
   *
   * @param string $compartmentId
   */
  public function setCompartmentId($compartmentId)
  {
    $this->compartmentId = $compartmentId;
  }
  /**
   * @return string
   */
  public function getCompartmentId()
  {
    return $this->compartmentId;
  }
  /**
   * Output only. The compute model of the VM Cluster.
   *
   * Accepted values: COMPUTE_MODEL_UNSPECIFIED, COMPUTE_MODEL_ECPU,
   * COMPUTE_MODEL_OCPU
   *
   * @param self::COMPUTE_MODEL_* $computeModel
   */
  public function setComputeModel($computeModel)
  {
    $this->computeModel = $computeModel;
  }
  /**
   * @return self::COMPUTE_MODEL_*
   */
  public function getComputeModel()
  {
    return $this->computeModel;
  }
  /**
   * Required. Number of enabled CPU cores.
   *
   * @param int $cpuCoreCount
   */
  public function setCpuCoreCount($cpuCoreCount)
  {
    $this->cpuCoreCount = $cpuCoreCount;
  }
  /**
   * @return int
   */
  public function getCpuCoreCount()
  {
    return $this->cpuCoreCount;
  }
  public function setDataStorageSizeTb($dataStorageSizeTb)
  {
    $this->dataStorageSizeTb = $dataStorageSizeTb;
  }
  public function getDataStorageSizeTb()
  {
    return $this->dataStorageSizeTb;
  }
  /**
   * Optional. Local storage per VM.
   *
   * @param int $dbNodeStorageSizeGb
   */
  public function setDbNodeStorageSizeGb($dbNodeStorageSizeGb)
  {
    $this->dbNodeStorageSizeGb = $dbNodeStorageSizeGb;
  }
  /**
   * @return int
   */
  public function getDbNodeStorageSizeGb()
  {
    return $this->dbNodeStorageSizeGb;
  }
  /**
   * Optional. OCID of database servers.
   *
   * @param string[] $dbServerOcids
   */
  public function setDbServerOcids($dbServerOcids)
  {
    $this->dbServerOcids = $dbServerOcids;
  }
  /**
   * @return string[]
   */
  public function getDbServerOcids()
  {
    return $this->dbServerOcids;
  }
  /**
   * Optional. Data collection options for diagnostics.
   *
   * @param DataCollectionOptions $diagnosticsDataCollectionOptions
   */
  public function setDiagnosticsDataCollectionOptions(DataCollectionOptions $diagnosticsDataCollectionOptions)
  {
    $this->diagnosticsDataCollectionOptions = $diagnosticsDataCollectionOptions;
  }
  /**
   * @return DataCollectionOptions
   */
  public function getDiagnosticsDataCollectionOptions()
  {
    return $this->diagnosticsDataCollectionOptions;
  }
  /**
   * Optional. The type of redundancy.
   *
   * Accepted values: DISK_REDUNDANCY_UNSPECIFIED, HIGH, NORMAL
   *
   * @param self::DISK_REDUNDANCY_* $diskRedundancy
   */
  public function setDiskRedundancy($diskRedundancy)
  {
    $this->diskRedundancy = $diskRedundancy;
  }
  /**
   * @return self::DISK_REDUNDANCY_*
   */
  public function getDiskRedundancy()
  {
    return $this->diskRedundancy;
  }
  /**
   * Output only. DNS listener IP.
   *
   * @param string $dnsListenerIp
   */
  public function setDnsListenerIp($dnsListenerIp)
  {
    $this->dnsListenerIp = $dnsListenerIp;
  }
  /**
   * @return string
   */
  public function getDnsListenerIp()
  {
    return $this->dnsListenerIp;
  }
  /**
   * Output only. Parent DNS domain where SCAN DNS and hosts names are
   * qualified. ex: ocispdelegated.ocisp10jvnet.oraclevcn.com
   *
   * @param string $domain
   */
  public function setDomain($domain)
  {
    $this->domain = $domain;
  }
  /**
   * @return string
   */
  public function getDomain()
  {
    return $this->domain;
  }
  /**
   * Optional. Grid Infrastructure Version.
   *
   * @param string $giVersion
   */
  public function setGiVersion($giVersion)
  {
    $this->giVersion = $giVersion;
  }
  /**
   * @return string
   */
  public function getGiVersion()
  {
    return $this->giVersion;
  }
  /**
   * Output only. host name without domain. format: "-" with some suffix. ex:
   * sp2-yi0xq where "sp2" is the hostname_prefix.
   *
   * @param string $hostname
   */
  public function setHostname($hostname)
  {
    $this->hostname = $hostname;
  }
  /**
   * @return string
   */
  public function getHostname()
  {
    return $this->hostname;
  }
  /**
   * Optional. Prefix for VM cluster host names.
   *
   * @param string $hostnamePrefix
   */
  public function setHostnamePrefix($hostnamePrefix)
  {
    $this->hostnamePrefix = $hostnamePrefix;
  }
  /**
   * @return string
   */
  public function getHostnamePrefix()
  {
    return $this->hostnamePrefix;
  }
  /**
   * Required. License type of VM Cluster.
   *
   * Accepted values: LICENSE_TYPE_UNSPECIFIED, LICENSE_INCLUDED,
   * BRING_YOUR_OWN_LICENSE
   *
   * @param self::LICENSE_TYPE_* $licenseType
   */
  public function setLicenseType($licenseType)
  {
    $this->licenseType = $licenseType;
  }
  /**
   * @return self::LICENSE_TYPE_*
   */
  public function getLicenseType()
  {
    return $this->licenseType;
  }
  /**
   * Optional. Use local backup.
   *
   * @param bool $localBackupEnabled
   */
  public function setLocalBackupEnabled($localBackupEnabled)
  {
    $this->localBackupEnabled = $localBackupEnabled;
  }
  /**
   * @return bool
   */
  public function getLocalBackupEnabled()
  {
    return $this->localBackupEnabled;
  }
  /**
   * Optional. Memory allocated in GBs.
   *
   * @param int $memorySizeGb
   */
  public function setMemorySizeGb($memorySizeGb)
  {
    $this->memorySizeGb = $memorySizeGb;
  }
  /**
   * @return int
   */
  public function getMemorySizeGb()
  {
    return $this->memorySizeGb;
  }
  /**
   * Optional. Number of database servers.
   *
   * @param int $nodeCount
   */
  public function setNodeCount($nodeCount)
  {
    $this->nodeCount = $nodeCount;
  }
  /**
   * @return int
   */
  public function getNodeCount()
  {
    return $this->nodeCount;
  }
  /**
   * Output only. Deep link to the OCI console to view this resource.
   *
   * @param string $ociUrl
   */
  public function setOciUrl($ociUrl)
  {
    $this->ociUrl = $ociUrl;
  }
  /**
   * @return string
   */
  public function getOciUrl()
  {
    return $this->ociUrl;
  }
  /**
   * Output only. Oracle Cloud Infrastructure ID of VM Cluster.
   *
   * @param string $ocid
   */
  public function setOcid($ocid)
  {
    $this->ocid = $ocid;
  }
  /**
   * @return string
   */
  public function getOcid()
  {
    return $this->ocid;
  }
  /**
   * Optional. OCPU count per VM. Minimum is 0.1.
   *
   * @param float $ocpuCount
   */
  public function setOcpuCount($ocpuCount)
  {
    $this->ocpuCount = $ocpuCount;
  }
  /**
   * @return float
   */
  public function getOcpuCount()
  {
    return $this->ocpuCount;
  }
  /**
   * Output only. SCAN DNS name. ex: sp2-yi0xq-
   * scan.ocispdelegated.ocisp10jvnet.oraclevcn.com
   *
   * @param string $scanDns
   */
  public function setScanDns($scanDns)
  {
    $this->scanDns = $scanDns;
  }
  /**
   * @return string
   */
  public function getScanDns()
  {
    return $this->scanDns;
  }
  /**
   * Output only. OCID of scan DNS record.
   *
   * @param string $scanDnsRecordId
   */
  public function setScanDnsRecordId($scanDnsRecordId)
  {
    $this->scanDnsRecordId = $scanDnsRecordId;
  }
  /**
   * @return string
   */
  public function getScanDnsRecordId()
  {
    return $this->scanDnsRecordId;
  }
  /**
   * Output only. OCIDs of scan IPs.
   *
   * @param string[] $scanIpIds
   */
  public function setScanIpIds($scanIpIds)
  {
    $this->scanIpIds = $scanIpIds;
  }
  /**
   * @return string[]
   */
  public function getScanIpIds()
  {
    return $this->scanIpIds;
  }
  /**
   * Output only. SCAN listener port - TCP
   *
   * @param int $scanListenerPortTcp
   */
  public function setScanListenerPortTcp($scanListenerPortTcp)
  {
    $this->scanListenerPortTcp = $scanListenerPortTcp;
  }
  /**
   * @return int
   */
  public function getScanListenerPortTcp()
  {
    return $this->scanListenerPortTcp;
  }
  /**
   * Output only. SCAN listener port - TLS
   *
   * @param int $scanListenerPortTcpSsl
   */
  public function setScanListenerPortTcpSsl($scanListenerPortTcpSsl)
  {
    $this->scanListenerPortTcpSsl = $scanListenerPortTcpSsl;
  }
  /**
   * @return int
   */
  public function getScanListenerPortTcpSsl()
  {
    return $this->scanListenerPortTcpSsl;
  }
  /**
   * Output only. Shape of VM Cluster.
   *
   * @param string $shape
   */
  public function setShape($shape)
  {
    $this->shape = $shape;
  }
  /**
   * @return string
   */
  public function getShape()
  {
    return $this->shape;
  }
  /**
   * Optional. Use exadata sparse snapshots.
   *
   * @param bool $sparseDiskgroupEnabled
   */
  public function setSparseDiskgroupEnabled($sparseDiskgroupEnabled)
  {
    $this->sparseDiskgroupEnabled = $sparseDiskgroupEnabled;
  }
  /**
   * @return bool
   */
  public function getSparseDiskgroupEnabled()
  {
    return $this->sparseDiskgroupEnabled;
  }
  /**
   * Optional. SSH public keys to be stored with cluster.
   *
   * @param string[] $sshPublicKeys
   */
  public function setSshPublicKeys($sshPublicKeys)
  {
    $this->sshPublicKeys = $sshPublicKeys;
  }
  /**
   * @return string[]
   */
  public function getSshPublicKeys()
  {
    return $this->sshPublicKeys;
  }
  /**
   * Output only. State of the cluster.
   *
   * Accepted values: STATE_UNSPECIFIED, PROVISIONING, AVAILABLE, UPDATING,
   * TERMINATING, TERMINATED, FAILED, MAINTENANCE_IN_PROGRESS
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * Output only. The storage allocation for the disk group, in gigabytes (GB).
   *
   * @param int $storageSizeGb
   */
  public function setStorageSizeGb($storageSizeGb)
  {
    $this->storageSizeGb = $storageSizeGb;
  }
  /**
   * @return int
   */
  public function getStorageSizeGb()
  {
    return $this->storageSizeGb;
  }
  /**
   * Optional. Operating system version of the image.
   *
   * @param string $systemVersion
   */
  public function setSystemVersion($systemVersion)
  {
    $this->systemVersion = $systemVersion;
  }
  /**
   * @return string
   */
  public function getSystemVersion()
  {
    return $this->systemVersion;
  }
  /**
   * Optional. Time zone of VM Cluster to set. Defaults to UTC if not specified.
   *
   * @param TimeZone $timeZone
   */
  public function setTimeZone(TimeZone $timeZone)
  {
    $this->timeZone = $timeZone;
  }
  /**
   * @return TimeZone
   */
  public function getTimeZone()
  {
    return $this->timeZone;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CloudVmClusterProperties::class, 'Google_Service_OracleDatabase_CloudVmClusterProperties');
