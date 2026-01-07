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

class Kpi extends \Google\Model
{
  /**
   * KPI type is not specified or is unknown in this version.
   */
  public const KPI_TYPE_KPI_TYPE_UNSPECIFIED = 'KPI_TYPE_UNSPECIFIED';
  /**
   * The KPI is CPM (cost per mille).
   */
  public const KPI_TYPE_KPI_TYPE_CPM = 'KPI_TYPE_CPM';
  /**
   * The KPI is CPC (cost per click).
   */
  public const KPI_TYPE_KPI_TYPE_CPC = 'KPI_TYPE_CPC';
  /**
   * The KPI is CPA (cost per action).
   */
  public const KPI_TYPE_KPI_TYPE_CPA = 'KPI_TYPE_CPA';
  /**
   * The KPI is CTR (click-through rate) percentage.
   */
  public const KPI_TYPE_KPI_TYPE_CTR = 'KPI_TYPE_CTR';
  /**
   * The KPI is Viewability percentage.
   */
  public const KPI_TYPE_KPI_TYPE_VIEWABILITY = 'KPI_TYPE_VIEWABILITY';
  /**
   * The KPI is CPIAVC (cost per impression audible and visible at completion).
   */
  public const KPI_TYPE_KPI_TYPE_CPIAVC = 'KPI_TYPE_CPIAVC';
  /**
   * The KPI is CPE (cost per engagement).
   */
  public const KPI_TYPE_KPI_TYPE_CPE = 'KPI_TYPE_CPE';
  /**
   * The KPI is set in CPV (cost per view).
   */
  public const KPI_TYPE_KPI_TYPE_CPV = 'KPI_TYPE_CPV';
  /**
   * The KPI is click conversion rate (conversions per click) percentage.
   */
  public const KPI_TYPE_KPI_TYPE_CLICK_CVR = 'KPI_TYPE_CLICK_CVR';
  /**
   * The KPI is impression conversion rate (conversions per impression)
   * percentage.
   */
  public const KPI_TYPE_KPI_TYPE_IMPRESSION_CVR = 'KPI_TYPE_IMPRESSION_CVR';
  /**
   * The KPI is VCPM (cost per thousand viewable impressions).
   */
  public const KPI_TYPE_KPI_TYPE_VCPM = 'KPI_TYPE_VCPM';
  /**
   * The KPI is YouTube view rate (YouTube views per impression) percentage.
   */
  public const KPI_TYPE_KPI_TYPE_VTR = 'KPI_TYPE_VTR';
  /**
   * The KPI is audio completion rate (complete audio listens per impression)
   * percentage.
   */
  public const KPI_TYPE_KPI_TYPE_AUDIO_COMPLETION_RATE = 'KPI_TYPE_AUDIO_COMPLETION_RATE';
  /**
   * The KPI is video completion rate (complete video views per impression)
   * percentage.
   */
  public const KPI_TYPE_KPI_TYPE_VIDEO_COMPLETION_RATE = 'KPI_TYPE_VIDEO_COMPLETION_RATE';
  /**
   * The KPI is set in CPCL (cost per complete audio listen).
   */
  public const KPI_TYPE_KPI_TYPE_CPCL = 'KPI_TYPE_CPCL';
  /**
   * The KPI is set in CPCV (cost per complete video view).
   */
  public const KPI_TYPE_KPI_TYPE_CPCV = 'KPI_TYPE_CPCV';
  /**
   * The KPI is set in rate of time on screen 10+ seconds (Percentage of
   * measurable, non-skippable impressions that were on the screen for at least
   * 10 seconds).
   */
  public const KPI_TYPE_KPI_TYPE_TOS10 = 'KPI_TYPE_TOS10';
  /**
   * The KPI is set to maximize brand impact while prioritizing spending the
   * full budget.
   */
  public const KPI_TYPE_KPI_TYPE_MAXIMIZE_PACING = 'KPI_TYPE_MAXIMIZE_PACING';
  /**
   * The KPI is set in custom impression value divided by cost.
   */
  public const KPI_TYPE_KPI_TYPE_CUSTOM_IMPRESSION_VALUE_OVER_COST = 'KPI_TYPE_CUSTOM_IMPRESSION_VALUE_OVER_COST';
  /**
   * The KPI is some other value.
   */
  public const KPI_TYPE_KPI_TYPE_OTHER = 'KPI_TYPE_OTHER';
  /**
   * Optional. Custom Bidding Algorithm ID associated with
   * KPI_CUSTOM_IMPRESSION_VALUE_OVER_COST. This field is ignored if the proper
   * KPI is not selected.
   *
   * @var string
   */
  public $kpiAlgorithmId;
  /**
   * The goal amount, in micros of the advertiser's currency. Applicable when
   * kpi_type is one of: * `KPI_TYPE_CPM` * `KPI_TYPE_CPC` * `KPI_TYPE_CPA` *
   * `KPI_TYPE_CPIAVC` * `KPI_TYPE_VCPM` For example: 1500000 represents 1.5
   * standard units of the currency.
   *
   * @var string
   */
  public $kpiAmountMicros;
  /**
   * The decimal representation of the goal percentage in micros. Applicable
   * when kpi_type is one of: * `KPI_TYPE_CTR` * `KPI_TYPE_VIEWABILITY` *
   * `KPI_TYPE_CLICK_CVR` * `KPI_TYPE_IMPRESSION_CVR` * `KPI_TYPE_VTR` *
   * `KPI_TYPE_AUDIO_COMPLETION_RATE` * `KPI_TYPE_VIDEO_COMPLETION_RATE` For
   * example: 70000 represents 7% (decimal 0.07).
   *
   * @var string
   */
  public $kpiPercentageMicros;
  /**
   * A KPI string, which can be empty. Must be UTF-8 encoded with a length of no
   * more than 100 characters. Applicable when kpi_type is `KPI_TYPE_OTHER`.
   *
   * @var string
   */
  public $kpiString;
  /**
   * Required. The type of KPI.
   *
   * @var string
   */
  public $kpiType;

  /**
   * Optional. Custom Bidding Algorithm ID associated with
   * KPI_CUSTOM_IMPRESSION_VALUE_OVER_COST. This field is ignored if the proper
   * KPI is not selected.
   *
   * @param string $kpiAlgorithmId
   */
  public function setKpiAlgorithmId($kpiAlgorithmId)
  {
    $this->kpiAlgorithmId = $kpiAlgorithmId;
  }
  /**
   * @return string
   */
  public function getKpiAlgorithmId()
  {
    return $this->kpiAlgorithmId;
  }
  /**
   * The goal amount, in micros of the advertiser's currency. Applicable when
   * kpi_type is one of: * `KPI_TYPE_CPM` * `KPI_TYPE_CPC` * `KPI_TYPE_CPA` *
   * `KPI_TYPE_CPIAVC` * `KPI_TYPE_VCPM` For example: 1500000 represents 1.5
   * standard units of the currency.
   *
   * @param string $kpiAmountMicros
   */
  public function setKpiAmountMicros($kpiAmountMicros)
  {
    $this->kpiAmountMicros = $kpiAmountMicros;
  }
  /**
   * @return string
   */
  public function getKpiAmountMicros()
  {
    return $this->kpiAmountMicros;
  }
  /**
   * The decimal representation of the goal percentage in micros. Applicable
   * when kpi_type is one of: * `KPI_TYPE_CTR` * `KPI_TYPE_VIEWABILITY` *
   * `KPI_TYPE_CLICK_CVR` * `KPI_TYPE_IMPRESSION_CVR` * `KPI_TYPE_VTR` *
   * `KPI_TYPE_AUDIO_COMPLETION_RATE` * `KPI_TYPE_VIDEO_COMPLETION_RATE` For
   * example: 70000 represents 7% (decimal 0.07).
   *
   * @param string $kpiPercentageMicros
   */
  public function setKpiPercentageMicros($kpiPercentageMicros)
  {
    $this->kpiPercentageMicros = $kpiPercentageMicros;
  }
  /**
   * @return string
   */
  public function getKpiPercentageMicros()
  {
    return $this->kpiPercentageMicros;
  }
  /**
   * A KPI string, which can be empty. Must be UTF-8 encoded with a length of no
   * more than 100 characters. Applicable when kpi_type is `KPI_TYPE_OTHER`.
   *
   * @param string $kpiString
   */
  public function setKpiString($kpiString)
  {
    $this->kpiString = $kpiString;
  }
  /**
   * @return string
   */
  public function getKpiString()
  {
    return $this->kpiString;
  }
  /**
   * Required. The type of KPI.
   *
   * Accepted values: KPI_TYPE_UNSPECIFIED, KPI_TYPE_CPM, KPI_TYPE_CPC,
   * KPI_TYPE_CPA, KPI_TYPE_CTR, KPI_TYPE_VIEWABILITY, KPI_TYPE_CPIAVC,
   * KPI_TYPE_CPE, KPI_TYPE_CPV, KPI_TYPE_CLICK_CVR, KPI_TYPE_IMPRESSION_CVR,
   * KPI_TYPE_VCPM, KPI_TYPE_VTR, KPI_TYPE_AUDIO_COMPLETION_RATE,
   * KPI_TYPE_VIDEO_COMPLETION_RATE, KPI_TYPE_CPCL, KPI_TYPE_CPCV,
   * KPI_TYPE_TOS10, KPI_TYPE_MAXIMIZE_PACING,
   * KPI_TYPE_CUSTOM_IMPRESSION_VALUE_OVER_COST, KPI_TYPE_OTHER
   *
   * @param self::KPI_TYPE_* $kpiType
   */
  public function setKpiType($kpiType)
  {
    $this->kpiType = $kpiType;
  }
  /**
   * @return self::KPI_TYPE_*
   */
  public function getKpiType()
  {
    return $this->kpiType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Kpi::class, 'Google_Service_DisplayVideo_Kpi');
