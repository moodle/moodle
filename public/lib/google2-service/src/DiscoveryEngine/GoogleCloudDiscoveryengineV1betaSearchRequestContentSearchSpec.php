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

class GoogleCloudDiscoveryengineV1betaSearchRequestContentSearchSpec extends \Google\Model
{
  /**
   * Default value.
   */
  public const SEARCH_RESULT_MODE_SEARCH_RESULT_MODE_UNSPECIFIED = 'SEARCH_RESULT_MODE_UNSPECIFIED';
  /**
   * Returns documents in the search result.
   */
  public const SEARCH_RESULT_MODE_DOCUMENTS = 'DOCUMENTS';
  /**
   * Returns chunks in the search result. Only available if the
   * DocumentProcessingConfig.chunking_config is specified.
   */
  public const SEARCH_RESULT_MODE_CHUNKS = 'CHUNKS';
  protected $chunkSpecType = GoogleCloudDiscoveryengineV1betaSearchRequestContentSearchSpecChunkSpec::class;
  protected $chunkSpecDataType = '';
  protected $extractiveContentSpecType = GoogleCloudDiscoveryengineV1betaSearchRequestContentSearchSpecExtractiveContentSpec::class;
  protected $extractiveContentSpecDataType = '';
  /**
   * Specifies the search result mode. If unspecified, the search result mode
   * defaults to `DOCUMENTS`.
   *
   * @var string
   */
  public $searchResultMode;
  protected $snippetSpecType = GoogleCloudDiscoveryengineV1betaSearchRequestContentSearchSpecSnippetSpec::class;
  protected $snippetSpecDataType = '';
  protected $summarySpecType = GoogleCloudDiscoveryengineV1betaSearchRequestContentSearchSpecSummarySpec::class;
  protected $summarySpecDataType = '';

  /**
   * Specifies the chunk spec to be returned from the search response. Only
   * available if the SearchRequest.ContentSearchSpec.search_result_mode is set
   * to CHUNKS
   *
   * @param GoogleCloudDiscoveryengineV1betaSearchRequestContentSearchSpecChunkSpec $chunkSpec
   */
  public function setChunkSpec(GoogleCloudDiscoveryengineV1betaSearchRequestContentSearchSpecChunkSpec $chunkSpec)
  {
    $this->chunkSpec = $chunkSpec;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaSearchRequestContentSearchSpecChunkSpec
   */
  public function getChunkSpec()
  {
    return $this->chunkSpec;
  }
  /**
   * If there is no extractive_content_spec provided, there will be no
   * extractive answer in the search response.
   *
   * @param GoogleCloudDiscoveryengineV1betaSearchRequestContentSearchSpecExtractiveContentSpec $extractiveContentSpec
   */
  public function setExtractiveContentSpec(GoogleCloudDiscoveryengineV1betaSearchRequestContentSearchSpecExtractiveContentSpec $extractiveContentSpec)
  {
    $this->extractiveContentSpec = $extractiveContentSpec;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaSearchRequestContentSearchSpecExtractiveContentSpec
   */
  public function getExtractiveContentSpec()
  {
    return $this->extractiveContentSpec;
  }
  /**
   * Specifies the search result mode. If unspecified, the search result mode
   * defaults to `DOCUMENTS`.
   *
   * Accepted values: SEARCH_RESULT_MODE_UNSPECIFIED, DOCUMENTS, CHUNKS
   *
   * @param self::SEARCH_RESULT_MODE_* $searchResultMode
   */
  public function setSearchResultMode($searchResultMode)
  {
    $this->searchResultMode = $searchResultMode;
  }
  /**
   * @return self::SEARCH_RESULT_MODE_*
   */
  public function getSearchResultMode()
  {
    return $this->searchResultMode;
  }
  /**
   * If `snippetSpec` is not specified, snippets are not included in the search
   * response.
   *
   * @param GoogleCloudDiscoveryengineV1betaSearchRequestContentSearchSpecSnippetSpec $snippetSpec
   */
  public function setSnippetSpec(GoogleCloudDiscoveryengineV1betaSearchRequestContentSearchSpecSnippetSpec $snippetSpec)
  {
    $this->snippetSpec = $snippetSpec;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaSearchRequestContentSearchSpecSnippetSpec
   */
  public function getSnippetSpec()
  {
    return $this->snippetSpec;
  }
  /**
   * If `summarySpec` is not specified, summaries are not included in the search
   * response.
   *
   * @param GoogleCloudDiscoveryengineV1betaSearchRequestContentSearchSpecSummarySpec $summarySpec
   */
  public function setSummarySpec(GoogleCloudDiscoveryengineV1betaSearchRequestContentSearchSpecSummarySpec $summarySpec)
  {
    $this->summarySpec = $summarySpec;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaSearchRequestContentSearchSpecSummarySpec
   */
  public function getSummarySpec()
  {
    return $this->summarySpec;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1betaSearchRequestContentSearchSpec::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1betaSearchRequestContentSearchSpec');
