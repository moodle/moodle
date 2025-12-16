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

class GoogleCloudDiscoveryengineV1betaSearchRequestContentSearchSpecSummarySpec extends \Google\Model
{
  /**
   * Specifies whether to filter out adversarial queries. The default value is
   * `false`. Google employs search-query classification to detect adversarial
   * queries. No summary is returned if the search query is classified as an
   * adversarial query. For example, a user might ask a question regarding
   * negative comments about the company or submit a query designed to generate
   * unsafe, policy-violating output. If this field is set to `true`, we skip
   * generating summaries for adversarial queries and return fallback messages
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
   * Specifies whether to filter out queries that have low relevance. The
   * default value is `false`. If this field is set to `false`, all search
   * results are used regardless of relevance to generate answers. If set to
   * `true`, only queries with high relevance search results will generate
   * answers.
   *
   * @var bool
   */
  public $ignoreLowRelevantContent;
  /**
   * Specifies whether to filter out queries that are not summary-seeking. The
   * default value is `false`. Google employs search-query classification to
   * detect summary-seeking queries. No summary is returned if the search query
   * is classified as a non-summary seeking query. For example, `why is the sky
   * blue` and `Who is the best soccer player in the world?` are summary-seeking
   * queries, but `SFO airport` and `world cup 2026` are not. They are most
   * likely navigational queries. If this field is set to `true`, we skip
   * generating summaries for non-summary seeking queries and return fallback
   * messages instead.
   *
   * @var bool
   */
  public $ignoreNonSummarySeekingQuery;
  /**
   * Specifies whether to include citations in the summary. The default value is
   * `false`. When this field is set to `true`, summaries include in-line
   * citation numbers. Example summary including citations: BigQuery is Google
   * Cloud's fully managed and completely serverless enterprise data warehouse
   * [1]. BigQuery supports all data types, works across clouds, and has built-
   * in machine learning and business intelligence, all within a unified
   * platform [2, 3]. The citation numbers refer to the returned search results
   * and are 1-indexed. For example, [1] means that the sentence is attributed
   * to the first search result. [2, 3] means that the sentence is attributed to
   * both the second and third search results.
   *
   * @var bool
   */
  public $includeCitations;
  /**
   * Language code for Summary. Use language tags defined by
   * [BCP47](https://www.rfc-editor.org/rfc/bcp/bcp47.txt). Note: This is an
   * experimental feature.
   *
   * @var string
   */
  public $languageCode;
  protected $modelPromptSpecType = GoogleCloudDiscoveryengineV1betaSearchRequestContentSearchSpecSummarySpecModelPromptSpec::class;
  protected $modelPromptSpecDataType = '';
  protected $modelSpecType = GoogleCloudDiscoveryengineV1betaSearchRequestContentSearchSpecSummarySpecModelSpec::class;
  protected $modelSpecDataType = '';
  protected $multimodalSpecType = GoogleCloudDiscoveryengineV1betaSearchRequestContentSearchSpecSummarySpecMultiModalSpec::class;
  protected $multimodalSpecDataType = '';
  /**
   * The number of top results to generate the summary from. If the number of
   * results returned is less than `summaryResultCount`, the summary is
   * generated from all of the results. At most 10 results for documents mode,
   * or 50 for chunks mode, can be used to generate a summary. The chunks mode
   * is used when SearchRequest.ContentSearchSpec.search_result_mode is set to
   * CHUNKS.
   *
   * @var int
   */
  public $summaryResultCount;
  /**
   * If true, answer will be generated from most relevant chunks from top search
   * results. This feature will improve summary quality. Note that with this
   * feature enabled, not all top search results will be referenced and included
   * in the reference list, so the citation source index only points to the
   * search results listed in the reference list.
   *
   * @var bool
   */
  public $useSemanticChunks;

  /**
   * Specifies whether to filter out adversarial queries. The default value is
   * `false`. Google employs search-query classification to detect adversarial
   * queries. No summary is returned if the search query is classified as an
   * adversarial query. For example, a user might ask a question regarding
   * negative comments about the company or submit a query designed to generate
   * unsafe, policy-violating output. If this field is set to `true`, we skip
   * generating summaries for adversarial queries and return fallback messages
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
   * Specifies whether to filter out queries that have low relevance. The
   * default value is `false`. If this field is set to `false`, all search
   * results are used regardless of relevance to generate answers. If set to
   * `true`, only queries with high relevance search results will generate
   * answers.
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
   * Specifies whether to filter out queries that are not summary-seeking. The
   * default value is `false`. Google employs search-query classification to
   * detect summary-seeking queries. No summary is returned if the search query
   * is classified as a non-summary seeking query. For example, `why is the sky
   * blue` and `Who is the best soccer player in the world?` are summary-seeking
   * queries, but `SFO airport` and `world cup 2026` are not. They are most
   * likely navigational queries. If this field is set to `true`, we skip
   * generating summaries for non-summary seeking queries and return fallback
   * messages instead.
   *
   * @param bool $ignoreNonSummarySeekingQuery
   */
  public function setIgnoreNonSummarySeekingQuery($ignoreNonSummarySeekingQuery)
  {
    $this->ignoreNonSummarySeekingQuery = $ignoreNonSummarySeekingQuery;
  }
  /**
   * @return bool
   */
  public function getIgnoreNonSummarySeekingQuery()
  {
    return $this->ignoreNonSummarySeekingQuery;
  }
  /**
   * Specifies whether to include citations in the summary. The default value is
   * `false`. When this field is set to `true`, summaries include in-line
   * citation numbers. Example summary including citations: BigQuery is Google
   * Cloud's fully managed and completely serverless enterprise data warehouse
   * [1]. BigQuery supports all data types, works across clouds, and has built-
   * in machine learning and business intelligence, all within a unified
   * platform [2, 3]. The citation numbers refer to the returned search results
   * and are 1-indexed. For example, [1] means that the sentence is attributed
   * to the first search result. [2, 3] means that the sentence is attributed to
   * both the second and third search results.
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
   * If specified, the spec will be used to modify the prompt provided to the
   * LLM.
   *
   * @param GoogleCloudDiscoveryengineV1betaSearchRequestContentSearchSpecSummarySpecModelPromptSpec $modelPromptSpec
   */
  public function setModelPromptSpec(GoogleCloudDiscoveryengineV1betaSearchRequestContentSearchSpecSummarySpecModelPromptSpec $modelPromptSpec)
  {
    $this->modelPromptSpec = $modelPromptSpec;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaSearchRequestContentSearchSpecSummarySpecModelPromptSpec
   */
  public function getModelPromptSpec()
  {
    return $this->modelPromptSpec;
  }
  /**
   * If specified, the spec will be used to modify the model specification
   * provided to the LLM.
   *
   * @param GoogleCloudDiscoveryengineV1betaSearchRequestContentSearchSpecSummarySpecModelSpec $modelSpec
   */
  public function setModelSpec(GoogleCloudDiscoveryengineV1betaSearchRequestContentSearchSpecSummarySpecModelSpec $modelSpec)
  {
    $this->modelSpec = $modelSpec;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaSearchRequestContentSearchSpecSummarySpecModelSpec
   */
  public function getModelSpec()
  {
    return $this->modelSpec;
  }
  /**
   * Optional. Multimodal specification.
   *
   * @param GoogleCloudDiscoveryengineV1betaSearchRequestContentSearchSpecSummarySpecMultiModalSpec $multimodalSpec
   */
  public function setMultimodalSpec(GoogleCloudDiscoveryengineV1betaSearchRequestContentSearchSpecSummarySpecMultiModalSpec $multimodalSpec)
  {
    $this->multimodalSpec = $multimodalSpec;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaSearchRequestContentSearchSpecSummarySpecMultiModalSpec
   */
  public function getMultimodalSpec()
  {
    return $this->multimodalSpec;
  }
  /**
   * The number of top results to generate the summary from. If the number of
   * results returned is less than `summaryResultCount`, the summary is
   * generated from all of the results. At most 10 results for documents mode,
   * or 50 for chunks mode, can be used to generate a summary. The chunks mode
   * is used when SearchRequest.ContentSearchSpec.search_result_mode is set to
   * CHUNKS.
   *
   * @param int $summaryResultCount
   */
  public function setSummaryResultCount($summaryResultCount)
  {
    $this->summaryResultCount = $summaryResultCount;
  }
  /**
   * @return int
   */
  public function getSummaryResultCount()
  {
    return $this->summaryResultCount;
  }
  /**
   * If true, answer will be generated from most relevant chunks from top search
   * results. This feature will improve summary quality. Note that with this
   * feature enabled, not all top search results will be referenced and included
   * in the reference list, so the citation source index only points to the
   * search results listed in the reference list.
   *
   * @param bool $useSemanticChunks
   */
  public function setUseSemanticChunks($useSemanticChunks)
  {
    $this->useSemanticChunks = $useSemanticChunks;
  }
  /**
   * @return bool
   */
  public function getUseSemanticChunks()
  {
    return $this->useSemanticChunks;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1betaSearchRequestContentSearchSpecSummarySpec::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1betaSearchRequestContentSearchSpecSummarySpec');
