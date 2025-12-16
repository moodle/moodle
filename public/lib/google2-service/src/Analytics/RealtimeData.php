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

namespace Google\Service\Analytics;

class RealtimeData extends \Google\Collection
{
  protected $collection_key = 'rows';
  protected $columnHeadersType = RealtimeDataColumnHeaders::class;
  protected $columnHeadersDataType = 'array';
  /**
   * Unique ID for this data response.
   *
   * @var string
   */
  public $id;
  /**
   * Resource type.
   *
   * @var string
   */
  public $kind;
  protected $profileInfoType = RealtimeDataProfileInfo::class;
  protected $profileInfoDataType = '';
  protected $queryType = RealtimeDataQuery::class;
  protected $queryDataType = '';
  /**
   * Real time data rows, where each row contains a list of dimension values
   * followed by the metric values. The order of dimensions and metrics is same
   * as specified in the request.
   *
   * @var string[]
   */
  public $rows;
  /**
   * Link to this page.
   *
   * @var string
   */
  public $selfLink;
  /**
   * The total number of rows for the query, regardless of the number of rows in
   * the response.
   *
   * @var int
   */
  public $totalResults;
  /**
   * Total values for the requested metrics over all the results, not just the
   * results returned in this response. The order of the metric totals is same
   * as the metric order specified in the request.
   *
   * @var string[]
   */
  public $totalsForAllResults;

  /**
   * Column headers that list dimension names followed by the metric names. The
   * order of dimensions and metrics is same as specified in the request.
   *
   * @param RealtimeDataColumnHeaders[] $columnHeaders
   */
  public function setColumnHeaders($columnHeaders)
  {
    $this->columnHeaders = $columnHeaders;
  }
  /**
   * @return RealtimeDataColumnHeaders[]
   */
  public function getColumnHeaders()
  {
    return $this->columnHeaders;
  }
  /**
   * Unique ID for this data response.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Resource type.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * Information for the view (profile), for which the real time data was
   * requested.
   *
   * @param RealtimeDataProfileInfo $profileInfo
   */
  public function setProfileInfo(RealtimeDataProfileInfo $profileInfo)
  {
    $this->profileInfo = $profileInfo;
  }
  /**
   * @return RealtimeDataProfileInfo
   */
  public function getProfileInfo()
  {
    return $this->profileInfo;
  }
  /**
   * Real time data request query parameters.
   *
   * @param RealtimeDataQuery $query
   */
  public function setQuery(RealtimeDataQuery $query)
  {
    $this->query = $query;
  }
  /**
   * @return RealtimeDataQuery
   */
  public function getQuery()
  {
    return $this->query;
  }
  /**
   * Real time data rows, where each row contains a list of dimension values
   * followed by the metric values. The order of dimensions and metrics is same
   * as specified in the request.
   *
   * @param string[] $rows
   */
  public function setRows($rows)
  {
    $this->rows = $rows;
  }
  /**
   * @return string[]
   */
  public function getRows()
  {
    return $this->rows;
  }
  /**
   * Link to this page.
   *
   * @param string $selfLink
   */
  public function setSelfLink($selfLink)
  {
    $this->selfLink = $selfLink;
  }
  /**
   * @return string
   */
  public function getSelfLink()
  {
    return $this->selfLink;
  }
  /**
   * The total number of rows for the query, regardless of the number of rows in
   * the response.
   *
   * @param int $totalResults
   */
  public function setTotalResults($totalResults)
  {
    $this->totalResults = $totalResults;
  }
  /**
   * @return int
   */
  public function getTotalResults()
  {
    return $this->totalResults;
  }
  /**
   * Total values for the requested metrics over all the results, not just the
   * results returned in this response. The order of the metric totals is same
   * as the metric order specified in the request.
   *
   * @param string[] $totalsForAllResults
   */
  public function setTotalsForAllResults($totalsForAllResults)
  {
    $this->totalsForAllResults = $totalsForAllResults;
  }
  /**
   * @return string[]
   */
  public function getTotalsForAllResults()
  {
    return $this->totalsForAllResults;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RealtimeData::class, 'Google_Service_Analytics_RealtimeData');
