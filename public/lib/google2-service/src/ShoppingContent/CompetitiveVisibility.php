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

class CompetitiveVisibility extends \Google\Model
{
  /**
   * Traffic source is unknown.
   */
  public const TRAFFIC_SOURCE_UNKNOWN = 'UNKNOWN';
  /**
   * Organic traffic.
   */
  public const TRAFFIC_SOURCE_ORGANIC = 'ORGANIC';
  /**
   * Traffic from Ads.
   */
  public const TRAFFIC_SOURCE_ADS = 'ADS';
  /**
   * Organic and Ads traffic.
   */
  public const TRAFFIC_SOURCE_ALL = 'ALL';
  /**
   * [Ads / organic ratio]
   * (https://support.google.com/merchants/answer/11366442#zippy=%2Cadsfree-
   * ratio) shows how often a merchant receives impressions from Shopping ads
   * compared to organic traffic. The number is rounded and bucketed. Available
   * only in `CompetitiveVisibilityTopMerchantView` and
   * `CompetitiveVisibilityCompetitorView`. Cannot be filtered on in the 'WHERE'
   * clause.
   *
   * @var 
   */
  public $adsOrganicRatio;
  /**
   * Change in visibility based on impressions with respect to the start of the
   * selected time range (or first day with non-zero impressions) for a combined
   * set of merchants with highest visibility approximating the market.
   * Available only in `CompetitiveVisibilityBenchmarkView`. Cannot be filtered
   * on in the 'WHERE' clause.
   *
   * @var 
   */
  public $categoryBenchmarkVisibilityTrend;
  /**
   * Google product category ID to calculate the report for, represented in
   * [Google's product
   * taxonomy](https://support.google.com/merchants/answer/6324436). Required in
   * the `SELECT` clause. A `WHERE` condition on
   * `competitive_visibility.category_id` is required in the query.
   *
   * @var string
   */
  public $categoryId;
  /**
   * The country where impression appeared. Required in the `SELECT` clause. A
   * `WHERE` condition on `competitive_visibility.country_code` is required in
   * the query.
   *
   * @var string
   */
  public $countryCode;
  protected $dateType = Date::class;
  protected $dateDataType = '';
  /**
   * Domain of your competitor or your domain, if 'is_your_domain' is true.
   * Available only in `CompetitiveVisibilityTopMerchantView` and
   * `CompetitiveVisibilityCompetitorView`. Required in the `SELECT` clause for
   * `CompetitiveVisibilityTopMerchantView` and
   * `CompetitiveVisibilityCompetitorView`. Cannot be filtered on in the 'WHERE'
   * clause.
   *
   * @var string
   */
  public $domain;
  /**
   * Higher position rate shows how often a competitor’s offer got placed in a
   * higher position on the page than your offer. Available only in
   * `CompetitiveVisibilityTopMerchantView` and
   * `CompetitiveVisibilityCompetitorView`. Cannot be filtered on in the 'WHERE'
   * clause.
   *
   * @var 
   */
  public $higherPositionRate;
  /**
   * True if this row contains data for your domain. Available only in
   * `CompetitiveVisibilityTopMerchantView` and
   * `CompetitiveVisibilityCompetitorView`. Cannot be filtered on in the 'WHERE'
   * clause.
   *
   * @var bool
   */
  public $isYourDomain;
  /**
   * Page overlap rate describes how frequently competing retailers’ offers are
   * shown together with your offers on the same page. Available only in
   * `CompetitiveVisibilityTopMerchantView` and
   * `CompetitiveVisibilityCompetitorView`. Cannot be filtered on in the 'WHERE'
   * clause.
   *
   * @var 
   */
  public $pageOverlapRate;
  /**
   * Position of the domain in the top merchants ranking for the selected keys
   * (`date`, `category_id`, `country_code`, `listing_type`) based on
   * impressions. 1 is the highest. Available only in
   * `CompetitiveVisibilityTopMerchantView` and
   * `CompetitiveVisibilityCompetitorView`. Cannot be filtered on in the 'WHERE'
   * clause.
   *
   * @var string
   */
  public $rank;
  /**
   * Relative visibility shows how often your competitors’ offers are shown
   * compared to your offers. In other words, this is the number of displayed
   * impressions of a competitor retailer divided by the number of your
   * displayed impressions during a selected time range for a selected product
   * category and country. Available only in
   * `CompetitiveVisibilityCompetitorView`. Cannot be filtered on in the 'WHERE'
   * clause.
   *
   * @var 
   */
  public $relativeVisibility;
  /**
   * Type of impression listing. Required in the `SELECT` clause. Cannot be
   * filtered on in the 'WHERE' clause.
   *
   * @var string
   */
  public $trafficSource;
  /**
   * Change in visibility based on impressions for your domain with respect to
   * the start of the selected time range (or first day with non-zero
   * impressions). Available only in `CompetitiveVisibilityBenchmarkView`.
   * Cannot be filtered on in the 'WHERE' clause.
   *
   * @var 
   */
  public $yourDomainVisibilityTrend;

  public function setAdsOrganicRatio($adsOrganicRatio)
  {
    $this->adsOrganicRatio = $adsOrganicRatio;
  }
  public function getAdsOrganicRatio()
  {
    return $this->adsOrganicRatio;
  }
  public function setCategoryBenchmarkVisibilityTrend($categoryBenchmarkVisibilityTrend)
  {
    $this->categoryBenchmarkVisibilityTrend = $categoryBenchmarkVisibilityTrend;
  }
  public function getCategoryBenchmarkVisibilityTrend()
  {
    return $this->categoryBenchmarkVisibilityTrend;
  }
  /**
   * Google product category ID to calculate the report for, represented in
   * [Google's product
   * taxonomy](https://support.google.com/merchants/answer/6324436). Required in
   * the `SELECT` clause. A `WHERE` condition on
   * `competitive_visibility.category_id` is required in the query.
   *
   * @param string $categoryId
   */
  public function setCategoryId($categoryId)
  {
    $this->categoryId = $categoryId;
  }
  /**
   * @return string
   */
  public function getCategoryId()
  {
    return $this->categoryId;
  }
  /**
   * The country where impression appeared. Required in the `SELECT` clause. A
   * `WHERE` condition on `competitive_visibility.country_code` is required in
   * the query.
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
   * Date of this row. Available only in `CompetitiveVisibilityBenchmarkView`
   * and `CompetitiveVisibilityCompetitorView`. Required in the `SELECT` clause
   * for `CompetitiveVisibilityMarketBenchmarkView`.
   *
   * @param Date $date
   */
  public function setDate(Date $date)
  {
    $this->date = $date;
  }
  /**
   * @return Date
   */
  public function getDate()
  {
    return $this->date;
  }
  /**
   * Domain of your competitor or your domain, if 'is_your_domain' is true.
   * Available only in `CompetitiveVisibilityTopMerchantView` and
   * `CompetitiveVisibilityCompetitorView`. Required in the `SELECT` clause for
   * `CompetitiveVisibilityTopMerchantView` and
   * `CompetitiveVisibilityCompetitorView`. Cannot be filtered on in the 'WHERE'
   * clause.
   *
   * @param string $domain
   */
  public function setDomain($domain)
  {
    $this->domain = $domain;
  }
  /**
   * @return string
   */
  public function getDomain()
  {
    return $this->domain;
  }
  public function setHigherPositionRate($higherPositionRate)
  {
    $this->higherPositionRate = $higherPositionRate;
  }
  public function getHigherPositionRate()
  {
    return $this->higherPositionRate;
  }
  /**
   * True if this row contains data for your domain. Available only in
   * `CompetitiveVisibilityTopMerchantView` and
   * `CompetitiveVisibilityCompetitorView`. Cannot be filtered on in the 'WHERE'
   * clause.
   *
   * @param bool $isYourDomain
   */
  public function setIsYourDomain($isYourDomain)
  {
    $this->isYourDomain = $isYourDomain;
  }
  /**
   * @return bool
   */
  public function getIsYourDomain()
  {
    return $this->isYourDomain;
  }
  public function setPageOverlapRate($pageOverlapRate)
  {
    $this->pageOverlapRate = $pageOverlapRate;
  }
  public function getPageOverlapRate()
  {
    return $this->pageOverlapRate;
  }
  /**
   * Position of the domain in the top merchants ranking for the selected keys
   * (`date`, `category_id`, `country_code`, `listing_type`) based on
   * impressions. 1 is the highest. Available only in
   * `CompetitiveVisibilityTopMerchantView` and
   * `CompetitiveVisibilityCompetitorView`. Cannot be filtered on in the 'WHERE'
   * clause.
   *
   * @param string $rank
   */
  public function setRank($rank)
  {
    $this->rank = $rank;
  }
  /**
   * @return string
   */
  public function getRank()
  {
    return $this->rank;
  }
  public function setRelativeVisibility($relativeVisibility)
  {
    $this->relativeVisibility = $relativeVisibility;
  }
  public function getRelativeVisibility()
  {
    return $this->relativeVisibility;
  }
  /**
   * Type of impression listing. Required in the `SELECT` clause. Cannot be
   * filtered on in the 'WHERE' clause.
   *
   * Accepted values: UNKNOWN, ORGANIC, ADS, ALL
   *
   * @param self::TRAFFIC_SOURCE_* $trafficSource
   */
  public function setTrafficSource($trafficSource)
  {
    $this->trafficSource = $trafficSource;
  }
  /**
   * @return self::TRAFFIC_SOURCE_*
   */
  public function getTrafficSource()
  {
    return $this->trafficSource;
  }
  public function setYourDomainVisibilityTrend($yourDomainVisibilityTrend)
  {
    $this->yourDomainVisibilityTrend = $yourDomainVisibilityTrend;
  }
  public function getYourDomainVisibilityTrend()
  {
    return $this->yourDomainVisibilityTrend;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CompetitiveVisibility::class, 'Google_Service_ShoppingContent_CompetitiveVisibility');
