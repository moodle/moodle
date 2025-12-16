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

class ReportFloodlightCriteriaReportProperties extends \Google\Model
{
  /**
   * Include conversions that have no cookie, but do have an exposure path.
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
   * Include conversions that have no cookie, but do have an exposure path.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ReportFloodlightCriteriaReportProperties::class, 'Google_Service_Dfareporting_ReportFloodlightCriteriaReportProperties');
