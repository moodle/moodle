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

class GoogleCloudDiscoveryengineV1SearchResponse extends \Google\Collection
{
  /**
   * Default value. Should not be used.
   */
  public const SEMANTIC_STATE_SEMANTIC_STATE_UNSPECIFIED = 'SEMANTIC_STATE_UNSPECIFIED';
  /**
   * Semantic search was disabled for this search response.
   */
  public const SEMANTIC_STATE_DISABLED = 'DISABLED';
  /**
   * Semantic search was enabled for this search response.
   */
  public const SEMANTIC_STATE_ENABLED = 'ENABLED';
  protected $collection_key = 'searchLinkPromotions';
  /**
   * A unique search token. This should be included in the UserEvent logs
   * resulting from this search, which enables accurate attribution of search
   * model performance. This also helps to identify a request during the
   * customer support scenarios.
   *
   * @var string
   */
  public $attributionToken;
  /**
   * Contains the spell corrected query, if found. If the spell correction type
   * is AUTOMATIC, then the search results are based on corrected_query.
   * Otherwise the original query is used for search.
   *
   * @var string
   */
  public $correctedQuery;
  protected $facetsType = GoogleCloudDiscoveryengineV1SearchResponseFacet::class;
  protected $facetsDataType = 'array';
  /**
   * A token that can be sent as SearchRequest.page_token to retrieve the next
   * page. If this field is omitted, there are no subsequent pages.
   *
   * @var string
   */
  public $nextPageToken;
  protected $queryExpansionInfoType = GoogleCloudDiscoveryengineV1SearchResponseQueryExpansionInfo::class;
  protected $queryExpansionInfoDataType = '';
  /**
   * The URI of a customer-defined redirect page. If redirect action is
   * triggered, no search is performed, and only redirect_uri and
   * attribution_token are set in the response.
   *
   * @var string
   */
  public $redirectUri;
  protected $resultsType = GoogleCloudDiscoveryengineV1SearchResponseSearchResult::class;
  protected $resultsDataType = 'array';
  protected $searchLinkPromotionsType = GoogleCloudDiscoveryengineV1SearchLinkPromotion::class;
  protected $searchLinkPromotionsDataType = 'array';
  /**
   * Output only. Indicates the semantic state of the search response.
   *
   * @var string
   */
  public $semanticState;
  protected $sessionInfoType = GoogleCloudDiscoveryengineV1SearchResponseSessionInfo::class;
  protected $sessionInfoDataType = '';
  protected $summaryType = GoogleCloudDiscoveryengineV1SearchResponseSummary::class;
  protected $summaryDataType = '';
  /**
   * The estimated total count of matched items irrespective of pagination. The
   * count of results returned by pagination may be less than the total_size
   * that matches.
   *
   * @var int
   */
  public $totalSize;

  /**
   * A unique search token. This should be included in the UserEvent logs
   * resulting from this search, which enables accurate attribution of search
   * model performance. This also helps to identify a request during the
   * customer support scenarios.
   *
   * @param string $attributionToken
   */
  public function setAttributionToken($attributionToken)
  {
    $this->attributionToken = $attributionToken;
  }
  /**
   * @return string
   */
  public function getAttributionToken()
  {
    return $this->attributionToken;
  }
  /**
   * Contains the spell corrected query, if found. If the spell correction type
   * is AUTOMATIC, then the search results are based on corrected_query.
   * Otherwise the original query is used for search.
   *
   * @param string $correctedQuery
   */
  public function setCorrectedQuery($correctedQuery)
  {
    $this->correctedQuery = $correctedQuery;
  }
  /**
   * @return string
   */
  public function getCorrectedQuery()
  {
    return $this->correctedQuery;
  }
  /**
   * Results of facets requested by user.
   *
   * @param GoogleCloudDiscoveryengineV1SearchResponseFacet[] $facets
   */
  public function setFacets($facets)
  {
    $this->facets = $facets;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1SearchResponseFacet[]
   */
  public function getFacets()
  {
    return $this->facets;
  }
  /**
   * A token that can be sent as SearchRequest.page_token to retrieve the next
   * page. If this field is omitted, there are no subsequent pages.
   *
   * @param string $nextPageToken
   */
  public function setNextPageToken($nextPageToken)
  {
    $this->nextPageToken = $nextPageToken;
  }
  /**
   * @return string
   */
  public function getNextPageToken()
  {
    return $this->nextPageToken;
  }
  /**
   * Query expansion information for the returned results.
   *
   * @param GoogleCloudDiscoveryengineV1SearchResponseQueryExpansionInfo $queryExpansionInfo
   */
  public function setQueryExpansionInfo(GoogleCloudDiscoveryengineV1SearchResponseQueryExpansionInfo $queryExpansionInfo)
  {
    $this->queryExpansionInfo = $queryExpansionInfo;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1SearchResponseQueryExpansionInfo
   */
  public function getQueryExpansionInfo()
  {
    return $this->queryExpansionInfo;
  }
  /**
   * The URI of a customer-defined redirect page. If redirect action is
   * triggered, no search is performed, and only redirect_uri and
   * attribution_token are set in the response.
   *
   * @param string $redirectUri
   */
  public function setRedirectUri($redirectUri)
  {
    $this->redirectUri = $redirectUri;
  }
  /**
   * @return string
   */
  public function getRedirectUri()
  {
    return $this->redirectUri;
  }
  /**
   * A list of matched documents. The order represents the ranking.
   *
   * @param GoogleCloudDiscoveryengineV1SearchResponseSearchResult[] $results
   */
  public function setResults($results)
  {
    $this->results = $results;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1SearchResponseSearchResult[]
   */
  public function getResults()
  {
    return $this->results;
  }
  /**
   * Promotions for site search.
   *
   * @param GoogleCloudDiscoveryengineV1SearchLinkPromotion[] $searchLinkPromotions
   */
  public function setSearchLinkPromotions($searchLinkPromotions)
  {
    $this->searchLinkPromotions = $searchLinkPromotions;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1SearchLinkPromotion[]
   */
  public function getSearchLinkPromotions()
  {
    return $this->searchLinkPromotions;
  }
  /**
   * Output only. Indicates the semantic state of the search response.
   *
   * Accepted values: SEMANTIC_STATE_UNSPECIFIED, DISABLED, ENABLED
   *
   * @param self::SEMANTIC_STATE_* $semanticState
   */
  public function setSemanticState($semanticState)
  {
    $this->semanticState = $semanticState;
  }
  /**
   * @return self::SEMANTIC_STATE_*
   */
  public function getSemanticState()
  {
    return $this->semanticState;
  }
  /**
   * Session information. Only set if SearchRequest.session is provided. See its
   * description for more details.
   *
   * @param GoogleCloudDiscoveryengineV1SearchResponseSessionInfo $sessionInfo
   */
  public function setSessionInfo(GoogleCloudDiscoveryengineV1SearchResponseSessionInfo $sessionInfo)
  {
    $this->sessionInfo = $sessionInfo;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1SearchResponseSessionInfo
   */
  public function getSessionInfo()
  {
    return $this->sessionInfo;
  }
  /**
   * A summary as part of the search results. This field is only returned if
   * SearchRequest.ContentSearchSpec.summary_spec is set.
   *
   * @param GoogleCloudDiscoveryengineV1SearchResponseSummary $summary
   */
  public function setSummary(GoogleCloudDiscoveryengineV1SearchResponseSummary $summary)
  {
    $this->summary = $summary;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1SearchResponseSummary
   */
  public function getSummary()
  {
    return $this->summary;
  }
  /**
   * The estimated total count of matched items irrespective of pagination. The
   * count of results returned by pagination may be less than the total_size
   * that matches.
   *
   * @param int $totalSize
   */
  public function setTotalSize($totalSize)
  {
    $this->totalSize = $totalSize;
  }
  /**
   * @return int
   */
  public function getTotalSize()
  {
    return $this->totalSize;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1SearchResponse::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1SearchResponse');
