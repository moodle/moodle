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

namespace Google\Service\Dfareporting;

class DimensionValueRequest extends \Google\Collection
{
  protected $collection_key = 'filters';
  /**
   * The name of the dimension for which values should be requested.
   *
   * @var string
   */
  public $dimensionName;
  /**
   * @var string
   */
  public $endDate;
  protected $filtersType = DimensionFilter::class;
  protected $filtersDataType = 'array';
  /**
   * The kind of request this is, in this case
   * dfareporting#dimensionValueRequest .
   *
   * @var string
   */
  public $kind;
  /**
   * @var string
   */
  public $startDate;

  /**
   * The name of the dimension for which values should be requested.
   *
   * @param string $dimensionName
   */
  public function setDimensionName($dimensionName)
  {
    $this->dimensionName = $dimensionName;
  }
  /**
   * @return string
   */
  public function getDimensionName()
  {
    return $this->dimensionName;
  }
  /**
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
   * The list of filters by which to filter values. The filters are ANDed.
   *
   * @param DimensionFilter[] $filters
   */
  public function setFilters($filters)
  {
    $this->filters = $filters;
  }
  /**
   * @return DimensionFilter[]
   */
  public function getFilters()
  {
    return $this->filters;
  }
  /**
   * The kind of request this is, in this case
   * dfareporting#dimensionValueRequest .
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DimensionValueRequest::class, 'Google_Service_Dfareporting_DimensionValueRequest');
