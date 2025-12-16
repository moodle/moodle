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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1betaTrainCustomModelRequestGcsTrainingInput extends \Google\Model
{
  /**
   * @var string
   */
  public $corpusDataPath;
  /**
   * @var string
   */
  public $queryDataPath;
  /**
   * @var string
   */
  public $testDataPath;
  /**
   * @var string
   */
  public $trainDataPath;

  /**
   * @param string
   */
  public function setCorpusDataPath($corpusDataPath)
  {
    $this->corpusDataPath = $corpusDataPath;
  }
  /**
   * @return string
   */
  public function getCorpusDataPath()
  {
    return $this->corpusDataPath;
  }
  /**
   * @param string
   */
  public function setQueryDataPath($queryDataPath)
  {
    $this->queryDataPath = $queryDataPath;
  }
  /**
   * @return string
   */
  public function getQueryDataPath()
  {
    return $this->queryDataPath;
  }
  /**
   * @param string
   */
  public function setTestDataPath($testDataPath)
  {
    $this->testDataPath = $testDataPath;
  }
  /**
   * @return string
   */
  public function getTestDataPath()
  {
    return $this->testDataPath;
  }
  /**
   * @param string
   */
  public function setTrainDataPath($trainDataPath)
  {
    $this->trainDataPath = $trainDataPath;
  }
  /**
   * @return string
   */
  public function getTrainDataPath()
  {
    return $this->trainDataPath;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1betaTrainCustomModelRequestGcsTrainingInput::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1betaTrainCustomModelRequestGcsTrainingInput');
