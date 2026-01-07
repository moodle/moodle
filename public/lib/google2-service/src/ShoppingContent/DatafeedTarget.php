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

namespace Google\Service\ShoppingContent;

class DatafeedTarget extends \Google\Collection
{
  protected $collection_key = 'targetCountries';
  /**
   * Deprecated. Use `feedLabel` instead. The country where the items in the
   * feed will be included in the search index, represented as a CLDR territory
   * code.
   *
   * @var string
   */
  public $country;
  /**
   * The list of [destinations to
   * exclude](//support.google.com/merchants/answer/6324486) for this target
   * (corresponds to cleared check boxes in Merchant Center). Products that are
   * excluded from all destinations for more than 7 days are automatically
   * deleted.
   *
   * @var string[]
   */
  public $excludedDestinations;
  /**
   * Feed label for the DatafeedTarget. Either `country` or `feedLabel` is
   * required. If both `feedLabel` and `country` is specified, the values must
   * match. Must be less than or equal to 20 uppercase letters (A-Z), numbers
   * (0-9), and dashes (-).
   *
   * @var string
   */
  public $feedLabel;
  /**
   * The list of [destinations to
   * include](//support.google.com/merchants/answer/7501026) for this target
   * (corresponds to checked check boxes in Merchant Center). Default
   * destinations are always included unless provided in `excludedDestinations`.
   *
   * @var string[]
   */
  public $includedDestinations;
  /**
   * The two-letter ISO 639-1 language of the items in the feed. Must be a valid
   * language for `targets[].country`.
   *
   * @var string
   */
  public $language;
  /**
   * The countries where the items may be displayed. Represented as a CLDR
   * territory code. Will be ignored for "product inventory" feeds.
   *
   * @var string[]
   */
  public $targetCountries;

  /**
   * Deprecated. Use `feedLabel` instead. The country where the items in the
   * feed will be included in the search index, represented as a CLDR territory
   * code.
   *
   * @param string $country
   */
  public function setCountry($country)
  {
    $this->country = $country;
  }
  /**
   * @return string
   */
  public function getCountry()
  {
    return $this->country;
  }
  /**
   * The list of [destinations to
   * exclude](//support.google.com/merchants/answer/6324486) for this target
   * (corresponds to cleared check boxes in Merchant Center). Products that are
   * excluded from all destinations for more than 7 days are automatically
   * deleted.
   *
   * @param string[] $excludedDestinations
   */
  public function setExcludedDestinations($excludedDestinations)
  {
    $this->excludedDestinations = $excludedDestinations;
  }
  /**
   * @return string[]
   */
  public function getExcludedDestinations()
  {
    return $this->excludedDestinations;
  }
  /**
   * Feed label for the DatafeedTarget. Either `country` or `feedLabel` is
   * required. If both `feedLabel` and `country` is specified, the values must
   * match. Must be less than or equal to 20 uppercase letters (A-Z), numbers
   * (0-9), and dashes (-).
   *
   * @param string $feedLabel
   */
  public function setFeedLabel($feedLabel)
  {
    $this->feedLabel = $feedLabel;
  }
  /**
   * @return string
   */
  public function getFeedLabel()
  {
    return $this->feedLabel;
  }
  /**
   * The list of [destinations to
   * include](//support.google.com/merchants/answer/7501026) for this target
   * (corresponds to checked check boxes in Merchant Center). Default
   * destinations are always included unless provided in `excludedDestinations`.
   *
   * @param string[] $includedDestinations
   */
  public function setIncludedDestinations($includedDestinations)
  {
    $this->includedDestinations = $includedDestinations;
  }
  /**
   * @return string[]
   */
  public function getIncludedDestinations()
  {
    return $this->includedDestinations;
  }
  /**
   * The two-letter ISO 639-1 language of the items in the feed. Must be a valid
   * language for `targets[].country`.
   *
   * @param string $language
   */
  public function setLanguage($language)
  {
    $this->language = $language;
  }
  /**
   * @return string
   */
  public function getLanguage()
  {
    return $this->language;
  }
  /**
   * The countries where the items may be displayed. Represented as a CLDR
   * territory code. Will be ignored for "product inventory" feeds.
   *
   * @param string[] $targetCountries
   */
  public function setTargetCountries($targetCountries)
  {
    $this->targetCountries = $targetCountries;
  }
  /**
   * @return string[]
   */
  public function getTargetCountries()
  {
    return $this->targetCountries;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DatafeedTarget::class, 'Google_Service_ShoppingContent_DatafeedTarget');
