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

namespace Google\Service\AdExperienceReport;

class PlatformSummary extends \Google\Collection
{
  /**
   * Not reviewed.
   */
  public const BETTER_ADS_STATUS_UNKNOWN = 'UNKNOWN';
  /**
   * Passing.
   */
  public const BETTER_ADS_STATUS_PASSING = 'PASSING';
  /**
   * Warning. No longer a possible status.
   *
   * @deprecated
   */
  public const BETTER_ADS_STATUS_WARNING = 'WARNING';
  /**
   * Failing.
   */
  public const BETTER_ADS_STATUS_FAILING = 'FAILING';
  /**
   * N/A.
   */
  public const FILTER_STATUS_UNKNOWN = 'UNKNOWN';
  /**
   * Ad filtering is on.
   */
  public const FILTER_STATUS_ON = 'ON';
  /**
   * Ad filtering is off.
   */
  public const FILTER_STATUS_OFF = 'OFF';
  /**
   * Ad filtering is paused.
   */
  public const FILTER_STATUS_PAUSED = 'PAUSED';
  /**
   * Ad filtering is pending.
   */
  public const FILTER_STATUS_PENDING = 'PENDING';
  protected $collection_key = 'region';
  /**
   * The site's Ad Experience Report status on this platform.
   *
   * @var string
   */
  public $betterAdsStatus;
  /**
   * The time at which
   * [enforcement](https://support.google.com/webtools/answer/7308033) against
   * the site began or will begin on this platform. Not set when the
   * filter_status is OFF.
   *
   * @var string
   */
  public $enforcementTime;
  /**
   * The site's [enforcement
   * status](https://support.google.com/webtools/answer/7308033) on this
   * platform.
   *
   * @var string
   */
  public $filterStatus;
  /**
   * The time at which the site's status last changed on this platform.
   *
   * @var string
   */
  public $lastChangeTime;
  /**
   * The site's regions on this platform. No longer populated, because there is
   * no longer any semantic difference between sites in different regions.
   *
   * @deprecated
   * @var string[]
   */
  public $region;
  /**
   * A link to the full Ad Experience Report for the site on this platform.. Not
   * set in ViolatingSitesResponse. Note that you must complete the [Search
   * Console verification
   * process](https://support.google.com/webmasters/answer/9008080) for the site
   * before you can access the full report.
   *
   * @var string
   */
  public $reportUrl;
  /**
   * Whether the site is currently under review on this platform.
   *
   * @var bool
   */
  public $underReview;

  /**
   * The site's Ad Experience Report status on this platform.
   *
   * Accepted values: UNKNOWN, PASSING, WARNING, FAILING
   *
   * @param self::BETTER_ADS_STATUS_* $betterAdsStatus
   */
  public function setBetterAdsStatus($betterAdsStatus)
  {
    $this->betterAdsStatus = $betterAdsStatus;
  }
  /**
   * @return self::BETTER_ADS_STATUS_*
   */
  public function getBetterAdsStatus()
  {
    return $this->betterAdsStatus;
  }
  /**
   * The time at which
   * [enforcement](https://support.google.com/webtools/answer/7308033) against
   * the site began or will begin on this platform. Not set when the
   * filter_status is OFF.
   *
   * @param string $enforcementTime
   */
  public function setEnforcementTime($enforcementTime)
  {
    $this->enforcementTime = $enforcementTime;
  }
  /**
   * @return string
   */
  public function getEnforcementTime()
  {
    return $this->enforcementTime;
  }
  /**
   * The site's [enforcement
   * status](https://support.google.com/webtools/answer/7308033) on this
   * platform.
   *
   * Accepted values: UNKNOWN, ON, OFF, PAUSED, PENDING
   *
   * @param self::FILTER_STATUS_* $filterStatus
   */
  public function setFilterStatus($filterStatus)
  {
    $this->filterStatus = $filterStatus;
  }
  /**
   * @return self::FILTER_STATUS_*
   */
  public function getFilterStatus()
  {
    return $this->filterStatus;
  }
  /**
   * The time at which the site's status last changed on this platform.
   *
   * @param string $lastChangeTime
   */
  public function setLastChangeTime($lastChangeTime)
  {
    $this->lastChangeTime = $lastChangeTime;
  }
  /**
   * @return string
   */
  public function getLastChangeTime()
  {
    return $this->lastChangeTime;
  }
  /**
   * The site's regions on this platform. No longer populated, because there is
   * no longer any semantic difference between sites in different regions.
   *
   * @deprecated
   * @param string[] $region
   */
  public function setRegion($region)
  {
    $this->region = $region;
  }
  /**
   * @deprecated
   * @return string[]
   */
  public function getRegion()
  {
    return $this->region;
  }
  /**
   * A link to the full Ad Experience Report for the site on this platform.. Not
   * set in ViolatingSitesResponse. Note that you must complete the [Search
   * Console verification
   * process](https://support.google.com/webmasters/answer/9008080) for the site
   * before you can access the full report.
   *
   * @param string $reportUrl
   */
  public function setReportUrl($reportUrl)
  {
    $this->reportUrl = $reportUrl;
  }
  /**
   * @return string
   */
  public function getReportUrl()
  {
    return $this->reportUrl;
  }
  /**
   * Whether the site is currently under review on this platform.
   *
   * @param bool $underReview
   */
  public function setUnderReview($underReview)
  {
    $this->underReview = $underReview;
  }
  /**
   * @return bool
   */
  public function getUnderReview()
  {
    return $this->underReview;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PlatformSummary::class, 'Google_Service_AdExperienceReport_PlatformSummary');
