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

namespace Google\Service\AdMob;

class MediationReportSpecSortCondition extends \Google\Model
{
  /**
   * Default value for an unset field. Do not use.
   */
  public const DIMENSION_DIMENSION_UNSPECIFIED = 'DIMENSION_UNSPECIFIED';
  /**
   * A date in the YYYYMMDD format (for example, "20210701"). Requests can
   * specify at most one time dimension.
   */
  public const DIMENSION_DATE = 'DATE';
  /**
   * A month in the YYYYMM format (for example, "202107"). Requests can specify
   * at most one time dimension.
   */
  public const DIMENSION_MONTH = 'MONTH';
  /**
   * The date of the first day of a week in the YYYYMMDD format (for example,
   * "20210701"). Requests can specify at most one time dimension.
   */
  public const DIMENSION_WEEK = 'WEEK';
  /**
   * The [unique ID of the ad source](/admob/api/v1/ad_sources) (for example,
   * "5450213213286189855" and "AdMob Network" as label value).
   */
  public const DIMENSION_AD_SOURCE = 'AD_SOURCE';
  /**
   * The unique ID of the ad source instance (for example, "ca-app-
   * pub-1234:asi:5678" and "AdMob (default)" as label value).
   */
  public const DIMENSION_AD_SOURCE_INSTANCE = 'AD_SOURCE_INSTANCE';
  /**
   * The unique ID of the ad unit (for example, "ca-app-pub-1234/8790"). If
   * AD_UNIT dimension is specified, then APP is included automatically.
   */
  public const DIMENSION_AD_UNIT = 'AD_UNIT';
  /**
   * The unique ID of the mobile application (for example, "ca-app-
   * pub-1234~1234").
   */
  public const DIMENSION_APP = 'APP';
  /**
   * The unique ID of the mediation group (for example, "ca-app-
   * pub-1234:mg:1234" and "AdMob (default)" as label value).
   */
  public const DIMENSION_MEDIATION_GROUP = 'MEDIATION_GROUP';
  /**
   * CLDR country code of the place where the ad views/clicks occur (for
   * example, "US" or "FR"). This is a geography dimension.
   */
  public const DIMENSION_COUNTRY = 'COUNTRY';
  /**
   * Format of the ad unit (for example, "banner", "native"), an ad delivery
   * dimension.
   */
  public const DIMENSION_FORMAT = 'FORMAT';
  /**
   * Mobile OS platform of the app (for example, "Android" or "iOS").
   */
  public const DIMENSION_PLATFORM = 'PLATFORM';
  /**
   * Mobile operating system version, e.g. "iOS 13.5.1".
   */
  public const DIMENSION_MOBILE_OS_VERSION = 'MOBILE_OS_VERSION';
  /**
   * GMA SDK version, e.g. "iOS 7.62.0".
   */
  public const DIMENSION_GMA_SDK_VERSION = 'GMA_SDK_VERSION';
  /**
   * For Android, the app version name can be found in versionName in
   * PackageInfo. For iOS, the app version name can be found in
   * CFBundleShortVersionString.
   */
  public const DIMENSION_APP_VERSION_NAME = 'APP_VERSION_NAME';
  /**
   * Restriction mode for ads serving (e.g. "Non-personalized ads").
   */
  public const DIMENSION_SERVING_RESTRICTION = 'SERVING_RESTRICTION';
  /**
   * Default value for an unset field. Do not use.
   */
  public const METRIC_METRIC_UNSPECIFIED = 'METRIC_UNSPECIFIED';
  /**
   * The number of requests. The value is an integer.
   */
  public const METRIC_AD_REQUESTS = 'AD_REQUESTS';
  /**
   * The number of times a user clicks an ad. The value is an integer.
   */
  public const METRIC_CLICKS = 'CLICKS';
  /**
   * The estimated earnings of the AdMob publisher. The currency unit (USD, EUR,
   * or other) of the earning metrics are determined by the localization setting
   * for currency. The amount is in micros. For example, $6.50 would be
   * represented as 6500000. Estimated earnings per mediation group and per ad
   * source instance level is supported dating back to October 20, 2019. Third-
   * party estimated earnings will show 0 for dates prior to October 20, 2019.
   */
  public const METRIC_ESTIMATED_EARNINGS = 'ESTIMATED_EARNINGS';
  /**
   * The total number of ads shown to users. The value is an integer.
   */
  public const METRIC_IMPRESSIONS = 'IMPRESSIONS';
  /**
   * The ratio of clicks over impressions. The value is a double precision
   * (approximate) decimal value.
   */
  public const METRIC_IMPRESSION_CTR = 'IMPRESSION_CTR';
  /**
   * The number of times ads are returned in response to a request. The value is
   * an integer.
   */
  public const METRIC_MATCHED_REQUESTS = 'MATCHED_REQUESTS';
  /**
   * The ratio of matched ad requests over the total ad requests. The value is a
   * double precision (approximate) decimal value.
   */
  public const METRIC_MATCH_RATE = 'MATCH_RATE';
  /**
   * The third-party ad network's estimated average eCPM. The currency unit
   * (USD, EUR, or other) of the earning metrics are determined by the
   * localization setting for currency. The amount is in micros. For example,
   * $2.30 would be represented as 2300000. The estimated average eCPM per
   * mediation group and per ad source instance level is supported dating back
   * to October 20, 2019. Third-party estimated average eCPM will show 0 for
   * dates prior to October 20, 2019.
   */
  public const METRIC_OBSERVED_ECPM = 'OBSERVED_ECPM';
  /**
   * Default value for an unset field. Do not use.
   */
  public const ORDER_SORT_ORDER_UNSPECIFIED = 'SORT_ORDER_UNSPECIFIED';
  /**
   * Sort dimension value or metric value in ascending order.
   */
  public const ORDER_ASCENDING = 'ASCENDING';
  /**
   * Sort dimension value or metric value in descending order.
   */
  public const ORDER_DESCENDING = 'DESCENDING';
  /**
   * Sort by the specified dimension.
   *
   * @var string
   */
  public $dimension;
  /**
   * Sort by the specified metric.
   *
   * @var string
   */
  public $metric;
  /**
   * Sorting order of the dimension or metric.
   *
   * @var string
   */
  public $order;

  /**
   * Sort by the specified dimension.
   *
   * Accepted values: DIMENSION_UNSPECIFIED, DATE, MONTH, WEEK, AD_SOURCE,
   * AD_SOURCE_INSTANCE, AD_UNIT, APP, MEDIATION_GROUP, COUNTRY, FORMAT,
   * PLATFORM, MOBILE_OS_VERSION, GMA_SDK_VERSION, APP_VERSION_NAME,
   * SERVING_RESTRICTION
   *
   * @param self::DIMENSION_* $dimension
   */
  public function setDimension($dimension)
  {
    $this->dimension = $dimension;
  }
  /**
   * @return self::DIMENSION_*
   */
  public function getDimension()
  {
    return $this->dimension;
  }
  /**
   * Sort by the specified metric.
   *
   * Accepted values: METRIC_UNSPECIFIED, AD_REQUESTS, CLICKS,
   * ESTIMATED_EARNINGS, IMPRESSIONS, IMPRESSION_CTR, MATCHED_REQUESTS,
   * MATCH_RATE, OBSERVED_ECPM
   *
   * @param self::METRIC_* $metric
   */
  public function setMetric($metric)
  {
    $this->metric = $metric;
  }
  /**
   * @return self::METRIC_*
   */
  public function getMetric()
  {
    return $this->metric;
  }
  /**
   * Sorting order of the dimension or metric.
   *
   * Accepted values: SORT_ORDER_UNSPECIFIED, ASCENDING, DESCENDING
   *
   * @param self::ORDER_* $order
   */
  public function setOrder($order)
  {
    $this->order = $order;
  }
  /**
   * @return self::ORDER_*
   */
  public function getOrder()
  {
    return $this->order;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MediationReportSpecSortCondition::class, 'Google_Service_AdMob_MediationReportSpecSortCondition');
