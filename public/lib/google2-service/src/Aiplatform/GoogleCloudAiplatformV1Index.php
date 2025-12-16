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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1Index extends \Google\Collection
{
  /**
   * Should not be used.
   */
  public const INDEX_UPDATE_METHOD_INDEX_UPDATE_METHOD_UNSPECIFIED = 'INDEX_UPDATE_METHOD_UNSPECIFIED';
  /**
   * BatchUpdate: user can call UpdateIndex with files on Cloud Storage of
   * Datapoints to update.
   */
  public const INDEX_UPDATE_METHOD_BATCH_UPDATE = 'BATCH_UPDATE';
  /**
   * StreamUpdate: user can call UpsertDatapoints/DeleteDatapoints to update the
   * Index and the updates will be applied in corresponding DeployedIndexes in
   * nearly real-time.
   */
  public const INDEX_UPDATE_METHOD_STREAM_UPDATE = 'STREAM_UPDATE';
  protected $collection_key = 'deployedIndexes';
  /**
   * Output only. Timestamp when this Index was created.
   *
   * @var string
   */
  public $createTime;
  protected $deployedIndexesType = GoogleCloudAiplatformV1DeployedIndexRef::class;
  protected $deployedIndexesDataType = 'array';
  /**
   * The description of the Index.
   *
   * @var string
   */
  public $description;
  /**
   * Required. The display name of the Index. The name can be up to 128
   * characters long and can consist of any UTF-8 characters.
   *
   * @var string
   */
  public $displayName;
  protected $encryptionSpecType = GoogleCloudAiplatformV1EncryptionSpec::class;
  protected $encryptionSpecDataType = '';
  /**
   * Used to perform consistent read-modify-write updates. If not set, a blind
   * "overwrite" update happens.
   *
   * @var string
   */
  public $etag;
  protected $indexStatsType = GoogleCloudAiplatformV1IndexStats::class;
  protected $indexStatsDataType = '';
  /**
   * Immutable. The update method to use with this Index. If not set,
   * BATCH_UPDATE will be used by default.
   *
   * @var string
   */
  public $indexUpdateMethod;
  /**
   * The labels with user-defined metadata to organize your Indexes. Label keys
   * and values can be no longer than 64 characters (Unicode codepoints), can
   * only contain lowercase letters, numeric characters, underscores and dashes.
   * International characters are allowed. See https://goo.gl/xmQnxf for more
   * information and examples of labels.
   *
   * @var string[]
   */
  public $labels;
  /**
   * An additional information about the Index; the schema of the metadata can
   * be found in metadata_schema.
   *
   * @var array
   */
  public $metadata;
  /**
   * Immutable. Points to a YAML file stored on Google Cloud Storage describing
   * additional information about the Index, that is specific to it. Unset if
   * the Index does not have any additional information. The schema is defined
   * as an OpenAPI 3.0.2 [Schema Object](https://github.com/OAI/OpenAPI-
   * Specification/blob/main/versions/3.0.2.md#schemaObject). Note: The URI
   * given on output will be immutable and probably different, including the URI
   * scheme, than the one given on input. The output URI will point to a
   * location where the user only has a read access.
   *
   * @var string
   */
  public $metadataSchemaUri;
  /**
   * Output only. The resource name of the Index.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Reserved for future use.
   *
   * @var bool
   */
  public $satisfiesPzi;
  /**
   * Output only. Reserved for future use.
   *
   * @var bool
   */
  public $satisfiesPzs;
  /**
   * Output only. Timestamp when this Index was most recently updated. This also
   * includes any update to the contents of the Index. Note that Operations
   * working on this Index may have their
   * Operations.metadata.generic_metadata.update_time a little after the value
   * of this timestamp, yet that does not mean their results are not already
   * reflected in the Index. Result of any successfully completed Operation on
   * the Index is reflected in it.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. Timestamp when this Index was created.
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
   * Output only. The pointers to DeployedIndexes created from this Index. An
   * Index can be only deleted if all its DeployedIndexes had been undeployed
   * first.
   *
   * @param GoogleCloudAiplatformV1DeployedIndexRef[] $deployedIndexes
   */
  public function setDeployedIndexes($deployedIndexes)
  {
    $this->deployedIndexes = $deployedIndexes;
  }
  /**
   * @return GoogleCloudAiplatformV1DeployedIndexRef[]
   */
  public function getDeployedIndexes()
  {
    return $this->deployedIndexes;
  }
  /**
   * The description of the Index.
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
   * Required. The display name of the Index. The name can be up to 128
   * characters long and can consist of any UTF-8 characters.
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
   * Immutable. Customer-managed encryption key spec for an Index. If set, this
   * Index and all sub-resources of this Index will be secured by this key.
   *
   * @param GoogleCloudAiplatformV1EncryptionSpec $encryptionSpec
   */
  public function setEncryptionSpec(GoogleCloudAiplatformV1EncryptionSpec $encryptionSpec)
  {
    $this->encryptionSpec = $encryptionSpec;
  }
  /**
   * @return GoogleCloudAiplatformV1EncryptionSpec
   */
  public function getEncryptionSpec()
  {
    return $this->encryptionSpec;
  }
  /**
   * Used to perform consistent read-modify-write updates. If not set, a blind
   * "overwrite" update happens.
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
   * Output only. Stats of the index resource.
   *
   * @param GoogleCloudAiplatformV1IndexStats $indexStats
   */
  public function setIndexStats(GoogleCloudAiplatformV1IndexStats $indexStats)
  {
    $this->indexStats = $indexStats;
  }
  /**
   * @return GoogleCloudAiplatformV1IndexStats
   */
  public function getIndexStats()
  {
    return $this->indexStats;
  }
  /**
   * Immutable. The update method to use with this Index. If not set,
   * BATCH_UPDATE will be used by default.
   *
   * Accepted values: INDEX_UPDATE_METHOD_UNSPECIFIED, BATCH_UPDATE,
   * STREAM_UPDATE
   *
   * @param self::INDEX_UPDATE_METHOD_* $indexUpdateMethod
   */
  public function setIndexUpdateMethod($indexUpdateMethod)
  {
    $this->indexUpdateMethod = $indexUpdateMethod;
  }
  /**
   * @return self::INDEX_UPDATE_METHOD_*
   */
  public function getIndexUpdateMethod()
  {
    return $this->indexUpdateMethod;
  }
  /**
   * The labels with user-defined metadata to organize your Indexes. Label keys
   * and values can be no longer than 64 characters (Unicode codepoints), can
   * only contain lowercase letters, numeric characters, underscores and dashes.
   * International characters are allowed. See https://goo.gl/xmQnxf for more
   * information and examples of labels.
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
   * An additional information about the Index; the schema of the metadata can
   * be found in metadata_schema.
   *
   * @param array $metadata
   */
  public function setMetadata($metadata)
  {
    $this->metadata = $metadata;
  }
  /**
   * @return array
   */
  public function getMetadata()
  {
    return $this->metadata;
  }
  /**
   * Immutable. Points to a YAML file stored on Google Cloud Storage describing
   * additional information about the Index, that is specific to it. Unset if
   * the Index does not have any additional information. The schema is defined
   * as an OpenAPI 3.0.2 [Schema Object](https://github.com/OAI/OpenAPI-
   * Specification/blob/main/versions/3.0.2.md#schemaObject). Note: The URI
   * given on output will be immutable and probably different, including the URI
   * scheme, than the one given on input. The output URI will point to a
   * location where the user only has a read access.
   *
   * @param string $metadataSchemaUri
   */
  public function setMetadataSchemaUri($metadataSchemaUri)
  {
    $this->metadataSchemaUri = $metadataSchemaUri;
  }
  /**
   * @return string
   */
  public function getMetadataSchemaUri()
  {
    return $this->metadataSchemaUri;
  }
  /**
   * Output only. The resource name of the Index.
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
   * Output only. Reserved for future use.
   *
   * @param bool $satisfiesPzi
   */
  public function setSatisfiesPzi($satisfiesPzi)
  {
    $this->satisfiesPzi = $satisfiesPzi;
  }
  /**
   * @return bool
   */
  public function getSatisfiesPzi()
  {
    return $this->satisfiesPzi;
  }
  /**
   * Output only. Reserved for future use.
   *
   * @param bool $satisfiesPzs
   */
  public function setSatisfiesPzs($satisfiesPzs)
  {
    $this->satisfiesPzs = $satisfiesPzs;
  }
  /**
   * @return bool
   */
  public function getSatisfiesPzs()
  {
    return $this->satisfiesPzs;
  }
  /**
   * Output only. Timestamp when this Index was most recently updated. This also
   * includes any update to the contents of the Index. Note that Operations
   * working on this Index may have their
   * Operations.metadata.generic_metadata.update_time a little after the value
   * of this timestamp, yet that does not mean their results are not already
   * reflected in the Index. Result of any successfully completed Operation on
   * the Index is reflected in it.
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
class_alias(GoogleCloudAiplatformV1Index::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1Index');
