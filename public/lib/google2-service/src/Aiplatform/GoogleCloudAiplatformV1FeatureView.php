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

class GoogleCloudAiplatformV1FeatureView extends \Google\Model
{
  /**
   * By default, the project-level Vertex AI Service Agent is enabled.
   */
  public const SERVICE_AGENT_TYPE_SERVICE_AGENT_TYPE_UNSPECIFIED = 'SERVICE_AGENT_TYPE_UNSPECIFIED';
  /**
   * Indicates the project-level Vertex AI Service Agent
   * (https://cloud.google.com/vertex-ai/docs/general/access-control#service-
   * agents) will be used during sync jobs.
   */
  public const SERVICE_AGENT_TYPE_SERVICE_AGENT_TYPE_PROJECT = 'SERVICE_AGENT_TYPE_PROJECT';
  /**
   * Enable a FeatureView service account to be created by Vertex AI and output
   * in the field `service_account_email`. This service account will be used to
   * read from the source BigQuery table during sync.
   */
  public const SERVICE_AGENT_TYPE_SERVICE_AGENT_TYPE_FEATURE_VIEW = 'SERVICE_AGENT_TYPE_FEATURE_VIEW';
  protected $bigQuerySourceType = GoogleCloudAiplatformV1FeatureViewBigQuerySource::class;
  protected $bigQuerySourceDataType = '';
  protected $bigtableMetadataType = GoogleCloudAiplatformV1FeatureViewBigtableMetadata::class;
  protected $bigtableMetadataDataType = '';
  /**
   * Output only. Timestamp when this FeatureView was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. Used to perform consistent read-modify-write updates. If not set,
   * a blind "overwrite" update happens.
   *
   * @var string
   */
  public $etag;
  protected $featureRegistrySourceType = GoogleCloudAiplatformV1FeatureViewFeatureRegistrySource::class;
  protected $featureRegistrySourceDataType = '';
  protected $indexConfigType = GoogleCloudAiplatformV1FeatureViewIndexConfig::class;
  protected $indexConfigDataType = '';
  /**
   * Optional. The labels with user-defined metadata to organize your
   * FeatureViews. Label keys and values can be no longer than 64 characters
   * (Unicode codepoints), can only contain lowercase letters, numeric
   * characters, underscores and dashes. International characters are allowed.
   * See https://goo.gl/xmQnxf for more information on and examples of labels.
   * No more than 64 user labels can be associated with one
   * FeatureOnlineStore(System labels are excluded)." System reserved label keys
   * are prefixed with "aiplatform.googleapis.com/" and are immutable.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Identifier. Name of the FeatureView. Format: `projects/{project}/locations/
   * {location}/featureOnlineStores/{feature_online_store}/featureViews/{feature
   * _view}`
   *
   * @var string
   */
  public $name;
  protected $optimizedConfigType = GoogleCloudAiplatformV1FeatureViewOptimizedConfig::class;
  protected $optimizedConfigDataType = '';
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
   * Output only. A Service Account unique to this FeatureView. The role
   * bigquery.dataViewer should be granted to this service account to allow
   * Vertex AI Feature Store to sync data to the online store.
   *
   * @var string
   */
  public $serviceAccountEmail;
  /**
   * Optional. Service agent type used during data sync. By default, the Vertex
   * AI Service Agent is used. When using an IAM Policy to isolate this
   * FeatureView within a project, a separate service account should be
   * provisioned by setting this field to `SERVICE_AGENT_TYPE_FEATURE_VIEW`.
   * This will generate a separate service account to access the BigQuery source
   * table.
   *
   * @var string
   */
  public $serviceAgentType;
  protected $syncConfigType = GoogleCloudAiplatformV1FeatureViewSyncConfig::class;
  protected $syncConfigDataType = '';
  /**
   * Output only. Timestamp when this FeatureView was last updated.
   *
   * @var string
   */
  public $updateTime;
  protected $vertexRagSourceType = GoogleCloudAiplatformV1FeatureViewVertexRagSource::class;
  protected $vertexRagSourceDataType = '';

  /**
   * Optional. Configures how data is supposed to be extracted from a BigQuery
   * source to be loaded onto the FeatureOnlineStore.
   *
   * @param GoogleCloudAiplatformV1FeatureViewBigQuerySource $bigQuerySource
   */
  public function setBigQuerySource(GoogleCloudAiplatformV1FeatureViewBigQuerySource $bigQuerySource)
  {
    $this->bigQuerySource = $bigQuerySource;
  }
  /**
   * @return GoogleCloudAiplatformV1FeatureViewBigQuerySource
   */
  public function getBigQuerySource()
  {
    return $this->bigQuerySource;
  }
  /**
   * Output only. Metadata containing information about the Cloud Bigtable.
   *
   * @param GoogleCloudAiplatformV1FeatureViewBigtableMetadata $bigtableMetadata
   */
  public function setBigtableMetadata(GoogleCloudAiplatformV1FeatureViewBigtableMetadata $bigtableMetadata)
  {
    $this->bigtableMetadata = $bigtableMetadata;
  }
  /**
   * @return GoogleCloudAiplatformV1FeatureViewBigtableMetadata
   */
  public function getBigtableMetadata()
  {
    return $this->bigtableMetadata;
  }
  /**
   * Output only. Timestamp when this FeatureView was created.
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
   * Optional. Used to perform consistent read-modify-write updates. If not set,
   * a blind "overwrite" update happens.
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
   * Optional. Configures the features from a Feature Registry source that need
   * to be loaded onto the FeatureOnlineStore.
   *
   * @param GoogleCloudAiplatformV1FeatureViewFeatureRegistrySource $featureRegistrySource
   */
  public function setFeatureRegistrySource(GoogleCloudAiplatformV1FeatureViewFeatureRegistrySource $featureRegistrySource)
  {
    $this->featureRegistrySource = $featureRegistrySource;
  }
  /**
   * @return GoogleCloudAiplatformV1FeatureViewFeatureRegistrySource
   */
  public function getFeatureRegistrySource()
  {
    return $this->featureRegistrySource;
  }
  /**
   * Optional. Configuration for index preparation for vector search. It
   * contains the required configurations to create an index from source data,
   * so that approximate nearest neighbor (a.k.a ANN) algorithms search can be
   * performed during online serving.
   *
   * @param GoogleCloudAiplatformV1FeatureViewIndexConfig $indexConfig
   */
  public function setIndexConfig(GoogleCloudAiplatformV1FeatureViewIndexConfig $indexConfig)
  {
    $this->indexConfig = $indexConfig;
  }
  /**
   * @return GoogleCloudAiplatformV1FeatureViewIndexConfig
   */
  public function getIndexConfig()
  {
    return $this->indexConfig;
  }
  /**
   * Optional. The labels with user-defined metadata to organize your
   * FeatureViews. Label keys and values can be no longer than 64 characters
   * (Unicode codepoints), can only contain lowercase letters, numeric
   * characters, underscores and dashes. International characters are allowed.
   * See https://goo.gl/xmQnxf for more information on and examples of labels.
   * No more than 64 user labels can be associated with one
   * FeatureOnlineStore(System labels are excluded)." System reserved label keys
   * are prefixed with "aiplatform.googleapis.com/" and are immutable.
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
   * Identifier. Name of the FeatureView. Format: `projects/{project}/locations/
   * {location}/featureOnlineStores/{feature_online_store}/featureViews/{feature
   * _view}`
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
   * Optional. Configuration for FeatureView created under Optimized
   * FeatureOnlineStore.
   *
   * @param GoogleCloudAiplatformV1FeatureViewOptimizedConfig $optimizedConfig
   */
  public function setOptimizedConfig(GoogleCloudAiplatformV1FeatureViewOptimizedConfig $optimizedConfig)
  {
    $this->optimizedConfig = $optimizedConfig;
  }
  /**
   * @return GoogleCloudAiplatformV1FeatureViewOptimizedConfig
   */
  public function getOptimizedConfig()
  {
    return $this->optimizedConfig;
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
   * Output only. A Service Account unique to this FeatureView. The role
   * bigquery.dataViewer should be granted to this service account to allow
   * Vertex AI Feature Store to sync data to the online store.
   *
   * @param string $serviceAccountEmail
   */
  public function setServiceAccountEmail($serviceAccountEmail)
  {
    $this->serviceAccountEmail = $serviceAccountEmail;
  }
  /**
   * @return string
   */
  public function getServiceAccountEmail()
  {
    return $this->serviceAccountEmail;
  }
  /**
   * Optional. Service agent type used during data sync. By default, the Vertex
   * AI Service Agent is used. When using an IAM Policy to isolate this
   * FeatureView within a project, a separate service account should be
   * provisioned by setting this field to `SERVICE_AGENT_TYPE_FEATURE_VIEW`.
   * This will generate a separate service account to access the BigQuery source
   * table.
   *
   * Accepted values: SERVICE_AGENT_TYPE_UNSPECIFIED,
   * SERVICE_AGENT_TYPE_PROJECT, SERVICE_AGENT_TYPE_FEATURE_VIEW
   *
   * @param self::SERVICE_AGENT_TYPE_* $serviceAgentType
   */
  public function setServiceAgentType($serviceAgentType)
  {
    $this->serviceAgentType = $serviceAgentType;
  }
  /**
   * @return self::SERVICE_AGENT_TYPE_*
   */
  public function getServiceAgentType()
  {
    return $this->serviceAgentType;
  }
  /**
   * Configures when data is to be synced/updated for this FeatureView. At the
   * end of the sync the latest featureValues for each entityId of this
   * FeatureView are made ready for online serving.
   *
   * @param GoogleCloudAiplatformV1FeatureViewSyncConfig $syncConfig
   */
  public function setSyncConfig(GoogleCloudAiplatformV1FeatureViewSyncConfig $syncConfig)
  {
    $this->syncConfig = $syncConfig;
  }
  /**
   * @return GoogleCloudAiplatformV1FeatureViewSyncConfig
   */
  public function getSyncConfig()
  {
    return $this->syncConfig;
  }
  /**
   * Output only. Timestamp when this FeatureView was last updated.
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
   * Optional. The Vertex RAG Source that the FeatureView is linked to.
   *
   * @param GoogleCloudAiplatformV1FeatureViewVertexRagSource $vertexRagSource
   */
  public function setVertexRagSource(GoogleCloudAiplatformV1FeatureViewVertexRagSource $vertexRagSource)
  {
    $this->vertexRagSource = $vertexRagSource;
  }
  /**
   * @return GoogleCloudAiplatformV1FeatureViewVertexRagSource
   */
  public function getVertexRagSource()
  {
    return $this->vertexRagSource;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1FeatureView::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1FeatureView');
