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

class GoogleCloudDocumentaiUiv1beta3ImportDocumentsMetadataIndividualImportStatus extends \Google\Model
{
  /**
   * The source Cloud Storage URI of the document.
   *
   * @var string
   */
  public $inputGcsSource;
  protected $outputDocumentIdType = GoogleCloudDocumentaiUiv1beta3DocumentId::class;
  protected $outputDocumentIdDataType = '';
  /**
   * The output_gcs_destination of the processed document if it was successful,
   * otherwise empty.
   *
   * @var string
   */
  public $outputGcsDestination;
  protected $statusType = GoogleRpcStatus::class;
  protected $statusDataType = '';

  /**
   * The source Cloud Storage URI of the document.
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
   * The document id of imported document if it was successful, otherwise empty.
   *
   * @param GoogleCloudDocumentaiUiv1beta3DocumentId $outputDocumentId
   */
  public function setOutputDocumentId(GoogleCloudDocumentaiUiv1beta3DocumentId $outputDocumentId)
  {
    $this->outputDocumentId = $outputDocumentId;
  }
  /**
   * @return GoogleCloudDocumentaiUiv1beta3DocumentId
   */
  public function getOutputDocumentId()
  {
    return $this->outputDocumentId;
  }
  /**
   * The output_gcs_destination of the processed document if it was successful,
   * otherwise empty.
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
   * The status of the importing of the document.
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
class_alias(GoogleCloudDocumentaiUiv1beta3ImportDocumentsMetadataIndividualImportStatus::class, 'Google_Service_Document_GoogleCloudDocumentaiUiv1beta3ImportDocumentsMetadataIndividualImportStatus');
