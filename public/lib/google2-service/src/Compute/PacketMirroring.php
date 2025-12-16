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

class PacketMirroring extends \Google\Model
{
  public const ENABLE_FALSE = 'FALSE';
  public const ENABLE_TRUE = 'TRUE';
  protected $collectorIlbType = PacketMirroringForwardingRuleInfo::class;
  protected $collectorIlbDataType = '';
  /**
   * Output only. [Output Only] Creation timestamp inRFC3339 text format.
   *
   * @var string
   */
  public $creationTimestamp;
  /**
   * An optional description of this resource. Provide this property when you
   * create the resource.
   *
   * @var string
   */
  public $description;
  /**
   * Indicates whether or not this packet mirroring takes effect. If set to
   * FALSE, this packet mirroring policy will not be enforced on the network.
   *
   * The default is TRUE.
   *
   * @var string
   */
  public $enable;
  protected $filterType = PacketMirroringFilter::class;
  protected $filterDataType = '';
  /**
   * Output only. [Output Only] The unique identifier for the resource. This
   * identifier is defined by the server.
   *
   * @var string
   */
  public $id;
  /**
   * Output only. [Output Only] Type of the resource.
   * Alwayscompute#packetMirroring for packet mirrorings.
   *
   * @var string
   */
  public $kind;
  protected $mirroredResourcesType = PacketMirroringMirroredResourceInfo::class;
  protected $mirroredResourcesDataType = '';
  /**
   * Name of the resource; provided by the client when the resource is created.
   * The name must be 1-63 characters long, and comply withRFC1035.
   * Specifically, the name must be 1-63 characters long and match the regular
   * expression `[a-z]([-a-z0-9]*[a-z0-9])?` which means the first character
   * must be a lowercase letter, and all following characters must be a dash,
   * lowercase letter, or digit, except the last character, which cannot be a
   * dash.
   *
   * @var string
   */
  public $name;
  protected $networkType = PacketMirroringNetworkInfo::class;
  protected $networkDataType = '';
  /**
   * The priority of applying this configuration. Priority is used to break ties
   * in cases where there is more than one matching rule. In the case of two
   * rules that apply for a given Instance, the one with the lowest-numbered
   * priority value wins.
   *
   * Default value is 1000. Valid range is 0 through 65535.
   *
   * @var string
   */
  public $priority;
  /**
   * [Output Only] URI of the region where the packetMirroring resides.
   *
   * @var string
   */
  public $region;
  /**
   * Output only. [Output Only] Server-defined URL for the resource.
   *
   * @var string
   */
  public $selfLink;

  /**
   * The Forwarding Rule resource of typeloadBalancingScheme=INTERNAL that will
   * be used as collector for mirrored traffic. The specified forwarding rule
   * must have isMirroringCollector set to true.
   *
   * @param PacketMirroringForwardingRuleInfo $collectorIlb
   */
  public function setCollectorIlb(PacketMirroringForwardingRuleInfo $collectorIlb)
  {
    $this->collectorIlb = $collectorIlb;
  }
  /**
   * @return PacketMirroringForwardingRuleInfo
   */
  public function getCollectorIlb()
  {
    return $this->collectorIlb;
  }
  /**
   * Output only. [Output Only] Creation timestamp inRFC3339 text format.
   *
   * @param string $creationTimestamp
   */
  public function setCreationTimestamp($creationTimestamp)
  {
    $this->creationTimestamp = $creationTimestamp;
  }
  /**
   * @return string
   */
  public function getCreationTimestamp()
  {
    return $this->creationTimestamp;
  }
  /**
   * An optional description of this resource. Provide this property when you
   * create the resource.
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
   * Indicates whether or not this packet mirroring takes effect. If set to
   * FALSE, this packet mirroring policy will not be enforced on the network.
   *
   * The default is TRUE.
   *
   * Accepted values: FALSE, TRUE
   *
   * @param self::ENABLE_* $enable
   */
  public function setEnable($enable)
  {
    $this->enable = $enable;
  }
  /**
   * @return self::ENABLE_*
   */
  public function getEnable()
  {
    return $this->enable;
  }
  /**
   * Filter for mirrored traffic. If unspecified, all IPv4 traffic is mirrored.
   *
   * @param PacketMirroringFilter $filter
   */
  public function setFilter(PacketMirroringFilter $filter)
  {
    $this->filter = $filter;
  }
  /**
   * @return PacketMirroringFilter
   */
  public function getFilter()
  {
    return $this->filter;
  }
  /**
   * Output only. [Output Only] The unique identifier for the resource. This
   * identifier is defined by the server.
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
   * Output only. [Output Only] Type of the resource.
   * Alwayscompute#packetMirroring for packet mirrorings.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * PacketMirroring mirroredResourceInfos. MirroredResourceInfo specifies a set
   * of mirrored VM instances, subnetworks and/or tags for which traffic from/to
   * all VM instances will be mirrored.
   *
   * @param PacketMirroringMirroredResourceInfo $mirroredResources
   */
  public function setMirroredResources(PacketMirroringMirroredResourceInfo $mirroredResources)
  {
    $this->mirroredResources = $mirroredResources;
  }
  /**
   * @return PacketMirroringMirroredResourceInfo
   */
  public function getMirroredResources()
  {
    return $this->mirroredResources;
  }
  /**
   * Name of the resource; provided by the client when the resource is created.
   * The name must be 1-63 characters long, and comply withRFC1035.
   * Specifically, the name must be 1-63 characters long and match the regular
   * expression `[a-z]([-a-z0-9]*[a-z0-9])?` which means the first character
   * must be a lowercase letter, and all following characters must be a dash,
   * lowercase letter, or digit, except the last character, which cannot be a
   * dash.
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
   * Specifies the mirrored VPC network. Only packets in this network will be
   * mirrored. All mirrored VMs should have a NIC in the given network. All
   * mirrored subnetworks should belong to the given network.
   *
   * @param PacketMirroringNetworkInfo $network
   */
  public function setNetwork(PacketMirroringNetworkInfo $network)
  {
    $this->network = $network;
  }
  /**
   * @return PacketMirroringNetworkInfo
   */
  public function getNetwork()
  {
    return $this->network;
  }
  /**
   * The priority of applying this configuration. Priority is used to break ties
   * in cases where there is more than one matching rule. In the case of two
   * rules that apply for a given Instance, the one with the lowest-numbered
   * priority value wins.
   *
   * Default value is 1000. Valid range is 0 through 65535.
   *
   * @param string $priority
   */
  public function setPriority($priority)
  {
    $this->priority = $priority;
  }
  /**
   * @return string
   */
  public function getPriority()
  {
    return $this->priority;
  }
  /**
   * [Output Only] URI of the region where the packetMirroring resides.
   *
   * @param string $region
   */
  public function setRegion($region)
  {
    $this->region = $region;
  }
  /**
   * @return string
   */
  public function getRegion()
  {
    return $this->region;
  }
  /**
   * Output only. [Output Only] Server-defined URL for the resource.
   *
   * @param string $selfLink
   */
  public function setSelfLink($selfLink)
  {
    $this->selfLink = $selfLink;
  }
  /**
   * @return string
   */
  public function getSelfLink()
  {
    return $this->selfLink;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PacketMirroring::class, 'Google_Service_Compute_PacketMirroring');
