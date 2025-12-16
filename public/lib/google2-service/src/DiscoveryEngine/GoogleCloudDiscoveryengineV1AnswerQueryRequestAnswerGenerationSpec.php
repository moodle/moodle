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

class GoogleCloudDiscoveryengineV1AnswerQueryRequestAnswerGenerationSpec extends \Google\Model
{
  /**
   * Language code for Answer. Use language tags defined by
   * [BCP47](https://www.rfc-editor.org/rfc/bcp/bcp47.txt). Note: This is an
   * experimental feature.
   *
   * @var string
   */
  public $answerLanguageCode;
  /**
   * Specifies whether to filter out adversarial queries. The default value is
   * `false`. Google employs search-query classification to detect adversarial
   * queries. No answer is returned if the search query is classified as an
   * adversarial query. For example, a user might ask a question regarding
   * negative comments about the company or submit a query designed to generate
   * unsafe, policy-violating output. If this field is set to `true`, we skip
   * generating answers for adversarial queries and return fallback messages
   * instead.
   *
   * @var bool
   */
  public $ignoreAdversarialQuery;
  /**
   * Optional. Specifies whether to filter out jail-breaking queries. The
   * default value is `false`. Google employs search-query classification to
   * detect jail-breaking queries. No summary is returned if the search query is
   * classified as a jail-breaking query. A user might add instructions to the
   * query to change the tone, style, language, content of the answer, or ask
   * the model to act as a different entity, e.g. "Reply in the tone of a
   * competing company's CEO". If this field is set to `true`, we skip
   * generating summaries for jail-breaking queries and return fallback messages
   * instead.
   *
   * @var bool
   */
  public $ignoreJailBreakingQuery;
  /**
   * Specifies whether to filter out queries that have low relevance. If this
   * field is set to `false`, all search results are used regardless of
   * relevance to generate answers. If set to `true` or unset, the behavior will
   * be determined automatically by the service.
   *
   * @var bool
   */
  public $ignoreLowRelevantContent;
  /**
   * Specifies whether to filter out queries that are not answer-seeking. The
   * default value is `false`. Google employs search-query classification to
   * detect answer-seeking queries. No answer is returned if the search query is
   * classified as a non-answer seeking query. If this field is set to `true`,
   * we skip generating answers for non-answer seeking queries and return
   * fallback messages instead.
   *
   * @var bool
   */
  public $ignoreNonAnswerSeekingQuery;
  /**
   * Specifies whether to include citation metadata in the answer. The default
   * value is `false`.
   *
   * @var bool
   */
  public $includeCitations;
  protected $modelSpecType = GoogleCloudDiscoveryengineV1AnswerQueryRequestAnswerGenerationSpecModelSpec::class;
  protected $modelSpecDataType = '';
  protected $promptSpecType = GoogleCloudDiscoveryengineV1AnswerQueryRequestAnswerGenerationSpecPromptSpec::class;
  protected $promptSpecDataType = '';

  /**
   * Language code for Answer. Use language tags defined by
   * [BCP47](https://www.rfc-editor.org/rfc/bcp/bcp47.txt). Note: This is an
   * experimental feature.
   *
   * @param string $answerLanguageCode
   */
  public function setAnswerLanguageCode($answerLanguageCode)
  {
    $this->answerLanguageCode = $answerLanguageCode;
  }
  /**
   * @return string
   */
  public function getAnswerLanguageCode()
  {
    return $this->answerLanguageCode;
  }
  /**
   * Specifies whether to filter out adversarial queries. The default value is
   * `false`. Google employs search-query classification to detect adversarial
   * queries. No answer is returned if the search query is classified as an
   * adversarial query. For example, a user might ask a question regarding
   * negative comments about the company or submit a query designed to generate
   * unsafe, policy-violating output. If this field is set to `true`, we skip
   * generating answers for adversarial queries and return fallback messages
   * instead.
   *
   * @param bool $ignoreAdversarialQuery
   */
  public function setIgnoreAdversarialQuery($ignoreAdversarialQuery)
  {
    $this->ignoreAdversarialQuery = $ignoreAdversarialQuery;
  }
  /**
   * @return bool
   */
  public function getIgnoreAdversarialQuery()
  {
    return $this->ignoreAdversarialQuery;
  }
  /**
   * Optional. Specifies whether to filter out jail-breaking queries. The
   * default value is `false`. Google employs search-query classification to
   * detect jail-breaking queries. No summary is returned if the search query is
   * classified as a jail-breaking query. A user might add instructions to the
   * query to change the tone, style, language, content of the answer, or ask
   * the model to act as a different entity, e.g. "Reply in the tone of a
   * competing company's CEO". If this field is set to `true`, we skip
   * generating summaries for jail-breaking queries and return fallback messages
   * instead.
   *
   * @param bool $ignoreJailBreakingQuery
   */
  public function setIgnoreJailBreakingQuery($ignoreJailBreakingQuery)
  {
    $this->ignoreJailBreakingQuery = $ignoreJailBreakingQuery;
  }
  /**
   * @return bool
   */
  public function getIgnoreJailBreakingQuery()
  {
    return $this->ignoreJailBreakingQuery;
  }
  /**
   * Specifies whether to filter out queries that have low relevance. If this
   * field is set to `false`, all search results are used regardless of
   * relevance to generate answers. If set to `true` or unset, the behavior will
   * be determined automatically by the service.
   *
   * @param bool $ignoreLowRelevantContent
   */
  public function setIgnoreLowRelevantContent($ignoreLowRelevantContent)
  {
    $this->ignoreLowRelevantContent = $ignoreLowRelevantContent;
  }
  /**
   * @return bool
   */
  public function getIgnoreLowRelevantContent()
  {
    return $this->ignoreLowRelevantContent;
  }
  /**
   * Specifies whether to filter out queries that are not answer-seeking. The
   * default value is `false`. Google employs search-query classification to
   * detect answer-seeking queries. No answer is returned if the search query is
   * classified as a non-answer seeking query. If this field is set to `true`,
   * we skip generating answers for non-answer seeking queries and return
   * fallback messages instead.
   *
   * @param bool $ignoreNonAnswerSeekingQuery
   */
  public function setIgnoreNonAnswerSeekingQuery($ignoreNonAnswerSeekingQuery)
  {
    $this->ignoreNonAnswerSeekingQuery = $ignoreNonAnswerSeekingQuery;
  }
  /**
   * @return bool
   */
  public function getIgnoreNonAnswerSeekingQuery()
  {
    return $this->ignoreNonAnswerSeekingQuery;
  }
  /**
   * Specifies whether to include citation metadata in the answer. The default
   * value is `false`.
   *
   * @param bool $includeCitations
   */
  public function setIncludeCitations($includeCitations)
  {
    $this->includeCitations = $includeCitations;
  }
  /**
   * @return bool
   */
  public function getIncludeCitations()
  {
    return $this->includeCitations;
  }
  /**
   * Answer generation model specification.
   *
   * @param GoogleCloudDiscoveryengineV1AnswerQueryRequestAnswerGenerationSpecModelSpec $modelSpec
   */
  public function setModelSpec(GoogleCloudDiscoveryengineV1AnswerQueryRequestAnswerGenerationSpecModelSpec $modelSpec)
  {
    $this->modelSpec = $modelSpec;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1AnswerQueryRequestAnswerGenerationSpecModelSpec
   */
  public function getModelSpec()
  {
    return $this->modelSpec;
  }
  /**
   * Answer generation prompt specification.
   *
   * @param GoogleCloudDiscoveryengineV1AnswerQueryRequestAnswerGenerationSpecPromptSpec $promptSpec
   */
  public function setPromptSpec(GoogleCloudDiscoveryengineV1AnswerQueryRequestAnswerGenerationSpecPromptSpec $promptSpec)
  {
    $this->promptSpec = $promptSpec;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1AnswerQueryRequestAnswerGenerationSpecPromptSpec
   */
  public function getPromptSpec()
  {
    return $this->promptSpec;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1AnswerQueryRequestAnswerGenerationSpec::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1AnswerQueryRequestAnswerGenerationSpec');
