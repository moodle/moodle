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

class GoogleCloudDiscoveryengineV1GenerateGroundedContentRequestGenerationSpec extends \Google\Model
{
  /**
   * @var float
   */
  public $frequencyPenalty;
  /**
   * @var string
   */
  public $languageCode;
  /**
   * @var int
   */
  public $maxOutputTokens;
  /**
   * @var string
   */
  public $modelId;
  /**
   * @var float
   */
  public $presencePenalty;
  /**
   * @var int
   */
  public $seed;
  /**
   * @var float
   */
  public $temperature;
  /**
   * @var int
   */
  public $topK;
  /**
   * @var float
   */
  public $topP;

  /**
   * @param float
   */
  public function setFrequencyPenalty($frequencyPenalty)
  {
    $this->frequencyPenalty = $frequencyPenalty;
  }
  /**
   * @return float
   */
  public function getFrequencyPenalty()
  {
    return $this->frequencyPenalty;
  }
  /**
   * @param string
   */
  public function setLanguageCode($languageCode)
  {
    $this->languageCode = $languageCode;
  }
  /**
   * @return string
   */
  public function getLanguageCode()
  {
    return $this->languageCode;
  }
  /**
   * @param int
   */
  public function setMaxOutputTokens($maxOutputTokens)
  {
    $this->maxOutputTokens = $maxOutputTokens;
  }
  /**
   * @return int
   */
  public function getMaxOutputTokens()
  {
    return $this->maxOutputTokens;
  }
  /**
   * @param string
   */
  public function setModelId($modelId)
  {
    $this->modelId = $modelId;
  }
  /**
   * @return string
   */
  public function getModelId()
  {
    return $this->modelId;
  }
  /**
   * @param float
   */
  public function setPresencePenalty($presencePenalty)
  {
    $this->presencePenalty = $presencePenalty;
  }
  /**
   * @return float
   */
  public function getPresencePenalty()
  {
    return $this->presencePenalty;
  }
  /**
   * @param int
   */
  public function setSeed($seed)
  {
    $this->seed = $seed;
  }
  /**
   * @return int
   */
  public function getSeed()
  {
    return $this->seed;
  }
  /**
   * @param float
   */
  public function setTemperature($temperature)
  {
    $this->temperature = $temperature;
  }
  /**
   * @return float
   */
  public function getTemperature()
  {
    return $this->temperature;
  }
  /**
   * @param int
   */
  public function setTopK($topK)
  {
    $this->topK = $topK;
  }
  /**
   * @return int
   */
  public function getTopK()
  {
    return $this->topK;
  }
  /**
   * @param float
   */
  public function setTopP($topP)
  {
    $this->topP = $topP;
  }
  /**
   * @return float
   */
  public function getTopP()
  {
    return $this->topP;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1GenerateGroundedContentRequestGenerationSpec::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1GenerateGroundedContentRequestGenerationSpec');
