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

namespace Google\Service\MyBusinessBusinessInformation;

class Location extends \Google\Collection
{
  protected $collection_key = 'serviceItems';
  protected $adWordsLocationExtensionsType = AdWordsLocationExtensions::class;
  protected $adWordsLocationExtensionsDataType = '';
  protected $categoriesType = Categories::class;
  protected $categoriesDataType = '';
  /**
   * Optional. A collection of free-form strings to allow you to tag your
   * business. These labels are NOT user facing; only you can see them. Must be
   * between 1-255 characters per label.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Immutable. The language of the location. Set during creation and not
   * updateable.
   *
   * @var string
   */
  public $languageCode;
  protected $latlngType = LatLng::class;
  protected $latlngDataType = '';
  protected $metadataType = Metadata::class;
  protected $metadataDataType = '';
  protected $moreHoursType = MoreHours::class;
  protected $moreHoursDataType = 'array';
  /**
   * Google identifier for this location in the form: `locations/{location_id}`.
   *
   * @var string
   */
  public $name;
  protected $openInfoType = OpenInfo::class;
  protected $openInfoDataType = '';
  protected $phoneNumbersType = PhoneNumbers::class;
  protected $phoneNumbersDataType = '';
  protected $profileType = Profile::class;
  protected $profileDataType = '';
  protected $regularHoursType = BusinessHours::class;
  protected $regularHoursDataType = '';
  protected $relationshipDataType = RelationshipData::class;
  protected $relationshipDataDataType = '';
  protected $serviceAreaType = ServiceAreaBusiness::class;
  protected $serviceAreaDataType = '';
  protected $serviceItemsType = ServiceItem::class;
  protected $serviceItemsDataType = 'array';
  protected $specialHoursType = SpecialHours::class;
  protected $specialHoursDataType = '';
  /**
   * Optional. External identifier for this location, which must be unique
   * within a given account. This is a means of associating the location with
   * your own records.
   *
   * @var string
   */
  public $storeCode;
  protected $storefrontAddressType = PostalAddress::class;
  protected $storefrontAddressDataType = '';
  /**
   * Required. Location name should reflect your business's real-world name, as
   * used consistently on your storefront, website, and stationery, and as known
   * to customers. Any additional information, when relevant, can be included in
   * other fields of the resource (for example, `Address`, `Categories`). Don't
   * add unnecessary information to your name (for example, prefer "Google" over
   * "Google Inc. - Mountain View Corporate Headquarters"). Don't include
   * marketing taglines, store codes, special characters, hours or closed/open
   * status, phone numbers, website URLs, service/product information,
   * location/address or directions, or containment information (for example,
   * "Chase ATM in Duane Reade").
   *
   * @var string
   */
  public $title;
  /**
   * Optional. A URL for this business. If possible, use a URL that represents
   * this individual business location instead of a generic website/URL that
   * represents all locations, or the brand.
   *
   * @var string
   */
  public $websiteUri;

  /**
   * Optional. Additional information that is surfaced in AdWords.
   *
   * @param AdWordsLocationExtensions $adWordsLocationExtensions
   */
  public function setAdWordsLocationExtensions(AdWordsLocationExtensions $adWordsLocationExtensions)
  {
    $this->adWordsLocationExtensions = $adWordsLocationExtensions;
  }
  /**
   * @return AdWordsLocationExtensions
   */
  public function getAdWordsLocationExtensions()
  {
    return $this->adWordsLocationExtensions;
  }
  /**
   * Optional. The different categories that describe the business.
   *
   * @param Categories $categories
   */
  public function setCategories(Categories $categories)
  {
    $this->categories = $categories;
  }
  /**
   * @return Categories
   */
  public function getCategories()
  {
    return $this->categories;
  }
  /**
   * Optional. A collection of free-form strings to allow you to tag your
   * business. These labels are NOT user facing; only you can see them. Must be
   * between 1-255 characters per label.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Immutable. The language of the location. Set during creation and not
   * updateable.
   *
   * @param string $languageCode
   */
  public function setLanguageCode($languageCode)
  {
    $this->languageCode = $languageCode;
  }
  /**
   * @return string
   */
  public function getLanguageCode()
  {
    return $this->languageCode;
  }
  /**
   * Optional. User-provided latitude and longitude. When creating a location,
   * this field is ignored if the provided address geocodes successfully. This
   * field is only returned on get requests if the user-provided `latlng` value
   * was accepted during create, or the `latlng` value was updated through the
   * Google Business Profile website. This field can only be updated by approved
   * clients.
   *
   * @param LatLng $latlng
   */
  public function setLatlng(LatLng $latlng)
  {
    $this->latlng = $latlng;
  }
  /**
   * @return LatLng
   */
  public function getLatlng()
  {
    return $this->latlng;
  }
  /**
   * Output only. Additional non-user-editable information.
   *
   * @param Metadata $metadata
   */
  public function setMetadata(Metadata $metadata)
  {
    $this->metadata = $metadata;
  }
  /**
   * @return Metadata
   */
  public function getMetadata()
  {
    return $this->metadata;
  }
  /**
   * Optional. More hours for a business's different departments or specific
   * customers.
   *
   * @param MoreHours[] $moreHours
   */
  public function setMoreHours($moreHours)
  {
    $this->moreHours = $moreHours;
  }
  /**
   * @return MoreHours[]
   */
  public function getMoreHours()
  {
    return $this->moreHours;
  }
  /**
   * Google identifier for this location in the form: `locations/{location_id}`.
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
   * Optional. A flag that indicates whether the location is currently open for
   * business.
   *
   * @param OpenInfo $openInfo
   */
  public function setOpenInfo(OpenInfo $openInfo)
  {
    $this->openInfo = $openInfo;
  }
  /**
   * @return OpenInfo
   */
  public function getOpenInfo()
  {
    return $this->openInfo;
  }
  /**
   * Optional. The different phone numbers that customers can use to get in
   * touch with the business.
   *
   * @param PhoneNumbers $phoneNumbers
   */
  public function setPhoneNumbers(PhoneNumbers $phoneNumbers)
  {
    $this->phoneNumbers = $phoneNumbers;
  }
  /**
   * @return PhoneNumbers
   */
  public function getPhoneNumbers()
  {
    return $this->phoneNumbers;
  }
  /**
   * Optional. Describes your business in your own voice and shares with users
   * the unique story of your business and offerings. This field is required for
   * all categories except lodging categories (e.g. hotels, motels, inns).
   *
   * @param Profile $profile
   */
  public function setProfile(Profile $profile)
  {
    $this->profile = $profile;
  }
  /**
   * @return Profile
   */
  public function getProfile()
  {
    return $this->profile;
  }
  /**
   * Optional. Operating hours for the business.
   *
   * @param BusinessHours $regularHours
   */
  public function setRegularHours(BusinessHours $regularHours)
  {
    $this->regularHours = $regularHours;
  }
  /**
   * @return BusinessHours
   */
  public function getRegularHours()
  {
    return $this->regularHours;
  }
  /**
   * Optional. All locations and chain related to this one.
   *
   * @param RelationshipData $relationshipData
   */
  public function setRelationshipData(RelationshipData $relationshipData)
  {
    $this->relationshipData = $relationshipData;
  }
  /**
   * @return RelationshipData
   */
  public function getRelationshipData()
  {
    return $this->relationshipData;
  }
  /**
   * Optional. Service area businesses provide their service at the customer's
   * location. If this business is a service area business, this field describes
   * the area(s) serviced by the business.
   *
   * @param ServiceAreaBusiness $serviceArea
   */
  public function setServiceArea(ServiceAreaBusiness $serviceArea)
  {
    $this->serviceArea = $serviceArea;
  }
  /**
   * @return ServiceAreaBusiness
   */
  public function getServiceArea()
  {
    return $this->serviceArea;
  }
  /**
   * Optional. List of services supported by merchants. A service can be
   * haircut, install water heater, etc. Duplicated service items will be
   * removed automatically.
   *
   * @param ServiceItem[] $serviceItems
   */
  public function setServiceItems($serviceItems)
  {
    $this->serviceItems = $serviceItems;
  }
  /**
   * @return ServiceItem[]
   */
  public function getServiceItems()
  {
    return $this->serviceItems;
  }
  /**
   * Optional. Special hours for the business. This typically includes holiday
   * hours, and other times outside of regular operating hours. These override
   * regular business hours. This field cannot be set without regular hours.
   *
   * @param SpecialHours $specialHours
   */
  public function setSpecialHours(SpecialHours $specialHours)
  {
    $this->specialHours = $specialHours;
  }
  /**
   * @return SpecialHours
   */
  public function getSpecialHours()
  {
    return $this->specialHours;
  }
  /**
   * Optional. External identifier for this location, which must be unique
   * within a given account. This is a means of associating the location with
   * your own records.
   *
   * @param string $storeCode
   */
  public function setStoreCode($storeCode)
  {
    $this->storeCode = $storeCode;
  }
  /**
   * @return string
   */
  public function getStoreCode()
  {
    return $this->storeCode;
  }
  /**
   * Optional. A precise, accurate address to describe your business location.
   * PO boxes or mailboxes located at remote locations are not acceptable. At
   * this time, you can specify a maximum of five `address_lines` values in the
   * address. This field should only be set for businesses that have a
   * storefront. This field should not be set for locations of type
   * `CUSTOMER_LOCATION_ONLY` but if set, any value provided will be discarded.
   *
   * @param PostalAddress $storefrontAddress
   */
  public function setStorefrontAddress(PostalAddress $storefrontAddress)
  {
    $this->storefrontAddress = $storefrontAddress;
  }
  /**
   * @return PostalAddress
   */
  public function getStorefrontAddress()
  {
    return $this->storefrontAddress;
  }
  /**
   * Required. Location name should reflect your business's real-world name, as
   * used consistently on your storefront, website, and stationery, and as known
   * to customers. Any additional information, when relevant, can be included in
   * other fields of the resource (for example, `Address`, `Categories`). Don't
   * add unnecessary information to your name (for example, prefer "Google" over
   * "Google Inc. - Mountain View Corporate Headquarters"). Don't include
   * marketing taglines, store codes, special characters, hours or closed/open
   * status, phone numbers, website URLs, service/product information,
   * location/address or directions, or containment information (for example,
   * "Chase ATM in Duane Reade").
   *
   * @param string $title
   */
  public function setTitle($title)
  {
    $this->title = $title;
  }
  /**
   * @return string
   */
  public function getTitle()
  {
    return $this->title;
  }
  /**
   * Optional. A URL for this business. If possible, use a URL that represents
   * this individual business location instead of a generic website/URL that
   * represents all locations, or the brand.
   *
   * @param string $websiteUri
   */
  public function setWebsiteUri($websiteUri)
  {
    $this->websiteUri = $websiteUri;
  }
  /**
   * @return string
   */
  public function getWebsiteUri()
  {
    return $this->websiteUri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Location::class, 'Google_Service_MyBusinessBusinessInformation_Location');
