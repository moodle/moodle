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

class ConversionCountingConfig extends \Google\Collection
{
  protected $collection_key = 'floodlightActivityConfigs';
  protected $floodlightActivityConfigsType = TrackingFloodlightActivityConfig::class;
  protected $floodlightActivityConfigsDataType = 'array';
  /**
   * The percentage of post-view conversions to count, in millis (1/1000 of a
   * percent). Must be between 0 and 100000 inclusive. For example, to track 50%
   * of the post-click conversions, set a value of 50000.
   *
   * @var string
   */
  public $postViewCountPercentageMillis;

  /**
   * The Floodlight activity configs used to track conversions. The number of
   * conversions counted is the sum of all of the conversions counted by all of
   * the Floodlight activity IDs specified in this field. This field can't be
   * updated if a custom bidding algorithm is assigned to the line item. If you
   * set this field and assign a custom bidding algorithm in the same request,
   * the floodlight activities must match the ones used by the custom bidding
   * algorithm.
   *
   * @param TrackingFloodlightActivityConfig[] $floodlightActivityConfigs
   */
  public function setFloodlightActivityConfigs($floodlightActivityConfigs)
  {
    $this->floodlightActivityConfigs = $floodlightActivityConfigs;
  }
  /**
   * @return TrackingFloodlightActivityConfig[]
   */
  public function getFloodlightActivityConfigs()
  {
    return $this->floodlightActivityConfigs;
  }
  /**
   * The percentage of post-view conversions to count, in millis (1/1000 of a
   * percent). Must be between 0 and 100000 inclusive. For example, to track 50%
   * of the post-click conversions, set a value of 50000.
   *
   * @param string $postViewCountPercentageMillis
   */
  public function setPostViewCountPercentageMillis($postViewCountPercentageMillis)
  {
    $this->postViewCountPercentageMillis = $postViewCountPercentageMillis;
  }
  /**
   * @return string
   */
  public function getPostViewCountPercentageMillis()
  {
    return $this->postViewCountPercentageMillis;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ConversionCountingConfig::class, 'Google_Service_DisplayVideo_ConversionCountingConfig');
