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

namespace Google\Service\AnalyticsData;

class PivotHeader extends \Google\Collection
{
  protected $collection_key = 'pivotDimensionHeaders';
  protected $pivotDimensionHeadersType = PivotDimensionHeader::class;
  protected $pivotDimensionHeadersDataType = 'array';
  /**
   * The cardinality of the pivot. The total number of rows for this pivot's
   * fields regardless of how the parameters `offset` and `limit` are specified
   * in the request.
   *
   * @var int
   */
  public $rowCount;

  /**
   * The size is the same as the cardinality of the corresponding dimension
   * combinations.
   *
   * @param PivotDimensionHeader[] $pivotDimensionHeaders
   */
  public function setPivotDimensionHeaders($pivotDimensionHeaders)
  {
    $this->pivotDimensionHeaders = $pivotDimensionHeaders;
  }
  /**
   * @return PivotDimensionHeader[]
   */
  public function getPivotDimensionHeaders()
  {
    return $this->pivotDimensionHeaders;
  }
  /**
   * The cardinality of the pivot. The total number of rows for this pivot's
   * fields regardless of how the parameters `offset` and `limit` are specified
   * in the request.
   *
   * @param int $rowCount
   */
  public function setRowCount($rowCount)
  {
    $this->rowCount = $rowCount;
  }
  /**
   * @return int
   */
  public function getRowCount()
  {
    return $this->rowCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PivotHeader::class, 'Google_Service_AnalyticsData_PivotHeader');
