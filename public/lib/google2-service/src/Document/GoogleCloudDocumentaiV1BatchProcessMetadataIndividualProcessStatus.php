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

class GoogleCloudDocumentaiV1BatchProcessMetadataIndividualProcessStatus extends \Google\Model
{
  protected $humanReviewStatusType = GoogleCloudDocumentaiV1HumanReviewStatus::class;
  protected $humanReviewStatusDataType = '';
  /**
   * The source of the document, same as the input_gcs_source field in the
   * request when the batch process started.
   *
   * @var string
   */
  public $inputGcsSource;
  /**
   * The Cloud Storage output destination (in the request as
   * DocumentOutputConfig.GcsOutputConfig.gcs_uri) of the processed document if
   * it was successful, otherwise empty.
   *
   * @var string
   */
  public $outputGcsDestination;
  protected $statusType = GoogleRpcStatus::class;
  protected $statusDataType = '';

  /**
   * The status of human review on the processed document.
   *
   * @param GoogleCloudDocumentaiV1HumanReviewStatus $humanReviewStatus
   */
  public function setHumanReviewStatus(GoogleCloudDocumentaiV1HumanReviewStatus $humanReviewStatus)
  {
    $this->humanReviewStatus = $humanReviewStatus;
  }
  /**
   * @return GoogleCloudDocumentaiV1HumanReviewStatus
   */
  public function getHumanReviewStatus()
  {
    return $this->humanReviewStatus;
  }
  /**
   * The source of the document, same as the input_gcs_source field in the
   * request when the batch process started.
   *
   * @param string $inputGcsSource
   */
  public function setInputGcsSource($inputGcsSource)
  {
    $this->inputGcsSource = $inputGcsSource;
  }
  /**
   * @return string
   */
  public function getInputGcsSource()
  {
    return $this->inputGcsSource;
  }
  /**
   * The Cloud Storage output destination (in the request as
   * DocumentOutputConfig.GcsOutputConfig.gcs_uri) of the processed document if
   * it was successful, otherwise empty.
   *
   * @param string $outputGcsDestination
   */
  public function setOutputGcsDestination($outputGcsDestination)
  {
    $this->outputGcsDestination = $outputGcsDestination;
  }
  /**
   * @return string
   */
  public function getOutputGcsDestination()
  {
    return $this->outputGcsDestination;
  }
  /**
   * The status processing the document.
   *
   * @param GoogleRpcStatus $status
   */
  public function setStatus(GoogleRpcStatus $status)
  {
    $this->status = $status;
  }
  /**
   * @return GoogleRpcStatus
   */
  public function getStatus()
  {
    return $this->status;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDocumentaiV1BatchProcessMetadataIndividualProcessStatus::class, 'Google_Service_Document_GoogleCloudDocumentaiV1BatchProcessMetadataIndividualProcessStatus');
