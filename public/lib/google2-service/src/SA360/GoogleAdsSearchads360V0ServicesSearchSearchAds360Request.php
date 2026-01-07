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

class GoogleAdsSearchads360V0ServicesSearchSearchAds360Request extends \Google\Model
{
  /**
   * Not specified.
   */
  public const SUMMARY_ROW_SETTING_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * Represent unknown values of return summary row.
   */
  public const SUMMARY_ROW_SETTING_UNKNOWN = 'UNKNOWN';
  /**
   * Do not return summary row.
   */
  public const SUMMARY_ROW_SETTING_NO_SUMMARY_ROW = 'NO_SUMMARY_ROW';
  /**
   * Return summary row along with results. The summary row will be returned in
   * the last batch alone (last batch will contain no results).
   */
  public const SUMMARY_ROW_SETTING_SUMMARY_ROW_WITH_RESULTS = 'SUMMARY_ROW_WITH_RESULTS';
  /**
   * Return summary row only and return no results.
   */
  public const SUMMARY_ROW_SETTING_SUMMARY_ROW_ONLY = 'SUMMARY_ROW_ONLY';
  /**
   * Number of elements to retrieve in a single page. When too large a page is
   * requested, the server may decide to further limit the number of returned
   * resources.
   *
   * @var int
   */
  public $pageSize;
  /**
   * Token of the page to retrieve. If not specified, the first page of results
   * will be returned. Use the value obtained from `next_page_token` in the
   * previous response in order to request the next page of results.
   *
   * @var string
   */
  public $pageToken;
  /**
   * Required. The query string.
   *
   * @var string
   */
  public $query;
  /**
   * If true, the total number of results that match the query ignoring the
   * LIMIT clause will be included in the response. Default is false.
   *
   * @var bool
   */
  public $returnTotalResultsCount;
  /**
   * Determines whether a summary row will be returned. By default, summary row
   * is not returned. If requested, the summary row will be sent in a response
   * by itself after all other query results are returned.
   *
   * @var string
   */
  public $summaryRowSetting;
  /**
   * If true, the request is validated but not executed.
   *
   * @var bool
   */
  public $validateOnly;

  /**
   * Number of elements to retrieve in a single page. When too large a page is
   * requested, the server may decide to further limit the number of returned
   * resources.
   *
   * @param int $pageSize
   */
  public function setPageSize($pageSize)
  {
    $this->pageSize = $pageSize;
  }
  /**
   * @return int
   */
  public function getPageSize()
  {
    return $this->pageSize;
  }
  /**
   * Token of the page to retrieve. If not specified, the first page of results
   * will be returned. Use the value obtained from `next_page_token` in the
   * previous response in order to request the next page of results.
   *
   * @param string $pageToken
   */
  public function setPageToken($pageToken)
  {
    $this->pageToken = $pageToken;
  }
  /**
   * @return string
   */
  public function getPageToken()
  {
    return $this->pageToken;
  }
  /**
   * Required. The query string.
   *
   * @param string $query
   */
  public function setQuery($query)
  {
    $this->query = $query;
  }
  /**
   * @return string
   */
  public function getQuery()
  {
    return $this->query;
  }
  /**
   * If true, the total number of results that match the query ignoring the
   * LIMIT clause will be included in the response. Default is false.
   *
   * @param bool $returnTotalResultsCount
   */
  public function setReturnTotalResultsCount($returnTotalResultsCount)
  {
    $this->returnTotalResultsCount = $returnTotalResultsCount;
  }
  /**
   * @return bool
   */
  public function getReturnTotalResultsCount()
  {
    return $this->returnTotalResultsCount;
  }
  /**
   * Determines whether a summary row will be returned. By default, summary row
   * is not returned. If requested, the summary row will be sent in a response
   * by itself after all other query results are returned.
   *
   * Accepted values: UNSPECIFIED, UNKNOWN, NO_SUMMARY_ROW,
   * SUMMARY_ROW_WITH_RESULTS, SUMMARY_ROW_ONLY
   *
   * @param self::SUMMARY_ROW_SETTING_* $summaryRowSetting
   */
  public function setSummaryRowSetting($summaryRowSetting)
  {
    $this->summaryRowSetting = $summaryRowSetting;
  }
  /**
   * @return self::SUMMARY_ROW_SETTING_*
   */
  public function getSummaryRowSetting()
  {
    return $this->summaryRowSetting;
  }
  /**
   * If true, the request is validated but not executed.
   *
   * @param bool $validateOnly
   */
  public function setValidateOnly($validateOnly)
  {
    $this->validateOnly = $validateOnly;
  }
  /**
   * @return bool
   */
  public function getValidateOnly()
  {
    return $this->validateOnly;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAdsSearchads360V0ServicesSearchSearchAds360Request::class, 'Google_Service_SA360_GoogleAdsSearchads360V0ServicesSearchSearchAds360Request');
