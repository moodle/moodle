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

class GoogleCloudAiplatformV1ImportRagFilesConfig extends \Google\Model
{
  protected $gcsSourceType = GoogleCloudAiplatformV1GcsSource::class;
  protected $gcsSourceDataType = '';
  protected $googleDriveSourceType = GoogleCloudAiplatformV1GoogleDriveSource::class;
  protected $googleDriveSourceDataType = '';
  protected $importResultBigquerySinkType = GoogleCloudAiplatformV1BigQueryDestination::class;
  protected $importResultBigquerySinkDataType = '';
  protected $importResultGcsSinkType = GoogleCloudAiplatformV1GcsDestination::class;
  protected $importResultGcsSinkDataType = '';
  protected $jiraSourceType = GoogleCloudAiplatformV1JiraSource::class;
  protected $jiraSourceDataType = '';
  /**
   * Optional. The max number of queries per minute that this job is allowed to
   * make to the embedding model specified on the corpus. This value is specific
   * to this job and not shared across other import jobs. Consult the Quotas
   * page on the project to set an appropriate value here. If unspecified, a
   * default value of 1,000 QPM would be used.
   *
   * @var int
   */
  public $maxEmbeddingRequestsPerMin;
  protected $partialFailureBigquerySinkType = GoogleCloudAiplatformV1BigQueryDestination::class;
  protected $partialFailureBigquerySinkDataType = '';
  protected $partialFailureGcsSinkType = GoogleCloudAiplatformV1GcsDestination::class;
  protected $partialFailureGcsSinkDataType = '';
  protected $ragFileParsingConfigType = GoogleCloudAiplatformV1RagFileParsingConfig::class;
  protected $ragFileParsingConfigDataType = '';
  protected $ragFileTransformationConfigType = GoogleCloudAiplatformV1RagFileTransformationConfig::class;
  protected $ragFileTransformationConfigDataType = '';
  /**
   * Rebuilds the ANN index to optimize for recall on the imported data. Only
   * applicable for RagCorpora running on RagManagedDb with `retrieval_strategy`
   * set to `ANN`. The rebuild will be performed using the existing ANN config
   * set on the RagCorpus. To change the ANN config, please use the
   * UpdateRagCorpus API. Default is false, i.e., index is not rebuilt.
   *
   * @var bool
   */
  public $rebuildAnnIndex;
  protected $sharePointSourcesType = GoogleCloudAiplatformV1SharePointSources::class;
  protected $sharePointSourcesDataType = '';
  protected $slackSourceType = GoogleCloudAiplatformV1SlackSource::class;
  protected $slackSourceDataType = '';

  /**
   * Google Cloud Storage location. Supports importing individual files as well
   * as entire Google Cloud Storage directories. Sample formats: -
   * `gs://bucket_name/my_directory/object_name/my_file.txt` -
   * `gs://bucket_name/my_directory`
   *
   * @param GoogleCloudAiplatformV1GcsSource $gcsSource
   */
  public function setGcsSource(GoogleCloudAiplatformV1GcsSource $gcsSource)
  {
    $this->gcsSource = $gcsSource;
  }
  /**
   * @return GoogleCloudAiplatformV1GcsSource
   */
  public function getGcsSource()
  {
    return $this->gcsSource;
  }
  /**
   * Google Drive location. Supports importing individual files as well as
   * Google Drive folders.
   *
   * @param GoogleCloudAiplatformV1GoogleDriveSource $googleDriveSource
   */
  public function setGoogleDriveSource(GoogleCloudAiplatformV1GoogleDriveSource $googleDriveSource)
  {
    $this->googleDriveSource = $googleDriveSource;
  }
  /**
   * @return GoogleCloudAiplatformV1GoogleDriveSource
   */
  public function getGoogleDriveSource()
  {
    return $this->googleDriveSource;
  }
  /**
   * The BigQuery destination to write import result to. It should be a bigquery
   * table resource name (e.g. "bq://projectId.bqDatasetId.bqTableId"). The
   * dataset must exist. If the table does not exist, it will be created with
   * the expected schema. If the table exists, the schema will be validated and
   * data will be added to this existing table.
   *
   * @param GoogleCloudAiplatformV1BigQueryDestination $importResultBigquerySink
   */
  public function setImportResultBigquerySink(GoogleCloudAiplatformV1BigQueryDestination $importResultBigquerySink)
  {
    $this->importResultBigquerySink = $importResultBigquerySink;
  }
  /**
   * @return GoogleCloudAiplatformV1BigQueryDestination
   */
  public function getImportResultBigquerySink()
  {
    return $this->importResultBigquerySink;
  }
  /**
   * The Cloud Storage path to write import result to.
   *
   * @param GoogleCloudAiplatformV1GcsDestination $importResultGcsSink
   */
  public function setImportResultGcsSink(GoogleCloudAiplatformV1GcsDestination $importResultGcsSink)
  {
    $this->importResultGcsSink = $importResultGcsSink;
  }
  /**
   * @return GoogleCloudAiplatformV1GcsDestination
   */
  public function getImportResultGcsSink()
  {
    return $this->importResultGcsSink;
  }
  /**
   * Jira queries with their corresponding authentication.
   *
   * @param GoogleCloudAiplatformV1JiraSource $jiraSource
   */
  public function setJiraSource(GoogleCloudAiplatformV1JiraSource $jiraSource)
  {
    $this->jiraSource = $jiraSource;
  }
  /**
   * @return GoogleCloudAiplatformV1JiraSource
   */
  public function getJiraSource()
  {
    return $this->jiraSource;
  }
  /**
   * Optional. The max number of queries per minute that this job is allowed to
   * make to the embedding model specified on the corpus. This value is specific
   * to this job and not shared across other import jobs. Consult the Quotas
   * page on the project to set an appropriate value here. If unspecified, a
   * default value of 1,000 QPM would be used.
   *
   * @param int $maxEmbeddingRequestsPerMin
   */
  public function setMaxEmbeddingRequestsPerMin($maxEmbeddingRequestsPerMin)
  {
    $this->maxEmbeddingRequestsPerMin = $maxEmbeddingRequestsPerMin;
  }
  /**
   * @return int
   */
  public function getMaxEmbeddingRequestsPerMin()
  {
    return $this->maxEmbeddingRequestsPerMin;
  }
  /**
   * The BigQuery destination to write partial failures to. It should be a
   * bigquery table resource name (e.g. "bq://projectId.bqDatasetId.bqTableId").
   * The dataset must exist. If the table does not exist, it will be created
   * with the expected schema. If the table exists, the schema will be validated
   * and data will be added to this existing table. Deprecated. Prefer to use
   * `import_result_bq_sink`.
   *
   * @deprecated
   * @param GoogleCloudAiplatformV1BigQueryDestination $partialFailureBigquerySink
   */
  public function setPartialFailureBigquerySink(GoogleCloudAiplatformV1BigQueryDestination $partialFailureBigquerySink)
  {
    $this->partialFailureBigquerySink = $partialFailureBigquerySink;
  }
  /**
   * @deprecated
   * @return GoogleCloudAiplatformV1BigQueryDestination
   */
  public function getPartialFailureBigquerySink()
  {
    return $this->partialFailureBigquerySink;
  }
  /**
   * The Cloud Storage path to write partial failures to. Deprecated. Prefer to
   * use `import_result_gcs_sink`.
   *
   * @deprecated
   * @param GoogleCloudAiplatformV1GcsDestination $partialFailureGcsSink
   */
  public function setPartialFailureGcsSink(GoogleCloudAiplatformV1GcsDestination $partialFailureGcsSink)
  {
    $this->partialFailureGcsSink = $partialFailureGcsSink;
  }
  /**
   * @deprecated
   * @return GoogleCloudAiplatformV1GcsDestination
   */
  public function getPartialFailureGcsSink()
  {
    return $this->partialFailureGcsSink;
  }
  /**
   * Optional. Specifies the parsing config for RagFiles. RAG will use the
   * default parser if this field is not set.
   *
   * @param GoogleCloudAiplatformV1RagFileParsingConfig $ragFileParsingConfig
   */
  public function setRagFileParsingConfig(GoogleCloudAiplatformV1RagFileParsingConfig $ragFileParsingConfig)
  {
    $this->ragFileParsingConfig = $ragFileParsingConfig;
  }
  /**
   * @return GoogleCloudAiplatformV1RagFileParsingConfig
   */
  public function getRagFileParsingConfig()
  {
    return $this->ragFileParsingConfig;
  }
  /**
   * Specifies the transformation config for RagFiles.
   *
   * @param GoogleCloudAiplatformV1RagFileTransformationConfig $ragFileTransformationConfig
   */
  public function setRagFileTransformationConfig(GoogleCloudAiplatformV1RagFileTransformationConfig $ragFileTransformationConfig)
  {
    $this->ragFileTransformationConfig = $ragFileTransformationConfig;
  }
  /**
   * @return GoogleCloudAiplatformV1RagFileTransformationConfig
   */
  public function getRagFileTransformationConfig()
  {
    return $this->ragFileTransformationConfig;
  }
  /**
   * Rebuilds the ANN index to optimize for recall on the imported data. Only
   * applicable for RagCorpora running on RagManagedDb with `retrieval_strategy`
   * set to `ANN`. The rebuild will be performed using the existing ANN config
   * set on the RagCorpus. To change the ANN config, please use the
   * UpdateRagCorpus API. Default is false, i.e., index is not rebuilt.
   *
   * @param bool $rebuildAnnIndex
   */
  public function setRebuildAnnIndex($rebuildAnnIndex)
  {
    $this->rebuildAnnIndex = $rebuildAnnIndex;
  }
  /**
   * @return bool
   */
  public function getRebuildAnnIndex()
  {
    return $this->rebuildAnnIndex;
  }
  /**
   * SharePoint sources.
   *
   * @param GoogleCloudAiplatformV1SharePointSources $sharePointSources
   */
  public function setSharePointSources(GoogleCloudAiplatformV1SharePointSources $sharePointSources)
  {
    $this->sharePointSources = $sharePointSources;
  }
  /**
   * @return GoogleCloudAiplatformV1SharePointSources
   */
  public function getSharePointSources()
  {
    return $this->sharePointSources;
  }
  /**
   * Slack channels with their corresponding access tokens.
   *
   * @param GoogleCloudAiplatformV1SlackSource $slackSource
   */
  public function setSlackSource(GoogleCloudAiplatformV1SlackSource $slackSource)
  {
    $this->slackSource = $slackSource;
  }
  /**
   * @return GoogleCloudAiplatformV1SlackSource
   */
  public function getSlackSource()
  {
    return $this->slackSource;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1ImportRagFilesConfig::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1ImportRagFilesConfig');
