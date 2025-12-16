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

class McfData extends \Google\Collection
{
  protected $collection_key = 'rows';
  protected $columnHeadersType = McfDataColumnHeaders::class;
  protected $columnHeadersDataType = 'array';
  /**
   * Determines if the Analytics data contains sampled data.
   *
   * @var bool
   */
  public $containsSampledData;
  /**
   * Unique ID for this data response.
   *
   * @var string
   */
  public $id;
  /**
   * The maximum number of rows the response can contain, regardless of the
   * actual number of rows returned. Its value ranges from 1 to 10,000 with a
   * value of 1000 by default, or otherwise specified by the max-results query
   * parameter.
   *
   * @var int
   */
  public $itemsPerPage;
  /**
   * Resource type.
   *
   * @var string
   */
  public $kind;
  /**
   * Link to next page for this Analytics data query.
   *
   * @var string
   */
  public $nextLink;
  /**
   * Link to previous page for this Analytics data query.
   *
   * @var string
   */
  public $previousLink;
  protected $profileInfoType = McfDataProfileInfo::class;
  protected $profileInfoDataType = '';
  protected $queryType = McfDataQuery::class;
  protected $queryDataType = '';
  protected $rowsType = McfDataRows::class;
  protected $rowsDataType = 'array';
  /**
   * The number of samples used to calculate the result.
   *
   * @var string
   */
  public $sampleSize;
  /**
   * Total size of the sample space from which the samples were selected.
   *
   * @var string
   */
  public $sampleSpace;
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
   * @param McfDataColumnHeaders[] $columnHeaders
   */
  public function setColumnHeaders($columnHeaders)
  {
    $this->columnHeaders = $columnHeaders;
  }
  /**
   * @return McfDataColumnHeaders[]
   */
  public function getColumnHeaders()
  {
    return $this->columnHeaders;
  }
  /**
   * Determines if the Analytics data contains sampled data.
   *
   * @param bool $containsSampledData
   */
  public function setContainsSampledData($containsSampledData)
  {
    $this->containsSampledData = $containsSampledData;
  }
  /**
   * @return bool
   */
  public function getContainsSampledData()
  {
    return $this->containsSampledData;
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
   * The maximum number of rows the response can contain, regardless of the
   * actual number of rows returned. Its value ranges from 1 to 10,000 with a
   * value of 1000 by default, or otherwise specified by the max-results query
   * parameter.
   *
   * @param int $itemsPerPage
   */
  public function setItemsPerPage($itemsPerPage)
  {
    $this->itemsPerPage = $itemsPerPage;
  }
  /**
   * @return int
   */
  public function getItemsPerPage()
  {
    return $this->itemsPerPage;
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
   * Link to next page for this Analytics data query.
   *
   * @param string $nextLink
   */
  public function setNextLink($nextLink)
  {
    $this->nextLink = $nextLink;
  }
  /**
   * @return string
   */
  public function getNextLink()
  {
    return $this->nextLink;
  }
  /**
   * Link to previous page for this Analytics data query.
   *
   * @param string $previousLink
   */
  public function setPreviousLink($previousLink)
  {
    $this->previousLink = $previousLink;
  }
  /**
   * @return string
   */
  public function getPreviousLink()
  {
    return $this->previousLink;
  }
  /**
   * Information for the view (profile), for which the Analytics data was
   * requested.
   *
   * @param McfDataProfileInfo $profileInfo
   */
  public function setProfileInfo(McfDataProfileInfo $profileInfo)
  {
    $this->profileInfo = $profileInfo;
  }
  /**
   * @return McfDataProfileInfo
   */
  public function getProfileInfo()
  {
    return $this->profileInfo;
  }
  /**
   * Analytics data request query parameters.
   *
   * @param McfDataQuery $query
   */
  public function setQuery(McfDataQuery $query)
  {
    $this->query = $query;
  }
  /**
   * @return McfDataQuery
   */
  public function getQuery()
  {
    return $this->query;
  }
  /**
   * Analytics data rows, where each row contains a list of dimension values
   * followed by the metric values. The order of dimensions and metrics is same
   * as specified in the request.
   *
   * @param McfDataRows[] $rows
   */
  public function setRows($rows)
  {
    $this->rows = $rows;
  }
  /**
   * @return McfDataRows[]
   */
  public function getRows()
  {
    return $this->rows;
  }
  /**
   * The number of samples used to calculate the result.
   *
   * @param string $sampleSize
   */
  public function setSampleSize($sampleSize)
  {
    $this->sampleSize = $sampleSize;
  }
  /**
   * @return string
   */
  public function getSampleSize()
  {
    return $this->sampleSize;
  }
  /**
   * Total size of the sample space from which the samples were selected.
   *
   * @param string $sampleSpace
   */
  public function setSampleSpace($sampleSpace)
  {
    $this->sampleSpace = $sampleSpace;
  }
  /**
   * @return string
   */
  public function getSampleSpace()
  {
    return $this->sampleSpace;
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
class_alias(McfData::class, 'Google_Service_Analytics_McfData');
