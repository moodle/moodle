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

class CreativeServingDecision extends \Google\Collection
{
  /**
   * Default value that should never be used.
   */
  public const DETECTED_CATEGORIES_TAXONOMY_AD_CATEGORY_TAXONOMY_UNSPECIFIED = 'AD_CATEGORY_TAXONOMY_UNSPECIFIED';
  /**
   * Google ad categories taxonomy, including product categories and sensitive
   * categories. Find the category lists at
   * https://developers.google.com/authorized-buyers/rtb/data#reference-data
   */
  public const DETECTED_CATEGORIES_TAXONOMY_GOOGLE_AD_CATEGORY_TAXONOMY = 'GOOGLE_AD_CATEGORY_TAXONOMY';
  /**
   * IAB Content Taxonomy 1.0. See https://github.com/InteractiveAdvertisingBure
   * au/Taxonomies/blob/main/Content%20Taxonomies/Content%20Taxonomy%201.0.tsv
   * for more details.
   */
  public const DETECTED_CATEGORIES_TAXONOMY_IAB_CONTENT_1_0 = 'IAB_CONTENT_1_0';
  protected $collection_key = 'detectedVendorIds';
  protected $adTechnologyProvidersType = AdTechnologyProviders::class;
  protected $adTechnologyProvidersDataType = '';
  protected $chinaPolicyComplianceType = PolicyCompliance::class;
  protected $chinaPolicyComplianceDataType = '';
  protected $dealsPolicyComplianceType = PolicyCompliance::class;
  protected $dealsPolicyComplianceDataType = '';
  protected $detectedAdvertisersType = AdvertiserAndBrand::class;
  protected $detectedAdvertisersDataType = 'array';
  /**
   * Publisher-excludable attributes that were detected for this creative. Can
   * be used to filter the response of the creatives.list method. If the
   * `excluded_attribute` field of a [bid
   * request](https://developers.google.com/authorized-
   * buyers/rtb/downloads/realtime-bidding-proto) contains one of the attributes
   * that were declared or detected for a given creative, and a bid is submitted
   * with that creative, the bid will be filtered before the auction.
   *
   * @var string[]
   */
  public $detectedAttributes;
  /**
   * Output only. IDs of the detected categories. The taxonomy in which the
   * categories are expressed is specified by the detected_categories_taxonomy
   * field. Use this in conjunction with BidRequest.bcat to avoid bidding on
   * impressions where a given ad category is blocked, or to troubleshoot
   * filtered bids. Can be used to filter the response of the creatives.list
   * method.
   *
   * @var string[]
   */
  public $detectedCategories;
  /**
   * Output only. The taxonomy in which the detected_categories field is
   * expressed.
   *
   * @var string
   */
  public $detectedCategoriesTaxonomy;
  /**
   * The set of detected destination URLs for the creative. Can be used to
   * filter the response of the creatives.list method.
   *
   * @var string[]
   */
  public $detectedClickThroughUrls;
  /**
   * The detected domains for this creative.
   *
   * @var string[]
   */
  public $detectedDomains;
  /**
   * The detected languages for this creative. The order is arbitrary. The codes
   * are 2 or 5 characters and are documented at
   * https://developers.google.com/adwords/api/docs/appendix/languagecodes. Can
   * be used to filter the response of the creatives.list method.
   *
   * @var string[]
   */
  public $detectedLanguages;
  /**
   * Detected product categories, if any. See the ad-product-categories.txt file
   * in the technical documentation for a list of IDs. Can be used to filter the
   * response of the creatives.list method.
   *
   * @var int[]
   */
  public $detectedProductCategories;
  /**
   * Detected sensitive categories, if any. Can be used to filter the response
   * of the creatives.list method. See the ad-sensitive-categories.txt file in
   * the technical documentation for a list of IDs. You should use these IDs
   * along with the excluded-sensitive-category field in the bid request to
   * filter your bids.
   *
   * @var int[]
   */
  public $detectedSensitiveCategories;
  /**
   * IDs of the ad technology vendors that were detected to be used by this
   * creative. See https://storage.googleapis.com/adx-rtb-
   * dictionaries/vendors.txt for possible values. Can be used to filter the
   * response of the creatives.list method. If the `allowed_vendor_type` field
   * of a [bid request](https://developers.google.com/authorized-
   * buyers/rtb/downloads/realtime-bidding-proto) does not contain one of the
   * vendor type IDs that were declared or detected for a given creative, and a
   * bid is submitted with that creative, the bid will be filtered before the
   * auction.
   *
   * @var int[]
   */
  public $detectedVendorIds;
  /**
   * The last time the creative status was updated. Can be used to filter the
   * response of the creatives.list method.
   *
   * @var string
   */
  public $lastStatusUpdate;
  protected $networkPolicyComplianceType = PolicyCompliance::class;
  protected $networkPolicyComplianceDataType = '';
  protected $platformPolicyComplianceType = PolicyCompliance::class;
  protected $platformPolicyComplianceDataType = '';
  protected $russiaPolicyComplianceType = PolicyCompliance::class;
  protected $russiaPolicyComplianceDataType = '';

  /**
   * The detected ad technology providers.
   *
   * @param AdTechnologyProviders $adTechnologyProviders
   */
  public function setAdTechnologyProviders(AdTechnologyProviders $adTechnologyProviders)
  {
    $this->adTechnologyProviders = $adTechnologyProviders;
  }
  /**
   * @return AdTechnologyProviders
   */
  public function getAdTechnologyProviders()
  {
    return $this->adTechnologyProviders;
  }
  /**
   * The policy compliance of this creative in China. When approved or
   * disapproved, this applies to both deals and open auction in China. When
   * pending review, this creative is allowed to serve for deals but not for
   * open auction.
   *
   * @param PolicyCompliance $chinaPolicyCompliance
   */
  public function setChinaPolicyCompliance(PolicyCompliance $chinaPolicyCompliance)
  {
    $this->chinaPolicyCompliance = $chinaPolicyCompliance;
  }
  /**
   * @return PolicyCompliance
   */
  public function getChinaPolicyCompliance()
  {
    return $this->chinaPolicyCompliance;
  }
  /**
   * Policy compliance of this creative when bidding on Programmatic Guaranteed
   * and Preferred Deals (outside of Russia and China).
   *
   * @param PolicyCompliance $dealsPolicyCompliance
   */
  public function setDealsPolicyCompliance(PolicyCompliance $dealsPolicyCompliance)
  {
    $this->dealsPolicyCompliance = $dealsPolicyCompliance;
  }
  /**
   * @return PolicyCompliance
   */
  public function getDealsPolicyCompliance()
  {
    return $this->dealsPolicyCompliance;
  }
  /**
   * Detected advertisers and brands.
   *
   * @param AdvertiserAndBrand[] $detectedAdvertisers
   */
  public function setDetectedAdvertisers($detectedAdvertisers)
  {
    $this->detectedAdvertisers = $detectedAdvertisers;
  }
  /**
   * @return AdvertiserAndBrand[]
   */
  public function getDetectedAdvertisers()
  {
    return $this->detectedAdvertisers;
  }
  /**
   * Publisher-excludable attributes that were detected for this creative. Can
   * be used to filter the response of the creatives.list method. If the
   * `excluded_attribute` field of a [bid
   * request](https://developers.google.com/authorized-
   * buyers/rtb/downloads/realtime-bidding-proto) contains one of the attributes
   * that were declared or detected for a given creative, and a bid is submitted
   * with that creative, the bid will be filtered before the auction.
   *
   * @param string[] $detectedAttributes
   */
  public function setDetectedAttributes($detectedAttributes)
  {
    $this->detectedAttributes = $detectedAttributes;
  }
  /**
   * @return string[]
   */
  public function getDetectedAttributes()
  {
    return $this->detectedAttributes;
  }
  /**
   * Output only. IDs of the detected categories. The taxonomy in which the
   * categories are expressed is specified by the detected_categories_taxonomy
   * field. Use this in conjunction with BidRequest.bcat to avoid bidding on
   * impressions where a given ad category is blocked, or to troubleshoot
   * filtered bids. Can be used to filter the response of the creatives.list
   * method.
   *
   * @param string[] $detectedCategories
   */
  public function setDetectedCategories($detectedCategories)
  {
    $this->detectedCategories = $detectedCategories;
  }
  /**
   * @return string[]
   */
  public function getDetectedCategories()
  {
    return $this->detectedCategories;
  }
  /**
   * Output only. The taxonomy in which the detected_categories field is
   * expressed.
   *
   * Accepted values: AD_CATEGORY_TAXONOMY_UNSPECIFIED,
   * GOOGLE_AD_CATEGORY_TAXONOMY, IAB_CONTENT_1_0
   *
   * @param self::DETECTED_CATEGORIES_TAXONOMY_* $detectedCategoriesTaxonomy
   */
  public function setDetectedCategoriesTaxonomy($detectedCategoriesTaxonomy)
  {
    $this->detectedCategoriesTaxonomy = $detectedCategoriesTaxonomy;
  }
  /**
   * @return self::DETECTED_CATEGORIES_TAXONOMY_*
   */
  public function getDetectedCategoriesTaxonomy()
  {
    return $this->detectedCategoriesTaxonomy;
  }
  /**
   * The set of detected destination URLs for the creative. Can be used to
   * filter the response of the creatives.list method.
   *
   * @param string[] $detectedClickThroughUrls
   */
  public function setDetectedClickThroughUrls($detectedClickThroughUrls)
  {
    $this->detectedClickThroughUrls = $detectedClickThroughUrls;
  }
  /**
   * @return string[]
   */
  public function getDetectedClickThroughUrls()
  {
    return $this->detectedClickThroughUrls;
  }
  /**
   * The detected domains for this creative.
   *
   * @param string[] $detectedDomains
   */
  public function setDetectedDomains($detectedDomains)
  {
    $this->detectedDomains = $detectedDomains;
  }
  /**
   * @return string[]
   */
  public function getDetectedDomains()
  {
    return $this->detectedDomains;
  }
  /**
   * The detected languages for this creative. The order is arbitrary. The codes
   * are 2 or 5 characters and are documented at
   * https://developers.google.com/adwords/api/docs/appendix/languagecodes. Can
   * be used to filter the response of the creatives.list method.
   *
   * @param string[] $detectedLanguages
   */
  public function setDetectedLanguages($detectedLanguages)
  {
    $this->detectedLanguages = $detectedLanguages;
  }
  /**
   * @return string[]
   */
  public function getDetectedLanguages()
  {
    return $this->detectedLanguages;
  }
  /**
   * Detected product categories, if any. See the ad-product-categories.txt file
   * in the technical documentation for a list of IDs. Can be used to filter the
   * response of the creatives.list method.
   *
   * @param int[] $detectedProductCategories
   */
  public function setDetectedProductCategories($detectedProductCategories)
  {
    $this->detectedProductCategories = $detectedProductCategories;
  }
  /**
   * @return int[]
   */
  public function getDetectedProductCategories()
  {
    return $this->detectedProductCategories;
  }
  /**
   * Detected sensitive categories, if any. Can be used to filter the response
   * of the creatives.list method. See the ad-sensitive-categories.txt file in
   * the technical documentation for a list of IDs. You should use these IDs
   * along with the excluded-sensitive-category field in the bid request to
   * filter your bids.
   *
   * @param int[] $detectedSensitiveCategories
   */
  public function setDetectedSensitiveCategories($detectedSensitiveCategories)
  {
    $this->detectedSensitiveCategories = $detectedSensitiveCategories;
  }
  /**
   * @return int[]
   */
  public function getDetectedSensitiveCategories()
  {
    return $this->detectedSensitiveCategories;
  }
  /**
   * IDs of the ad technology vendors that were detected to be used by this
   * creative. See https://storage.googleapis.com/adx-rtb-
   * dictionaries/vendors.txt for possible values. Can be used to filter the
   * response of the creatives.list method. If the `allowed_vendor_type` field
   * of a [bid request](https://developers.google.com/authorized-
   * buyers/rtb/downloads/realtime-bidding-proto) does not contain one of the
   * vendor type IDs that were declared or detected for a given creative, and a
   * bid is submitted with that creative, the bid will be filtered before the
   * auction.
   *
   * @param int[] $detectedVendorIds
   */
  public function setDetectedVendorIds($detectedVendorIds)
  {
    $this->detectedVendorIds = $detectedVendorIds;
  }
  /**
   * @return int[]
   */
  public function getDetectedVendorIds()
  {
    return $this->detectedVendorIds;
  }
  /**
   * The last time the creative status was updated. Can be used to filter the
   * response of the creatives.list method.
   *
   * @param string $lastStatusUpdate
   */
  public function setLastStatusUpdate($lastStatusUpdate)
  {
    $this->lastStatusUpdate = $lastStatusUpdate;
  }
  /**
   * @return string
   */
  public function getLastStatusUpdate()
  {
    return $this->lastStatusUpdate;
  }
  /**
   * Policy compliance of this creative when bidding in open auction, private
   * auction, or auction packages (outside of Russia and China).
   *
   * @param PolicyCompliance $networkPolicyCompliance
   */
  public function setNetworkPolicyCompliance(PolicyCompliance $networkPolicyCompliance)
  {
    $this->networkPolicyCompliance = $networkPolicyCompliance;
  }
  /**
   * @return PolicyCompliance
   */
  public function getNetworkPolicyCompliance()
  {
    return $this->networkPolicyCompliance;
  }
  /**
   * Policy compliance of this creative when bidding in Open Bidding (outside of
   * Russia and China). For the list of platform policies, see:
   * https://support.google.com/platformspolicy/answer/3013851.
   *
   * @param PolicyCompliance $platformPolicyCompliance
   */
  public function setPlatformPolicyCompliance(PolicyCompliance $platformPolicyCompliance)
  {
    $this->platformPolicyCompliance = $platformPolicyCompliance;
  }
  /**
   * @return PolicyCompliance
   */
  public function getPlatformPolicyCompliance()
  {
    return $this->platformPolicyCompliance;
  }
  /**
   * The policy compliance of this creative in Russia. When approved or
   * disapproved, this applies to both deals and open auction in Russia. When
   * pending review, this creative is allowed to serve for deals but not for
   * open auction.
   *
   * @param PolicyCompliance $russiaPolicyCompliance
   */
  public function setRussiaPolicyCompliance(PolicyCompliance $russiaPolicyCompliance)
  {
    $this->russiaPolicyCompliance = $russiaPolicyCompliance;
  }
  /**
   * @return PolicyCompliance
   */
  public function getRussiaPolicyCompliance()
  {
    return $this->russiaPolicyCompliance;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CreativeServingDecision::class, 'Google_Service_RealTimeBidding_CreativeServingDecision');
