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

namespace Google\Service\AdExchangeBuyerII;

class FilterSet extends \Google\Collection
{
  /**
   * A placeholder for an undefined environment; indicates that no environment
   * filter will be applied.
   */
  public const ENVIRONMENT_ENVIRONMENT_UNSPECIFIED = 'ENVIRONMENT_UNSPECIFIED';
  /**
   * The ad impression appears on the web.
   */
  public const ENVIRONMENT_WEB = 'WEB';
  /**
   * The ad impression appears in an app.
   */
  public const ENVIRONMENT_APP = 'APP';
  /**
   * A placeholder for an undefined format; indicates that no format filter will
   * be applied.
   */
  public const FORMAT_FORMAT_UNSPECIFIED = 'FORMAT_UNSPECIFIED';
  /**
   * The ad impression is a native ad, and display (for example, image) format.
   */
  public const FORMAT_NATIVE_DISPLAY = 'NATIVE_DISPLAY';
  /**
   * The ad impression is a native ad, and video format.
   */
  public const FORMAT_NATIVE_VIDEO = 'NATIVE_VIDEO';
  /**
   * The ad impression is not a native ad, and display (for example, image)
   * format.
   */
  public const FORMAT_NON_NATIVE_DISPLAY = 'NON_NATIVE_DISPLAY';
  /**
   * The ad impression is not a native ad, and video format.
   */
  public const FORMAT_NON_NATIVE_VIDEO = 'NON_NATIVE_VIDEO';
  /**
   * A placeholder for an unspecified interval; no time series is applied. All
   * rows in response will contain data for the entire requested time range.
   */
  public const TIME_SERIES_GRANULARITY_TIME_SERIES_GRANULARITY_UNSPECIFIED = 'TIME_SERIES_GRANULARITY_UNSPECIFIED';
  /**
   * Indicates that data will be broken down by the hour.
   */
  public const TIME_SERIES_GRANULARITY_HOURLY = 'HOURLY';
  /**
   * Indicates that data will be broken down by the day.
   */
  public const TIME_SERIES_GRANULARITY_DAILY = 'DAILY';
  protected $collection_key = 'sellerNetworkIds';
  protected $absoluteDateRangeType = AbsoluteDateRange::class;
  protected $absoluteDateRangeDataType = '';
  /**
   * The set of dimensions along which to break down the response; may be empty.
   * If multiple dimensions are requested, the breakdown is along the Cartesian
   * product of the requested dimensions.
   *
   * @var string[]
   */
  public $breakdownDimensions;
  /**
   * The ID of the creative on which to filter; optional. This field may be set
   * only for a filter set that accesses account-level troubleshooting data, for
   * example, one whose name matches the `bidders/accounts/filterSets` pattern.
   *
   * @var string
   */
  public $creativeId;
  /**
   * The ID of the deal on which to filter; optional. This field may be set only
   * for a filter set that accesses account-level troubleshooting data, for
   * example, one whose name matches the `bidders/accounts/filterSets` pattern.
   *
   * @var string
   */
  public $dealId;
  /**
   * The environment on which to filter; optional.
   *
   * @var string
   */
  public $environment;
  /**
   * Creative format bidded on or allowed to bid on, can be empty.
   *
   * @var string
   */
  public $format;
  /**
   * Creative formats bidded on or allowed to bid on, can be empty. Although
   * this field is a list, it can only be populated with a single item. A HTTP
   * 400 bad request error will be returned in the response if you specify
   * multiple items.
   *
   * @deprecated
   * @var string[]
   */
  public $formats;
  /**
   * A user-defined name of the filter set. Filter set names must be unique
   * globally and match one of the patterns: - `bidders/filterSets` (for
   * accessing bidder-level troubleshooting data) -
   * `bidders/accounts/filterSets` (for accessing account-level troubleshooting
   * data) This field is required in create operations.
   *
   * @var string
   */
  public $name;
  /**
   * The list of platforms on which to filter; may be empty. The filters
   * represented by multiple platforms are ORed together (for example, if non-
   * empty, results must match any one of the platforms).
   *
   * @var string[]
   */
  public $platforms;
  /**
   * For Open Bidding partners only. The list of publisher identifiers on which
   * to filter; may be empty. The filters represented by multiple publisher
   * identifiers are ORed together.
   *
   * @var string[]
   */
  public $publisherIdentifiers;
  protected $realtimeTimeRangeType = RealtimeTimeRange::class;
  protected $realtimeTimeRangeDataType = '';
  protected $relativeDateRangeType = RelativeDateRange::class;
  protected $relativeDateRangeDataType = '';
  /**
   * For Authorized Buyers only. The list of IDs of the seller (publisher)
   * networks on which to filter; may be empty. The filters represented by
   * multiple seller network IDs are ORed together (for example, if non-empty,
   * results must match any one of the publisher networks). See [seller-network-
   * ids](https://developers.google.com/authorized-buyers/rtb/downloads/seller-
   * network-ids) file for the set of existing seller network IDs.
   *
   * @var int[]
   */
  public $sellerNetworkIds;
  /**
   * The granularity of time intervals if a time series breakdown is preferred;
   * optional.
   *
   * @var string
   */
  public $timeSeriesGranularity;

  /**
   * An absolute date range, defined by a start date and an end date.
   * Interpreted relative to Pacific time zone.
   *
   * @param AbsoluteDateRange $absoluteDateRange
   */
  public function setAbsoluteDateRange(AbsoluteDateRange $absoluteDateRange)
  {
    $this->absoluteDateRange = $absoluteDateRange;
  }
  /**
   * @return AbsoluteDateRange
   */
  public function getAbsoluteDateRange()
  {
    return $this->absoluteDateRange;
  }
  /**
   * The set of dimensions along which to break down the response; may be empty.
   * If multiple dimensions are requested, the breakdown is along the Cartesian
   * product of the requested dimensions.
   *
   * @param string[] $breakdownDimensions
   */
  public function setBreakdownDimensions($breakdownDimensions)
  {
    $this->breakdownDimensions = $breakdownDimensions;
  }
  /**
   * @return string[]
   */
  public function getBreakdownDimensions()
  {
    return $this->breakdownDimensions;
  }
  /**
   * The ID of the creative on which to filter; optional. This field may be set
   * only for a filter set that accesses account-level troubleshooting data, for
   * example, one whose name matches the `bidders/accounts/filterSets` pattern.
   *
   * @param string $creativeId
   */
  public function setCreativeId($creativeId)
  {
    $this->creativeId = $creativeId;
  }
  /**
   * @return string
   */
  public function getCreativeId()
  {
    return $this->creativeId;
  }
  /**
   * The ID of the deal on which to filter; optional. This field may be set only
   * for a filter set that accesses account-level troubleshooting data, for
   * example, one whose name matches the `bidders/accounts/filterSets` pattern.
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
   * The environment on which to filter; optional.
   *
   * Accepted values: ENVIRONMENT_UNSPECIFIED, WEB, APP
   *
   * @param self::ENVIRONMENT_* $environment
   */
  public function setEnvironment($environment)
  {
    $this->environment = $environment;
  }
  /**
   * @return self::ENVIRONMENT_*
   */
  public function getEnvironment()
  {
    return $this->environment;
  }
  /**
   * Creative format bidded on or allowed to bid on, can be empty.
   *
   * Accepted values: FORMAT_UNSPECIFIED, NATIVE_DISPLAY, NATIVE_VIDEO,
   * NON_NATIVE_DISPLAY, NON_NATIVE_VIDEO
   *
   * @param self::FORMAT_* $format
   */
  public function setFormat($format)
  {
    $this->format = $format;
  }
  /**
   * @return self::FORMAT_*
   */
  public function getFormat()
  {
    return $this->format;
  }
  /**
   * Creative formats bidded on or allowed to bid on, can be empty. Although
   * this field is a list, it can only be populated with a single item. A HTTP
   * 400 bad request error will be returned in the response if you specify
   * multiple items.
   *
   * @deprecated
   * @param string[] $formats
   */
  public function setFormats($formats)
  {
    $this->formats = $formats;
  }
  /**
   * @deprecated
   * @return string[]
   */
  public function getFormats()
  {
    return $this->formats;
  }
  /**
   * A user-defined name of the filter set. Filter set names must be unique
   * globally and match one of the patterns: - `bidders/filterSets` (for
   * accessing bidder-level troubleshooting data) -
   * `bidders/accounts/filterSets` (for accessing account-level troubleshooting
   * data) This field is required in create operations.
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
   * The list of platforms on which to filter; may be empty. The filters
   * represented by multiple platforms are ORed together (for example, if non-
   * empty, results must match any one of the platforms).
   *
   * @param string[] $platforms
   */
  public function setPlatforms($platforms)
  {
    $this->platforms = $platforms;
  }
  /**
   * @return string[]
   */
  public function getPlatforms()
  {
    return $this->platforms;
  }
  /**
   * For Open Bidding partners only. The list of publisher identifiers on which
   * to filter; may be empty. The filters represented by multiple publisher
   * identifiers are ORed together.
   *
   * @param string[] $publisherIdentifiers
   */
  public function setPublisherIdentifiers($publisherIdentifiers)
  {
    $this->publisherIdentifiers = $publisherIdentifiers;
  }
  /**
   * @return string[]
   */
  public function getPublisherIdentifiers()
  {
    return $this->publisherIdentifiers;
  }
  /**
   * An open-ended realtime time range, defined by the aggregation start
   * timestamp.
   *
   * @param RealtimeTimeRange $realtimeTimeRange
   */
  public function setRealtimeTimeRange(RealtimeTimeRange $realtimeTimeRange)
  {
    $this->realtimeTimeRange = $realtimeTimeRange;
  }
  /**
   * @return RealtimeTimeRange
   */
  public function getRealtimeTimeRange()
  {
    return $this->realtimeTimeRange;
  }
  /**
   * A relative date range, defined by an offset from today and a duration.
   * Interpreted relative to Pacific time zone.
   *
   * @param RelativeDateRange $relativeDateRange
   */
  public function setRelativeDateRange(RelativeDateRange $relativeDateRange)
  {
    $this->relativeDateRange = $relativeDateRange;
  }
  /**
   * @return RelativeDateRange
   */
  public function getRelativeDateRange()
  {
    return $this->relativeDateRange;
  }
  /**
   * For Authorized Buyers only. The list of IDs of the seller (publisher)
   * networks on which to filter; may be empty. The filters represented by
   * multiple seller network IDs are ORed together (for example, if non-empty,
   * results must match any one of the publisher networks). See [seller-network-
   * ids](https://developers.google.com/authorized-buyers/rtb/downloads/seller-
   * network-ids) file for the set of existing seller network IDs.
   *
   * @param int[] $sellerNetworkIds
   */
  public function setSellerNetworkIds($sellerNetworkIds)
  {
    $this->sellerNetworkIds = $sellerNetworkIds;
  }
  /**
   * @return int[]
   */
  public function getSellerNetworkIds()
  {
    return $this->sellerNetworkIds;
  }
  /**
   * The granularity of time intervals if a time series breakdown is preferred;
   * optional.
   *
   * Accepted values: TIME_SERIES_GRANULARITY_UNSPECIFIED, HOURLY, DAILY
   *
   * @param self::TIME_SERIES_GRANULARITY_* $timeSeriesGranularity
   */
  public function setTimeSeriesGranularity($timeSeriesGranularity)
  {
    $this->timeSeriesGranularity = $timeSeriesGranularity;
  }
  /**
   * @return self::TIME_SERIES_GRANULARITY_*
   */
  public function getTimeSeriesGranularity()
  {
    return $this->timeSeriesGranularity;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FilterSet::class, 'Google_Service_AdExchangeBuyerII_FilterSet');
