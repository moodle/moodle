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

class GoogleCloudAiplatformV1NearestNeighborSearchOperationMetadataContentValidationStats extends \Google\Collection
{
  protected $collection_key = 'partialErrors';
  /**
   * Number of records in this file we skipped due to validate errors.
   *
   * @var string
   */
  public $invalidRecordCount;
  /**
   * Number of sparse records in this file we skipped due to validate errors.
   *
   * @var string
   */
  public $invalidSparseRecordCount;
  protected $partialErrorsType = GoogleCloudAiplatformV1NearestNeighborSearchOperationMetadataRecordError::class;
  protected $partialErrorsDataType = 'array';
  /**
   * Cloud Storage URI pointing to the original file in user's bucket.
   *
   * @var string
   */
  public $sourceGcsUri;
  /**
   * Number of records in this file that were successfully processed.
   *
   * @var string
   */
  public $validRecordCount;
  /**
   * Number of sparse records in this file that were successfully processed.
   *
   * @var string
   */
  public $validSparseRecordCount;

  /**
   * Number of records in this file we skipped due to validate errors.
   *
   * @param string $invalidRecordCount
   */
  public function setInvalidRecordCount($invalidRecordCount)
  {
    $this->invalidRecordCount = $invalidRecordCount;
  }
  /**
   * @return string
   */
  public function getInvalidRecordCount()
  {
    return $this->invalidRecordCount;
  }
  /**
   * Number of sparse records in this file we skipped due to validate errors.
   *
   * @param string $invalidSparseRecordCount
   */
  public function setInvalidSparseRecordCount($invalidSparseRecordCount)
  {
    $this->invalidSparseRecordCount = $invalidSparseRecordCount;
  }
  /**
   * @return string
   */
  public function getInvalidSparseRecordCount()
  {
    return $this->invalidSparseRecordCount;
  }
  /**
   * The detail information of the partial failures encountered for those
   * invalid records that couldn't be parsed. Up to 50 partial errors will be
   * reported.
   *
   * @param GoogleCloudAiplatformV1NearestNeighborSearchOperationMetadataRecordError[] $partialErrors
   */
  public function setPartialErrors($partialErrors)
  {
    $this->partialErrors = $partialErrors;
  }
  /**
   * @return GoogleCloudAiplatformV1NearestNeighborSearchOperationMetadataRecordError[]
   */
  public function getPartialErrors()
  {
    return $this->partialErrors;
  }
  /**
   * Cloud Storage URI pointing to the original file in user's bucket.
   *
   * @param string $sourceGcsUri
   */
  public function setSourceGcsUri($sourceGcsUri)
  {
    $this->sourceGcsUri = $sourceGcsUri;
  }
  /**
   * @return string
   */
  public function getSourceGcsUri()
  {
    return $this->sourceGcsUri;
  }
  /**
   * Number of records in this file that were successfully processed.
   *
   * @param string $validRecordCount
   */
  public function setValidRecordCount($validRecordCount)
  {
    $this->validRecordCount = $validRecordCount;
  }
  /**
   * @return string
   */
  public function getValidRecordCount()
  {
    return $this->validRecordCount;
  }
  /**
   * Number of sparse records in this file that were successfully processed.
   *
   * @param string $validSparseRecordCount
   */
  public function setValidSparseRecordCount($validSparseRecordCount)
  {
    $this->validSparseRecordCount = $validSparseRecordCount;
  }
  /**
   * @return string
   */
  public function getValidSparseRecordCount()
  {
    return $this->validSparseRecordCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1NearestNeighborSearchOperationMetadataContentValidationStats::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1NearestNeighborSearchOperationMetadataContentValidationStats');
