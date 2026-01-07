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

class GoogleCloudAiplatformV1ModelDataStats extends \Google\Model
{
  /**
   * Number of Annotations that are used for evaluating this Model. If the Model
   * is evaluated multiple times, this will be the number of test Annotations
   * used by the first evaluation. If the Model is not evaluated, the number is
   * 0.
   *
   * @var string
   */
  public $testAnnotationsCount;
  /**
   * Number of DataItems that were used for evaluating this Model. If the Model
   * is evaluated multiple times, this will be the number of test DataItems used
   * by the first evaluation. If the Model is not evaluated, the number is 0.
   *
   * @var string
   */
  public $testDataItemsCount;
  /**
   * Number of Annotations that are used for training this Model.
   *
   * @var string
   */
  public $trainingAnnotationsCount;
  /**
   * Number of DataItems that were used for training this Model.
   *
   * @var string
   */
  public $trainingDataItemsCount;
  /**
   * Number of Annotations that are used for validating this Model during
   * training.
   *
   * @var string
   */
  public $validationAnnotationsCount;
  /**
   * Number of DataItems that were used for validating this Model during
   * training.
   *
   * @var string
   */
  public $validationDataItemsCount;

  /**
   * Number of Annotations that are used for evaluating this Model. If the Model
   * is evaluated multiple times, this will be the number of test Annotations
   * used by the first evaluation. If the Model is not evaluated, the number is
   * 0.
   *
   * @param string $testAnnotationsCount
   */
  public function setTestAnnotationsCount($testAnnotationsCount)
  {
    $this->testAnnotationsCount = $testAnnotationsCount;
  }
  /**
   * @return string
   */
  public function getTestAnnotationsCount()
  {
    return $this->testAnnotationsCount;
  }
  /**
   * Number of DataItems that were used for evaluating this Model. If the Model
   * is evaluated multiple times, this will be the number of test DataItems used
   * by the first evaluation. If the Model is not evaluated, the number is 0.
   *
   * @param string $testDataItemsCount
   */
  public function setTestDataItemsCount($testDataItemsCount)
  {
    $this->testDataItemsCount = $testDataItemsCount;
  }
  /**
   * @return string
   */
  public function getTestDataItemsCount()
  {
    return $this->testDataItemsCount;
  }
  /**
   * Number of Annotations that are used for training this Model.
   *
   * @param string $trainingAnnotationsCount
   */
  public function setTrainingAnnotationsCount($trainingAnnotationsCount)
  {
    $this->trainingAnnotationsCount = $trainingAnnotationsCount;
  }
  /**
   * @return string
   */
  public function getTrainingAnnotationsCount()
  {
    return $this->trainingAnnotationsCount;
  }
  /**
   * Number of DataItems that were used for training this Model.
   *
   * @param string $trainingDataItemsCount
   */
  public function setTrainingDataItemsCount($trainingDataItemsCount)
  {
    $this->trainingDataItemsCount = $trainingDataItemsCount;
  }
  /**
   * @return string
   */
  public function getTrainingDataItemsCount()
  {
    return $this->trainingDataItemsCount;
  }
  /**
   * Number of Annotations that are used for validating this Model during
   * training.
   *
   * @param string $validationAnnotationsCount
   */
  public function setValidationAnnotationsCount($validationAnnotationsCount)
  {
    $this->validationAnnotationsCount = $validationAnnotationsCount;
  }
  /**
   * @return string
   */
  public function getValidationAnnotationsCount()
  {
    return $this->validationAnnotationsCount;
  }
  /**
   * Number of DataItems that were used for validating this Model during
   * training.
   *
   * @param string $validationDataItemsCount
   */
  public function setValidationDataItemsCount($validationDataItemsCount)
  {
    $this->validationDataItemsCount = $validationDataItemsCount;
  }
  /**
   * @return string
   */
  public function getValidationDataItemsCount()
  {
    return $this->validationDataItemsCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1ModelDataStats::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1ModelDataStats');
