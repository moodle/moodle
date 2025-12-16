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

class MetricMetadata extends \Google\Collection
{
  /**
   * Unspecified type.
   */
  public const TYPE_METRIC_TYPE_UNSPECIFIED = 'METRIC_TYPE_UNSPECIFIED';
  /**
   * Integer type.
   */
  public const TYPE_TYPE_INTEGER = 'TYPE_INTEGER';
  /**
   * Floating point type.
   */
  public const TYPE_TYPE_FLOAT = 'TYPE_FLOAT';
  /**
   * A duration of seconds; a special floating point type.
   */
  public const TYPE_TYPE_SECONDS = 'TYPE_SECONDS';
  /**
   * A duration in milliseconds; a special floating point type.
   */
  public const TYPE_TYPE_MILLISECONDS = 'TYPE_MILLISECONDS';
  /**
   * A duration in minutes; a special floating point type.
   */
  public const TYPE_TYPE_MINUTES = 'TYPE_MINUTES';
  /**
   * A duration in hours; a special floating point type.
   */
  public const TYPE_TYPE_HOURS = 'TYPE_HOURS';
  /**
   * A custom metric of standard type; a special floating point type.
   */
  public const TYPE_TYPE_STANDARD = 'TYPE_STANDARD';
  /**
   * An amount of money; a special floating point type.
   */
  public const TYPE_TYPE_CURRENCY = 'TYPE_CURRENCY';
  /**
   * A length in feet; a special floating point type.
   */
  public const TYPE_TYPE_FEET = 'TYPE_FEET';
  /**
   * A length in miles; a special floating point type.
   */
  public const TYPE_TYPE_MILES = 'TYPE_MILES';
  /**
   * A length in meters; a special floating point type.
   */
  public const TYPE_TYPE_METERS = 'TYPE_METERS';
  /**
   * A length in kilometers; a special floating point type.
   */
  public const TYPE_TYPE_KILOMETERS = 'TYPE_KILOMETERS';
  protected $collection_key = 'deprecatedApiNames';
  /**
   * A metric name. Useable in [Metric](#Metric)'s `name`. For example,
   * `eventCount`.
   *
   * @var string
   */
  public $apiName;
  /**
   * If reasons are specified, your access is blocked to this metric for this
   * property. API requests from you to this property for this metric will
   * succeed; however, the report will contain only zeros for this metric. API
   * requests with metric filters on blocked metrics will fail. If reasons are
   * empty, you have access to this metric. To learn more, see [Access and data-
   * restriction
   * management](https://support.google.com/analytics/answer/10851388).
   *
   * @var string[]
   */
  public $blockedReasons;
  /**
   * The display name of the category that this metrics belongs to. Similar
   * dimensions and metrics are categorized together.
   *
   * @var string
   */
  public $category;
  /**
   * True if the metric is a custom metric for this property.
   *
   * @var bool
   */
  public $customDefinition;
  /**
   * Still usable but deprecated names for this metric. If populated, this
   * metric is available by either `apiName` or one of `deprecatedApiNames` for
   * a period of time. After the deprecation period, the metric will be
   * available only by `apiName`.
   *
   * @var string[]
   */
  public $deprecatedApiNames;
  /**
   * Description of how this metric is used and calculated.
   *
   * @var string
   */
  public $description;
  /**
   * The mathematical expression for this derived metric. Can be used in
   * [Metric](#Metric)'s `expression` field for equivalent reports. Most metrics
   * are not expressions, and for non-expressions, this field is empty.
   *
   * @var string
   */
  public $expression;
  /**
   * The type of this metric.
   *
   * @var string
   */
  public $type;
  /**
   * This metric's name within the Google Analytics user interface. For example,
   * `Event count`.
   *
   * @var string
   */
  public $uiName;

  /**
   * A metric name. Useable in [Metric](#Metric)'s `name`. For example,
   * `eventCount`.
   *
   * @param string $apiName
   */
  public function setApiName($apiName)
  {
    $this->apiName = $apiName;
  }
  /**
   * @return string
   */
  public function getApiName()
  {
    return $this->apiName;
  }
  /**
   * If reasons are specified, your access is blocked to this metric for this
   * property. API requests from you to this property for this metric will
   * succeed; however, the report will contain only zeros for this metric. API
   * requests with metric filters on blocked metrics will fail. If reasons are
   * empty, you have access to this metric. To learn more, see [Access and data-
   * restriction
   * management](https://support.google.com/analytics/answer/10851388).
   *
   * @param string[] $blockedReasons
   */
  public function setBlockedReasons($blockedReasons)
  {
    $this->blockedReasons = $blockedReasons;
  }
  /**
   * @return string[]
   */
  public function getBlockedReasons()
  {
    return $this->blockedReasons;
  }
  /**
   * The display name of the category that this metrics belongs to. Similar
   * dimensions and metrics are categorized together.
   *
   * @param string $category
   */
  public function setCategory($category)
  {
    $this->category = $category;
  }
  /**
   * @return string
   */
  public function getCategory()
  {
    return $this->category;
  }
  /**
   * True if the metric is a custom metric for this property.
   *
   * @param bool $customDefinition
   */
  public function setCustomDefinition($customDefinition)
  {
    $this->customDefinition = $customDefinition;
  }
  /**
   * @return bool
   */
  public function getCustomDefinition()
  {
    return $this->customDefinition;
  }
  /**
   * Still usable but deprecated names for this metric. If populated, this
   * metric is available by either `apiName` or one of `deprecatedApiNames` for
   * a period of time. After the deprecation period, the metric will be
   * available only by `apiName`.
   *
   * @param string[] $deprecatedApiNames
   */
  public function setDeprecatedApiNames($deprecatedApiNames)
  {
    $this->deprecatedApiNames = $deprecatedApiNames;
  }
  /**
   * @return string[]
   */
  public function getDeprecatedApiNames()
  {
    return $this->deprecatedApiNames;
  }
  /**
   * Description of how this metric is used and calculated.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * The mathematical expression for this derived metric. Can be used in
   * [Metric](#Metric)'s `expression` field for equivalent reports. Most metrics
   * are not expressions, and for non-expressions, this field is empty.
   *
   * @param string $expression
   */
  public function setExpression($expression)
  {
    $this->expression = $expression;
  }
  /**
   * @return string
   */
  public function getExpression()
  {
    return $this->expression;
  }
  /**
   * The type of this metric.
   *
   * Accepted values: METRIC_TYPE_UNSPECIFIED, TYPE_INTEGER, TYPE_FLOAT,
   * TYPE_SECONDS, TYPE_MILLISECONDS, TYPE_MINUTES, TYPE_HOURS, TYPE_STANDARD,
   * TYPE_CURRENCY, TYPE_FEET, TYPE_MILES, TYPE_METERS, TYPE_KILOMETERS
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
  /**
   * This metric's name within the Google Analytics user interface. For example,
   * `Event count`.
   *
   * @param string $uiName
   */
  public function setUiName($uiName)
  {
    $this->uiName = $uiName;
  }
  /**
   * @return string
   */
  public function getUiName()
  {
    return $this->uiName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MetricMetadata::class, 'Google_Service_AnalyticsData_MetricMetadata');
