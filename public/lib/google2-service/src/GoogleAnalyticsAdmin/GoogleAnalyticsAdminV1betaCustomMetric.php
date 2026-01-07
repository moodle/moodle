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

class GoogleAnalyticsAdminV1betaCustomMetric extends \Google\Collection
{
  /**
   * MeasurementUnit unspecified or missing.
   */
  public const MEASUREMENT_UNIT_MEASUREMENT_UNIT_UNSPECIFIED = 'MEASUREMENT_UNIT_UNSPECIFIED';
  /**
   * This metric uses default units.
   */
  public const MEASUREMENT_UNIT_STANDARD = 'STANDARD';
  /**
   * This metric measures a currency.
   */
  public const MEASUREMENT_UNIT_CURRENCY = 'CURRENCY';
  /**
   * This metric measures feet.
   */
  public const MEASUREMENT_UNIT_FEET = 'FEET';
  /**
   * This metric measures meters.
   */
  public const MEASUREMENT_UNIT_METERS = 'METERS';
  /**
   * This metric measures kilometers.
   */
  public const MEASUREMENT_UNIT_KILOMETERS = 'KILOMETERS';
  /**
   * This metric measures miles.
   */
  public const MEASUREMENT_UNIT_MILES = 'MILES';
  /**
   * This metric measures milliseconds.
   */
  public const MEASUREMENT_UNIT_MILLISECONDS = 'MILLISECONDS';
  /**
   * This metric measures seconds.
   */
  public const MEASUREMENT_UNIT_SECONDS = 'SECONDS';
  /**
   * This metric measures minutes.
   */
  public const MEASUREMENT_UNIT_MINUTES = 'MINUTES';
  /**
   * This metric measures hours.
   */
  public const MEASUREMENT_UNIT_HOURS = 'HOURS';
  /**
   * Scope unknown or not specified.
   */
  public const SCOPE_METRIC_SCOPE_UNSPECIFIED = 'METRIC_SCOPE_UNSPECIFIED';
  /**
   * Metric scoped to an event.
   */
  public const SCOPE_EVENT = 'EVENT';
  protected $collection_key = 'restrictedMetricType';
  /**
   * Optional. Description for this custom dimension. Max length of 150
   * characters.
   *
   * @var string
   */
  public $description;
  /**
   * Required. Display name for this custom metric as shown in the Analytics UI.
   * Max length of 82 characters, alphanumeric plus space and underscore
   * starting with a letter. Legacy system-generated display names may contain
   * square brackets, but updates to this field will never permit square
   * brackets.
   *
   * @var string
   */
  public $displayName;
  /**
   * Required. The type for the custom metric's value.
   *
   * @var string
   */
  public $measurementUnit;
  /**
   * Output only. Resource name for this CustomMetric resource. Format:
   * properties/{property}/customMetrics/{customMetric}
   *
   * @var string
   */
  public $name;
  /**
   * Required. Immutable. Tagging name for this custom metric. If this is an
   * event-scoped metric, then this is the event parameter name. May only
   * contain alphanumeric and underscore charactes, starting with a letter. Max
   * length of 40 characters for event-scoped metrics.
   *
   * @var string
   */
  public $parameterName;
  /**
   * Optional. Types of restricted data that this metric may contain. Required
   * for metrics with CURRENCY measurement unit. Must be empty for metrics with
   * a non-CURRENCY measurement unit.
   *
   * @var string[]
   */
  public $restrictedMetricType;
  /**
   * Required. Immutable. The scope of this custom metric.
   *
   * @var string
   */
  public $scope;

  /**
   * Optional. Description for this custom dimension. Max length of 150
   * characters.
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
   * Required. Display name for this custom metric as shown in the Analytics UI.
   * Max length of 82 characters, alphanumeric plus space and underscore
   * starting with a letter. Legacy system-generated display names may contain
   * square brackets, but updates to this field will never permit square
   * brackets.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Required. The type for the custom metric's value.
   *
   * Accepted values: MEASUREMENT_UNIT_UNSPECIFIED, STANDARD, CURRENCY, FEET,
   * METERS, KILOMETERS, MILES, MILLISECONDS, SECONDS, MINUTES, HOURS
   *
   * @param self::MEASUREMENT_UNIT_* $measurementUnit
   */
  public function setMeasurementUnit($measurementUnit)
  {
    $this->measurementUnit = $measurementUnit;
  }
  /**
   * @return self::MEASUREMENT_UNIT_*
   */
  public function getMeasurementUnit()
  {
    return $this->measurementUnit;
  }
  /**
   * Output only. Resource name for this CustomMetric resource. Format:
   * properties/{property}/customMetrics/{customMetric}
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
  /**
   * Required. Immutable. Tagging name for this custom metric. If this is an
   * event-scoped metric, then this is the event parameter name. May only
   * contain alphanumeric and underscore charactes, starting with a letter. Max
   * length of 40 characters for event-scoped metrics.
   *
   * @param string $parameterName
   */
  public function setParameterName($parameterName)
  {
    $this->parameterName = $parameterName;
  }
  /**
   * @return string
   */
  public function getParameterName()
  {
    return $this->parameterName;
  }
  /**
   * Optional. Types of restricted data that this metric may contain. Required
   * for metrics with CURRENCY measurement unit. Must be empty for metrics with
   * a non-CURRENCY measurement unit.
   *
   * @param string[] $restrictedMetricType
   */
  public function setRestrictedMetricType($restrictedMetricType)
  {
    $this->restrictedMetricType = $restrictedMetricType;
  }
  /**
   * @return string[]
   */
  public function getRestrictedMetricType()
  {
    return $this->restrictedMetricType;
  }
  /**
   * Required. Immutable. The scope of this custom metric.
   *
   * Accepted values: METRIC_SCOPE_UNSPECIFIED, EVENT
   *
   * @param self::SCOPE_* $scope
   */
  public function setScope($scope)
  {
    $this->scope = $scope;
  }
  /**
   * @return self::SCOPE_*
   */
  public function getScope()
  {
    return $this->scope;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAnalyticsAdminV1betaCustomMetric::class, 'Google_Service_GoogleAnalyticsAdmin_GoogleAnalyticsAdminV1betaCustomMetric');
