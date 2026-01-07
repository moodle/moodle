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

class ExchangeTargetingOptionDetails extends \Google\Model
{
  /**
   * Exchange is not specified or is unknown in this version.
   */
  public const EXCHANGE_EXCHANGE_UNSPECIFIED = 'EXCHANGE_UNSPECIFIED';
  /**
   * Google Ad Manager.
   */
  public const EXCHANGE_EXCHANGE_GOOGLE_AD_MANAGER = 'EXCHANGE_GOOGLE_AD_MANAGER';
  /**
   * AppNexus.
   */
  public const EXCHANGE_EXCHANGE_APPNEXUS = 'EXCHANGE_APPNEXUS';
  /**
   * BrightRoll Exchange for Video from Yahoo!.
   */
  public const EXCHANGE_EXCHANGE_BRIGHTROLL = 'EXCHANGE_BRIGHTROLL';
  /**
   * Adform.
   */
  public const EXCHANGE_EXCHANGE_ADFORM = 'EXCHANGE_ADFORM';
  /**
   * Admeta.
   */
  public const EXCHANGE_EXCHANGE_ADMETA = 'EXCHANGE_ADMETA';
  /**
   * Admixer.
   */
  public const EXCHANGE_EXCHANGE_ADMIXER = 'EXCHANGE_ADMIXER';
  /**
   * AdsMogo.
   */
  public const EXCHANGE_EXCHANGE_ADSMOGO = 'EXCHANGE_ADSMOGO';
  /**
   * AdsWizz.
   */
  public const EXCHANGE_EXCHANGE_ADSWIZZ = 'EXCHANGE_ADSWIZZ';
  /**
   * BidSwitch.
   */
  public const EXCHANGE_EXCHANGE_BIDSWITCH = 'EXCHANGE_BIDSWITCH';
  /**
   * BrightRoll Exchange for Display from Yahoo!.
   */
  public const EXCHANGE_EXCHANGE_BRIGHTROLL_DISPLAY = 'EXCHANGE_BRIGHTROLL_DISPLAY';
  /**
   * Cadreon.
   */
  public const EXCHANGE_EXCHANGE_CADREON = 'EXCHANGE_CADREON';
  /**
   * Dailymotion.
   */
  public const EXCHANGE_EXCHANGE_DAILYMOTION = 'EXCHANGE_DAILYMOTION';
  /**
   * Five.
   */
  public const EXCHANGE_EXCHANGE_FIVE = 'EXCHANGE_FIVE';
  /**
   * Fluct.
   */
  public const EXCHANGE_EXCHANGE_FLUCT = 'EXCHANGE_FLUCT';
  /**
   * FreeWheel SSP.
   */
  public const EXCHANGE_EXCHANGE_FREEWHEEL = 'EXCHANGE_FREEWHEEL';
  /**
   * Geniee.
   */
  public const EXCHANGE_EXCHANGE_GENIEE = 'EXCHANGE_GENIEE';
  /**
   * GumGum.
   */
  public const EXCHANGE_EXCHANGE_GUMGUM = 'EXCHANGE_GUMGUM';
  /**
   * i-mobile.
   */
  public const EXCHANGE_EXCHANGE_IMOBILE = 'EXCHANGE_IMOBILE';
  /**
   * iBILLBOARD.
   */
  public const EXCHANGE_EXCHANGE_IBILLBOARD = 'EXCHANGE_IBILLBOARD';
  /**
   * Improve Digital.
   */
  public const EXCHANGE_EXCHANGE_IMPROVE_DIGITAL = 'EXCHANGE_IMPROVE_DIGITAL';
  /**
   * Index Exchange.
   */
  public const EXCHANGE_EXCHANGE_INDEX = 'EXCHANGE_INDEX';
  /**
   * Kargo.
   */
  public const EXCHANGE_EXCHANGE_KARGO = 'EXCHANGE_KARGO';
  /**
   * MicroAd.
   */
  public const EXCHANGE_EXCHANGE_MICROAD = 'EXCHANGE_MICROAD';
  /**
   * MoPub.
   */
  public const EXCHANGE_EXCHANGE_MOPUB = 'EXCHANGE_MOPUB';
  /**
   * Nend.
   */
  public const EXCHANGE_EXCHANGE_NEND = 'EXCHANGE_NEND';
  /**
   * ONE by AOL: Display Market Place.
   */
  public const EXCHANGE_EXCHANGE_ONE_BY_AOL_DISPLAY = 'EXCHANGE_ONE_BY_AOL_DISPLAY';
  /**
   * ONE by AOL: Mobile.
   */
  public const EXCHANGE_EXCHANGE_ONE_BY_AOL_MOBILE = 'EXCHANGE_ONE_BY_AOL_MOBILE';
  /**
   * ONE by AOL: Video.
   */
  public const EXCHANGE_EXCHANGE_ONE_BY_AOL_VIDEO = 'EXCHANGE_ONE_BY_AOL_VIDEO';
  /**
   * Ooyala.
   */
  public const EXCHANGE_EXCHANGE_OOYALA = 'EXCHANGE_OOYALA';
  /**
   * OpenX.
   */
  public const EXCHANGE_EXCHANGE_OPENX = 'EXCHANGE_OPENX';
  /**
   * Permodo.
   */
  public const EXCHANGE_EXCHANGE_PERMODO = 'EXCHANGE_PERMODO';
  /**
   * Platform One.
   */
  public const EXCHANGE_EXCHANGE_PLATFORMONE = 'EXCHANGE_PLATFORMONE';
  /**
   * PlatformId.
   */
  public const EXCHANGE_EXCHANGE_PLATFORMID = 'EXCHANGE_PLATFORMID';
  /**
   * PubMatic.
   */
  public const EXCHANGE_EXCHANGE_PUBMATIC = 'EXCHANGE_PUBMATIC';
  /**
   * PulsePoint.
   */
  public const EXCHANGE_EXCHANGE_PULSEPOINT = 'EXCHANGE_PULSEPOINT';
  /**
   * RevenueMax.
   */
  public const EXCHANGE_EXCHANGE_REVENUEMAX = 'EXCHANGE_REVENUEMAX';
  /**
   * Rubicon.
   */
  public const EXCHANGE_EXCHANGE_RUBICON = 'EXCHANGE_RUBICON';
  /**
   * SmartClip.
   */
  public const EXCHANGE_EXCHANGE_SMARTCLIP = 'EXCHANGE_SMARTCLIP';
  /**
   * SmartRTB+.
   */
  public const EXCHANGE_EXCHANGE_SMARTRTB = 'EXCHANGE_SMARTRTB';
  /**
   * SmartstreamTv.
   */
  public const EXCHANGE_EXCHANGE_SMARTSTREAMTV = 'EXCHANGE_SMARTSTREAMTV';
  /**
   * Sovrn.
   */
  public const EXCHANGE_EXCHANGE_SOVRN = 'EXCHANGE_SOVRN';
  /**
   * SpotXchange.
   */
  public const EXCHANGE_EXCHANGE_SPOTXCHANGE = 'EXCHANGE_SPOTXCHANGE';
  /**
   * Ströer SSP.
   */
  public const EXCHANGE_EXCHANGE_STROER = 'EXCHANGE_STROER';
  /**
   * TeadsTv.
   */
  public const EXCHANGE_EXCHANGE_TEADSTV = 'EXCHANGE_TEADSTV';
  /**
   * Telaria.
   */
  public const EXCHANGE_EXCHANGE_TELARIA = 'EXCHANGE_TELARIA';
  /**
   * TVN.
   */
  public const EXCHANGE_EXCHANGE_TVN = 'EXCHANGE_TVN';
  /**
   * United.
   */
  public const EXCHANGE_EXCHANGE_UNITED = 'EXCHANGE_UNITED';
  /**
   * Yieldlab.
   */
  public const EXCHANGE_EXCHANGE_YIELDLAB = 'EXCHANGE_YIELDLAB';
  /**
   * Yieldmo.
   */
  public const EXCHANGE_EXCHANGE_YIELDMO = 'EXCHANGE_YIELDMO';
  /**
   * UnrulyX.
   */
  public const EXCHANGE_EXCHANGE_UNRULYX = 'EXCHANGE_UNRULYX';
  /**
   * Open8.
   */
  public const EXCHANGE_EXCHANGE_OPEN8 = 'EXCHANGE_OPEN8';
  /**
   * Triton.
   */
  public const EXCHANGE_EXCHANGE_TRITON = 'EXCHANGE_TRITON';
  /**
   * TripleLift.
   */
  public const EXCHANGE_EXCHANGE_TRIPLELIFT = 'EXCHANGE_TRIPLELIFT';
  /**
   * Taboola.
   */
  public const EXCHANGE_EXCHANGE_TABOOLA = 'EXCHANGE_TABOOLA';
  /**
   * InMobi.
   */
  public const EXCHANGE_EXCHANGE_INMOBI = 'EXCHANGE_INMOBI';
  /**
   * Smaato.
   */
  public const EXCHANGE_EXCHANGE_SMAATO = 'EXCHANGE_SMAATO';
  /**
   * Aja.
   */
  public const EXCHANGE_EXCHANGE_AJA = 'EXCHANGE_AJA';
  /**
   * Supership.
   */
  public const EXCHANGE_EXCHANGE_SUPERSHIP = 'EXCHANGE_SUPERSHIP';
  /**
   * Nexstar Digital.
   */
  public const EXCHANGE_EXCHANGE_NEXSTAR_DIGITAL = 'EXCHANGE_NEXSTAR_DIGITAL';
  /**
   * Waze.
   */
  public const EXCHANGE_EXCHANGE_WAZE = 'EXCHANGE_WAZE';
  /**
   * SoundCast.
   */
  public const EXCHANGE_EXCHANGE_SOUNDCAST = 'EXCHANGE_SOUNDCAST';
  /**
   * Sharethrough.
   */
  public const EXCHANGE_EXCHANGE_SHARETHROUGH = 'EXCHANGE_SHARETHROUGH';
  /**
   * Fyber.
   */
  public const EXCHANGE_EXCHANGE_FYBER = 'EXCHANGE_FYBER';
  /**
   * Red For Publishers.
   */
  public const EXCHANGE_EXCHANGE_RED_FOR_PUBLISHERS = 'EXCHANGE_RED_FOR_PUBLISHERS';
  /**
   * Media.net.
   */
  public const EXCHANGE_EXCHANGE_MEDIANET = 'EXCHANGE_MEDIANET';
  /**
   * Tapjoy.
   */
  public const EXCHANGE_EXCHANGE_TAPJOY = 'EXCHANGE_TAPJOY';
  /**
   * Vistar.
   */
  public const EXCHANGE_EXCHANGE_VISTAR = 'EXCHANGE_VISTAR';
  /**
   * DAX.
   */
  public const EXCHANGE_EXCHANGE_DAX = 'EXCHANGE_DAX';
  /**
   * JCD.
   */
  public const EXCHANGE_EXCHANGE_JCD = 'EXCHANGE_JCD';
  /**
   * Place Exchange.
   */
  public const EXCHANGE_EXCHANGE_PLACE_EXCHANGE = 'EXCHANGE_PLACE_EXCHANGE';
  /**
   * AppLovin.
   */
  public const EXCHANGE_EXCHANGE_APPLOVIN = 'EXCHANGE_APPLOVIN';
  /**
   * Connatix.
   */
  public const EXCHANGE_EXCHANGE_CONNATIX = 'EXCHANGE_CONNATIX';
  /**
   * Reset Digital.
   */
  public const EXCHANGE_EXCHANGE_RESET_DIGITAL = 'EXCHANGE_RESET_DIGITAL';
  /**
   * Hivestack.
   */
  public const EXCHANGE_EXCHANGE_HIVESTACK = 'EXCHANGE_HIVESTACK';
  /**
   * Drax.
   */
  public const EXCHANGE_EXCHANGE_DRAX = 'EXCHANGE_DRAX';
  /**
   * AppLovin MAX.
   */
  public const EXCHANGE_EXCHANGE_APPLOVIN_GBID = 'EXCHANGE_APPLOVIN_GBID';
  /**
   * DT Fairbid.
   */
  public const EXCHANGE_EXCHANGE_FYBER_GBID = 'EXCHANGE_FYBER_GBID';
  /**
   * Unity LevelPlay.
   */
  public const EXCHANGE_EXCHANGE_UNITY_GBID = 'EXCHANGE_UNITY_GBID';
  /**
   * Chartboost Mediation.
   */
  public const EXCHANGE_EXCHANGE_CHARTBOOST_GBID = 'EXCHANGE_CHARTBOOST_GBID';
  /**
   * AdMost.
   */
  public const EXCHANGE_EXCHANGE_ADMOST_GBID = 'EXCHANGE_ADMOST_GBID';
  /**
   * TopOn.
   */
  public const EXCHANGE_EXCHANGE_TOPON_GBID = 'EXCHANGE_TOPON_GBID';
  /**
   * Netflix.
   */
  public const EXCHANGE_EXCHANGE_NETFLIX = 'EXCHANGE_NETFLIX';
  /**
   * Core.
   */
  public const EXCHANGE_EXCHANGE_CORE = 'EXCHANGE_CORE';
  /**
   * Commerce Grid.
   */
  public const EXCHANGE_EXCHANGE_COMMERCE_GRID = 'EXCHANGE_COMMERCE_GRID';
  /**
   * Spotify.
   */
  public const EXCHANGE_EXCHANGE_SPOTIFY = 'EXCHANGE_SPOTIFY';
  /**
   * Tubi.
   */
  public const EXCHANGE_EXCHANGE_TUBI = 'EXCHANGE_TUBI';
  /**
   * Snap.
   */
  public const EXCHANGE_EXCHANGE_SNAP = 'EXCHANGE_SNAP';
  /**
   * Cadent.
   */
  public const EXCHANGE_EXCHANGE_CADENT = 'EXCHANGE_CADENT';
  /**
   * Output only. The type of exchange.
   *
   * @var string
   */
  public $exchange;

  /**
   * Output only. The type of exchange.
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
   * @param self::EXCHANGE_* $exchange
   */
  public function setExchange($exchange)
  {
    $this->exchange = $exchange;
  }
  /**
   * @return self::EXCHANGE_*
   */
  public function getExchange()
  {
    return $this->exchange;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ExchangeTargetingOptionDetails::class, 'Google_Service_DisplayVideo_ExchangeTargetingOptionDetails');
