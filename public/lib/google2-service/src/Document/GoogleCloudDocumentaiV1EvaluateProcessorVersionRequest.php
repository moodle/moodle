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

class GoogleCloudDocumentaiV1EvaluateProcessorVersionRequest extends \Google\Model
{
  protected $evaluationDocumentsType = GoogleCloudDocumentaiV1BatchDocumentsInputConfig::class;
  protected $evaluationDocumentsDataType = '';

  /**
   * Optional. The documents used in the evaluation. If unspecified, use the
   * processor's dataset as evaluation input.
   *
   * @param GoogleCloudDocumentaiV1BatchDocumentsInputConfig $evaluationDocuments
   */
  public function setEvaluationDocuments(GoogleCloudDocumentaiV1BatchDocumentsInputConfig $evaluationDocuments)
  {
    $this->evaluationDocuments = $evaluationDocuments;
  }
  /**
   * @return GoogleCloudDocumentaiV1BatchDocumentsInputConfig
   */
  public function getEvaluationDocuments()
  {
    return $this->evaluationDocuments;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDocumentaiV1EvaluateProcessorVersionRequest::class, 'Google_Service_Document_GoogleCloudDocumentaiV1EvaluateProcessorVersionRequest');
