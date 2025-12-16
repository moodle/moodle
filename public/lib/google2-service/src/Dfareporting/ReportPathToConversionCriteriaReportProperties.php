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

namespace Google\Service\Dfareporting;

class ReportPathToConversionCriteriaReportProperties extends \Google\Model
{
  /**
   * CM360 checks to see if a click interaction occurred within the specified
   * period of time before a conversion. By default the value is pulled from
   * Floodlight or you can manually enter a custom value. Valid values: 1-90.
   *
   * @var int
   */
  public $clicksLookbackWindow;
  /**
   * CM360 checks to see if an impression interaction occurred within the
   * specified period of time before a conversion. By default the value is
   * pulled from Floodlight or you can manually enter a custom value. Valid
   * values: 1-90.
   *
   * @var int
   */
  public $impressionsLookbackWindow;
  /**
   * Deprecated: has no effect.
   *
   * @var bool
   */
  public $includeAttributedIPConversions;
  /**
   * Include conversions of users with a DoubleClick cookie but without an
   * exposure. That means the user did not click or see an ad from the
   * advertiser within the Floodlight group, or that the interaction happened
   * outside the lookback window.
   *
   * @var bool
   */
  public $includeUnattributedCookieConversions;
  /**
   * Include conversions that have no associated cookies and no exposures. It’s
   * therefore impossible to know how the user was exposed to your ads during
   * the lookback window prior to a conversion.
   *
   * @var bool
   */
  public $includeUnattributedIPConversions;
  /**
   * The maximum number of click interactions to include in the report.
   * Advertisers currently paying for E2C reports get up to 200 (100 clicks, 100
   * impressions). If another advertiser in your network is paying for E2C, you
   * can have up to 5 total exposures per report.
   *
   * @var int
   */
  public $maximumClickInteractions;
  /**
   * The maximum number of click interactions to include in the report.
   * Advertisers currently paying for E2C reports get up to 200 (100 clicks, 100
   * impressions). If another advertiser in your network is paying for E2C, you
   * can have up to 5 total exposures per report.
   *
   * @var int
   */
  public $maximumImpressionInteractions;
  /**
   * The maximum amount of time that can take place between interactions (clicks
   * or impressions) by the same user. Valid values: 1-90.
   *
   * @var int
   */
  public $maximumInteractionGap;
  /**
   * Enable pivoting on interaction path.
   *
   * @var bool
   */
  public $pivotOnInteractionPath;

  /**
   * CM360 checks to see if a click interaction occurred within the specified
   * period of time before a conversion. By default the value is pulled from
   * Floodlight or you can manually enter a custom value. Valid values: 1-90.
   *
   * @param int $clicksLookbackWindow
   */
  public function setClicksLookbackWindow($clicksLookbackWindow)
  {
    $this->clicksLookbackWindow = $clicksLookbackWindow;
  }
  /**
   * @return int
   */
  public function getClicksLookbackWindow()
  {
    return $this->clicksLookbackWindow;
  }
  /**
   * CM360 checks to see if an impression interaction occurred within the
   * specified period of time before a conversion. By default the value is
   * pulled from Floodlight or you can manually enter a custom value. Valid
   * values: 1-90.
   *
   * @param int $impressionsLookbackWindow
   */
  public function setImpressionsLookbackWindow($impressionsLookbackWindow)
  {
    $this->impressionsLookbackWindow = $impressionsLookbackWindow;
  }
  /**
   * @return int
   */
  public function getImpressionsLookbackWindow()
  {
    return $this->impressionsLookbackWindow;
  }
  /**
   * Deprecated: has no effect.
   *
   * @param bool $includeAttributedIPConversions
   */
  public function setIncludeAttributedIPConversions($includeAttributedIPConversions)
  {
    $this->includeAttributedIPConversions = $includeAttributedIPConversions;
  }
  /**
   * @return bool
   */
  public function getIncludeAttributedIPConversions()
  {
    return $this->includeAttributedIPConversions;
  }
  /**
   * Include conversions of users with a DoubleClick cookie but without an
   * exposure. That means the user did not click or see an ad from the
   * advertiser within the Floodlight group, or that the interaction happened
   * outside the lookback window.
   *
   * @param bool $includeUnattributedCookieConversions
   */
  public function setIncludeUnattributedCookieConversions($includeUnattributedCookieConversions)
  {
    $this->includeUnattributedCookieConversions = $includeUnattributedCookieConversions;
  }
  /**
   * @return bool
   */
  public function getIncludeUnattributedCookieConversions()
  {
    return $this->includeUnattributedCookieConversions;
  }
  /**
   * Include conversions that have no associated cookies and no exposures. It’s
   * therefore impossible to know how the user was exposed to your ads during
   * the lookback window prior to a conversion.
   *
   * @param bool $includeUnattributedIPConversions
   */
  public function setIncludeUnattributedIPConversions($includeUnattributedIPConversions)
  {
    $this->includeUnattributedIPConversions = $includeUnattributedIPConversions;
  }
  /**
   * @return bool
   */
  public function getIncludeUnattributedIPConversions()
  {
    return $this->includeUnattributedIPConversions;
  }
  /**
   * The maximum number of click interactions to include in the report.
   * Advertisers currently paying for E2C reports get up to 200 (100 clicks, 100
   * impressions). If another advertiser in your network is paying for E2C, you
   * can have up to 5 total exposures per report.
   *
   * @param int $maximumClickInteractions
   */
  public function setMaximumClickInteractions($maximumClickInteractions)
  {
    $this->maximumClickInteractions = $maximumClickInteractions;
  }
  /**
   * @return int
   */
  public function getMaximumClickInteractions()
  {
    return $this->maximumClickInteractions;
  }
  /**
   * The maximum number of click interactions to include in the report.
   * Advertisers currently paying for E2C reports get up to 200 (100 clicks, 100
   * impressions). If another advertiser in your network is paying for E2C, you
   * can have up to 5 total exposures per report.
   *
   * @param int $maximumImpressionInteractions
   */
  public function setMaximumImpressionInteractions($maximumImpressionInteractions)
  {
    $this->maximumImpressionInteractions = $maximumImpressionInteractions;
  }
  /**
   * @return int
   */
  public function getMaximumImpressionInteractions()
  {
    return $this->maximumImpressionInteractions;
  }
  /**
   * The maximum amount of time that can take place between interactions (clicks
   * or impressions) by the same user. Valid values: 1-90.
   *
   * @param int $maximumInteractionGap
   */
  public function setMaximumInteractionGap($maximumInteractionGap)
  {
    $this->maximumInteractionGap = $maximumInteractionGap;
  }
  /**
   * @return int
   */
  public function getMaximumInteractionGap()
  {
    return $this->maximumInteractionGap;
  }
  /**
   * Enable pivoting on interaction path.
   *
   * @param bool $pivotOnInteractionPath
   */
  public function setPivotOnInteractionPath($pivotOnInteractionPath)
  {
    $this->pivotOnInteractionPath = $pivotOnInteractionPath;
  }
  /**
   * @return bool
   */
  public function getPivotOnInteractionPath()
  {
    return $this->pivotOnInteractionPath;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ReportPathToConversionCriteriaReportProperties::class, 'Google_Service_Dfareporting_ReportPathToConversionCriteriaReportProperties');
