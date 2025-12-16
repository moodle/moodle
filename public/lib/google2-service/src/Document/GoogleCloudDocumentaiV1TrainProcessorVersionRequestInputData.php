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

class GoogleCloudDocumentaiV1TrainProcessorVersionRequestInputData extends \Google\Model
{
  protected $testDocumentsType = GoogleCloudDocumentaiV1BatchDocumentsInputConfig::class;
  protected $testDocumentsDataType = '';
  protected $trainingDocumentsType = GoogleCloudDocumentaiV1BatchDocumentsInputConfig::class;
  protected $trainingDocumentsDataType = '';

  /**
   * The documents used for testing the trained version.
   *
   * @param GoogleCloudDocumentaiV1BatchDocumentsInputConfig $testDocuments
   */
  public function setTestDocuments(GoogleCloudDocumentaiV1BatchDocumentsInputConfig $testDocuments)
  {
    $this->testDocuments = $testDocuments;
  }
  /**
   * @return GoogleCloudDocumentaiV1BatchDocumentsInputConfig
   */
  public function getTestDocuments()
  {
    return $this->testDocuments;
  }
  /**
   * The documents used for training the new version.
   *
   * @param GoogleCloudDocumentaiV1BatchDocumentsInputConfig $trainingDocuments
   */
  public function setTrainingDocuments(GoogleCloudDocumentaiV1BatchDocumentsInputConfig $trainingDocuments)
  {
    $this->trainingDocuments = $trainingDocuments;
  }
  /**
   * @return GoogleCloudDocumentaiV1BatchDocumentsInputConfig
   */
  public function getTrainingDocuments()
  {
    return $this->trainingDocuments;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDocumentaiV1TrainProcessorVersionRequestInputData::class, 'Google_Service_Document_GoogleCloudDocumentaiV1TrainProcessorVersionRequestInputData');
