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

class GoogleCloudContentwarehouseV1ExportToCdwPipeline extends \Google\Collection
{
  protected $collection_key = 'documents';
  /**
   * Optional. The CDW dataset resource name. This field is optional. If not
   * set, the documents will be exported to Cloud Storage only. Format:
   * projects/{project}/locations/{location}/processors/{processor}/dataset
   *
   * @var string
   */
  public $docAiDataset;
  /**
   * The list of all the resource names of the documents to be processed.
   * Format:
   * projects/{project_number}/locations/{location}/documents/{document_id}.
   *
   * @var string[]
   */
  public $documents;
  /**
   * The Cloud Storage folder path used to store the exported documents before
   * being sent to CDW. Format: `gs:`.
   *
   * @var string
   */
  public $exportFolderPath;
  /**
   * Ratio of training dataset split. When importing into Document AI Workbench,
   * documents will be automatically split into training and test split category
   * with the specified ratio. This field is required if doc_ai_dataset is set.
   *
   * @var float
   */
  public $trainingSplitRatio;

  /**
   * Optional. The CDW dataset resource name. This field is optional. If not
   * set, the documents will be exported to Cloud Storage only. Format:
   * projects/{project}/locations/{location}/processors/{processor}/dataset
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
   * The list of all the resource names of the documents to be processed.
   * Format:
   * projects/{project_number}/locations/{location}/documents/{document_id}.
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
   * The Cloud Storage folder path used to store the exported documents before
   * being sent to CDW. Format: `gs:`.
   *
   * @param string $exportFolderPath
   */
  public function setExportFolderPath($exportFolderPath)
  {
    $this->exportFolderPath = $exportFolderPath;
  }
  /**
   * @return string
   */
  public function getExportFolderPath()
  {
    return $this->exportFolderPath;
  }
  /**
   * Ratio of training dataset split. When importing into Document AI Workbench,
   * documents will be automatically split into training and test split category
   * with the specified ratio. This field is required if doc_ai_dataset is set.
   *
   * @param float $trainingSplitRatio
   */
  public function setTrainingSplitRatio($trainingSplitRatio)
  {
    $this->trainingSplitRatio = $trainingSplitRatio;
  }
  /**
   * @return float
   */
  public function getTrainingSplitRatio()
  {
    return $this->trainingSplitRatio;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContentwarehouseV1ExportToCdwPipeline::class, 'Google_Service_Contentwarehouse_GoogleCloudContentwarehouseV1ExportToCdwPipeline');
