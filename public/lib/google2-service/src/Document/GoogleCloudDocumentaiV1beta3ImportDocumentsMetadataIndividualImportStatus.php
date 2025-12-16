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

class GoogleCloudDocumentaiV1beta3ImportDocumentsMetadataIndividualImportStatus extends \Google\Model
{
  /**
   * The source Cloud Storage URI of the document.
   *
   * @var string
   */
  public $inputGcsSource;
  protected $outputDocumentIdType = GoogleCloudDocumentaiV1beta3DocumentId::class;
  protected $outputDocumentIdDataType = '';
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
   * @param GoogleCloudDocumentaiV1beta3DocumentId $outputDocumentId
   */
  public function setOutputDocumentId(GoogleCloudDocumentaiV1beta3DocumentId $outputDocumentId)
  {
    $this->outputDocumentId = $outputDocumentId;
  }
  /**
   * @return GoogleCloudDocumentaiV1beta3DocumentId
   */
  public function getOutputDocumentId()
  {
    return $this->outputDocumentId;
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
class_alias(GoogleCloudDocumentaiV1beta3ImportDocumentsMetadataIndividualImportStatus::class, 'Google_Service_Document_GoogleCloudDocumentaiV1beta3ImportDocumentsMetadataIndividualImportStatus');
