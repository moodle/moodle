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

class GoogleCloudDocumentaiV1TrainProcessorVersionRequestFoundationModelTuningOptions extends \Google\Model
{
  /**
   * Optional. The multiplier to apply to the recommended learning rate. Valid
   * values are between 0.1 and 10. If not provided, recommended learning rate
   * will be used.
   *
   * @var float
   */
  public $learningRateMultiplier;
  /**
   * Optional. The number of steps to run for model tuning. Valid values are
   * between 1 and 400. If not provided, recommended steps will be used.
   *
   * @var int
   */
  public $trainSteps;

  /**
   * Optional. The multiplier to apply to the recommended learning rate. Valid
   * values are between 0.1 and 10. If not provided, recommended learning rate
   * will be used.
   *
   * @param float $learningRateMultiplier
   */
  public function setLearningRateMultiplier($learningRateMultiplier)
  {
    $this->learningRateMultiplier = $learningRateMultiplier;
  }
  /**
   * @return float
   */
  public function getLearningRateMultiplier()
  {
    return $this->learningRateMultiplier;
  }
  /**
   * Optional. The number of steps to run for model tuning. Valid values are
   * between 1 and 400. If not provided, recommended steps will be used.
   *
   * @param int $trainSteps
   */
  public function setTrainSteps($trainSteps)
  {
    $this->trainSteps = $trainSteps;
  }
  /**
   * @return int
   */
  public function getTrainSteps()
  {
    return $this->trainSteps;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDocumentaiV1TrainProcessorVersionRequestFoundationModelTuningOptions::class, 'Google_Service_Document_GoogleCloudDocumentaiV1TrainProcessorVersionRequestFoundationModelTuningOptions');
