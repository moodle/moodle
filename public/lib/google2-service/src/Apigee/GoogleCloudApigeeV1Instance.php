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

namespace Google\Service\Apigee;

class GoogleCloudApigeeV1Instance extends \Google\Collection
{
  /**
   * Range not specified.
   */
  public const PEERING_CIDR_RANGE_CIDR_RANGE_UNSPECIFIED = 'CIDR_RANGE_UNSPECIFIED';
  /**
   * `/16` CIDR range.
   */
  public const PEERING_CIDR_RANGE_SLASH_16 = 'SLASH_16';
  /**
   * `/17` CIDR range.
   */
  public const PEERING_CIDR_RANGE_SLASH_17 = 'SLASH_17';
  /**
   * `/18` CIDR range.
   */
  public const PEERING_CIDR_RANGE_SLASH_18 = 'SLASH_18';
  /**
   * `/19` CIDR range.
   */
  public const PEERING_CIDR_RANGE_SLASH_19 = 'SLASH_19';
  /**
   * `/20` CIDR range.
   */
  public const PEERING_CIDR_RANGE_SLASH_20 = 'SLASH_20';
  /**
   * `/22` CIDR range. Supported for evaluation only.
   */
  public const PEERING_CIDR_RANGE_SLASH_22 = 'SLASH_22';
  /**
   * `/23` CIDR range. Supported for evaluation only.
   */
  public const PEERING_CIDR_RANGE_SLASH_23 = 'SLASH_23';
  /**
   * Resource is in an unspecified state.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Resource is being created.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * Resource is provisioned and ready to use.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * The resource is being deleted.
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * The resource is being updated.
   */
  public const STATE_UPDATING = 'UPDATING';
  protected $collection_key = 'consumerAcceptList';
  protected $accessLoggingConfigType = GoogleCloudApigeeV1AccessLoggingConfig::class;
  protected $accessLoggingConfigDataType = '';
  /**
   * Optional. Customer accept list represents the list of projects (id/number)
   * on customer side that can privately connect to the service attachment. It
   * is an optional field which the customers can provide during the instance
   * creation. By default, the customer project associated with the Apigee
   * organization will be included to the list.
   *
   * @var string[]
   */
  public $consumerAcceptList;
  /**
   * Output only. Time the instance was created in milliseconds since epoch.
   *
   * @var string
   */
  public $createdAt;
  /**
   * Optional. Description of the instance.
   *
   * @var string
   */
  public $description;
  /**
   * Optional. Customer Managed Encryption Key (CMEK) used for disk and volume
   * encryption. If not specified, a Google-Managed encryption key will be used.
   * Use the following format:
   * `projects/([^/]+)/locations/([^/]+)/keyRings/([^/]+)/cryptoKeys/([^/]+)`
   *
   * @var string
   */
  public $diskEncryptionKeyName;
  /**
   * Optional. Display name for the instance.
   *
   * @var string
   */
  public $displayName;
  /**
   * Output only. Internal hostname or IP address of the Apigee endpoint used by
   * clients to connect to the service.
   *
   * @var string
   */
  public $host;
  /**
   * Optional. Comma-separated list of CIDR blocks of length 22 and/or 28 used
   * to create the Apigee instance. Providing CIDR ranges is optional. You can
   * provide just /22 or /28 or both (or neither). Ranges you provide should be
   * freely available as part of a larger named range you have allocated to the
   * Service Networking peering. If this parameter is not provided, Apigee
   * automatically requests an available /22 and /28 CIDR block from Service
   * Networking. Use the /22 CIDR block for configuring your firewall needs to
   * allow traffic from Apigee. Input formats: `a.b.c.d/22` or `e.f.g.h/28` or
   * `a.b.c.d/22,e.f.g.h/28`
   *
   * @var string
   */
  public $ipRange;
  /**
   * Output only. Indicates whether the instance is version locked. If true, the
   * instance will not be updated by automated runtime rollouts. This is only
   * supported for Apigee X instances.
   *
   * @var bool
   */
  public $isVersionLocked;
  /**
   * Output only. Time the instance was last modified in milliseconds since
   * epoch.
   *
   * @var string
   */
  public $lastModifiedAt;
  /**
   * Required. Compute Engine location where the instance resides.
   *
   * @var string
   */
  public $location;
  protected $maintenanceUpdatePolicyType = GoogleCloudApigeeV1MaintenanceUpdatePolicy::class;
  protected $maintenanceUpdatePolicyDataType = '';
  /**
   * Required. Resource ID of the instance. Values must match the regular
   * expression `^a-z{0,30}[a-z\d]$`.
   *
   * @var string
   */
  public $name;
  /**
   * Optional. Size of the CIDR block range that will be reserved by the
   * instance. PAID organizations support `SLASH_16` to `SLASH_20` and defaults
   * to `SLASH_16`. Evaluation organizations support only `SLASH_23`.
   *
   * @deprecated
   * @var string
   */
  public $peeringCidrRange;
  /**
   * Output only. Port number of the exposed Apigee endpoint.
   *
   * @var string
   */
  public $port;
  /**
   * Output only. Version of the runtime system running in the instance. The
   * runtime system is the set of components that serve the API Proxy traffic in
   * your Environments.
   *
   * @var string
   */
  public $runtimeVersion;
  protected $scheduledMaintenanceType = GoogleCloudApigeeV1ScheduledMaintenance::class;
  protected $scheduledMaintenanceDataType = '';
  /**
   * Output only. Resource name of the service attachment created for the
   * instance in the format: `projects/regions/serviceAttachments` Apigee
   * customers can privately forward traffic to this service attachment using
   * the PSC endpoints.
   *
   * @var string
   */
  public $serviceAttachment;
  /**
   * Output only. State of the instance. Values other than `ACTIVE` means the
   * resource is not ready to use.
   *
   * @var string
   */
  public $state;

  /**
   * Optional. Access logging configuration enables the access logging feature
   * at the instance. Apigee customers can enable access logging to ship the
   * access logs to their own project's cloud logging.
   *
   * @param GoogleCloudApigeeV1AccessLoggingConfig $accessLoggingConfig
   */
  public function setAccessLoggingConfig(GoogleCloudApigeeV1AccessLoggingConfig $accessLoggingConfig)
  {
    $this->accessLoggingConfig = $accessLoggingConfig;
  }
  /**
   * @return GoogleCloudApigeeV1AccessLoggingConfig
   */
  public function getAccessLoggingConfig()
  {
    return $this->accessLoggingConfig;
  }
  /**
   * Optional. Customer accept list represents the list of projects (id/number)
   * on customer side that can privately connect to the service attachment. It
   * is an optional field which the customers can provide during the instance
   * creation. By default, the customer project associated with the Apigee
   * organization will be included to the list.
   *
   * @param string[] $consumerAcceptList
   */
  public function setConsumerAcceptList($consumerAcceptList)
  {
    $this->consumerAcceptList = $consumerAcceptList;
  }
  /**
   * @return string[]
   */
  public function getConsumerAcceptList()
  {
    return $this->consumerAcceptList;
  }
  /**
   * Output only. Time the instance was created in milliseconds since epoch.
   *
   * @param string $createdAt
   */
  public function setCreatedAt($createdAt)
  {
    $this->createdAt = $createdAt;
  }
  /**
   * @return string
   */
  public function getCreatedAt()
  {
    return $this->createdAt;
  }
  /**
   * Optional. Description of the instance.
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
   * Optional. Customer Managed Encryption Key (CMEK) used for disk and volume
   * encryption. If not specified, a Google-Managed encryption key will be used.
   * Use the following format:
   * `projects/([^/]+)/locations/([^/]+)/keyRings/([^/]+)/cryptoKeys/([^/]+)`
   *
   * @param string $diskEncryptionKeyName
   */
  public function setDiskEncryptionKeyName($diskEncryptionKeyName)
  {
    $this->diskEncryptionKeyName = $diskEncryptionKeyName;
  }
  /**
   * @return string
   */
  public function getDiskEncryptionKeyName()
  {
    return $this->diskEncryptionKeyName;
  }
  /**
   * Optional. Display name for the instance.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Output only. Internal hostname or IP address of the Apigee endpoint used by
   * clients to connect to the service.
   *
   * @param string $host
   */
  public function setHost($host)
  {
    $this->host = $host;
  }
  /**
   * @return string
   */
  public function getHost()
  {
    return $this->host;
  }
  /**
   * Optional. Comma-separated list of CIDR blocks of length 22 and/or 28 used
   * to create the Apigee instance. Providing CIDR ranges is optional. You can
   * provide just /22 or /28 or both (or neither). Ranges you provide should be
   * freely available as part of a larger named range you have allocated to the
   * Service Networking peering. If this parameter is not provided, Apigee
   * automatically requests an available /22 and /28 CIDR block from Service
   * Networking. Use the /22 CIDR block for configuring your firewall needs to
   * allow traffic from Apigee. Input formats: `a.b.c.d/22` or `e.f.g.h/28` or
   * `a.b.c.d/22,e.f.g.h/28`
   *
   * @param string $ipRange
   */
  public function setIpRange($ipRange)
  {
    $this->ipRange = $ipRange;
  }
  /**
   * @return string
   */
  public function getIpRange()
  {
    return $this->ipRange;
  }
  /**
   * Output only. Indicates whether the instance is version locked. If true, the
   * instance will not be updated by automated runtime rollouts. This is only
   * supported for Apigee X instances.
   *
   * @param bool $isVersionLocked
   */
  public function setIsVersionLocked($isVersionLocked)
  {
    $this->isVersionLocked = $isVersionLocked;
  }
  /**
   * @return bool
   */
  public function getIsVersionLocked()
  {
    return $this->isVersionLocked;
  }
  /**
   * Output only. Time the instance was last modified in milliseconds since
   * epoch.
   *
   * @param string $lastModifiedAt
   */
  public function setLastModifiedAt($lastModifiedAt)
  {
    $this->lastModifiedAt = $lastModifiedAt;
  }
  /**
   * @return string
   */
  public function getLastModifiedAt()
  {
    return $this->lastModifiedAt;
  }
  /**
   * Required. Compute Engine location where the instance resides.
   *
   * @param string $location
   */
  public function setLocation($location)
  {
    $this->location = $location;
  }
  /**
   * @return string
   */
  public function getLocation()
  {
    return $this->location;
  }
  /**
   * Optional. Apigee customers can set the preferred window to perform
   * maintenance on the instance (day of the week and time of day).
   *
   * @param GoogleCloudApigeeV1MaintenanceUpdatePolicy $maintenanceUpdatePolicy
   */
  public function setMaintenanceUpdatePolicy(GoogleCloudApigeeV1MaintenanceUpdatePolicy $maintenanceUpdatePolicy)
  {
    $this->maintenanceUpdatePolicy = $maintenanceUpdatePolicy;
  }
  /**
   * @return GoogleCloudApigeeV1MaintenanceUpdatePolicy
   */
  public function getMaintenanceUpdatePolicy()
  {
    return $this->maintenanceUpdatePolicy;
  }
  /**
   * Required. Resource ID of the instance. Values must match the regular
   * expression `^a-z{0,30}[a-z\d]$`.
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
   * Optional. Size of the CIDR block range that will be reserved by the
   * instance. PAID organizations support `SLASH_16` to `SLASH_20` and defaults
   * to `SLASH_16`. Evaluation organizations support only `SLASH_23`.
   *
   * Accepted values: CIDR_RANGE_UNSPECIFIED, SLASH_16, SLASH_17, SLASH_18,
   * SLASH_19, SLASH_20, SLASH_22, SLASH_23
   *
   * @deprecated
   * @param self::PEERING_CIDR_RANGE_* $peeringCidrRange
   */
  public function setPeeringCidrRange($peeringCidrRange)
  {
    $this->peeringCidrRange = $peeringCidrRange;
  }
  /**
   * @deprecated
   * @return self::PEERING_CIDR_RANGE_*
   */
  public function getPeeringCidrRange()
  {
    return $this->peeringCidrRange;
  }
  /**
   * Output only. Port number of the exposed Apigee endpoint.
   *
   * @param string $port
   */
  public function setPort($port)
  {
    $this->port = $port;
  }
  /**
   * @return string
   */
  public function getPort()
  {
    return $this->port;
  }
  /**
   * Output only. Version of the runtime system running in the instance. The
   * runtime system is the set of components that serve the API Proxy traffic in
   * your Environments.
   *
   * @param string $runtimeVersion
   */
  public function setRuntimeVersion($runtimeVersion)
  {
    $this->runtimeVersion = $runtimeVersion;
  }
  /**
   * @return string
   */
  public function getRuntimeVersion()
  {
    return $this->runtimeVersion;
  }
  /**
   * Output only. Time and date of the scheduled maintenance for this instance.
   * This field is only populated for instances that have opted into Maintenance
   * Window and if there is an upcoming maintenance. Cleared once the
   * maintenance is complete.
   *
   * @param GoogleCloudApigeeV1ScheduledMaintenance $scheduledMaintenance
   */
  public function setScheduledMaintenance(GoogleCloudApigeeV1ScheduledMaintenance $scheduledMaintenance)
  {
    $this->scheduledMaintenance = $scheduledMaintenance;
  }
  /**
   * @return GoogleCloudApigeeV1ScheduledMaintenance
   */
  public function getScheduledMaintenance()
  {
    return $this->scheduledMaintenance;
  }
  /**
   * Output only. Resource name of the service attachment created for the
   * instance in the format: `projects/regions/serviceAttachments` Apigee
   * customers can privately forward traffic to this service attachment using
   * the PSC endpoints.
   *
   * @param string $serviceAttachment
   */
  public function setServiceAttachment($serviceAttachment)
  {
    $this->serviceAttachment = $serviceAttachment;
  }
  /**
   * @return string
   */
  public function getServiceAttachment()
  {
    return $this->serviceAttachment;
  }
  /**
   * Output only. State of the instance. Values other than `ACTIVE` means the
   * resource is not ready to use.
   *
   * Accepted values: STATE_UNSPECIFIED, CREATING, ACTIVE, DELETING, UPDATING
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1Instance::class, 'Google_Service_Apigee_GoogleCloudApigeeV1Instance');
