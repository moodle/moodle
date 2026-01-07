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

class PerformanceGoal extends \Google\Model
{
  /**
   * Performance goal type is not specified or is unknown in this version.
   */
  public const PERFORMANCE_GOAL_TYPE_PERFORMANCE_GOAL_TYPE_UNSPECIFIED = 'PERFORMANCE_GOAL_TYPE_UNSPECIFIED';
  /**
   * The performance goal is set in CPM (cost per mille).
   */
  public const PERFORMANCE_GOAL_TYPE_PERFORMANCE_GOAL_TYPE_CPM = 'PERFORMANCE_GOAL_TYPE_CPM';
  /**
   * The performance goal is set in CPC (cost per click).
   */
  public const PERFORMANCE_GOAL_TYPE_PERFORMANCE_GOAL_TYPE_CPC = 'PERFORMANCE_GOAL_TYPE_CPC';
  /**
   * The performance goal is set in CPA (cost per action).
   */
  public const PERFORMANCE_GOAL_TYPE_PERFORMANCE_GOAL_TYPE_CPA = 'PERFORMANCE_GOAL_TYPE_CPA';
  /**
   * The performance goal is set in CTR (click-through rate) percentage.
   */
  public const PERFORMANCE_GOAL_TYPE_PERFORMANCE_GOAL_TYPE_CTR = 'PERFORMANCE_GOAL_TYPE_CTR';
  /**
   * The performance goal is set in Viewability percentage.
   */
  public const PERFORMANCE_GOAL_TYPE_PERFORMANCE_GOAL_TYPE_VIEWABILITY = 'PERFORMANCE_GOAL_TYPE_VIEWABILITY';
  /**
   * The performance goal is set as CPIAVC (cost per impression audible and
   * visible at completion).
   */
  public const PERFORMANCE_GOAL_TYPE_PERFORMANCE_GOAL_TYPE_CPIAVC = 'PERFORMANCE_GOAL_TYPE_CPIAVC';
  /**
   * The performance goal is set in CPE (cost per engagement).
   */
  public const PERFORMANCE_GOAL_TYPE_PERFORMANCE_GOAL_TYPE_CPE = 'PERFORMANCE_GOAL_TYPE_CPE';
  /**
   * The performance goal is set in CPV (cost per view).
   */
  public const PERFORMANCE_GOAL_TYPE_PERFORMANCE_GOAL_TYPE_CPV = 'PERFORMANCE_GOAL_TYPE_CPV';
  /**
   * The performance goal is set in click conversion rate (conversions per
   * click) percentage.
   */
  public const PERFORMANCE_GOAL_TYPE_PERFORMANCE_GOAL_TYPE_CLICK_CVR = 'PERFORMANCE_GOAL_TYPE_CLICK_CVR';
  /**
   * The performance goal is set in impression conversion rate (conversions per
   * impression) percentage.
   */
  public const PERFORMANCE_GOAL_TYPE_PERFORMANCE_GOAL_TYPE_IMPRESSION_CVR = 'PERFORMANCE_GOAL_TYPE_IMPRESSION_CVR';
  /**
   * The performance goal is set in VCPM (cost per thousand viewable
   * impressions).
   */
  public const PERFORMANCE_GOAL_TYPE_PERFORMANCE_GOAL_TYPE_VCPM = 'PERFORMANCE_GOAL_TYPE_VCPM';
  /**
   * The performance goal is set in YouTube view rate (YouTube views per
   * impression) percentage.
   */
  public const PERFORMANCE_GOAL_TYPE_PERFORMANCE_GOAL_TYPE_VTR = 'PERFORMANCE_GOAL_TYPE_VTR';
  /**
   * The performance goal is set in audio completion rate (complete audio
   * listens per impression) percentage.
   */
  public const PERFORMANCE_GOAL_TYPE_PERFORMANCE_GOAL_TYPE_AUDIO_COMPLETION_RATE = 'PERFORMANCE_GOAL_TYPE_AUDIO_COMPLETION_RATE';
  /**
   * The performance goal is set in video completion rate (complete video views
   * per impression) percentage.
   */
  public const PERFORMANCE_GOAL_TYPE_PERFORMANCE_GOAL_TYPE_VIDEO_COMPLETION_RATE = 'PERFORMANCE_GOAL_TYPE_VIDEO_COMPLETION_RATE';
  /**
   * The performance goal is set to Other.
   */
  public const PERFORMANCE_GOAL_TYPE_PERFORMANCE_GOAL_TYPE_OTHER = 'PERFORMANCE_GOAL_TYPE_OTHER';
  /**
   * The goal amount, in micros of the advertiser's currency. Applicable when
   * performance_goal_type is one of: * `PERFORMANCE_GOAL_TYPE_CPM` *
   * `PERFORMANCE_GOAL_TYPE_CPC` * `PERFORMANCE_GOAL_TYPE_CPA` *
   * `PERFORMANCE_GOAL_TYPE_CPIAVC` * `PERFORMANCE_GOAL_TYPE_VCPM` For example
   * 1500000 represents 1.5 standard units of the currency.
   *
   * @var string
   */
  public $performanceGoalAmountMicros;
  /**
   * The decimal representation of the goal percentage in micros. Applicable
   * when performance_goal_type is one of: * `PERFORMANCE_GOAL_TYPE_CTR` *
   * `PERFORMANCE_GOAL_TYPE_VIEWABILITY` * `PERFORMANCE_GOAL_TYPE_CLICK_CVR` *
   * `PERFORMANCE_GOAL_TYPE_IMPRESSION_CVR` * `PERFORMANCE_GOAL_TYPE_VTR` *
   * `PERFORMANCE_GOAL_TYPE_AUDIO_COMPLETION_RATE` *
   * `PERFORMANCE_GOAL_TYPE_VIDEO_COMPLETION_RATE` For example, 70000 represents
   * 7% (decimal 0.07).
   *
   * @var string
   */
  public $performanceGoalPercentageMicros;
  /**
   * A key performance indicator (KPI) string, which can be empty. Must be UTF-8
   * encoded with a length of no more than 100 characters. Applicable when
   * performance_goal_type is set to `PERFORMANCE_GOAL_TYPE_OTHER`.
   *
   * @var string
   */
  public $performanceGoalString;
  /**
   * Required. The type of the performance goal.
   *
   * @var string
   */
  public $performanceGoalType;

  /**
   * The goal amount, in micros of the advertiser's currency. Applicable when
   * performance_goal_type is one of: * `PERFORMANCE_GOAL_TYPE_CPM` *
   * `PERFORMANCE_GOAL_TYPE_CPC` * `PERFORMANCE_GOAL_TYPE_CPA` *
   * `PERFORMANCE_GOAL_TYPE_CPIAVC` * `PERFORMANCE_GOAL_TYPE_VCPM` For example
   * 1500000 represents 1.5 standard units of the currency.
   *
   * @param string $performanceGoalAmountMicros
   */
  public function setPerformanceGoalAmountMicros($performanceGoalAmountMicros)
  {
    $this->performanceGoalAmountMicros = $performanceGoalAmountMicros;
  }
  /**
   * @return string
   */
  public function getPerformanceGoalAmountMicros()
  {
    return $this->performanceGoalAmountMicros;
  }
  /**
   * The decimal representation of the goal percentage in micros. Applicable
   * when performance_goal_type is one of: * `PERFORMANCE_GOAL_TYPE_CTR` *
   * `PERFORMANCE_GOAL_TYPE_VIEWABILITY` * `PERFORMANCE_GOAL_TYPE_CLICK_CVR` *
   * `PERFORMANCE_GOAL_TYPE_IMPRESSION_CVR` * `PERFORMANCE_GOAL_TYPE_VTR` *
   * `PERFORMANCE_GOAL_TYPE_AUDIO_COMPLETION_RATE` *
   * `PERFORMANCE_GOAL_TYPE_VIDEO_COMPLETION_RATE` For example, 70000 represents
   * 7% (decimal 0.07).
   *
   * @param string $performanceGoalPercentageMicros
   */
  public function setPerformanceGoalPercentageMicros($performanceGoalPercentageMicros)
  {
    $this->performanceGoalPercentageMicros = $performanceGoalPercentageMicros;
  }
  /**
   * @return string
   */
  public function getPerformanceGoalPercentageMicros()
  {
    return $this->performanceGoalPercentageMicros;
  }
  /**
   * A key performance indicator (KPI) string, which can be empty. Must be UTF-8
   * encoded with a length of no more than 100 characters. Applicable when
   * performance_goal_type is set to `PERFORMANCE_GOAL_TYPE_OTHER`.
   *
   * @param string $performanceGoalString
   */
  public function setPerformanceGoalString($performanceGoalString)
  {
    $this->performanceGoalString = $performanceGoalString;
  }
  /**
   * @return string
   */
  public function getPerformanceGoalString()
  {
    return $this->performanceGoalString;
  }
  /**
   * Required. The type of the performance goal.
   *
   * Accepted values: PERFORMANCE_GOAL_TYPE_UNSPECIFIED,
   * PERFORMANCE_GOAL_TYPE_CPM, PERFORMANCE_GOAL_TYPE_CPC,
   * PERFORMANCE_GOAL_TYPE_CPA, PERFORMANCE_GOAL_TYPE_CTR,
   * PERFORMANCE_GOAL_TYPE_VIEWABILITY, PERFORMANCE_GOAL_TYPE_CPIAVC,
   * PERFORMANCE_GOAL_TYPE_CPE, PERFORMANCE_GOAL_TYPE_CPV,
   * PERFORMANCE_GOAL_TYPE_CLICK_CVR, PERFORMANCE_GOAL_TYPE_IMPRESSION_CVR,
   * PERFORMANCE_GOAL_TYPE_VCPM, PERFORMANCE_GOAL_TYPE_VTR,
   * PERFORMANCE_GOAL_TYPE_AUDIO_COMPLETION_RATE,
   * PERFORMANCE_GOAL_TYPE_VIDEO_COMPLETION_RATE, PERFORMANCE_GOAL_TYPE_OTHER
   *
   * @param self::PERFORMANCE_GOAL_TYPE_* $performanceGoalType
   */
  public function setPerformanceGoalType($performanceGoalType)
  {
    $this->performanceGoalType = $performanceGoalType;
  }
  /**
   * @return self::PERFORMANCE_GOAL_TYPE_*
   */
  public function getPerformanceGoalType()
  {
    return $this->performanceGoalType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PerformanceGoal::class, 'Google_Service_DisplayVideo_PerformanceGoal');
