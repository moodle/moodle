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

class GoogleCloudAiplatformV1Candidate extends \Google\Collection
{
  /**
   * The finish reason is unspecified.
   */
  public const FINISH_REASON_FINISH_REASON_UNSPECIFIED = 'FINISH_REASON_UNSPECIFIED';
  /**
   * The model reached a natural stopping point or a configured stop sequence.
   */
  public const FINISH_REASON_STOP = 'STOP';
  /**
   * The model generated the maximum number of tokens allowed by the
   * `max_output_tokens` parameter.
   */
  public const FINISH_REASON_MAX_TOKENS = 'MAX_TOKENS';
  /**
   * The model stopped generating because the content potentially violates
   * safety policies. NOTE: When streaming, the `content` field is empty if
   * content filters block the output.
   */
  public const FINISH_REASON_SAFETY = 'SAFETY';
  /**
   * The model stopped generating because the content may be a recitation from a
   * source.
   */
  public const FINISH_REASON_RECITATION = 'RECITATION';
  /**
   * The model stopped generating for a reason not otherwise specified.
   */
  public const FINISH_REASON_OTHER = 'OTHER';
  /**
   * The model stopped generating because the content contains a term from a
   * configured blocklist.
   */
  public const FINISH_REASON_BLOCKLIST = 'BLOCKLIST';
  /**
   * The model stopped generating because the content may be prohibited.
   */
  public const FINISH_REASON_PROHIBITED_CONTENT = 'PROHIBITED_CONTENT';
  /**
   * The model stopped generating because the content may contain sensitive
   * personally identifiable information (SPII).
   */
  public const FINISH_REASON_SPII = 'SPII';
  /**
   * The model generated a function call that is syntactically invalid and can't
   * be parsed.
   */
  public const FINISH_REASON_MALFORMED_FUNCTION_CALL = 'MALFORMED_FUNCTION_CALL';
  /**
   * The model response was blocked by Model Armor.
   */
  public const FINISH_REASON_MODEL_ARMOR = 'MODEL_ARMOR';
  /**
   * The generated image potentially violates safety policies.
   */
  public const FINISH_REASON_IMAGE_SAFETY = 'IMAGE_SAFETY';
  /**
   * The generated image may contain prohibited content.
   */
  public const FINISH_REASON_IMAGE_PROHIBITED_CONTENT = 'IMAGE_PROHIBITED_CONTENT';
  /**
   * The generated image may be a recitation from a source.
   */
  public const FINISH_REASON_IMAGE_RECITATION = 'IMAGE_RECITATION';
  /**
   * The image generation stopped for a reason not otherwise specified.
   */
  public const FINISH_REASON_IMAGE_OTHER = 'IMAGE_OTHER';
  /**
   * The model generated a function call that is semantically invalid. This can
   * happen, for example, if function calling is not enabled or the generated
   * function is not in the function declaration.
   */
  public const FINISH_REASON_UNEXPECTED_TOOL_CALL = 'UNEXPECTED_TOOL_CALL';
  /**
   * The model was expected to generate an image, but didn't.
   */
  public const FINISH_REASON_NO_IMAGE = 'NO_IMAGE';
  protected $collection_key = 'safetyRatings';
  /**
   * Output only. The average log probability of the tokens in this candidate.
   * This is a length-normalized score that can be used to compare the quality
   * of candidates of different lengths. A higher average log probability
   * suggests a more confident and coherent response.
   *
   * @var 
   */
  public $avgLogprobs;
  protected $citationMetadataType = GoogleCloudAiplatformV1CitationMetadata::class;
  protected $citationMetadataDataType = '';
  protected $contentType = GoogleCloudAiplatformV1Content::class;
  protected $contentDataType = '';
  /**
   * Output only. Describes the reason the model stopped generating tokens in
   * more detail. This field is returned only when `finish_reason` is set.
   *
   * @var string
   */
  public $finishMessage;
  /**
   * Output only. The reason why the model stopped generating tokens. If empty,
   * the model has not stopped generating.
   *
   * @var string
   */
  public $finishReason;
  protected $groundingMetadataType = GoogleCloudAiplatformV1GroundingMetadata::class;
  protected $groundingMetadataDataType = '';
  /**
   * Output only. The 0-based index of this candidate in the list of generated
   * responses. This is useful for distinguishing between multiple candidates
   * when `candidate_count` > 1.
   *
   * @var int
   */
  public $index;
  protected $logprobsResultType = GoogleCloudAiplatformV1LogprobsResult::class;
  protected $logprobsResultDataType = '';
  protected $safetyRatingsType = GoogleCloudAiplatformV1SafetyRating::class;
  protected $safetyRatingsDataType = 'array';
  protected $urlContextMetadataType = GoogleCloudAiplatformV1UrlContextMetadata::class;
  protected $urlContextMetadataDataType = '';

  public function setAvgLogprobs($avgLogprobs)
  {
    $this->avgLogprobs = $avgLogprobs;
  }
  public function getAvgLogprobs()
  {
    return $this->avgLogprobs;
  }
  /**
   * Output only. A collection of citations that apply to the generated content.
   *
   * @param GoogleCloudAiplatformV1CitationMetadata $citationMetadata
   */
  public function setCitationMetadata(GoogleCloudAiplatformV1CitationMetadata $citationMetadata)
  {
    $this->citationMetadata = $citationMetadata;
  }
  /**
   * @return GoogleCloudAiplatformV1CitationMetadata
   */
  public function getCitationMetadata()
  {
    return $this->citationMetadata;
  }
  /**
   * Output only. The content of the candidate.
   *
   * @param GoogleCloudAiplatformV1Content $content
   */
  public function setContent(GoogleCloudAiplatformV1Content $content)
  {
    $this->content = $content;
  }
  /**
   * @return GoogleCloudAiplatformV1Content
   */
  public function getContent()
  {
    return $this->content;
  }
  /**
   * Output only. Describes the reason the model stopped generating tokens in
   * more detail. This field is returned only when `finish_reason` is set.
   *
   * @param string $finishMessage
   */
  public function setFinishMessage($finishMessage)
  {
    $this->finishMessage = $finishMessage;
  }
  /**
   * @return string
   */
  public function getFinishMessage()
  {
    return $this->finishMessage;
  }
  /**
   * Output only. The reason why the model stopped generating tokens. If empty,
   * the model has not stopped generating.
   *
   * Accepted values: FINISH_REASON_UNSPECIFIED, STOP, MAX_TOKENS, SAFETY,
   * RECITATION, OTHER, BLOCKLIST, PROHIBITED_CONTENT, SPII,
   * MALFORMED_FUNCTION_CALL, MODEL_ARMOR, IMAGE_SAFETY,
   * IMAGE_PROHIBITED_CONTENT, IMAGE_RECITATION, IMAGE_OTHER,
   * UNEXPECTED_TOOL_CALL, NO_IMAGE
   *
   * @param self::FINISH_REASON_* $finishReason
   */
  public function setFinishReason($finishReason)
  {
    $this->finishReason = $finishReason;
  }
  /**
   * @return self::FINISH_REASON_*
   */
  public function getFinishReason()
  {
    return $this->finishReason;
  }
  /**
   * Output only. Metadata returned when grounding is enabled. It contains the
   * sources used to ground the generated content.
   *
   * @param GoogleCloudAiplatformV1GroundingMetadata $groundingMetadata
   */
  public function setGroundingMetadata(GoogleCloudAiplatformV1GroundingMetadata $groundingMetadata)
  {
    $this->groundingMetadata = $groundingMetadata;
  }
  /**
   * @return GoogleCloudAiplatformV1GroundingMetadata
   */
  public function getGroundingMetadata()
  {
    return $this->groundingMetadata;
  }
  /**
   * Output only. The 0-based index of this candidate in the list of generated
   * responses. This is useful for distinguishing between multiple candidates
   * when `candidate_count` > 1.
   *
   * @param int $index
   */
  public function setIndex($index)
  {
    $this->index = $index;
  }
  /**
   * @return int
   */
  public function getIndex()
  {
    return $this->index;
  }
  /**
   * Output only. The detailed log probability information for the tokens in
   * this candidate. This is useful for debugging, understanding model
   * uncertainty, and identifying potential "hallucinations".
   *
   * @param GoogleCloudAiplatformV1LogprobsResult $logprobsResult
   */
  public function setLogprobsResult(GoogleCloudAiplatformV1LogprobsResult $logprobsResult)
  {
    $this->logprobsResult = $logprobsResult;
  }
  /**
   * @return GoogleCloudAiplatformV1LogprobsResult
   */
  public function getLogprobsResult()
  {
    return $this->logprobsResult;
  }
  /**
   * Output only. A list of ratings for the safety of a response candidate.
   * There is at most one rating per category.
   *
   * @param GoogleCloudAiplatformV1SafetyRating[] $safetyRatings
   */
  public function setSafetyRatings($safetyRatings)
  {
    $this->safetyRatings = $safetyRatings;
  }
  /**
   * @return GoogleCloudAiplatformV1SafetyRating[]
   */
  public function getSafetyRatings()
  {
    return $this->safetyRatings;
  }
  /**
   * Output only. Metadata returned when the model uses the `url_context` tool
   * to get information from a user-provided URL.
   *
   * @param GoogleCloudAiplatformV1UrlContextMetadata $urlContextMetadata
   */
  public function setUrlContextMetadata(GoogleCloudAiplatformV1UrlContextMetadata $urlContextMetadata)
  {
    $this->urlContextMetadata = $urlContextMetadata;
  }
  /**
   * @return GoogleCloudAiplatformV1UrlContextMetadata
   */
  public function getUrlContextMetadata()
  {
    return $this->urlContextMetadata;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1Candidate::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1Candidate');
