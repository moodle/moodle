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

namespace Google\Service\GoogleAnalyticsAdmin;

class GoogleAnalyticsAdminV1betaProperty extends \Google\Model
{
  /**
   * Industry category unspecified
   */
  public const INDUSTRY_CATEGORY_INDUSTRY_CATEGORY_UNSPECIFIED = 'INDUSTRY_CATEGORY_UNSPECIFIED';
  /**
   * Automotive
   */
  public const INDUSTRY_CATEGORY_AUTOMOTIVE = 'AUTOMOTIVE';
  /**
   * Business and industrial markets
   */
  public const INDUSTRY_CATEGORY_BUSINESS_AND_INDUSTRIAL_MARKETS = 'BUSINESS_AND_INDUSTRIAL_MARKETS';
  /**
   * Finance
   */
  public const INDUSTRY_CATEGORY_FINANCE = 'FINANCE';
  /**
   * Healthcare
   */
  public const INDUSTRY_CATEGORY_HEALTHCARE = 'HEALTHCARE';
  /**
   * Technology
   */
  public const INDUSTRY_CATEGORY_TECHNOLOGY = 'TECHNOLOGY';
  /**
   * Travel
   */
  public const INDUSTRY_CATEGORY_TRAVEL = 'TRAVEL';
  /**
   * Other
   */
  public const INDUSTRY_CATEGORY_OTHER = 'OTHER';
  /**
   * Arts and entertainment
   */
  public const INDUSTRY_CATEGORY_ARTS_AND_ENTERTAINMENT = 'ARTS_AND_ENTERTAINMENT';
  /**
   * Beauty and fitness
   */
  public const INDUSTRY_CATEGORY_BEAUTY_AND_FITNESS = 'BEAUTY_AND_FITNESS';
  /**
   * Books and literature
   */
  public const INDUSTRY_CATEGORY_BOOKS_AND_LITERATURE = 'BOOKS_AND_LITERATURE';
  /**
   * Food and drink
   */
  public const INDUSTRY_CATEGORY_FOOD_AND_DRINK = 'FOOD_AND_DRINK';
  /**
   * Games
   */
  public const INDUSTRY_CATEGORY_GAMES = 'GAMES';
  /**
   * Hobbies and leisure
   */
  public const INDUSTRY_CATEGORY_HOBBIES_AND_LEISURE = 'HOBBIES_AND_LEISURE';
  /**
   * Home and garden
   */
  public const INDUSTRY_CATEGORY_HOME_AND_GARDEN = 'HOME_AND_GARDEN';
  /**
   * Internet and telecom
   */
  public const INDUSTRY_CATEGORY_INTERNET_AND_TELECOM = 'INTERNET_AND_TELECOM';
  /**
   * Law and government
   */
  public const INDUSTRY_CATEGORY_LAW_AND_GOVERNMENT = 'LAW_AND_GOVERNMENT';
  /**
   * News
   */
  public const INDUSTRY_CATEGORY_NEWS = 'NEWS';
  /**
   * Online communities
   */
  public const INDUSTRY_CATEGORY_ONLINE_COMMUNITIES = 'ONLINE_COMMUNITIES';
  /**
   * People and society
   */
  public const INDUSTRY_CATEGORY_PEOPLE_AND_SOCIETY = 'PEOPLE_AND_SOCIETY';
  /**
   * Pets and animals
   */
  public const INDUSTRY_CATEGORY_PETS_AND_ANIMALS = 'PETS_AND_ANIMALS';
  /**
   * Real estate
   */
  public const INDUSTRY_CATEGORY_REAL_ESTATE = 'REAL_ESTATE';
  /**
   * Reference
   */
  public const INDUSTRY_CATEGORY_REFERENCE = 'REFERENCE';
  /**
   * Science
   */
  public const INDUSTRY_CATEGORY_SCIENCE = 'SCIENCE';
  /**
   * Sports
   */
  public const INDUSTRY_CATEGORY_SPORTS = 'SPORTS';
  /**
   * Jobs and education
   */
  public const INDUSTRY_CATEGORY_JOBS_AND_EDUCATION = 'JOBS_AND_EDUCATION';
  /**
   * Shopping
   */
  public const INDUSTRY_CATEGORY_SHOPPING = 'SHOPPING';
  /**
   * Unknown or unspecified property type
   */
  public const PROPERTY_TYPE_PROPERTY_TYPE_UNSPECIFIED = 'PROPERTY_TYPE_UNSPECIFIED';
  /**
   * Ordinary Google Analytics property
   */
  public const PROPERTY_TYPE_PROPERTY_TYPE_ORDINARY = 'PROPERTY_TYPE_ORDINARY';
  /**
   * Google Analytics subproperty
   */
  public const PROPERTY_TYPE_PROPERTY_TYPE_SUBPROPERTY = 'PROPERTY_TYPE_SUBPROPERTY';
  /**
   * Google Analytics rollup property
   */
  public const PROPERTY_TYPE_PROPERTY_TYPE_ROLLUP = 'PROPERTY_TYPE_ROLLUP';
  /**
   * Service level not specified or invalid.
   */
  public const SERVICE_LEVEL_SERVICE_LEVEL_UNSPECIFIED = 'SERVICE_LEVEL_UNSPECIFIED';
  /**
   * The standard version of Google Analytics.
   */
  public const SERVICE_LEVEL_GOOGLE_ANALYTICS_STANDARD = 'GOOGLE_ANALYTICS_STANDARD';
  /**
   * The paid, premium version of Google Analytics.
   */
  public const SERVICE_LEVEL_GOOGLE_ANALYTICS_360 = 'GOOGLE_ANALYTICS_360';
  /**
   * Immutable. The resource name of the parent account Format:
   * accounts/{account_id} Example: "accounts/123"
   *
   * @var string
   */
  public $account;
  /**
   * Output only. Time when the entity was originally created.
   *
   * @var string
   */
  public $createTime;
  /**
   * The currency type used in reports involving monetary values. Format:
   * https://en.wikipedia.org/wiki/ISO_4217 Examples: "USD", "EUR", "JPY"
   *
   * @var string
   */
  public $currencyCode;
  /**
   * Output only. If set, the time at which this property was trashed. If not
   * set, then this property is not currently in the trash can.
   *
   * @var string
   */
  public $deleteTime;
  /**
   * Required. Human-readable display name for this property. The max allowed
   * display name length is 100 UTF-16 code units.
   *
   * @var string
   */
  public $displayName;
  /**
   * Output only. If set, the time at which this trashed property will be
   * permanently deleted. If not set, then this property is not currently in the
   * trash can and is not slated to be deleted.
   *
   * @var string
   */
  public $expireTime;
  /**
   * Industry associated with this property Example: AUTOMOTIVE, FOOD_AND_DRINK
   *
   * @var string
   */
  public $industryCategory;
  /**
   * Output only. Resource name of this property. Format:
   * properties/{property_id} Example: "properties/1000"
   *
   * @var string
   */
  public $name;
  /**
   * Immutable. Resource name of this property's logical parent. Note: The
   * Property-Moving UI can be used to change the parent. Format:
   * accounts/{account}, properties/{property} Example: "accounts/100",
   * "properties/101"
   *
   * @var string
   */
  public $parent;
  /**
   * Immutable. The property type for this Property resource. When creating a
   * property, if the type is "PROPERTY_TYPE_UNSPECIFIED", then
   * "ORDINARY_PROPERTY" will be implied.
   *
   * @var string
   */
  public $propertyType;
  /**
   * Output only. The Google Analytics service level that applies to this
   * property.
   *
   * @var string
   */
  public $serviceLevel;
  /**
   * Required. Reporting Time Zone, used as the day boundary for reports,
   * regardless of where the data originates. If the time zone honors DST,
   * Analytics will automatically adjust for the changes. NOTE: Changing the
   * time zone only affects data going forward, and is not applied
   * retroactively. Format: https://www.iana.org/time-zones Example:
   * "America/Los_Angeles"
   *
   * @var string
   */
  public $timeZone;
  /**
   * Output only. Time when entity payload fields were last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Immutable. The resource name of the parent account Format:
   * accounts/{account_id} Example: "accounts/123"
   *
   * @param string $account
   */
  public function setAccount($account)
  {
    $this->account = $account;
  }
  /**
   * @return string
   */
  public function getAccount()
  {
    return $this->account;
  }
  /**
   * Output only. Time when the entity was originally created.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * The currency type used in reports involving monetary values. Format:
   * https://en.wikipedia.org/wiki/ISO_4217 Examples: "USD", "EUR", "JPY"
   *
   * @param string $currencyCode
   */
  public function setCurrencyCode($currencyCode)
  {
    $this->currencyCode = $currencyCode;
  }
  /**
   * @return string
   */
  public function getCurrencyCode()
  {
    return $this->currencyCode;
  }
  /**
   * Output only. If set, the time at which this property was trashed. If not
   * set, then this property is not currently in the trash can.
   *
   * @param string $deleteTime
   */
  public function setDeleteTime($deleteTime)
  {
    $this->deleteTime = $deleteTime;
  }
  /**
   * @return string
   */
  public function getDeleteTime()
  {
    return $this->deleteTime;
  }
  /**
   * Required. Human-readable display name for this property. The max allowed
   * display name length is 100 UTF-16 code units.
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
   * Output only. If set, the time at which this trashed property will be
   * permanently deleted. If not set, then this property is not currently in the
   * trash can and is not slated to be deleted.
   *
   * @param string $expireTime
   */
  public function setExpireTime($expireTime)
  {
    $this->expireTime = $expireTime;
  }
  /**
   * @return string
   */
  public function getExpireTime()
  {
    return $this->expireTime;
  }
  /**
   * Industry associated with this property Example: AUTOMOTIVE, FOOD_AND_DRINK
   *
   * Accepted values: INDUSTRY_CATEGORY_UNSPECIFIED, AUTOMOTIVE,
   * BUSINESS_AND_INDUSTRIAL_MARKETS, FINANCE, HEALTHCARE, TECHNOLOGY, TRAVEL,
   * OTHER, ARTS_AND_ENTERTAINMENT, BEAUTY_AND_FITNESS, BOOKS_AND_LITERATURE,
   * FOOD_AND_DRINK, GAMES, HOBBIES_AND_LEISURE, HOME_AND_GARDEN,
   * INTERNET_AND_TELECOM, LAW_AND_GOVERNMENT, NEWS, ONLINE_COMMUNITIES,
   * PEOPLE_AND_SOCIETY, PETS_AND_ANIMALS, REAL_ESTATE, REFERENCE, SCIENCE,
   * SPORTS, JOBS_AND_EDUCATION, SHOPPING
   *
   * @param self::INDUSTRY_CATEGORY_* $industryCategory
   */
  public function setIndustryCategory($industryCategory)
  {
    $this->industryCategory = $industryCategory;
  }
  /**
   * @return self::INDUSTRY_CATEGORY_*
   */
  public function getIndustryCategory()
  {
    return $this->industryCategory;
  }
  /**
   * Output only. Resource name of this property. Format:
   * properties/{property_id} Example: "properties/1000"
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
   * Immutable. Resource name of this property's logical parent. Note: The
   * Property-Moving UI can be used to change the parent. Format:
   * accounts/{account}, properties/{property} Example: "accounts/100",
   * "properties/101"
   *
   * @param string $parent
   */
  public function setParent($parent)
  {
    $this->parent = $parent;
  }
  /**
   * @return string
   */
  public function getParent()
  {
    return $this->parent;
  }
  /**
   * Immutable. The property type for this Property resource. When creating a
   * property, if the type is "PROPERTY_TYPE_UNSPECIFIED", then
   * "ORDINARY_PROPERTY" will be implied.
   *
   * Accepted values: PROPERTY_TYPE_UNSPECIFIED, PROPERTY_TYPE_ORDINARY,
   * PROPERTY_TYPE_SUBPROPERTY, PROPERTY_TYPE_ROLLUP
   *
   * @param self::PROPERTY_TYPE_* $propertyType
   */
  public function setPropertyType($propertyType)
  {
    $this->propertyType = $propertyType;
  }
  /**
   * @return self::PROPERTY_TYPE_*
   */
  public function getPropertyType()
  {
    return $this->propertyType;
  }
  /**
   * Output only. The Google Analytics service level that applies to this
   * property.
   *
   * Accepted values: SERVICE_LEVEL_UNSPECIFIED, GOOGLE_ANALYTICS_STANDARD,
   * GOOGLE_ANALYTICS_360
   *
   * @param self::SERVICE_LEVEL_* $serviceLevel
   */
  public function setServiceLevel($serviceLevel)
  {
    $this->serviceLevel = $serviceLevel;
  }
  /**
   * @return self::SERVICE_LEVEL_*
   */
  public function getServiceLevel()
  {
    return $this->serviceLevel;
  }
  /**
   * Required. Reporting Time Zone, used as the day boundary for reports,
   * regardless of where the data originates. If the time zone honors DST,
   * Analytics will automatically adjust for the changes. NOTE: Changing the
   * time zone only affects data going forward, and is not applied
   * retroactively. Format: https://www.iana.org/time-zones Example:
   * "America/Los_Angeles"
   *
   * @param string $timeZone
   */
  public function setTimeZone($timeZone)
  {
    $this->timeZone = $timeZone;
  }
  /**
   * @return string
   */
  public function getTimeZone()
  {
    return $this->timeZone;
  }
  /**
   * Output only. Time when entity payload fields were last updated.
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
class_alias(GoogleAnalyticsAdminV1betaProperty::class, 'Google_Service_GoogleAnalyticsAdmin_GoogleAnalyticsAdminV1betaProperty');
