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

class AlgorithmRulesSignalValue extends \Google\Model
{
  /**
   * Unknown signal.
   */
  public const ACTIVE_VIEW_SIGNAL_ACTIVE_VIEW_SIGNAL_UNSPECIFIED = 'ACTIVE_VIEW_SIGNAL_UNSPECIFIED';
  /**
   * Whether Active View detects that your ad has been viewed. Value is stored
   * in the boolValue field of the comparison value.
   */
  public const ACTIVE_VIEW_SIGNAL_ACTIVE_VIEW_VIEWED = 'ACTIVE_VIEW_VIEWED';
  /**
   * Whether Active View detects that your ad was audible. Value is stored in
   * the boolValue field of the comparison value.
   */
  public const ACTIVE_VIEW_SIGNAL_AUDIBLE = 'AUDIBLE';
  /**
   * Whether the video was completed. Value is stored in the boolValue field of
   * the comparison value.
   */
  public const ACTIVE_VIEW_SIGNAL_VIDEO_COMPLETED = 'VIDEO_COMPLETED';
  /**
   * The time the ad was on screen in seconds. Value is stored in the int64Value
   * field of the comparison value.
   */
  public const ACTIVE_VIEW_SIGNAL_TIME_ON_SCREEN = 'TIME_ON_SCREEN';
  /**
   * The size of the video player displaying the ad. Value is stored in the
   * videoPlayerSizeValue field of the comparison value.
   */
  public const ACTIVE_VIEW_SIGNAL_VIDEO_PLAYER_SIZE = 'VIDEO_PLAYER_SIZE';
  /**
   * Whether the ad was completed in view and audible. Value is stored in the
   * boolValue field of the comparison value.
   */
  public const ACTIVE_VIEW_SIGNAL_COMPLETED_IN_VIEW_AUDIBLE = 'COMPLETED_IN_VIEW_AUDIBLE';
  /**
   * Signal based on active views. Only `TIME_ON_SCREEN` is supported. This
   * field is only supported for allowlisted partners.
   *
   * @var string
   */
  public $activeViewSignal;
  protected $floodlightActivityConversionSignalType = AlgorithmRulesFloodlightActivityConversionSignal::class;
  protected $floodlightActivityConversionSignalDataType = '';
  /**
   * Value to use as result.
   *
   * @var 
   */
  public $number;

  /**
   * Signal based on active views. Only `TIME_ON_SCREEN` is supported. This
   * field is only supported for allowlisted partners.
   *
   * Accepted values: ACTIVE_VIEW_SIGNAL_UNSPECIFIED, ACTIVE_VIEW_VIEWED,
   * AUDIBLE, VIDEO_COMPLETED, TIME_ON_SCREEN, VIDEO_PLAYER_SIZE,
   * COMPLETED_IN_VIEW_AUDIBLE
   *
   * @param self::ACTIVE_VIEW_SIGNAL_* $activeViewSignal
   */
  public function setActiveViewSignal($activeViewSignal)
  {
    $this->activeViewSignal = $activeViewSignal;
  }
  /**
   * @return self::ACTIVE_VIEW_SIGNAL_*
   */
  public function getActiveViewSignal()
  {
    return $this->activeViewSignal;
  }
  /**
   * Signal based on floodlight conversion events. This field is only supported
   * for allowlisted partners.
   *
   * @param AlgorithmRulesFloodlightActivityConversionSignal $floodlightActivityConversionSignal
   */
  public function setFloodlightActivityConversionSignal(AlgorithmRulesFloodlightActivityConversionSignal $floodlightActivityConversionSignal)
  {
    $this->floodlightActivityConversionSignal = $floodlightActivityConversionSignal;
  }
  /**
   * @return AlgorithmRulesFloodlightActivityConversionSignal
   */
  public function getFloodlightActivityConversionSignal()
  {
    return $this->floodlightActivityConversionSignal;
  }
  public function setNumber($number)
  {
    $this->number = $number;
  }
  public function getNumber()
  {
    return $this->number;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AlgorithmRulesSignalValue::class, 'Google_Service_DisplayVideo_AlgorithmRulesSignalValue');
