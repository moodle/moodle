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

class AlgorithmRulesComparisonValue extends \Google\Model
{
  /**
   * Content duration is not specified in this version. This enum is a place
   * holder for a default value and does not represent a real content duration.
   */
  public const CONTENT_DURATION_VALUE_CONTENT_DURATION_UNSPECIFIED = 'CONTENT_DURATION_UNSPECIFIED';
  /**
   * The content duration is unknown.
   */
  public const CONTENT_DURATION_VALUE_CONTENT_DURATION_UNKNOWN = 'CONTENT_DURATION_UNKNOWN';
  /**
   * Content is 0-1 minute long.
   */
  public const CONTENT_DURATION_VALUE_CONTENT_DURATION_0_TO_1_MIN = 'CONTENT_DURATION_0_TO_1_MIN';
  /**
   * Content is 1-5 minutes long.
   */
  public const CONTENT_DURATION_VALUE_CONTENT_DURATION_1_TO_5_MIN = 'CONTENT_DURATION_1_TO_5_MIN';
  /**
   * Content is 5-15 minutes long.
   */
  public const CONTENT_DURATION_VALUE_CONTENT_DURATION_5_TO_15_MIN = 'CONTENT_DURATION_5_TO_15_MIN';
  /**
   * Content is 15-30 minutes long.
   */
  public const CONTENT_DURATION_VALUE_CONTENT_DURATION_15_TO_30_MIN = 'CONTENT_DURATION_15_TO_30_MIN';
  /**
   * Content is 30-60 minutes long.
   */
  public const CONTENT_DURATION_VALUE_CONTENT_DURATION_30_TO_60_MIN = 'CONTENT_DURATION_30_TO_60_MIN';
  /**
   * Content is over 60 minutes long.
   */
  public const CONTENT_DURATION_VALUE_CONTENT_DURATION_OVER_60_MIN = 'CONTENT_DURATION_OVER_60_MIN';
  /**
   * Content stream type is not specified in this version. This enum is a place
   * holder for a default value and does not represent a real content stream
   * type.
   */
  public const CONTENT_STREAM_TYPE_VALUE_CONTENT_STREAM_TYPE_UNSPECIFIED = 'CONTENT_STREAM_TYPE_UNSPECIFIED';
  /**
   * The content is being live-streamed.
   */
  public const CONTENT_STREAM_TYPE_VALUE_CONTENT_LIVE_STREAM = 'CONTENT_LIVE_STREAM';
  /**
   * The content is viewed on-demand.
   */
  public const CONTENT_STREAM_TYPE_VALUE_CONTENT_ON_DEMAND = 'CONTENT_ON_DEMAND';
  /**
   * Default value when device type is not specified in this version. This enum
   * is a placeholder for default value and does not represent a real device
   * type option.
   */
  public const DEVICE_TYPE_VALUE_RULE_DEVICE_TYPE_UNSPECIFIED = 'RULE_DEVICE_TYPE_UNSPECIFIED';
  /**
   * Computer.
   */
  public const DEVICE_TYPE_VALUE_RULE_DEVICE_TYPE_COMPUTER = 'RULE_DEVICE_TYPE_COMPUTER';
  /**
   * Connected TV.
   */
  public const DEVICE_TYPE_VALUE_RULE_DEVICE_TYPE_CONNECTED_TV = 'RULE_DEVICE_TYPE_CONNECTED_TV';
  /**
   * Smart phone.
   */
  public const DEVICE_TYPE_VALUE_RULE_DEVICE_TYPE_SMART_PHONE = 'RULE_DEVICE_TYPE_SMART_PHONE';
  /**
   * Tablet.
   */
  public const DEVICE_TYPE_VALUE_RULE_DEVICE_TYPE_TABLET = 'RULE_DEVICE_TYPE_TABLET';
  /**
   * Connected device.
   */
  public const DEVICE_TYPE_VALUE_RULE_DEVICE_TYPE_CONNECTED_DEVICE = 'RULE_DEVICE_TYPE_CONNECTED_DEVICE';
  /**
   * Set top box.
   */
  public const DEVICE_TYPE_VALUE_RULE_DEVICE_TYPE_SET_TOP_BOX = 'RULE_DEVICE_TYPE_SET_TOP_BOX';
  /**
   * Default value when environment is not specified in this version. This enum
   * is a placeholder for default value and does not represent a real
   * environment option.
   */
  public const ENVIRONMENT_VALUE_ENVIRONMENT_UNSPECIFIED = 'ENVIRONMENT_UNSPECIFIED';
  /**
   * Target inventory displayed in browsers. This includes inventory that was
   * designed for the device it was viewed on, such as mobile websites viewed on
   * a mobile device. ENVIRONMENT_WEB_NOT_OPTIMIZED, if targeted, should be
   * deleted prior to the deletion of this targeting option.
   */
  public const ENVIRONMENT_VALUE_ENVIRONMENT_WEB_OPTIMIZED = 'ENVIRONMENT_WEB_OPTIMIZED';
  /**
   * Target inventory displayed in browsers. This includes inventory that was
   * not designed for the device but viewed on it, such as websites optimized
   * for desktop but viewed on a mobile device. ENVIRONMENT_WEB_OPTIMIZED should
   * be targeted prior to the addition of this targeting option.
   */
  public const ENVIRONMENT_VALUE_ENVIRONMENT_WEB_NOT_OPTIMIZED = 'ENVIRONMENT_WEB_NOT_OPTIMIZED';
  /**
   * Target inventory displayed in apps.
   */
  public const ENVIRONMENT_VALUE_ENVIRONMENT_APP = 'ENVIRONMENT_APP';
  /**
   * Exchange is not specified or is unknown in this version.
   */
  public const EXCHANGE_VALUE_EXCHANGE_UNSPECIFIED = 'EXCHANGE_UNSPECIFIED';
  /**
   * Google Ad Manager.
   */
  public const EXCHANGE_VALUE_EXCHANGE_GOOGLE_AD_MANAGER = 'EXCHANGE_GOOGLE_AD_MANAGER';
  /**
   * AppNexus.
   */
  public const EXCHANGE_VALUE_EXCHANGE_APPNEXUS = 'EXCHANGE_APPNEXUS';
  /**
   * BrightRoll Exchange for Video from Yahoo!.
   */
  public const EXCHANGE_VALUE_EXCHANGE_BRIGHTROLL = 'EXCHANGE_BRIGHTROLL';
  /**
   * Adform.
   */
  public const EXCHANGE_VALUE_EXCHANGE_ADFORM = 'EXCHANGE_ADFORM';
  /**
   * Admeta.
   */
  public const EXCHANGE_VALUE_EXCHANGE_ADMETA = 'EXCHANGE_ADMETA';
  /**
   * Admixer.
   */
  public const EXCHANGE_VALUE_EXCHANGE_ADMIXER = 'EXCHANGE_ADMIXER';
  /**
   * AdsMogo.
   */
  public const EXCHANGE_VALUE_EXCHANGE_ADSMOGO = 'EXCHANGE_ADSMOGO';
  /**
   * AdsWizz.
   */
  public const EXCHANGE_VALUE_EXCHANGE_ADSWIZZ = 'EXCHANGE_ADSWIZZ';
  /**
   * BidSwitch.
   */
  public const EXCHANGE_VALUE_EXCHANGE_BIDSWITCH = 'EXCHANGE_BIDSWITCH';
  /**
   * BrightRoll Exchange for Display from Yahoo!.
   */
  public const EXCHANGE_VALUE_EXCHANGE_BRIGHTROLL_DISPLAY = 'EXCHANGE_BRIGHTROLL_DISPLAY';
  /**
   * Cadreon.
   */
  public const EXCHANGE_VALUE_EXCHANGE_CADREON = 'EXCHANGE_CADREON';
  /**
   * Dailymotion.
   */
  public const EXCHANGE_VALUE_EXCHANGE_DAILYMOTION = 'EXCHANGE_DAILYMOTION';
  /**
   * Five.
   */
  public const EXCHANGE_VALUE_EXCHANGE_FIVE = 'EXCHANGE_FIVE';
  /**
   * Fluct.
   */
  public const EXCHANGE_VALUE_EXCHANGE_FLUCT = 'EXCHANGE_FLUCT';
  /**
   * FreeWheel SSP.
   */
  public const EXCHANGE_VALUE_EXCHANGE_FREEWHEEL = 'EXCHANGE_FREEWHEEL';
  /**
   * Geniee.
   */
  public const EXCHANGE_VALUE_EXCHANGE_GENIEE = 'EXCHANGE_GENIEE';
  /**
   * GumGum.
   */
  public const EXCHANGE_VALUE_EXCHANGE_GUMGUM = 'EXCHANGE_GUMGUM';
  /**
   * i-mobile.
   */
  public const EXCHANGE_VALUE_EXCHANGE_IMOBILE = 'EXCHANGE_IMOBILE';
  /**
   * iBILLBOARD.
   */
  public const EXCHANGE_VALUE_EXCHANGE_IBILLBOARD = 'EXCHANGE_IBILLBOARD';
  /**
   * Improve Digital.
   */
  public const EXCHANGE_VALUE_EXCHANGE_IMPROVE_DIGITAL = 'EXCHANGE_IMPROVE_DIGITAL';
  /**
   * Index Exchange.
   */
  public const EXCHANGE_VALUE_EXCHANGE_INDEX = 'EXCHANGE_INDEX';
  /**
   * Kargo.
   */
  public const EXCHANGE_VALUE_EXCHANGE_KARGO = 'EXCHANGE_KARGO';
  /**
   * MicroAd.
   */
  public const EXCHANGE_VALUE_EXCHANGE_MICROAD = 'EXCHANGE_MICROAD';
  /**
   * MoPub.
   */
  public const EXCHANGE_VALUE_EXCHANGE_MOPUB = 'EXCHANGE_MOPUB';
  /**
   * Nend.
   */
  public const EXCHANGE_VALUE_EXCHANGE_NEND = 'EXCHANGE_NEND';
  /**
   * ONE by AOL: Display Market Place.
   */
  public const EXCHANGE_VALUE_EXCHANGE_ONE_BY_AOL_DISPLAY = 'EXCHANGE_ONE_BY_AOL_DISPLAY';
  /**
   * ONE by AOL: Mobile.
   */
  public const EXCHANGE_VALUE_EXCHANGE_ONE_BY_AOL_MOBILE = 'EXCHANGE_ONE_BY_AOL_MOBILE';
  /**
   * ONE by AOL: Video.
   */
  public const EXCHANGE_VALUE_EXCHANGE_ONE_BY_AOL_VIDEO = 'EXCHANGE_ONE_BY_AOL_VIDEO';
  /**
   * Ooyala.
   */
  public const EXCHANGE_VALUE_EXCHANGE_OOYALA = 'EXCHANGE_OOYALA';
  /**
   * OpenX.
   */
  public const EXCHANGE_VALUE_EXCHANGE_OPENX = 'EXCHANGE_OPENX';
  /**
   * Permodo.
   */
  public const EXCHANGE_VALUE_EXCHANGE_PERMODO = 'EXCHANGE_PERMODO';
  /**
   * Platform One.
   */
  public const EXCHANGE_VALUE_EXCHANGE_PLATFORMONE = 'EXCHANGE_PLATFORMONE';
  /**
   * PlatformId.
   */
  public const EXCHANGE_VALUE_EXCHANGE_PLATFORMID = 'EXCHANGE_PLATFORMID';
  /**
   * PubMatic.
   */
  public const EXCHANGE_VALUE_EXCHANGE_PUBMATIC = 'EXCHANGE_PUBMATIC';
  /**
   * PulsePoint.
   */
  public const EXCHANGE_VALUE_EXCHANGE_PULSEPOINT = 'EXCHANGE_PULSEPOINT';
  /**
   * RevenueMax.
   */
  public const EXCHANGE_VALUE_EXCHANGE_REVENUEMAX = 'EXCHANGE_REVENUEMAX';
  /**
   * Rubicon.
   */
  public const EXCHANGE_VALUE_EXCHANGE_RUBICON = 'EXCHANGE_RUBICON';
  /**
   * SmartClip.
   */
  public const EXCHANGE_VALUE_EXCHANGE_SMARTCLIP = 'EXCHANGE_SMARTCLIP';
  /**
   * SmartRTB+.
   */
  public const EXCHANGE_VALUE_EXCHANGE_SMARTRTB = 'EXCHANGE_SMARTRTB';
  /**
   * SmartstreamTv.
   */
  public const EXCHANGE_VALUE_EXCHANGE_SMARTSTREAMTV = 'EXCHANGE_SMARTSTREAMTV';
  /**
   * Sovrn.
   */
  public const EXCHANGE_VALUE_EXCHANGE_SOVRN = 'EXCHANGE_SOVRN';
  /**
   * SpotXchange.
   */
  public const EXCHANGE_VALUE_EXCHANGE_SPOTXCHANGE = 'EXCHANGE_SPOTXCHANGE';
  /**
   * Ströer SSP.
   */
  public const EXCHANGE_VALUE_EXCHANGE_STROER = 'EXCHANGE_STROER';
  /**
   * TeadsTv.
   */
  public const EXCHANGE_VALUE_EXCHANGE_TEADSTV = 'EXCHANGE_TEADSTV';
  /**
   * Telaria.
   */
  public const EXCHANGE_VALUE_EXCHANGE_TELARIA = 'EXCHANGE_TELARIA';
  /**
   * TVN.
   */
  public const EXCHANGE_VALUE_EXCHANGE_TVN = 'EXCHANGE_TVN';
  /**
   * United.
   */
  public const EXCHANGE_VALUE_EXCHANGE_UNITED = 'EXCHANGE_UNITED';
  /**
   * Yieldlab.
   */
  public const EXCHANGE_VALUE_EXCHANGE_YIELDLAB = 'EXCHANGE_YIELDLAB';
  /**
   * Yieldmo.
   */
  public const EXCHANGE_VALUE_EXCHANGE_YIELDMO = 'EXCHANGE_YIELDMO';
  /**
   * UnrulyX.
   */
  public const EXCHANGE_VALUE_EXCHANGE_UNRULYX = 'EXCHANGE_UNRULYX';
  /**
   * Open8.
   */
  public const EXCHANGE_VALUE_EXCHANGE_OPEN8 = 'EXCHANGE_OPEN8';
  /**
   * Triton.
   */
  public const EXCHANGE_VALUE_EXCHANGE_TRITON = 'EXCHANGE_TRITON';
  /**
   * TripleLift.
   */
  public const EXCHANGE_VALUE_EXCHANGE_TRIPLELIFT = 'EXCHANGE_TRIPLELIFT';
  /**
   * Taboola.
   */
  public const EXCHANGE_VALUE_EXCHANGE_TABOOLA = 'EXCHANGE_TABOOLA';
  /**
   * InMobi.
   */
  public const EXCHANGE_VALUE_EXCHANGE_INMOBI = 'EXCHANGE_INMOBI';
  /**
   * Smaato.
   */
  public const EXCHANGE_VALUE_EXCHANGE_SMAATO = 'EXCHANGE_SMAATO';
  /**
   * Aja.
   */
  public const EXCHANGE_VALUE_EXCHANGE_AJA = 'EXCHANGE_AJA';
  /**
   * Supership.
   */
  public const EXCHANGE_VALUE_EXCHANGE_SUPERSHIP = 'EXCHANGE_SUPERSHIP';
  /**
   * Nexstar Digital.
   */
  public const EXCHANGE_VALUE_EXCHANGE_NEXSTAR_DIGITAL = 'EXCHANGE_NEXSTAR_DIGITAL';
  /**
   * Waze.
   */
  public const EXCHANGE_VALUE_EXCHANGE_WAZE = 'EXCHANGE_WAZE';
  /**
   * SoundCast.
   */
  public const EXCHANGE_VALUE_EXCHANGE_SOUNDCAST = 'EXCHANGE_SOUNDCAST';
  /**
   * Sharethrough.
   */
  public const EXCHANGE_VALUE_EXCHANGE_SHARETHROUGH = 'EXCHANGE_SHARETHROUGH';
  /**
   * Fyber.
   */
  public const EXCHANGE_VALUE_EXCHANGE_FYBER = 'EXCHANGE_FYBER';
  /**
   * Red For Publishers.
   */
  public const EXCHANGE_VALUE_EXCHANGE_RED_FOR_PUBLISHERS = 'EXCHANGE_RED_FOR_PUBLISHERS';
  /**
   * Media.net.
   */
  public const EXCHANGE_VALUE_EXCHANGE_MEDIANET = 'EXCHANGE_MEDIANET';
  /**
   * Tapjoy.
   */
  public const EXCHANGE_VALUE_EXCHANGE_TAPJOY = 'EXCHANGE_TAPJOY';
  /**
   * Vistar.
   */
  public const EXCHANGE_VALUE_EXCHANGE_VISTAR = 'EXCHANGE_VISTAR';
  /**
   * DAX.
   */
  public const EXCHANGE_VALUE_EXCHANGE_DAX = 'EXCHANGE_DAX';
  /**
   * JCD.
   */
  public const EXCHANGE_VALUE_EXCHANGE_JCD = 'EXCHANGE_JCD';
  /**
   * Place Exchange.
   */
  public const EXCHANGE_VALUE_EXCHANGE_PLACE_EXCHANGE = 'EXCHANGE_PLACE_EXCHANGE';
  /**
   * AppLovin.
   */
  public const EXCHANGE_VALUE_EXCHANGE_APPLOVIN = 'EXCHANGE_APPLOVIN';
  /**
   * Connatix.
   */
  public const EXCHANGE_VALUE_EXCHANGE_CONNATIX = 'EXCHANGE_CONNATIX';
  /**
   * Reset Digital.
   */
  public const EXCHANGE_VALUE_EXCHANGE_RESET_DIGITAL = 'EXCHANGE_RESET_DIGITAL';
  /**
   * Hivestack.
   */
  public const EXCHANGE_VALUE_EXCHANGE_HIVESTACK = 'EXCHANGE_HIVESTACK';
  /**
   * Drax.
   */
  public const EXCHANGE_VALUE_EXCHANGE_DRAX = 'EXCHANGE_DRAX';
  /**
   * AppLovin MAX.
   */
  public const EXCHANGE_VALUE_EXCHANGE_APPLOVIN_GBID = 'EXCHANGE_APPLOVIN_GBID';
  /**
   * DT Fairbid.
   */
  public const EXCHANGE_VALUE_EXCHANGE_FYBER_GBID = 'EXCHANGE_FYBER_GBID';
  /**
   * Unity LevelPlay.
   */
  public const EXCHANGE_VALUE_EXCHANGE_UNITY_GBID = 'EXCHANGE_UNITY_GBID';
  /**
   * Chartboost Mediation.
   */
  public const EXCHANGE_VALUE_EXCHANGE_CHARTBOOST_GBID = 'EXCHANGE_CHARTBOOST_GBID';
  /**
   * AdMost.
   */
  public const EXCHANGE_VALUE_EXCHANGE_ADMOST_GBID = 'EXCHANGE_ADMOST_GBID';
  /**
   * TopOn.
   */
  public const EXCHANGE_VALUE_EXCHANGE_TOPON_GBID = 'EXCHANGE_TOPON_GBID';
  /**
   * Netflix.
   */
  public const EXCHANGE_VALUE_EXCHANGE_NETFLIX = 'EXCHANGE_NETFLIX';
  /**
   * Core.
   */
  public const EXCHANGE_VALUE_EXCHANGE_CORE = 'EXCHANGE_CORE';
  /**
   * Commerce Grid.
   */
  public const EXCHANGE_VALUE_EXCHANGE_COMMERCE_GRID = 'EXCHANGE_COMMERCE_GRID';
  /**
   * Spotify.
   */
  public const EXCHANGE_VALUE_EXCHANGE_SPOTIFY = 'EXCHANGE_SPOTIFY';
  /**
   * Tubi.
   */
  public const EXCHANGE_VALUE_EXCHANGE_TUBI = 'EXCHANGE_TUBI';
  /**
   * Snap.
   */
  public const EXCHANGE_VALUE_EXCHANGE_SNAP = 'EXCHANGE_SNAP';
  /**
   * Cadent.
   */
  public const EXCHANGE_VALUE_EXCHANGE_CADENT = 'EXCHANGE_CADENT';
  /**
   * On screen position is not specified in this version. This enum is a place
   * holder for a default value and does not represent a real on screen
   * position.
   */
  public const ON_SCREEN_POSITION_VALUE_ON_SCREEN_POSITION_UNSPECIFIED = 'ON_SCREEN_POSITION_UNSPECIFIED';
  /**
   * The ad position is unknown on the screen.
   */
  public const ON_SCREEN_POSITION_VALUE_ON_SCREEN_POSITION_UNKNOWN = 'ON_SCREEN_POSITION_UNKNOWN';
  /**
   * The ad is located above the fold.
   */
  public const ON_SCREEN_POSITION_VALUE_ON_SCREEN_POSITION_ABOVE_THE_FOLD = 'ON_SCREEN_POSITION_ABOVE_THE_FOLD';
  /**
   * The ad is located below the fold.
   */
  public const ON_SCREEN_POSITION_VALUE_ON_SCREEN_POSITION_BELOW_THE_FOLD = 'ON_SCREEN_POSITION_BELOW_THE_FOLD';
  /**
   * Video player size is not specified in this version. This enum is a place
   * holder for a default value and does not represent a real video player size.
   */
  public const VIDEO_PLAYER_SIZE_VALUE_VIDEO_PLAYER_SIZE_UNSPECIFIED = 'VIDEO_PLAYER_SIZE_UNSPECIFIED';
  /**
   * The dimensions of the video player are less than 400×300 (desktop), or up
   * to 20% of screen covered (mobile).
   */
  public const VIDEO_PLAYER_SIZE_VALUE_VIDEO_PLAYER_SIZE_SMALL = 'VIDEO_PLAYER_SIZE_SMALL';
  /**
   * The dimensions of the video player are between 400x300 and 1280x720 pixels
   * (desktop), or 20% to 90% of the screen covered (mobile).
   */
  public const VIDEO_PLAYER_SIZE_VALUE_VIDEO_PLAYER_SIZE_LARGE = 'VIDEO_PLAYER_SIZE_LARGE';
  /**
   * The dimensions of the video player are 1280×720 or greater (desktop), or
   * over 90% of the screen covered (mobile).
   */
  public const VIDEO_PLAYER_SIZE_VALUE_VIDEO_PLAYER_SIZE_HD = 'VIDEO_PLAYER_SIZE_HD';
  /**
   * The dimensions of the video player are unknown.
   */
  public const VIDEO_PLAYER_SIZE_VALUE_VIDEO_PLAYER_SIZE_UNKNOWN = 'VIDEO_PLAYER_SIZE_UNKNOWN';
  /**
   * Boolean value.
   *
   * @var bool
   */
  public $boolValue;
  /**
   * Video content duration value.
   *
   * @var string
   */
  public $contentDurationValue;
  /**
   * Video genre id value.
   *
   * @var string
   */
  public $contentGenreIdValue;
  /**
   * Video delivery type value.
   *
   * @var string
   */
  public $contentStreamTypeValue;
  protected $creativeDimensionValueType = Dimensions::class;
  protected $creativeDimensionValueDataType = '';
  protected $dayAndTimeValueType = DayAndTime::class;
  protected $dayAndTimeValueDataType = '';
  /**
   * Device type value.
   *
   * @var string
   */
  public $deviceTypeValue;
  /**
   * Double value.
   *
   * @var 
   */
  public $doubleValue;
  /**
   * Environment value.
   *
   * @var string
   */
  public $environmentValue;
  /**
   * Exchange value.
   *
   * @var string
   */
  public $exchangeValue;
  /**
   * Integer value.
   *
   * @var string
   */
  public $int64Value;
  /**
   * Ad position value.
   *
   * @var string
   */
  public $onScreenPositionValue;
  /**
   * String value.
   *
   * @var string
   */
  public $stringValue;
  /**
   * Video player size value. This field is only supported for allowlisted
   * partners.
   *
   * @var string
   */
  public $videoPlayerSizeValue;

  /**
   * Boolean value.
   *
   * @param bool $boolValue
   */
  public function setBoolValue($boolValue)
  {
    $this->boolValue = $boolValue;
  }
  /**
   * @return bool
   */
  public function getBoolValue()
  {
    return $this->boolValue;
  }
  /**
   * Video content duration value.
   *
   * Accepted values: CONTENT_DURATION_UNSPECIFIED, CONTENT_DURATION_UNKNOWN,
   * CONTENT_DURATION_0_TO_1_MIN, CONTENT_DURATION_1_TO_5_MIN,
   * CONTENT_DURATION_5_TO_15_MIN, CONTENT_DURATION_15_TO_30_MIN,
   * CONTENT_DURATION_30_TO_60_MIN, CONTENT_DURATION_OVER_60_MIN
   *
   * @param self::CONTENT_DURATION_VALUE_* $contentDurationValue
   */
  public function setContentDurationValue($contentDurationValue)
  {
    $this->contentDurationValue = $contentDurationValue;
  }
  /**
   * @return self::CONTENT_DURATION_VALUE_*
   */
  public function getContentDurationValue()
  {
    return $this->contentDurationValue;
  }
  /**
   * Video genre id value.
   *
   * @param string $contentGenreIdValue
   */
  public function setContentGenreIdValue($contentGenreIdValue)
  {
    $this->contentGenreIdValue = $contentGenreIdValue;
  }
  /**
   * @return string
   */
  public function getContentGenreIdValue()
  {
    return $this->contentGenreIdValue;
  }
  /**
   * Video delivery type value.
   *
   * Accepted values: CONTENT_STREAM_TYPE_UNSPECIFIED, CONTENT_LIVE_STREAM,
   * CONTENT_ON_DEMAND
   *
   * @param self::CONTENT_STREAM_TYPE_VALUE_* $contentStreamTypeValue
   */
  public function setContentStreamTypeValue($contentStreamTypeValue)
  {
    $this->contentStreamTypeValue = $contentStreamTypeValue;
  }
  /**
   * @return self::CONTENT_STREAM_TYPE_VALUE_*
   */
  public function getContentStreamTypeValue()
  {
    return $this->contentStreamTypeValue;
  }
  /**
   * Creative dimension value.
   *
   * @param Dimensions $creativeDimensionValue
   */
  public function setCreativeDimensionValue(Dimensions $creativeDimensionValue)
  {
    $this->creativeDimensionValue = $creativeDimensionValue;
  }
  /**
   * @return Dimensions
   */
  public function getCreativeDimensionValue()
  {
    return $this->creativeDimensionValue;
  }
  /**
   * Day and time value. Only `TIME_ZONE_RESOLUTION_END_USER` is supported.
   *
   * @param DayAndTime $dayAndTimeValue
   */
  public function setDayAndTimeValue(DayAndTime $dayAndTimeValue)
  {
    $this->dayAndTimeValue = $dayAndTimeValue;
  }
  /**
   * @return DayAndTime
   */
  public function getDayAndTimeValue()
  {
    return $this->dayAndTimeValue;
  }
  /**
   * Device type value.
   *
   * Accepted values: RULE_DEVICE_TYPE_UNSPECIFIED, RULE_DEVICE_TYPE_COMPUTER,
   * RULE_DEVICE_TYPE_CONNECTED_TV, RULE_DEVICE_TYPE_SMART_PHONE,
   * RULE_DEVICE_TYPE_TABLET, RULE_DEVICE_TYPE_CONNECTED_DEVICE,
   * RULE_DEVICE_TYPE_SET_TOP_BOX
   *
   * @param self::DEVICE_TYPE_VALUE_* $deviceTypeValue
   */
  public function setDeviceTypeValue($deviceTypeValue)
  {
    $this->deviceTypeValue = $deviceTypeValue;
  }
  /**
   * @return self::DEVICE_TYPE_VALUE_*
   */
  public function getDeviceTypeValue()
  {
    return $this->deviceTypeValue;
  }
  public function setDoubleValue($doubleValue)
  {
    $this->doubleValue = $doubleValue;
  }
  public function getDoubleValue()
  {
    return $this->doubleValue;
  }
  /**
   * Environment value.
   *
   * Accepted values: ENVIRONMENT_UNSPECIFIED, ENVIRONMENT_WEB_OPTIMIZED,
   * ENVIRONMENT_WEB_NOT_OPTIMIZED, ENVIRONMENT_APP
   *
   * @param self::ENVIRONMENT_VALUE_* $environmentValue
   */
  public function setEnvironmentValue($environmentValue)
  {
    $this->environmentValue = $environmentValue;
  }
  /**
   * @return self::ENVIRONMENT_VALUE_*
   */
  public function getEnvironmentValue()
  {
    return $this->environmentValue;
  }
  /**
   * Exchange value.
   *
   * Accepted values: EXCHANGE_UNSPECIFIED, EXCHANGE_GOOGLE_AD_MANAGER,
   * EXCHANGE_APPNEXUS, EXCHANGE_BRIGHTROLL, EXCHANGE_ADFORM, EXCHANGE_ADMETA,
   * EXCHANGE_ADMIXER, EXCHANGE_ADSMOGO, EXCHANGE_ADSWIZZ, EXCHANGE_BIDSWITCH,
   * EXCHANGE_BRIGHTROLL_DISPLAY, EXCHANGE_CADREON, EXCHANGE_DAILYMOTION,
   * EXCHANGE_FIVE, EXCHANGE_FLUCT, EXCHANGE_FREEWHEEL, EXCHANGE_GENIEE,
   * EXCHANGE_GUMGUM, EXCHANGE_IMOBILE, EXCHANGE_IBILLBOARD,
   * EXCHANGE_IMPROVE_DIGITAL, EXCHANGE_INDEX, EXCHANGE_KARGO, EXCHANGE_MICROAD,
   * EXCHANGE_MOPUB, EXCHANGE_NEND, EXCHANGE_ONE_BY_AOL_DISPLAY,
   * EXCHANGE_ONE_BY_AOL_MOBILE, EXCHANGE_ONE_BY_AOL_VIDEO, EXCHANGE_OOYALA,
   * EXCHANGE_OPENX, EXCHANGE_PERMODO, EXCHANGE_PLATFORMONE,
   * EXCHANGE_PLATFORMID, EXCHANGE_PUBMATIC, EXCHANGE_PULSEPOINT,
   * EXCHANGE_REVENUEMAX, EXCHANGE_RUBICON, EXCHANGE_SMARTCLIP,
   * EXCHANGE_SMARTRTB, EXCHANGE_SMARTSTREAMTV, EXCHANGE_SOVRN,
   * EXCHANGE_SPOTXCHANGE, EXCHANGE_STROER, EXCHANGE_TEADSTV, EXCHANGE_TELARIA,
   * EXCHANGE_TVN, EXCHANGE_UNITED, EXCHANGE_YIELDLAB, EXCHANGE_YIELDMO,
   * EXCHANGE_UNRULYX, EXCHANGE_OPEN8, EXCHANGE_TRITON, EXCHANGE_TRIPLELIFT,
   * EXCHANGE_TABOOLA, EXCHANGE_INMOBI, EXCHANGE_SMAATO, EXCHANGE_AJA,
   * EXCHANGE_SUPERSHIP, EXCHANGE_NEXSTAR_DIGITAL, EXCHANGE_WAZE,
   * EXCHANGE_SOUNDCAST, EXCHANGE_SHARETHROUGH, EXCHANGE_FYBER,
   * EXCHANGE_RED_FOR_PUBLISHERS, EXCHANGE_MEDIANET, EXCHANGE_TAPJOY,
   * EXCHANGE_VISTAR, EXCHANGE_DAX, EXCHANGE_JCD, EXCHANGE_PLACE_EXCHANGE,
   * EXCHANGE_APPLOVIN, EXCHANGE_CONNATIX, EXCHANGE_RESET_DIGITAL,
   * EXCHANGE_HIVESTACK, EXCHANGE_DRAX, EXCHANGE_APPLOVIN_GBID,
   * EXCHANGE_FYBER_GBID, EXCHANGE_UNITY_GBID, EXCHANGE_CHARTBOOST_GBID,
   * EXCHANGE_ADMOST_GBID, EXCHANGE_TOPON_GBID, EXCHANGE_NETFLIX, EXCHANGE_CORE,
   * EXCHANGE_COMMERCE_GRID, EXCHANGE_SPOTIFY, EXCHANGE_TUBI, EXCHANGE_SNAP,
   * EXCHANGE_CADENT
   *
   * @param self::EXCHANGE_VALUE_* $exchangeValue
   */
  public function setExchangeValue($exchangeValue)
  {
    $this->exchangeValue = $exchangeValue;
  }
  /**
   * @return self::EXCHANGE_VALUE_*
   */
  public function getExchangeValue()
  {
    return $this->exchangeValue;
  }
  /**
   * Integer value.
   *
   * @param string $int64Value
   */
  public function setInt64Value($int64Value)
  {
    $this->int64Value = $int64Value;
  }
  /**
   * @return string
   */
  public function getInt64Value()
  {
    return $this->int64Value;
  }
  /**
   * Ad position value.
   *
   * Accepted values: ON_SCREEN_POSITION_UNSPECIFIED,
   * ON_SCREEN_POSITION_UNKNOWN, ON_SCREEN_POSITION_ABOVE_THE_FOLD,
   * ON_SCREEN_POSITION_BELOW_THE_FOLD
   *
   * @param self::ON_SCREEN_POSITION_VALUE_* $onScreenPositionValue
   */
  public function setOnScreenPositionValue($onScreenPositionValue)
  {
    $this->onScreenPositionValue = $onScreenPositionValue;
  }
  /**
   * @return self::ON_SCREEN_POSITION_VALUE_*
   */
  public function getOnScreenPositionValue()
  {
    return $this->onScreenPositionValue;
  }
  /**
   * String value.
   *
   * @param string $stringValue
   */
  public function setStringValue($stringValue)
  {
    $this->stringValue = $stringValue;
  }
  /**
   * @return string
   */
  public function getStringValue()
  {
    return $this->stringValue;
  }
  /**
   * Video player size value. This field is only supported for allowlisted
   * partners.
   *
   * Accepted values: VIDEO_PLAYER_SIZE_UNSPECIFIED, VIDEO_PLAYER_SIZE_SMALL,
   * VIDEO_PLAYER_SIZE_LARGE, VIDEO_PLAYER_SIZE_HD, VIDEO_PLAYER_SIZE_UNKNOWN
   *
   * @param self::VIDEO_PLAYER_SIZE_VALUE_* $videoPlayerSizeValue
   */
  public function setVideoPlayerSizeValue($videoPlayerSizeValue)
  {
    $this->videoPlayerSizeValue = $videoPlayerSizeValue;
  }
  /**
   * @return self::VIDEO_PLAYER_SIZE_VALUE_*
   */
  public function getVideoPlayerSizeValue()
  {
    return $this->videoPlayerSizeValue;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AlgorithmRulesComparisonValue::class, 'Google_Service_DisplayVideo_AlgorithmRulesComparisonValue');
