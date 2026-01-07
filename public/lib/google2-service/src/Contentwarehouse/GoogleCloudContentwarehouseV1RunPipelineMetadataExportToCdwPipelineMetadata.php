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

class GoogleCloudContentwarehouseV1RunPipelineMetadataExportToCdwPipelineMetadata extends \Google\Collection
{
  protected $collection_key = 'documents';
  /**
   * The output CDW dataset resource name.
   *
   * @var string
   */
  public $docAiDataset;
  /**
   * The input list of all the resource names of the documents to be exported.
   *
   * @var string[]
   */
  public $documents;
  /**
   * The output Cloud Storage folder in this pipeline.
   *
   * @var string
   */
  public $outputPath;

  /**
   * The output CDW dataset resource name.
   *
   * @param string $docAiDataset
   */
  public function setDocAiDataset($docAiDataset)
  {
    $this->docAiDataset = $docAiDataset;
  }
  /**
   * @return string
   */
  public function getDocAiDataset()
  {
    return $this->docAiDataset;
  }
  /**
   * The input list of all the resource names of the documents to be exported.
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
   * The output Cloud Storage folder in this pipeline.
   *
   * @param string $outputPath
   */
  public function setOutputPath($outputPath)
  {
    $this->outputPath = $outputPath;
  }
  /**
   * @return string
   */
  public function getOutputPath()
  {
    return $this->outputPath;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContentwarehouseV1RunPipelineMetadataExportToCdwPipelineMetadata::class, 'Google_Service_Contentwarehouse_GoogleCloudContentwarehouseV1RunPipelineMetadataExportToCdwPipelineMetadata');
