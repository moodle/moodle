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

namespace Google\Service\Playdeveloperreporting;

class GooglePlayDeveloperReportingV1beta1QueryStuckBackgroundWakelockRateMetricSetRequest extends \Google\Collection
{
  /**
   * Unspecified User cohort. This will automatically choose the default value.
   */
  public const USER_COHORT_USER_COHORT_UNSPECIFIED = 'USER_COHORT_UNSPECIFIED';
  /**
   * This is default view. Contains data from public released android versions
   * only.
   */
  public const USER_COHORT_OS_PUBLIC = 'OS_PUBLIC';
  /**
   * This is the view with just android beta data excluding released OS version
   * data.
   */
  public const USER_COHORT_OS_BETA = 'OS_BETA';
  /**
   * This is the view with data only from users who have opted in to be testers
   * for a given app, excluding OS beta data.
   */
  public const USER_COHORT_APP_TESTERS = 'APP_TESTERS';
  protected $collection_key = 'metrics';
  /**
   * Dimensions to slice the data by. **Supported dimensions:** * `apiLevel`
   * (string): the API level of Android that was running on the user's device,
   * e.g., 26. * `versionCode` (int64): version of the app that was running on
   * the user's device. * `deviceModel` (string): unique identifier of the
   * user's device model. The form of the identifier is 'deviceBrand/device',
   * where deviceBrand corresponds to Build.BRAND and device corresponds to
   * Build.DEVICE, e.g., google/coral. * `deviceBrand` (string): unique
   * identifier of the user's device brand, e.g., google. * `deviceType`
   * (string): the type (also known as form factor) of the user's device, e.g.,
   * PHONE. * `countryCode` (string): the country or region of the user's device
   * based on their IP address, represented as a 2-letter ISO-3166 code (e.g. US
   * for the United States). * `deviceRamBucket` (int64): RAM of the device, in
   * MB, in buckets (3GB, 4GB, etc.). * `deviceSocMake` (string): Make of the
   * device's primary system-on-chip, e.g., Samsung. [Reference](https://develop
   * er.android.com/reference/android/os/Build#SOC_MANUFACTURER) *
   * `deviceSocModel` (string): Model of the device's primary system-on-chip,
   * e.g., "Exynos 2100". [Reference](https://developer.android.com/reference/an
   * droid/os/Build#SOC_MODEL) * `deviceCpuMake` (string): Make of the device's
   * CPU, e.g., Qualcomm. * `deviceCpuModel` (string): Model of the device's
   * CPU, e.g., "Kryo 240". * `deviceGpuMake` (string): Make of the device's
   * GPU, e.g., ARM. * `deviceGpuModel` (string): Model of the device's GPU,
   * e.g., Mali. * `deviceGpuVersion` (string): Version of the device's GPU,
   * e.g., T750. * `deviceVulkanVersion` (string): Vulkan version of the device,
   * e.g., "4198400". * `deviceGlEsVersion` (string): OpenGL ES version of the
   * device, e.g., "196610". * `deviceScreenSize` (string): Screen size of the
   * device, e.g., NORMAL, LARGE. * `deviceScreenDpi` (string): Screen density
   * of the device, e.g., mdpi, hdpi.
   *
   * @var string[]
   */
  public $dimensions;
  /**
   * Filters to apply to data. The filtering expression follows
   * [AIP-160](https://google.aip.dev/160) standard and supports filtering by
   * equality of all breakdown dimensions.
   *
   * @var string
   */
  public $filter;
  /**
   * Metrics to aggregate. **Supported metrics:** * `stuckBgWakelockRate`
   * (`google.type.Decimal`): Percentage of distinct users in the aggregation
   * period that had a wakelock held in the background for longer than 1 hour. *
   * `stuckBgWakelockRate7dUserWeighted` (`google.type.Decimal`): Rolling
   * average value of `stuckBgWakelockRate` in the last 7 days. The daily values
   * are weighted by the count of distinct users for the day. *
   * `stuckBgWakelockRate28dUserWeighted` (`google.type.Decimal`): Rolling
   * average value of `stuckBgWakelockRate` in the last 28 days. The daily
   * values are weighted by the count of distinct users for the day. *
   * `distinctUsers` (`google.type.Decimal`): Count of distinct users in the
   * aggregation period that were used as normalization value for the
   * `stuckBgWakelockRate` metric. A user is counted in this metric if they app
   * was doing any work on the device, i.e., not just active foreground usage
   * but also background work. Care must be taken not to aggregate this count
   * further, as it may result in users being counted multiple times. The value
   * is rounded to the nearest multiple of 10, 100, 1,000 or 1,000,000,
   * depending on the magnitude of the value.
   *
   * @var string[]
   */
  public $metrics;
  /**
   * Maximum size of the returned data. If unspecified, at most 1000 rows will
   * be returned. The maximum value is 100000; values above 100000 will be
   * coerced to 100000.
   *
   * @var int
   */
  public $pageSize;
  /**
   * A page token, received from a previous call. Provide this to retrieve the
   * subsequent page. When paginating, all other parameters provided to the
   * request must match the call that provided the page token.
   *
   * @var string
   */
  public $pageToken;
  protected $timelineSpecType = GooglePlayDeveloperReportingV1beta1TimelineSpec::class;
  protected $timelineSpecDataType = '';
  /**
   * User view to select. The output data will correspond to the selected view.
   * The only supported value is `OS_PUBLIC`.
   *
   * @var string
   */
  public $userCohort;

  /**
   * Dimensions to slice the data by. **Supported dimensions:** * `apiLevel`
   * (string): the API level of Android that was running on the user's device,
   * e.g., 26. * `versionCode` (int64): version of the app that was running on
   * the user's device. * `deviceModel` (string): unique identifier of the
   * user's device model. The form of the identifier is 'deviceBrand/device',
   * where deviceBrand corresponds to Build.BRAND and device corresponds to
   * Build.DEVICE, e.g., google/coral. * `deviceBrand` (string): unique
   * identifier of the user's device brand, e.g., google. * `deviceType`
   * (string): the type (also known as form factor) of the user's device, e.g.,
   * PHONE. * `countryCode` (string): the country or region of the user's device
   * based on their IP address, represented as a 2-letter ISO-3166 code (e.g. US
   * for the United States). * `deviceRamBucket` (int64): RAM of the device, in
   * MB, in buckets (3GB, 4GB, etc.). * `deviceSocMake` (string): Make of the
   * device's primary system-on-chip, e.g., Samsung. [Reference](https://develop
   * er.android.com/reference/android/os/Build#SOC_MANUFACTURER) *
   * `deviceSocModel` (string): Model of the device's primary system-on-chip,
   * e.g., "Exynos 2100". [Reference](https://developer.android.com/reference/an
   * droid/os/Build#SOC_MODEL) * `deviceCpuMake` (string): Make of the device's
   * CPU, e.g., Qualcomm. * `deviceCpuModel` (string): Model of the device's
   * CPU, e.g., "Kryo 240". * `deviceGpuMake` (string): Make of the device's
   * GPU, e.g., ARM. * `deviceGpuModel` (string): Model of the device's GPU,
   * e.g., Mali. * `deviceGpuVersion` (string): Version of the device's GPU,
   * e.g., T750. * `deviceVulkanVersion` (string): Vulkan version of the device,
   * e.g., "4198400". * `deviceGlEsVersion` (string): OpenGL ES version of the
   * device, e.g., "196610". * `deviceScreenSize` (string): Screen size of the
   * device, e.g., NORMAL, LARGE. * `deviceScreenDpi` (string): Screen density
   * of the device, e.g., mdpi, hdpi.
   *
   * @param string[] $dimensions
   */
  public function setDimensions($dimensions)
  {
    $this->dimensions = $dimensions;
  }
  /**
   * @return string[]
   */
  public function getDimensions()
  {
    return $this->dimensions;
  }
  /**
   * Filters to apply to data. The filtering expression follows
   * [AIP-160](https://google.aip.dev/160) standard and supports filtering by
   * equality of all breakdown dimensions.
   *
   * @param string $filter
   */
  public function setFilter($filter)
  {
    $this->filter = $filter;
  }
  /**
   * @return string
   */
  public function getFilter()
  {
    return $this->filter;
  }
  /**
   * Metrics to aggregate. **Supported metrics:** * `stuckBgWakelockRate`
   * (`google.type.Decimal`): Percentage of distinct users in the aggregation
   * period that had a wakelock held in the background for longer than 1 hour. *
   * `stuckBgWakelockRate7dUserWeighted` (`google.type.Decimal`): Rolling
   * average value of `stuckBgWakelockRate` in the last 7 days. The daily values
   * are weighted by the count of distinct users for the day. *
   * `stuckBgWakelockRate28dUserWeighted` (`google.type.Decimal`): Rolling
   * average value of `stuckBgWakelockRate` in the last 28 days. The daily
   * values are weighted by the count of distinct users for the day. *
   * `distinctUsers` (`google.type.Decimal`): Count of distinct users in the
   * aggregation period that were used as normalization value for the
   * `stuckBgWakelockRate` metric. A user is counted in this metric if they app
   * was doing any work on the device, i.e., not just active foreground usage
   * but also background work. Care must be taken not to aggregate this count
   * further, as it may result in users being counted multiple times. The value
   * is rounded to the nearest multiple of 10, 100, 1,000 or 1,000,000,
   * depending on the magnitude of the value.
   *
   * @param string[] $metrics
   */
  public function setMetrics($metrics)
  {
    $this->metrics = $metrics;
  }
  /**
   * @return string[]
   */
  public function getMetrics()
  {
    return $this->metrics;
  }
  /**
   * Maximum size of the returned data. If unspecified, at most 1000 rows will
   * be returned. The maximum value is 100000; values above 100000 will be
   * coerced to 100000.
   *
   * @param int $pageSize
   */
  public function setPageSize($pageSize)
  {
    $this->pageSize = $pageSize;
  }
  /**
   * @return int
   */
  public function getPageSize()
  {
    return $this->pageSize;
  }
  /**
   * A page token, received from a previous call. Provide this to retrieve the
   * subsequent page. When paginating, all other parameters provided to the
   * request must match the call that provided the page token.
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
   * Specification of the timeline aggregation parameters. **Supported
   * aggregation periods:** * DAILY: metrics are aggregated in calendar date
   * intervals. Due to historical constraints, the only supported timezone is
   * `America/Los_Angeles`.
   *
   * @param GooglePlayDeveloperReportingV1beta1TimelineSpec $timelineSpec
   */
  public function setTimelineSpec(GooglePlayDeveloperReportingV1beta1TimelineSpec $timelineSpec)
  {
    $this->timelineSpec = $timelineSpec;
  }
  /**
   * @return GooglePlayDeveloperReportingV1beta1TimelineSpec
   */
  public function getTimelineSpec()
  {
    return $this->timelineSpec;
  }
  /**
   * User view to select. The output data will correspond to the selected view.
   * The only supported value is `OS_PUBLIC`.
   *
   * Accepted values: USER_COHORT_UNSPECIFIED, OS_PUBLIC, OS_BETA, APP_TESTERS
   *
   * @param self::USER_COHORT_* $userCohort
   */
  public function setUserCohort($userCohort)
  {
    $this->userCohort = $userCohort;
  }
  /**
   * @return self::USER_COHORT_*
   */
  public function getUserCohort()
  {
    return $this->userCohort;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePlayDeveloperReportingV1beta1QueryStuckBackgroundWakelockRateMetricSetRequest::class, 'Google_Service_Playdeveloperreporting_GooglePlayDeveloperReportingV1beta1QueryStuckBackgroundWakelockRateMetricSetRequest');
