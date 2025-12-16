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

class GooglePlayDeveloperReportingV1beta1QueryErrorCountMetricSetRequest extends \Google\Collection
{
  protected $collection_key = 'metrics';
  /**
   * Dimensions to slice the data by. **Supported dimensions:** * `apiLevel`
   * (string): the API level of Android that was running on the user's device,
   * e.g., 26. * `versionCode` (int64): unique identifier of the user's device
   * model. The form of the identifier is 'deviceBrand/device', where
   * deviceBrand corresponds to Build.BRAND and device corresponds to
   * Build.DEVICE, e.g., google/coral. * `deviceModel` (string): unique
   * identifier of the user's device model. * `deviceType` (string): identifier
   * of the device's form factor, e.g., PHONE. * `reportType` (string): the type
   * of error. The value should correspond to one of the possible values in
   * ErrorType. * `issueId` (string): the id an error was assigned to. The value
   * should correspond to the `{issue}` component of the issue name. *
   * `deviceRamBucket` (int64): RAM of the device, in MB, in buckets (3GB, 4GB,
   * etc.). * `deviceSocMake` (string): Make of the device's primary system-on-
   * chip, e.g., Samsung. [Reference](https://developer.android.com/reference/an
   * droid/os/Build#SOC_MANUFACTURER) * `deviceSocModel` (string): Model of the
   * device's primary system-on-chip, e.g., "Exynos 2100". [Reference](https://d
   * eveloper.android.com/reference/android/os/Build#SOC_MODEL) *
   * `deviceCpuMake` (string): Make of the device's CPU, e.g., Qualcomm. *
   * `deviceCpuModel` (string): Model of the device's CPU, e.g., "Kryo 240". *
   * `deviceGpuMake` (string): Make of the device's GPU, e.g., ARM. *
   * `deviceGpuModel` (string): Model of the device's GPU, e.g., Mali. *
   * `deviceGpuVersion` (string): Version of the device's GPU, e.g., T750. *
   * `deviceVulkanVersion` (string): Vulkan version of the device, e.g.,
   * "4198400". * `deviceGlEsVersion` (string): OpenGL ES version of the device,
   * e.g., "196610". * `deviceScreenSize` (string): Screen size of the device,
   * e.g., NORMAL, LARGE. * `deviceScreenDpi` (string): Screen density of the
   * device, e.g., mdpi, hdpi.
   *
   * @var string[]
   */
  public $dimensions;
  /**
   * Filters to apply to data. The filtering expression follows
   * [AIP-160](https://google.aip.dev/160) standard and supports filtering by
   * equality of all breakdown dimensions and: * `isUserPerceived` (string):
   * denotes whether error is user perceived or not, USER_PERCEIVED or
   * NOT_USER_PERCEIVED.
   *
   * @var string
   */
  public $filter;
  /**
   * Metrics to aggregate. **Supported metrics:** * `errorReportCount`
   * (`google.type.Decimal`): Absolute count of individual error reports that
   * have been received for an app. * `distinctUsers` (`google.type.Decimal`):
   * Count of distinct users for which reports have been received. Care must be
   * taken not to aggregate this count further, as it may result in users being
   * counted multiple times. This value is not rounded, however it may be an
   * approximation.
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
   * Dimensions to slice the data by. **Supported dimensions:** * `apiLevel`
   * (string): the API level of Android that was running on the user's device,
   * e.g., 26. * `versionCode` (int64): unique identifier of the user's device
   * model. The form of the identifier is 'deviceBrand/device', where
   * deviceBrand corresponds to Build.BRAND and device corresponds to
   * Build.DEVICE, e.g., google/coral. * `deviceModel` (string): unique
   * identifier of the user's device model. * `deviceType` (string): identifier
   * of the device's form factor, e.g., PHONE. * `reportType` (string): the type
   * of error. The value should correspond to one of the possible values in
   * ErrorType. * `issueId` (string): the id an error was assigned to. The value
   * should correspond to the `{issue}` component of the issue name. *
   * `deviceRamBucket` (int64): RAM of the device, in MB, in buckets (3GB, 4GB,
   * etc.). * `deviceSocMake` (string): Make of the device's primary system-on-
   * chip, e.g., Samsung. [Reference](https://developer.android.com/reference/an
   * droid/os/Build#SOC_MANUFACTURER) * `deviceSocModel` (string): Model of the
   * device's primary system-on-chip, e.g., "Exynos 2100". [Reference](https://d
   * eveloper.android.com/reference/android/os/Build#SOC_MODEL) *
   * `deviceCpuMake` (string): Make of the device's CPU, e.g., Qualcomm. *
   * `deviceCpuModel` (string): Model of the device's CPU, e.g., "Kryo 240". *
   * `deviceGpuMake` (string): Make of the device's GPU, e.g., ARM. *
   * `deviceGpuModel` (string): Model of the device's GPU, e.g., Mali. *
   * `deviceGpuVersion` (string): Version of the device's GPU, e.g., T750. *
   * `deviceVulkanVersion` (string): Vulkan version of the device, e.g.,
   * "4198400". * `deviceGlEsVersion` (string): OpenGL ES version of the device,
   * e.g., "196610". * `deviceScreenSize` (string): Screen size of the device,
   * e.g., NORMAL, LARGE. * `deviceScreenDpi` (string): Screen density of the
   * device, e.g., mdpi, hdpi.
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
   * equality of all breakdown dimensions and: * `isUserPerceived` (string):
   * denotes whether error is user perceived or not, USER_PERCEIVED or
   * NOT_USER_PERCEIVED.
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
   * Metrics to aggregate. **Supported metrics:** * `errorReportCount`
   * (`google.type.Decimal`): Absolute count of individual error reports that
   * have been received for an app. * `distinctUsers` (`google.type.Decimal`):
   * Count of distinct users for which reports have been received. Care must be
   * taken not to aggregate this count further, as it may result in users being
   * counted multiple times. This value is not rounded, however it may be an
   * approximation.
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
   * intervals. The default and only supported timezone is
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePlayDeveloperReportingV1beta1QueryErrorCountMetricSetRequest::class, 'Google_Service_Playdeveloperreporting_GooglePlayDeveloperReportingV1beta1QueryErrorCountMetricSetRequest');
