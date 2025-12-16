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

class GoogleCloudAiplatformV1SchemaPromptSpecStructuredPrompt extends \Google\Collection
{
  protected $collection_key = 'predictionInputs';
  protected $appBuilderDataType = GoogleCloudAiplatformV1SchemaPromptSpecAppBuilderData::class;
  protected $appBuilderDataDataType = '';
  protected $contextType = GoogleCloudAiplatformV1Content::class;
  protected $contextDataType = '';
  protected $examplesType = GoogleCloudAiplatformV1SchemaPromptSpecPartList::class;
  protected $examplesDataType = 'array';
  /**
   * Preamble: For infill prompt, the prefix before expected model response.
   *
   * @var string
   */
  public $infillPrefix;
  /**
   * Preamble: For infill prompt, the suffix after expected model response.
   *
   * @var string
   */
  public $infillSuffix;
  /**
   * Preamble: The input prefixes before each example input.
   *
   * @var string[]
   */
  public $inputPrefixes;
  /**
   * Preamble: The output prefixes before each example output.
   *
   * @var string[]
   */
  public $outputPrefixes;
  protected $predictionInputsType = GoogleCloudAiplatformV1SchemaPromptSpecPartList::class;
  protected $predictionInputsDataType = 'array';
  protected $promptMessageType = GoogleCloudAiplatformV1SchemaPromptSpecPromptMessage::class;
  protected $promptMessageDataType = '';

  /**
   * Data for app builder use case.
   *
   * @param GoogleCloudAiplatformV1SchemaPromptSpecAppBuilderData $appBuilderData
   */
  public function setAppBuilderData(GoogleCloudAiplatformV1SchemaPromptSpecAppBuilderData $appBuilderData)
  {
    $this->appBuilderData = $appBuilderData;
  }
  /**
   * @return GoogleCloudAiplatformV1SchemaPromptSpecAppBuilderData
   */
  public function getAppBuilderData()
  {
    return $this->appBuilderData;
  }
  /**
   * Preamble: The context of the prompt.
   *
   * @param GoogleCloudAiplatformV1Content $context
   */
  public function setContext(GoogleCloudAiplatformV1Content $context)
  {
    $this->context = $context;
  }
  /**
   * @return GoogleCloudAiplatformV1Content
   */
  public function getContext()
  {
    return $this->context;
  }
  /**
   * Preamble: A set of examples for expected model response.
   *
   * @param GoogleCloudAiplatformV1SchemaPromptSpecPartList[] $examples
   */
  public function setExamples($examples)
  {
    $this->examples = $examples;
  }
  /**
   * @return GoogleCloudAiplatformV1SchemaPromptSpecPartList[]
   */
  public function getExamples()
  {
    return $this->examples;
  }
  /**
   * Preamble: For infill prompt, the prefix before expected model response.
   *
   * @param string $infillPrefix
   */
  public function setInfillPrefix($infillPrefix)
  {
    $this->infillPrefix = $infillPrefix;
  }
  /**
   * @return string
   */
  public function getInfillPrefix()
  {
    return $this->infillPrefix;
  }
  /**
   * Preamble: For infill prompt, the suffix after expected model response.
   *
   * @param string $infillSuffix
   */
  public function setInfillSuffix($infillSuffix)
  {
    $this->infillSuffix = $infillSuffix;
  }
  /**
   * @return string
   */
  public function getInfillSuffix()
  {
    return $this->infillSuffix;
  }
  /**
   * Preamble: The input prefixes before each example input.
   *
   * @param string[] $inputPrefixes
   */
  public function setInputPrefixes($inputPrefixes)
  {
    $this->inputPrefixes = $inputPrefixes;
  }
  /**
   * @return string[]
   */
  public function getInputPrefixes()
  {
    return $this->inputPrefixes;
  }
  /**
   * Preamble: The output prefixes before each example output.
   *
   * @param string[] $outputPrefixes
   */
  public function setOutputPrefixes($outputPrefixes)
  {
    $this->outputPrefixes = $outputPrefixes;
  }
  /**
   * @return string[]
   */
  public function getOutputPrefixes()
  {
    return $this->outputPrefixes;
  }
  /**
   * Preamble: The input test data for prediction. Each PartList in this field
   * represents one text-only input set for a single model request.
   *
   * @param GoogleCloudAiplatformV1SchemaPromptSpecPartList[] $predictionInputs
   */
  public function setPredictionInputs($predictionInputs)
  {
    $this->predictionInputs = $predictionInputs;
  }
  /**
   * @return GoogleCloudAiplatformV1SchemaPromptSpecPartList[]
   */
  public function getPredictionInputs()
  {
    return $this->predictionInputs;
  }
  /**
   * The prompt message.
   *
   * @param GoogleCloudAiplatformV1SchemaPromptSpecPromptMessage $promptMessage
   */
  public function setPromptMessage(GoogleCloudAiplatformV1SchemaPromptSpecPromptMessage $promptMessage)
  {
    $this->promptMessage = $promptMessage;
  }
  /**
   * @return GoogleCloudAiplatformV1SchemaPromptSpecPromptMessage
   */
  public function getPromptMessage()
  {
    return $this->promptMessage;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1SchemaPromptSpecStructuredPrompt::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1SchemaPromptSpecStructuredPrompt');
