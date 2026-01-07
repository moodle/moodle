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

class GoogleCloudContentwarehouseV1RunPipelineMetadata extends \Google\Collection
{
  protected $collection_key = 'individualDocumentStatuses';
  protected $exportToCdwPipelineMetadataType = GoogleCloudContentwarehouseV1RunPipelineMetadataExportToCdwPipelineMetadata::class;
  protected $exportToCdwPipelineMetadataDataType = '';
  /**
   * Number of files that have failed at some point in the pipeline.
   *
   * @var int
   */
  public $failedFileCount;
  protected $gcsIngestPipelineMetadataType = GoogleCloudContentwarehouseV1RunPipelineMetadataGcsIngestPipelineMetadata::class;
  protected $gcsIngestPipelineMetadataDataType = '';
  protected $individualDocumentStatusesType = GoogleCloudContentwarehouseV1RunPipelineMetadataIndividualDocumentStatus::class;
  protected $individualDocumentStatusesDataType = 'array';
  protected $processWithDocAiPipelineMetadataType = GoogleCloudContentwarehouseV1RunPipelineMetadataProcessWithDocAiPipelineMetadata::class;
  protected $processWithDocAiPipelineMetadataDataType = '';
  /**
   * Number of files that were processed by the pipeline.
   *
   * @var int
   */
  public $totalFileCount;
  protected $userInfoType = GoogleCloudContentwarehouseV1UserInfo::class;
  protected $userInfoDataType = '';

  /**
   * The pipeline metadata for Export-to-CDW pipeline.
   *
   * @param GoogleCloudContentwarehouseV1RunPipelineMetadataExportToCdwPipelineMetadata $exportToCdwPipelineMetadata
   */
  public function setExportToCdwPipelineMetadata(GoogleCloudContentwarehouseV1RunPipelineMetadataExportToCdwPipelineMetadata $exportToCdwPipelineMetadata)
  {
    $this->exportToCdwPipelineMetadata = $exportToCdwPipelineMetadata;
  }
  /**
   * @return GoogleCloudContentwarehouseV1RunPipelineMetadataExportToCdwPipelineMetadata
   */
  public function getExportToCdwPipelineMetadata()
  {
    return $this->exportToCdwPipelineMetadata;
  }
  /**
   * Number of files that have failed at some point in the pipeline.
   *
   * @param int $failedFileCount
   */
  public function setFailedFileCount($failedFileCount)
  {
    $this->failedFileCount = $failedFileCount;
  }
  /**
   * @return int
   */
  public function getFailedFileCount()
  {
    return $this->failedFileCount;
  }
  /**
   * The pipeline metadata for GcsIngest pipeline.
   *
   * @param GoogleCloudContentwarehouseV1RunPipelineMetadataGcsIngestPipelineMetadata $gcsIngestPipelineMetadata
   */
  public function setGcsIngestPipelineMetadata(GoogleCloudContentwarehouseV1RunPipelineMetadataGcsIngestPipelineMetadata $gcsIngestPipelineMetadata)
  {
    $this->gcsIngestPipelineMetadata = $gcsIngestPipelineMetadata;
  }
  /**
   * @return GoogleCloudContentwarehouseV1RunPipelineMetadataGcsIngestPipelineMetadata
   */
  public function getGcsIngestPipelineMetadata()
  {
    return $this->gcsIngestPipelineMetadata;
  }
  /**
   * The list of response details of each document.
   *
   * @param GoogleCloudContentwarehouseV1RunPipelineMetadataIndividualDocumentStatus[] $individualDocumentStatuses
   */
  public function setIndividualDocumentStatuses($individualDocumentStatuses)
  {
    $this->individualDocumentStatuses = $individualDocumentStatuses;
  }
  /**
   * @return GoogleCloudContentwarehouseV1RunPipelineMetadataIndividualDocumentStatus[]
   */
  public function getIndividualDocumentStatuses()
  {
    return $this->individualDocumentStatuses;
  }
  /**
   * The pipeline metadata for Process-with-DocAi pipeline.
   *
   * @param GoogleCloudContentwarehouseV1RunPipelineMetadataProcessWithDocAiPipelineMetadata $processWithDocAiPipelineMetadata
   */
  public function setProcessWithDocAiPipelineMetadata(GoogleCloudContentwarehouseV1RunPipelineMetadataProcessWithDocAiPipelineMetadata $processWithDocAiPipelineMetadata)
  {
    $this->processWithDocAiPipelineMetadata = $processWithDocAiPipelineMetadata;
  }
  /**
   * @return GoogleCloudContentwarehouseV1RunPipelineMetadataProcessWithDocAiPipelineMetadata
   */
  public function getProcessWithDocAiPipelineMetadata()
  {
    return $this->processWithDocAiPipelineMetadata;
  }
  /**
   * Number of files that were processed by the pipeline.
   *
   * @param int $totalFileCount
   */
  public function setTotalFileCount($totalFileCount)
  {
    $this->totalFileCount = $totalFileCount;
  }
  /**
   * @return int
   */
  public function getTotalFileCount()
  {
    return $this->totalFileCount;
  }
  /**
   * User unique identification and groups information.
   *
   * @param GoogleCloudContentwarehouseV1UserInfo $userInfo
   */
  public function setUserInfo(GoogleCloudContentwarehouseV1UserInfo $userInfo)
  {
    $this->userInfo = $userInfo;
  }
  /**
   * @return GoogleCloudContentwarehouseV1UserInfo
   */
  public function getUserInfo()
  {
    return $this->userInfo;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContentwarehouseV1RunPipelineMetadata::class, 'Google_Service_Contentwarehouse_GoogleCloudContentwarehouseV1RunPipelineMetadata');
