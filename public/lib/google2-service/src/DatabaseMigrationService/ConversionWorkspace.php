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

namespace Google\Service\DatabaseMigrationService;

class ConversionWorkspace extends \Google\Model
{
  /**
   * Use this value for on-premise source database instances and ORACLE.
   */
  public const DESTINATION_PROVIDER_DATABASE_PROVIDER_UNSPECIFIED = 'DATABASE_PROVIDER_UNSPECIFIED';
  /**
   * Cloud SQL is the source instance provider.
   */
  public const DESTINATION_PROVIDER_CLOUDSQL = 'CLOUDSQL';
  /**
   * Amazon RDS is the source instance provider.
   */
  public const DESTINATION_PROVIDER_RDS = 'RDS';
  /**
   * Amazon Aurora is the source instance provider.
   */
  public const DESTINATION_PROVIDER_AURORA = 'AURORA';
  /**
   * AlloyDB for PostgreSQL is the source instance provider.
   */
  public const DESTINATION_PROVIDER_ALLOYDB = 'ALLOYDB';
  /**
   * Microsoft Azure Database for MySQL/PostgreSQL.
   */
  public const DESTINATION_PROVIDER_AZURE_DATABASE = 'AZURE_DATABASE';
  /**
   * Use this value for on-premise source database instances and ORACLE.
   */
  public const SOURCE_PROVIDER_DATABASE_PROVIDER_UNSPECIFIED = 'DATABASE_PROVIDER_UNSPECIFIED';
  /**
   * Cloud SQL is the source instance provider.
   */
  public const SOURCE_PROVIDER_CLOUDSQL = 'CLOUDSQL';
  /**
   * Amazon RDS is the source instance provider.
   */
  public const SOURCE_PROVIDER_RDS = 'RDS';
  /**
   * Amazon Aurora is the source instance provider.
   */
  public const SOURCE_PROVIDER_AURORA = 'AURORA';
  /**
   * AlloyDB for PostgreSQL is the source instance provider.
   */
  public const SOURCE_PROVIDER_ALLOYDB = 'ALLOYDB';
  /**
   * Microsoft Azure Database for MySQL/PostgreSQL.
   */
  public const SOURCE_PROVIDER_AZURE_DATABASE = 'AZURE_DATABASE';
  /**
   * Output only. The timestamp when the workspace resource was created.
   *
   * @var string
   */
  public $createTime;
  protected $destinationType = DatabaseEngineInfo::class;
  protected $destinationDataType = '';
  /**
   * Optional. The provider for the destination database.
   *
   * @var string
   */
  public $destinationProvider;
  /**
   * Optional. The display name for the workspace.
   *
   * @var string
   */
  public $displayName;
  /**
   * Optional. A generic list of settings for the workspace. The settings are
   * database pair dependant and can indicate default behavior for the mapping
   * rules engine or turn on or off specific features. Such examples can be:
   * convert_foreign_key_to_interleave=true, skip_triggers=false,
   * ignore_non_table_synonyms=true
   *
   * @var string[]
   */
  public $globalSettings;
  /**
   * Output only. Whether the workspace has uncommitted changes (changes which
   * were made after the workspace was committed).
   *
   * @var bool
   */
  public $hasUncommittedChanges;
  /**
   * Output only. The latest commit ID.
   *
   * @var string
   */
  public $latestCommitId;
  /**
   * Output only. The timestamp when the workspace was committed.
   *
   * @var string
   */
  public $latestCommitTime;
  /**
   * Full name of the workspace resource, in the form of: projects/{project}/loc
   * ations/{location}/conversionWorkspaces/{conversion_workspace}.
   *
   * @var string
   */
  public $name;
  protected $sourceType = DatabaseEngineInfo::class;
  protected $sourceDataType = '';
  /**
   * Optional. The provider for the source database.
   *
   * @var string
   */
  public $sourceProvider;
  /**
   * Output only. The timestamp when the workspace resource was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. The timestamp when the workspace resource was created.
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
   * Required. The destination engine details.
   *
   * @param DatabaseEngineInfo $destination
   */
  public function setDestination(DatabaseEngineInfo $destination)
  {
    $this->destination = $destination;
  }
  /**
   * @return DatabaseEngineInfo
   */
  public function getDestination()
  {
    return $this->destination;
  }
  /**
   * Optional. The provider for the destination database.
   *
   * Accepted values: DATABASE_PROVIDER_UNSPECIFIED, CLOUDSQL, RDS, AURORA,
   * ALLOYDB, AZURE_DATABASE
   *
   * @param self::DESTINATION_PROVIDER_* $destinationProvider
   */
  public function setDestinationProvider($destinationProvider)
  {
    $this->destinationProvider = $destinationProvider;
  }
  /**
   * @return self::DESTINATION_PROVIDER_*
   */
  public function getDestinationProvider()
  {
    return $this->destinationProvider;
  }
  /**
   * Optional. The display name for the workspace.
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
   * Optional. A generic list of settings for the workspace. The settings are
   * database pair dependant and can indicate default behavior for the mapping
   * rules engine or turn on or off specific features. Such examples can be:
   * convert_foreign_key_to_interleave=true, skip_triggers=false,
   * ignore_non_table_synonyms=true
   *
   * @param string[] $globalSettings
   */
  public function setGlobalSettings($globalSettings)
  {
    $this->globalSettings = $globalSettings;
  }
  /**
   * @return string[]
   */
  public function getGlobalSettings()
  {
    return $this->globalSettings;
  }
  /**
   * Output only. Whether the workspace has uncommitted changes (changes which
   * were made after the workspace was committed).
   *
   * @param bool $hasUncommittedChanges
   */
  public function setHasUncommittedChanges($hasUncommittedChanges)
  {
    $this->hasUncommittedChanges = $hasUncommittedChanges;
  }
  /**
   * @return bool
   */
  public function getHasUncommittedChanges()
  {
    return $this->hasUncommittedChanges;
  }
  /**
   * Output only. The latest commit ID.
   *
   * @param string $latestCommitId
   */
  public function setLatestCommitId($latestCommitId)
  {
    $this->latestCommitId = $latestCommitId;
  }
  /**
   * @return string
   */
  public function getLatestCommitId()
  {
    return $this->latestCommitId;
  }
  /**
   * Output only. The timestamp when the workspace was committed.
   *
   * @param string $latestCommitTime
   */
  public function setLatestCommitTime($latestCommitTime)
  {
    $this->latestCommitTime = $latestCommitTime;
  }
  /**
   * @return string
   */
  public function getLatestCommitTime()
  {
    return $this->latestCommitTime;
  }
  /**
   * Full name of the workspace resource, in the form of: projects/{project}/loc
   * ations/{location}/conversionWorkspaces/{conversion_workspace}.
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
   * Required. The source engine details.
   *
   * @param DatabaseEngineInfo $source
   */
  public function setSource(DatabaseEngineInfo $source)
  {
    $this->source = $source;
  }
  /**
   * @return DatabaseEngineInfo
   */
  public function getSource()
  {
    return $this->source;
  }
  /**
   * Optional. The provider for the source database.
   *
   * Accepted values: DATABASE_PROVIDER_UNSPECIFIED, CLOUDSQL, RDS, AURORA,
   * ALLOYDB, AZURE_DATABASE
   *
   * @param self::SOURCE_PROVIDER_* $sourceProvider
   */
  public function setSourceProvider($sourceProvider)
  {
    $this->sourceProvider = $sourceProvider;
  }
  /**
   * @return self::SOURCE_PROVIDER_*
   */
  public function getSourceProvider()
  {
    return $this->sourceProvider;
  }
  /**
   * Output only. The timestamp when the workspace resource was last updated.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ConversionWorkspace::class, 'Google_Service_DatabaseMigrationService_ConversionWorkspace');
