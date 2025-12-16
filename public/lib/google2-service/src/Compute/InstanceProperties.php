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

class InstanceProperties extends \Google\Collection
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
   * Bidirectional private IPv6 access to/from Google services. If specified,
   * the subnetwork who is attached to the instance's default network interface
   * will be assigned an internal IPv6 prefix if it doesn't have before.
   */
  public const PRIVATE_IPV6_GOOGLE_ACCESS_ENABLE_BIDIRECTIONAL_ACCESS_TO_GOOGLE = 'ENABLE_BIDIRECTIONAL_ACCESS_TO_GOOGLE';
  /**
   * Outbound private IPv6 access from VMs in this subnet to Google services. If
   * specified, the subnetwork who is attached to the instance's default network
   * interface will be assigned an internal IPv6 prefix if it doesn't have
   * before.
   */
  public const PRIVATE_IPV6_GOOGLE_ACCESS_ENABLE_OUTBOUND_VM_ACCESS_TO_GOOGLE = 'ENABLE_OUTBOUND_VM_ACCESS_TO_GOOGLE';
  /**
   * Each network interface inherits PrivateIpv6GoogleAccess from its
   * subnetwork.
   */
  public const PRIVATE_IPV6_GOOGLE_ACCESS_INHERIT_FROM_SUBNETWORK = 'INHERIT_FROM_SUBNETWORK';
  protected $collection_key = 'serviceAccounts';
  protected $advancedMachineFeaturesType = AdvancedMachineFeatures::class;
  protected $advancedMachineFeaturesDataType = '';
  /**
   * Enables instances created based on these properties to send packets with
   * source IP addresses other than their own and receive packets with
   * destination IP addresses other than their own. If these instances will be
   * used as an IP gateway or it will be set as the next-hop in a Route
   * resource, specify true. If unsure, leave this set tofalse. See theEnable IP
   * forwarding documentation for more information.
   *
   * @var bool
   */
  public $canIpForward;
  protected $confidentialInstanceConfigType = ConfidentialInstanceConfig::class;
  protected $confidentialInstanceConfigDataType = '';
  /**
   * An optional text description for the instances that are created from these
   * properties.
   *
   * @var string
   */
  public $description;
  protected $disksType = AttachedDisk::class;
  protected $disksDataType = 'array';
  protected $guestAcceleratorsType = AcceleratorConfig::class;
  protected $guestAcceleratorsDataType = 'array';
  /**
   * KeyRevocationActionType of the instance. Supported options are "STOP" and
   * "NONE". The default value is "NONE" if it is not specified.
   *
   * @var string
   */
  public $keyRevocationActionType;
  /**
   * Labels to apply to instances that are created from these properties.
   *
   * @var string[]
   */
  public $labels;
  /**
   * The machine type to use for instances that are created from these
   * properties. This field only accepts a machine type name, for example
   * `n2-standard-4`. If you use the machine type full or partial URL, for
   * example `projects/my-l7ilb-project/zones/us-
   * central1-a/machineTypes/n2-standard-4`, the request will result in an
   * `INTERNAL_ERROR`.
   *
   * @var string
   */
  public $machineType;
  protected $metadataType = Metadata::class;
  protected $metadataDataType = '';
  /**
   * Minimum cpu/platform to be used by instances. The instance may be scheduled
   * on the specified or newer cpu/platform. Applicable values are the friendly
   * names of CPU platforms, such asminCpuPlatform: "Intel Haswell"
   * orminCpuPlatform: "Intel Sandy Bridge". For more information, read
   * Specifying a Minimum CPU Platform.
   *
   * @var string
   */
  public $minCpuPlatform;
  protected $networkInterfacesType = NetworkInterface::class;
  protected $networkInterfacesDataType = 'array';
  protected $networkPerformanceConfigType = NetworkPerformanceConfig::class;
  protected $networkPerformanceConfigDataType = '';
  /**
   * The private IPv6 google access type for VMs. If not specified, use
   * INHERIT_FROM_SUBNETWORK as default. Note that for MachineImage, this is not
   * supported yet.
   *
   * @var string
   */
  public $privateIpv6GoogleAccess;
  protected $reservationAffinityType = ReservationAffinity::class;
  protected $reservationAffinityDataType = '';
  /**
   * Resource manager tags to be bound to the instance. Tag keys and values have
   * the same definition as resource manager tags. Keys must be in the format
   * `tagKeys/{tag_key_id}`, and values are in the format `tagValues/456`. The
   * field is ignored (both PUT & PATCH) when empty.
   *
   * @var string[]
   */
  public $resourceManagerTags;
  /**
   * Resource policies (names, not URLs) applied to instances created from these
   * properties. Note that for MachineImage, this is not supported yet.
   *
   * @var string[]
   */
  public $resourcePolicies;
  protected $schedulingType = Scheduling::class;
  protected $schedulingDataType = '';
  protected $serviceAccountsType = ServiceAccount::class;
  protected $serviceAccountsDataType = 'array';
  protected $shieldedInstanceConfigType = ShieldedInstanceConfig::class;
  protected $shieldedInstanceConfigDataType = '';
  protected $tagsType = Tags::class;
  protected $tagsDataType = '';

  /**
   * Controls for advanced machine-related behavior features. Note that for
   * MachineImage, this is not supported yet.
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
   * Enables instances created based on these properties to send packets with
   * source IP addresses other than their own and receive packets with
   * destination IP addresses other than their own. If these instances will be
   * used as an IP gateway or it will be set as the next-hop in a Route
   * resource, specify true. If unsure, leave this set tofalse. See theEnable IP
   * forwarding documentation for more information.
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
   * Specifies the Confidential Instance options. Note that for MachineImage,
   * this is not supported yet.
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
   * An optional text description for the instances that are created from these
   * properties.
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
   * An array of disks that are associated with the instances that are created
   * from these properties.
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
   * A list of guest accelerator cards' type and count to use for instances
   * created from these properties.
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
   * KeyRevocationActionType of the instance. Supported options are "STOP" and
   * "NONE". The default value is "NONE" if it is not specified.
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
   * Labels to apply to instances that are created from these properties.
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
   * The machine type to use for instances that are created from these
   * properties. This field only accepts a machine type name, for example
   * `n2-standard-4`. If you use the machine type full or partial URL, for
   * example `projects/my-l7ilb-project/zones/us-
   * central1-a/machineTypes/n2-standard-4`, the request will result in an
   * `INTERNAL_ERROR`.
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
   * The metadata key/value pairs to assign to instances that are created from
   * these properties. These pairs can consist of custom metadata or predefined
   * keys. SeeProject and instance metadata for more information.
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
   * Minimum cpu/platform to be used by instances. The instance may be scheduled
   * on the specified or newer cpu/platform. Applicable values are the friendly
   * names of CPU platforms, such asminCpuPlatform: "Intel Haswell"
   * orminCpuPlatform: "Intel Sandy Bridge". For more information, read
   * Specifying a Minimum CPU Platform.
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
   * An array of network access configurations for this interface.
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
   * Note that for MachineImage, this is not supported yet.
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
   * The private IPv6 google access type for VMs. If not specified, use
   * INHERIT_FROM_SUBNETWORK as default. Note that for MachineImage, this is not
   * supported yet.
   *
   * Accepted values: ENABLE_BIDIRECTIONAL_ACCESS_TO_GOOGLE,
   * ENABLE_OUTBOUND_VM_ACCESS_TO_GOOGLE, INHERIT_FROM_SUBNETWORK
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
   * Specifies the reservations that instances can consume from. Note that for
   * MachineImage, this is not supported yet.
   *
   * @param ReservationAffinity $reservationAffinity
   */
  public function setReservationAffinity(ReservationAffinity $reservationAffinity)
  {
    $this->reservationAffinity = $reservationAffinity;
  }
  /**
   * @return ReservationAffinity
   */
  public function getReservationAffinity()
  {
    return $this->reservationAffinity;
  }
  /**
   * Resource manager tags to be bound to the instance. Tag keys and values have
   * the same definition as resource manager tags. Keys must be in the format
   * `tagKeys/{tag_key_id}`, and values are in the format `tagValues/456`. The
   * field is ignored (both PUT & PATCH) when empty.
   *
   * @param string[] $resourceManagerTags
   */
  public function setResourceManagerTags($resourceManagerTags)
  {
    $this->resourceManagerTags = $resourceManagerTags;
  }
  /**
   * @return string[]
   */
  public function getResourceManagerTags()
  {
    return $this->resourceManagerTags;
  }
  /**
   * Resource policies (names, not URLs) applied to instances created from these
   * properties. Note that for MachineImage, this is not supported yet.
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
   * Specifies the scheduling options for the instances that are created from
   * these properties.
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
   * A list of service accounts with specified scopes. Access tokens for these
   * service accounts are available to the instances that are created from these
   * properties. Use metadata queries to obtain the access tokens for these
   * instances.
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
   * Note that for MachineImage, this is not supported yet.
   *
   * @param ShieldedInstanceConfig $shieldedInstanceConfig
   */
  public function setShieldedInstanceConfig(ShieldedInstanceConfig $shieldedInstanceConfig)
  {
    $this->shieldedInstanceConfig = $shieldedInstanceConfig;
  }
  /**
   * @return ShieldedInstanceConfig
   */
  public function getShieldedInstanceConfig()
  {
    return $this->shieldedInstanceConfig;
  }
  /**
   * A list of tags to apply to the instances that are created from these
   * properties. The tags identify valid sources or targets for network
   * firewalls. The setTags method can modify this list of tags. Each tag within
   * the list must comply with RFC1035.
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
class_alias(InstanceProperties::class, 'Google_Service_Compute_InstanceProperties');
