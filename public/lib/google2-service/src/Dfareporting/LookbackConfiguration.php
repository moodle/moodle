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

class LookbackConfiguration extends \Google\Model
{
  /**
   * Lookback window, in days, from the last time a given user clicked on one of
   * your ads. If you enter 0, clicks will not be considered as triggering
   * events for floodlight tracking. If you leave this field blank, the default
   * value for your account will be used. Acceptable values are 0 to 90,
   * inclusive.
   *
   * @var int
   */
  public $clickDuration;
  /**
   * Lookback window, in days, from the last time a given user viewed one of
   * your ads. If you enter 0, impressions will not be considered as triggering
   * events for floodlight tracking. If you leave this field blank, the default
   * value for your account will be used. Acceptable values are 0 to 90,
   * inclusive.
   *
   * @var int
   */
  public $postImpressionActivitiesDuration;

  /**
   * Lookback window, in days, from the last time a given user clicked on one of
   * your ads. If you enter 0, clicks will not be considered as triggering
   * events for floodlight tracking. If you leave this field blank, the default
   * value for your account will be used. Acceptable values are 0 to 90,
   * inclusive.
   *
   * @param int $clickDuration
   */
  public function setClickDuration($clickDuration)
  {
    $this->clickDuration = $clickDuration;
  }
  /**
   * @return int
   */
  public function getClickDuration()
  {
    return $this->clickDuration;
  }
  /**
   * Lookback window, in days, from the last time a given user viewed one of
   * your ads. If you enter 0, impressions will not be considered as triggering
   * events for floodlight tracking. If you leave this field blank, the default
   * value for your account will be used. Acceptable values are 0 to 90,
   * inclusive.
   *
   * @param int $postImpressionActivitiesDuration
   */
  public function setPostImpressionActivitiesDuration($postImpressionActivitiesDuration)
  {
    $this->postImpressionActivitiesDuration = $postImpressionActivitiesDuration;
  }
  /**
   * @return int
   */
  public function getPostImpressionActivitiesDuration()
  {
    return $this->postImpressionActivitiesDuration;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LookbackConfiguration::class, 'Google_Service_Dfareporting_LookbackConfiguration');
