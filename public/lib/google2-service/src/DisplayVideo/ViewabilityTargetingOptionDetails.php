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

namespace Google\Service\DisplayVideo;

class ViewabilityTargetingOptionDetails extends \Google\Model
{
  /**
   * Default value when viewability is not specified in this version. This enum
   * is a placeholder for default value and does not represent a real
   * viewability option.
   */
  public const VIEWABILITY_VIEWABILITY_UNSPECIFIED = 'VIEWABILITY_UNSPECIFIED';
  /**
   * Bid only on impressions that are at least 10% likely to be viewable.
   */
  public const VIEWABILITY_VIEWABILITY_10_PERCENT_OR_MORE = 'VIEWABILITY_10_PERCENT_OR_MORE';
  /**
   * Bid only on impressions that are at least 20% likely to be viewable.
   */
  public const VIEWABILITY_VIEWABILITY_20_PERCENT_OR_MORE = 'VIEWABILITY_20_PERCENT_OR_MORE';
  /**
   * Bid only on impressions that are at least 30% likely to be viewable.
   */
  public const VIEWABILITY_VIEWABILITY_30_PERCENT_OR_MORE = 'VIEWABILITY_30_PERCENT_OR_MORE';
  /**
   * Bid only on impressions that are at least 40% likely to be viewable.
   */
  public const VIEWABILITY_VIEWABILITY_40_PERCENT_OR_MORE = 'VIEWABILITY_40_PERCENT_OR_MORE';
  /**
   * Bid only on impressions that are at least 50% likely to be viewable.
   */
  public const VIEWABILITY_VIEWABILITY_50_PERCENT_OR_MORE = 'VIEWABILITY_50_PERCENT_OR_MORE';
  /**
   * Bid only on impressions that are at least 60% likely to be viewable.
   */
  public const VIEWABILITY_VIEWABILITY_60_PERCENT_OR_MORE = 'VIEWABILITY_60_PERCENT_OR_MORE';
  /**
   * Bid only on impressions that are at least 70% likely to be viewable.
   */
  public const VIEWABILITY_VIEWABILITY_70_PERCENT_OR_MORE = 'VIEWABILITY_70_PERCENT_OR_MORE';
  /**
   * Bid only on impressions that are at least 80% likely to be viewable.
   */
  public const VIEWABILITY_VIEWABILITY_80_PERCENT_OR_MORE = 'VIEWABILITY_80_PERCENT_OR_MORE';
  /**
   * Bid only on impressions that are at least 90% likely to be viewable.
   */
  public const VIEWABILITY_VIEWABILITY_90_PERCENT_OR_MORE = 'VIEWABILITY_90_PERCENT_OR_MORE';
  /**
   * Output only. The predicted viewability percentage.
   *
   * @var string
   */
  public $viewability;

  /**
   * Output only. The predicted viewability percentage.
   *
   * Accepted values: VIEWABILITY_UNSPECIFIED, VIEWABILITY_10_PERCENT_OR_MORE,
   * VIEWABILITY_20_PERCENT_OR_MORE, VIEWABILITY_30_PERCENT_OR_MORE,
   * VIEWABILITY_40_PERCENT_OR_MORE, VIEWABILITY_50_PERCENT_OR_MORE,
   * VIEWABILITY_60_PERCENT_OR_MORE, VIEWABILITY_70_PERCENT_OR_MORE,
   * VIEWABILITY_80_PERCENT_OR_MORE, VIEWABILITY_90_PERCENT_OR_MORE
   *
   * @param self::VIEWABILITY_* $viewability
   */
  public function setViewability($viewability)
  {
    $this->viewability = $viewability;
  }
  /**
   * @return self::VIEWABILITY_*
   */
  public function getViewability()
  {
    return $this->viewability;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ViewabilityTargetingOptionDetails::class, 'Google_Service_DisplayVideo_ViewabilityTargetingOptionDetails');
