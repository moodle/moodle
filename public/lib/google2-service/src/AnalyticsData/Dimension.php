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

class Dimension extends \Google\Model
{
  protected $dimensionExpressionType = DimensionExpression::class;
  protected $dimensionExpressionDataType = '';
  /**
   * The name of the dimension. See the [API Dimensions](https://developers.goog
   * le.com/analytics/devguides/reporting/data/v1/api-schema#dimensions) for the
   * list of dimension names supported by core reporting methods such as
   * `runReport` and `batchRunReports`. See [Realtime Dimensions](https://develo
   * pers.google.com/analytics/devguides/reporting/data/v1/realtime-api-
   * schema#dimensions) for the list of dimension names supported by the
   * `runRealtimeReport` method. See [Funnel Dimensions](https://developers.goog
   * le.com/analytics/devguides/reporting/data/v1/exploration-api-
   * schema#dimensions) for the list of dimension names supported by the
   * `runFunnelReport` method. If `dimensionExpression` is specified, `name` can
   * be any string that you would like within the allowed character set. For
   * example if a `dimensionExpression` concatenates `country` and `city`, you
   * could call that dimension `countryAndCity`. Dimension names that you choose
   * must match the regular expression `^[a-zA-Z0-9_]$`. Dimensions are
   * referenced by `name` in `dimensionFilter`, `orderBys`,
   * `dimensionExpression`, and `pivots`.
   *
   * @var string
   */
  public $name;

  /**
   * One dimension can be the result of an expression of multiple dimensions.
   * For example, dimension "country, city": concatenate(country, ", ", city).
   *
   * @param DimensionExpression $dimensionExpression
   */
  public function setDimensionExpression(DimensionExpression $dimensionExpression)
  {
    $this->dimensionExpression = $dimensionExpression;
  }
  /**
   * @return DimensionExpression
   */
  public function getDimensionExpression()
  {
    return $this->dimensionExpression;
  }
  /**
   * The name of the dimension. See the [API Dimensions](https://developers.goog
   * le.com/analytics/devguides/reporting/data/v1/api-schema#dimensions) for the
   * list of dimension names supported by core reporting methods such as
   * `runReport` and `batchRunReports`. See [Realtime Dimensions](https://develo
   * pers.google.com/analytics/devguides/reporting/data/v1/realtime-api-
   * schema#dimensions) for the list of dimension names supported by the
   * `runRealtimeReport` method. See [Funnel Dimensions](https://developers.goog
   * le.com/analytics/devguides/reporting/data/v1/exploration-api-
   * schema#dimensions) for the list of dimension names supported by the
   * `runFunnelReport` method. If `dimensionExpression` is specified, `name` can
   * be any string that you would like within the allowed character set. For
   * example if a `dimensionExpression` concatenates `country` and `city`, you
   * could call that dimension `countryAndCity`. Dimension names that you choose
   * must match the regular expression `^[a-zA-Z0-9_]$`. Dimensions are
   * referenced by `name` in `dimensionFilter`, `orderBys`,
   * `dimensionExpression`, and `pivots`.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Dimension::class, 'Google_Service_AnalyticsData_Dimension');
