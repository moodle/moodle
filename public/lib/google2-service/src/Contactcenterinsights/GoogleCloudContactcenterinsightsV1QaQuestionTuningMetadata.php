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

namespace Google\Service\Contactcenterinsights;

class GoogleCloudContactcenterinsightsV1QaQuestionTuningMetadata extends \Google\Collection
{
  protected $collection_key = 'datasetValidationWarnings';
  /**
   * A list of any applicable data validation warnings about the question's
   * feedback labels.
   *
   * @var string[]
   */
  public $datasetValidationWarnings;
  /**
   * Total number of valid labels provided for the question at the time of
   * tuining.
   *
   * @var string
   */
  public $totalValidLabelCount;
  /**
   * Error status of the tuning operation for the question. Will only be set if
   * the tuning operation failed.
   *
   * @var string
   */
  public $tuningError;

  /**
   * A list of any applicable data validation warnings about the question's
   * feedback labels.
   *
   * @param string[] $datasetValidationWarnings
   */
  public function setDatasetValidationWarnings($datasetValidationWarnings)
  {
    $this->datasetValidationWarnings = $datasetValidationWarnings;
  }
  /**
   * @return string[]
   */
  public function getDatasetValidationWarnings()
  {
    return $this->datasetValidationWarnings;
  }
  /**
   * Total number of valid labels provided for the question at the time of
   * tuining.
   *
   * @param string $totalValidLabelCount
   */
  public function setTotalValidLabelCount($totalValidLabelCount)
  {
    $this->totalValidLabelCount = $totalValidLabelCount;
  }
  /**
   * @return string
   */
  public function getTotalValidLabelCount()
  {
    return $this->totalValidLabelCount;
  }
  /**
   * Error status of the tuning operation for the question. Will only be set if
   * the tuning operation failed.
   *
   * @param string $tuningError
   */
  public function setTuningError($tuningError)
  {
    $this->tuningError = $tuningError;
  }
  /**
   * @return string
   */
  public function getTuningError()
  {
    return $this->tuningError;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContactcenterinsightsV1QaQuestionTuningMetadata::class, 'Google_Service_Contactcenterinsights_GoogleCloudContactcenterinsightsV1QaQuestionTuningMetadata');
