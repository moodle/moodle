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

class SourceInstanceProperties extends \Google\Collection
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
  protected $collection_key = 'serviceAccounts';
  /**
   * Enables instances created based on this machine image to send packets with
   * source IP addresses other than their own and receive packets with
   * destination IP addresses other than their own. If these instances will be
   * used as an IP gateway or it will be set as the next-hop in a Route
   * resource, specify true. If unsure, leave this set tofalse. See theEnable IP
   * forwarding documentation for more information.
   *
   * @var bool
   */
  public $canIpForward;
  /**
   * Whether the instance created from this machine image should be protected
   * against deletion.
   *
   * @var bool
   */
  public $deletionProtection;
  /**
   * An optional text description for the instances that are created from this
   * machine image.
   *
   * @var string
   */
  public $description;
  protected $disksType = SavedAttachedDisk::class;
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
   * Labels to apply to instances that are created from this machine image.
   *
   * @var string[]
   */
  public $labels;
  /**
   * The machine type to use for instances that are created from this machine
   * image.
   *
   * @var string
   */
  public $machineType;
  protected $metadataType = Metadata::class;
  protected $metadataDataType = '';
  /**
   * Minimum cpu/platform to be used by instances created from this machine
   * image. The instance may be scheduled on the specified or newer
   * cpu/platform. Applicable values are the friendly names of CPU platforms,
   * such as minCpuPlatform: "Intel Haswell" orminCpuPlatform: "Intel Sandy
   * Bridge". For more information, read Specifying a Minimum CPU Platform.
   *
   * @var string
   */
  public $minCpuPlatform;
  protected $networkInterfacesType = NetworkInterface::class;
  protected $networkInterfacesDataType = 'array';
  protected $schedulingType = Scheduling::class;
  protected $schedulingDataType = '';
  protected $serviceAccountsType = ServiceAccount::class;
  protected $serviceAccountsDataType = 'array';
  protected $tagsType = Tags::class;
  protected $tagsDataType = '';

  /**
   * Enables instances created based on this machine image to send packets with
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
   * Whether the instance created from this machine image should be protected
   * against deletion.
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
   * An optional text description for the instances that are created from this
   * machine image.
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
   * from this machine image.
   *
   * @param SavedAttachedDisk[] $disks
   */
  public function setDisks($disks)
  {
    $this->disks = $disks;
  }
  /**
   * @return SavedAttachedDisk[]
   */
  public function getDisks()
  {
    return $this->disks;
  }
  /**
   * A list of guest accelerator cards' type and count to use for instances
   * created from this machine image.
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
   * Labels to apply to instances that are created from this machine image.
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
   * The machine type to use for instances that are created from this machine
   * image.
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
   * this machine image. These pairs can consist of custom metadata or
   * predefined keys. SeeProject and instance metadata for more information.
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
   * Minimum cpu/platform to be used by instances created from this machine
   * image. The instance may be scheduled on the specified or newer
   * cpu/platform. Applicable values are the friendly names of CPU platforms,
   * such as minCpuPlatform: "Intel Haswell" orminCpuPlatform: "Intel Sandy
   * Bridge". For more information, read Specifying a Minimum CPU Platform.
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
   * Specifies the scheduling options for the instances that are created from
   * this machine image.
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
   * service accounts are available to the instances that are created from this
   * machine image. Use metadata queries to obtain the access tokens for these
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
   * A list of tags to apply to the instances that are created from this machine
   * image. The tags identify valid sources or targets for network firewalls.
   * The setTags method can modify this list of tags. Each tag within the list
   * must comply withRFC1035.
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
class_alias(SourceInstanceProperties::class, 'Google_Service_Compute_SourceInstanceProperties');
