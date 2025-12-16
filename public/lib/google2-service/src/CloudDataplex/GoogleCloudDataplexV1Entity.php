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

namespace Google\Service\CloudDataplex;

class GoogleCloudDataplexV1Entity extends \Google\Model
{
  /**
   * Storage system unspecified.
   */
  public const SYSTEM_STORAGE_SYSTEM_UNSPECIFIED = 'STORAGE_SYSTEM_UNSPECIFIED';
  /**
   * The entity data is contained within a Cloud Storage bucket.
   */
  public const SYSTEM_CLOUD_STORAGE = 'CLOUD_STORAGE';
  /**
   * The entity data is contained within a BigQuery dataset.
   */
  public const SYSTEM_BIGQUERY = 'BIGQUERY';
  /**
   * Type unspecified.
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * Structured and semi-structured data.
   */
  public const TYPE_TABLE = 'TABLE';
  /**
   * Unstructured data.
   */
  public const TYPE_FILESET = 'FILESET';
  protected $accessType = GoogleCloudDataplexV1StorageAccess::class;
  protected $accessDataType = '';
  /**
   * Required. Immutable. The ID of the asset associated with the storage
   * location containing the entity data. The entity must be with in the same
   * zone with the asset.
   *
   * @var string
   */
  public $asset;
  /**
   * Output only. The name of the associated Data Catalog entry.
   *
   * @var string
   */
  public $catalogEntry;
  protected $compatibilityType = GoogleCloudDataplexV1EntityCompatibilityStatus::class;
  protected $compatibilityDataType = '';
  /**
   * Output only. The time when the entity was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Required. Immutable. The storage path of the entity data. For Cloud Storage
   * data, this is the fully-qualified path to the entity, such as
   * gs://bucket/path/to/data. For BigQuery data, this is the name of the table
   * resource, such as projects/project_id/datasets/dataset_id/tables/table_id.
   *
   * @var string
   */
  public $dataPath;
  /**
   * Optional. The set of items within the data path constituting the data in
   * the entity, represented as a glob path. Example:
   * gs://bucket/path/to/data*.csv.
   *
   * @var string
   */
  public $dataPathPattern;
  /**
   * Optional. User friendly longer description text. Must be shorter than or
   * equal to 1024 characters.
   *
   * @var string
   */
  public $description;
  /**
   * Optional. Display name must be shorter than or equal to 256 characters.
   *
   * @var string
   */
  public $displayName;
  /**
   * Optional. The etag associated with the entity, which can be retrieved with
   * a GetEntity request. Required for update and delete requests.
   *
   * @var string
   */
  public $etag;
  protected $formatType = GoogleCloudDataplexV1StorageFormat::class;
  protected $formatDataType = '';
  /**
   * Required. A user-provided entity ID. It is mutable, and will be used as the
   * published table name. Specifying a new ID in an update entity request will
   * override the existing value. The ID must contain only letters (a-z, A-Z),
   * numbers (0-9), and underscores, and consist of 256 or fewer characters.
   *
   * @var string
   */
  public $id;
  /**
   * Output only. The resource name of the entity, of the form: projects/{projec
   * t_number}/locations/{location_id}/lakes/{lake_id}/zones/{zone_id}/entities/
   * {id}.
   *
   * @var string
   */
  public $name;
  protected $schemaType = GoogleCloudDataplexV1Schema::class;
  protected $schemaDataType = '';
  /**
   * Required. Immutable. Identifies the storage system of the entity data.
   *
   * @var string
   */
  public $system;
  /**
   * Required. Immutable. The type of entity.
   *
   * @var string
   */
  public $type;
  /**
   * Output only. System generated unique ID for the Entity. This ID will be
   * different if the Entity is deleted and re-created with the same name.
   *
   * @var string
   */
  public $uid;
  /**
   * Output only. The time when the entity was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. Identifies the access mechanism to the entity. Not user
   * settable.
   *
   * @param GoogleCloudDataplexV1StorageAccess $access
   */
  public function setAccess(GoogleCloudDataplexV1StorageAccess $access)
  {
    $this->access = $access;
  }
  /**
   * @return GoogleCloudDataplexV1StorageAccess
   */
  public function getAccess()
  {
    return $this->access;
  }
  /**
   * Required. Immutable. The ID of the asset associated with the storage
   * location containing the entity data. The entity must be with in the same
   * zone with the asset.
   *
   * @param string $asset
   */
  public function setAsset($asset)
  {
    $this->asset = $asset;
  }
  /**
   * @return string
   */
  public function getAsset()
  {
    return $this->asset;
  }
  /**
   * Output only. The name of the associated Data Catalog entry.
   *
   * @param string $catalogEntry
   */
  public function setCatalogEntry($catalogEntry)
  {
    $this->catalogEntry = $catalogEntry;
  }
  /**
   * @return string
   */
  public function getCatalogEntry()
  {
    return $this->catalogEntry;
  }
  /**
   * Output only. Metadata stores that the entity is compatible with.
   *
   * @param GoogleCloudDataplexV1EntityCompatibilityStatus $compatibility
   */
  public function setCompatibility(GoogleCloudDataplexV1EntityCompatibilityStatus $compatibility)
  {
    $this->compatibility = $compatibility;
  }
  /**
   * @return GoogleCloudDataplexV1EntityCompatibilityStatus
   */
  public function getCompatibility()
  {
    return $this->compatibility;
  }
  /**
   * Output only. The time when the entity was created.
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
   * Required. Immutable. The storage path of the entity data. For Cloud Storage
   * data, this is the fully-qualified path to the entity, such as
   * gs://bucket/path/to/data. For BigQuery data, this is the name of the table
   * resource, such as projects/project_id/datasets/dataset_id/tables/table_id.
   *
   * @param string $dataPath
   */
  public function setDataPath($dataPath)
  {
    $this->dataPath = $dataPath;
  }
  /**
   * @return string
   */
  public function getDataPath()
  {
    return $this->dataPath;
  }
  /**
   * Optional. The set of items within the data path constituting the data in
   * the entity, represented as a glob path. Example:
   * gs://bucket/path/to/data*.csv.
   *
   * @param string $dataPathPattern
   */
  public function setDataPathPattern($dataPathPattern)
  {
    $this->dataPathPattern = $dataPathPattern;
  }
  /**
   * @return string
   */
  public function getDataPathPattern()
  {
    return $this->dataPathPattern;
  }
  /**
   * Optional. User friendly longer description text. Must be shorter than or
   * equal to 1024 characters.
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
   * Optional. Display name must be shorter than or equal to 256 characters.
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
   * Optional. The etag associated with the entity, which can be retrieved with
   * a GetEntity request. Required for update and delete requests.
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
   * Required. Identifies the storage format of the entity data. It does not
   * apply to entities with data stored in BigQuery.
   *
   * @param GoogleCloudDataplexV1StorageFormat $format
   */
  public function setFormat(GoogleCloudDataplexV1StorageFormat $format)
  {
    $this->format = $format;
  }
  /**
   * @return GoogleCloudDataplexV1StorageFormat
   */
  public function getFormat()
  {
    return $this->format;
  }
  /**
   * Required. A user-provided entity ID. It is mutable, and will be used as the
   * published table name. Specifying a new ID in an update entity request will
   * override the existing value. The ID must contain only letters (a-z, A-Z),
   * numbers (0-9), and underscores, and consist of 256 or fewer characters.
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
   * Output only. The resource name of the entity, of the form: projects/{projec
   * t_number}/locations/{location_id}/lakes/{lake_id}/zones/{zone_id}/entities/
   * {id}.
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
   * Required. The description of the data structure and layout. The schema is
   * not included in list responses. It is only included in SCHEMA and FULL
   * entity views of a GetEntity response.
   *
   * @param GoogleCloudDataplexV1Schema $schema
   */
  public function setSchema(GoogleCloudDataplexV1Schema $schema)
  {
    $this->schema = $schema;
  }
  /**
   * @return GoogleCloudDataplexV1Schema
   */
  public function getSchema()
  {
    return $this->schema;
  }
  /**
   * Required. Immutable. Identifies the storage system of the entity data.
   *
   * Accepted values: STORAGE_SYSTEM_UNSPECIFIED, CLOUD_STORAGE, BIGQUERY
   *
   * @param self::SYSTEM_* $system
   */
  public function setSystem($system)
  {
    $this->system = $system;
  }
  /**
   * @return self::SYSTEM_*
   */
  public function getSystem()
  {
    return $this->system;
  }
  /**
   * Required. Immutable. The type of entity.
   *
   * Accepted values: TYPE_UNSPECIFIED, TABLE, FILESET
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
   * Output only. System generated unique ID for the Entity. This ID will be
   * different if the Entity is deleted and re-created with the same name.
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
   * Output only. The time when the entity was last updated.
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
class_alias(GoogleCloudDataplexV1Entity::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1Entity');
