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

class GoogleCloudAiplatformV1ExportFractionSplit extends \Google\Model
{
  /**
   * The fraction of the input data that is to be used to evaluate the Model.
   *
   * @var 
   */
  public $testFraction;
  /**
   * The fraction of the input data that is to be used to train the Model.
   *
   * @var 
   */
  public $trainingFraction;
  /**
   * The fraction of the input data that is to be used to validate the Model.
   *
   * @var 
   */
  public $validationFraction;

  public function setTestFraction($testFraction)
  {
    $this->testFraction = $testFraction;
  }
  public function getTestFraction()
  {
    return $this->testFraction;
  }
  public function setTrainingFraction($trainingFraction)
  {
    $this->trainingFraction = $trainingFraction;
  }
  public function getTrainingFraction()
  {
    return $this->trainingFraction;
  }
  public function setValidationFraction($validationFraction)
  {
    $this->validationFraction = $validationFraction;
  }
  public function getValidationFraction()
  {
    return $this->validationFraction;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1ExportFractionSplit::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1ExportFractionSplit');
