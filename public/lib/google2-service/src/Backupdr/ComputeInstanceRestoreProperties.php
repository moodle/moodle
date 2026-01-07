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

namespace Google\Service\Backupdr;

class ComputeInstanceRestoreProperties extends \Google\Collection
{
  /**
   * Default value. This value is unused.
   */
  public const KEY_REVOCATION_ACTION_TYPE_KEY_REVOCATION_ACTION_TYPE_UNSPECIFIED = 'KEY_REVOCATION_ACTION_TYPE_UNSPECIFIED';
  /**
   * Indicates user chose no operation.
   */
  public const KEY_REVOCATION_ACTION_TYPE_NONE = 'NONE';
  /**
   * Indicates user chose to opt for VM shutdown on key revocation.
   */
  public const KEY_REVOCATION_ACTION_TYPE_STOP = 'STOP';
  /**
   * Default value. This value is unused.
   */
  public const PRIVATE_IPV6_GOOGLE_ACCESS_INSTANCE_PRIVATE_IPV6_GOOGLE_ACCESS_UNSPECIFIED = 'INSTANCE_PRIVATE_IPV6_GOOGLE_ACCESS_UNSPECIFIED';
  /**
   * Each network interface inherits PrivateIpv6GoogleAccess from its
   * subnetwork.
   */
  public const PRIVATE_IPV6_GOOGLE_ACCESS_INHERIT_FROM_SUBNETWORK = 'INHERIT_FROM_SUBNETWORK';
  /**
   * Outbound private IPv6 access from VMs in this subnet to Google services. If
   * specified, the subnetwork who is attached to the instance's default network
   * interface will be assigned an internal IPv6 prefix if it doesn't have
   * before.
   */
  public const PRIVATE_IPV6_GOOGLE_ACCESS_ENABLE_OUTBOUND_VM_ACCESS_TO_GOOGLE = 'ENABLE_OUTBOUND_VM_ACCESS_TO_GOOGLE';
  /**
   * Bidirectional private IPv6 access to/from Google services. If specified,
   * the subnetwork who is attached to the instance's default network interface
   * will be assigned an internal IPv6 prefix if it doesn't have before.
   */
  public const PRIVATE_IPV6_GOOGLE_ACCESS_ENABLE_BIDIRECTIONAL_ACCESS_TO_GOOGLE = 'ENABLE_BIDIRECTIONAL_ACCESS_TO_GOOGLE';
  protected $collection_key = 'serviceAccounts';
  protected $advancedMachineFeaturesType = AdvancedMachineFeatures::class;
  protected $advancedMachineFeaturesDataType = '';
  /**
   * Optional. Allows this instance to send and receive packets with non-
   * matching destination or source IPs.
   *
   * @var bool
   */
  public $canIpForward;
  protected $confidentialInstanceConfigType = ConfidentialInstanceConfig::class;
  protected $confidentialInstanceConfigDataType = '';
  /**
   * Optional. Whether the resource should be protected against deletion.
   *
   * @var bool
   */
  public $deletionProtection;
  /**
   * Optional. An optional description of this resource. Provide this property
   * when you create the resource.
   *
   * @var string
   */
  public $description;
  protected $disksType = AttachedDisk::class;
  protected $disksDataType = 'array';
  protected $displayDeviceType = DisplayDevice::class;
  protected $displayDeviceDataType = '';
  protected $guestAcceleratorsType = AcceleratorConfig::class;
  protected $guestAcceleratorsDataType = 'array';
  /**
   * Optional. Specifies the hostname of the instance. The specified hostname
   * must be RFC1035 compliant. If hostname is not specified, the default
   * hostname is [INSTANCE_NAME].c.[PROJECT_ID].internal when using the global
   * DNS, and [INSTANCE_NAME].[ZONE].c.[PROJECT_ID].internal when using zonal
   * DNS.
   *
   * @var string
   */
  public $hostname;
  protected $instanceEncryptionKeyType = CustomerEncryptionKey::class;
  protected $instanceEncryptionKeyDataType = '';
  /**
   * Optional. KeyRevocationActionType of the instance.
   *
   * @var string
   */
  public $keyRevocationActionType;
  /**
   * Optional. Labels to apply to this instance.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Optional. Full or partial URL of the machine type resource to use for this
   * instance.
   *
   * @var string
   */
  public $machineType;
  protected $metadataType = Metadata::class;
  protected $metadataDataType = '';
  /**
   * Optional. Minimum CPU platform to use for this instance.
   *
   * @var string
   */
  public $minCpuPlatform;
  /**
   * Required. Name of the compute instance.
   *
   * @var string
   */
  public $name;
  protected $networkInterfacesType = NetworkInterface::class;
  protected $networkInterfacesDataType = 'array';
  protected $networkPerformanceConfigType = NetworkPerformanceConfig::class;
  protected $networkPerformanceConfigDataType = '';
  protected $paramsType = InstanceParams::class;
  protected $paramsDataType = '';
  /**
   * Optional. The private IPv6 google access type for the VM. If not specified,
   * use INHERIT_FROM_SUBNETWORK as default.
   *
   * @var string
   */
  public $privateIpv6GoogleAccess;
  protected $reservationAffinityType = AllocationAffinity::class;
  protected $reservationAffinityDataType = '';
  /**
   * Optional. Resource policies applied to this instance. By default, no
   * resource policies will be applied.
   *
   * @var string[]
   */
  public $resourcePolicies;
  protected $schedulingType = Scheduling::class;
  protected $schedulingDataType = '';
  protected $serviceAccountsType = ServiceAccount::class;
  protected $serviceAccountsDataType = 'array';
  protected $tagsType = Tags::class;
  protected $tagsDataType = '';

  /**
   * Optional. Controls for advanced machine-related behavior features.
   *
   * @param AdvancedMachineFeatures $advancedMachineFeatures
   */
  public function setAdvancedMachineFeatures(AdvancedMachineFeatures $advancedMachineFeatures)
  {
    $this->advancedMachineFeatures = $advancedMachineFeatures;
  }
  /**
   * @return AdvancedMachineFeatures
   */
  public function getAdvancedMachineFeatures()
  {
    return $this->advancedMachineFeatures;
  }
  /**
   * Optional. Allows this instance to send and receive packets with non-
   * matching destination or source IPs.
   *
   * @param bool $canIpForward
   */
  public function setCanIpForward($canIpForward)
  {
    $this->canIpForward = $canIpForward;
  }
  /**
   * @return bool
   */
  public function getCanIpForward()
  {
    return $this->canIpForward;
  }
  /**
   * Optional. Controls Confidential compute options on the instance
   *
   * @param ConfidentialInstanceConfig $confidentialInstanceConfig
   */
  public function setConfidentialInstanceConfig(ConfidentialInstanceConfig $confidentialInstanceConfig)
  {
    $this->confidentialInstanceConfig = $confidentialInstanceConfig;
  }
  /**
   * @return ConfidentialInstanceConfig
   */
  public function getConfidentialInstanceConfig()
  {
    return $this->confidentialInstanceConfig;
  }
  /**
   * Optional. Whether the resource should be protected against deletion.
   *
   * @param bool $deletionProtection
   */
  public function setDeletionProtection($deletionProtection)
  {
    $this->deletionProtection = $deletionProtection;
  }
  /**
   * @return bool
   */
  public function getDeletionProtection()
  {
    return $this->deletionProtection;
  }
  /**
   * Optional. An optional description of this resource. Provide this property
   * when you create the resource.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Optional. Array of disks associated with this instance. Persistent disks
   * must be created before you can assign them. Source regional persistent
   * disks will be restored with default replica zones if not specified.
   *
   * @param AttachedDisk[] $disks
   */
  public function setDisks($disks)
  {
    $this->disks = $disks;
  }
  /**
   * @return AttachedDisk[]
   */
  public function getDisks()
  {
    return $this->disks;
  }
  /**
   * Optional. Enables display device for the instance.
   *
   * @param DisplayDevice $displayDevice
   */
  public function setDisplayDevice(DisplayDevice $displayDevice)
  {
    $this->displayDevice = $displayDevice;
  }
  /**
   * @return DisplayDevice
   */
  public function getDisplayDevice()
  {
    return $this->displayDevice;
  }
  /**
   * Optional. A list of the type and count of accelerator cards attached to the
   * instance.
   *
   * @param AcceleratorConfig[] $guestAccelerators
   */
  public function setGuestAccelerators($guestAccelerators)
  {
    $this->guestAccelerators = $guestAccelerators;
  }
  /**
   * @return AcceleratorConfig[]
   */
  public function getGuestAccelerators()
  {
    return $this->guestAccelerators;
  }
  /**
   * Optional. Specifies the hostname of the instance. The specified hostname
   * must be RFC1035 compliant. If hostname is not specified, the default
   * hostname is [INSTANCE_NAME].c.[PROJECT_ID].internal when using the global
   * DNS, and [INSTANCE_NAME].[ZONE].c.[PROJECT_ID].internal when using zonal
   * DNS.
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
   * Optional. Encrypts suspended data for an instance with a customer-managed
   * encryption key.
   *
   * @param CustomerEncryptionKey $instanceEncryptionKey
   */
  public function setInstanceEncryptionKey(CustomerEncryptionKey $instanceEncryptionKey)
  {
    $this->instanceEncryptionKey = $instanceEncryptionKey;
  }
  /**
   * @return CustomerEncryptionKey
   */
  public function getInstanceEncryptionKey()
  {
    return $this->instanceEncryptionKey;
  }
  /**
   * Optional. KeyRevocationActionType of the instance.
   *
   * Accepted values: KEY_REVOCATION_ACTION_TYPE_UNSPECIFIED, NONE, STOP
   *
   * @param self::KEY_REVOCATION_ACTION_TYPE_* $keyRevocationActionType
   */
  public function setKeyRevocationActionType($keyRevocationActionType)
  {
    $this->keyRevocationActionType = $keyRevocationActionType;
  }
  /**
   * @return self::KEY_REVOCATION_ACTION_TYPE_*
   */
  public function getKeyRevocationActionType()
  {
    return $this->keyRevocationActionType;
  }
  /**
   * Optional. Labels to apply to this instance.
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
   * Optional. Full or partial URL of the machine type resource to use for this
   * instance.
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
   * Optional. This includes custom metadata and predefined keys.
   *
   * @param Metadata $metadata
   */
  public function setMetadata(Metadata $metadata)
  {
    $this->metadata = $metadata;
  }
  /**
   * @return Metadata
   */
  public function getMetadata()
  {
    return $this->metadata;
  }
  /**
   * Optional. Minimum CPU platform to use for this instance.
   *
   * @param string $minCpuPlatform
   */
  public function setMinCpuPlatform($minCpuPlatform)
  {
    $this->minCpuPlatform = $minCpuPlatform;
  }
  /**
   * @return string
   */
  public function getMinCpuPlatform()
  {
    return $this->minCpuPlatform;
  }
  /**
   * Required. Name of the compute instance.
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
   * Optional. An array of network configurations for this instance. These
   * specify how interfaces are configured to interact with other network
   * services, such as connecting to the internet. Multiple interfaces are
   * supported per instance. Required to restore in different project or region.
   *
   * @param NetworkInterface[] $networkInterfaces
   */
  public function setNetworkInterfaces($networkInterfaces)
  {
    $this->networkInterfaces = $networkInterfaces;
  }
  /**
   * @return NetworkInterface[]
   */
  public function getNetworkInterfaces()
  {
    return $this->networkInterfaces;
  }
  /**
   * Optional. Configure network performance such as egress bandwidth tier.
   *
   * @param NetworkPerformanceConfig $networkPerformanceConfig
   */
  public function setNetworkPerformanceConfig(NetworkPerformanceConfig $networkPerformanceConfig)
  {
    $this->networkPerformanceConfig = $networkPerformanceConfig;
  }
  /**
   * @return NetworkPerformanceConfig
   */
  public function getNetworkPerformanceConfig()
  {
    return $this->networkPerformanceConfig;
  }
  /**
   * Input only. Additional params passed with the request, but not persisted as
   * part of resource payload.
   *
   * @param InstanceParams $params
   */
  public function setParams(InstanceParams $params)
  {
    $this->params = $params;
  }
  /**
   * @return InstanceParams
   */
  public function getParams()
  {
    return $this->params;
  }
  /**
   * Optional. The private IPv6 google access type for the VM. If not specified,
   * use INHERIT_FROM_SUBNETWORK as default.
   *
   * Accepted values: INSTANCE_PRIVATE_IPV6_GOOGLE_ACCESS_UNSPECIFIED,
   * INHERIT_FROM_SUBNETWORK, ENABLE_OUTBOUND_VM_ACCESS_TO_GOOGLE,
   * ENABLE_BIDIRECTIONAL_ACCESS_TO_GOOGLE
   *
   * @param self::PRIVATE_IPV6_GOOGLE_ACCESS_* $privateIpv6GoogleAccess
   */
  public function setPrivateIpv6GoogleAccess($privateIpv6GoogleAccess)
  {
    $this->privateIpv6GoogleAccess = $privateIpv6GoogleAccess;
  }
  /**
   * @return self::PRIVATE_IPV6_GOOGLE_ACCESS_*
   */
  public function getPrivateIpv6GoogleAccess()
  {
    return $this->privateIpv6GoogleAccess;
  }
  /**
   * Optional. Specifies the reservations that this instance can consume from.
   *
   * @param AllocationAffinity $reservationAffinity
   */
  public function setReservationAffinity(AllocationAffinity $reservationAffinity)
  {
    $this->reservationAffinity = $reservationAffinity;
  }
  /**
   * @return AllocationAffinity
   */
  public function getReservationAffinity()
  {
    return $this->reservationAffinity;
  }
  /**
   * Optional. Resource policies applied to this instance. By default, no
   * resource policies will be applied.
   *
   * @param string[] $resourcePolicies
   */
  public function setResourcePolicies($resourcePolicies)
  {
    $this->resourcePolicies = $resourcePolicies;
  }
  /**
   * @return string[]
   */
  public function getResourcePolicies()
  {
    return $this->resourcePolicies;
  }
  /**
   * Optional. Sets the scheduling options for this instance.
   *
   * @param Scheduling $scheduling
   */
  public function setScheduling(Scheduling $scheduling)
  {
    $this->scheduling = $scheduling;
  }
  /**
   * @return Scheduling
   */
  public function getScheduling()
  {
    return $this->scheduling;
  }
  /**
   * Optional. A list of service accounts, with their specified scopes,
   * authorized for this instance. Only one service account per VM instance is
   * supported.
   *
   * @param ServiceAccount[] $serviceAccounts
   */
  public function setServiceAccounts($serviceAccounts)
  {
    $this->serviceAccounts = $serviceAccounts;
  }
  /**
   * @return ServiceAccount[]
   */
  public function getServiceAccounts()
  {
    return $this->serviceAccounts;
  }
  /**
   * Optional. Tags to apply to this instance. Tags are used to identify valid
   * sources or targets for network firewalls and are specified by the client
   * during instance creation.
   *
   * @param Tags $tags
   */
  public function setTags(Tags $tags)
  {
    $this->tags = $tags;
  }
  /**
   * @return Tags
   */
  public function getTags()
  {
    return $this->tags;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ComputeInstanceRestoreProperties::class, 'Google_Service_Backupdr_ComputeInstanceRestoreProperties');
