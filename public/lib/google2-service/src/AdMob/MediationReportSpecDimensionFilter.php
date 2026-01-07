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

class MediationReportSpecDimensionFilter extends \Google\Model
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
   * Applies the filter criterion to the specified dimension.
   *
   * @var string
   */
  public $dimension;
  protected $matchesAnyType = StringList::class;
  protected $matchesAnyDataType = '';

  /**
   * Applies the filter criterion to the specified dimension.
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
   * Matches a row if its value for the specified dimension is in one of the
   * values specified in this condition.
   *
   * @param StringList $matchesAny
   */
  public function setMatchesAny(StringList $matchesAny)
  {
    $this->matchesAny = $matchesAny;
  }
  /**
   * @return StringList
   */
  public function getMatchesAny()
  {
    return $this->matchesAny;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MediationReportSpecDimensionFilter::class, 'Google_Service_AdMob_MediationReportSpecDimensionFilter');
