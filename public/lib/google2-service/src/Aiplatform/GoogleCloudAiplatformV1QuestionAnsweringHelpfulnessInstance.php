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

class GoogleCloudAiplatformV1QuestionAnsweringHelpfulnessInstance extends \Google\Model
{
  /**
   * Optional. Text provided as context to answer the question.
   *
   * @var string
   */
  public $context;
  /**
   * Required. The question asked and other instruction in the inference prompt.
   *
   * @var string
   */
  public $instruction;
  /**
   * Required. Output of the evaluated model.
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
   * Optional. Text provided as context to answer the question.
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
   * Required. The question asked and other instruction in the inference prompt.
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
   * Required. Output of the evaluated model.
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
class_alias(GoogleCloudAiplatformV1QuestionAnsweringHelpfulnessInstance::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1QuestionAnsweringHelpfulnessInstance');
