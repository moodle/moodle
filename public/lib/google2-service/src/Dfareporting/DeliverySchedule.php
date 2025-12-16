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

class DeliverySchedule extends \Google\Model
{
  public const PRIORITY_AD_PRIORITY_01 = 'AD_PRIORITY_01';
  public const PRIORITY_AD_PRIORITY_02 = 'AD_PRIORITY_02';
  public const PRIORITY_AD_PRIORITY_03 = 'AD_PRIORITY_03';
  public const PRIORITY_AD_PRIORITY_04 = 'AD_PRIORITY_04';
  public const PRIORITY_AD_PRIORITY_05 = 'AD_PRIORITY_05';
  public const PRIORITY_AD_PRIORITY_06 = 'AD_PRIORITY_06';
  public const PRIORITY_AD_PRIORITY_07 = 'AD_PRIORITY_07';
  public const PRIORITY_AD_PRIORITY_08 = 'AD_PRIORITY_08';
  public const PRIORITY_AD_PRIORITY_09 = 'AD_PRIORITY_09';
  public const PRIORITY_AD_PRIORITY_10 = 'AD_PRIORITY_10';
  public const PRIORITY_AD_PRIORITY_11 = 'AD_PRIORITY_11';
  public const PRIORITY_AD_PRIORITY_12 = 'AD_PRIORITY_12';
  public const PRIORITY_AD_PRIORITY_13 = 'AD_PRIORITY_13';
  public const PRIORITY_AD_PRIORITY_14 = 'AD_PRIORITY_14';
  public const PRIORITY_AD_PRIORITY_15 = 'AD_PRIORITY_15';
  public const PRIORITY_AD_PRIORITY_16 = 'AD_PRIORITY_16';
  protected $frequencyCapType = FrequencyCap::class;
  protected $frequencyCapDataType = '';
  /**
   * Whether or not hard cutoff is enabled. If true, the ad will not serve after
   * the end date and time. Otherwise the ad will continue to be served until it
   * has reached its delivery goals.
   *
   * @var bool
   */
  public $hardCutoff;
  /**
   * Impression ratio for this ad. This ratio determines how often each ad is
   * served relative to the others. For example, if ad A has an impression ratio
   * of 1 and ad B has an impression ratio of 3, then Campaign Manager will
   * serve ad B three times as often as ad A. Acceptable values are 1 to 10,
   * inclusive.
   *
   * @var string
   */
  public $impressionRatio;
  /**
   * Serving priority of an ad, with respect to other ads. The lower the
   * priority number, the greater the priority with which it is served.
   *
   * @var string
   */
  public $priority;

  /**
   * Limit on the number of times an individual user can be served the ad within
   * a specified period of time.
   *
   * @param FrequencyCap $frequencyCap
   */
  public function setFrequencyCap(FrequencyCap $frequencyCap)
  {
    $this->frequencyCap = $frequencyCap;
  }
  /**
   * @return FrequencyCap
   */
  public function getFrequencyCap()
  {
    return $this->frequencyCap;
  }
  /**
   * Whether or not hard cutoff is enabled. If true, the ad will not serve after
   * the end date and time. Otherwise the ad will continue to be served until it
   * has reached its delivery goals.
   *
   * @param bool $hardCutoff
   */
  public function setHardCutoff($hardCutoff)
  {
    $this->hardCutoff = $hardCutoff;
  }
  /**
   * @return bool
   */
  public function getHardCutoff()
  {
    return $this->hardCutoff;
  }
  /**
   * Impression ratio for this ad. This ratio determines how often each ad is
   * served relative to the others. For example, if ad A has an impression ratio
   * of 1 and ad B has an impression ratio of 3, then Campaign Manager will
   * serve ad B three times as often as ad A. Acceptable values are 1 to 10,
   * inclusive.
   *
   * @param string $impressionRatio
   */
  public function setImpressionRatio($impressionRatio)
  {
    $this->impressionRatio = $impressionRatio;
  }
  /**
   * @return string
   */
  public function getImpressionRatio()
  {
    return $this->impressionRatio;
  }
  /**
   * Serving priority of an ad, with respect to other ads. The lower the
   * priority number, the greater the priority with which it is served.
   *
   * Accepted values: AD_PRIORITY_01, AD_PRIORITY_02, AD_PRIORITY_03,
   * AD_PRIORITY_04, AD_PRIORITY_05, AD_PRIORITY_06, AD_PRIORITY_07,
   * AD_PRIORITY_08, AD_PRIORITY_09, AD_PRIORITY_10, AD_PRIORITY_11,
   * AD_PRIORITY_12, AD_PRIORITY_13, AD_PRIORITY_14, AD_PRIORITY_15,
   * AD_PRIORITY_16
   *
   * @param self::PRIORITY_* $priority
   */
  public function setPriority($priority)
  {
    $this->priority = $priority;
  }
  /**
   * @return self::PRIORITY_*
   */
  public function getPriority()
  {
    return $this->priority;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DeliverySchedule::class, 'Google_Service_Dfareporting_DeliverySchedule');
