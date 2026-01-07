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

namespace Google\Service\CloudSearch;

class SearchResponse extends \Google\Collection
{
  protected $collection_key = 'structuredResults';
  protected $debugInfoType = ResponseDebugInfo::class;
  protected $debugInfoDataType = '';
  protected $errorInfoType = ErrorInfo::class;
  protected $errorInfoDataType = '';
  protected $facetResultsType = FacetResult::class;
  protected $facetResultsDataType = 'array';
  /**
   * Whether there are more search results matching the query.
   *
   * @var bool
   */
  public $hasMoreResults;
  protected $queryInterpretationType = QueryInterpretation::class;
  protected $queryInterpretationDataType = '';
  /**
   * The estimated result count for this query.
   *
   * @var string
   */
  public $resultCountEstimate;
  /**
   * The exact result count for this query.
   *
   * @var string
   */
  public $resultCountExact;
  protected $resultCountsType = ResultCounts::class;
  protected $resultCountsDataType = '';
  protected $resultsType = SearchResult::class;
  protected $resultsDataType = 'array';
  protected $spellResultsType = SpellResult::class;
  protected $spellResultsDataType = 'array';
  protected $structuredResultsType = StructuredResult::class;
  protected $structuredResultsDataType = 'array';

  /**
   * Debugging information about the response.
   *
   * @param ResponseDebugInfo $debugInfo
   */
  public function setDebugInfo(ResponseDebugInfo $debugInfo)
  {
    $this->debugInfo = $debugInfo;
  }
  /**
   * @return ResponseDebugInfo
   */
  public function getDebugInfo()
  {
    return $this->debugInfo;
  }
  /**
   * Error information about the response.
   *
   * @param ErrorInfo $errorInfo
   */
  public function setErrorInfo(ErrorInfo $errorInfo)
  {
    $this->errorInfo = $errorInfo;
  }
  /**
   * @return ErrorInfo
   */
  public function getErrorInfo()
  {
    return $this->errorInfo;
  }
  /**
   * Repeated facet results.
   *
   * @param FacetResult[] $facetResults
   */
  public function setFacetResults($facetResults)
  {
    $this->facetResults = $facetResults;
  }
  /**
   * @return FacetResult[]
   */
  public function getFacetResults()
  {
    return $this->facetResults;
  }
  /**
   * Whether there are more search results matching the query.
   *
   * @param bool $hasMoreResults
   */
  public function setHasMoreResults($hasMoreResults)
  {
    $this->hasMoreResults = $hasMoreResults;
  }
  /**
   * @return bool
   */
  public function getHasMoreResults()
  {
    return $this->hasMoreResults;
  }
  /**
   * Query interpretation result for user query. Empty if query interpretation
   * is disabled.
   *
   * @param QueryInterpretation $queryInterpretation
   */
  public function setQueryInterpretation(QueryInterpretation $queryInterpretation)
  {
    $this->queryInterpretation = $queryInterpretation;
  }
  /**
   * @return QueryInterpretation
   */
  public function getQueryInterpretation()
  {
    return $this->queryInterpretation;
  }
  /**
   * The estimated result count for this query.
   *
   * @param string $resultCountEstimate
   */
  public function setResultCountEstimate($resultCountEstimate)
  {
    $this->resultCountEstimate = $resultCountEstimate;
  }
  /**
   * @return string
   */
  public function getResultCountEstimate()
  {
    return $this->resultCountEstimate;
  }
  /**
   * The exact result count for this query.
   *
   * @param string $resultCountExact
   */
  public function setResultCountExact($resultCountExact)
  {
    $this->resultCountExact = $resultCountExact;
  }
  /**
   * @return string
   */
  public function getResultCountExact()
  {
    return $this->resultCountExact;
  }
  /**
   * Expanded result count information.
   *
   * @param ResultCounts $resultCounts
   */
  public function setResultCounts(ResultCounts $resultCounts)
  {
    $this->resultCounts = $resultCounts;
  }
  /**
   * @return ResultCounts
   */
  public function getResultCounts()
  {
    return $this->resultCounts;
  }
  /**
   * Results from a search query.
   *
   * @param SearchResult[] $results
   */
  public function setResults($results)
  {
    $this->results = $results;
  }
  /**
   * @return SearchResult[]
   */
  public function getResults()
  {
    return $this->results;
  }
  /**
   * Suggested spelling for the query.
   *
   * @param SpellResult[] $spellResults
   */
  public function setSpellResults($spellResults)
  {
    $this->spellResults = $spellResults;
  }
  /**
   * @return SpellResult[]
   */
  public function getSpellResults()
  {
    return $this->spellResults;
  }
  /**
   * Structured results for the user query. These results are not counted
   * against the page_size.
   *
   * @param StructuredResult[] $structuredResults
   */
  public function setStructuredResults($structuredResults)
  {
    $this->structuredResults = $structuredResults;
  }
  /**
   * @return StructuredResult[]
   */
  public function getStructuredResults()
  {
    return $this->structuredResults;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SearchResponse::class, 'Google_Service_CloudSearch_SearchResponse');
