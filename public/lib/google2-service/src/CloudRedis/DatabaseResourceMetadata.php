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

namespace Google\Service\CloudRedis;

class DatabaseResourceMetadata extends \Google\Collection
{
  public const CURRENT_STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The instance is running.
   */
  public const CURRENT_STATE_HEALTHY = 'HEALTHY';
  /**
   * Instance being created, updated, deleted or under maintenance
   */
  public const CURRENT_STATE_UNHEALTHY = 'UNHEALTHY';
  /**
   * When instance is suspended
   */
  public const CURRENT_STATE_SUSPENDED = 'SUSPENDED';
  /**
   * Instance is deleted.
   */
  public const CURRENT_STATE_DELETED = 'DELETED';
  /**
   * For rest of the other category
   */
  public const CURRENT_STATE_STATE_OTHER = 'STATE_OTHER';
  /**
   * Default, to make it consistent with instance edition enum.
   */
  public const EDITION_EDITION_UNSPECIFIED = 'EDITION_UNSPECIFIED';
  /**
   * Represents the enterprise edition.
   */
  public const EDITION_EDITION_ENTERPRISE = 'EDITION_ENTERPRISE';
  /**
   * Represents the enterprise plus edition.
   */
  public const EDITION_EDITION_ENTERPRISE_PLUS = 'EDITION_ENTERPRISE_PLUS';
  /**
   * Represents the standard edition.
   */
  public const EDITION_EDITION_STANDARD = 'EDITION_STANDARD';
  public const EXPECTED_STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The instance is running.
   */
  public const EXPECTED_STATE_HEALTHY = 'HEALTHY';
  /**
   * Instance being created, updated, deleted or under maintenance
   */
  public const EXPECTED_STATE_UNHEALTHY = 'UNHEALTHY';
  /**
   * When instance is suspended
   */
  public const EXPECTED_STATE_SUSPENDED = 'SUSPENDED';
  /**
   * Instance is deleted.
   */
  public const EXPECTED_STATE_DELETED = 'DELETED';
  /**
   * For rest of the other category
   */
  public const EXPECTED_STATE_STATE_OTHER = 'STATE_OTHER';
  /**
   * Unspecified.
   *
   * @deprecated
   */
  public const INSTANCE_TYPE_INSTANCE_TYPE_UNSPECIFIED = 'INSTANCE_TYPE_UNSPECIFIED';
  /**
   * For rest of the other categories.
   */
  public const INSTANCE_TYPE_SUB_RESOURCE_TYPE_UNSPECIFIED = 'SUB_RESOURCE_TYPE_UNSPECIFIED';
  /**
   * A regular primary database instance.
   *
   * @deprecated
   */
  public const INSTANCE_TYPE_PRIMARY = 'PRIMARY';
  /**
   * A cluster or an instance acting as a secondary.
   *
   * @deprecated
   */
  public const INSTANCE_TYPE_SECONDARY = 'SECONDARY';
  /**
   * An instance acting as a read-replica.
   *
   * @deprecated
   */
  public const INSTANCE_TYPE_READ_REPLICA = 'READ_REPLICA';
  /**
   * For rest of the other categories.
   *
   * @deprecated
   */
  public const INSTANCE_TYPE_OTHER = 'OTHER';
  /**
   * A regular primary database instance.
   */
  public const INSTANCE_TYPE_SUB_RESOURCE_TYPE_PRIMARY = 'SUB_RESOURCE_TYPE_PRIMARY';
  /**
   * A cluster or an instance acting as a secondary.
   */
  public const INSTANCE_TYPE_SUB_RESOURCE_TYPE_SECONDARY = 'SUB_RESOURCE_TYPE_SECONDARY';
  /**
   * An instance acting as a read-replica.
   */
  public const INSTANCE_TYPE_SUB_RESOURCE_TYPE_READ_REPLICA = 'SUB_RESOURCE_TYPE_READ_REPLICA';
  /**
   * An instance acting as an external primary.
   */
  public const INSTANCE_TYPE_SUB_RESOURCE_TYPE_EXTERNAL_PRIMARY = 'SUB_RESOURCE_TYPE_EXTERNAL_PRIMARY';
  /**
   * For rest of the other categories.
   */
  public const INSTANCE_TYPE_SUB_RESOURCE_TYPE_OTHER = 'SUB_RESOURCE_TYPE_OTHER';
  /**
   * Suspension reason is unspecified.
   */
  public const SUSPENSION_REASON_SUSPENSION_REASON_UNSPECIFIED = 'SUSPENSION_REASON_UNSPECIFIED';
  /**
   * Wipeout hide event.
   */
  public const SUSPENSION_REASON_WIPEOUT_HIDE_EVENT = 'WIPEOUT_HIDE_EVENT';
  /**
   * Wipeout purge event.
   */
  public const SUSPENSION_REASON_WIPEOUT_PURGE_EVENT = 'WIPEOUT_PURGE_EVENT';
  /**
   * Billing disabled for project
   */
  public const SUSPENSION_REASON_BILLING_DISABLED = 'BILLING_DISABLED';
  /**
   * Abuse detected for resource
   */
  public const SUSPENSION_REASON_ABUSER_DETECTED = 'ABUSER_DETECTED';
  /**
   * Encryption key inaccessible.
   */
  public const SUSPENSION_REASON_ENCRYPTION_KEY_INACCESSIBLE = 'ENCRYPTION_KEY_INACCESSIBLE';
  /**
   * Replicated cluster encryption key inaccessible.
   */
  public const SUSPENSION_REASON_REPLICATED_CLUSTER_ENCRYPTION_KEY_INACCESSIBLE = 'REPLICATED_CLUSTER_ENCRYPTION_KEY_INACCESSIBLE';
  protected $collection_key = 'entitlements';
  protected $availabilityConfigurationType = AvailabilityConfiguration::class;
  protected $availabilityConfigurationDataType = '';
  protected $backupConfigurationType = BackupConfiguration::class;
  protected $backupConfigurationDataType = '';
  protected $backupRunType = BackupRun::class;
  protected $backupRunDataType = '';
  protected $backupdrConfigurationType = BackupDRConfiguration::class;
  protected $backupdrConfigurationDataType = '';
  /**
   * The creation time of the resource, i.e. the time when resource is created
   * and recorded in partner service.
   *
   * @var string
   */
  public $creationTime;
  /**
   * Current state of the instance.
   *
   * @var string
   */
  public $currentState;
  protected $customMetadataType = CustomMetadataData::class;
  protected $customMetadataDataType = '';
  /**
   * Optional. Edition represents whether the instance is ENTERPRISE or
   * ENTERPRISE_PLUS. This information is core to Cloud SQL only and is used to
   * identify the edition of the instance.
   *
   * @var string
   */
  public $edition;
  protected $entitlementsType = Entitlement::class;
  protected $entitlementsDataType = 'array';
  /**
   * The state that the instance is expected to be in. For example, an instance
   * state can transition to UNHEALTHY due to wrong patch update, while the
   * expected state will remain at the HEALTHY.
   *
   * @var string
   */
  public $expectedState;
  protected $gcbdrConfigurationType = GCBDRConfiguration::class;
  protected $gcbdrConfigurationDataType = '';
  protected $idType = DatabaseResourceId::class;
  protected $idDataType = '';
  /**
   * The type of the instance. Specified at creation time.
   *
   * @var string
   */
  public $instanceType;
  /**
   * Optional. Whether deletion protection is enabled for this resource.
   *
   * @var bool
   */
  public $isDeletionProtectionEnabled;
  /**
   * The resource location. REQUIRED
   *
   * @var string
   */
  public $location;
  protected $machineConfigurationType = MachineConfiguration::class;
  protected $machineConfigurationDataType = '';
  protected $maintenanceInfoType = ResourceMaintenanceInfo::class;
  protected $maintenanceInfoDataType = '';
  protected $primaryResourceIdType = DatabaseResourceId::class;
  protected $primaryResourceIdDataType = '';
  /**
   * Primary resource location. REQUIRED if the immediate parent exists when
   * first time resource is getting ingested, otherwise optional.
   *
   * @var string
   */
  public $primaryResourceLocation;
  protected $productType = Product::class;
  protected $productDataType = '';
  /**
   * Closest parent Cloud Resource Manager container of this resource. It must
   * be resource name of a Cloud Resource Manager project with the format of
   * "/", such as "projects/123". For GCP provided resources, number should be
   * project number.
   *
   * @var string
   */
  public $resourceContainer;
  /**
   * Required. Different from DatabaseResourceId.unique_id, a resource name can
   * be reused over time. That is, after a resource named "ABC" is deleted, the
   * name "ABC" can be used to to create a new resource within the same source.
   * Resource name to follow CAIS resource_name format as noted here go/condor-
   * common-datamodel
   *
   * @var string
   */
  public $resourceName;
  /**
   * Optional. Suspension reason for the resource.
   *
   * @var string
   */
  public $suspensionReason;
  protected $tagsSetType = Tags::class;
  protected $tagsSetDataType = '';
  /**
   * The time at which the resource was updated and recorded at partner service.
   *
   * @var string
   */
  public $updationTime;
  protected $userLabelSetType = UserLabels::class;
  protected $userLabelSetDataType = '';
  /**
   * The resource zone. This is only applicable for zonal resources and will be
   * empty for regional and multi-regional resources.
   *
   * @var string
   */
  public $zone;

  /**
   * Availability configuration for this instance
   *
   * @param AvailabilityConfiguration $availabilityConfiguration
   */
  public function setAvailabilityConfiguration(AvailabilityConfiguration $availabilityConfiguration)
  {
    $this->availabilityConfiguration = $availabilityConfiguration;
  }
  /**
   * @return AvailabilityConfiguration
   */
  public function getAvailabilityConfiguration()
  {
    return $this->availabilityConfiguration;
  }
  /**
   * Backup configuration for this instance
   *
   * @param BackupConfiguration $backupConfiguration
   */
  public function setBackupConfiguration(BackupConfiguration $backupConfiguration)
  {
    $this->backupConfiguration = $backupConfiguration;
  }
  /**
   * @return BackupConfiguration
   */
  public function getBackupConfiguration()
  {
    return $this->backupConfiguration;
  }
  /**
   * Latest backup run information for this instance
   *
   * @param BackupRun $backupRun
   */
  public function setBackupRun(BackupRun $backupRun)
  {
    $this->backupRun = $backupRun;
  }
  /**
   * @return BackupRun
   */
  public function getBackupRun()
  {
    return $this->backupRun;
  }
  /**
   * Optional. BackupDR Configuration for the resource.
   *
   * @param BackupDRConfiguration $backupdrConfiguration
   */
  public function setBackupdrConfiguration(BackupDRConfiguration $backupdrConfiguration)
  {
    $this->backupdrConfiguration = $backupdrConfiguration;
  }
  /**
   * @return BackupDRConfiguration
   */
  public function getBackupdrConfiguration()
  {
    return $this->backupdrConfiguration;
  }
  /**
   * The creation time of the resource, i.e. the time when resource is created
   * and recorded in partner service.
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
   * Current state of the instance.
   *
   * Accepted values: STATE_UNSPECIFIED, HEALTHY, UNHEALTHY, SUSPENDED, DELETED,
   * STATE_OTHER
   *
   * @param self::CURRENT_STATE_* $currentState
   */
  public function setCurrentState($currentState)
  {
    $this->currentState = $currentState;
  }
  /**
   * @return self::CURRENT_STATE_*
   */
  public function getCurrentState()
  {
    return $this->currentState;
  }
  /**
   * Any custom metadata associated with the resource
   *
   * @param CustomMetadataData $customMetadata
   */
  public function setCustomMetadata(CustomMetadataData $customMetadata)
  {
    $this->customMetadata = $customMetadata;
  }
  /**
   * @return CustomMetadataData
   */
  public function getCustomMetadata()
  {
    return $this->customMetadata;
  }
  /**
   * Optional. Edition represents whether the instance is ENTERPRISE or
   * ENTERPRISE_PLUS. This information is core to Cloud SQL only and is used to
   * identify the edition of the instance.
   *
   * Accepted values: EDITION_UNSPECIFIED, EDITION_ENTERPRISE,
   * EDITION_ENTERPRISE_PLUS, EDITION_STANDARD
   *
   * @param self::EDITION_* $edition
   */
  public function setEdition($edition)
  {
    $this->edition = $edition;
  }
  /**
   * @return self::EDITION_*
   */
  public function getEdition()
  {
    return $this->edition;
  }
  /**
   * Entitlements associated with the resource
   *
   * @param Entitlement[] $entitlements
   */
  public function setEntitlements($entitlements)
  {
    $this->entitlements = $entitlements;
  }
  /**
   * @return Entitlement[]
   */
  public function getEntitlements()
  {
    return $this->entitlements;
  }
  /**
   * The state that the instance is expected to be in. For example, an instance
   * state can transition to UNHEALTHY due to wrong patch update, while the
   * expected state will remain at the HEALTHY.
   *
   * Accepted values: STATE_UNSPECIFIED, HEALTHY, UNHEALTHY, SUSPENDED, DELETED,
   * STATE_OTHER
   *
   * @param self::EXPECTED_STATE_* $expectedState
   */
  public function setExpectedState($expectedState)
  {
    $this->expectedState = $expectedState;
  }
  /**
   * @return self::EXPECTED_STATE_*
   */
  public function getExpectedState()
  {
    return $this->expectedState;
  }
  /**
   * GCBDR configuration for the resource.
   *
   * @deprecated
   * @param GCBDRConfiguration $gcbdrConfiguration
   */
  public function setGcbdrConfiguration(GCBDRConfiguration $gcbdrConfiguration)
  {
    $this->gcbdrConfiguration = $gcbdrConfiguration;
  }
  /**
   * @deprecated
   * @return GCBDRConfiguration
   */
  public function getGcbdrConfiguration()
  {
    return $this->gcbdrConfiguration;
  }
  /**
   * Required. Unique identifier for a Database resource
   *
   * @param DatabaseResourceId $id
   */
  public function setId(DatabaseResourceId $id)
  {
    $this->id = $id;
  }
  /**
   * @return DatabaseResourceId
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * The type of the instance. Specified at creation time.
   *
   * Accepted values: INSTANCE_TYPE_UNSPECIFIED, SUB_RESOURCE_TYPE_UNSPECIFIED,
   * PRIMARY, SECONDARY, READ_REPLICA, OTHER, SUB_RESOURCE_TYPE_PRIMARY,
   * SUB_RESOURCE_TYPE_SECONDARY, SUB_RESOURCE_TYPE_READ_REPLICA,
   * SUB_RESOURCE_TYPE_EXTERNAL_PRIMARY, SUB_RESOURCE_TYPE_OTHER
   *
   * @param self::INSTANCE_TYPE_* $instanceType
   */
  public function setInstanceType($instanceType)
  {
    $this->instanceType = $instanceType;
  }
  /**
   * @return self::INSTANCE_TYPE_*
   */
  public function getInstanceType()
  {
    return $this->instanceType;
  }
  /**
   * Optional. Whether deletion protection is enabled for this resource.
   *
   * @param bool $isDeletionProtectionEnabled
   */
  public function setIsDeletionProtectionEnabled($isDeletionProtectionEnabled)
  {
    $this->isDeletionProtectionEnabled = $isDeletionProtectionEnabled;
  }
  /**
   * @return bool
   */
  public function getIsDeletionProtectionEnabled()
  {
    return $this->isDeletionProtectionEnabled;
  }
  /**
   * The resource location. REQUIRED
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
   * Machine configuration for this resource.
   *
   * @param MachineConfiguration $machineConfiguration
   */
  public function setMachineConfiguration(MachineConfiguration $machineConfiguration)
  {
    $this->machineConfiguration = $machineConfiguration;
  }
  /**
   * @return MachineConfiguration
   */
  public function getMachineConfiguration()
  {
    return $this->machineConfiguration;
  }
  /**
   * Optional. Maintenance info for the resource.
   *
   * @param ResourceMaintenanceInfo $maintenanceInfo
   */
  public function setMaintenanceInfo(ResourceMaintenanceInfo $maintenanceInfo)
  {
    $this->maintenanceInfo = $maintenanceInfo;
  }
  /**
   * @return ResourceMaintenanceInfo
   */
  public function getMaintenanceInfo()
  {
    return $this->maintenanceInfo;
  }
  /**
   * Identifier for this resource's immediate parent/primary resource if the
   * current resource is a replica or derived form of another Database resource.
   * Else it would be NULL. REQUIRED if the immediate parent exists when first
   * time resource is getting ingested, otherwise optional.
   *
   * @param DatabaseResourceId $primaryResourceId
   */
  public function setPrimaryResourceId(DatabaseResourceId $primaryResourceId)
  {
    $this->primaryResourceId = $primaryResourceId;
  }
  /**
   * @return DatabaseResourceId
   */
  public function getPrimaryResourceId()
  {
    return $this->primaryResourceId;
  }
  /**
   * Primary resource location. REQUIRED if the immediate parent exists when
   * first time resource is getting ingested, otherwise optional.
   *
   * @param string $primaryResourceLocation
   */
  public function setPrimaryResourceLocation($primaryResourceLocation)
  {
    $this->primaryResourceLocation = $primaryResourceLocation;
  }
  /**
   * @return string
   */
  public function getPrimaryResourceLocation()
  {
    return $this->primaryResourceLocation;
  }
  /**
   * The product this resource represents.
   *
   * @param Product $product
   */
  public function setProduct(Product $product)
  {
    $this->product = $product;
  }
  /**
   * @return Product
   */
  public function getProduct()
  {
    return $this->product;
  }
  /**
   * Closest parent Cloud Resource Manager container of this resource. It must
   * be resource name of a Cloud Resource Manager project with the format of
   * "/", such as "projects/123". For GCP provided resources, number should be
   * project number.
   *
   * @param string $resourceContainer
   */
  public function setResourceContainer($resourceContainer)
  {
    $this->resourceContainer = $resourceContainer;
  }
  /**
   * @return string
   */
  public function getResourceContainer()
  {
    return $this->resourceContainer;
  }
  /**
   * Required. Different from DatabaseResourceId.unique_id, a resource name can
   * be reused over time. That is, after a resource named "ABC" is deleted, the
   * name "ABC" can be used to to create a new resource within the same source.
   * Resource name to follow CAIS resource_name format as noted here go/condor-
   * common-datamodel
   *
   * @param string $resourceName
   */
  public function setResourceName($resourceName)
  {
    $this->resourceName = $resourceName;
  }
  /**
   * @return string
   */
  public function getResourceName()
  {
    return $this->resourceName;
  }
  /**
   * Optional. Suspension reason for the resource.
   *
   * Accepted values: SUSPENSION_REASON_UNSPECIFIED, WIPEOUT_HIDE_EVENT,
   * WIPEOUT_PURGE_EVENT, BILLING_DISABLED, ABUSER_DETECTED,
   * ENCRYPTION_KEY_INACCESSIBLE, REPLICATED_CLUSTER_ENCRYPTION_KEY_INACCESSIBLE
   *
   * @param self::SUSPENSION_REASON_* $suspensionReason
   */
  public function setSuspensionReason($suspensionReason)
  {
    $this->suspensionReason = $suspensionReason;
  }
  /**
   * @return self::SUSPENSION_REASON_*
   */
  public function getSuspensionReason()
  {
    return $this->suspensionReason;
  }
  /**
   * Optional. Tags associated with this resources.
   *
   * @param Tags $tagsSet
   */
  public function setTagsSet(Tags $tagsSet)
  {
    $this->tagsSet = $tagsSet;
  }
  /**
   * @return Tags
   */
  public function getTagsSet()
  {
    return $this->tagsSet;
  }
  /**
   * The time at which the resource was updated and recorded at partner service.
   *
   * @param string $updationTime
   */
  public function setUpdationTime($updationTime)
  {
    $this->updationTime = $updationTime;
  }
  /**
   * @return string
   */
  public function getUpdationTime()
  {
    return $this->updationTime;
  }
  /**
   * User-provided labels associated with the resource
   *
   * @param UserLabels $userLabelSet
   */
  public function setUserLabelSet(UserLabels $userLabelSet)
  {
    $this->userLabelSet = $userLabelSet;
  }
  /**
   * @return UserLabels
   */
  public function getUserLabelSet()
  {
    return $this->userLabelSet;
  }
  /**
   * The resource zone. This is only applicable for zonal resources and will be
   * empty for regional and multi-regional resources.
   *
   * @param string $zone
   */
  public function setZone($zone)
  {
    $this->zone = $zone;
  }
  /**
   * @return string
   */
  public function getZone()
  {
    return $this->zone;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DatabaseResourceMetadata::class, 'Google_Service_CloudRedis_DatabaseResourceMetadata');
