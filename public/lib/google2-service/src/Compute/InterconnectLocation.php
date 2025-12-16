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

class InterconnectLocation extends \Google\Collection
{
  public const CONTINENT_AFRICA = 'AFRICA';
  public const CONTINENT_ASIA_PAC = 'ASIA_PAC';
  public const CONTINENT_C_AFRICA = 'C_AFRICA';
  public const CONTINENT_C_ASIA_PAC = 'C_ASIA_PAC';
  public const CONTINENT_C_EUROPE = 'C_EUROPE';
  public const CONTINENT_C_NORTH_AMERICA = 'C_NORTH_AMERICA';
  public const CONTINENT_C_SOUTH_AMERICA = 'C_SOUTH_AMERICA';
  public const CONTINENT_EUROPE = 'EUROPE';
  public const CONTINENT_NORTH_AMERICA = 'NORTH_AMERICA';
  public const CONTINENT_SOUTH_AMERICA = 'SOUTH_AMERICA';
  /**
   * The InterconnectLocation is available for provisioning new Interconnects.
   */
  public const STATUS_AVAILABLE = 'AVAILABLE';
  /**
   * The InterconnectLocation is closed for provisioning new Interconnects.
   */
  public const STATUS_CLOSED = 'CLOSED';
  protected $collection_key = 'singleRegionProductionCriticalPeerLocations';
  /**
   * Output only. [Output Only] The postal address of the Point of Presence,
   * each line in the address is separated by a newline character.
   *
   * @var string
   */
  public $address;
  /**
   * [Output Only] Availability zone for this InterconnectLocation. Within a
   * metropolitan area (metro), maintenance will not be simultaneously scheduled
   * in more than one availability zone.  Example: "zone1" or "zone2".
   *
   * @var string
   */
  public $availabilityZone;
  /**
   * [Output only] List of features available at this InterconnectLocation,
   * which can take one of the following values:        - IF_MACSEC    -
   * IF_CROSS_SITE_NETWORK
   *
   * @var string[]
   */
  public $availableFeatures;
  /**
   * [Output only] List of link types available at this InterconnectLocation,
   * which can take one of the following values:        -
   * LINK_TYPE_ETHERNET_10G_LR    - LINK_TYPE_ETHERNET_100G_LR    -
   * LINK_TYPE_ETHERNET_400G_LR4
   *
   * @var string[]
   */
  public $availableLinkTypes;
  /**
   * [Output Only] Metropolitan area designator that indicates which city an
   * interconnect is located. For example: "Chicago, IL", "Amsterdam,
   * Netherlands".
   *
   * @var string
   */
  public $city;
  /**
   * [Output Only] Continent for this location, which can take one of the
   * following values:        - AFRICA    - ASIA_PAC    - EUROPE    -
   * NORTH_AMERICA    - SOUTH_AMERICA
   *
   * @var string
   */
  public $continent;
  /**
   * Output only. [Output Only] Creation timestamp inRFC3339 text format.
   *
   * @var string
   */
  public $creationTimestamp;
  protected $crossSiteInterconnectInfosType = InterconnectLocationCrossSiteInterconnectInfo::class;
  protected $crossSiteInterconnectInfosDataType = 'array';
  /**
   * Output only. [Output Only] An optional description of the resource.
   *
   * @var string
   */
  public $description;
  /**
   * Output only. [Output Only] The name of the provider for this facility
   * (e.g., EQUINIX).
   *
   * @var string
   */
  public $facilityProvider;
  /**
   * Output only. [Output Only] A provider-assigned Identifier for this facility
   * (e.g., Ashburn-DC1).
   *
   * @var string
   */
  public $facilityProviderFacilityId;
  /**
   * Output only. [Output Only] The unique identifier for the resource. This
   * identifier is defined by the server.
   *
   * @var string
   */
  public $id;
  /**
   * Output only. [Output Only] Type of the resource.
   * Alwayscompute#interconnectLocation for interconnect locations.
   *
   * @var string
   */
  public $kind;
  /**
   * Output only. [Output Only] Name of the resource.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. [Output Only] The peeringdb identifier for this facility
   * (corresponding with a netfac type in peeringdb).
   *
   * @var string
   */
  public $peeringdbFacilityId;
  protected $regionInfosType = InterconnectLocationRegionInfo::class;
  protected $regionInfosDataType = 'array';
  /**
   * Output only. [Output Only] Server-defined URL for the resource.
   *
   * @var string
   */
  public $selfLink;
  /**
   * Output only. [Output Only] URLs of the other locations that can pair up
   * with this location to support Single-Region 99.99% SLA. E.g. iad-zone1-1
   * and iad-zone2-5467 are Single-Region 99.99% peer locations of each other.
   *
   * @var string[]
   */
  public $singleRegionProductionCriticalPeerLocations;
  /**
   * [Output Only] The status of this InterconnectLocation, which can take one
   * of the following values:        - CLOSED: The InterconnectLocation is
   * closed and is unavailable for    provisioning new Interconnects.    -
   * AVAILABLE: The InterconnectLocation is available for provisioning new
   * Interconnects.
   *
   * @var string
   */
  public $status;
  /**
   * Output only. [Output Only] Reserved for future use.
   *
   * @var bool
   */
  public $supportsPzs;

  /**
   * Output only. [Output Only] The postal address of the Point of Presence,
   * each line in the address is separated by a newline character.
   *
   * @param string $address
   */
  public function setAddress($address)
  {
    $this->address = $address;
  }
  /**
   * @return string
   */
  public function getAddress()
  {
    return $this->address;
  }
  /**
   * [Output Only] Availability zone for this InterconnectLocation. Within a
   * metropolitan area (metro), maintenance will not be simultaneously scheduled
   * in more than one availability zone.  Example: "zone1" or "zone2".
   *
   * @param string $availabilityZone
   */
  public function setAvailabilityZone($availabilityZone)
  {
    $this->availabilityZone = $availabilityZone;
  }
  /**
   * @return string
   */
  public function getAvailabilityZone()
  {
    return $this->availabilityZone;
  }
  /**
   * [Output only] List of features available at this InterconnectLocation,
   * which can take one of the following values:        - IF_MACSEC    -
   * IF_CROSS_SITE_NETWORK
   *
   * @param string[] $availableFeatures
   */
  public function setAvailableFeatures($availableFeatures)
  {
    $this->availableFeatures = $availableFeatures;
  }
  /**
   * @return string[]
   */
  public function getAvailableFeatures()
  {
    return $this->availableFeatures;
  }
  /**
   * [Output only] List of link types available at this InterconnectLocation,
   * which can take one of the following values:        -
   * LINK_TYPE_ETHERNET_10G_LR    - LINK_TYPE_ETHERNET_100G_LR    -
   * LINK_TYPE_ETHERNET_400G_LR4
   *
   * @param string[] $availableLinkTypes
   */
  public function setAvailableLinkTypes($availableLinkTypes)
  {
    $this->availableLinkTypes = $availableLinkTypes;
  }
  /**
   * @return string[]
   */
  public function getAvailableLinkTypes()
  {
    return $this->availableLinkTypes;
  }
  /**
   * [Output Only] Metropolitan area designator that indicates which city an
   * interconnect is located. For example: "Chicago, IL", "Amsterdam,
   * Netherlands".
   *
   * @param string $city
   */
  public function setCity($city)
  {
    $this->city = $city;
  }
  /**
   * @return string
   */
  public function getCity()
  {
    return $this->city;
  }
  /**
   * [Output Only] Continent for this location, which can take one of the
   * following values:        - AFRICA    - ASIA_PAC    - EUROPE    -
   * NORTH_AMERICA    - SOUTH_AMERICA
   *
   * Accepted values: AFRICA, ASIA_PAC, C_AFRICA, C_ASIA_PAC, C_EUROPE,
   * C_NORTH_AMERICA, C_SOUTH_AMERICA, EUROPE, NORTH_AMERICA, SOUTH_AMERICA
   *
   * @param self::CONTINENT_* $continent
   */
  public function setContinent($continent)
  {
    $this->continent = $continent;
  }
  /**
   * @return self::CONTINENT_*
   */
  public function getContinent()
  {
    return $this->continent;
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
   * [Output Only] A list of InterconnectLocation.CrossSiteInterconnectInfo
   * objects, that describe where Cross-Site Interconnect wires may connect to
   * from this location and associated connection parameters. Cross-Site
   * Interconnect isn't allowed to locations which are not listed.
   *
   * @param InterconnectLocationCrossSiteInterconnectInfo[] $crossSiteInterconnectInfos
   */
  public function setCrossSiteInterconnectInfos($crossSiteInterconnectInfos)
  {
    $this->crossSiteInterconnectInfos = $crossSiteInterconnectInfos;
  }
  /**
   * @return InterconnectLocationCrossSiteInterconnectInfo[]
   */
  public function getCrossSiteInterconnectInfos()
  {
    return $this->crossSiteInterconnectInfos;
  }
  /**
   * Output only. [Output Only] An optional description of the resource.
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
   * Output only. [Output Only] The name of the provider for this facility
   * (e.g., EQUINIX).
   *
   * @param string $facilityProvider
   */
  public function setFacilityProvider($facilityProvider)
  {
    $this->facilityProvider = $facilityProvider;
  }
  /**
   * @return string
   */
  public function getFacilityProvider()
  {
    return $this->facilityProvider;
  }
  /**
   * Output only. [Output Only] A provider-assigned Identifier for this facility
   * (e.g., Ashburn-DC1).
   *
   * @param string $facilityProviderFacilityId
   */
  public function setFacilityProviderFacilityId($facilityProviderFacilityId)
  {
    $this->facilityProviderFacilityId = $facilityProviderFacilityId;
  }
  /**
   * @return string
   */
  public function getFacilityProviderFacilityId()
  {
    return $this->facilityProviderFacilityId;
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
   * Alwayscompute#interconnectLocation for interconnect locations.
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
   * Output only. [Output Only] Name of the resource.
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
   * Output only. [Output Only] The peeringdb identifier for this facility
   * (corresponding with a netfac type in peeringdb).
   *
   * @param string $peeringdbFacilityId
   */
  public function setPeeringdbFacilityId($peeringdbFacilityId)
  {
    $this->peeringdbFacilityId = $peeringdbFacilityId;
  }
  /**
   * @return string
   */
  public function getPeeringdbFacilityId()
  {
    return $this->peeringdbFacilityId;
  }
  /**
   * Output only. [Output Only] A list of InterconnectLocation.RegionInfo
   * objects, that describe parameters pertaining to the relation between this
   * InterconnectLocation and various Google Cloud regions.
   *
   * @param InterconnectLocationRegionInfo[] $regionInfos
   */
  public function setRegionInfos($regionInfos)
  {
    $this->regionInfos = $regionInfos;
  }
  /**
   * @return InterconnectLocationRegionInfo[]
   */
  public function getRegionInfos()
  {
    return $this->regionInfos;
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
  /**
   * Output only. [Output Only] URLs of the other locations that can pair up
   * with this location to support Single-Region 99.99% SLA. E.g. iad-zone1-1
   * and iad-zone2-5467 are Single-Region 99.99% peer locations of each other.
   *
   * @param string[] $singleRegionProductionCriticalPeerLocations
   */
  public function setSingleRegionProductionCriticalPeerLocations($singleRegionProductionCriticalPeerLocations)
  {
    $this->singleRegionProductionCriticalPeerLocations = $singleRegionProductionCriticalPeerLocations;
  }
  /**
   * @return string[]
   */
  public function getSingleRegionProductionCriticalPeerLocations()
  {
    return $this->singleRegionProductionCriticalPeerLocations;
  }
  /**
   * [Output Only] The status of this InterconnectLocation, which can take one
   * of the following values:        - CLOSED: The InterconnectLocation is
   * closed and is unavailable for    provisioning new Interconnects.    -
   * AVAILABLE: The InterconnectLocation is available for provisioning new
   * Interconnects.
   *
   * Accepted values: AVAILABLE, CLOSED
   *
   * @param self::STATUS_* $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return self::STATUS_*
   */
  public function getStatus()
  {
    return $this->status;
  }
  /**
   * Output only. [Output Only] Reserved for future use.
   *
   * @param bool $supportsPzs
   */
  public function setSupportsPzs($supportsPzs)
  {
    $this->supportsPzs = $supportsPzs;
  }
  /**
   * @return bool
   */
  public function getSupportsPzs()
  {
    return $this->supportsPzs;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InterconnectLocation::class, 'Google_Service_Compute_InterconnectLocation');
