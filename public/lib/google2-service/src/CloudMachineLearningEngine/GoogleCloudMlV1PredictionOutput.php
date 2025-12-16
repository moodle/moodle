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

namespace Google\Service\CloudMachineLearningEngine;

class GoogleCloudMlV1PredictionOutput extends \Google\Model
{
  /**
   * The number of data instances which resulted in errors.
   *
   * @var string
   */
  public $errorCount;
  /**
   * Node hours used by the batch prediction job.
   *
   * @var 
   */
  public $nodeHours;
  /**
   * The output Google Cloud Storage location provided at the job creation time.
   *
   * @var string
   */
  public $outputPath;
  /**
   * The number of generated predictions.
   *
   * @var string
   */
  public $predictionCount;

  /**
   * The number of data instances which resulted in errors.
   *
   * @param string $errorCount
   */
  public function setErrorCount($errorCount)
  {
    $this->errorCount = $errorCount;
  }
  /**
   * @return string
   */
  public function getErrorCount()
  {
    return $this->errorCount;
  }
  public function setNodeHours($nodeHours)
  {
    $this->nodeHours = $nodeHours;
  }
  public function getNodeHours()
  {
    return $this->nodeHours;
  }
  /**
   * The output Google Cloud Storage location provided at the job creation time.
   *
   * @param string $outputPath
   */
  public function setOutputPath($outputPath)
  {
    $this->outputPath = $outputPath;
  }
  /**
   * @return string
   */
  public function getOutputPath()
  {
    return $this->outputPath;
  }
  /**
   * The number of generated predictions.
   *
   * @param string $predictionCount
   */
  public function setPredictionCount($predictionCount)
  {
    $this->predictionCount = $predictionCount;
  }
  /**
   * @return string
   */
  public function getPredictionCount()
  {
    return $this->predictionCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudMlV1PredictionOutput::class, 'Google_Service_CloudMachineLearningEngine_GoogleCloudMlV1PredictionOutput');
