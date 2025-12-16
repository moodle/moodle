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

class GoogleCloudAiplatformV1GenerationConfig extends \Google\Collection
{
  /**
   * Media resolution has not been set.
   */
  public const MEDIA_RESOLUTION_MEDIA_RESOLUTION_UNSPECIFIED = 'MEDIA_RESOLUTION_UNSPECIFIED';
  /**
   * Media resolution set to low (64 tokens).
   */
  public const MEDIA_RESOLUTION_MEDIA_RESOLUTION_LOW = 'MEDIA_RESOLUTION_LOW';
  /**
   * Media resolution set to medium (256 tokens).
   */
  public const MEDIA_RESOLUTION_MEDIA_RESOLUTION_MEDIUM = 'MEDIA_RESOLUTION_MEDIUM';
  /**
   * Media resolution set to high (zoomed reframing with 256 tokens).
   */
  public const MEDIA_RESOLUTION_MEDIA_RESOLUTION_HIGH = 'MEDIA_RESOLUTION_HIGH';
  protected $collection_key = 'stopSequences';
  /**
   * Optional. If enabled, audio timestamps will be included in the request to
   * the model. This can be useful for synchronizing audio with other modalities
   * in the response.
   *
   * @var bool
   */
  public $audioTimestamp;
  /**
   * Optional. The number of candidate responses to generate. A higher
   * `candidate_count` can provide more options to choose from, but it also
   * consumes more resources. This can be useful for generating a variety of
   * responses and selecting the best one.
   *
   * @var int
   */
  public $candidateCount;
  /**
   * Optional. If enabled, the model will detect emotions and adapt its
   * responses accordingly. For example, if the model detects that the user is
   * frustrated, it may provide a more empathetic response.
   *
   * @var bool
   */
  public $enableAffectiveDialog;
  /**
   * Optional. Penalizes tokens based on their frequency in the generated text.
   * A positive value helps to reduce the repetition of words and phrases. Valid
   * values can range from [-2.0, 2.0].
   *
   * @var float
   */
  public $frequencyPenalty;
  protected $imageConfigType = GoogleCloudAiplatformV1ImageConfig::class;
  protected $imageConfigDataType = '';
  /**
   * Optional. The number of top log probabilities to return for each token.
   * This can be used to see which other tokens were considered likely
   * candidates for a given position. A higher value will return more options,
   * but it will also increase the size of the response.
   *
   * @var int
   */
  public $logprobs;
  /**
   * Optional. The maximum number of tokens to generate in the response. A token
   * is approximately four characters. The default value varies by model. This
   * parameter can be used to control the length of the generated text and
   * prevent overly long responses.
   *
   * @var int
   */
  public $maxOutputTokens;
  /**
   * Optional. The token resolution at which input media content is sampled.
   * This is used to control the trade-off between the quality of the response
   * and the number of tokens used to represent the media. A higher resolution
   * allows the model to perceive more detail, which can lead to a more nuanced
   * response, but it will also use more tokens. This does not affect the image
   * dimensions sent to the model.
   *
   * @var string
   */
  public $mediaResolution;
  /**
   * Optional. Penalizes tokens that have already appeared in the generated
   * text. A positive value encourages the model to generate more diverse and
   * less repetitive text. Valid values can range from [-2.0, 2.0].
   *
   * @var float
   */
  public $presencePenalty;
  /**
   * Optional. When this field is set, response_schema must be omitted and
   * response_mime_type must be set to `application/json`.
   *
   * @var array
   */
  public $responseJsonSchema;
  /**
   * Optional. If set to true, the log probabilities of the output tokens are
   * returned. Log probabilities are the logarithm of the probability of a token
   * appearing in the output. A higher log probability means the token is more
   * likely to be generated. This can be useful for analyzing the model's
   * confidence in its own output and for debugging.
   *
   * @var bool
   */
  public $responseLogprobs;
  /**
   * Optional. The IANA standard MIME type of the response. The model will
   * generate output that conforms to this MIME type. Supported values include
   * 'text/plain' (default) and 'application/json'. The model needs to be
   * prompted to output the appropriate response type, otherwise the behavior is
   * undefined. This is a preview feature.
   *
   * @var string
   */
  public $responseMimeType;
  /**
   * Optional. The modalities of the response. The model will generate a
   * response that includes all the specified modalities. For example, if this
   * is set to `[TEXT, IMAGE]`, the response will include both text and an
   * image.
   *
   * @var string[]
   */
  public $responseModalities;
  protected $responseSchemaType = GoogleCloudAiplatformV1Schema::class;
  protected $responseSchemaDataType = '';
  protected $routingConfigType = GoogleCloudAiplatformV1GenerationConfigRoutingConfig::class;
  protected $routingConfigDataType = '';
  /**
   * Optional. A seed for the random number generator. By setting a seed, you
   * can make the model's output mostly deterministic. For a given prompt and
   * parameters (like temperature, top_p, etc.), the model will produce the same
   * response every time. However, it's not a guaranteed absolute deterministic
   * behavior. This is different from parameters like `temperature`, which
   * control the *level* of randomness. `seed` ensures that the "random" choices
   * the model makes are the same on every run, making it essential for testing
   * and ensuring reproducible results.
   *
   * @var int
   */
  public $seed;
  protected $speechConfigType = GoogleCloudAiplatformV1SpeechConfig::class;
  protected $speechConfigDataType = '';
  /**
   * Optional. A list of character sequences that will stop the model from
   * generating further tokens. If a stop sequence is generated, the output will
   * end at that point. This is useful for controlling the length and structure
   * of the output. For example, you can use ["\n", "###"] to stop generation at
   * a new line or a specific marker.
   *
   * @var string[]
   */
  public $stopSequences;
  /**
   * Optional. Controls the randomness of the output. A higher temperature
   * results in more creative and diverse responses, while a lower temperature
   * makes the output more predictable and focused. The valid range is (0.0,
   * 2.0].
   *
   * @var float
   */
  public $temperature;
  protected $thinkingConfigType = GoogleCloudAiplatformV1GenerationConfigThinkingConfig::class;
  protected $thinkingConfigDataType = '';
  /**
   * Optional. Specifies the top-k sampling threshold. The model considers only
   * the top k most probable tokens for the next token. This can be useful for
   * generating more coherent and less random text. For example, a `top_k` of 40
   * means the model will choose the next word from the 40 most likely words.
   *
   * @var float
   */
  public $topK;
  /**
   * Optional. Specifies the nucleus sampling threshold. The model considers
   * only the smallest set of tokens whose cumulative probability is at least
   * `top_p`. This helps generate more diverse and less repetitive responses.
   * For example, a `top_p` of 0.9 means the model considers tokens until the
   * cumulative probability of the tokens to select from reaches 0.9. It's
   * recommended to adjust either temperature or `top_p`, but not both.
   *
   * @var float
   */
  public $topP;

  /**
   * Optional. If enabled, audio timestamps will be included in the request to
   * the model. This can be useful for synchronizing audio with other modalities
   * in the response.
   *
   * @param bool $audioTimestamp
   */
  public function setAudioTimestamp($audioTimestamp)
  {
    $this->audioTimestamp = $audioTimestamp;
  }
  /**
   * @return bool
   */
  public function getAudioTimestamp()
  {
    return $this->audioTimestamp;
  }
  /**
   * Optional. The number of candidate responses to generate. A higher
   * `candidate_count` can provide more options to choose from, but it also
   * consumes more resources. This can be useful for generating a variety of
   * responses and selecting the best one.
   *
   * @param int $candidateCount
   */
  public function setCandidateCount($candidateCount)
  {
    $this->candidateCount = $candidateCount;
  }
  /**
   * @return int
   */
  public function getCandidateCount()
  {
    return $this->candidateCount;
  }
  /**
   * Optional. If enabled, the model will detect emotions and adapt its
   * responses accordingly. For example, if the model detects that the user is
   * frustrated, it may provide a more empathetic response.
   *
   * @param bool $enableAffectiveDialog
   */
  public function setEnableAffectiveDialog($enableAffectiveDialog)
  {
    $this->enableAffectiveDialog = $enableAffectiveDialog;
  }
  /**
   * @return bool
   */
  public function getEnableAffectiveDialog()
  {
    return $this->enableAffectiveDialog;
  }
  /**
   * Optional. Penalizes tokens based on their frequency in the generated text.
   * A positive value helps to reduce the repetition of words and phrases. Valid
   * values can range from [-2.0, 2.0].
   *
   * @param float $frequencyPenalty
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
   * Optional. Config for image generation features.
   *
   * @param GoogleCloudAiplatformV1ImageConfig $imageConfig
   */
  public function setImageConfig(GoogleCloudAiplatformV1ImageConfig $imageConfig)
  {
    $this->imageConfig = $imageConfig;
  }
  /**
   * @return GoogleCloudAiplatformV1ImageConfig
   */
  public function getImageConfig()
  {
    return $this->imageConfig;
  }
  /**
   * Optional. The number of top log probabilities to return for each token.
   * This can be used to see which other tokens were considered likely
   * candidates for a given position. A higher value will return more options,
   * but it will also increase the size of the response.
   *
   * @param int $logprobs
   */
  public function setLogprobs($logprobs)
  {
    $this->logprobs = $logprobs;
  }
  /**
   * @return int
   */
  public function getLogprobs()
  {
    return $this->logprobs;
  }
  /**
   * Optional. The maximum number of tokens to generate in the response. A token
   * is approximately four characters. The default value varies by model. This
   * parameter can be used to control the length of the generated text and
   * prevent overly long responses.
   *
   * @param int $maxOutputTokens
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
   * Optional. The token resolution at which input media content is sampled.
   * This is used to control the trade-off between the quality of the response
   * and the number of tokens used to represent the media. A higher resolution
   * allows the model to perceive more detail, which can lead to a more nuanced
   * response, but it will also use more tokens. This does not affect the image
   * dimensions sent to the model.
   *
   * Accepted values: MEDIA_RESOLUTION_UNSPECIFIED, MEDIA_RESOLUTION_LOW,
   * MEDIA_RESOLUTION_MEDIUM, MEDIA_RESOLUTION_HIGH
   *
   * @param self::MEDIA_RESOLUTION_* $mediaResolution
   */
  public function setMediaResolution($mediaResolution)
  {
    $this->mediaResolution = $mediaResolution;
  }
  /**
   * @return self::MEDIA_RESOLUTION_*
   */
  public function getMediaResolution()
  {
    return $this->mediaResolution;
  }
  /**
   * Optional. Penalizes tokens that have already appeared in the generated
   * text. A positive value encourages the model to generate more diverse and
   * less repetitive text. Valid values can range from [-2.0, 2.0].
   *
   * @param float $presencePenalty
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
   * Optional. When this field is set, response_schema must be omitted and
   * response_mime_type must be set to `application/json`.
   *
   * @param array $responseJsonSchema
   */
  public function setResponseJsonSchema($responseJsonSchema)
  {
    $this->responseJsonSchema = $responseJsonSchema;
  }
  /**
   * @return array
   */
  public function getResponseJsonSchema()
  {
    return $this->responseJsonSchema;
  }
  /**
   * Optional. If set to true, the log probabilities of the output tokens are
   * returned. Log probabilities are the logarithm of the probability of a token
   * appearing in the output. A higher log probability means the token is more
   * likely to be generated. This can be useful for analyzing the model's
   * confidence in its own output and for debugging.
   *
   * @param bool $responseLogprobs
   */
  public function setResponseLogprobs($responseLogprobs)
  {
    $this->responseLogprobs = $responseLogprobs;
  }
  /**
   * @return bool
   */
  public function getResponseLogprobs()
  {
    return $this->responseLogprobs;
  }
  /**
   * Optional. The IANA standard MIME type of the response. The model will
   * generate output that conforms to this MIME type. Supported values include
   * 'text/plain' (default) and 'application/json'. The model needs to be
   * prompted to output the appropriate response type, otherwise the behavior is
   * undefined. This is a preview feature.
   *
   * @param string $responseMimeType
   */
  public function setResponseMimeType($responseMimeType)
  {
    $this->responseMimeType = $responseMimeType;
  }
  /**
   * @return string
   */
  public function getResponseMimeType()
  {
    return $this->responseMimeType;
  }
  /**
   * Optional. The modalities of the response. The model will generate a
   * response that includes all the specified modalities. For example, if this
   * is set to `[TEXT, IMAGE]`, the response will include both text and an
   * image.
   *
   * @param string[] $responseModalities
   */
  public function setResponseModalities($responseModalities)
  {
    $this->responseModalities = $responseModalities;
  }
  /**
   * @return string[]
   */
  public function getResponseModalities()
  {
    return $this->responseModalities;
  }
  /**
   * Optional. Lets you to specify a schema for the model's response, ensuring
   * that the output conforms to a particular structure. This is useful for
   * generating structured data such as JSON. The schema is a subset of the
   * [OpenAPI 3.0 schema object](https://spec.openapis.org/oas/v3.0.3#schema)
   * object. When this field is set, you must also set the `response_mime_type`
   * to `application/json`.
   *
   * @param GoogleCloudAiplatformV1Schema $responseSchema
   */
  public function setResponseSchema(GoogleCloudAiplatformV1Schema $responseSchema)
  {
    $this->responseSchema = $responseSchema;
  }
  /**
   * @return GoogleCloudAiplatformV1Schema
   */
  public function getResponseSchema()
  {
    return $this->responseSchema;
  }
  /**
   * Optional. Routing configuration.
   *
   * @param GoogleCloudAiplatformV1GenerationConfigRoutingConfig $routingConfig
   */
  public function setRoutingConfig(GoogleCloudAiplatformV1GenerationConfigRoutingConfig $routingConfig)
  {
    $this->routingConfig = $routingConfig;
  }
  /**
   * @return GoogleCloudAiplatformV1GenerationConfigRoutingConfig
   */
  public function getRoutingConfig()
  {
    return $this->routingConfig;
  }
  /**
   * Optional. A seed for the random number generator. By setting a seed, you
   * can make the model's output mostly deterministic. For a given prompt and
   * parameters (like temperature, top_p, etc.), the model will produce the same
   * response every time. However, it's not a guaranteed absolute deterministic
   * behavior. This is different from parameters like `temperature`, which
   * control the *level* of randomness. `seed` ensures that the "random" choices
   * the model makes are the same on every run, making it essential for testing
   * and ensuring reproducible results.
   *
   * @param int $seed
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
   * Optional. The speech generation config.
   *
   * @param GoogleCloudAiplatformV1SpeechConfig $speechConfig
   */
  public function setSpeechConfig(GoogleCloudAiplatformV1SpeechConfig $speechConfig)
  {
    $this->speechConfig = $speechConfig;
  }
  /**
   * @return GoogleCloudAiplatformV1SpeechConfig
   */
  public function getSpeechConfig()
  {
    return $this->speechConfig;
  }
  /**
   * Optional. A list of character sequences that will stop the model from
   * generating further tokens. If a stop sequence is generated, the output will
   * end at that point. This is useful for controlling the length and structure
   * of the output. For example, you can use ["\n", "###"] to stop generation at
   * a new line or a specific marker.
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
   * Optional. Controls the randomness of the output. A higher temperature
   * results in more creative and diverse responses, while a lower temperature
   * makes the output more predictable and focused. The valid range is (0.0,
   * 2.0].
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
   * Optional. Configuration for thinking features. An error will be returned if
   * this field is set for models that don't support thinking.
   *
   * @param GoogleCloudAiplatformV1GenerationConfigThinkingConfig $thinkingConfig
   */
  public function setThinkingConfig(GoogleCloudAiplatformV1GenerationConfigThinkingConfig $thinkingConfig)
  {
    $this->thinkingConfig = $thinkingConfig;
  }
  /**
   * @return GoogleCloudAiplatformV1GenerationConfigThinkingConfig
   */
  public function getThinkingConfig()
  {
    return $this->thinkingConfig;
  }
  /**
   * Optional. Specifies the top-k sampling threshold. The model considers only
   * the top k most probable tokens for the next token. This can be useful for
   * generating more coherent and less random text. For example, a `top_k` of 40
   * means the model will choose the next word from the 40 most likely words.
   *
   * @param float $topK
   */
  public function setTopK($topK)
  {
    $this->topK = $topK;
  }
  /**
   * @return float
   */
  public function getTopK()
  {
    return $this->topK;
  }
  /**
   * Optional. Specifies the nucleus sampling threshold. The model considers
   * only the smallest set of tokens whose cumulative probability is at least
   * `top_p`. This helps generate more diverse and less repetitive responses.
   * For example, a `top_p` of 0.9 means the model considers tokens until the
   * cumulative probability of the tokens to select from reaches 0.9. It's
   * recommended to adjust either temperature or `top_p`, but not both.
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
class_alias(GoogleCloudAiplatformV1GenerationConfig::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1GenerationConfig');
