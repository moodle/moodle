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

class InventorySource extends \Google\Collection
{
  /**
   * The commitment is not specified or is unknown in this version.
   */
  public const COMMITMENT_INVENTORY_SOURCE_COMMITMENT_UNSPECIFIED = 'INVENTORY_SOURCE_COMMITMENT_UNSPECIFIED';
  /**
   * The commitment is guaranteed delivery.
   */
  public const COMMITMENT_INVENTORY_SOURCE_COMMITMENT_GUARANTEED = 'INVENTORY_SOURCE_COMMITMENT_GUARANTEED';
  /**
   * The commitment is non-guaranteed delivery.
   */
  public const COMMITMENT_INVENTORY_SOURCE_COMMITMENT_NON_GUARANTEED = 'INVENTORY_SOURCE_COMMITMENT_NON_GUARANTEED';
  /**
   * The delivery method is not specified or is unknown in this version.
   */
  public const DELIVERY_METHOD_INVENTORY_SOURCE_DELIVERY_METHOD_UNSPECIFIED = 'INVENTORY_SOURCE_DELIVERY_METHOD_UNSPECIFIED';
  /**
   * The delivery method is programmatic.
   */
  public const DELIVERY_METHOD_INVENTORY_SOURCE_DELIVERY_METHOD_PROGRAMMATIC = 'INVENTORY_SOURCE_DELIVERY_METHOD_PROGRAMMATIC';
  /**
   * The delivery method is tag.
   */
  public const DELIVERY_METHOD_INVENTORY_SOURCE_DELIVERY_METHOD_TAG = 'INVENTORY_SOURCE_DELIVERY_METHOD_TAG';
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
   * The product type is not specified or is unknown in this version. Modifying
   * inventory sources of this product type are not supported via API.
   */
  public const INVENTORY_SOURCE_PRODUCT_TYPE_INVENTORY_SOURCE_PRODUCT_TYPE_UNSPECIFIED = 'INVENTORY_SOURCE_PRODUCT_TYPE_UNSPECIFIED';
  /**
   * The inventory source sells inventory through Preferred Deal.
   */
  public const INVENTORY_SOURCE_PRODUCT_TYPE_PREFERRED_DEAL = 'PREFERRED_DEAL';
  /**
   * The inventory source sells inventory through Private Auction.
   */
  public const INVENTORY_SOURCE_PRODUCT_TYPE_PRIVATE_AUCTION = 'PRIVATE_AUCTION';
  /**
   * The inventory source sells inventory through Programmatic Guaranteed.
   */
  public const INVENTORY_SOURCE_PRODUCT_TYPE_PROGRAMMATIC_GUARANTEED = 'PROGRAMMATIC_GUARANTEED';
  /**
   * The inventory source sells inventory through Tag Guaranteed.
   */
  public const INVENTORY_SOURCE_PRODUCT_TYPE_TAG_GUARANTEED = 'TAG_GUARANTEED';
  /**
   * The inventory source sells inventory through YouTube Reserve.
   */
  public const INVENTORY_SOURCE_PRODUCT_TYPE_YOUTUBE_RESERVE = 'YOUTUBE_RESERVE';
  /**
   * The inventory source sells inventory through Instant Reserve. Modifying
   * inventory sources of this product type are not supported via API.
   */
  public const INVENTORY_SOURCE_PRODUCT_TYPE_INSTANT_RESERVE = 'INSTANT_RESERVE';
  /**
   * The inventory source sells inventory through Guaranteed Package. Modifying
   * inventory sources of this product type are not supported via API.
   */
  public const INVENTORY_SOURCE_PRODUCT_TYPE_GUARANTEED_PACKAGE = 'GUARANTEED_PACKAGE';
  /**
   * The inventory source sells inventory through Programmtic TV. Modifying
   * inventory sources of this product type are not supported via API.
   */
  public const INVENTORY_SOURCE_PRODUCT_TYPE_PROGRAMMATIC_TV = 'PROGRAMMATIC_TV';
  /**
   * The inventory source sells inventory through Auction Package. Modifying
   * inventory sources of this product type are not supported via API.
   */
  public const INVENTORY_SOURCE_PRODUCT_TYPE_AUCTION_PACKAGE = 'AUCTION_PACKAGE';
  /**
   * The inventory source type is not specified or is unknown in this version.
   */
  public const INVENTORY_SOURCE_TYPE_INVENTORY_SOURCE_TYPE_UNSPECIFIED = 'INVENTORY_SOURCE_TYPE_UNSPECIFIED';
  /**
   * Private inventory source.
   */
  public const INVENTORY_SOURCE_TYPE_INVENTORY_SOURCE_TYPE_PRIVATE = 'INVENTORY_SOURCE_TYPE_PRIVATE';
  /**
   * Auction package.
   */
  public const INVENTORY_SOURCE_TYPE_INVENTORY_SOURCE_TYPE_AUCTION_PACKAGE = 'INVENTORY_SOURCE_TYPE_AUCTION_PACKAGE';
  protected $collection_key = 'readPartnerIds';
  /**
   * Whether the inventory source has a guaranteed or non-guaranteed delivery.
   *
   * @var string
   */
  public $commitment;
  protected $creativeConfigsType = CreativeConfig::class;
  protected $creativeConfigsDataType = 'array';
  /**
   * The ID in the exchange space that uniquely identifies the inventory source.
   * Must be unique across buyers within each exchange but not necessarily
   * unique across exchanges.
   *
   * @var string
   */
  public $dealId;
  /**
   * The delivery method of the inventory source. * For non-guaranteed inventory
   * sources, the only acceptable value is
   * `INVENTORY_SOURCE_DELIVERY_METHOD_PROGRAMMATIC`. * For guaranteed inventory
   * sources, acceptable values are `INVENTORY_SOURCE_DELIVERY_METHOD_TAG` and
   * `INVENTORY_SOURCE_DELIVERY_METHOD_PROGRAMMATIC`.
   *
   * @var string
   */
  public $deliveryMethod;
  /**
   * The display name of the inventory source. Must be UTF-8 encoded with a
   * maximum size of 240 bytes.
   *
   * @var string
   */
  public $displayName;
  /**
   * The exchange to which the inventory source belongs.
   *
   * @var string
   */
  public $exchange;
  /**
   * Immutable. The ID of the guaranteed order that this inventory source
   * belongs to. Only applicable when commitment is
   * `INVENTORY_SOURCE_COMMITMENT_GUARANTEED`.
   *
   * @var string
   */
  public $guaranteedOrderId;
  /**
   * Output only. The unique ID of the inventory source. Assigned by the system.
   *
   * @var string
   */
  public $inventorySourceId;
  /**
   * Output only. The product type of the inventory source, denoting the way
   * through which it sells inventory.
   *
   * @var string
   */
  public $inventorySourceProductType;
  /**
   * Denotes the type of the inventory source.
   *
   * @var string
   */
  public $inventorySourceType;
  /**
   * Output only. The resource name of the inventory source.
   *
   * @var string
   */
  public $name;
  /**
   * The publisher/seller name of the inventory source.
   *
   * @var string
   */
  public $publisherName;
  protected $rateDetailsType = RateDetails::class;
  protected $rateDetailsDataType = '';
  /**
   * Output only. The IDs of advertisers with read-only access to the inventory
   * source.
   *
   * @var string[]
   */
  public $readAdvertiserIds;
  /**
   * Output only. The IDs of partners with read-only access to the inventory
   * source. All advertisers of partners in this field inherit read-only access
   * to the inventory source.
   *
   * @var string[]
   */
  public $readPartnerIds;
  protected $readWriteAccessorsType = InventorySourceAccessors::class;
  protected $readWriteAccessorsDataType = '';
  protected $statusType = InventorySourceStatus::class;
  protected $statusDataType = '';
  protected $timeRangeType = TimeRange::class;
  protected $timeRangeDataType = '';
  /**
   * Output only. The timestamp when the inventory source was last updated.
   * Assigned by the system.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Whether the inventory source has a guaranteed or non-guaranteed delivery.
   *
   * Accepted values: INVENTORY_SOURCE_COMMITMENT_UNSPECIFIED,
   * INVENTORY_SOURCE_COMMITMENT_GUARANTEED,
   * INVENTORY_SOURCE_COMMITMENT_NON_GUARANTEED
   *
   * @param self::COMMITMENT_* $commitment
   */
  public function setCommitment($commitment)
  {
    $this->commitment = $commitment;
  }
  /**
   * @return self::COMMITMENT_*
   */
  public function getCommitment()
  {
    return $this->commitment;
  }
  /**
   * The creative requirements of the inventory source. Not applicable for
   * auction packages.
   *
   * @param CreativeConfig[] $creativeConfigs
   */
  public function setCreativeConfigs($creativeConfigs)
  {
    $this->creativeConfigs = $creativeConfigs;
  }
  /**
   * @return CreativeConfig[]
   */
  public function getCreativeConfigs()
  {
    return $this->creativeConfigs;
  }
  /**
   * The ID in the exchange space that uniquely identifies the inventory source.
   * Must be unique across buyers within each exchange but not necessarily
   * unique across exchanges.
   *
   * @param string $dealId
   */
  public function setDealId($dealId)
  {
    $this->dealId = $dealId;
  }
  /**
   * @return string
   */
  public function getDealId()
  {
    return $this->dealId;
  }
  /**
   * The delivery method of the inventory source. * For non-guaranteed inventory
   * sources, the only acceptable value is
   * `INVENTORY_SOURCE_DELIVERY_METHOD_PROGRAMMATIC`. * For guaranteed inventory
   * sources, acceptable values are `INVENTORY_SOURCE_DELIVERY_METHOD_TAG` and
   * `INVENTORY_SOURCE_DELIVERY_METHOD_PROGRAMMATIC`.
   *
   * Accepted values: INVENTORY_SOURCE_DELIVERY_METHOD_UNSPECIFIED,
   * INVENTORY_SOURCE_DELIVERY_METHOD_PROGRAMMATIC,
   * INVENTORY_SOURCE_DELIVERY_METHOD_TAG
   *
   * @param self::DELIVERY_METHOD_* $deliveryMethod
   */
  public function setDeliveryMethod($deliveryMethod)
  {
    $this->deliveryMethod = $deliveryMethod;
  }
  /**
   * @return self::DELIVERY_METHOD_*
   */
  public function getDeliveryMethod()
  {
    return $this->deliveryMethod;
  }
  /**
   * The display name of the inventory source. Must be UTF-8 encoded with a
   * maximum size of 240 bytes.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * The exchange to which the inventory source belongs.
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
  /**
   * Immutable. The ID of the guaranteed order that this inventory source
   * belongs to. Only applicable when commitment is
   * `INVENTORY_SOURCE_COMMITMENT_GUARANTEED`.
   *
   * @param string $guaranteedOrderId
   */
  public function setGuaranteedOrderId($guaranteedOrderId)
  {
    $this->guaranteedOrderId = $guaranteedOrderId;
  }
  /**
   * @return string
   */
  public function getGuaranteedOrderId()
  {
    return $this->guaranteedOrderId;
  }
  /**
   * Output only. The unique ID of the inventory source. Assigned by the system.
   *
   * @param string $inventorySourceId
   */
  public function setInventorySourceId($inventorySourceId)
  {
    $this->inventorySourceId = $inventorySourceId;
  }
  /**
   * @return string
   */
  public function getInventorySourceId()
  {
    return $this->inventorySourceId;
  }
  /**
   * Output only. The product type of the inventory source, denoting the way
   * through which it sells inventory.
   *
   * Accepted values: INVENTORY_SOURCE_PRODUCT_TYPE_UNSPECIFIED, PREFERRED_DEAL,
   * PRIVATE_AUCTION, PROGRAMMATIC_GUARANTEED, TAG_GUARANTEED, YOUTUBE_RESERVE,
   * INSTANT_RESERVE, GUARANTEED_PACKAGE, PROGRAMMATIC_TV, AUCTION_PACKAGE
   *
   * @param self::INVENTORY_SOURCE_PRODUCT_TYPE_* $inventorySourceProductType
   */
  public function setInventorySourceProductType($inventorySourceProductType)
  {
    $this->inventorySourceProductType = $inventorySourceProductType;
  }
  /**
   * @return self::INVENTORY_SOURCE_PRODUCT_TYPE_*
   */
  public function getInventorySourceProductType()
  {
    return $this->inventorySourceProductType;
  }
  /**
   * Denotes the type of the inventory source.
   *
   * Accepted values: INVENTORY_SOURCE_TYPE_UNSPECIFIED,
   * INVENTORY_SOURCE_TYPE_PRIVATE, INVENTORY_SOURCE_TYPE_AUCTION_PACKAGE
   *
   * @param self::INVENTORY_SOURCE_TYPE_* $inventorySourceType
   */
  public function setInventorySourceType($inventorySourceType)
  {
    $this->inventorySourceType = $inventorySourceType;
  }
  /**
   * @return self::INVENTORY_SOURCE_TYPE_*
   */
  public function getInventorySourceType()
  {
    return $this->inventorySourceType;
  }
  /**
   * Output only. The resource name of the inventory source.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * The publisher/seller name of the inventory source.
   *
   * @param string $publisherName
   */
  public function setPublisherName($publisherName)
  {
    $this->publisherName = $publisherName;
  }
  /**
   * @return string
   */
  public function getPublisherName()
  {
    return $this->publisherName;
  }
  /**
   * Required. The rate details of the inventory source.
   *
   * @param RateDetails $rateDetails
   */
  public function setRateDetails(RateDetails $rateDetails)
  {
    $this->rateDetails = $rateDetails;
  }
  /**
   * @return RateDetails
   */
  public function getRateDetails()
  {
    return $this->rateDetails;
  }
  /**
   * Output only. The IDs of advertisers with read-only access to the inventory
   * source.
   *
   * @param string[] $readAdvertiserIds
   */
  public function setReadAdvertiserIds($readAdvertiserIds)
  {
    $this->readAdvertiserIds = $readAdvertiserIds;
  }
  /**
   * @return string[]
   */
  public function getReadAdvertiserIds()
  {
    return $this->readAdvertiserIds;
  }
  /**
   * Output only. The IDs of partners with read-only access to the inventory
   * source. All advertisers of partners in this field inherit read-only access
   * to the inventory source.
   *
   * @param string[] $readPartnerIds
   */
  public function setReadPartnerIds($readPartnerIds)
  {
    $this->readPartnerIds = $readPartnerIds;
  }
  /**
   * @return string[]
   */
  public function getReadPartnerIds()
  {
    return $this->readPartnerIds;
  }
  /**
   * The partner or advertisers that have read/write access to the inventory
   * source. Output only when commitment is
   * `INVENTORY_SOURCE_COMMITMENT_GUARANTEED`, in which case the read/write
   * accessors are inherited from the parent guaranteed order. Required when
   * commitment is `INVENTORY_SOURCE_COMMITMENT_NON_GUARANTEED`. If commitment
   * is `INVENTORY_SOURCE_COMMITMENT_NON_GUARANTEED` and a partner is set in
   * this field, all advertisers under this partner will automatically have
   * read-only access to the inventory source. These advertisers will not be
   * included in read_advertiser_ids.
   *
   * @param InventorySourceAccessors $readWriteAccessors
   */
  public function setReadWriteAccessors(InventorySourceAccessors $readWriteAccessors)
  {
    $this->readWriteAccessors = $readWriteAccessors;
  }
  /**
   * @return InventorySourceAccessors
   */
  public function getReadWriteAccessors()
  {
    return $this->readWriteAccessors;
  }
  /**
   * The status settings of the inventory source.
   *
   * @param InventorySourceStatus $status
   */
  public function setStatus(InventorySourceStatus $status)
  {
    $this->status = $status;
  }
  /**
   * @return InventorySourceStatus
   */
  public function getStatus()
  {
    return $this->status;
  }
  /**
   * The time range when this inventory source starts and stops serving.
   *
   * @param TimeRange $timeRange
   */
  public function setTimeRange(TimeRange $timeRange)
  {
    $this->timeRange = $timeRange;
  }
  /**
   * @return TimeRange
   */
  public function getTimeRange()
  {
    return $this->timeRange;
  }
  /**
   * Output only. The timestamp when the inventory source was last updated.
   * Assigned by the system.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InventorySource::class, 'Google_Service_DisplayVideo_InventorySource');
