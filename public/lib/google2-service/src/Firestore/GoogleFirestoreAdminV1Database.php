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

namespace Google\Service\Firestore;

class GoogleFirestoreAdminV1Database extends \Google\Model
{
  /**
   * Not used.
   */
  public const APP_ENGINE_INTEGRATION_MODE_APP_ENGINE_INTEGRATION_MODE_UNSPECIFIED = 'APP_ENGINE_INTEGRATION_MODE_UNSPECIFIED';
  /**
   * If an App Engine application exists in the same region as this database,
   * App Engine configuration will impact this database. This includes disabling
   * of the application & database, as well as disabling writes to the database.
   */
  public const APP_ENGINE_INTEGRATION_MODE_ENABLED = 'ENABLED';
  /**
   * App Engine has no effect on the ability of this database to serve requests.
   * This is the default setting for databases created with the Firestore API.
   */
  public const APP_ENGINE_INTEGRATION_MODE_DISABLED = 'DISABLED';
  /**
   * Not used.
   */
  public const CONCURRENCY_MODE_CONCURRENCY_MODE_UNSPECIFIED = 'CONCURRENCY_MODE_UNSPECIFIED';
  /**
   * Use optimistic concurrency control by default. This mode is available for
   * Cloud Firestore databases.
   */
  public const CONCURRENCY_MODE_OPTIMISTIC = 'OPTIMISTIC';
  /**
   * Use pessimistic concurrency control by default. This mode is available for
   * Cloud Firestore databases. This is the default setting for Cloud Firestore.
   */
  public const CONCURRENCY_MODE_PESSIMISTIC = 'PESSIMISTIC';
  /**
   * Use optimistic concurrency control with entity groups by default. This is
   * the only available mode for Cloud Datastore. This mode is also available
   * for Cloud Firestore with Datastore Mode but is not recommended.
   */
  public const CONCURRENCY_MODE_OPTIMISTIC_WITH_ENTITY_GROUPS = 'OPTIMISTIC_WITH_ENTITY_GROUPS';
  /**
   * Not used.
   */
  public const DATABASE_EDITION_DATABASE_EDITION_UNSPECIFIED = 'DATABASE_EDITION_UNSPECIFIED';
  /**
   * Standard edition. This is the default setting if not specified.
   */
  public const DATABASE_EDITION_STANDARD = 'STANDARD';
  /**
   * Enterprise edition.
   */
  public const DATABASE_EDITION_ENTERPRISE = 'ENTERPRISE';
  /**
   * The default value. Delete protection type is not specified
   */
  public const DELETE_PROTECTION_STATE_DELETE_PROTECTION_STATE_UNSPECIFIED = 'DELETE_PROTECTION_STATE_UNSPECIFIED';
  /**
   * Delete protection is disabled
   */
  public const DELETE_PROTECTION_STATE_DELETE_PROTECTION_DISABLED = 'DELETE_PROTECTION_DISABLED';
  /**
   * Delete protection is enabled
   */
  public const DELETE_PROTECTION_STATE_DELETE_PROTECTION_ENABLED = 'DELETE_PROTECTION_ENABLED';
  /**
   * Not Used.
   */
  public const FIRESTORE_DATA_ACCESS_MODE_DATA_ACCESS_MODE_UNSPECIFIED = 'DATA_ACCESS_MODE_UNSPECIFIED';
  /**
   * Accessing the database through the API is allowed.
   */
  public const FIRESTORE_DATA_ACCESS_MODE_DATA_ACCESS_MODE_ENABLED = 'DATA_ACCESS_MODE_ENABLED';
  /**
   * Accessing the database through the API is disallowed.
   */
  public const FIRESTORE_DATA_ACCESS_MODE_DATA_ACCESS_MODE_DISABLED = 'DATA_ACCESS_MODE_DISABLED';
  /**
   * Not Used.
   */
  public const MONGODB_COMPATIBLE_DATA_ACCESS_MODE_DATA_ACCESS_MODE_UNSPECIFIED = 'DATA_ACCESS_MODE_UNSPECIFIED';
  /**
   * Accessing the database through the API is allowed.
   */
  public const MONGODB_COMPATIBLE_DATA_ACCESS_MODE_DATA_ACCESS_MODE_ENABLED = 'DATA_ACCESS_MODE_ENABLED';
  /**
   * Accessing the database through the API is disallowed.
   */
  public const MONGODB_COMPATIBLE_DATA_ACCESS_MODE_DATA_ACCESS_MODE_DISABLED = 'DATA_ACCESS_MODE_DISABLED';
  /**
   * Not used.
   */
  public const POINT_IN_TIME_RECOVERY_ENABLEMENT_POINT_IN_TIME_RECOVERY_ENABLEMENT_UNSPECIFIED = 'POINT_IN_TIME_RECOVERY_ENABLEMENT_UNSPECIFIED';
  /**
   * Reads are supported on selected versions of the data from within the past 7
   * days: * Reads against any timestamp within the past hour * Reads against
   * 1-minute snapshots beyond 1 hour and within 7 days
   * `version_retention_period` and `earliest_version_time` can be used to
   * determine the supported versions.
   */
  public const POINT_IN_TIME_RECOVERY_ENABLEMENT_POINT_IN_TIME_RECOVERY_ENABLED = 'POINT_IN_TIME_RECOVERY_ENABLED';
  /**
   * Reads are supported on any version of the data from within the past 1 hour.
   */
  public const POINT_IN_TIME_RECOVERY_ENABLEMENT_POINT_IN_TIME_RECOVERY_DISABLED = 'POINT_IN_TIME_RECOVERY_DISABLED';
  /**
   * The Realtime Updates feature is not specified.
   */
  public const REALTIME_UPDATES_MODE_REALTIME_UPDATES_MODE_UNSPECIFIED = 'REALTIME_UPDATES_MODE_UNSPECIFIED';
  /**
   * The Realtime Updates feature is enabled by default. This could potentially
   * degrade write performance for the database.
   */
  public const REALTIME_UPDATES_MODE_REALTIME_UPDATES_MODE_ENABLED = 'REALTIME_UPDATES_MODE_ENABLED';
  /**
   * The Realtime Updates feature is disabled by default.
   */
  public const REALTIME_UPDATES_MODE_REALTIME_UPDATES_MODE_DISABLED = 'REALTIME_UPDATES_MODE_DISABLED';
  /**
   * Not used.
   */
  public const TYPE_DATABASE_TYPE_UNSPECIFIED = 'DATABASE_TYPE_UNSPECIFIED';
  /**
   * Firestore Native Mode
   */
  public const TYPE_FIRESTORE_NATIVE = 'FIRESTORE_NATIVE';
  /**
   * Firestore in Datastore Mode.
   */
  public const TYPE_DATASTORE_MODE = 'DATASTORE_MODE';
  /**
   * The App Engine integration mode to use for this database.
   *
   * @var string
   */
  public $appEngineIntegrationMode;
  protected $cmekConfigType = GoogleFirestoreAdminV1CmekConfig::class;
  protected $cmekConfigDataType = '';
  /**
   * The concurrency control mode to use for this database.
   *
   * @var string
   */
  public $concurrencyMode;
  /**
   * Output only. The timestamp at which this database was created. Databases
   * created before 2016 do not populate create_time.
   *
   * @var string
   */
  public $createTime;
  /**
   * Immutable. The edition of the database.
   *
   * @var string
   */
  public $databaseEdition;
  /**
   * State of delete protection for the database.
   *
   * @var string
   */
  public $deleteProtectionState;
  /**
   * Output only. The timestamp at which this database was deleted. Only set if
   * the database has been deleted.
   *
   * @var string
   */
  public $deleteTime;
  /**
   * Output only. The earliest timestamp at which older versions of the data can
   * be read from the database. See [version_retention_period] above; this field
   * is populated with `now - version_retention_period`. This value is
   * continuously updated, and becomes stale the moment it is queried. If you
   * are using this value to recover data, make sure to account for the time
   * from the moment when the value is queried to the moment when you initiate
   * the recovery.
   *
   * @var string
   */
  public $earliestVersionTime;
  /**
   * This checksum is computed by the server based on the value of other fields,
   * and may be sent on update and delete requests to ensure the client has an
   * up-to-date value before proceeding.
   *
   * @var string
   */
  public $etag;
  /**
   * Optional. The Firestore API data access mode to use for this database. If
   * not set on write: - the default value is DATA_ACCESS_MODE_DISABLED for
   * Enterprise Edition. - the default value is DATA_ACCESS_MODE_ENABLED for
   * Standard Edition.
   *
   * @var string
   */
  public $firestoreDataAccessMode;
  /**
   * Output only. Background: Free tier is the ability of a Firestore database
   * to use a small amount of resources every day without being charged. Once
   * usage exceeds the free tier limit further usage is charged. Whether this
   * database can make use of the free tier. Only one database per project can
   * be eligible for the free tier. The first (or next) database that is created
   * in a project without a free tier database will be marked as eligible for
   * the free tier. Databases that are created while there is a free tier
   * database will not be eligible for the free tier.
   *
   * @var bool
   */
  public $freeTier;
  /**
   * Output only. The key_prefix for this database. This key_prefix is used, in
   * combination with the project ID ("~") to construct the application ID that
   * is returned from the Cloud Datastore APIs in Google App Engine first
   * generation runtimes. This value may be empty in which case the appid to use
   * for URL-encoded keys is the project_id (eg: foo instead of v~foo).
   *
   * @var string
   */
  public $keyPrefix;
  /**
   * The location of the database. Available locations are listed at
   * https://cloud.google.com/firestore/docs/locations.
   *
   * @var string
   */
  public $locationId;
  /**
   * Optional. The MongoDB compatible API data access mode to use for this
   * database. If not set on write, the default value is
   * DATA_ACCESS_MODE_ENABLED for Enterprise Edition. The value is always
   * DATA_ACCESS_MODE_DISABLED for Standard Edition.
   *
   * @var string
   */
  public $mongodbCompatibleDataAccessMode;
  /**
   * The resource name of the Database. Format:
   * `projects/{project}/databases/{database}`
   *
   * @var string
   */
  public $name;
  /**
   * Whether to enable the PITR feature on this database.
   *
   * @var string
   */
  public $pointInTimeRecoveryEnablement;
  /**
   * Output only. The database resource's prior database ID. This field is only
   * populated for deleted databases.
   *
   * @var string
   */
  public $previousId;
  /**
   * Immutable. The default Realtime Updates mode to use for this database.
   *
   * @var string
   */
  public $realtimeUpdatesMode;
  protected $sourceInfoType = GoogleFirestoreAdminV1SourceInfo::class;
  protected $sourceInfoDataType = '';
  /**
   * Optional. Input only. Immutable. Tag keys/values directly bound to this
   * resource. For example: "123/environment": "production", "123/costCenter":
   * "marketing"
   *
   * @var string[]
   */
  public $tags;
  /**
   * The type of the database. See
   * https://cloud.google.com/datastore/docs/firestore-or-datastore for
   * information about how to choose.
   *
   * @var string
   */
  public $type;
  /**
   * Output only. The system-generated UUID4 for this Database.
   *
   * @var string
   */
  public $uid;
  /**
   * Output only. The timestamp at which this database was most recently
   * updated. Note this only includes updates to the database resource and not
   * data contained by the database.
   *
   * @var string
   */
  public $updateTime;
  /**
   * Output only. The period during which past versions of data are retained in
   * the database. Any read or query can specify a `read_time` within this
   * window, and will read the state of the database at that time. If the PITR
   * feature is enabled, the retention period is 7 days. Otherwise, the
   * retention period is 1 hour.
   *
   * @var string
   */
  public $versionRetentionPeriod;

  /**
   * The App Engine integration mode to use for this database.
   *
   * Accepted values: APP_ENGINE_INTEGRATION_MODE_UNSPECIFIED, ENABLED, DISABLED
   *
   * @param self::APP_ENGINE_INTEGRATION_MODE_* $appEngineIntegrationMode
   */
  public function setAppEngineIntegrationMode($appEngineIntegrationMode)
  {
    $this->appEngineIntegrationMode = $appEngineIntegrationMode;
  }
  /**
   * @return self::APP_ENGINE_INTEGRATION_MODE_*
   */
  public function getAppEngineIntegrationMode()
  {
    return $this->appEngineIntegrationMode;
  }
  /**
   * Optional. Presence indicates CMEK is enabled for this database.
   *
   * @param GoogleFirestoreAdminV1CmekConfig $cmekConfig
   */
  public function setCmekConfig(GoogleFirestoreAdminV1CmekConfig $cmekConfig)
  {
    $this->cmekConfig = $cmekConfig;
  }
  /**
   * @return GoogleFirestoreAdminV1CmekConfig
   */
  public function getCmekConfig()
  {
    return $this->cmekConfig;
  }
  /**
   * The concurrency control mode to use for this database.
   *
   * Accepted values: CONCURRENCY_MODE_UNSPECIFIED, OPTIMISTIC, PESSIMISTIC,
   * OPTIMISTIC_WITH_ENTITY_GROUPS
   *
   * @param self::CONCURRENCY_MODE_* $concurrencyMode
   */
  public function setConcurrencyMode($concurrencyMode)
  {
    $this->concurrencyMode = $concurrencyMode;
  }
  /**
   * @return self::CONCURRENCY_MODE_*
   */
  public function getConcurrencyMode()
  {
    return $this->concurrencyMode;
  }
  /**
   * Output only. The timestamp at which this database was created. Databases
   * created before 2016 do not populate create_time.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Immutable. The edition of the database.
   *
   * Accepted values: DATABASE_EDITION_UNSPECIFIED, STANDARD, ENTERPRISE
   *
   * @param self::DATABASE_EDITION_* $databaseEdition
   */
  public function setDatabaseEdition($databaseEdition)
  {
    $this->databaseEdition = $databaseEdition;
  }
  /**
   * @return self::DATABASE_EDITION_*
   */
  public function getDatabaseEdition()
  {
    return $this->databaseEdition;
  }
  /**
   * State of delete protection for the database.
   *
   * Accepted values: DELETE_PROTECTION_STATE_UNSPECIFIED,
   * DELETE_PROTECTION_DISABLED, DELETE_PROTECTION_ENABLED
   *
   * @param self::DELETE_PROTECTION_STATE_* $deleteProtectionState
   */
  public function setDeleteProtectionState($deleteProtectionState)
  {
    $this->deleteProtectionState = $deleteProtectionState;
  }
  /**
   * @return self::DELETE_PROTECTION_STATE_*
   */
  public function getDeleteProtectionState()
  {
    return $this->deleteProtectionState;
  }
  /**
   * Output only. The timestamp at which this database was deleted. Only set if
   * the database has been deleted.
   *
   * @param string $deleteTime
   */
  public function setDeleteTime($deleteTime)
  {
    $this->deleteTime = $deleteTime;
  }
  /**
   * @return string
   */
  public function getDeleteTime()
  {
    return $this->deleteTime;
  }
  /**
   * Output only. The earliest timestamp at which older versions of the data can
   * be read from the database. See [version_retention_period] above; this field
   * is populated with `now - version_retention_period`. This value is
   * continuously updated, and becomes stale the moment it is queried. If you
   * are using this value to recover data, make sure to account for the time
   * from the moment when the value is queried to the moment when you initiate
   * the recovery.
   *
   * @param string $earliestVersionTime
   */
  public function setEarliestVersionTime($earliestVersionTime)
  {
    $this->earliestVersionTime = $earliestVersionTime;
  }
  /**
   * @return string
   */
  public function getEarliestVersionTime()
  {
    return $this->earliestVersionTime;
  }
  /**
   * This checksum is computed by the server based on the value of other fields,
   * and may be sent on update and delete requests to ensure the client has an
   * up-to-date value before proceeding.
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * Optional. The Firestore API data access mode to use for this database. If
   * not set on write: - the default value is DATA_ACCESS_MODE_DISABLED for
   * Enterprise Edition. - the default value is DATA_ACCESS_MODE_ENABLED for
   * Standard Edition.
   *
   * Accepted values: DATA_ACCESS_MODE_UNSPECIFIED, DATA_ACCESS_MODE_ENABLED,
   * DATA_ACCESS_MODE_DISABLED
   *
   * @param self::FIRESTORE_DATA_ACCESS_MODE_* $firestoreDataAccessMode
   */
  public function setFirestoreDataAccessMode($firestoreDataAccessMode)
  {
    $this->firestoreDataAccessMode = $firestoreDataAccessMode;
  }
  /**
   * @return self::FIRESTORE_DATA_ACCESS_MODE_*
   */
  public function getFirestoreDataAccessMode()
  {
    return $this->firestoreDataAccessMode;
  }
  /**
   * Output only. Background: Free tier is the ability of a Firestore database
   * to use a small amount of resources every day without being charged. Once
   * usage exceeds the free tier limit further usage is charged. Whether this
   * database can make use of the free tier. Only one database per project can
   * be eligible for the free tier. The first (or next) database that is created
   * in a project without a free tier database will be marked as eligible for
   * the free tier. Databases that are created while there is a free tier
   * database will not be eligible for the free tier.
   *
   * @param bool $freeTier
   */
  public function setFreeTier($freeTier)
  {
    $this->freeTier = $freeTier;
  }
  /**
   * @return bool
   */
  public function getFreeTier()
  {
    return $this->freeTier;
  }
  /**
   * Output only. The key_prefix for this database. This key_prefix is used, in
   * combination with the project ID ("~") to construct the application ID that
   * is returned from the Cloud Datastore APIs in Google App Engine first
   * generation runtimes. This value may be empty in which case the appid to use
   * for URL-encoded keys is the project_id (eg: foo instead of v~foo).
   *
   * @param string $keyPrefix
   */
  public function setKeyPrefix($keyPrefix)
  {
    $this->keyPrefix = $keyPrefix;
  }
  /**
   * @return string
   */
  public function getKeyPrefix()
  {
    return $this->keyPrefix;
  }
  /**
   * The location of the database. Available locations are listed at
   * https://cloud.google.com/firestore/docs/locations.
   *
   * @param string $locationId
   */
  public function setLocationId($locationId)
  {
    $this->locationId = $locationId;
  }
  /**
   * @return string
   */
  public function getLocationId()
  {
    return $this->locationId;
  }
  /**
   * Optional. The MongoDB compatible API data access mode to use for this
   * database. If not set on write, the default value is
   * DATA_ACCESS_MODE_ENABLED for Enterprise Edition. The value is always
   * DATA_ACCESS_MODE_DISABLED for Standard Edition.
   *
   * Accepted values: DATA_ACCESS_MODE_UNSPECIFIED, DATA_ACCESS_MODE_ENABLED,
   * DATA_ACCESS_MODE_DISABLED
   *
   * @param self::MONGODB_COMPATIBLE_DATA_ACCESS_MODE_* $mongodbCompatibleDataAccessMode
   */
  public function setMongodbCompatibleDataAccessMode($mongodbCompatibleDataAccessMode)
  {
    $this->mongodbCompatibleDataAccessMode = $mongodbCompatibleDataAccessMode;
  }
  /**
   * @return self::MONGODB_COMPATIBLE_DATA_ACCESS_MODE_*
   */
  public function getMongodbCompatibleDataAccessMode()
  {
    return $this->mongodbCompatibleDataAccessMode;
  }
  /**
   * The resource name of the Database. Format:
   * `projects/{project}/databases/{database}`
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
   * Whether to enable the PITR feature on this database.
   *
   * Accepted values: POINT_IN_TIME_RECOVERY_ENABLEMENT_UNSPECIFIED,
   * POINT_IN_TIME_RECOVERY_ENABLED, POINT_IN_TIME_RECOVERY_DISABLED
   *
   * @param self::POINT_IN_TIME_RECOVERY_ENABLEMENT_* $pointInTimeRecoveryEnablement
   */
  public function setPointInTimeRecoveryEnablement($pointInTimeRecoveryEnablement)
  {
    $this->pointInTimeRecoveryEnablement = $pointInTimeRecoveryEnablement;
  }
  /**
   * @return self::POINT_IN_TIME_RECOVERY_ENABLEMENT_*
   */
  public function getPointInTimeRecoveryEnablement()
  {
    return $this->pointInTimeRecoveryEnablement;
  }
  /**
   * Output only. The database resource's prior database ID. This field is only
   * populated for deleted databases.
   *
   * @param string $previousId
   */
  public function setPreviousId($previousId)
  {
    $this->previousId = $previousId;
  }
  /**
   * @return string
   */
  public function getPreviousId()
  {
    return $this->previousId;
  }
  /**
   * Immutable. The default Realtime Updates mode to use for this database.
   *
   * Accepted values: REALTIME_UPDATES_MODE_UNSPECIFIED,
   * REALTIME_UPDATES_MODE_ENABLED, REALTIME_UPDATES_MODE_DISABLED
   *
   * @param self::REALTIME_UPDATES_MODE_* $realtimeUpdatesMode
   */
  public function setRealtimeUpdatesMode($realtimeUpdatesMode)
  {
    $this->realtimeUpdatesMode = $realtimeUpdatesMode;
  }
  /**
   * @return self::REALTIME_UPDATES_MODE_*
   */
  public function getRealtimeUpdatesMode()
  {
    return $this->realtimeUpdatesMode;
  }
  /**
   * Output only. Information about the provenance of this database.
   *
   * @param GoogleFirestoreAdminV1SourceInfo $sourceInfo
   */
  public function setSourceInfo(GoogleFirestoreAdminV1SourceInfo $sourceInfo)
  {
    $this->sourceInfo = $sourceInfo;
  }
  /**
   * @return GoogleFirestoreAdminV1SourceInfo
   */
  public function getSourceInfo()
  {
    return $this->sourceInfo;
  }
  /**
   * Optional. Input only. Immutable. Tag keys/values directly bound to this
   * resource. For example: "123/environment": "production", "123/costCenter":
   * "marketing"
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
   * The type of the database. See
   * https://cloud.google.com/datastore/docs/firestore-or-datastore for
   * information about how to choose.
   *
   * Accepted values: DATABASE_TYPE_UNSPECIFIED, FIRESTORE_NATIVE,
   * DATASTORE_MODE
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
  /**
   * Output only. The system-generated UUID4 for this Database.
   *
   * @param string $uid
   */
  public function setUid($uid)
  {
    $this->uid = $uid;
  }
  /**
   * @return string
   */
  public function getUid()
  {
    return $this->uid;
  }
  /**
   * Output only. The timestamp at which this database was most recently
   * updated. Note this only includes updates to the database resource and not
   * data contained by the database.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
  /**
   * Output only. The period during which past versions of data are retained in
   * the database. Any read or query can specify a `read_time` within this
   * window, and will read the state of the database at that time. If the PITR
   * feature is enabled, the retention period is 7 days. Otherwise, the
   * retention period is 1 hour.
   *
   * @param string $versionRetentionPeriod
   */
  public function setVersionRetentionPeriod($versionRetentionPeriod)
  {
    $this->versionRetentionPeriod = $versionRetentionPeriod;
  }
  /**
   * @return string
   */
  public function getVersionRetentionPeriod()
  {
    return $this->versionRetentionPeriod;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleFirestoreAdminV1Database::class, 'Google_Service_Firestore_GoogleFirestoreAdminV1Database');
