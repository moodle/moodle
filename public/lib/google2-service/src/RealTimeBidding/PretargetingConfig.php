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

namespace Google\Service\RealTimeBidding;

class PretargetingConfig extends \Google\Collection
{
  /**
   * Unspecified interstitial targeting. Represents an interstitial-agnostic
   * selection.
   */
  public const INTERSTITIAL_TARGETING_INTERSTITIAL_TARGETING_UNSPECIFIED = 'INTERSTITIAL_TARGETING_UNSPECIFIED';
  /**
   * Only bid requests for interstitial inventory should be sent.
   */
  public const INTERSTITIAL_TARGETING_ONLY_INTERSTITIAL_REQUESTS = 'ONLY_INTERSTITIAL_REQUESTS';
  /**
   * Only bid requests for non-interstitial inventory should be sent.
   */
  public const INTERSTITIAL_TARGETING_ONLY_NON_INTERSTITIAL_REQUESTS = 'ONLY_NON_INTERSTITIAL_REQUESTS';
  /**
   * Placeholder for undefined state.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * This pretargeting configuration is actively being used to filter bid
   * requests.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * This pretargeting configuration is suspended and not used in serving.
   */
  public const STATE_SUSPENDED = 'SUSPENDED';
  protected $collection_key = 'invalidGeoIds';
  /**
   * Targeting modes included by this configuration. A bid request must allow
   * all the specified targeting modes. An unset value allows all bid requests
   * to be sent, regardless of which targeting modes they allow.
   *
   * @var string[]
   */
  public $allowedUserTargetingModes;
  protected $appTargetingType = AppTargeting::class;
  protected $appTargetingDataType = '';
  /**
   * Output only. The identifier that corresponds to this pretargeting
   * configuration that helps buyers track and attribute their spend across
   * their own arbitrary divisions. If a bid request matches more than one
   * configuration, the buyer chooses which billing_id to attribute each of
   * their bids.
   *
   * @var string
   */
  public $billingId;
  /**
   * The diplay name associated with this configuration. This name must be
   * unique among all the pretargeting configurations a bidder has.
   *
   * @var string
   */
  public $displayName;
  /**
   * The sensitive content category label IDs excluded in this configuration.
   * Bid requests for inventory with any of the specified content label IDs will
   * not be sent. Refer to this file https://storage.googleapis.com/adx-rtb-
   * dictionaries/content-labels.txt for category IDs.
   *
   * @var string[]
   */
  public $excludedContentLabelIds;
  protected $geoTargetingType = NumericTargetingDimension::class;
  protected $geoTargetingDataType = '';
  protected $includedCreativeDimensionsType = CreativeDimensions::class;
  protected $includedCreativeDimensionsDataType = 'array';
  /**
   * Environments that are being included. Bid requests will not be sent for a
   * given environment if it is not included. Further restrictions can be
   * applied to included environments to target only a subset of its inventory.
   * An unset value includes all environments.
   *
   * @var string[]
   */
  public $includedEnvironments;
  /**
   * Creative formats included by this configuration. Only bid requests eligible
   * for at least one of the specified creative formats will be sent. An unset
   * value will allow all bid requests to be sent, regardless of format.
   *
   * @var string[]
   */
  public $includedFormats;
  /**
   * The languages included in this configuration, represented by their language
   * code. See
   * https://developers.google.com/adwords/api/docs/appendix/languagecodes.
   *
   * @var string[]
   */
  public $includedLanguages;
  /**
   * The mobile operating systems included in this configuration as defined in
   * https://storage.googleapis.com/adx-rtb-dictionaries/mobile-os.csv
   *
   * @var string[]
   */
  public $includedMobileOperatingSystemIds;
  /**
   * The platforms included by this configration. Bid requests for devices with
   * the specified platform types will be sent. An unset value allows all bid
   * requests to be sent, regardless of platform.
   *
   * @var string[]
   */
  public $includedPlatforms;
  /**
   * User identifier types included in this configuration. At least one of the
   * user identifier types specified in this list must be available for the bid
   * request to be sent.
   *
   * @var string[]
   */
  public $includedUserIdTypes;
  /**
   * The interstitial targeting specified for this configuration. The unset
   * value will allow bid requests to be sent regardless of whether they are for
   * interstitials or not.
   *
   * @var string
   */
  public $interstitialTargeting;
  /**
   * Output only. Existing included or excluded geos that are invalid.
   * Previously targeted geos may become invalid due to privacy restrictions.
   *
   * @var string[]
   */
  public $invalidGeoIds;
  /**
   * The maximum QPS threshold for this configuration. The bidder should receive
   * no more than this number of bid requests matching this configuration per
   * second across all their bidding endpoints among all trading locations.
   * Further information available at https://developers.google.com/authorized-
   * buyers/rtb/peer-guide
   *
   * @var string
   */
  public $maximumQps;
  /**
   * The targeted minimum viewability decile, ranging in values [0, 10]. A value
   * of 5 means that the configuration will only match adslots for which we
   * predict at least 50% viewability. Values > 10 will be rounded down to 10.
   * An unset value or a value of 0 indicates that bid requests will be sent
   * regardless of viewability.
   *
   * @var int
   */
  public $minimumViewabilityDecile;
  /**
   * Output only. Name of the pretargeting configuration that must follow the
   * pattern `bidders/{bidder_account_id}/pretargetingConfigs/{config_id}`
   *
   * @var string
   */
  public $name;
  protected $publisherTargetingType = StringTargetingDimension::class;
  protected $publisherTargetingDataType = '';
  /**
   * Output only. The state of this pretargeting configuration.
   *
   * @var string
   */
  public $state;
  protected $userListTargetingType = NumericTargetingDimension::class;
  protected $userListTargetingDataType = '';
  protected $verticalTargetingType = NumericTargetingDimension::class;
  protected $verticalTargetingDataType = '';
  protected $webTargetingType = StringTargetingDimension::class;
  protected $webTargetingDataType = '';

  /**
   * Targeting modes included by this configuration. A bid request must allow
   * all the specified targeting modes. An unset value allows all bid requests
   * to be sent, regardless of which targeting modes they allow.
   *
   * @param string[] $allowedUserTargetingModes
   */
  public function setAllowedUserTargetingModes($allowedUserTargetingModes)
  {
    $this->allowedUserTargetingModes = $allowedUserTargetingModes;
  }
  /**
   * @return string[]
   */
  public function getAllowedUserTargetingModes()
  {
    return $this->allowedUserTargetingModes;
  }
  /**
   * Targeting on a subset of app inventory. If APP is listed in
   * targeted_environments, the specified targeting is applied. A maximum of
   * 30,000 app IDs can be targeted. An unset value for targeting allows all
   * app-based bid requests to be sent. Apps can either be targeting positively
   * (bid requests will be sent only if the destination app is listed in the
   * targeting dimension) or negatively (bid requests will be sent only if the
   * destination app is not listed in the targeting dimension).
   *
   * @param AppTargeting $appTargeting
   */
  public function setAppTargeting(AppTargeting $appTargeting)
  {
    $this->appTargeting = $appTargeting;
  }
  /**
   * @return AppTargeting
   */
  public function getAppTargeting()
  {
    return $this->appTargeting;
  }
  /**
   * Output only. The identifier that corresponds to this pretargeting
   * configuration that helps buyers track and attribute their spend across
   * their own arbitrary divisions. If a bid request matches more than one
   * configuration, the buyer chooses which billing_id to attribute each of
   * their bids.
   *
   * @param string $billingId
   */
  public function setBillingId($billingId)
  {
    $this->billingId = $billingId;
  }
  /**
   * @return string
   */
  public function getBillingId()
  {
    return $this->billingId;
  }
  /**
   * The diplay name associated with this configuration. This name must be
   * unique among all the pretargeting configurations a bidder has.
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
   * The sensitive content category label IDs excluded in this configuration.
   * Bid requests for inventory with any of the specified content label IDs will
   * not be sent. Refer to this file https://storage.googleapis.com/adx-rtb-
   * dictionaries/content-labels.txt for category IDs.
   *
   * @param string[] $excludedContentLabelIds
   */
  public function setExcludedContentLabelIds($excludedContentLabelIds)
  {
    $this->excludedContentLabelIds = $excludedContentLabelIds;
  }
  /**
   * @return string[]
   */
  public function getExcludedContentLabelIds()
  {
    return $this->excludedContentLabelIds;
  }
  /**
   * The geos included or excluded in this configuration defined in
   * https://storage.googleapis.com/adx-rtb-dictionaries/geo-table.csv
   *
   * @param NumericTargetingDimension $geoTargeting
   */
  public function setGeoTargeting(NumericTargetingDimension $geoTargeting)
  {
    $this->geoTargeting = $geoTargeting;
  }
  /**
   * @return NumericTargetingDimension
   */
  public function getGeoTargeting()
  {
    return $this->geoTargeting;
  }
  /**
   * Creative dimensions included by this configuration. Only bid requests
   * eligible for at least one of the specified creative dimensions will be
   * sent. An unset value allows all bid requests to be sent, regardless of
   * creative dimension.
   *
   * @param CreativeDimensions[] $includedCreativeDimensions
   */
  public function setIncludedCreativeDimensions($includedCreativeDimensions)
  {
    $this->includedCreativeDimensions = $includedCreativeDimensions;
  }
  /**
   * @return CreativeDimensions[]
   */
  public function getIncludedCreativeDimensions()
  {
    return $this->includedCreativeDimensions;
  }
  /**
   * Environments that are being included. Bid requests will not be sent for a
   * given environment if it is not included. Further restrictions can be
   * applied to included environments to target only a subset of its inventory.
   * An unset value includes all environments.
   *
   * @param string[] $includedEnvironments
   */
  public function setIncludedEnvironments($includedEnvironments)
  {
    $this->includedEnvironments = $includedEnvironments;
  }
  /**
   * @return string[]
   */
  public function getIncludedEnvironments()
  {
    return $this->includedEnvironments;
  }
  /**
   * Creative formats included by this configuration. Only bid requests eligible
   * for at least one of the specified creative formats will be sent. An unset
   * value will allow all bid requests to be sent, regardless of format.
   *
   * @param string[] $includedFormats
   */
  public function setIncludedFormats($includedFormats)
  {
    $this->includedFormats = $includedFormats;
  }
  /**
   * @return string[]
   */
  public function getIncludedFormats()
  {
    return $this->includedFormats;
  }
  /**
   * The languages included in this configuration, represented by their language
   * code. See
   * https://developers.google.com/adwords/api/docs/appendix/languagecodes.
   *
   * @param string[] $includedLanguages
   */
  public function setIncludedLanguages($includedLanguages)
  {
    $this->includedLanguages = $includedLanguages;
  }
  /**
   * @return string[]
   */
  public function getIncludedLanguages()
  {
    return $this->includedLanguages;
  }
  /**
   * The mobile operating systems included in this configuration as defined in
   * https://storage.googleapis.com/adx-rtb-dictionaries/mobile-os.csv
   *
   * @param string[] $includedMobileOperatingSystemIds
   */
  public function setIncludedMobileOperatingSystemIds($includedMobileOperatingSystemIds)
  {
    $this->includedMobileOperatingSystemIds = $includedMobileOperatingSystemIds;
  }
  /**
   * @return string[]
   */
  public function getIncludedMobileOperatingSystemIds()
  {
    return $this->includedMobileOperatingSystemIds;
  }
  /**
   * The platforms included by this configration. Bid requests for devices with
   * the specified platform types will be sent. An unset value allows all bid
   * requests to be sent, regardless of platform.
   *
   * @param string[] $includedPlatforms
   */
  public function setIncludedPlatforms($includedPlatforms)
  {
    $this->includedPlatforms = $includedPlatforms;
  }
  /**
   * @return string[]
   */
  public function getIncludedPlatforms()
  {
    return $this->includedPlatforms;
  }
  /**
   * User identifier types included in this configuration. At least one of the
   * user identifier types specified in this list must be available for the bid
   * request to be sent.
   *
   * @param string[] $includedUserIdTypes
   */
  public function setIncludedUserIdTypes($includedUserIdTypes)
  {
    $this->includedUserIdTypes = $includedUserIdTypes;
  }
  /**
   * @return string[]
   */
  public function getIncludedUserIdTypes()
  {
    return $this->includedUserIdTypes;
  }
  /**
   * The interstitial targeting specified for this configuration. The unset
   * value will allow bid requests to be sent regardless of whether they are for
   * interstitials or not.
   *
   * Accepted values: INTERSTITIAL_TARGETING_UNSPECIFIED,
   * ONLY_INTERSTITIAL_REQUESTS, ONLY_NON_INTERSTITIAL_REQUESTS
   *
   * @param self::INTERSTITIAL_TARGETING_* $interstitialTargeting
   */
  public function setInterstitialTargeting($interstitialTargeting)
  {
    $this->interstitialTargeting = $interstitialTargeting;
  }
  /**
   * @return self::INTERSTITIAL_TARGETING_*
   */
  public function getInterstitialTargeting()
  {
    return $this->interstitialTargeting;
  }
  /**
   * Output only. Existing included or excluded geos that are invalid.
   * Previously targeted geos may become invalid due to privacy restrictions.
   *
   * @param string[] $invalidGeoIds
   */
  public function setInvalidGeoIds($invalidGeoIds)
  {
    $this->invalidGeoIds = $invalidGeoIds;
  }
  /**
   * @return string[]
   */
  public function getInvalidGeoIds()
  {
    return $this->invalidGeoIds;
  }
  /**
   * The maximum QPS threshold for this configuration. The bidder should receive
   * no more than this number of bid requests matching this configuration per
   * second across all their bidding endpoints among all trading locations.
   * Further information available at https://developers.google.com/authorized-
   * buyers/rtb/peer-guide
   *
   * @param string $maximumQps
   */
  public function setMaximumQps($maximumQps)
  {
    $this->maximumQps = $maximumQps;
  }
  /**
   * @return string
   */
  public function getMaximumQps()
  {
    return $this->maximumQps;
  }
  /**
   * The targeted minimum viewability decile, ranging in values [0, 10]. A value
   * of 5 means that the configuration will only match adslots for which we
   * predict at least 50% viewability. Values > 10 will be rounded down to 10.
   * An unset value or a value of 0 indicates that bid requests will be sent
   * regardless of viewability.
   *
   * @param int $minimumViewabilityDecile
   */
  public function setMinimumViewabilityDecile($minimumViewabilityDecile)
  {
    $this->minimumViewabilityDecile = $minimumViewabilityDecile;
  }
  /**
   * @return int
   */
  public function getMinimumViewabilityDecile()
  {
    return $this->minimumViewabilityDecile;
  }
  /**
   * Output only. Name of the pretargeting configuration that must follow the
   * pattern `bidders/{bidder_account_id}/pretargetingConfigs/{config_id}`
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
   * Targeting on a subset of publisher inventory. Publishers can either be
   * targeted positively (bid requests will be sent only if the publisher is
   * listed in the targeting dimension) or negatively (bid requests will be sent
   * only if the publisher is not listed in the targeting dimension). A maximum
   * of 10,000 publisher IDs can be targeted. Publisher IDs are found in
   * [ads.txt](https://iabtechlab.com/ads-txt/) / [app-
   * ads.txt](https://iabtechlab.com/app-ads-txt/) and in bid requests in the
   * `BidRequest.publisher_id` field on the [Google RTB
   * protocol](https://developers.google.com/authorized-
   * buyers/rtb/downloads/realtime-bidding-proto) or the
   * `BidRequest.site.publisher.id` / `BidRequest.app.publisher.id` field on the
   * [OpenRTB protocol](https://developers.google.com/authorized-
   * buyers/rtb/downloads/openrtb-adx-proto). Publisher IDs will be returned in
   * the order that they were entered.
   *
   * @param StringTargetingDimension $publisherTargeting
   */
  public function setPublisherTargeting(StringTargetingDimension $publisherTargeting)
  {
    $this->publisherTargeting = $publisherTargeting;
  }
  /**
   * @return StringTargetingDimension
   */
  public function getPublisherTargeting()
  {
    return $this->publisherTargeting;
  }
  /**
   * Output only. The state of this pretargeting configuration.
   *
   * Accepted values: STATE_UNSPECIFIED, ACTIVE, SUSPENDED
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * The remarketing lists included or excluded in this configuration as defined
   * in UserList.
   *
   * @param NumericTargetingDimension $userListTargeting
   */
  public function setUserListTargeting(NumericTargetingDimension $userListTargeting)
  {
    $this->userListTargeting = $userListTargeting;
  }
  /**
   * @return NumericTargetingDimension
   */
  public function getUserListTargeting()
  {
    return $this->userListTargeting;
  }
  /**
   * The verticals included or excluded in this configuration as defined in
   * https://developers.google.com/authorized-buyers/rtb/downloads/publisher-
   * verticals
   *
   * @param NumericTargetingDimension $verticalTargeting
   */
  public function setVerticalTargeting(NumericTargetingDimension $verticalTargeting)
  {
    $this->verticalTargeting = $verticalTargeting;
  }
  /**
   * @return NumericTargetingDimension
   */
  public function getVerticalTargeting()
  {
    return $this->verticalTargeting;
  }
  /**
   * Targeting on a subset of site inventory. If WEB is listed in
   * included_environments, the specified targeting is applied. A maximum of
   * 50,000 site URLs can be targeted. An unset value for targeting allows all
   * web-based bid requests to be sent. Sites can either be targeting positively
   * (bid requests will be sent only if the destination site is listed in the
   * targeting dimension) or negatively (bid requests will be sent only if the
   * destination site is not listed in the pretargeting configuration).
   *
   * @param StringTargetingDimension $webTargeting
   */
  public function setWebTargeting(StringTargetingDimension $webTargeting)
  {
    $this->webTargeting = $webTargeting;
  }
  /**
   * @return StringTargetingDimension
   */
  public function getWebTargeting()
  {
    return $this->webTargeting;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PretargetingConfig::class, 'Google_Service_RealTimeBidding_PretargetingConfig');
