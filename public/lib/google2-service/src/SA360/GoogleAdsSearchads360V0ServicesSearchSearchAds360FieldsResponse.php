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

namespace Google\Service\SA360;

class GoogleAdsSearchads360V0ServicesSearchSearchAds360FieldsResponse extends \Google\Collection
{
  protected $collection_key = 'results';
  /**
   * Pagination token used to retrieve the next page of results. Pass the
   * content of this string as the `page_token` attribute of the next request.
   * `next_page_token` is not returned for the last page.
   *
   * @var string
   */
  public $nextPageToken;
  protected $resultsType = GoogleAdsSearchads360V0ResourcesSearchAds360Field::class;
  protected $resultsDataType = 'array';
  /**
   * Total number of results that match the query ignoring the LIMIT clause.
   *
   * @var string
   */
  public $totalResultsCount;

  /**
   * Pagination token used to retrieve the next page of results. Pass the
   * content of this string as the `page_token` attribute of the next request.
   * `next_page_token` is not returned for the last page.
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
   * The list of fields that matched the query.
   *
   * @param GoogleAdsSearchads360V0ResourcesSearchAds360Field[] $results
   */
  public function setResults($results)
  {
    $this->results = $results;
  }
  /**
   * @return GoogleAdsSearchads360V0ResourcesSearchAds360Field[]
   */
  public function getResults()
  {
    return $this->results;
  }
  /**
   * Total number of results that match the query ignoring the LIMIT clause.
   *
   * @param string $totalResultsCount
   */
  public function setTotalResultsCount($totalResultsCount)
  {
    $this->totalResultsCount = $totalResultsCount;
  }
  /**
   * @return string
   */
  public function getTotalResultsCount()
  {
    return $this->totalResultsCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAdsSearchads360V0ServicesSearchSearchAds360FieldsResponse::class, 'Google_Service_SA360_GoogleAdsSearchads360V0ServicesSearchSearchAds360FieldsResponse');
