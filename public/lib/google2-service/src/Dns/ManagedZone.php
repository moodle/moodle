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

namespace Google\Service\Dns;

class ManagedZone extends \Google\Collection
{
  /**
   * Indicates that records in this zone can be queried from the public
   * internet.
   */
  public const VISIBILITY_public = 'public';
  /**
   * Indicates that records in this zone cannot be queried from the public
   * internet. Access to private zones depends on the zone configuration.
   */
  public const VISIBILITY_private = 'private';
  protected $collection_key = 'nameServers';
  protected $cloudLoggingConfigType = ManagedZoneCloudLoggingConfig::class;
  protected $cloudLoggingConfigDataType = '';
  /**
   * The time that this resource was created on the server. This is in RFC3339
   * text format. Output only.
   *
   * @var string
   */
  public $creationTime;
  /**
   * A mutable string of at most 1024 characters associated with this resource
   * for the user's convenience. Has no effect on the managed zone's function.
   *
   * @var string
   */
  public $description;
  /**
   * The DNS name of this managed zone, for instance "example.com.".
   *
   * @var string
   */
  public $dnsName;
  protected $dnssecConfigType = ManagedZoneDnsSecConfig::class;
  protected $dnssecConfigDataType = '';
  protected $forwardingConfigType = ManagedZoneForwardingConfig::class;
  protected $forwardingConfigDataType = '';
  /**
   * Unique identifier for the resource; defined by the server (output only)
   *
   * @var string
   */
  public $id;
  /**
   * @var string
   */
  public $kind;
  /**
   * User labels.
   *
   * @var string[]
   */
  public $labels;
  /**
   * User assigned name for this resource. Must be unique within the project.
   * The name must be 1-63 characters long, must begin with a letter, end with a
   * letter or digit, and only contain lowercase letters, digits or dashes.
   *
   * @var string
   */
  public $name;
  /**
   * Optionally specifies the NameServerSet for this ManagedZone. A
   * NameServerSet is a set of DNS name servers that all host the same
   * ManagedZones. Most users leave this field unset. If you need to use this
   * field, contact your account team.
   *
   * @var string
   */
  public $nameServerSet;
  /**
   * Delegate your managed_zone to these virtual name servers; defined by the
   * server (output only)
   *
   * @var string[]
   */
  public $nameServers;
  protected $peeringConfigType = ManagedZonePeeringConfig::class;
  protected $peeringConfigDataType = '';
  protected $privateVisibilityConfigType = ManagedZonePrivateVisibilityConfig::class;
  protected $privateVisibilityConfigDataType = '';
  protected $reverseLookupConfigType = ManagedZoneReverseLookupConfig::class;
  protected $reverseLookupConfigDataType = '';
  protected $serviceDirectoryConfigType = ManagedZoneServiceDirectoryConfig::class;
  protected $serviceDirectoryConfigDataType = '';
  /**
   * The zone's visibility: public zones are exposed to the Internet, while
   * private zones are visible only to Virtual Private Cloud resources.
   *
   * @var string
   */
  public $visibility;

  /**
   * @param ManagedZoneCloudLoggingConfig $cloudLoggingConfig
   */
  public function setCloudLoggingConfig(ManagedZoneCloudLoggingConfig $cloudLoggingConfig)
  {
    $this->cloudLoggingConfig = $cloudLoggingConfig;
  }
  /**
   * @return ManagedZoneCloudLoggingConfig
   */
  public function getCloudLoggingConfig()
  {
    return $this->cloudLoggingConfig;
  }
  /**
   * The time that this resource was created on the server. This is in RFC3339
   * text format. Output only.
   *
   * @param string $creationTime
   */
  public function setCreationTime($creationTime)
  {
    $this->creationTime = $creationTime;
  }
  /**
   * @return string
   */
  public function getCreationTime()
  {
    return $this->creationTime;
  }
  /**
   * A mutable string of at most 1024 characters associated with this resource
   * for the user's convenience. Has no effect on the managed zone's function.
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
   * The DNS name of this managed zone, for instance "example.com.".
   *
   * @param string $dnsName
   */
  public function setDnsName($dnsName)
  {
    $this->dnsName = $dnsName;
  }
  /**
   * @return string
   */
  public function getDnsName()
  {
    return $this->dnsName;
  }
  /**
   * DNSSEC configuration.
   *
   * @param ManagedZoneDnsSecConfig $dnssecConfig
   */
  public function setDnssecConfig(ManagedZoneDnsSecConfig $dnssecConfig)
  {
    $this->dnssecConfig = $dnssecConfig;
  }
  /**
   * @return ManagedZoneDnsSecConfig
   */
  public function getDnssecConfig()
  {
    return $this->dnssecConfig;
  }
  /**
   * The presence for this field indicates that outbound forwarding is enabled
   * for this zone. The value of this field contains the set of destinations to
   * forward to.
   *
   * @param ManagedZoneForwardingConfig $forwardingConfig
   */
  public function setForwardingConfig(ManagedZoneForwardingConfig $forwardingConfig)
  {
    $this->forwardingConfig = $forwardingConfig;
  }
  /**
   * @return ManagedZoneForwardingConfig
   */
  public function getForwardingConfig()
  {
    return $this->forwardingConfig;
  }
  /**
   * Unique identifier for the resource; defined by the server (output only)
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
   * User labels.
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
   * User assigned name for this resource. Must be unique within the project.
   * The name must be 1-63 characters long, must begin with a letter, end with a
   * letter or digit, and only contain lowercase letters, digits or dashes.
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
   * Optionally specifies the NameServerSet for this ManagedZone. A
   * NameServerSet is a set of DNS name servers that all host the same
   * ManagedZones. Most users leave this field unset. If you need to use this
   * field, contact your account team.
   *
   * @param string $nameServerSet
   */
  public function setNameServerSet($nameServerSet)
  {
    $this->nameServerSet = $nameServerSet;
  }
  /**
   * @return string
   */
  public function getNameServerSet()
  {
    return $this->nameServerSet;
  }
  /**
   * Delegate your managed_zone to these virtual name servers; defined by the
   * server (output only)
   *
   * @param string[] $nameServers
   */
  public function setNameServers($nameServers)
  {
    $this->nameServers = $nameServers;
  }
  /**
   * @return string[]
   */
  public function getNameServers()
  {
    return $this->nameServers;
  }
  /**
   * The presence of this field indicates that DNS Peering is enabled for this
   * zone. The value of this field contains the network to peer with.
   *
   * @param ManagedZonePeeringConfig $peeringConfig
   */
  public function setPeeringConfig(ManagedZonePeeringConfig $peeringConfig)
  {
    $this->peeringConfig = $peeringConfig;
  }
  /**
   * @return ManagedZonePeeringConfig
   */
  public function getPeeringConfig()
  {
    return $this->peeringConfig;
  }
  /**
   * For privately visible zones, the set of Virtual Private Cloud resources
   * that the zone is visible from.
   *
   * @param ManagedZonePrivateVisibilityConfig $privateVisibilityConfig
   */
  public function setPrivateVisibilityConfig(ManagedZonePrivateVisibilityConfig $privateVisibilityConfig)
  {
    $this->privateVisibilityConfig = $privateVisibilityConfig;
  }
  /**
   * @return ManagedZonePrivateVisibilityConfig
   */
  public function getPrivateVisibilityConfig()
  {
    return $this->privateVisibilityConfig;
  }
  /**
   * The presence of this field indicates that this is a managed reverse lookup
   * zone and Cloud DNS resolves reverse lookup queries using automatically
   * configured records for VPC resources. This only applies to networks listed
   * under private_visibility_config.
   *
   * @param ManagedZoneReverseLookupConfig $reverseLookupConfig
   */
  public function setReverseLookupConfig(ManagedZoneReverseLookupConfig $reverseLookupConfig)
  {
    $this->reverseLookupConfig = $reverseLookupConfig;
  }
  /**
   * @return ManagedZoneReverseLookupConfig
   */
  public function getReverseLookupConfig()
  {
    return $this->reverseLookupConfig;
  }
  /**
   * This field links to the associated service directory namespace. Do not set
   * this field for public zones or forwarding zones.
   *
   * @param ManagedZoneServiceDirectoryConfig $serviceDirectoryConfig
   */
  public function setServiceDirectoryConfig(ManagedZoneServiceDirectoryConfig $serviceDirectoryConfig)
  {
    $this->serviceDirectoryConfig = $serviceDirectoryConfig;
  }
  /**
   * @return ManagedZoneServiceDirectoryConfig
   */
  public function getServiceDirectoryConfig()
  {
    return $this->serviceDirectoryConfig;
  }
  /**
   * The zone's visibility: public zones are exposed to the Internet, while
   * private zones are visible only to Virtual Private Cloud resources.
   *
   * Accepted values: public, private
   *
   * @param self::VISIBILITY_* $visibility
   */
  public function setVisibility($visibility)
  {
    $this->visibility = $visibility;
  }
  /**
   * @return self::VISIBILITY_*
   */
  public function getVisibility()
  {
    return $this->visibility;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ManagedZone::class, 'Google_Service_Dns_ManagedZone');
