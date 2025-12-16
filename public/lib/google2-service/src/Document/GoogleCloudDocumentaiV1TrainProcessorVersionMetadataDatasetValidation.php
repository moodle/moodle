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

namespace Google\Service\Document;

class GoogleCloudDocumentaiV1TrainProcessorVersionMetadataDatasetValidation extends \Google\Collection
{
  protected $collection_key = 'documentErrors';
  /**
   * The total number of dataset errors.
   *
   * @var int
   */
  public $datasetErrorCount;
  protected $datasetErrorsType = GoogleRpcStatus::class;
  protected $datasetErrorsDataType = 'array';
  /**
   * The total number of document errors.
   *
   * @var int
   */
  public $documentErrorCount;
  protected $documentErrorsType = GoogleRpcStatus::class;
  protected $documentErrorsDataType = 'array';

  /**
   * The total number of dataset errors.
   *
   * @param int $datasetErrorCount
   */
  public function setDatasetErrorCount($datasetErrorCount)
  {
    $this->datasetErrorCount = $datasetErrorCount;
  }
  /**
   * @return int
   */
  public function getDatasetErrorCount()
  {
    return $this->datasetErrorCount;
  }
  /**
   * Error information for the dataset as a whole. A maximum of 10 dataset
   * errors will be returned. A single dataset error is terminal for training.
   *
   * @param GoogleRpcStatus[] $datasetErrors
   */
  public function setDatasetErrors($datasetErrors)
  {
    $this->datasetErrors = $datasetErrors;
  }
  /**
   * @return GoogleRpcStatus[]
   */
  public function getDatasetErrors()
  {
    return $this->datasetErrors;
  }
  /**
   * The total number of document errors.
   *
   * @param int $documentErrorCount
   */
  public function setDocumentErrorCount($documentErrorCount)
  {
    $this->documentErrorCount = $documentErrorCount;
  }
  /**
   * @return int
   */
  public function getDocumentErrorCount()
  {
    return $this->documentErrorCount;
  }
  /**
   * Error information pertaining to specific documents. A maximum of 10
   * document errors will be returned. Any document with errors will not be used
   * throughout training.
   *
   * @param GoogleRpcStatus[] $documentErrors
   */
  public function setDocumentErrors($documentErrors)
  {
    $this->documentErrors = $documentErrors;
  }
  /**
   * @return GoogleRpcStatus[]
   */
  public function getDocumentErrors()
  {
    return $this->documentErrors;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDocumentaiV1TrainProcessorVersionMetadataDatasetValidation::class, 'Google_Service_Document_GoogleCloudDocumentaiV1TrainProcessorVersionMetadataDatasetValidation');
