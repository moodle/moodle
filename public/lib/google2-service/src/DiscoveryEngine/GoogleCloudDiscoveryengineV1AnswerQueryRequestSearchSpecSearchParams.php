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

class GoogleCloudDiscoveryengineV1AnswerQueryRequestSearchSpecSearchParams extends \Google\Collection
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
  protected $collection_key = 'dataStoreSpecs';
  protected $boostSpecType = GoogleCloudDiscoveryengineV1SearchRequestBoostSpec::class;
  protected $boostSpecDataType = '';
  protected $dataStoreSpecsType = GoogleCloudDiscoveryengineV1SearchRequestDataStoreSpec::class;
  protected $dataStoreSpecsDataType = 'array';
  /**
   * The filter syntax consists of an expression language for constructing a
   * predicate from one or more fields of the documents being filtered. Filter
   * expression is case-sensitive. This will be used to filter search results
   * which may affect the Answer response. If this field is unrecognizable, an
   * `INVALID_ARGUMENT` is returned. Filtering in Vertex AI Search is done by
   * mapping the LHS filter key to a key property defined in the Vertex AI
   * Search backend -- this mapping is defined by the customer in their schema.
   * For example a media customers might have a field 'name' in their schema. In
   * this case the filter would look like this: filter --> name:'ANY("king
   * kong")' For more information about filtering including syntax and filter
   * operators, see [Filter](https://cloud.google.com/generative-ai-app-
   * builder/docs/filter-search-metadata)
   *
   * @var string
   */
  public $filter;
  /**
   * Number of search results to return. The default value is 10.
   *
   * @var int
   */
  public $maxReturnResults;
  /**
   * The order in which documents are returned. Documents can be ordered by a
   * field in an Document object. Leave it unset if ordered by relevance.
   * `order_by` expression is case-sensitive. For more information on ordering,
   * see [Ordering](https://cloud.google.com/retail/docs/filter-and-order#order)
   * If this field is unrecognizable, an `INVALID_ARGUMENT` is returned.
   *
   * @var string
   */
  public $orderBy;
  /**
   * Specifies the search result mode. If unspecified, the search result mode
   * defaults to `DOCUMENTS`. See [parse and chunk
   * documents](https://cloud.google.com/generative-ai-app-builder/docs/parse-
   * chunk-documents)
   *
   * @var string
   */
  public $searchResultMode;

  /**
   * Boost specification to boost certain documents in search results which may
   * affect the answer query response. For more information on boosting, see
   * [Boosting](https://cloud.google.com/retail/docs/boosting#boost)
   *
   * @param GoogleCloudDiscoveryengineV1SearchRequestBoostSpec $boostSpec
   */
  public function setBoostSpec(GoogleCloudDiscoveryengineV1SearchRequestBoostSpec $boostSpec)
  {
    $this->boostSpec = $boostSpec;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1SearchRequestBoostSpec
   */
  public function getBoostSpec()
  {
    return $this->boostSpec;
  }
  /**
   * Specs defining dataStores to filter on in a search call and configurations
   * for those dataStores. This is only considered for engines with multiple
   * dataStores use case. For single dataStore within an engine, they should use
   * the specs at the top level.
   *
   * @param GoogleCloudDiscoveryengineV1SearchRequestDataStoreSpec[] $dataStoreSpecs
   */
  public function setDataStoreSpecs($dataStoreSpecs)
  {
    $this->dataStoreSpecs = $dataStoreSpecs;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1SearchRequestDataStoreSpec[]
   */
  public function getDataStoreSpecs()
  {
    return $this->dataStoreSpecs;
  }
  /**
   * The filter syntax consists of an expression language for constructing a
   * predicate from one or more fields of the documents being filtered. Filter
   * expression is case-sensitive. This will be used to filter search results
   * which may affect the Answer response. If this field is unrecognizable, an
   * `INVALID_ARGUMENT` is returned. Filtering in Vertex AI Search is done by
   * mapping the LHS filter key to a key property defined in the Vertex AI
   * Search backend -- this mapping is defined by the customer in their schema.
   * For example a media customers might have a field 'name' in their schema. In
   * this case the filter would look like this: filter --> name:'ANY("king
   * kong")' For more information about filtering including syntax and filter
   * operators, see [Filter](https://cloud.google.com/generative-ai-app-
   * builder/docs/filter-search-metadata)
   *
   * @param string $filter
   */
  public function setFilter($filter)
  {
    $this->filter = $filter;
  }
  /**
   * @return string
   */
  public function getFilter()
  {
    return $this->filter;
  }
  /**
   * Number of search results to return. The default value is 10.
   *
   * @param int $maxReturnResults
   */
  public function setMaxReturnResults($maxReturnResults)
  {
    $this->maxReturnResults = $maxReturnResults;
  }
  /**
   * @return int
   */
  public function getMaxReturnResults()
  {
    return $this->maxReturnResults;
  }
  /**
   * The order in which documents are returned. Documents can be ordered by a
   * field in an Document object. Leave it unset if ordered by relevance.
   * `order_by` expression is case-sensitive. For more information on ordering,
   * see [Ordering](https://cloud.google.com/retail/docs/filter-and-order#order)
   * If this field is unrecognizable, an `INVALID_ARGUMENT` is returned.
   *
   * @param string $orderBy
   */
  public function setOrderBy($orderBy)
  {
    $this->orderBy = $orderBy;
  }
  /**
   * @return string
   */
  public function getOrderBy()
  {
    return $this->orderBy;
  }
  /**
   * Specifies the search result mode. If unspecified, the search result mode
   * defaults to `DOCUMENTS`. See [parse and chunk
   * documents](https://cloud.google.com/generative-ai-app-builder/docs/parse-
   * chunk-documents)
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1AnswerQueryRequestSearchSpecSearchParams::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1AnswerQueryRequestSearchSpecSearchParams');
