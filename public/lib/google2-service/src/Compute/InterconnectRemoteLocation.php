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

class InterconnectRemoteLocation extends \Google\Collection
{
  public const CONTINENT_AFRICA = 'AFRICA';
  public const CONTINENT_ASIA_PAC = 'ASIA_PAC';
  public const CONTINENT_EUROPE = 'EUROPE';
  public const CONTINENT_NORTH_AMERICA = 'NORTH_AMERICA';
  public const CONTINENT_SOUTH_AMERICA = 'SOUTH_AMERICA';
  /**
   * LACP_SUPPORTED: LACP is supported, and enabled by default on the Cross-
   * Cloud Interconnect.
   */
  public const LACP_LACP_SUPPORTED = 'LACP_SUPPORTED';
  /**
   * LACP_UNSUPPORTED: LACP is not supported and is not be enabled on this port.
   * GetDiagnostics shows bundleAggregationType as "static". GCP does not
   * support LAGs without LACP, so requestedLinkCount must be 1.
   */
  public const LACP_LACP_UNSUPPORTED = 'LACP_UNSUPPORTED';
  /**
   * The InterconnectRemoteLocation is available for provisioning new Cross-
   * Cloud Interconnects.
   */
  public const STATUS_AVAILABLE = 'AVAILABLE';
  /**
   * The InterconnectRemoteLocation is closed for provisioning new Cross-Cloud
   * Interconnects.
   */
  public const STATUS_CLOSED = 'CLOSED';
  protected $collection_key = 'permittedConnections';
  /**
   * Output only. [Output Only] The postal address of the Point of Presence,
   * each line in the address is separated by a newline character.
   *
   * @var string
   */
  public $address;
  protected $attachmentConfigurationConstraintsType = InterconnectAttachmentConfigurationConstraints::class;
  protected $attachmentConfigurationConstraintsDataType = '';
  /**
   * Output only. [Output Only] Metropolitan area designator that indicates
   * which city an interconnect is located. For example: "Chicago, IL",
   * "Amsterdam, Netherlands".
   *
   * @var string
   */
  public $city;
  protected $constraintsType = InterconnectRemoteLocationConstraints::class;
  protected $constraintsDataType = '';
  /**
   * Output only. [Output Only] Continent for this location, which can take one
   * of the following values:        - AFRICA    - ASIA_PAC    - EUROPE    -
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
   * Alwayscompute#interconnectRemoteLocation for interconnect remote locations.
   *
   * @var string
   */
  public $kind;
  /**
   * Output only. [Output Only] Link Aggregation Control Protocol (LACP)
   * constraints, which can take one of the following values: LACP_SUPPORTED,
   * LACP_UNSUPPORTED
   *
   * @var string
   */
  public $lacp;
  /**
   * Output only. [Output Only] The maximum number of 100 Gbps ports supported
   * in a link aggregation group (LAG). When linkType is 100 Gbps,
   * requestedLinkCount cannot exceed max_lag_size_100_gbps.
   *
   * @var int
   */
  public $maxLagSize100Gbps;
  /**
   * Output only. [Output Only] The maximum number of 10 Gbps ports supported in
   * a link aggregation group (LAG). When linkType is 10 Gbps,
   * requestedLinkCount cannot exceed max_lag_size_10_gbps.
   *
   * @var int
   */
  public $maxLagSize10Gbps;
  /**
   * Output only. [Output Only] The maximum number of 400 Gbps ports supported
   * in a link aggregation group (LAG). When linkType is 400 Gbps,
   * requestedLinkCount cannot exceed max_lag_size_400_gbps.
   *
   * @var int
   */
  public $maxLagSize400Gbps;
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
  protected $permittedConnectionsType = InterconnectRemoteLocationPermittedConnections::class;
  protected $permittedConnectionsDataType = 'array';
  /**
   * Output only. [Output Only] Indicates the service provider present at the
   * remote location. Example values: "Amazon Web Services", "Microsoft Azure".
   *
   * @var string
   */
  public $remoteService;
  /**
   * Output only. [Output Only] Server-defined URL for the resource.
   *
   * @var string
   */
  public $selfLink;
  /**
   * Output only. [Output Only] The status of this InterconnectRemoteLocation,
   * which can take one of the following values:        - CLOSED: The
   * InterconnectRemoteLocation is closed and is unavailable    for provisioning
   * new Cross-Cloud Interconnects.     - AVAILABLE: The
   * InterconnectRemoteLocation is available for provisioning new    Cross-Cloud
   * Interconnects.
   *
   * @var string
   */
  public $status;

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
   * Output only. [Output Only] Subset of fields from InterconnectAttachment's
   * |configurationConstraints| field that apply to all attachments for this
   * remote location.
   *
   * @param InterconnectAttachmentConfigurationConstraints $attachmentConfigurationConstraints
   */
  public function setAttachmentConfigurationConstraints(InterconnectAttachmentConfigurationConstraints $attachmentConfigurationConstraints)
  {
    $this->attachmentConfigurationConstraints = $attachmentConfigurationConstraints;
  }
  /**
   * @return InterconnectAttachmentConfigurationConstraints
   */
  public function getAttachmentConfigurationConstraints()
  {
    return $this->attachmentConfigurationConstraints;
  }
  /**
   * Output only. [Output Only] Metropolitan area designator that indicates
   * which city an interconnect is located. For example: "Chicago, IL",
   * "Amsterdam, Netherlands".
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
   * Output only. [Output Only] Constraints on the parameters for creating
   * Cross-Cloud Interconnect and associated InterconnectAttachments.
   *
   * @param InterconnectRemoteLocationConstraints $constraints
   */
  public function setConstraints(InterconnectRemoteLocationConstraints $constraints)
  {
    $this->constraints = $constraints;
  }
  /**
   * @return InterconnectRemoteLocationConstraints
   */
  public function getConstraints()
  {
    return $this->constraints;
  }
  /**
   * Output only. [Output Only] Continent for this location, which can take one
   * of the following values:        - AFRICA    - ASIA_PAC    - EUROPE    -
   * NORTH_AMERICA    - SOUTH_AMERICA
   *
   * Accepted values: AFRICA, ASIA_PAC, EUROPE, NORTH_AMERICA, SOUTH_AMERICA
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
   * Alwayscompute#interconnectRemoteLocation for interconnect remote locations.
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
   * Output only. [Output Only] Link Aggregation Control Protocol (LACP)
   * constraints, which can take one of the following values: LACP_SUPPORTED,
   * LACP_UNSUPPORTED
   *
   * Accepted values: LACP_SUPPORTED, LACP_UNSUPPORTED
   *
   * @param self::LACP_* $lacp
   */
  public function setLacp($lacp)
  {
    $this->lacp = $lacp;
  }
  /**
   * @return self::LACP_*
   */
  public function getLacp()
  {
    return $this->lacp;
  }
  /**
   * Output only. [Output Only] The maximum number of 100 Gbps ports supported
   * in a link aggregation group (LAG). When linkType is 100 Gbps,
   * requestedLinkCount cannot exceed max_lag_size_100_gbps.
   *
   * @param int $maxLagSize100Gbps
   */
  public function setMaxLagSize100Gbps($maxLagSize100Gbps)
  {
    $this->maxLagSize100Gbps = $maxLagSize100Gbps;
  }
  /**
   * @return int
   */
  public function getMaxLagSize100Gbps()
  {
    return $this->maxLagSize100Gbps;
  }
  /**
   * Output only. [Output Only] The maximum number of 10 Gbps ports supported in
   * a link aggregation group (LAG). When linkType is 10 Gbps,
   * requestedLinkCount cannot exceed max_lag_size_10_gbps.
   *
   * @param int $maxLagSize10Gbps
   */
  public function setMaxLagSize10Gbps($maxLagSize10Gbps)
  {
    $this->maxLagSize10Gbps = $maxLagSize10Gbps;
  }
  /**
   * @return int
   */
  public function getMaxLagSize10Gbps()
  {
    return $this->maxLagSize10Gbps;
  }
  /**
   * Output only. [Output Only] The maximum number of 400 Gbps ports supported
   * in a link aggregation group (LAG). When linkType is 400 Gbps,
   * requestedLinkCount cannot exceed max_lag_size_400_gbps.
   *
   * @param int $maxLagSize400Gbps
   */
  public function setMaxLagSize400Gbps($maxLagSize400Gbps)
  {
    $this->maxLagSize400Gbps = $maxLagSize400Gbps;
  }
  /**
   * @return int
   */
  public function getMaxLagSize400Gbps()
  {
    return $this->maxLagSize400Gbps;
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
   * Output only. [Output Only] Permitted connections.
   *
   * @param InterconnectRemoteLocationPermittedConnections[] $permittedConnections
   */
  public function setPermittedConnections($permittedConnections)
  {
    $this->permittedConnections = $permittedConnections;
  }
  /**
   * @return InterconnectRemoteLocationPermittedConnections[]
   */
  public function getPermittedConnections()
  {
    return $this->permittedConnections;
  }
  /**
   * Output only. [Output Only] Indicates the service provider present at the
   * remote location. Example values: "Amazon Web Services", "Microsoft Azure".
   *
   * @param string $remoteService
   */
  public function setRemoteService($remoteService)
  {
    $this->remoteService = $remoteService;
  }
  /**
   * @return string
   */
  public function getRemoteService()
  {
    return $this->remoteService;
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
   * Output only. [Output Only] The status of this InterconnectRemoteLocation,
   * which can take one of the following values:        - CLOSED: The
   * InterconnectRemoteLocation is closed and is unavailable    for provisioning
   * new Cross-Cloud Interconnects.     - AVAILABLE: The
   * InterconnectRemoteLocation is available for provisioning new    Cross-Cloud
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InterconnectRemoteLocation::class, 'Google_Service_Compute_InterconnectRemoteLocation');
