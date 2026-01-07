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

namespace Google\Service\SA360;

class GoogleAdsSearchads360V0CommonUnifiedCalloutAsset extends \Google\Collection
{
  protected $collection_key = 'adScheduleTargets';
  protected $adScheduleTargetsType = GoogleAdsSearchads360V0CommonAdScheduleInfo::class;
  protected $adScheduleTargetsDataType = 'array';
  /**
   * The callout text. The length of this string should be between 1 and 25,
   * inclusive.
   *
   * @var string
   */
  public $calloutText;
  /**
   * Last date of when this asset is effective and still serving, in yyyy-MM-dd
   * format.
   *
   * @var string
   */
  public $endDate;
  /**
   * Start date of when this asset is effective and can begin serving, in yyyy-
   * MM-dd format.
   *
   * @var string
   */
  public $startDate;
  /**
   * Whether to show the asset in search user's time zone. Applies to Microsoft
   * Ads.
   *
   * @var bool
   */
  public $useSearcherTimeZone;

  /**
   * List of non-overlapping schedules specifying all time intervals for which
   * the asset may serve. There can be a maximum of 6 schedules per day, 42 in
   * total.
   *
   * @param GoogleAdsSearchads360V0CommonAdScheduleInfo[] $adScheduleTargets
   */
  public function setAdScheduleTargets($adScheduleTargets)
  {
    $this->adScheduleTargets = $adScheduleTargets;
  }
  /**
   * @return GoogleAdsSearchads360V0CommonAdScheduleInfo[]
   */
  public function getAdScheduleTargets()
  {
    return $this->adScheduleTargets;
  }
  /**
   * The callout text. The length of this string should be between 1 and 25,
   * inclusive.
   *
   * @param string $calloutText
   */
  public function setCalloutText($calloutText)
  {
    $this->calloutText = $calloutText;
  }
  /**
   * @return string
   */
  public function getCalloutText()
  {
    return $this->calloutText;
  }
  /**
   * Last date of when this asset is effective and still serving, in yyyy-MM-dd
   * format.
   *
   * @param string $endDate
   */
  public function setEndDate($endDate)
  {
    $this->endDate = $endDate;
  }
  /**
   * @return string
   */
  public function getEndDate()
  {
    return $this->endDate;
  }
  /**
   * Start date of when this asset is effective and can begin serving, in yyyy-
   * MM-dd format.
   *
   * @param string $startDate
   */
  public function setStartDate($startDate)
  {
    $this->startDate = $startDate;
  }
  /**
   * @return string
   */
  public function getStartDate()
  {
    return $this->startDate;
  }
  /**
   * Whether to show the asset in search user's time zone. Applies to Microsoft
   * Ads.
   *
   * @param bool $useSearcherTimeZone
   */
  public function setUseSearcherTimeZone($useSearcherTimeZone)
  {
    $this->useSearcherTimeZone = $useSearcherTimeZone;
  }
  /**
   * @return bool
   */
  public function getUseSearcherTimeZone()
  {
    return $this->useSearcherTimeZone;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAdsSearchads360V0CommonUnifiedCalloutAsset::class, 'Google_Service_SA360_GoogleAdsSearchads360V0CommonUnifiedCalloutAsset');
