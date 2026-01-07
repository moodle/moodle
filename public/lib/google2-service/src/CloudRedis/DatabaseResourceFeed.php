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

class DatabaseResourceFeed extends \Google\Model
{
  public const FEED_TYPE_FEEDTYPE_UNSPECIFIED = 'FEEDTYPE_UNSPECIFIED';
  /**
   * Database resource metadata feed from control plane
   */
  public const FEED_TYPE_RESOURCE_METADATA = 'RESOURCE_METADATA';
  /**
   * Database resource monitoring data
   */
  public const FEED_TYPE_OBSERVABILITY_DATA = 'OBSERVABILITY_DATA';
  /**
   * Database resource security health signal data
   */
  public const FEED_TYPE_SECURITY_FINDING_DATA = 'SECURITY_FINDING_DATA';
  /**
   * Database resource recommendation signal data
   */
  public const FEED_TYPE_RECOMMENDATION_SIGNAL_DATA = 'RECOMMENDATION_SIGNAL_DATA';
  /**
   * Database config based signal data
   */
  public const FEED_TYPE_CONFIG_BASED_SIGNAL_DATA = 'CONFIG_BASED_SIGNAL_DATA';
  /**
   * Database resource metadata from BackupDR
   */
  public const FEED_TYPE_BACKUPDR_METADATA = 'BACKUPDR_METADATA';
  /**
   * Database resource signal data
   */
  public const FEED_TYPE_DATABASE_RESOURCE_SIGNAL_DATA = 'DATABASE_RESOURCE_SIGNAL_DATA';
  protected $backupdrMetadataType = BackupDRMetadata::class;
  protected $backupdrMetadataDataType = '';
  protected $configBasedSignalDataType = ConfigBasedSignalData::class;
  protected $configBasedSignalDataDataType = '';
  protected $databaseResourceSignalDataType = DatabaseResourceSignalData::class;
  protected $databaseResourceSignalDataDataType = '';
  /**
   * Required. Timestamp when feed is generated.
   *
   * @var string
   */
  public $feedTimestamp;
  /**
   * Required. Type feed to be ingested into condor
   *
   * @var string
   */
  public $feedType;
  protected $observabilityMetricDataType = ObservabilityMetricData::class;
  protected $observabilityMetricDataDataType = '';
  protected $recommendationSignalDataType = DatabaseResourceRecommendationSignalData::class;
  protected $recommendationSignalDataDataType = '';
  protected $resourceHealthSignalDataType = DatabaseResourceHealthSignalData::class;
  protected $resourceHealthSignalDataDataType = '';
  protected $resourceIdType = DatabaseResourceId::class;
  protected $resourceIdDataType = '';
  protected $resourceMetadataType = DatabaseResourceMetadata::class;
  protected $resourceMetadataDataType = '';
  /**
   * Optional. If true, the feed won't be ingested by DB Center. This indicates
   * that the feed is intentionally skipped. For example, BackupDR feeds are
   * only needed for resources integrated with DB Center (e.g., CloudSQL,
   * AlloyDB). Feeds for non-integrated resources (e.g., Compute Engine,
   * Persistent Disk) can be skipped.
   *
   * @var bool
   */
  public $skipIngestion;

  /**
   * BackupDR metadata is used to ingest metadata from BackupDR.
   *
   * @param BackupDRMetadata $backupdrMetadata
   */
  public function setBackupdrMetadata(BackupDRMetadata $backupdrMetadata)
  {
    $this->backupdrMetadata = $backupdrMetadata;
  }
  /**
   * @return BackupDRMetadata
   */
  public function getBackupdrMetadata()
  {
    return $this->backupdrMetadata;
  }
  /**
   * Config based signal data is used to ingest signals that are generated based
   * on the configuration of the database resource.
   *
   * @param ConfigBasedSignalData $configBasedSignalData
   */
  public function setConfigBasedSignalData(ConfigBasedSignalData $configBasedSignalData)
  {
    $this->configBasedSignalData = $configBasedSignalData;
  }
  /**
   * @return ConfigBasedSignalData
   */
  public function getConfigBasedSignalData()
  {
    return $this->configBasedSignalData;
  }
  /**
   * Database resource signal data is used to ingest signals from database
   * resource signal feeds.
   *
   * @param DatabaseResourceSignalData $databaseResourceSignalData
   */
  public function setDatabaseResourceSignalData(DatabaseResourceSignalData $databaseResourceSignalData)
  {
    $this->databaseResourceSignalData = $databaseResourceSignalData;
  }
  /**
   * @return DatabaseResourceSignalData
   */
  public function getDatabaseResourceSignalData()
  {
    return $this->databaseResourceSignalData;
  }
  /**
   * Required. Timestamp when feed is generated.
   *
   * @param string $feedTimestamp
   */
  public function setFeedTimestamp($feedTimestamp)
  {
    $this->feedTimestamp = $feedTimestamp;
  }
  /**
   * @return string
   */
  public function getFeedTimestamp()
  {
    return $this->feedTimestamp;
  }
  /**
   * Required. Type feed to be ingested into condor
   *
   * Accepted values: FEEDTYPE_UNSPECIFIED, RESOURCE_METADATA,
   * OBSERVABILITY_DATA, SECURITY_FINDING_DATA, RECOMMENDATION_SIGNAL_DATA,
   * CONFIG_BASED_SIGNAL_DATA, BACKUPDR_METADATA, DATABASE_RESOURCE_SIGNAL_DATA
   *
   * @param self::FEED_TYPE_* $feedType
   */
  public function setFeedType($feedType)
  {
    $this->feedType = $feedType;
  }
  /**
   * @return self::FEED_TYPE_*
   */
  public function getFeedType()
  {
    return $this->feedType;
  }
  /**
   * @param ObservabilityMetricData $observabilityMetricData
   */
  public function setObservabilityMetricData(ObservabilityMetricData $observabilityMetricData)
  {
    $this->observabilityMetricData = $observabilityMetricData;
  }
  /**
   * @return ObservabilityMetricData
   */
  public function getObservabilityMetricData()
  {
    return $this->observabilityMetricData;
  }
  /**
   * @param DatabaseResourceRecommendationSignalData $recommendationSignalData
   */
  public function setRecommendationSignalData(DatabaseResourceRecommendationSignalData $recommendationSignalData)
  {
    $this->recommendationSignalData = $recommendationSignalData;
  }
  /**
   * @return DatabaseResourceRecommendationSignalData
   */
  public function getRecommendationSignalData()
  {
    return $this->recommendationSignalData;
  }
  /**
   * @param DatabaseResourceHealthSignalData $resourceHealthSignalData
   */
  public function setResourceHealthSignalData(DatabaseResourceHealthSignalData $resourceHealthSignalData)
  {
    $this->resourceHealthSignalData = $resourceHealthSignalData;
  }
  /**
   * @return DatabaseResourceHealthSignalData
   */
  public function getResourceHealthSignalData()
  {
    return $this->resourceHealthSignalData;
  }
  /**
   * Primary key associated with the Resource. resource_id is available in
   * individual feed level as well.
   *
   * @deprecated
   * @param DatabaseResourceId $resourceId
   */
  public function setResourceId(DatabaseResourceId $resourceId)
  {
    $this->resourceId = $resourceId;
  }
  /**
   * @deprecated
   * @return DatabaseResourceId
   */
  public function getResourceId()
  {
    return $this->resourceId;
  }
  /**
   * @param DatabaseResourceMetadata $resourceMetadata
   */
  public function setResourceMetadata(DatabaseResourceMetadata $resourceMetadata)
  {
    $this->resourceMetadata = $resourceMetadata;
  }
  /**
   * @return DatabaseResourceMetadata
   */
  public function getResourceMetadata()
  {
    return $this->resourceMetadata;
  }
  /**
   * Optional. If true, the feed won't be ingested by DB Center. This indicates
   * that the feed is intentionally skipped. For example, BackupDR feeds are
   * only needed for resources integrated with DB Center (e.g., CloudSQL,
   * AlloyDB). Feeds for non-integrated resources (e.g., Compute Engine,
   * Persistent Disk) can be skipped.
   *
   * @param bool $skipIngestion
   */
  public function setSkipIngestion($skipIngestion)
  {
    $this->skipIngestion = $skipIngestion;
  }
  /**
   * @return bool
   */
  public function getSkipIngestion()
  {
    return $this->skipIngestion;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DatabaseResourceFeed::class, 'Google_Service_CloudRedis_DatabaseResourceFeed');
