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

class ComputeInstanceBackupProperties extends \Google\Collection
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
  protected $collection_key = 'serviceAccount';
  /**
   * Enables instances created based on these properties to send packets with
   * source IP addresses other than their own and receive packets with
   * destination IP addresses other than their own. If these instances will be
   * used as an IP gateway or it will be set as the next-hop in a Route
   * resource, specify `true`. If unsure, leave this set to `false`. See the
   * https://cloud.google.com/vpc/docs/using-routes#canipforward documentation
   * for more information.
   *
   * @var bool
   */
  public $canIpForward;
  /**
   * An optional text description for the instances that are created from these
   * properties.
   *
   * @var string
   */
  public $description;
  protected $diskType = AttachedDisk::class;
  protected $diskDataType = 'array';
  protected $guestAcceleratorType = AcceleratorConfig::class;
  protected $guestAcceleratorDataType = 'array';
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
   * properties.
   *
   * @var string
   */
  public $machineType;
  protected $metadataType = Metadata::class;
  protected $metadataDataType = '';
  /**
   * Minimum cpu/platform to be used by instances. The instance may be scheduled
   * on the specified or newer cpu/platform. Applicable values are the friendly
   * names of CPU platforms, such as `minCpuPlatform: Intel Haswell` or
   * `minCpuPlatform: Intel Sandy Bridge`. For more information, read
   * https://cloud.google.com/compute/docs/instances/specify-min-cpu-platform.
   *
   * @var string
   */
  public $minCpuPlatform;
  protected $networkInterfaceType = NetworkInterface::class;
  protected $networkInterfaceDataType = 'array';
  protected $schedulingType = Scheduling::class;
  protected $schedulingDataType = '';
  protected $serviceAccountType = ServiceAccount::class;
  protected $serviceAccountDataType = 'array';
  /**
   * The source instance used to create this backup. This can be a partial or
   * full URL to the resource. For example, the following are valid values: -htt
   * ps://www.googleapis.com/compute/v1/projects/project/zones/zone/instances/in
   * stance -projects/project/zones/zone/instances/instance
   *
   * @var string
   */
  public $sourceInstance;
  protected $tagsType = Tags::class;
  protected $tagsDataType = '';

  /**
   * Enables instances created based on these properties to send packets with
   * source IP addresses other than their own and receive packets with
   * destination IP addresses other than their own. If these instances will be
   * used as an IP gateway or it will be set as the next-hop in a Route
   * resource, specify `true`. If unsure, leave this set to `false`. See the
   * https://cloud.google.com/vpc/docs/using-routes#canipforward documentation
   * for more information.
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
   * @param AttachedDisk[] $disk
   */
  public function setDisk($disk)
  {
    $this->disk = $disk;
  }
  /**
   * @return AttachedDisk[]
   */
  public function getDisk()
  {
    return $this->disk;
  }
  /**
   * A list of guest accelerator cards' type and count to use for instances
   * created from these properties.
   *
   * @param AcceleratorConfig[] $guestAccelerator
   */
  public function setGuestAccelerator($guestAccelerator)
  {
    $this->guestAccelerator = $guestAccelerator;
  }
  /**
   * @return AcceleratorConfig[]
   */
  public function getGuestAccelerator()
  {
    return $this->guestAccelerator;
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
   * properties.
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
   * keys. See https://cloud.google.com/compute/docs/metadata/overview for more
   * information.
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
   * names of CPU platforms, such as `minCpuPlatform: Intel Haswell` or
   * `minCpuPlatform: Intel Sandy Bridge`. For more information, read
   * https://cloud.google.com/compute/docs/instances/specify-min-cpu-platform.
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
   * @param NetworkInterface[] $networkInterface
   */
  public function setNetworkInterface($networkInterface)
  {
    $this->networkInterface = $networkInterface;
  }
  /**
   * @return NetworkInterface[]
   */
  public function getNetworkInterface()
  {
    return $this->networkInterface;
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
   * @param ServiceAccount[] $serviceAccount
   */
  public function setServiceAccount($serviceAccount)
  {
    $this->serviceAccount = $serviceAccount;
  }
  /**
   * @return ServiceAccount[]
   */
  public function getServiceAccount()
  {
    return $this->serviceAccount;
  }
  /**
   * The source instance used to create this backup. This can be a partial or
   * full URL to the resource. For example, the following are valid values: -htt
   * ps://www.googleapis.com/compute/v1/projects/project/zones/zone/instances/in
   * stance -projects/project/zones/zone/instances/instance
   *
   * @param string $sourceInstance
   */
  public function setSourceInstance($sourceInstance)
  {
    $this->sourceInstance = $sourceInstance;
  }
  /**
   * @return string
   */
  public function getSourceInstance()
  {
    return $this->sourceInstance;
  }
  /**
   * A list of tags to apply to the instances that are created from these
   * properties. The tags identify valid sources or targets for network
   * firewalls. The setTags method can modify this list of tags. Each tag within
   * the list must comply with RFC1035 (https://www.ietf.org/rfc/rfc1035.txt).
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
class_alias(ComputeInstanceBackupProperties::class, 'Google_Service_Backupdr_ComputeInstanceBackupProperties');
