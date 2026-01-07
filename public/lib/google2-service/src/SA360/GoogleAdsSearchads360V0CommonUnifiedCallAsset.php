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

namespace Google\Service\SA360;

class GoogleAdsSearchads360V0CommonUnifiedCallAsset extends \Google\Collection
{
  /**
   * Not specified.
   */
  public const CALL_CONVERSION_REPORTING_STATE_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * Used for return value only. Represents value unknown in this version.
   */
  public const CALL_CONVERSION_REPORTING_STATE_UNKNOWN = 'UNKNOWN';
  /**
   * Call conversion action is disabled.
   */
  public const CALL_CONVERSION_REPORTING_STATE_DISABLED = 'DISABLED';
  /**
   * Call conversion action will use call conversion type set at the account
   * level.
   */
  public const CALL_CONVERSION_REPORTING_STATE_USE_ACCOUNT_LEVEL_CALL_CONVERSION_ACTION = 'USE_ACCOUNT_LEVEL_CALL_CONVERSION_ACTION';
  /**
   * Call conversion action will use call conversion type set at the resource
   * (call only ads/call extensions) level.
   */
  public const CALL_CONVERSION_REPORTING_STATE_USE_RESOURCE_LEVEL_CALL_CONVERSION_ACTION = 'USE_RESOURCE_LEVEL_CALL_CONVERSION_ACTION';
  protected $collection_key = 'adScheduleTargets';
  protected $adScheduleTargetsType = GoogleAdsSearchads360V0CommonAdScheduleInfo::class;
  protected $adScheduleTargetsDataType = 'array';
  /**
   * The conversion action to attribute a call conversion to. If not set, the
   * default conversion action is used. This field only has effect if
   * call_conversion_reporting_state is set to
   * USE_RESOURCE_LEVEL_CALL_CONVERSION_ACTION.
   *
   * @var string
   */
  public $callConversionAction;
  /**
   * Output only. Indicates whether this CallAsset should use its own call
   * conversion setting, follow the account level setting, or disable call
   * conversion.
   *
   * @var string
   */
  public $callConversionReportingState;
  /**
   * Whether the call only shows the phone number without a link to the website.
   * Applies to Microsoft Ads.
   *
   * @var bool
   */
  public $callOnly;
  /**
   * Whether the call should be enabled on call tracking. Applies to Microsoft
   * Ads.
   *
   * @var bool
   */
  public $callTrackingEnabled;
  /**
   * Two-letter country code of the phone number. Examples: 'US', 'us'.
   *
   * @var string
   */
  public $countryCode;
  /**
   * Last date of when this asset is effective and still serving, in yyyy-MM-dd
   * format.
   *
   * @var string
   */
  public $endDate;
  /**
   * The advertiser's raw phone number. Examples: '1234567890', '(123)456-7890'
   *
   * @var string
   */
  public $phoneNumber;
  /**
   * Start date of when this asset is effective and can begin serving, in yyyy-
   * MM-dd format.
   *
   * @var string
   */
  public $startDate;
  /**
   * Whether to show the call extension in search user's time zone. Applies to
   * Microsoft Ads.
   *
   * @var bool
   */
  public $useSearcherTimeZone;

  /**
   * List of non-overlapping schedules specifying all time intervals for which
   * the asset may serve. There can be a maximum of 6 schedules per day, 42 in
   * total.
   *
   * @param GoogleAdsSearchads360V0CommonAdScheduleInfo[] $adScheduleTargets
   */
  public function setAdScheduleTargets($adScheduleTargets)
  {
    $this->adScheduleTargets = $adScheduleTargets;
  }
  /**
   * @return GoogleAdsSearchads360V0CommonAdScheduleInfo[]
   */
  public function getAdScheduleTargets()
  {
    return $this->adScheduleTargets;
  }
  /**
   * The conversion action to attribute a call conversion to. If not set, the
   * default conversion action is used. This field only has effect if
   * call_conversion_reporting_state is set to
   * USE_RESOURCE_LEVEL_CALL_CONVERSION_ACTION.
   *
   * @param string $callConversionAction
   */
  public function setCallConversionAction($callConversionAction)
  {
    $this->callConversionAction = $callConversionAction;
  }
  /**
   * @return string
   */
  public function getCallConversionAction()
  {
    return $this->callConversionAction;
  }
  /**
   * Output only. Indicates whether this CallAsset should use its own call
   * conversion setting, follow the account level setting, or disable call
   * conversion.
   *
   * Accepted values: UNSPECIFIED, UNKNOWN, DISABLED,
   * USE_ACCOUNT_LEVEL_CALL_CONVERSION_ACTION,
   * USE_RESOURCE_LEVEL_CALL_CONVERSION_ACTION
   *
   * @param self::CALL_CONVERSION_REPORTING_STATE_* $callConversionReportingState
   */
  public function setCallConversionReportingState($callConversionReportingState)
  {
    $this->callConversionReportingState = $callConversionReportingState;
  }
  /**
   * @return self::CALL_CONVERSION_REPORTING_STATE_*
   */
  public function getCallConversionReportingState()
  {
    return $this->callConversionReportingState;
  }
  /**
   * Whether the call only shows the phone number without a link to the website.
   * Applies to Microsoft Ads.
   *
   * @param bool $callOnly
   */
  public function setCallOnly($callOnly)
  {
    $this->callOnly = $callOnly;
  }
  /**
   * @return bool
   */
  public function getCallOnly()
  {
    return $this->callOnly;
  }
  /**
   * Whether the call should be enabled on call tracking. Applies to Microsoft
   * Ads.
   *
   * @param bool $callTrackingEnabled
   */
  public function setCallTrackingEnabled($callTrackingEnabled)
  {
    $this->callTrackingEnabled = $callTrackingEnabled;
  }
  /**
   * @return bool
   */
  public function getCallTrackingEnabled()
  {
    return $this->callTrackingEnabled;
  }
  /**
   * Two-letter country code of the phone number. Examples: 'US', 'us'.
   *
   * @param string $countryCode
   */
  public function setCountryCode($countryCode)
  {
    $this->countryCode = $countryCode;
  }
  /**
   * @return string
   */
  public function getCountryCode()
  {
    return $this->countryCode;
  }
  /**
   * Last date of when this asset is effective and still serving, in yyyy-MM-dd
   * format.
   *
   * @param string $endDate
   */
  public function setEndDate($endDate)
  {
    $this->endDate = $endDate;
  }
  /**
   * @return string
   */
  public function getEndDate()
  {
    return $this->endDate;
  }
  /**
   * The advertiser's raw phone number. Examples: '1234567890', '(123)456-7890'
   *
   * @param string $phoneNumber
   */
  public function setPhoneNumber($phoneNumber)
  {
    $this->phoneNumber = $phoneNumber;
  }
  /**
   * @return string
   */
  public function getPhoneNumber()
  {
    return $this->phoneNumber;
  }
  /**
   * Start date of when this asset is effective and can begin serving, in yyyy-
   * MM-dd format.
   *
   * @param string $startDate
   */
  public function setStartDate($startDate)
  {
    $this->startDate = $startDate;
  }
  /**
   * @return string
   */
  public function getStartDate()
  {
    return $this->startDate;
  }
  /**
   * Whether to show the call extension in search user's time zone. Applies to
   * Microsoft Ads.
   *
   * @param bool $useSearcherTimeZone
   */
  public function setUseSearcherTimeZone($useSearcherTimeZone)
  {
    $this->useSearcherTimeZone = $useSearcherTimeZone;
  }
  /**
   * @return bool
   */
  public function getUseSearcherTimeZone()
  {
    return $this->useSearcherTimeZone;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAdsSearchads360V0CommonUnifiedCallAsset::class, 'Google_Service_SA360_GoogleAdsSearchads360V0CommonUnifiedCallAsset');
