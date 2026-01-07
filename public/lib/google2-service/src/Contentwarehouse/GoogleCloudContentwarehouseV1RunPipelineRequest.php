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

class GoogleCloudContentwarehouseV1RunPipelineRequest extends \Google\Model
{
  protected $exportCdwPipelineType = GoogleCloudContentwarehouseV1ExportToCdwPipeline::class;
  protected $exportCdwPipelineDataType = '';
  protected $gcsIngestPipelineType = GoogleCloudContentwarehouseV1GcsIngestPipeline::class;
  protected $gcsIngestPipelineDataType = '';
  protected $gcsIngestWithDocAiProcessorsPipelineType = GoogleCloudContentwarehouseV1GcsIngestWithDocAiProcessorsPipeline::class;
  protected $gcsIngestWithDocAiProcessorsPipelineDataType = '';
  protected $processWithDocAiPipelineType = GoogleCloudContentwarehouseV1ProcessWithDocAiPipeline::class;
  protected $processWithDocAiPipelineDataType = '';
  protected $requestMetadataType = GoogleCloudContentwarehouseV1RequestMetadata::class;
  protected $requestMetadataDataType = '';

  /**
   * Export docuemnts from Document Warehouse to CDW for training purpose.
   *
   * @param GoogleCloudContentwarehouseV1ExportToCdwPipeline $exportCdwPipeline
   */
  public function setExportCdwPipeline(GoogleCloudContentwarehouseV1ExportToCdwPipeline $exportCdwPipeline)
  {
    $this->exportCdwPipeline = $exportCdwPipeline;
  }
  /**
   * @return GoogleCloudContentwarehouseV1ExportToCdwPipeline
   */
  public function getExportCdwPipeline()
  {
    return $this->exportCdwPipeline;
  }
  /**
   * Cloud Storage ingestion pipeline.
   *
   * @param GoogleCloudContentwarehouseV1GcsIngestPipeline $gcsIngestPipeline
   */
  public function setGcsIngestPipeline(GoogleCloudContentwarehouseV1GcsIngestPipeline $gcsIngestPipeline)
  {
    $this->gcsIngestPipeline = $gcsIngestPipeline;
  }
  /**
   * @return GoogleCloudContentwarehouseV1GcsIngestPipeline
   */
  public function getGcsIngestPipeline()
  {
    return $this->gcsIngestPipeline;
  }
  /**
   * Use DocAI processors to process documents in Cloud Storage and ingest them
   * to Document Warehouse.
   *
   * @param GoogleCloudContentwarehouseV1GcsIngestWithDocAiProcessorsPipeline $gcsIngestWithDocAiProcessorsPipeline
   */
  public function setGcsIngestWithDocAiProcessorsPipeline(GoogleCloudContentwarehouseV1GcsIngestWithDocAiProcessorsPipeline $gcsIngestWithDocAiProcessorsPipeline)
  {
    $this->gcsIngestWithDocAiProcessorsPipeline = $gcsIngestWithDocAiProcessorsPipeline;
  }
  /**
   * @return GoogleCloudContentwarehouseV1GcsIngestWithDocAiProcessorsPipeline
   */
  public function getGcsIngestWithDocAiProcessorsPipeline()
  {
    return $this->gcsIngestWithDocAiProcessorsPipeline;
  }
  /**
   * Use a DocAI processor to process documents in Document Warehouse, and re-
   * ingest the updated results into Document Warehouse.
   *
   * @param GoogleCloudContentwarehouseV1ProcessWithDocAiPipeline $processWithDocAiPipeline
   */
  public function setProcessWithDocAiPipeline(GoogleCloudContentwarehouseV1ProcessWithDocAiPipeline $processWithDocAiPipeline)
  {
    $this->processWithDocAiPipeline = $processWithDocAiPipeline;
  }
  /**
   * @return GoogleCloudContentwarehouseV1ProcessWithDocAiPipeline
   */
  public function getProcessWithDocAiPipeline()
  {
    return $this->processWithDocAiPipeline;
  }
  /**
   * The meta information collected about the end user, used to enforce access
   * control for the service.
   *
   * @param GoogleCloudContentwarehouseV1RequestMetadata $requestMetadata
   */
  public function setRequestMetadata(GoogleCloudContentwarehouseV1RequestMetadata $requestMetadata)
  {
    $this->requestMetadata = $requestMetadata;
  }
  /**
   * @return GoogleCloudContentwarehouseV1RequestMetadata
   */
  public function getRequestMetadata()
  {
    return $this->requestMetadata;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContentwarehouseV1RunPipelineRequest::class, 'Google_Service_Contentwarehouse_GoogleCloudContentwarehouseV1RunPipelineRequest');
