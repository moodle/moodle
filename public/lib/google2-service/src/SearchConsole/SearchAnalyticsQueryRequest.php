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

namespace Google\Service\SearchConsole;

class SearchAnalyticsQueryRequest extends \Google\Collection
{
  public const AGGREGATION_TYPE_AUTO = 'AUTO';
  public const AGGREGATION_TYPE_BY_PROPERTY = 'BY_PROPERTY';
  public const AGGREGATION_TYPE_BY_PAGE = 'BY_PAGE';
  public const AGGREGATION_TYPE_BY_NEWS_SHOWCASE_PANEL = 'BY_NEWS_SHOWCASE_PANEL';
  /**
   * Default value, should not be used.
   */
  public const DATA_STATE_DATA_STATE_UNSPECIFIED = 'DATA_STATE_UNSPECIFIED';
  /**
   * Include full final data only, without partial.
   */
  public const DATA_STATE_FINAL = 'FINAL';
  /**
   * Include all data, full and partial.
   */
  public const DATA_STATE_ALL = 'ALL';
  /**
   * Include hourly data, full and partial. Required when grouping by HOUR.
   */
  public const DATA_STATE_HOURLY_ALL = 'HOURLY_ALL';
  public const SEARCH_TYPE_WEB = 'WEB';
  public const SEARCH_TYPE_IMAGE = 'IMAGE';
  public const SEARCH_TYPE_VIDEO = 'VIDEO';
  /**
   * News tab in search.
   */
  public const SEARCH_TYPE_NEWS = 'NEWS';
  /**
   * Discover.
   */
  public const SEARCH_TYPE_DISCOVER = 'DISCOVER';
  /**
   * Google News (news.google.com or mobile app).
   */
  public const SEARCH_TYPE_GOOGLE_NEWS = 'GOOGLE_NEWS';
  public const TYPE_WEB = 'WEB';
  public const TYPE_IMAGE = 'IMAGE';
  public const TYPE_VIDEO = 'VIDEO';
  /**
   * News tab in search.
   */
  public const TYPE_NEWS = 'NEWS';
  /**
   * Discover.
   */
  public const TYPE_DISCOVER = 'DISCOVER';
  /**
   * Google News (news.google.com or mobile app).
   */
  public const TYPE_GOOGLE_NEWS = 'GOOGLE_NEWS';
  protected $collection_key = 'dimensions';
  /**
   * [Optional; Default is \"auto\"] How data is aggregated. If aggregated by
   * property, all data for the same property is aggregated; if aggregated by
   * page, all data is aggregated by canonical URI. If you filter or group by
   * page, choose AUTO; otherwise you can aggregate either by property or by
   * page, depending on how you want your data calculated; see the help
   * documentation to learn how data is calculated differently by site versus by
   * page. **Note:** If you group or filter by page, you cannot aggregate by
   * property. If you specify any value other than AUTO, the aggregation type in
   * the result will match the requested type, or if you request an invalid
   * type, you will get an error. The API will never change your aggregation
   * type if the requested type is invalid.
   *
   * @var string
   */
  public $aggregationType;
  /**
   * The data state to be fetched, can be full or all, the latter including full
   * and partial data.
   *
   * @var string
   */
  public $dataState;
  protected $dimensionFilterGroupsType = ApiDimensionFilterGroup::class;
  protected $dimensionFilterGroupsDataType = 'array';
  /**
   * [Optional] Zero or more dimensions to group results by. Dimensions are the
   * group-by values in the Search Analytics page. Dimensions are combined to
   * create a unique row key for each row. Results are grouped in the order that
   * you supply these dimensions.
   *
   * @var string[]
   */
  public $dimensions;
  /**
   * [Required] End date of the requested date range, in YYYY-MM-DD format, in
   * PST (UTC - 8:00). Must be greater than or equal to the start date. This
   * value is included in the range.
   *
   * @var string
   */
  public $endDate;
  /**
   * [Optional; Default is 1000] The maximum number of rows to return. Must be a
   * number from 1 to 25,000 (inclusive).
   *
   * @var int
   */
  public $rowLimit;
  /**
   * [Optional; Default is \"web\"] The search type to filter for.
   *
   * @var string
   */
  public $searchType;
  /**
   * [Required] Start date of the requested date range, in YYYY-MM-DD format, in
   * PST time (UTC - 8:00). Must be less than or equal to the end date. This
   * value is included in the range.
   *
   * @var string
   */
  public $startDate;
  /**
   * [Optional; Default is 0] Zero-based index of the first row in the response.
   * Must be a non-negative number.
   *
   * @var int
   */
  public $startRow;
  /**
   * Optional. [Optional; Default is \"web\"] Type of report: search type, or
   * either Discover or Gnews.
   *
   * @var string
   */
  public $type;

  /**
   * [Optional; Default is \"auto\"] How data is aggregated. If aggregated by
   * property, all data for the same property is aggregated; if aggregated by
   * page, all data is aggregated by canonical URI. If you filter or group by
   * page, choose AUTO; otherwise you can aggregate either by property or by
   * page, depending on how you want your data calculated; see the help
   * documentation to learn how data is calculated differently by site versus by
   * page. **Note:** If you group or filter by page, you cannot aggregate by
   * property. If you specify any value other than AUTO, the aggregation type in
   * the result will match the requested type, or if you request an invalid
   * type, you will get an error. The API will never change your aggregation
   * type if the requested type is invalid.
   *
   * Accepted values: AUTO, BY_PROPERTY, BY_PAGE, BY_NEWS_SHOWCASE_PANEL
   *
   * @param self::AGGREGATION_TYPE_* $aggregationType
   */
  public function setAggregationType($aggregationType)
  {
    $this->aggregationType = $aggregationType;
  }
  /**
   * @return self::AGGREGATION_TYPE_*
   */
  public function getAggregationType()
  {
    return $this->aggregationType;
  }
  /**
   * The data state to be fetched, can be full or all, the latter including full
   * and partial data.
   *
   * Accepted values: DATA_STATE_UNSPECIFIED, FINAL, ALL, HOURLY_ALL
   *
   * @param self::DATA_STATE_* $dataState
   */
  public function setDataState($dataState)
  {
    $this->dataState = $dataState;
  }
  /**
   * @return self::DATA_STATE_*
   */
  public function getDataState()
  {
    return $this->dataState;
  }
  /**
   * [Optional] Zero or more filters to apply to the dimension grouping values;
   * for example, 'query contains \"buy\"' to see only data where the query
   * string contains the substring \"buy\" (not case-sensitive). You can filter
   * by a dimension without grouping by it.
   *
   * @param ApiDimensionFilterGroup[] $dimensionFilterGroups
   */
  public function setDimensionFilterGroups($dimensionFilterGroups)
  {
    $this->dimensionFilterGroups = $dimensionFilterGroups;
  }
  /**
   * @return ApiDimensionFilterGroup[]
   */
  public function getDimensionFilterGroups()
  {
    return $this->dimensionFilterGroups;
  }
  /**
   * [Optional] Zero or more dimensions to group results by. Dimensions are the
   * group-by values in the Search Analytics page. Dimensions are combined to
   * create a unique row key for each row. Results are grouped in the order that
   * you supply these dimensions.
   *
   * @param string[] $dimensions
   */
  public function setDimensions($dimensions)
  {
    $this->dimensions = $dimensions;
  }
  /**
   * @return string[]
   */
  public function getDimensions()
  {
    return $this->dimensions;
  }
  /**
   * [Required] End date of the requested date range, in YYYY-MM-DD format, in
   * PST (UTC - 8:00). Must be greater than or equal to the start date. This
   * value is included in the range.
   *
   * @param string $endDate
   */
  public function setEndDate($endDate)
  {
    $this->endDate = $endDate;
  }
  /**
   * @return string
   */
  public function getEndDate()
  {
    return $this->endDate;
  }
  /**
   * [Optional; Default is 1000] The maximum number of rows to return. Must be a
   * number from 1 to 25,000 (inclusive).
   *
   * @param int $rowLimit
   */
  public function setRowLimit($rowLimit)
  {
    $this->rowLimit = $rowLimit;
  }
  /**
   * @return int
   */
  public function getRowLimit()
  {
    return $this->rowLimit;
  }
  /**
   * [Optional; Default is \"web\"] The search type to filter for.
   *
   * Accepted values: WEB, IMAGE, VIDEO, NEWS, DISCOVER, GOOGLE_NEWS
   *
   * @param self::SEARCH_TYPE_* $searchType
   */
  public function setSearchType($searchType)
  {
    $this->searchType = $searchType;
  }
  /**
   * @return self::SEARCH_TYPE_*
   */
  public function getSearchType()
  {
    return $this->searchType;
  }
  /**
   * [Required] Start date of the requested date range, in YYYY-MM-DD format, in
   * PST time (UTC - 8:00). Must be less than or equal to the end date. This
   * value is included in the range.
   *
   * @param string $startDate
   */
  public function setStartDate($startDate)
  {
    $this->startDate = $startDate;
  }
  /**
   * @return string
   */
  public function getStartDate()
  {
    return $this->startDate;
  }
  /**
   * [Optional; Default is 0] Zero-based index of the first row in the response.
   * Must be a non-negative number.
   *
   * @param int $startRow
   */
  public function setStartRow($startRow)
  {
    $this->startRow = $startRow;
  }
  /**
   * @return int
   */
  public function getStartRow()
  {
    return $this->startRow;
  }
  /**
   * Optional. [Optional; Default is \"web\"] Type of report: search type, or
   * either Discover or Gnews.
   *
   * Accepted values: WEB, IMAGE, VIDEO, NEWS, DISCOVER, GOOGLE_NEWS
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SearchAnalyticsQueryRequest::class, 'Google_Service_SearchConsole_SearchAnalyticsQueryRequest');
