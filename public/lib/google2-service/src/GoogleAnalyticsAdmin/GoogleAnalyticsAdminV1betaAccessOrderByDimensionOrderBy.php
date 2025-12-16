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

namespace Google\Service\GoogleAnalyticsAdmin;

class GoogleAnalyticsAdminV1betaAccessOrderByDimensionOrderBy extends \Google\Model
{
  /**
   * Unspecified.
   */
  public const ORDER_TYPE_ORDER_TYPE_UNSPECIFIED = 'ORDER_TYPE_UNSPECIFIED';
  /**
   * Alphanumeric sort by Unicode code point. For example, "2" < "A" < "X" < "b"
   * < "z".
   */
  public const ORDER_TYPE_ALPHANUMERIC = 'ALPHANUMERIC';
  /**
   * Case insensitive alphanumeric sort by lower case Unicode code point. For
   * example, "2" < "A" < "b" < "X" < "z".
   */
  public const ORDER_TYPE_CASE_INSENSITIVE_ALPHANUMERIC = 'CASE_INSENSITIVE_ALPHANUMERIC';
  /**
   * Dimension values are converted to numbers before sorting. For example in
   * NUMERIC sort, "25" < "100", and in `ALPHANUMERIC` sort, "100" < "25". Non-
   * numeric dimension values all have equal ordering value below all numeric
   * values.
   */
  public const ORDER_TYPE_NUMERIC = 'NUMERIC';
  /**
   * A dimension name in the request to order by.
   *
   * @var string
   */
  public $dimensionName;
  /**
   * Controls the rule for dimension value ordering.
   *
   * @var string
   */
  public $orderType;

  /**
   * A dimension name in the request to order by.
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
   * Controls the rule for dimension value ordering.
   *
   * Accepted values: ORDER_TYPE_UNSPECIFIED, ALPHANUMERIC,
   * CASE_INSENSITIVE_ALPHANUMERIC, NUMERIC
   *
   * @param self::ORDER_TYPE_* $orderType
   */
  public function setOrderType($orderType)
  {
    $this->orderType = $orderType;
  }
  /**
   * @return self::ORDER_TYPE_*
   */
  public function getOrderType()
  {
    return $this->orderType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAnalyticsAdminV1betaAccessOrderByDimensionOrderBy::class, 'Google_Service_GoogleAnalyticsAdmin_GoogleAnalyticsAdminV1betaAccessOrderByDimensionOrderBy');
