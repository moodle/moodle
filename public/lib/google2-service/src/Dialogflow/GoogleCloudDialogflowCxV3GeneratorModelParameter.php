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

namespace Google\Service\Dialogflow;

class GoogleCloudDialogflowCxV3GeneratorModelParameter extends \Google\Model
{
  /**
   * The maximum number of tokens to generate.
   *
   * @var int
   */
  public $maxDecodeSteps;
  /**
   * The temperature used for sampling. Temperature sampling occurs after both
   * topP and topK have been applied. Valid range: [0.0, 1.0] Low temperature =
   * less random. High temperature = more random.
   *
   * @var float
   */
  public $temperature;
  /**
   * If set, the sampling process in each step is limited to the top_k tokens
   * with highest probabilities. Valid range: [1, 40] or 1000+. Small topK =
   * less random. Large topK = more random.
   *
   * @var int
   */
  public $topK;
  /**
   * If set, only the tokens comprising the top top_p probability mass are
   * considered. If both top_p and top_k are set, top_p will be used for further
   * refining candidates selected with top_k. Valid range: (0.0, 1.0]. Small
   * topP = less random. Large topP = more random.
   *
   * @var float
   */
  public $topP;

  /**
   * The maximum number of tokens to generate.
   *
   * @param int $maxDecodeSteps
   */
  public function setMaxDecodeSteps($maxDecodeSteps)
  {
    $this->maxDecodeSteps = $maxDecodeSteps;
  }
  /**
   * @return int
   */
  public function getMaxDecodeSteps()
  {
    return $this->maxDecodeSteps;
  }
  /**
   * The temperature used for sampling. Temperature sampling occurs after both
   * topP and topK have been applied. Valid range: [0.0, 1.0] Low temperature =
   * less random. High temperature = more random.
   *
   * @param float $temperature
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
   * If set, the sampling process in each step is limited to the top_k tokens
   * with highest probabilities. Valid range: [1, 40] or 1000+. Small topK =
   * less random. Large topK = more random.
   *
   * @param int $topK
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
   * If set, only the tokens comprising the top top_p probability mass are
   * considered. If both top_p and top_k are set, top_p will be used for further
   * refining candidates selected with top_k. Valid range: (0.0, 1.0]. Small
   * topP = less random. Large topP = more random.
   *
   * @param float $topP
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
class_alias(GoogleCloudDialogflowCxV3GeneratorModelParameter::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3GeneratorModelParameter');
