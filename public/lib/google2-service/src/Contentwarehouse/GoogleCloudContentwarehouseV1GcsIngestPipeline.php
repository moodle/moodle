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

namespace Google\Service\Contentwarehouse;

class GoogleCloudContentwarehouseV1GcsIngestPipeline extends \Google\Model
{
  /**
   * The input Cloud Storage folder. All files under this folder will be
   * imported to Document Warehouse. Format: `gs:`.
   *
   * @var string
   */
  public $inputPath;
  protected $pipelineConfigType = GoogleCloudContentwarehouseV1IngestPipelineConfig::class;
  protected $pipelineConfigDataType = '';
  /**
   * The Doc AI processor type name. Only used when the format of ingested files
   * is Doc AI Document proto format.
   *
   * @var string
   */
  public $processorType;
  /**
   * The Document Warehouse schema resource name. All documents processed by
   * this pipeline will use this schema. Format: projects/{project_number}/locat
   * ions/{location}/documentSchemas/{document_schema_id}.
   *
   * @var string
   */
  public $schemaName;
  /**
   * The flag whether to skip ingested documents. If it is set to true,
   * documents in Cloud Storage contains key "status" with value
   * "status=ingested" in custom metadata will be skipped to ingest.
   *
   * @var bool
   */
  public $skipIngestedDocuments;

  /**
   * The input Cloud Storage folder. All files under this folder will be
   * imported to Document Warehouse. Format: `gs:`.
   *
   * @param string $inputPath
   */
  public function setInputPath($inputPath)
  {
    $this->inputPath = $inputPath;
  }
  /**
   * @return string
   */
  public function getInputPath()
  {
    return $this->inputPath;
  }
  /**
   * Optional. The config for the Cloud Storage Ingestion pipeline. It provides
   * additional customization options to run the pipeline and can be skipped if
   * it is not applicable.
   *
   * @param GoogleCloudContentwarehouseV1IngestPipelineConfig $pipelineConfig
   */
  public function setPipelineConfig(GoogleCloudContentwarehouseV1IngestPipelineConfig $pipelineConfig)
  {
    $this->pipelineConfig = $pipelineConfig;
  }
  /**
   * @return GoogleCloudContentwarehouseV1IngestPipelineConfig
   */
  public function getPipelineConfig()
  {
    return $this->pipelineConfig;
  }
  /**
   * The Doc AI processor type name. Only used when the format of ingested files
   * is Doc AI Document proto format.
   *
   * @param string $processorType
   */
  public function setProcessorType($processorType)
  {
    $this->processorType = $processorType;
  }
  /**
   * @return string
   */
  public function getProcessorType()
  {
    return $this->processorType;
  }
  /**
   * The Document Warehouse schema resource name. All documents processed by
   * this pipeline will use this schema. Format: projects/{project_number}/locat
   * ions/{location}/documentSchemas/{document_schema_id}.
   *
   * @param string $schemaName
   */
  public function setSchemaName($schemaName)
  {
    $this->schemaName = $schemaName;
  }
  /**
   * @return string
   */
  public function getSchemaName()
  {
    return $this->schemaName;
  }
  /**
   * The flag whether to skip ingested documents. If it is set to true,
   * documents in Cloud Storage contains key "status" with value
   * "status=ingested" in custom metadata will be skipped to ingest.
   *
   * @param bool $skipIngestedDocuments
   */
  public function setSkipIngestedDocuments($skipIngestedDocuments)
  {
    $this->skipIngestedDocuments = $skipIngestedDocuments;
  }
  /**
   * @return bool
   */
  public function getSkipIngestedDocuments()
  {
    return $this->skipIngestedDocuments;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContentwarehouseV1GcsIngestPipeline::class, 'Google_Service_Contentwarehouse_GoogleCloudContentwarehouseV1GcsIngestPipeline');
