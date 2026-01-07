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

namespace Google\Service\Bigquery;

class TableDataList extends \Google\Collection
{
  protected $collection_key = 'rows';
  /**
   * A hash of this page of results.
   *
   * @var string
   */
  public $etag;
  /**
   * The resource type of the response.
   *
   * @var string
   */
  public $kind;
  /**
   * A token used for paging results. Providing this token instead of the
   * startIndex parameter can help you retrieve stable results when an
   * underlying table is changing.
   *
   * @var string
   */
  public $pageToken;
  protected $rowsType = TableRow::class;
  protected $rowsDataType = 'array';
  /**
   * Total rows of the entire table. In order to show default value 0 we have to
   * present it as string.
   *
   * @var string
   */
  public $totalRows;

  /**
   * A hash of this page of results.
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * The resource type of the response.
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
   * A token used for paging results. Providing this token instead of the
   * startIndex parameter can help you retrieve stable results when an
   * underlying table is changing.
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
   * Rows of results.
   *
   * @param TableRow[] $rows
   */
  public function setRows($rows)
  {
    $this->rows = $rows;
  }
  /**
   * @return TableRow[]
   */
  public function getRows()
  {
    return $this->rows;
  }
  /**
   * Total rows of the entire table. In order to show default value 0 we have to
   * present it as string.
   *
   * @param string $totalRows
   */
  public function setTotalRows($totalRows)
  {
    $this->totalRows = $totalRows;
  }
  /**
   * @return string
   */
  public function getTotalRows()
  {
    return $this->totalRows;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TableDataList::class, 'Google_Service_Bigquery_TableDataList');
