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

class GoogleCloudAiplatformV1PairwiseSummarizationQualityInstance extends \Google\Model
{
  /**
   * Required. Output of the baseline model.
   *
   * @var string
   */
  public $baselinePrediction;
  /**
   * Required. Text to be summarized.
   *
   * @var string
   */
  public $context;
  /**
   * Required. Summarization prompt for LLM.
   *
   * @var string
   */
  public $instruction;
  /**
   * Required. Output of the candidate model.
   *
   * @var string
   */
  public $prediction;
  /**
   * Optional. Ground truth used to compare against the prediction.
   *
   * @var string
   */
  public $reference;

  /**
   * Required. Output of the baseline model.
   *
   * @param string $baselinePrediction
   */
  public function setBaselinePrediction($baselinePrediction)
  {
    $this->baselinePrediction = $baselinePrediction;
  }
  /**
   * @return string
   */
  public function getBaselinePrediction()
  {
    return $this->baselinePrediction;
  }
  /**
   * Required. Text to be summarized.
   *
   * @param string $context
   */
  public function setContext($context)
  {
    $this->context = $context;
  }
  /**
   * @return string
   */
  public function getContext()
  {
    return $this->context;
  }
  /**
   * Required. Summarization prompt for LLM.
   *
   * @param string $instruction
   */
  public function setInstruction($instruction)
  {
    $this->instruction = $instruction;
  }
  /**
   * @return string
   */
  public function getInstruction()
  {
    return $this->instruction;
  }
  /**
   * Required. Output of the candidate model.
   *
   * @param string $prediction
   */
  public function setPrediction($prediction)
  {
    $this->prediction = $prediction;
  }
  /**
   * @return string
   */
  public function getPrediction()
  {
    return $this->prediction;
  }
  /**
   * Optional. Ground truth used to compare against the prediction.
   *
   * @param string $reference
   */
  public function setReference($reference)
  {
    $this->reference = $reference;
  }
  /**
   * @return string
   */
  public function getReference()
  {
    return $this->reference;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1PairwiseSummarizationQualityInstance::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1PairwiseSummarizationQualityInstance');
