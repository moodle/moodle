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

namespace Google\Service\CloudRetail;

class GoogleCloudRetailV2SearchResponse extends \Google\Collection
{
  protected $collection_key = 'results';
  /**
   * The fully qualified resource name of applied
   * [controls](https://cloud.google.com/retail/docs/serving-control-rules).
   *
   * @var string[]
   */
  public $appliedControls;
  /**
   * A unique search token. This should be included in the UserEvent logs
   * resulting from this search, which enables accurate attribution of search
   * model performance.
   *
   * @var string
   */
  public $attributionToken;
  protected $conversationalSearchResultType = GoogleCloudRetailV2SearchResponseConversationalSearchResult::class;
  protected $conversationalSearchResultDataType = '';
  /**
   * Contains the spell corrected query, if found. If the spell correction type
   * is AUTOMATIC, then the search results are based on corrected_query.
   * Otherwise the original query is used for search.
   *
   * @var string
   */
  public $correctedQuery;
  protected $experimentInfoType = GoogleCloudRetailV2ExperimentInfo::class;
  protected $experimentInfoDataType = 'array';
  protected $facetsType = GoogleCloudRetailV2SearchResponseFacet::class;
  protected $facetsDataType = 'array';
  protected $invalidConditionBoostSpecsType = GoogleCloudRetailV2SearchRequestBoostSpecConditionBoostSpec::class;
  protected $invalidConditionBoostSpecsDataType = 'array';
  /**
   * A token that can be sent as SearchRequest.page_token to retrieve the next
   * page. If this field is omitted, there are no subsequent pages.
   *
   * @var string
   */
  public $nextPageToken;
  protected $pinControlMetadataType = GoogleCloudRetailV2PinControlMetadata::class;
  protected $pinControlMetadataDataType = '';
  protected $queryExpansionInfoType = GoogleCloudRetailV2SearchResponseQueryExpansionInfo::class;
  protected $queryExpansionInfoDataType = '';
  /**
   * The URI of a customer-defined redirect page. If redirect action is
   * triggered, no search is performed, and only redirect_uri and
   * attribution_token are set in the response.
   *
   * @var string
   */
  public $redirectUri;
  protected $resultsType = GoogleCloudRetailV2SearchResponseSearchResult::class;
  protected $resultsDataType = 'array';
  protected $tileNavigationResultType = GoogleCloudRetailV2SearchResponseTileNavigationResult::class;
  protected $tileNavigationResultDataType = '';
  /**
   * The estimated total count of matched items irrespective of pagination. The
   * count of results returned by pagination may be less than the total_size
   * that matches.
   *
   * @var int
   */
  public $totalSize;

  /**
   * The fully qualified resource name of applied
   * [controls](https://cloud.google.com/retail/docs/serving-control-rules).
   *
   * @param string[] $appliedControls
   */
  public function setAppliedControls($appliedControls)
  {
    $this->appliedControls = $appliedControls;
  }
  /**
   * @return string[]
   */
  public function getAppliedControls()
  {
    return $this->appliedControls;
  }
  /**
   * A unique search token. This should be included in the UserEvent logs
   * resulting from this search, which enables accurate attribution of search
   * model performance.
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
   * This field specifies all related information that is needed on client side
   * for UI rendering of conversational retail search.
   *
   * @param GoogleCloudRetailV2SearchResponseConversationalSearchResult $conversationalSearchResult
   */
  public function setConversationalSearchResult(GoogleCloudRetailV2SearchResponseConversationalSearchResult $conversationalSearchResult)
  {
    $this->conversationalSearchResult = $conversationalSearchResult;
  }
  /**
   * @return GoogleCloudRetailV2SearchResponseConversationalSearchResult
   */
  public function getConversationalSearchResult()
  {
    return $this->conversationalSearchResult;
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
   * Metadata related to A/B testing experiment associated with this response.
   * Only exists when an experiment is triggered.
   *
   * @param GoogleCloudRetailV2ExperimentInfo[] $experimentInfo
   */
  public function setExperimentInfo($experimentInfo)
  {
    $this->experimentInfo = $experimentInfo;
  }
  /**
   * @return GoogleCloudRetailV2ExperimentInfo[]
   */
  public function getExperimentInfo()
  {
    return $this->experimentInfo;
  }
  /**
   * Results of facets requested by user.
   *
   * @param GoogleCloudRetailV2SearchResponseFacet[] $facets
   */
  public function setFacets($facets)
  {
    $this->facets = $facets;
  }
  /**
   * @return GoogleCloudRetailV2SearchResponseFacet[]
   */
  public function getFacets()
  {
    return $this->facets;
  }
  /**
   * The invalid SearchRequest.BoostSpec.condition_boost_specs that are not
   * applied during serving.
   *
   * @param GoogleCloudRetailV2SearchRequestBoostSpecConditionBoostSpec[] $invalidConditionBoostSpecs
   */
  public function setInvalidConditionBoostSpecs($invalidConditionBoostSpecs)
  {
    $this->invalidConditionBoostSpecs = $invalidConditionBoostSpecs;
  }
  /**
   * @return GoogleCloudRetailV2SearchRequestBoostSpecConditionBoostSpec[]
   */
  public function getInvalidConditionBoostSpecs()
  {
    return $this->invalidConditionBoostSpecs;
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
   * Metadata for pin controls which were applicable to the request. This
   * contains two map fields, one for all matched pins and one for pins which
   * were matched but not applied. The two maps are keyed by pin position, and
   * the values are the product ids which were matched to that pin.
   *
   * @param GoogleCloudRetailV2PinControlMetadata $pinControlMetadata
   */
  public function setPinControlMetadata(GoogleCloudRetailV2PinControlMetadata $pinControlMetadata)
  {
    $this->pinControlMetadata = $pinControlMetadata;
  }
  /**
   * @return GoogleCloudRetailV2PinControlMetadata
   */
  public function getPinControlMetadata()
  {
    return $this->pinControlMetadata;
  }
  /**
   * Query expansion information for the returned results.
   *
   * @param GoogleCloudRetailV2SearchResponseQueryExpansionInfo $queryExpansionInfo
   */
  public function setQueryExpansionInfo(GoogleCloudRetailV2SearchResponseQueryExpansionInfo $queryExpansionInfo)
  {
    $this->queryExpansionInfo = $queryExpansionInfo;
  }
  /**
   * @return GoogleCloudRetailV2SearchResponseQueryExpansionInfo
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
   * A list of matched items. The order represents the ranking.
   *
   * @param GoogleCloudRetailV2SearchResponseSearchResult[] $results
   */
  public function setResults($results)
  {
    $this->results = $results;
  }
  /**
   * @return GoogleCloudRetailV2SearchResponseSearchResult[]
   */
  public function getResults()
  {
    return $this->results;
  }
  /**
   * This field specifies all related information for tile navigation that will
   * be used in client side.
   *
   * @param GoogleCloudRetailV2SearchResponseTileNavigationResult $tileNavigationResult
   */
  public function setTileNavigationResult(GoogleCloudRetailV2SearchResponseTileNavigationResult $tileNavigationResult)
  {
    $this->tileNavigationResult = $tileNavigationResult;
  }
  /**
   * @return GoogleCloudRetailV2SearchResponseTileNavigationResult
   */
  public function getTileNavigationResult()
  {
    return $this->tileNavigationResult;
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
class_alias(GoogleCloudRetailV2SearchResponse::class, 'Google_Service_CloudRetail_GoogleCloudRetailV2SearchResponse');
