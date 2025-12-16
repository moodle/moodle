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

class GoogleCloudContentwarehouseV1RunPipelineMetadataProcessWithDocAiPipelineMetadata extends \Google\Collection
{
  protected $collection_key = 'documents';
  /**
   * The input list of all the resource names of the documents to be processed.
   *
   * @var string[]
   */
  public $documents;
  protected $processorInfoType = GoogleCloudContentwarehouseV1ProcessorInfo::class;
  protected $processorInfoDataType = '';

  /**
   * The input list of all the resource names of the documents to be processed.
   *
   * @param string[] $documents
   */
  public function setDocuments($documents)
  {
    $this->documents = $documents;
  }
  /**
   * @return string[]
   */
  public function getDocuments()
  {
    return $this->documents;
  }
  /**
   * The DocAI processor to process the documents with.
   *
   * @param GoogleCloudContentwarehouseV1ProcessorInfo $processorInfo
   */
  public function setProcessorInfo(GoogleCloudContentwarehouseV1ProcessorInfo $processorInfo)
  {
    $this->processorInfo = $processorInfo;
  }
  /**
   * @return GoogleCloudContentwarehouseV1ProcessorInfo
   */
  public function getProcessorInfo()
  {
    return $this->processorInfo;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContentwarehouseV1RunPipelineMetadataProcessWithDocAiPipelineMetadata::class, 'Google_Service_Contentwarehouse_GoogleCloudContentwarehouseV1RunPipelineMetadataProcessWithDocAiPipelineMetadata');
