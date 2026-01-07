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

class GoogleCloudContentwarehouseV1GcsIngestWithDocAiProcessorsPipeline extends \Google\Collection
{
  protected $collection_key = 'extractProcessorInfos';
  protected $extractProcessorInfosType = GoogleCloudContentwarehouseV1ProcessorInfo::class;
  protected $extractProcessorInfosDataType = 'array';
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
   * The Cloud Storage folder path used to store the raw results from
   * processors. Format: `gs:`.
   *
   * @var string
   */
  public $processorResultsFolderPath;
  /**
   * The flag whether to skip ingested documents. If it is set to true,
   * documents in Cloud Storage contains key "status" with value
   * "status=ingested" in custom metadata will be skipped to ingest.
   *
   * @var bool
   */
  public $skipIngestedDocuments;
  protected $splitClassifyProcessorInfoType = GoogleCloudContentwarehouseV1ProcessorInfo::class;
  protected $splitClassifyProcessorInfoDataType = '';

  /**
   * The extract processors information. One matched extract processor will be
   * used to process documents based on the classify processor result. If no
   * classify processor is specified, the first extract processor will be used.
   *
   * @param GoogleCloudContentwarehouseV1ProcessorInfo[] $extractProcessorInfos
   */
  public function setExtractProcessorInfos($extractProcessorInfos)
  {
    $this->extractProcessorInfos = $extractProcessorInfos;
  }
  /**
   * @return GoogleCloudContentwarehouseV1ProcessorInfo[]
   */
  public function getExtractProcessorInfos()
  {
    return $this->extractProcessorInfos;
  }
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
   * Optional. The config for the Cloud Storage Ingestion with DocAI Processors
   * pipeline. It provides additional customization options to run the pipeline
   * and can be skipped if it is not applicable.
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
   * The Cloud Storage folder path used to store the raw results from
   * processors. Format: `gs:`.
   *
   * @param string $processorResultsFolderPath
   */
  public function setProcessorResultsFolderPath($processorResultsFolderPath)
  {
    $this->processorResultsFolderPath = $processorResultsFolderPath;
  }
  /**
   * @return string
   */
  public function getProcessorResultsFolderPath()
  {
    return $this->processorResultsFolderPath;
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
  /**
   * The split and classify processor information. The split and classify result
   * will be used to find a matched extract processor.
   *
   * @param GoogleCloudContentwarehouseV1ProcessorInfo $splitClassifyProcessorInfo
   */
  public function setSplitClassifyProcessorInfo(GoogleCloudContentwarehouseV1ProcessorInfo $splitClassifyProcessorInfo)
  {
    $this->splitClassifyProcessorInfo = $splitClassifyProcessorInfo;
  }
  /**
   * @return GoogleCloudContentwarehouseV1ProcessorInfo
   */
  public function getSplitClassifyProcessorInfo()
  {
    return $this->splitClassifyProcessorInfo;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContentwarehouseV1GcsIngestWithDocAiProcessorsPipeline::class, 'Google_Service_Contentwarehouse_GoogleCloudContentwarehouseV1GcsIngestWithDocAiProcessorsPipeline');
