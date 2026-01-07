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

class GoogleCloudDiscoveryengineV1WidgetConfigUiSettingsGenerativeAnswerConfig extends \Google\Model
{
  /**
   * Unspecified image source (multimodal feature is disabled by default)
   */
  public const IMAGE_SOURCE_IMAGE_SOURCE_UNSPECIFIED = 'IMAGE_SOURCE_UNSPECIFIED';
  /**
   * Behavior when service determines the pick from all available sources.
   */
  public const IMAGE_SOURCE_ALL_AVAILABLE_SOURCES = 'ALL_AVAILABLE_SOURCES';
  /**
   * Include image from corpus in the answer.
   */
  public const IMAGE_SOURCE_CORPUS_IMAGE_ONLY = 'CORPUS_IMAGE_ONLY';
  /**
   * Triggers figure generation in the answer.
   */
  public const IMAGE_SOURCE_FIGURE_GENERATION_ONLY = 'FIGURE_GENERATION_ONLY';
  /**
   * Whether generated answer contains suggested related questions.
   *
   * @var bool
   */
  public $disableRelatedQuestions;
  /**
   * Optional. Specifies whether to filter out queries that are adversarial.
   *
   * @var bool
   */
  public $ignoreAdversarialQuery;
  /**
   * Optional. Specifies whether to filter out queries that are not relevant to
   * the content.
   *
   * @var bool
   */
  public $ignoreLowRelevantContent;
  /**
   * Optional. Specifies whether to filter out queries that are not answer-
   * seeking. The default value is `false`. No answer is returned if the search
   * query is classified as a non-answer seeking query. If this field is set to
   * `true`, we skip generating answers for non-answer seeking queries and
   * return fallback messages instead.
   *
   * @var bool
   */
  public $ignoreNonAnswerSeekingQuery;
  /**
   * Optional. Source of image returned in the answer.
   *
   * @var string
   */
  public $imageSource;
  /**
   * Language code for Summary. Use language tags defined by
   * [BCP47](https://www.rfc-editor.org/rfc/bcp/bcp47.txt). Note: This is an
   * experimental feature.
   *
   * @var string
   */
  public $languageCode;
  /**
   * Max rephrase steps. The max number is 5 steps. If not set or set to < 1, it
   * will be set to 1 by default.
   *
   * @var int
   */
  public $maxRephraseSteps;
  /**
   * Text at the beginning of the prompt that instructs the model that generates
   * the answer.
   *
   * @var string
   */
  public $modelPromptPreamble;
  /**
   * The model version used to generate the answer.
   *
   * @var string
   */
  public $modelVersion;
  /**
   * The number of top results to generate the answer from. Up to 10.
   *
   * @var int
   */
  public $resultCount;

  /**
   * Whether generated answer contains suggested related questions.
   *
   * @param bool $disableRelatedQuestions
   */
  public function setDisableRelatedQuestions($disableRelatedQuestions)
  {
    $this->disableRelatedQuestions = $disableRelatedQuestions;
  }
  /**
   * @return bool
   */
  public function getDisableRelatedQuestions()
  {
    return $this->disableRelatedQuestions;
  }
  /**
   * Optional. Specifies whether to filter out queries that are adversarial.
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
   * Optional. Specifies whether to filter out queries that are not relevant to
   * the content.
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
   * Optional. Specifies whether to filter out queries that are not answer-
   * seeking. The default value is `false`. No answer is returned if the search
   * query is classified as a non-answer seeking query. If this field is set to
   * `true`, we skip generating answers for non-answer seeking queries and
   * return fallback messages instead.
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
   * Optional. Source of image returned in the answer.
   *
   * Accepted values: IMAGE_SOURCE_UNSPECIFIED, ALL_AVAILABLE_SOURCES,
   * CORPUS_IMAGE_ONLY, FIGURE_GENERATION_ONLY
   *
   * @param self::IMAGE_SOURCE_* $imageSource
   */
  public function setImageSource($imageSource)
  {
    $this->imageSource = $imageSource;
  }
  /**
   * @return self::IMAGE_SOURCE_*
   */
  public function getImageSource()
  {
    return $this->imageSource;
  }
  /**
   * Language code for Summary. Use language tags defined by
   * [BCP47](https://www.rfc-editor.org/rfc/bcp/bcp47.txt). Note: This is an
   * experimental feature.
   *
   * @param string $languageCode
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
   * Max rephrase steps. The max number is 5 steps. If not set or set to < 1, it
   * will be set to 1 by default.
   *
   * @param int $maxRephraseSteps
   */
  public function setMaxRephraseSteps($maxRephraseSteps)
  {
    $this->maxRephraseSteps = $maxRephraseSteps;
  }
  /**
   * @return int
   */
  public function getMaxRephraseSteps()
  {
    return $this->maxRephraseSteps;
  }
  /**
   * Text at the beginning of the prompt that instructs the model that generates
   * the answer.
   *
   * @param string $modelPromptPreamble
   */
  public function setModelPromptPreamble($modelPromptPreamble)
  {
    $this->modelPromptPreamble = $modelPromptPreamble;
  }
  /**
   * @return string
   */
  public function getModelPromptPreamble()
  {
    return $this->modelPromptPreamble;
  }
  /**
   * The model version used to generate the answer.
   *
   * @param string $modelVersion
   */
  public function setModelVersion($modelVersion)
  {
    $this->modelVersion = $modelVersion;
  }
  /**
   * @return string
   */
  public function getModelVersion()
  {
    return $this->modelVersion;
  }
  /**
   * The number of top results to generate the answer from. Up to 10.
   *
   * @param int $resultCount
   */
  public function setResultCount($resultCount)
  {
    $this->resultCount = $resultCount;
  }
  /**
   * @return int
   */
  public function getResultCount()
  {
    return $this->resultCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1WidgetConfigUiSettingsGenerativeAnswerConfig::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1WidgetConfigUiSettingsGenerativeAnswerConfig');
