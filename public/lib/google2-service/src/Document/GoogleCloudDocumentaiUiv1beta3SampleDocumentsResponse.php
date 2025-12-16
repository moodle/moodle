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

class GoogleCloudDocumentaiUiv1beta3SampleDocumentsResponse extends \Google\Collection
{
  protected $collection_key = 'selectedDocuments';
  protected $sampleTestStatusType = GoogleRpcStatus::class;
  protected $sampleTestStatusDataType = '';
  protected $sampleTrainingStatusType = GoogleRpcStatus::class;
  protected $sampleTrainingStatusDataType = '';
  protected $selectedDocumentsType = GoogleCloudDocumentaiUiv1beta3SampleDocumentsResponseSelectedDocument::class;
  protected $selectedDocumentsDataType = 'array';

  /**
   * The status of sampling documents in test split.
   *
   * @param GoogleRpcStatus $sampleTestStatus
   */
  public function setSampleTestStatus(GoogleRpcStatus $sampleTestStatus)
  {
    $this->sampleTestStatus = $sampleTestStatus;
  }
  /**
   * @return GoogleRpcStatus
   */
  public function getSampleTestStatus()
  {
    return $this->sampleTestStatus;
  }
  /**
   * The status of sampling documents in training split.
   *
   * @param GoogleRpcStatus $sampleTrainingStatus
   */
  public function setSampleTrainingStatus(GoogleRpcStatus $sampleTrainingStatus)
  {
    $this->sampleTrainingStatus = $sampleTrainingStatus;
  }
  /**
   * @return GoogleRpcStatus
   */
  public function getSampleTrainingStatus()
  {
    return $this->sampleTrainingStatus;
  }
  /**
   * The result of the sampling process.
   *
   * @param GoogleCloudDocumentaiUiv1beta3SampleDocumentsResponseSelectedDocument[] $selectedDocuments
   */
  public function setSelectedDocuments($selectedDocuments)
  {
    $this->selectedDocuments = $selectedDocuments;
  }
  /**
   * @return GoogleCloudDocumentaiUiv1beta3SampleDocumentsResponseSelectedDocument[]
   */
  public function getSelectedDocuments()
  {
    return $this->selectedDocuments;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDocumentaiUiv1beta3SampleDocumentsResponse::class, 'Google_Service_Document_GoogleCloudDocumentaiUiv1beta3SampleDocumentsResponse');
