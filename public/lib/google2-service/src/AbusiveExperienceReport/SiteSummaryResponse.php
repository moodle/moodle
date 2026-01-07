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

namespace Google\Service\AbusiveExperienceReport;

class SiteSummaryResponse extends \Google\Model
{
  /**
   * Not reviewed.
   */
  public const ABUSIVE_STATUS_UNKNOWN = 'UNKNOWN';
  /**
   * Passing.
   */
  public const ABUSIVE_STATUS_PASSING = 'PASSING';
  /**
   * Failing.
   */
  public const ABUSIVE_STATUS_FAILING = 'FAILING';
  /**
   * N/A.
   */
  public const FILTER_STATUS_UNKNOWN = 'UNKNOWN';
  /**
   * Enforcement is on.
   */
  public const FILTER_STATUS_ON = 'ON';
  /**
   * Enforcement is off.
   */
  public const FILTER_STATUS_OFF = 'OFF';
  /**
   * Enforcement is paused.
   */
  public const FILTER_STATUS_PAUSED = 'PAUSED';
  /**
   * Enforcement is pending.
   */
  public const FILTER_STATUS_PENDING = 'PENDING';
  /**
   * The site's Abusive Experience Report status.
   *
   * @var string
   */
  public $abusiveStatus;
  /**
   * The time at which
   * [enforcement](https://support.google.com/webtools/answer/7538608) against
   * the site began or will begin. Not set when the filter_status is OFF.
   *
   * @var string
   */
  public $enforcementTime;
  /**
   * The site's [enforcement
   * status](https://support.google.com/webtools/answer/7538608).
   *
   * @var string
   */
  public $filterStatus;
  /**
   * The time at which the site's status last changed.
   *
   * @var string
   */
  public $lastChangeTime;
  /**
   * A link to the full Abusive Experience Report for the site. Not set in
   * ViolatingSitesResponse. Note that you must complete the [Search Console
   * verification process](https://support.google.com/webmasters/answer/9008080)
   * for the site before you can access the full report.
   *
   * @var string
   */
  public $reportUrl;
  /**
   * The name of the reviewed site, e.g. `google.com`.
   *
   * @var string
   */
  public $reviewedSite;
  /**
   * Whether the site is currently under review.
   *
   * @var bool
   */
  public $underReview;

  /**
   * The site's Abusive Experience Report status.
   *
   * Accepted values: UNKNOWN, PASSING, FAILING
   *
   * @param self::ABUSIVE_STATUS_* $abusiveStatus
   */
  public function setAbusiveStatus($abusiveStatus)
  {
    $this->abusiveStatus = $abusiveStatus;
  }
  /**
   * @return self::ABUSIVE_STATUS_*
   */
  public function getAbusiveStatus()
  {
    return $this->abusiveStatus;
  }
  /**
   * The time at which
   * [enforcement](https://support.google.com/webtools/answer/7538608) against
   * the site began or will begin. Not set when the filter_status is OFF.
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
   * status](https://support.google.com/webtools/answer/7538608).
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
   * The time at which the site's status last changed.
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
   * A link to the full Abusive Experience Report for the site. Not set in
   * ViolatingSitesResponse. Note that you must complete the [Search Console
   * verification process](https://support.google.com/webmasters/answer/9008080)
   * for the site before you can access the full report.
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
   * The name of the reviewed site, e.g. `google.com`.
   *
   * @param string $reviewedSite
   */
  public function setReviewedSite($reviewedSite)
  {
    $this->reviewedSite = $reviewedSite;
  }
  /**
   * @return string
   */
  public function getReviewedSite()
  {
    return $this->reviewedSite;
  }
  /**
   * Whether the site is currently under review.
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
class_alias(SiteSummaryResponse::class, 'Google_Service_AbusiveExperienceReport_SiteSummaryResponse');
