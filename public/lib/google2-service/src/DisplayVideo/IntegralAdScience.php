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

class IntegralAdScience extends \Google\Collection
{
  /**
   * This enum is only a placeholder and it doesn't specify any display
   * viewability options.
   */
  public const DISPLAY_VIEWABILITY_PERFORMANCE_VIEWABILITY_UNSPECIFIED = 'PERFORMANCE_VIEWABILITY_UNSPECIFIED';
  /**
   * Target 40% Viewability or Higher.
   */
  public const DISPLAY_VIEWABILITY_PERFORMANCE_VIEWABILITY_40 = 'PERFORMANCE_VIEWABILITY_40';
  /**
   * Target 50% Viewability or Higher.
   */
  public const DISPLAY_VIEWABILITY_PERFORMANCE_VIEWABILITY_50 = 'PERFORMANCE_VIEWABILITY_50';
  /**
   * Target 60% Viewability or Higher.
   */
  public const DISPLAY_VIEWABILITY_PERFORMANCE_VIEWABILITY_60 = 'PERFORMANCE_VIEWABILITY_60';
  /**
   * Target 70% Viewability or Higher.
   */
  public const DISPLAY_VIEWABILITY_PERFORMANCE_VIEWABILITY_70 = 'PERFORMANCE_VIEWABILITY_70';
  /**
   * This enum is only a placeholder and it doesn't specify any ad fraud
   * prevention options.
   */
  public const EXCLUDED_AD_FRAUD_RISK_SUSPICIOUS_ACTIVITY_UNSPECIFIED = 'SUSPICIOUS_ACTIVITY_UNSPECIFIED';
  /**
   * Ad Fraud - Exclude High Risk.
   */
  public const EXCLUDED_AD_FRAUD_RISK_SUSPICIOUS_ACTIVITY_HR = 'SUSPICIOUS_ACTIVITY_HR';
  /**
   * Ad Fraud - Exclude High and Moderate Risk.
   */
  public const EXCLUDED_AD_FRAUD_RISK_SUSPICIOUS_ACTIVITY_HMR = 'SUSPICIOUS_ACTIVITY_HMR';
  /**
   * Ad Fraud - Exclude Fraudulent Device.
   */
  public const EXCLUDED_AD_FRAUD_RISK_SUSPICIOUS_ACTIVITY_FD = 'SUSPICIOUS_ACTIVITY_FD';
  /**
   * This enum is only a placeholder and it doesn't specify any adult options.
   */
  public const EXCLUDED_ADULT_RISK_ADULT_UNSPECIFIED = 'ADULT_UNSPECIFIED';
  /**
   * Adult - Exclude High Risk.
   */
  public const EXCLUDED_ADULT_RISK_ADULT_HR = 'ADULT_HR';
  /**
   * Adult - Exclude High and Moderate Risk.
   */
  public const EXCLUDED_ADULT_RISK_ADULT_HMR = 'ADULT_HMR';
  /**
   * This enum is only a placeholder and it doesn't specify any alcohol options.
   */
  public const EXCLUDED_ALCOHOL_RISK_ALCOHOL_UNSPECIFIED = 'ALCOHOL_UNSPECIFIED';
  /**
   * Alcohol - Exclude High Risk.
   */
  public const EXCLUDED_ALCOHOL_RISK_ALCOHOL_HR = 'ALCOHOL_HR';
  /**
   * Alcohol - Exclude High and Moderate Risk.
   */
  public const EXCLUDED_ALCOHOL_RISK_ALCOHOL_HMR = 'ALCOHOL_HMR';
  /**
   * This enum is only a placeholder and it doesn't specify any drugs options.
   */
  public const EXCLUDED_DRUGS_RISK_DRUGS_UNSPECIFIED = 'DRUGS_UNSPECIFIED';
  /**
   * Drugs - Exclude High Risk.
   */
  public const EXCLUDED_DRUGS_RISK_DRUGS_HR = 'DRUGS_HR';
  /**
   * Drugs - Exclude High and Moderate Risk.
   */
  public const EXCLUDED_DRUGS_RISK_DRUGS_HMR = 'DRUGS_HMR';
  /**
   * This enum is only a placeholder and it doesn't specify any gambling
   * options.
   */
  public const EXCLUDED_GAMBLING_RISK_GAMBLING_UNSPECIFIED = 'GAMBLING_UNSPECIFIED';
  /**
   * Gambling - Exclude High Risk.
   */
  public const EXCLUDED_GAMBLING_RISK_GAMBLING_HR = 'GAMBLING_HR';
  /**
   * Gambling - Exclude High and Moderate Risk.
   */
  public const EXCLUDED_GAMBLING_RISK_GAMBLING_HMR = 'GAMBLING_HMR';
  /**
   * This enum is only a placeholder and it doesn't specify any hate speech
   * options.
   */
  public const EXCLUDED_HATE_SPEECH_RISK_HATE_SPEECH_UNSPECIFIED = 'HATE_SPEECH_UNSPECIFIED';
  /**
   * Hate Speech - Exclude High Risk.
   */
  public const EXCLUDED_HATE_SPEECH_RISK_HATE_SPEECH_HR = 'HATE_SPEECH_HR';
  /**
   * Hate Speech - Exclude High and Moderate Risk.
   */
  public const EXCLUDED_HATE_SPEECH_RISK_HATE_SPEECH_HMR = 'HATE_SPEECH_HMR';
  /**
   * This enum is only a placeholder and it doesn't specify any illegal
   * downloads options.
   */
  public const EXCLUDED_ILLEGAL_DOWNLOADS_RISK_ILLEGAL_DOWNLOADS_UNSPECIFIED = 'ILLEGAL_DOWNLOADS_UNSPECIFIED';
  /**
   * Illegal Downloads - Exclude High Risk.
   */
  public const EXCLUDED_ILLEGAL_DOWNLOADS_RISK_ILLEGAL_DOWNLOADS_HR = 'ILLEGAL_DOWNLOADS_HR';
  /**
   * Illegal Downloads - Exclude High and Moderate Risk.
   */
  public const EXCLUDED_ILLEGAL_DOWNLOADS_RISK_ILLEGAL_DOWNLOADS_HMR = 'ILLEGAL_DOWNLOADS_HMR';
  /**
   * This enum is only a placeholder and it doesn't specify any language
   * options.
   */
  public const EXCLUDED_OFFENSIVE_LANGUAGE_RISK_OFFENSIVE_LANGUAGE_UNSPECIFIED = 'OFFENSIVE_LANGUAGE_UNSPECIFIED';
  /**
   * Offensive Language - Exclude High Risk.
   */
  public const EXCLUDED_OFFENSIVE_LANGUAGE_RISK_OFFENSIVE_LANGUAGE_HR = 'OFFENSIVE_LANGUAGE_HR';
  /**
   * Offensive Language - Exclude High and Moderate Risk.
   */
  public const EXCLUDED_OFFENSIVE_LANGUAGE_RISK_OFFENSIVE_LANGUAGE_HMR = 'OFFENSIVE_LANGUAGE_HMR';
  /**
   * This enum is only a placeholder and it doesn't specify any violence
   * options.
   */
  public const EXCLUDED_VIOLENCE_RISK_VIOLENCE_UNSPECIFIED = 'VIOLENCE_UNSPECIFIED';
  /**
   * Violence - Exclude High Risk.
   */
  public const EXCLUDED_VIOLENCE_RISK_VIOLENCE_HR = 'VIOLENCE_HR';
  /**
   * Violence - Exclude High and Moderate Risk.
   */
  public const EXCLUDED_VIOLENCE_RISK_VIOLENCE_HMR = 'VIOLENCE_HMR';
  /**
   * This enum is only a placeholder and it doesn't specify any true advertising
   * quality scores.
   */
  public const TRAQ_SCORE_OPTION_TRAQ_UNSPECIFIED = 'TRAQ_UNSPECIFIED';
  /**
   * TRAQ score 250-1000.
   */
  public const TRAQ_SCORE_OPTION_TRAQ_250 = 'TRAQ_250';
  /**
   * TRAQ score 500-1000.
   */
  public const TRAQ_SCORE_OPTION_TRAQ_500 = 'TRAQ_500';
  /**
   * TRAQ score 600-1000.
   */
  public const TRAQ_SCORE_OPTION_TRAQ_600 = 'TRAQ_600';
  /**
   * TRAQ score 700-1000.
   */
  public const TRAQ_SCORE_OPTION_TRAQ_700 = 'TRAQ_700';
  /**
   * TRAQ score 750-1000.
   */
  public const TRAQ_SCORE_OPTION_TRAQ_750 = 'TRAQ_750';
  /**
   * TRAQ score 875-1000.
   */
  public const TRAQ_SCORE_OPTION_TRAQ_875 = 'TRAQ_875';
  /**
   * TRAQ score 1000.
   */
  public const TRAQ_SCORE_OPTION_TRAQ_1000 = 'TRAQ_1000';
  /**
   * This enum is only a placeholder and it doesn't specify any video
   * viewability options.
   */
  public const VIDEO_VIEWABILITY_VIDEO_VIEWABILITY_UNSPECIFIED = 'VIDEO_VIEWABILITY_UNSPECIFIED';
  /**
   * 40%+ in view (IAB video viewability standard).
   */
  public const VIDEO_VIEWABILITY_VIDEO_VIEWABILITY_40 = 'VIDEO_VIEWABILITY_40';
  /**
   * 50%+ in view (IAB video viewability standard).
   */
  public const VIDEO_VIEWABILITY_VIDEO_VIEWABILITY_50 = 'VIDEO_VIEWABILITY_50';
  /**
   * 60%+ in view (IAB video viewability standard).
   */
  public const VIDEO_VIEWABILITY_VIDEO_VIEWABILITY_60 = 'VIDEO_VIEWABILITY_60';
  /**
   * 70%+ in view (IAB video viewability standard).
   */
  public const VIDEO_VIEWABILITY_VIDEO_VIEWABILITY_70 = 'VIDEO_VIEWABILITY_70';
  protected $collection_key = 'qualitySyncCustomSegmentId';
  /**
   * The custom segment ID provided by Integral Ad Science. The ID must be
   * between `1000001` and `1999999` or `3000001` and `3999999`, inclusive.
   *
   * @var string[]
   */
  public $customSegmentId;
  /**
   * Display Viewability section (applicable to display line items only).
   *
   * @var string
   */
  public $displayViewability;
  /**
   * Brand Safety - **Unrateable**.
   *
   * @var bool
   */
  public $excludeUnrateable;
  /**
   * Ad Fraud settings.
   *
   * @var string
   */
  public $excludedAdFraudRisk;
  /**
   * Brand Safety - **Adult content**.
   *
   * @var string
   */
  public $excludedAdultRisk;
  /**
   * Brand Safety - **Alcohol**.
   *
   * @var string
   */
  public $excludedAlcoholRisk;
  /**
   * Brand Safety - **Drugs**.
   *
   * @var string
   */
  public $excludedDrugsRisk;
  /**
   * Brand Safety - **Gambling**.
   *
   * @var string
   */
  public $excludedGamblingRisk;
  /**
   * Brand Safety - **Hate speech**.
   *
   * @var string
   */
  public $excludedHateSpeechRisk;
  /**
   * Brand Safety - **Illegal downloads**.
   *
   * @var string
   */
  public $excludedIllegalDownloadsRisk;
  /**
   * Brand Safety - **Offensive language**.
   *
   * @var string
   */
  public $excludedOffensiveLanguageRisk;
  /**
   * Brand Safety - **Violence**.
   *
   * @var string
   */
  public $excludedViolenceRisk;
  /**
   * Optional. The quality sync custom segment ID provided by Integral Ad
   * Science. The ID must be between `3000000` and `4999999`, inclusive.
   *
   * @var string[]
   */
  public $qualitySyncCustomSegmentId;
  /**
   * True advertising quality (applicable to Display line items only).
   *
   * @var string
   */
  public $traqScoreOption;
  /**
   * Video Viewability Section (applicable to video line items only).
   *
   * @var string
   */
  public $videoViewability;

  /**
   * The custom segment ID provided by Integral Ad Science. The ID must be
   * between `1000001` and `1999999` or `3000001` and `3999999`, inclusive.
   *
   * @param string[] $customSegmentId
   */
  public function setCustomSegmentId($customSegmentId)
  {
    $this->customSegmentId = $customSegmentId;
  }
  /**
   * @return string[]
   */
  public function getCustomSegmentId()
  {
    return $this->customSegmentId;
  }
  /**
   * Display Viewability section (applicable to display line items only).
   *
   * Accepted values: PERFORMANCE_VIEWABILITY_UNSPECIFIED,
   * PERFORMANCE_VIEWABILITY_40, PERFORMANCE_VIEWABILITY_50,
   * PERFORMANCE_VIEWABILITY_60, PERFORMANCE_VIEWABILITY_70
   *
   * @param self::DISPLAY_VIEWABILITY_* $displayViewability
   */
  public function setDisplayViewability($displayViewability)
  {
    $this->displayViewability = $displayViewability;
  }
  /**
   * @return self::DISPLAY_VIEWABILITY_*
   */
  public function getDisplayViewability()
  {
    return $this->displayViewability;
  }
  /**
   * Brand Safety - **Unrateable**.
   *
   * @param bool $excludeUnrateable
   */
  public function setExcludeUnrateable($excludeUnrateable)
  {
    $this->excludeUnrateable = $excludeUnrateable;
  }
  /**
   * @return bool
   */
  public function getExcludeUnrateable()
  {
    return $this->excludeUnrateable;
  }
  /**
   * Ad Fraud settings.
   *
   * Accepted values: SUSPICIOUS_ACTIVITY_UNSPECIFIED, SUSPICIOUS_ACTIVITY_HR,
   * SUSPICIOUS_ACTIVITY_HMR, SUSPICIOUS_ACTIVITY_FD
   *
   * @param self::EXCLUDED_AD_FRAUD_RISK_* $excludedAdFraudRisk
   */
  public function setExcludedAdFraudRisk($excludedAdFraudRisk)
  {
    $this->excludedAdFraudRisk = $excludedAdFraudRisk;
  }
  /**
   * @return self::EXCLUDED_AD_FRAUD_RISK_*
   */
  public function getExcludedAdFraudRisk()
  {
    return $this->excludedAdFraudRisk;
  }
  /**
   * Brand Safety - **Adult content**.
   *
   * Accepted values: ADULT_UNSPECIFIED, ADULT_HR, ADULT_HMR
   *
   * @param self::EXCLUDED_ADULT_RISK_* $excludedAdultRisk
   */
  public function setExcludedAdultRisk($excludedAdultRisk)
  {
    $this->excludedAdultRisk = $excludedAdultRisk;
  }
  /**
   * @return self::EXCLUDED_ADULT_RISK_*
   */
  public function getExcludedAdultRisk()
  {
    return $this->excludedAdultRisk;
  }
  /**
   * Brand Safety - **Alcohol**.
   *
   * Accepted values: ALCOHOL_UNSPECIFIED, ALCOHOL_HR, ALCOHOL_HMR
   *
   * @param self::EXCLUDED_ALCOHOL_RISK_* $excludedAlcoholRisk
   */
  public function setExcludedAlcoholRisk($excludedAlcoholRisk)
  {
    $this->excludedAlcoholRisk = $excludedAlcoholRisk;
  }
  /**
   * @return self::EXCLUDED_ALCOHOL_RISK_*
   */
  public function getExcludedAlcoholRisk()
  {
    return $this->excludedAlcoholRisk;
  }
  /**
   * Brand Safety - **Drugs**.
   *
   * Accepted values: DRUGS_UNSPECIFIED, DRUGS_HR, DRUGS_HMR
   *
   * @param self::EXCLUDED_DRUGS_RISK_* $excludedDrugsRisk
   */
  public function setExcludedDrugsRisk($excludedDrugsRisk)
  {
    $this->excludedDrugsRisk = $excludedDrugsRisk;
  }
  /**
   * @return self::EXCLUDED_DRUGS_RISK_*
   */
  public function getExcludedDrugsRisk()
  {
    return $this->excludedDrugsRisk;
  }
  /**
   * Brand Safety - **Gambling**.
   *
   * Accepted values: GAMBLING_UNSPECIFIED, GAMBLING_HR, GAMBLING_HMR
   *
   * @param self::EXCLUDED_GAMBLING_RISK_* $excludedGamblingRisk
   */
  public function setExcludedGamblingRisk($excludedGamblingRisk)
  {
    $this->excludedGamblingRisk = $excludedGamblingRisk;
  }
  /**
   * @return self::EXCLUDED_GAMBLING_RISK_*
   */
  public function getExcludedGamblingRisk()
  {
    return $this->excludedGamblingRisk;
  }
  /**
   * Brand Safety - **Hate speech**.
   *
   * Accepted values: HATE_SPEECH_UNSPECIFIED, HATE_SPEECH_HR, HATE_SPEECH_HMR
   *
   * @param self::EXCLUDED_HATE_SPEECH_RISK_* $excludedHateSpeechRisk
   */
  public function setExcludedHateSpeechRisk($excludedHateSpeechRisk)
  {
    $this->excludedHateSpeechRisk = $excludedHateSpeechRisk;
  }
  /**
   * @return self::EXCLUDED_HATE_SPEECH_RISK_*
   */
  public function getExcludedHateSpeechRisk()
  {
    return $this->excludedHateSpeechRisk;
  }
  /**
   * Brand Safety - **Illegal downloads**.
   *
   * Accepted values: ILLEGAL_DOWNLOADS_UNSPECIFIED, ILLEGAL_DOWNLOADS_HR,
   * ILLEGAL_DOWNLOADS_HMR
   *
   * @param self::EXCLUDED_ILLEGAL_DOWNLOADS_RISK_* $excludedIllegalDownloadsRisk
   */
  public function setExcludedIllegalDownloadsRisk($excludedIllegalDownloadsRisk)
  {
    $this->excludedIllegalDownloadsRisk = $excludedIllegalDownloadsRisk;
  }
  /**
   * @return self::EXCLUDED_ILLEGAL_DOWNLOADS_RISK_*
   */
  public function getExcludedIllegalDownloadsRisk()
  {
    return $this->excludedIllegalDownloadsRisk;
  }
  /**
   * Brand Safety - **Offensive language**.
   *
   * Accepted values: OFFENSIVE_LANGUAGE_UNSPECIFIED, OFFENSIVE_LANGUAGE_HR,
   * OFFENSIVE_LANGUAGE_HMR
   *
   * @param self::EXCLUDED_OFFENSIVE_LANGUAGE_RISK_* $excludedOffensiveLanguageRisk
   */
  public function setExcludedOffensiveLanguageRisk($excludedOffensiveLanguageRisk)
  {
    $this->excludedOffensiveLanguageRisk = $excludedOffensiveLanguageRisk;
  }
  /**
   * @return self::EXCLUDED_OFFENSIVE_LANGUAGE_RISK_*
   */
  public function getExcludedOffensiveLanguageRisk()
  {
    return $this->excludedOffensiveLanguageRisk;
  }
  /**
   * Brand Safety - **Violence**.
   *
   * Accepted values: VIOLENCE_UNSPECIFIED, VIOLENCE_HR, VIOLENCE_HMR
   *
   * @param self::EXCLUDED_VIOLENCE_RISK_* $excludedViolenceRisk
   */
  public function setExcludedViolenceRisk($excludedViolenceRisk)
  {
    $this->excludedViolenceRisk = $excludedViolenceRisk;
  }
  /**
   * @return self::EXCLUDED_VIOLENCE_RISK_*
   */
  public function getExcludedViolenceRisk()
  {
    return $this->excludedViolenceRisk;
  }
  /**
   * Optional. The quality sync custom segment ID provided by Integral Ad
   * Science. The ID must be between `3000000` and `4999999`, inclusive.
   *
   * @param string[] $qualitySyncCustomSegmentId
   */
  public function setQualitySyncCustomSegmentId($qualitySyncCustomSegmentId)
  {
    $this->qualitySyncCustomSegmentId = $qualitySyncCustomSegmentId;
  }
  /**
   * @return string[]
   */
  public function getQualitySyncCustomSegmentId()
  {
    return $this->qualitySyncCustomSegmentId;
  }
  /**
   * True advertising quality (applicable to Display line items only).
   *
   * Accepted values: TRAQ_UNSPECIFIED, TRAQ_250, TRAQ_500, TRAQ_600, TRAQ_700,
   * TRAQ_750, TRAQ_875, TRAQ_1000
   *
   * @param self::TRAQ_SCORE_OPTION_* $traqScoreOption
   */
  public function setTraqScoreOption($traqScoreOption)
  {
    $this->traqScoreOption = $traqScoreOption;
  }
  /**
   * @return self::TRAQ_SCORE_OPTION_*
   */
  public function getTraqScoreOption()
  {
    return $this->traqScoreOption;
  }
  /**
   * Video Viewability Section (applicable to video line items only).
   *
   * Accepted values: VIDEO_VIEWABILITY_UNSPECIFIED, VIDEO_VIEWABILITY_40,
   * VIDEO_VIEWABILITY_50, VIDEO_VIEWABILITY_60, VIDEO_VIEWABILITY_70
   *
   * @param self::VIDEO_VIEWABILITY_* $videoViewability
   */
  public function setVideoViewability($videoViewability)
  {
    $this->videoViewability = $videoViewability;
  }
  /**
   * @return self::VIDEO_VIEWABILITY_*
   */
  public function getVideoViewability()
  {
    return $this->videoViewability;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(IntegralAdScience::class, 'Google_Service_DisplayVideo_IntegralAdScience');
