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

namespace Google\Service\Dataproc;

class GceClusterConfig extends \Google\Collection
{
  /**
   * If unspecified, Compute Engine default behavior will apply, which is the
   * same as INHERIT_FROM_SUBNETWORK.
   */
  public const PRIVATE_IPV6_GOOGLE_ACCESS_PRIVATE_IPV6_GOOGLE_ACCESS_UNSPECIFIED = 'PRIVATE_IPV6_GOOGLE_ACCESS_UNSPECIFIED';
  /**
   * Private access to and from Google Services configuration inherited from the
   * subnetwork configuration. This is the default Compute Engine behavior.
   */
  public const PRIVATE_IPV6_GOOGLE_ACCESS_INHERIT_FROM_SUBNETWORK = 'INHERIT_FROM_SUBNETWORK';
  /**
   * Enables outbound private IPv6 access to Google Services from the Dataproc
   * cluster.
   */
  public const PRIVATE_IPV6_GOOGLE_ACCESS_OUTBOUND = 'OUTBOUND';
  /**
   * Enables bidirectional private IPv6 access between Google Services and the
   * Dataproc cluster.
   */
  public const PRIVATE_IPV6_GOOGLE_ACCESS_BIDIRECTIONAL = 'BIDIRECTIONAL';
  protected $collection_key = 'tags';
  /**
   * Optional. An optional list of Compute Engine zones where the Dataproc
   * cluster will not be located when Auto Zone is enabled. Only one of zone_uri
   * or auto_zone_exclude_zone_uris can be set. If both are omitted, the service
   * will pick a zone in the cluster Compute Engine region. If
   * auto_zone_exclude_zone_uris is set and there is more than one non-excluded
   * zone, the service will pick one of the non-excluded zones. Otherwise,
   * cluster creation will fail with INVALID_ARGUMENT error.A full URL, partial
   * URI, or short name are valid. Examples:
   * https://www.googleapis.com/compute/v1/projects/[project_id]/zones/[zone]
   * projects/[project_id]/zones/[zone] [zone]
   *
   * @var string[]
   */
  public $autoZoneExcludeZoneUris;
  protected $confidentialInstanceConfigType = ConfidentialInstanceConfig::class;
  protected $confidentialInstanceConfigDataType = '';
  /**
   * Optional. This setting applies to subnetwork-enabled networks. It is set to
   * true by default in clusters created with image versions 2.2.x.When set to
   * true: All cluster VMs have internal IP addresses. Google Private Access
   * (https://cloud.google.com/vpc/docs/private-google-access) must be enabled
   * to access Dataproc and other Google Cloud APIs. Off-cluster dependencies
   * must be configured to be accessible without external IP addresses.When set
   * to false: Cluster VMs are not restricted to internal IP addresses.
   * Ephemeral external IP addresses are assigned to each cluster VM.
   *
   * @var bool
   */
  public $internalIpOnly;
  /**
   * Optional. The Compute Engine metadata entries to add to all instances (see
   * Project and instance metadata
   * (https://cloud.google.com/compute/docs/storing-retrieving-
   * metadata#project_and_instance_metadata)).
   *
   * @var string[]
   */
  public $metadata;
  /**
   * Optional. The Compute Engine network to be used for machine communications.
   * Cannot be specified with subnetwork_uri. If neither network_uri nor
   * subnetwork_uri is specified, the "default" network of the project is used,
   * if it exists. Cannot be a "Custom Subnet Network" (see Using Subnetworks
   * (https://cloud.google.com/compute/docs/subnetworks) for more information).A
   * full URL, partial URI, or short name are valid. Examples: https://www.googl
   * eapis.com/compute/v1/projects/[project_id]/global/networks/default
   * projects/[project_id]/global/networks/default default
   *
   * @var string
   */
  public $networkUri;
  protected $nodeGroupAffinityType = NodeGroupAffinity::class;
  protected $nodeGroupAffinityDataType = '';
  /**
   * Optional. The type of IPv6 access for a cluster.
   *
   * @var string
   */
  public $privateIpv6GoogleAccess;
  protected $reservationAffinityType = ReservationAffinity::class;
  protected $reservationAffinityDataType = '';
  /**
   * Optional. Resource manager tags (https://cloud.google.com/resource-
   * manager/docs/tags/tags-creating-and-managing) to add to all instances (see
   * Use secure tags in Dataproc
   * (https://cloud.google.com/dataproc/docs/guides/use-secure-tags)).
   *
   * @var string[]
   */
  public $resourceManagerTags;
  /**
   * Optional. The Dataproc service account
   * (https://cloud.google.com/dataproc/docs/concepts/configuring-
   * clusters/service-accounts#service_accounts_in_dataproc) (also see VM Data
   * Plane identity
   * (https://cloud.google.com/dataproc/docs/concepts/iam/dataproc-
   * principals#vm_service_account_data_plane_identity)) used by Dataproc
   * cluster VM instances to access Google Cloud Platform services.If not
   * specified, the Compute Engine default service account
   * (https://cloud.google.com/compute/docs/access/service-
   * accounts#default_service_account) is used.
   *
   * @var string
   */
  public $serviceAccount;
  /**
   * Optional. The URIs of service account scopes to be included in Compute
   * Engine instances. The following base set of scopes is always included:
   * https://www.googleapis.com/auth/cloud.useraccounts.readonly
   * https://www.googleapis.com/auth/devstorage.read_write
   * https://www.googleapis.com/auth/logging.writeIf no scopes are specified,
   * the following defaults are also provided:
   * https://www.googleapis.com/auth/bigquery
   * https://www.googleapis.com/auth/bigtable.admin.table
   * https://www.googleapis.com/auth/bigtable.data
   * https://www.googleapis.com/auth/devstorage.full_control
   *
   * @var string[]
   */
  public $serviceAccountScopes;
  protected $shieldedInstanceConfigType = ShieldedInstanceConfig::class;
  protected $shieldedInstanceConfigDataType = '';
  /**
   * Optional. The Compute Engine subnetwork to be used for machine
   * communications. Cannot be specified with network_uri.A full URL, partial
   * URI, or short name are valid. Examples: https://www.googleapis.com/compute/
   * v1/projects/[project_id]/regions/[region]/subnetworks/sub0
   * projects/[project_id]/regions/[region]/subnetworks/sub0 sub0
   *
   * @var string
   */
  public $subnetworkUri;
  /**
   * The Compute Engine network tags to add to all instances (see Tagging
   * instances (https://cloud.google.com/vpc/docs/add-remove-network-tags)).
   *
   * @var string[]
   */
  public $tags;
  /**
   * Optional. The Compute Engine zone where the Dataproc cluster will be
   * located. If omitted, the service will pick a zone in the cluster's Compute
   * Engine region. On a get request, zone will always be present.A full URL,
   * partial URI, or short name are valid. Examples:
   * https://www.googleapis.com/compute/v1/projects/[project_id]/zones/[zone]
   * projects/[project_id]/zones/[zone] [zone]
   *
   * @var string
   */
  public $zoneUri;

  /**
   * Optional. An optional list of Compute Engine zones where the Dataproc
   * cluster will not be located when Auto Zone is enabled. Only one of zone_uri
   * or auto_zone_exclude_zone_uris can be set. If both are omitted, the service
   * will pick a zone in the cluster Compute Engine region. If
   * auto_zone_exclude_zone_uris is set and there is more than one non-excluded
   * zone, the service will pick one of the non-excluded zones. Otherwise,
   * cluster creation will fail with INVALID_ARGUMENT error.A full URL, partial
   * URI, or short name are valid. Examples:
   * https://www.googleapis.com/compute/v1/projects/[project_id]/zones/[zone]
   * projects/[project_id]/zones/[zone] [zone]
   *
   * @param string[] $autoZoneExcludeZoneUris
   */
  public function setAutoZoneExcludeZoneUris($autoZoneExcludeZoneUris)
  {
    $this->autoZoneExcludeZoneUris = $autoZoneExcludeZoneUris;
  }
  /**
   * @return string[]
   */
  public function getAutoZoneExcludeZoneUris()
  {
    return $this->autoZoneExcludeZoneUris;
  }
  /**
   * Optional. Confidential Instance Config for clusters using Confidential VMs
   * (https://cloud.google.com/compute/confidential-vm/docs).
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
   * Optional. This setting applies to subnetwork-enabled networks. It is set to
   * true by default in clusters created with image versions 2.2.x.When set to
   * true: All cluster VMs have internal IP addresses. Google Private Access
   * (https://cloud.google.com/vpc/docs/private-google-access) must be enabled
   * to access Dataproc and other Google Cloud APIs. Off-cluster dependencies
   * must be configured to be accessible without external IP addresses.When set
   * to false: Cluster VMs are not restricted to internal IP addresses.
   * Ephemeral external IP addresses are assigned to each cluster VM.
   *
   * @param bool $internalIpOnly
   */
  public function setInternalIpOnly($internalIpOnly)
  {
    $this->internalIpOnly = $internalIpOnly;
  }
  /**
   * @return bool
   */
  public function getInternalIpOnly()
  {
    return $this->internalIpOnly;
  }
  /**
   * Optional. The Compute Engine metadata entries to add to all instances (see
   * Project and instance metadata
   * (https://cloud.google.com/compute/docs/storing-retrieving-
   * metadata#project_and_instance_metadata)).
   *
   * @param string[] $metadata
   */
  public function setMetadata($metadata)
  {
    $this->metadata = $metadata;
  }
  /**
   * @return string[]
   */
  public function getMetadata()
  {
    return $this->metadata;
  }
  /**
   * Optional. The Compute Engine network to be used for machine communications.
   * Cannot be specified with subnetwork_uri. If neither network_uri nor
   * subnetwork_uri is specified, the "default" network of the project is used,
   * if it exists. Cannot be a "Custom Subnet Network" (see Using Subnetworks
   * (https://cloud.google.com/compute/docs/subnetworks) for more information).A
   * full URL, partial URI, or short name are valid. Examples: https://www.googl
   * eapis.com/compute/v1/projects/[project_id]/global/networks/default
   * projects/[project_id]/global/networks/default default
   *
   * @param string $networkUri
   */
  public function setNetworkUri($networkUri)
  {
    $this->networkUri = $networkUri;
  }
  /**
   * @return string
   */
  public function getNetworkUri()
  {
    return $this->networkUri;
  }
  /**
   * Optional. Node Group Affinity for sole-tenant clusters.
   *
   * @param NodeGroupAffinity $nodeGroupAffinity
   */
  public function setNodeGroupAffinity(NodeGroupAffinity $nodeGroupAffinity)
  {
    $this->nodeGroupAffinity = $nodeGroupAffinity;
  }
  /**
   * @return NodeGroupAffinity
   */
  public function getNodeGroupAffinity()
  {
    return $this->nodeGroupAffinity;
  }
  /**
   * Optional. The type of IPv6 access for a cluster.
   *
   * Accepted values: PRIVATE_IPV6_GOOGLE_ACCESS_UNSPECIFIED,
   * INHERIT_FROM_SUBNETWORK, OUTBOUND, BIDIRECTIONAL
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
   * Optional. Reservation Affinity for consuming Zonal reservation.
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
   * Optional. Resource manager tags (https://cloud.google.com/resource-
   * manager/docs/tags/tags-creating-and-managing) to add to all instances (see
   * Use secure tags in Dataproc
   * (https://cloud.google.com/dataproc/docs/guides/use-secure-tags)).
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
   * Optional. The Dataproc service account
   * (https://cloud.google.com/dataproc/docs/concepts/configuring-
   * clusters/service-accounts#service_accounts_in_dataproc) (also see VM Data
   * Plane identity
   * (https://cloud.google.com/dataproc/docs/concepts/iam/dataproc-
   * principals#vm_service_account_data_plane_identity)) used by Dataproc
   * cluster VM instances to access Google Cloud Platform services.If not
   * specified, the Compute Engine default service account
   * (https://cloud.google.com/compute/docs/access/service-
   * accounts#default_service_account) is used.
   *
   * @param string $serviceAccount
   */
  public function setServiceAccount($serviceAccount)
  {
    $this->serviceAccount = $serviceAccount;
  }
  /**
   * @return string
   */
  public function getServiceAccount()
  {
    return $this->serviceAccount;
  }
  /**
   * Optional. The URIs of service account scopes to be included in Compute
   * Engine instances. The following base set of scopes is always included:
   * https://www.googleapis.com/auth/cloud.useraccounts.readonly
   * https://www.googleapis.com/auth/devstorage.read_write
   * https://www.googleapis.com/auth/logging.writeIf no scopes are specified,
   * the following defaults are also provided:
   * https://www.googleapis.com/auth/bigquery
   * https://www.googleapis.com/auth/bigtable.admin.table
   * https://www.googleapis.com/auth/bigtable.data
   * https://www.googleapis.com/auth/devstorage.full_control
   *
   * @param string[] $serviceAccountScopes
   */
  public function setServiceAccountScopes($serviceAccountScopes)
  {
    $this->serviceAccountScopes = $serviceAccountScopes;
  }
  /**
   * @return string[]
   */
  public function getServiceAccountScopes()
  {
    return $this->serviceAccountScopes;
  }
  /**
   * Optional. Shielded Instance Config for clusters using Compute Engine
   * Shielded VMs (https://cloud.google.com/security/shielded-cloud/shielded-
   * vm).
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
   * Optional. The Compute Engine subnetwork to be used for machine
   * communications. Cannot be specified with network_uri.A full URL, partial
   * URI, or short name are valid. Examples: https://www.googleapis.com/compute/
   * v1/projects/[project_id]/regions/[region]/subnetworks/sub0
   * projects/[project_id]/regions/[region]/subnetworks/sub0 sub0
   *
   * @param string $subnetworkUri
   */
  public function setSubnetworkUri($subnetworkUri)
  {
    $this->subnetworkUri = $subnetworkUri;
  }
  /**
   * @return string
   */
  public function getSubnetworkUri()
  {
    return $this->subnetworkUri;
  }
  /**
   * The Compute Engine network tags to add to all instances (see Tagging
   * instances (https://cloud.google.com/vpc/docs/add-remove-network-tags)).
   *
   * @param string[] $tags
   */
  public function setTags($tags)
  {
    $this->tags = $tags;
  }
  /**
   * @return string[]
   */
  public function getTags()
  {
    return $this->tags;
  }
  /**
   * Optional. The Compute Engine zone where the Dataproc cluster will be
   * located. If omitted, the service will pick a zone in the cluster's Compute
   * Engine region. On a get request, zone will always be present.A full URL,
   * partial URI, or short name are valid. Examples:
   * https://www.googleapis.com/compute/v1/projects/[project_id]/zones/[zone]
   * projects/[project_id]/zones/[zone] [zone]
   *
   * @param string $zoneUri
   */
  public function setZoneUri($zoneUri)
  {
    $this->zoneUri = $zoneUri;
  }
  /**
   * @return string
   */
  public function getZoneUri()
  {
    return $this->zoneUri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GceClusterConfig::class, 'Google_Service_Dataproc_GceClusterConfig');
