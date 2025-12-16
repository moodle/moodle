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

class GoogleCloudAiplatformV1SchemaTextPromptDatasetMetadata extends \Google\Collection
{
  protected $collection_key = 'stopSequences';
  /**
   * Number of candidates.
   *
   * @var string
   */
  public $candidateCount;
  /**
   * The Google Cloud Storage URI that stores the prompt data.
   *
   * @var string
   */
  public $gcsUri;
  protected $groundingConfigType = GoogleCloudAiplatformV1SchemaPredictParamsGroundingConfig::class;
  protected $groundingConfigDataType = '';
  /**
   * Whether the prompt dataset has prompt variable.
   *
   * @var bool
   */
  public $hasPromptVariable;
  /**
   * Whether or not the user has enabled logit probabilities in the model
   * parameters.
   *
   * @var bool
   */
  public $logprobs;
  /**
   * Value of the maximum number of tokens generated set when the dataset was
   * saved.
   *
   * @var string
   */
  public $maxOutputTokens;
  /**
   * User-created prompt note. Note size limit is 2KB.
   *
   * @var string
   */
  public $note;
  protected $promptApiSchemaType = GoogleCloudAiplatformV1SchemaPromptApiSchema::class;
  protected $promptApiSchemaDataType = '';
  /**
   * Type of the prompt dataset.
   *
   * @var string
   */
  public $promptType;
  /**
   * Seeding enables model to return a deterministic response on a best effort
   * basis. Determinism isn't guaranteed. This field determines whether or not
   * seeding is enabled.
   *
   * @var bool
   */
  public $seedEnabled;
  /**
   * The actual value of the seed.
   *
   * @var string
   */
  public $seedValue;
  /**
   * Customized stop sequences.
   *
   * @var string[]
   */
  public $stopSequences;
  /**
   * The content of the prompt dataset system instruction.
   *
   * @var string
   */
  public $systemInstruction;
  /**
   * The Google Cloud Storage URI that stores the system instruction, starting
   * with gs://.
   *
   * @var string
   */
  public $systemInstructionGcsUri;
  /**
   * Temperature value used for sampling set when the dataset was saved. This
   * value is used to tune the degree of randomness.
   *
   * @var float
   */
  public $temperature;
  /**
   * The content of the prompt dataset.
   *
   * @var string
   */
  public $text;
  /**
   * Top K value set when the dataset was saved. This value determines how many
   * candidates with highest probability from the vocab would be selected for
   * each decoding step.
   *
   * @var string
   */
  public $topK;
  /**
   * Top P value set when the dataset was saved. Given topK tokens for decoding,
   * top candidates will be selected until the sum of their probabilities is
   * topP.
   *
   * @var float
   */
  public $topP;

  /**
   * Number of candidates.
   *
   * @param string $candidateCount
   */
  public function setCandidateCount($candidateCount)
  {
    $this->candidateCount = $candidateCount;
  }
  /**
   * @return string
   */
  public function getCandidateCount()
  {
    return $this->candidateCount;
  }
  /**
   * The Google Cloud Storage URI that stores the prompt data.
   *
   * @param string $gcsUri
   */
  public function setGcsUri($gcsUri)
  {
    $this->gcsUri = $gcsUri;
  }
  /**
   * @return string
   */
  public function getGcsUri()
  {
    return $this->gcsUri;
  }
  /**
   * Grounding checking configuration.
   *
   * @param GoogleCloudAiplatformV1SchemaPredictParamsGroundingConfig $groundingConfig
   */
  public function setGroundingConfig(GoogleCloudAiplatformV1SchemaPredictParamsGroundingConfig $groundingConfig)
  {
    $this->groundingConfig = $groundingConfig;
  }
  /**
   * @return GoogleCloudAiplatformV1SchemaPredictParamsGroundingConfig
   */
  public function getGroundingConfig()
  {
    return $this->groundingConfig;
  }
  /**
   * Whether the prompt dataset has prompt variable.
   *
   * @param bool $hasPromptVariable
   */
  public function setHasPromptVariable($hasPromptVariable)
  {
    $this->hasPromptVariable = $hasPromptVariable;
  }
  /**
   * @return bool
   */
  public function getHasPromptVariable()
  {
    return $this->hasPromptVariable;
  }
  /**
   * Whether or not the user has enabled logit probabilities in the model
   * parameters.
   *
   * @param bool $logprobs
   */
  public function setLogprobs($logprobs)
  {
    $this->logprobs = $logprobs;
  }
  /**
   * @return bool
   */
  public function getLogprobs()
  {
    return $this->logprobs;
  }
  /**
   * Value of the maximum number of tokens generated set when the dataset was
   * saved.
   *
   * @param string $maxOutputTokens
   */
  public function setMaxOutputTokens($maxOutputTokens)
  {
    $this->maxOutputTokens = $maxOutputTokens;
  }
  /**
   * @return string
   */
  public function getMaxOutputTokens()
  {
    return $this->maxOutputTokens;
  }
  /**
   * User-created prompt note. Note size limit is 2KB.
   *
   * @param string $note
   */
  public function setNote($note)
  {
    $this->note = $note;
  }
  /**
   * @return string
   */
  public function getNote()
  {
    return $this->note;
  }
  /**
   * The API schema of the prompt to support both UI and SDK usages.
   *
   * @param GoogleCloudAiplatformV1SchemaPromptApiSchema $promptApiSchema
   */
  public function setPromptApiSchema(GoogleCloudAiplatformV1SchemaPromptApiSchema $promptApiSchema)
  {
    $this->promptApiSchema = $promptApiSchema;
  }
  /**
   * @return GoogleCloudAiplatformV1SchemaPromptApiSchema
   */
  public function getPromptApiSchema()
  {
    return $this->promptApiSchema;
  }
  /**
   * Type of the prompt dataset.
   *
   * @param string $promptType
   */
  public function setPromptType($promptType)
  {
    $this->promptType = $promptType;
  }
  /**
   * @return string
   */
  public function getPromptType()
  {
    return $this->promptType;
  }
  /**
   * Seeding enables model to return a deterministic response on a best effort
   * basis. Determinism isn't guaranteed. This field determines whether or not
   * seeding is enabled.
   *
   * @param bool $seedEnabled
   */
  public function setSeedEnabled($seedEnabled)
  {
    $this->seedEnabled = $seedEnabled;
  }
  /**
   * @return bool
   */
  public function getSeedEnabled()
  {
    return $this->seedEnabled;
  }
  /**
   * The actual value of the seed.
   *
   * @param string $seedValue
   */
  public function setSeedValue($seedValue)
  {
    $this->seedValue = $seedValue;
  }
  /**
   * @return string
   */
  public function getSeedValue()
  {
    return $this->seedValue;
  }
  /**
   * Customized stop sequences.
   *
   * @param string[] $stopSequences
   */
  public function setStopSequences($stopSequences)
  {
    $this->stopSequences = $stopSequences;
  }
  /**
   * @return string[]
   */
  public function getStopSequences()
  {
    return $this->stopSequences;
  }
  /**
   * The content of the prompt dataset system instruction.
   *
   * @param string $systemInstruction
   */
  public function setSystemInstruction($systemInstruction)
  {
    $this->systemInstruction = $systemInstruction;
  }
  /**
   * @return string
   */
  public function getSystemInstruction()
  {
    return $this->systemInstruction;
  }
  /**
   * The Google Cloud Storage URI that stores the system instruction, starting
   * with gs://.
   *
   * @param string $systemInstructionGcsUri
   */
  public function setSystemInstructionGcsUri($systemInstructionGcsUri)
  {
    $this->systemInstructionGcsUri = $systemInstructionGcsUri;
  }
  /**
   * @return string
   */
  public function getSystemInstructionGcsUri()
  {
    return $this->systemInstructionGcsUri;
  }
  /**
   * Temperature value used for sampling set when the dataset was saved. This
   * value is used to tune the degree of randomness.
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
   * The content of the prompt dataset.
   *
   * @param string $text
   */
  public function setText($text)
  {
    $this->text = $text;
  }
  /**
   * @return string
   */
  public function getText()
  {
    return $this->text;
  }
  /**
   * Top K value set when the dataset was saved. This value determines how many
   * candidates with highest probability from the vocab would be selected for
   * each decoding step.
   *
   * @param string $topK
   */
  public function setTopK($topK)
  {
    $this->topK = $topK;
  }
  /**
   * @return string
   */
  public function getTopK()
  {
    return $this->topK;
  }
  /**
   * Top P value set when the dataset was saved. Given topK tokens for decoding,
   * top candidates will be selected until the sum of their probabilities is
   * topP.
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
class_alias(GoogleCloudAiplatformV1SchemaTextPromptDatasetMetadata::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1SchemaTextPromptDatasetMetadata');
