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

class GoogleCloudDiscoveryengineV1betaSearchResponse extends \Google\Collection
{
  protected $collection_key = 'results';
  /**
   * @var string[]
   */
  public $appliedControls;
  /**
   * @var string
   */
  public $attributionToken;
  /**
   * @var string
   */
  public $correctedQuery;
  protected $facetsType = GoogleCloudDiscoveryengineV1betaSearchResponseFacet::class;
  protected $facetsDataType = 'array';
  protected $geoSearchDebugInfoType = GoogleCloudDiscoveryengineV1betaSearchResponseGeoSearchDebugInfo::class;
  protected $geoSearchDebugInfoDataType = 'array';
  protected $guidedSearchResultType = GoogleCloudDiscoveryengineV1betaSearchResponseGuidedSearchResult::class;
  protected $guidedSearchResultDataType = '';
  /**
   * @var string
   */
  public $nextPageToken;
  protected $queryExpansionInfoType = GoogleCloudDiscoveryengineV1betaSearchResponseQueryExpansionInfo::class;
  protected $queryExpansionInfoDataType = '';
  /**
   * @var string
   */
  public $redirectUri;
  protected $resultsType = GoogleCloudDiscoveryengineV1betaSearchResponseSearchResult::class;
  protected $resultsDataType = 'array';
  protected $summaryType = GoogleCloudDiscoveryengineV1betaSearchResponseSummary::class;
  protected $summaryDataType = '';
  /**
   * @var int
   */
  public $totalSize;

  /**
   * @param string[]
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
   * @param string
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
   * @param string
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
   * @param GoogleCloudDiscoveryengineV1betaSearchResponseFacet[]
   */
  public function setFacets($facets)
  {
    $this->facets = $facets;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaSearchResponseFacet[]
   */
  public function getFacets()
  {
    return $this->facets;
  }
  /**
   * @param GoogleCloudDiscoveryengineV1betaSearchResponseGeoSearchDebugInfo[]
   */
  public function setGeoSearchDebugInfo($geoSearchDebugInfo)
  {
    $this->geoSearchDebugInfo = $geoSearchDebugInfo;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaSearchResponseGeoSearchDebugInfo[]
   */
  public function getGeoSearchDebugInfo()
  {
    return $this->geoSearchDebugInfo;
  }
  /**
   * @param GoogleCloudDiscoveryengineV1betaSearchResponseGuidedSearchResult
   */
  public function setGuidedSearchResult(GoogleCloudDiscoveryengineV1betaSearchResponseGuidedSearchResult $guidedSearchResult)
  {
    $this->guidedSearchResult = $guidedSearchResult;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaSearchResponseGuidedSearchResult
   */
  public function getGuidedSearchResult()
  {
    return $this->guidedSearchResult;
  }
  /**
   * @param string
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
   * @param GoogleCloudDiscoveryengineV1betaSearchResponseQueryExpansionInfo
   */
  public function setQueryExpansionInfo(GoogleCloudDiscoveryengineV1betaSearchResponseQueryExpansionInfo $queryExpansionInfo)
  {
    $this->queryExpansionInfo = $queryExpansionInfo;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaSearchResponseQueryExpansionInfo
   */
  public function getQueryExpansionInfo()
  {
    return $this->queryExpansionInfo;
  }
  /**
   * @param string
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
   * @param GoogleCloudDiscoveryengineV1betaSearchResponseSearchResult[]
   */
  public function setResults($results)
  {
    $this->results = $results;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaSearchResponseSearchResult[]
   */
  public function getResults()
  {
    return $this->results;
  }
  /**
   * @param GoogleCloudDiscoveryengineV1betaSearchResponseSummary
   */
  public function setSummary(GoogleCloudDiscoveryengineV1betaSearchResponseSummary $summary)
  {
    $this->summary = $summary;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaSearchResponseSummary
   */
  public function getSummary()
  {
    return $this->summary;
  }
  /**
   * @param int
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
class_alias(GoogleCloudDiscoveryengineV1betaSearchResponse::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1betaSearchResponse');
