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

class GoogleCloudDocumentaiV1EvaluationCounters extends \Google\Model
{
  /**
   * How many documents were used in the evaluation.
   *
   * @var int
   */
  public $evaluatedDocumentsCount;
  /**
   * How many documents were not included in the evaluation as Document AI
   * failed to process them.
   *
   * @var int
   */
  public $failedDocumentsCount;
  /**
   * How many documents were sent for evaluation.
   *
   * @var int
   */
  public $inputDocumentsCount;
  /**
   * How many documents were not included in the evaluation as they didn't pass
   * validation.
   *
   * @var int
   */
  public $invalidDocumentsCount;

  /**
   * How many documents were used in the evaluation.
   *
   * @param int $evaluatedDocumentsCount
   */
  public function setEvaluatedDocumentsCount($evaluatedDocumentsCount)
  {
    $this->evaluatedDocumentsCount = $evaluatedDocumentsCount;
  }
  /**
   * @return int
   */
  public function getEvaluatedDocumentsCount()
  {
    return $this->evaluatedDocumentsCount;
  }
  /**
   * How many documents were not included in the evaluation as Document AI
   * failed to process them.
   *
   * @param int $failedDocumentsCount
   */
  public function setFailedDocumentsCount($failedDocumentsCount)
  {
    $this->failedDocumentsCount = $failedDocumentsCount;
  }
  /**
   * @return int
   */
  public function getFailedDocumentsCount()
  {
    return $this->failedDocumentsCount;
  }
  /**
   * How many documents were sent for evaluation.
   *
   * @param int $inputDocumentsCount
   */
  public function setInputDocumentsCount($inputDocumentsCount)
  {
    $this->inputDocumentsCount = $inputDocumentsCount;
  }
  /**
   * @return int
   */
  public function getInputDocumentsCount()
  {
    return $this->inputDocumentsCount;
  }
  /**
   * How many documents were not included in the evaluation as they didn't pass
   * validation.
   *
   * @param int $invalidDocumentsCount
   */
  public function setInvalidDocumentsCount($invalidDocumentsCount)
  {
    $this->invalidDocumentsCount = $invalidDocumentsCount;
  }
  /**
   * @return int
   */
  public function getInvalidDocumentsCount()
  {
    return $this->invalidDocumentsCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDocumentaiV1EvaluationCounters::class, 'Google_Service_Document_GoogleCloudDocumentaiV1EvaluationCounters');
