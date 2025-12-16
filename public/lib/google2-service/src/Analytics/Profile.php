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

namespace Google\Service\Analytics;

class Profile extends \Google\Model
{
  /**
   * Account ID to which this view (profile) belongs.
   *
   * @var string
   */
  public $accountId;
  /**
   * Indicates whether bot filtering is enabled for this view (profile).
   *
   * @var bool
   */
  public $botFilteringEnabled;
  protected $childLinkType = ProfileChildLink::class;
  protected $childLinkDataType = '';
  /**
   * Time this view (profile) was created.
   *
   * @var string
   */
  public $created;
  /**
   * The currency type associated with this view (profile), defaults to USD. The
   * supported values are: USD, JPY, EUR, GBP, AUD, KRW, BRL, CNY, DKK, RUB,
   * SEK, NOK, PLN, TRY, TWD, HKD, THB, IDR, ARS, MXN, VND, PHP, INR, CHF, CAD,
   * CZK, NZD, HUF, BGN, LTL, ZAR, UAH, AED, BOB, CLP, COP, EGP, HRK, ILS, MAD,
   * MYR, PEN, PKR, RON, RSD, SAR, SGD, VEF, LVL
   *
   * @var string
   */
  public $currency;
  /**
   * Default page for this view (profile).
   *
   * @var string
   */
  public $defaultPage;
  /**
   * Indicates whether ecommerce tracking is enabled for this view (profile).
   *
   * @var bool
   */
  public $eCommerceTracking;
  /**
   * Indicates whether enhanced ecommerce tracking is enabled for this view
   * (profile). This property can only be enabled if ecommerce tracking is
   * enabled.
   *
   * @var bool
   */
  public $enhancedECommerceTracking;
  /**
   * The query parameters that are excluded from this view (profile).
   *
   * @var string
   */
  public $excludeQueryParameters;
  /**
   * View (Profile) ID.
   *
   * @var string
   */
  public $id;
  /**
   * Internal ID for the web property to which this view (profile) belongs.
   *
   * @var string
   */
  public $internalWebPropertyId;
  /**
   * Resource type for Analytics view (profile).
   *
   * @var string
   */
  public $kind;
  /**
   * Name of this view (profile).
   *
   * @var string
   */
  public $name;
  protected $parentLinkType = ProfileParentLink::class;
  protected $parentLinkDataType = '';
  protected $permissionsType = ProfilePermissions::class;
  protected $permissionsDataType = '';
  /**
   * Link for this view (profile).
   *
   * @var string
   */
  public $selfLink;
  /**
   * Site search category parameters for this view (profile).
   *
   * @var string
   */
  public $siteSearchCategoryParameters;
  /**
   * The site search query parameters for this view (profile).
   *
   * @var string
   */
  public $siteSearchQueryParameters;
  /**
   * Indicates whether this view (profile) is starred or not.
   *
   * @var bool
   */
  public $starred;
  /**
   * Whether or not Analytics will strip search category parameters from the
   * URLs in your reports.
   *
   * @var bool
   */
  public $stripSiteSearchCategoryParameters;
  /**
   * Whether or not Analytics will strip search query parameters from the URLs
   * in your reports.
   *
   * @var bool
   */
  public $stripSiteSearchQueryParameters;
  /**
   * Time zone for which this view (profile) has been configured. Time zones are
   * identified by strings from the TZ database.
   *
   * @var string
   */
  public $timezone;
  /**
   * View (Profile) type. Supported types: WEB or APP.
   *
   * @var string
   */
  public $type;
  /**
   * Time this view (profile) was last modified.
   *
   * @var string
   */
  public $updated;
  /**
   * Web property ID of the form UA-XXXXX-YY to which this view (profile)
   * belongs.
   *
   * @var string
   */
  public $webPropertyId;
  /**
   * Website URL for this view (profile).
   *
   * @var string
   */
  public $websiteUrl;

  /**
   * Account ID to which this view (profile) belongs.
   *
   * @param string $accountId
   */
  public function setAccountId($accountId)
  {
    $this->accountId = $accountId;
  }
  /**
   * @return string
   */
  public function getAccountId()
  {
    return $this->accountId;
  }
  /**
   * Indicates whether bot filtering is enabled for this view (profile).
   *
   * @param bool $botFilteringEnabled
   */
  public function setBotFilteringEnabled($botFilteringEnabled)
  {
    $this->botFilteringEnabled = $botFilteringEnabled;
  }
  /**
   * @return bool
   */
  public function getBotFilteringEnabled()
  {
    return $this->botFilteringEnabled;
  }
  /**
   * Child link for this view (profile). Points to the list of goals for this
   * view (profile).
   *
   * @param ProfileChildLink $childLink
   */
  public function setChildLink(ProfileChildLink $childLink)
  {
    $this->childLink = $childLink;
  }
  /**
   * @return ProfileChildLink
   */
  public function getChildLink()
  {
    return $this->childLink;
  }
  /**
   * Time this view (profile) was created.
   *
   * @param string $created
   */
  public function setCreated($created)
  {
    $this->created = $created;
  }
  /**
   * @return string
   */
  public function getCreated()
  {
    return $this->created;
  }
  /**
   * The currency type associated with this view (profile), defaults to USD. The
   * supported values are: USD, JPY, EUR, GBP, AUD, KRW, BRL, CNY, DKK, RUB,
   * SEK, NOK, PLN, TRY, TWD, HKD, THB, IDR, ARS, MXN, VND, PHP, INR, CHF, CAD,
   * CZK, NZD, HUF, BGN, LTL, ZAR, UAH, AED, BOB, CLP, COP, EGP, HRK, ILS, MAD,
   * MYR, PEN, PKR, RON, RSD, SAR, SGD, VEF, LVL
   *
   * @param string $currency
   */
  public function setCurrency($currency)
  {
    $this->currency = $currency;
  }
  /**
   * @return string
   */
  public function getCurrency()
  {
    return $this->currency;
  }
  /**
   * Default page for this view (profile).
   *
   * @param string $defaultPage
   */
  public function setDefaultPage($defaultPage)
  {
    $this->defaultPage = $defaultPage;
  }
  /**
   * @return string
   */
  public function getDefaultPage()
  {
    return $this->defaultPage;
  }
  /**
   * Indicates whether ecommerce tracking is enabled for this view (profile).
   *
   * @param bool $eCommerceTracking
   */
  public function setECommerceTracking($eCommerceTracking)
  {
    $this->eCommerceTracking = $eCommerceTracking;
  }
  /**
   * @return bool
   */
  public function getECommerceTracking()
  {
    return $this->eCommerceTracking;
  }
  /**
   * Indicates whether enhanced ecommerce tracking is enabled for this view
   * (profile). This property can only be enabled if ecommerce tracking is
   * enabled.
   *
   * @param bool $enhancedECommerceTracking
   */
  public function setEnhancedECommerceTracking($enhancedECommerceTracking)
  {
    $this->enhancedECommerceTracking = $enhancedECommerceTracking;
  }
  /**
   * @return bool
   */
  public function getEnhancedECommerceTracking()
  {
    return $this->enhancedECommerceTracking;
  }
  /**
   * The query parameters that are excluded from this view (profile).
   *
   * @param string $excludeQueryParameters
   */
  public function setExcludeQueryParameters($excludeQueryParameters)
  {
    $this->excludeQueryParameters = $excludeQueryParameters;
  }
  /**
   * @return string
   */
  public function getExcludeQueryParameters()
  {
    return $this->excludeQueryParameters;
  }
  /**
   * View (Profile) ID.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Internal ID for the web property to which this view (profile) belongs.
   *
   * @param string $internalWebPropertyId
   */
  public function setInternalWebPropertyId($internalWebPropertyId)
  {
    $this->internalWebPropertyId = $internalWebPropertyId;
  }
  /**
   * @return string
   */
  public function getInternalWebPropertyId()
  {
    return $this->internalWebPropertyId;
  }
  /**
   * Resource type for Analytics view (profile).
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * Name of this view (profile).
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
   * Parent link for this view (profile). Points to the web property to which
   * this view (profile) belongs.
   *
   * @param ProfileParentLink $parentLink
   */
  public function setParentLink(ProfileParentLink $parentLink)
  {
    $this->parentLink = $parentLink;
  }
  /**
   * @return ProfileParentLink
   */
  public function getParentLink()
  {
    return $this->parentLink;
  }
  /**
   * Permissions the user has for this view (profile).
   *
   * @param ProfilePermissions $permissions
   */
  public function setPermissions(ProfilePermissions $permissions)
  {
    $this->permissions = $permissions;
  }
  /**
   * @return ProfilePermissions
   */
  public function getPermissions()
  {
    return $this->permissions;
  }
  /**
   * Link for this view (profile).
   *
   * @param string $selfLink
   */
  public function setSelfLink($selfLink)
  {
    $this->selfLink = $selfLink;
  }
  /**
   * @return string
   */
  public function getSelfLink()
  {
    return $this->selfLink;
  }
  /**
   * Site search category parameters for this view (profile).
   *
   * @param string $siteSearchCategoryParameters
   */
  public function setSiteSearchCategoryParameters($siteSearchCategoryParameters)
  {
    $this->siteSearchCategoryParameters = $siteSearchCategoryParameters;
  }
  /**
   * @return string
   */
  public function getSiteSearchCategoryParameters()
  {
    return $this->siteSearchCategoryParameters;
  }
  /**
   * The site search query parameters for this view (profile).
   *
   * @param string $siteSearchQueryParameters
   */
  public function setSiteSearchQueryParameters($siteSearchQueryParameters)
  {
    $this->siteSearchQueryParameters = $siteSearchQueryParameters;
  }
  /**
   * @return string
   */
  public function getSiteSearchQueryParameters()
  {
    return $this->siteSearchQueryParameters;
  }
  /**
   * Indicates whether this view (profile) is starred or not.
   *
   * @param bool $starred
   */
  public function setStarred($starred)
  {
    $this->starred = $starred;
  }
  /**
   * @return bool
   */
  public function getStarred()
  {
    return $this->starred;
  }
  /**
   * Whether or not Analytics will strip search category parameters from the
   * URLs in your reports.
   *
   * @param bool $stripSiteSearchCategoryParameters
   */
  public function setStripSiteSearchCategoryParameters($stripSiteSearchCategoryParameters)
  {
    $this->stripSiteSearchCategoryParameters = $stripSiteSearchCategoryParameters;
  }
  /**
   * @return bool
   */
  public function getStripSiteSearchCategoryParameters()
  {
    return $this->stripSiteSearchCategoryParameters;
  }
  /**
   * Whether or not Analytics will strip search query parameters from the URLs
   * in your reports.
   *
   * @param bool $stripSiteSearchQueryParameters
   */
  public function setStripSiteSearchQueryParameters($stripSiteSearchQueryParameters)
  {
    $this->stripSiteSearchQueryParameters = $stripSiteSearchQueryParameters;
  }
  /**
   * @return bool
   */
  public function getStripSiteSearchQueryParameters()
  {
    return $this->stripSiteSearchQueryParameters;
  }
  /**
   * Time zone for which this view (profile) has been configured. Time zones are
   * identified by strings from the TZ database.
   *
   * @param string $timezone
   */
  public function setTimezone($timezone)
  {
    $this->timezone = $timezone;
  }
  /**
   * @return string
   */
  public function getTimezone()
  {
    return $this->timezone;
  }
  /**
   * View (Profile) type. Supported types: WEB or APP.
   *
   * @param string $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }
  /**
   * Time this view (profile) was last modified.
   *
   * @param string $updated
   */
  public function setUpdated($updated)
  {
    $this->updated = $updated;
  }
  /**
   * @return string
   */
  public function getUpdated()
  {
    return $this->updated;
  }
  /**
   * Web property ID of the form UA-XXXXX-YY to which this view (profile)
   * belongs.
   *
   * @param string $webPropertyId
   */
  public function setWebPropertyId($webPropertyId)
  {
    $this->webPropertyId = $webPropertyId;
  }
  /**
   * @return string
   */
  public function getWebPropertyId()
  {
    return $this->webPropertyId;
  }
  /**
   * Website URL for this view (profile).
   *
   * @param string $websiteUrl
   */
  public function setWebsiteUrl($websiteUrl)
  {
    $this->websiteUrl = $websiteUrl;
  }
  /**
   * @return string
   */
  public function getWebsiteUrl()
  {
    return $this->websiteUrl;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Profile::class, 'Google_Service_Analytics_Profile');
