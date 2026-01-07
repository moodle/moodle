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

namespace Google\Service\MapsPlaces;

class GoogleMapsPlacesV1Place extends \Google\Collection
{
  /**
   * Default value. This value is unused.
   */
  public const BUSINESS_STATUS_BUSINESS_STATUS_UNSPECIFIED = 'BUSINESS_STATUS_UNSPECIFIED';
  /**
   * The establishment is operational, not necessarily open now.
   */
  public const BUSINESS_STATUS_OPERATIONAL = 'OPERATIONAL';
  /**
   * The establishment is temporarily closed.
   */
  public const BUSINESS_STATUS_CLOSED_TEMPORARILY = 'CLOSED_TEMPORARILY';
  /**
   * The establishment is permanently closed.
   */
  public const BUSINESS_STATUS_CLOSED_PERMANENTLY = 'CLOSED_PERMANENTLY';
  /**
   * Place price level is unspecified or unknown.
   */
  public const PRICE_LEVEL_PRICE_LEVEL_UNSPECIFIED = 'PRICE_LEVEL_UNSPECIFIED';
  /**
   * Place provides free services.
   */
  public const PRICE_LEVEL_PRICE_LEVEL_FREE = 'PRICE_LEVEL_FREE';
  /**
   * Place provides inexpensive services.
   */
  public const PRICE_LEVEL_PRICE_LEVEL_INEXPENSIVE = 'PRICE_LEVEL_INEXPENSIVE';
  /**
   * Place provides moderately priced services.
   */
  public const PRICE_LEVEL_PRICE_LEVEL_MODERATE = 'PRICE_LEVEL_MODERATE';
  /**
   * Place provides expensive services.
   */
  public const PRICE_LEVEL_PRICE_LEVEL_EXPENSIVE = 'PRICE_LEVEL_EXPENSIVE';
  /**
   * Place provides very expensive services.
   */
  public const PRICE_LEVEL_PRICE_LEVEL_VERY_EXPENSIVE = 'PRICE_LEVEL_VERY_EXPENSIVE';
  protected $collection_key = 'types';
  protected $accessibilityOptionsType = GoogleMapsPlacesV1PlaceAccessibilityOptions::class;
  protected $accessibilityOptionsDataType = '';
  protected $addressComponentsType = GoogleMapsPlacesV1PlaceAddressComponent::class;
  protected $addressComponentsDataType = 'array';
  protected $addressDescriptorType = GoogleMapsPlacesV1AddressDescriptor::class;
  protected $addressDescriptorDataType = '';
  /**
   * The place's address in adr microformat: http://microformats.org/wiki/adr.
   *
   * @var string
   */
  public $adrFormatAddress;
  /**
   * Place allows dogs.
   *
   * @var bool
   */
  public $allowsDogs;
  protected $attributionsType = GoogleMapsPlacesV1PlaceAttribution::class;
  protected $attributionsDataType = 'array';
  /**
   * The business status for the place.
   *
   * @var string
   */
  public $businessStatus;
  protected $consumerAlertType = GoogleMapsPlacesV1PlaceConsumerAlert::class;
  protected $consumerAlertDataType = '';
  protected $containingPlacesType = GoogleMapsPlacesV1PlaceContainingPlace::class;
  protected $containingPlacesDataType = 'array';
  /**
   * Specifies if the business supports curbside pickup.
   *
   * @var bool
   */
  public $curbsidePickup;
  protected $currentOpeningHoursType = GoogleMapsPlacesV1PlaceOpeningHours::class;
  protected $currentOpeningHoursDataType = '';
  protected $currentSecondaryOpeningHoursType = GoogleMapsPlacesV1PlaceOpeningHours::class;
  protected $currentSecondaryOpeningHoursDataType = 'array';
  /**
   * Specifies if the business supports delivery.
   *
   * @var bool
   */
  public $delivery;
  /**
   * Specifies if the business supports indoor or outdoor seating options.
   *
   * @var bool
   */
  public $dineIn;
  protected $displayNameType = GoogleTypeLocalizedText::class;
  protected $displayNameDataType = '';
  protected $editorialSummaryType = GoogleTypeLocalizedText::class;
  protected $editorialSummaryDataType = '';
  protected $evChargeAmenitySummaryType = GoogleMapsPlacesV1PlaceEvChargeAmenitySummary::class;
  protected $evChargeAmenitySummaryDataType = '';
  protected $evChargeOptionsType = GoogleMapsPlacesV1EVChargeOptions::class;
  protected $evChargeOptionsDataType = '';
  /**
   * A full, human-readable address for this place.
   *
   * @var string
   */
  public $formattedAddress;
  protected $fuelOptionsType = GoogleMapsPlacesV1FuelOptions::class;
  protected $fuelOptionsDataType = '';
  protected $generativeSummaryType = GoogleMapsPlacesV1PlaceGenerativeSummary::class;
  protected $generativeSummaryDataType = '';
  /**
   * Place is good for children.
   *
   * @var bool
   */
  public $goodForChildren;
  /**
   * Place accommodates groups.
   *
   * @var bool
   */
  public $goodForGroups;
  /**
   * Place is suitable for watching sports.
   *
   * @var bool
   */
  public $goodForWatchingSports;
  protected $googleMapsLinksType = GoogleMapsPlacesV1PlaceGoogleMapsLinks::class;
  protected $googleMapsLinksDataType = '';
  /**
   * A URL providing more information about this place.
   *
   * @var string
   */
  public $googleMapsUri;
  /**
   * Background color for icon_mask in hex format, e.g. #909CE1.
   *
   * @var string
   */
  public $iconBackgroundColor;
  /**
   * A truncated URL to an icon mask. User can access different icon type by
   * appending type suffix to the end (eg, ".svg" or ".png").
   *
   * @var string
   */
  public $iconMaskBaseUri;
  /**
   * The unique identifier of a place.
   *
   * @var string
   */
  public $id;
  /**
   * A human-readable phone number for the place, in international format.
   *
   * @var string
   */
  public $internationalPhoneNumber;
  /**
   * Place provides live music.
   *
   * @var bool
   */
  public $liveMusic;
  protected $locationType = GoogleTypeLatLng::class;
  protected $locationDataType = '';
  /**
   * Place has a children's menu.
   *
   * @var bool
   */
  public $menuForChildren;
  /**
   * If this Place is permanently closed and has moved to a new Place, this
   * field contains the new Place's resource name, in `places/{place_id}`
   * format. If this Place moved multiple times, this field will represent the
   * first moved place. This field will not be populated if this Place has not
   * moved.
   *
   * @var string
   */
  public $movedPlace;
  /**
   * If this Place is permanently closed and has moved to a new Place, this
   * field contains the new Place's place ID. If this Place moved multiple
   * times, this field will represent the first moved Place. This field will not
   * be populated if this Place has not moved.
   *
   * @var string
   */
  public $movedPlaceId;
  /**
   * This Place's resource name, in `places/{place_id}` format. Can be used to
   * look up the Place.
   *
   * @var string
   */
  public $name;
  /**
   * A human-readable phone number for the place, in national format.
   *
   * @var string
   */
  public $nationalPhoneNumber;
  protected $neighborhoodSummaryType = GoogleMapsPlacesV1PlaceNeighborhoodSummary::class;
  protected $neighborhoodSummaryDataType = '';
  /**
   * Place provides outdoor seating.
   *
   * @var bool
   */
  public $outdoorSeating;
  protected $parkingOptionsType = GoogleMapsPlacesV1PlaceParkingOptions::class;
  protected $parkingOptionsDataType = '';
  protected $paymentOptionsType = GoogleMapsPlacesV1PlacePaymentOptions::class;
  protected $paymentOptionsDataType = '';
  protected $photosType = GoogleMapsPlacesV1Photo::class;
  protected $photosDataType = 'array';
  protected $plusCodeType = GoogleMapsPlacesV1PlacePlusCode::class;
  protected $plusCodeDataType = '';
  protected $postalAddressType = GoogleTypePostalAddress::class;
  protected $postalAddressDataType = '';
  /**
   * Price level of the place.
   *
   * @var string
   */
  public $priceLevel;
  protected $priceRangeType = GoogleMapsPlacesV1PriceRange::class;
  protected $priceRangeDataType = '';
  /**
   * The primary type of the given result. This type must be one of the Places
   * API supported types. For example, "restaurant", "cafe", "airport", etc. A
   * place can only have a single primary type. For the complete list of
   * possible values, see Table A and Table B at
   * https://developers.google.com/maps/documentation/places/web-service/place-
   * types. The primary type may be missing if the place's primary type is not a
   * supported type. When a primary type is present, it is always one of the
   * types in the `types` field.
   *
   * @var string
   */
  public $primaryType;
  protected $primaryTypeDisplayNameType = GoogleTypeLocalizedText::class;
  protected $primaryTypeDisplayNameDataType = '';
  /**
   * Indicates whether the place is a pure service area business. Pure service
   * area business is a business that visits or delivers to customers directly
   * but does not serve customers at their business address. For example,
   * businesses like cleaning services or plumbers. Those businesses may not
   * have a physical address or location on Google Maps.
   *
   * @var bool
   */
  public $pureServiceAreaBusiness;
  /**
   * A rating between 1.0 and 5.0, based on user reviews of this place.
   *
   * @var 
   */
  public $rating;
  protected $regularOpeningHoursType = GoogleMapsPlacesV1PlaceOpeningHours::class;
  protected $regularOpeningHoursDataType = '';
  protected $regularSecondaryOpeningHoursType = GoogleMapsPlacesV1PlaceOpeningHours::class;
  protected $regularSecondaryOpeningHoursDataType = 'array';
  /**
   * Specifies if the place supports reservations.
   *
   * @var bool
   */
  public $reservable;
  /**
   * Place has restroom.
   *
   * @var bool
   */
  public $restroom;
  protected $reviewSummaryType = GoogleMapsPlacesV1PlaceReviewSummary::class;
  protected $reviewSummaryDataType = '';
  protected $reviewsType = GoogleMapsPlacesV1Review::class;
  protected $reviewsDataType = 'array';
  /**
   * Specifies if the place serves beer.
   *
   * @var bool
   */
  public $servesBeer;
  /**
   * Specifies if the place serves breakfast.
   *
   * @var bool
   */
  public $servesBreakfast;
  /**
   * Specifies if the place serves brunch.
   *
   * @var bool
   */
  public $servesBrunch;
  /**
   * Place serves cocktails.
   *
   * @var bool
   */
  public $servesCocktails;
  /**
   * Place serves coffee.
   *
   * @var bool
   */
  public $servesCoffee;
  /**
   * Place serves dessert.
   *
   * @var bool
   */
  public $servesDessert;
  /**
   * Specifies if the place serves dinner.
   *
   * @var bool
   */
  public $servesDinner;
  /**
   * Specifies if the place serves lunch.
   *
   * @var bool
   */
  public $servesLunch;
  /**
   * Specifies if the place serves vegetarian food.
   *
   * @var bool
   */
  public $servesVegetarianFood;
  /**
   * Specifies if the place serves wine.
   *
   * @var bool
   */
  public $servesWine;
  /**
   * A short, human-readable address for this place.
   *
   * @var string
   */
  public $shortFormattedAddress;
  protected $subDestinationsType = GoogleMapsPlacesV1PlaceSubDestination::class;
  protected $subDestinationsDataType = 'array';
  /**
   * Specifies if the business supports takeout.
   *
   * @var bool
   */
  public $takeout;
  protected $timeZoneType = GoogleTypeTimeZone::class;
  protected $timeZoneDataType = '';
  /**
   * A set of type tags for this result. For example, "political" and
   * "locality". For the complete list of possible values, see Table A and Table
   * B at https://developers.google.com/maps/documentation/places/web-
   * service/place-types
   *
   * @var string[]
   */
  public $types;
  /**
   * The total number of reviews (with or without text) for this place.
   *
   * @var int
   */
  public $userRatingCount;
  /**
   * Number of minutes this place's timezone is currently offset from UTC. This
   * is expressed in minutes to support timezones that are offset by fractions
   * of an hour, e.g. X hours and 15 minutes.
   *
   * @var int
   */
  public $utcOffsetMinutes;
  protected $viewportType = GoogleGeoTypeViewport::class;
  protected $viewportDataType = '';
  /**
   * The authoritative website for this place, e.g. a business' homepage. Note
   * that for places that are part of a chain (e.g. an IKEA store), this will
   * usually be the website for the individual store, not the overall chain.
   *
   * @var string
   */
  public $websiteUri;

  /**
   * Information about the accessibility options a place offers.
   *
   * @param GoogleMapsPlacesV1PlaceAccessibilityOptions $accessibilityOptions
   */
  public function setAccessibilityOptions(GoogleMapsPlacesV1PlaceAccessibilityOptions $accessibilityOptions)
  {
    $this->accessibilityOptions = $accessibilityOptions;
  }
  /**
   * @return GoogleMapsPlacesV1PlaceAccessibilityOptions
   */
  public function getAccessibilityOptions()
  {
    return $this->accessibilityOptions;
  }
  /**
   * Repeated components for each locality level. Note the following facts about
   * the address_components[] array: - The array of address components may
   * contain more components than the formatted_address. - The array does not
   * necessarily include all the political entities that contain an address,
   * apart from those included in the formatted_address. To retrieve all the
   * political entities that contain a specific address, you should use reverse
   * geocoding, passing the latitude/longitude of the address as a parameter to
   * the request. - The format of the response is not guaranteed to remain the
   * same between requests. In particular, the number of address_components
   * varies based on the address requested and can change over time for the same
   * address. A component can change position in the array. The type of the
   * component can change. A particular component may be missing in a later
   * response.
   *
   * @param GoogleMapsPlacesV1PlaceAddressComponent[] $addressComponents
   */
  public function setAddressComponents($addressComponents)
  {
    $this->addressComponents = $addressComponents;
  }
  /**
   * @return GoogleMapsPlacesV1PlaceAddressComponent[]
   */
  public function getAddressComponents()
  {
    return $this->addressComponents;
  }
  /**
   * The address descriptor of the place. Address descriptors include additional
   * information that help describe a location using landmarks and areas. See
   * address descriptor regional coverage in
   * https://developers.google.com/maps/documentation/geocoding/address-
   * descriptors/coverage.
   *
   * @param GoogleMapsPlacesV1AddressDescriptor $addressDescriptor
   */
  public function setAddressDescriptor(GoogleMapsPlacesV1AddressDescriptor $addressDescriptor)
  {
    $this->addressDescriptor = $addressDescriptor;
  }
  /**
   * @return GoogleMapsPlacesV1AddressDescriptor
   */
  public function getAddressDescriptor()
  {
    return $this->addressDescriptor;
  }
  /**
   * The place's address in adr microformat: http://microformats.org/wiki/adr.
   *
   * @param string $adrFormatAddress
   */
  public function setAdrFormatAddress($adrFormatAddress)
  {
    $this->adrFormatAddress = $adrFormatAddress;
  }
  /**
   * @return string
   */
  public function getAdrFormatAddress()
  {
    return $this->adrFormatAddress;
  }
  /**
   * Place allows dogs.
   *
   * @param bool $allowsDogs
   */
  public function setAllowsDogs($allowsDogs)
  {
    $this->allowsDogs = $allowsDogs;
  }
  /**
   * @return bool
   */
  public function getAllowsDogs()
  {
    return $this->allowsDogs;
  }
  /**
   * A set of data provider that must be shown with this result.
   *
   * @param GoogleMapsPlacesV1PlaceAttribution[] $attributions
   */
  public function setAttributions($attributions)
  {
    $this->attributions = $attributions;
  }
  /**
   * @return GoogleMapsPlacesV1PlaceAttribution[]
   */
  public function getAttributions()
  {
    return $this->attributions;
  }
  /**
   * The business status for the place.
   *
   * Accepted values: BUSINESS_STATUS_UNSPECIFIED, OPERATIONAL,
   * CLOSED_TEMPORARILY, CLOSED_PERMANENTLY
   *
   * @param self::BUSINESS_STATUS_* $businessStatus
   */
  public function setBusinessStatus($businessStatus)
  {
    $this->businessStatus = $businessStatus;
  }
  /**
   * @return self::BUSINESS_STATUS_*
   */
  public function getBusinessStatus()
  {
    return $this->businessStatus;
  }
  /**
   * The consumer alert message for the place when we detect suspicious review
   * activity on a business or a business violates our policies.
   *
   * @param GoogleMapsPlacesV1PlaceConsumerAlert $consumerAlert
   */
  public function setConsumerAlert(GoogleMapsPlacesV1PlaceConsumerAlert $consumerAlert)
  {
    $this->consumerAlert = $consumerAlert;
  }
  /**
   * @return GoogleMapsPlacesV1PlaceConsumerAlert
   */
  public function getConsumerAlert()
  {
    return $this->consumerAlert;
  }
  /**
   * List of places in which the current place is located.
   *
   * @param GoogleMapsPlacesV1PlaceContainingPlace[] $containingPlaces
   */
  public function setContainingPlaces($containingPlaces)
  {
    $this->containingPlaces = $containingPlaces;
  }
  /**
   * @return GoogleMapsPlacesV1PlaceContainingPlace[]
   */
  public function getContainingPlaces()
  {
    return $this->containingPlaces;
  }
  /**
   * Specifies if the business supports curbside pickup.
   *
   * @param bool $curbsidePickup
   */
  public function setCurbsidePickup($curbsidePickup)
  {
    $this->curbsidePickup = $curbsidePickup;
  }
  /**
   * @return bool
   */
  public function getCurbsidePickup()
  {
    return $this->curbsidePickup;
  }
  /**
   * The hours of operation for the next seven days (including today). The time
   * period starts at midnight on the date of the request and ends at 11:59 pm
   * six days later. This field includes the special_days subfield of all hours,
   * set for dates that have exceptional hours.
   *
   * @param GoogleMapsPlacesV1PlaceOpeningHours $currentOpeningHours
   */
  public function setCurrentOpeningHours(GoogleMapsPlacesV1PlaceOpeningHours $currentOpeningHours)
  {
    $this->currentOpeningHours = $currentOpeningHours;
  }
  /**
   * @return GoogleMapsPlacesV1PlaceOpeningHours
   */
  public function getCurrentOpeningHours()
  {
    return $this->currentOpeningHours;
  }
  /**
   * Contains an array of entries for the next seven days including information
   * about secondary hours of a business. Secondary hours are different from a
   * business's main hours. For example, a restaurant can specify drive through
   * hours or delivery hours as its secondary hours. This field populates the
   * type subfield, which draws from a predefined list of opening hours types
   * (such as DRIVE_THROUGH, PICKUP, or TAKEOUT) based on the types of the
   * place. This field includes the special_days subfield of all hours, set for
   * dates that have exceptional hours.
   *
   * @param GoogleMapsPlacesV1PlaceOpeningHours[] $currentSecondaryOpeningHours
   */
  public function setCurrentSecondaryOpeningHours($currentSecondaryOpeningHours)
  {
    $this->currentSecondaryOpeningHours = $currentSecondaryOpeningHours;
  }
  /**
   * @return GoogleMapsPlacesV1PlaceOpeningHours[]
   */
  public function getCurrentSecondaryOpeningHours()
  {
    return $this->currentSecondaryOpeningHours;
  }
  /**
   * Specifies if the business supports delivery.
   *
   * @param bool $delivery
   */
  public function setDelivery($delivery)
  {
    $this->delivery = $delivery;
  }
  /**
   * @return bool
   */
  public function getDelivery()
  {
    return $this->delivery;
  }
  /**
   * Specifies if the business supports indoor or outdoor seating options.
   *
   * @param bool $dineIn
   */
  public function setDineIn($dineIn)
  {
    $this->dineIn = $dineIn;
  }
  /**
   * @return bool
   */
  public function getDineIn()
  {
    return $this->dineIn;
  }
  /**
   * The localized name of the place, suitable as a short human-readable
   * description. For example, "Google Sydney", "Starbucks", "Pyrmont", etc.
   *
   * @param GoogleTypeLocalizedText $displayName
   */
  public function setDisplayName(GoogleTypeLocalizedText $displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return GoogleTypeLocalizedText
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Contains a summary of the place. A summary is comprised of a textual
   * overview, and also includes the language code for these if applicable.
   * Summary text must be presented as-is and can not be modified or altered.
   *
   * @param GoogleTypeLocalizedText $editorialSummary
   */
  public function setEditorialSummary(GoogleTypeLocalizedText $editorialSummary)
  {
    $this->editorialSummary = $editorialSummary;
  }
  /**
   * @return GoogleTypeLocalizedText
   */
  public function getEditorialSummary()
  {
    return $this->editorialSummary;
  }
  /**
   * The summary of amenities near the EV charging station.
   *
   * @param GoogleMapsPlacesV1PlaceEvChargeAmenitySummary $evChargeAmenitySummary
   */
  public function setEvChargeAmenitySummary(GoogleMapsPlacesV1PlaceEvChargeAmenitySummary $evChargeAmenitySummary)
  {
    $this->evChargeAmenitySummary = $evChargeAmenitySummary;
  }
  /**
   * @return GoogleMapsPlacesV1PlaceEvChargeAmenitySummary
   */
  public function getEvChargeAmenitySummary()
  {
    return $this->evChargeAmenitySummary;
  }
  /**
   * Information of ev charging options.
   *
   * @param GoogleMapsPlacesV1EVChargeOptions $evChargeOptions
   */
  public function setEvChargeOptions(GoogleMapsPlacesV1EVChargeOptions $evChargeOptions)
  {
    $this->evChargeOptions = $evChargeOptions;
  }
  /**
   * @return GoogleMapsPlacesV1EVChargeOptions
   */
  public function getEvChargeOptions()
  {
    return $this->evChargeOptions;
  }
  /**
   * A full, human-readable address for this place.
   *
   * @param string $formattedAddress
   */
  public function setFormattedAddress($formattedAddress)
  {
    $this->formattedAddress = $formattedAddress;
  }
  /**
   * @return string
   */
  public function getFormattedAddress()
  {
    return $this->formattedAddress;
  }
  /**
   * The most recent information about fuel options in a gas station. This
   * information is updated regularly.
   *
   * @param GoogleMapsPlacesV1FuelOptions $fuelOptions
   */
  public function setFuelOptions(GoogleMapsPlacesV1FuelOptions $fuelOptions)
  {
    $this->fuelOptions = $fuelOptions;
  }
  /**
   * @return GoogleMapsPlacesV1FuelOptions
   */
  public function getFuelOptions()
  {
    return $this->fuelOptions;
  }
  /**
   * AI-generated summary of the place.
   *
   * @param GoogleMapsPlacesV1PlaceGenerativeSummary $generativeSummary
   */
  public function setGenerativeSummary(GoogleMapsPlacesV1PlaceGenerativeSummary $generativeSummary)
  {
    $this->generativeSummary = $generativeSummary;
  }
  /**
   * @return GoogleMapsPlacesV1PlaceGenerativeSummary
   */
  public function getGenerativeSummary()
  {
    return $this->generativeSummary;
  }
  /**
   * Place is good for children.
   *
   * @param bool $goodForChildren
   */
  public function setGoodForChildren($goodForChildren)
  {
    $this->goodForChildren = $goodForChildren;
  }
  /**
   * @return bool
   */
  public function getGoodForChildren()
  {
    return $this->goodForChildren;
  }
  /**
   * Place accommodates groups.
   *
   * @param bool $goodForGroups
   */
  public function setGoodForGroups($goodForGroups)
  {
    $this->goodForGroups = $goodForGroups;
  }
  /**
   * @return bool
   */
  public function getGoodForGroups()
  {
    return $this->goodForGroups;
  }
  /**
   * Place is suitable for watching sports.
   *
   * @param bool $goodForWatchingSports
   */
  public function setGoodForWatchingSports($goodForWatchingSports)
  {
    $this->goodForWatchingSports = $goodForWatchingSports;
  }
  /**
   * @return bool
   */
  public function getGoodForWatchingSports()
  {
    return $this->goodForWatchingSports;
  }
  /**
   * Links to trigger different Google Maps actions.
   *
   * @param GoogleMapsPlacesV1PlaceGoogleMapsLinks $googleMapsLinks
   */
  public function setGoogleMapsLinks(GoogleMapsPlacesV1PlaceGoogleMapsLinks $googleMapsLinks)
  {
    $this->googleMapsLinks = $googleMapsLinks;
  }
  /**
   * @return GoogleMapsPlacesV1PlaceGoogleMapsLinks
   */
  public function getGoogleMapsLinks()
  {
    return $this->googleMapsLinks;
  }
  /**
   * A URL providing more information about this place.
   *
   * @param string $googleMapsUri
   */
  public function setGoogleMapsUri($googleMapsUri)
  {
    $this->googleMapsUri = $googleMapsUri;
  }
  /**
   * @return string
   */
  public function getGoogleMapsUri()
  {
    return $this->googleMapsUri;
  }
  /**
   * Background color for icon_mask in hex format, e.g. #909CE1.
   *
   * @param string $iconBackgroundColor
   */
  public function setIconBackgroundColor($iconBackgroundColor)
  {
    $this->iconBackgroundColor = $iconBackgroundColor;
  }
  /**
   * @return string
   */
  public function getIconBackgroundColor()
  {
    return $this->iconBackgroundColor;
  }
  /**
   * A truncated URL to an icon mask. User can access different icon type by
   * appending type suffix to the end (eg, ".svg" or ".png").
   *
   * @param string $iconMaskBaseUri
   */
  public function setIconMaskBaseUri($iconMaskBaseUri)
  {
    $this->iconMaskBaseUri = $iconMaskBaseUri;
  }
  /**
   * @return string
   */
  public function getIconMaskBaseUri()
  {
    return $this->iconMaskBaseUri;
  }
  /**
   * The unique identifier of a place.
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
   * A human-readable phone number for the place, in international format.
   *
   * @param string $internationalPhoneNumber
   */
  public function setInternationalPhoneNumber($internationalPhoneNumber)
  {
    $this->internationalPhoneNumber = $internationalPhoneNumber;
  }
  /**
   * @return string
   */
  public function getInternationalPhoneNumber()
  {
    return $this->internationalPhoneNumber;
  }
  /**
   * Place provides live music.
   *
   * @param bool $liveMusic
   */
  public function setLiveMusic($liveMusic)
  {
    $this->liveMusic = $liveMusic;
  }
  /**
   * @return bool
   */
  public function getLiveMusic()
  {
    return $this->liveMusic;
  }
  /**
   * The position of this place.
   *
   * @param GoogleTypeLatLng $location
   */
  public function setLocation(GoogleTypeLatLng $location)
  {
    $this->location = $location;
  }
  /**
   * @return GoogleTypeLatLng
   */
  public function getLocation()
  {
    return $this->location;
  }
  /**
   * Place has a children's menu.
   *
   * @param bool $menuForChildren
   */
  public function setMenuForChildren($menuForChildren)
  {
    $this->menuForChildren = $menuForChildren;
  }
  /**
   * @return bool
   */
  public function getMenuForChildren()
  {
    return $this->menuForChildren;
  }
  /**
   * If this Place is permanently closed and has moved to a new Place, this
   * field contains the new Place's resource name, in `places/{place_id}`
   * format. If this Place moved multiple times, this field will represent the
   * first moved place. This field will not be populated if this Place has not
   * moved.
   *
   * @param string $movedPlace
   */
  public function setMovedPlace($movedPlace)
  {
    $this->movedPlace = $movedPlace;
  }
  /**
   * @return string
   */
  public function getMovedPlace()
  {
    return $this->movedPlace;
  }
  /**
   * If this Place is permanently closed and has moved to a new Place, this
   * field contains the new Place's place ID. If this Place moved multiple
   * times, this field will represent the first moved Place. This field will not
   * be populated if this Place has not moved.
   *
   * @param string $movedPlaceId
   */
  public function setMovedPlaceId($movedPlaceId)
  {
    $this->movedPlaceId = $movedPlaceId;
  }
  /**
   * @return string
   */
  public function getMovedPlaceId()
  {
    return $this->movedPlaceId;
  }
  /**
   * This Place's resource name, in `places/{place_id}` format. Can be used to
   * look up the Place.
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
   * A human-readable phone number for the place, in national format.
   *
   * @param string $nationalPhoneNumber
   */
  public function setNationalPhoneNumber($nationalPhoneNumber)
  {
    $this->nationalPhoneNumber = $nationalPhoneNumber;
  }
  /**
   * @return string
   */
  public function getNationalPhoneNumber()
  {
    return $this->nationalPhoneNumber;
  }
  /**
   * A summary of points of interest near the place.
   *
   * @param GoogleMapsPlacesV1PlaceNeighborhoodSummary $neighborhoodSummary
   */
  public function setNeighborhoodSummary(GoogleMapsPlacesV1PlaceNeighborhoodSummary $neighborhoodSummary)
  {
    $this->neighborhoodSummary = $neighborhoodSummary;
  }
  /**
   * @return GoogleMapsPlacesV1PlaceNeighborhoodSummary
   */
  public function getNeighborhoodSummary()
  {
    return $this->neighborhoodSummary;
  }
  /**
   * Place provides outdoor seating.
   *
   * @param bool $outdoorSeating
   */
  public function setOutdoorSeating($outdoorSeating)
  {
    $this->outdoorSeating = $outdoorSeating;
  }
  /**
   * @return bool
   */
  public function getOutdoorSeating()
  {
    return $this->outdoorSeating;
  }
  /**
   * Options of parking provided by the place.
   *
   * @param GoogleMapsPlacesV1PlaceParkingOptions $parkingOptions
   */
  public function setParkingOptions(GoogleMapsPlacesV1PlaceParkingOptions $parkingOptions)
  {
    $this->parkingOptions = $parkingOptions;
  }
  /**
   * @return GoogleMapsPlacesV1PlaceParkingOptions
   */
  public function getParkingOptions()
  {
    return $this->parkingOptions;
  }
  /**
   * Payment options the place accepts. If a payment option data is not
   * available, the payment option field will be unset.
   *
   * @param GoogleMapsPlacesV1PlacePaymentOptions $paymentOptions
   */
  public function setPaymentOptions(GoogleMapsPlacesV1PlacePaymentOptions $paymentOptions)
  {
    $this->paymentOptions = $paymentOptions;
  }
  /**
   * @return GoogleMapsPlacesV1PlacePaymentOptions
   */
  public function getPaymentOptions()
  {
    return $this->paymentOptions;
  }
  /**
   * Information (including references) about photos of this place. A maximum of
   * 10 photos can be returned.
   *
   * @param GoogleMapsPlacesV1Photo[] $photos
   */
  public function setPhotos($photos)
  {
    $this->photos = $photos;
  }
  /**
   * @return GoogleMapsPlacesV1Photo[]
   */
  public function getPhotos()
  {
    return $this->photos;
  }
  /**
   * Plus code of the place location lat/long.
   *
   * @param GoogleMapsPlacesV1PlacePlusCode $plusCode
   */
  public function setPlusCode(GoogleMapsPlacesV1PlacePlusCode $plusCode)
  {
    $this->plusCode = $plusCode;
  }
  /**
   * @return GoogleMapsPlacesV1PlacePlusCode
   */
  public function getPlusCode()
  {
    return $this->plusCode;
  }
  /**
   * The address in postal address format.
   *
   * @param GoogleTypePostalAddress $postalAddress
   */
  public function setPostalAddress(GoogleTypePostalAddress $postalAddress)
  {
    $this->postalAddress = $postalAddress;
  }
  /**
   * @return GoogleTypePostalAddress
   */
  public function getPostalAddress()
  {
    return $this->postalAddress;
  }
  /**
   * Price level of the place.
   *
   * Accepted values: PRICE_LEVEL_UNSPECIFIED, PRICE_LEVEL_FREE,
   * PRICE_LEVEL_INEXPENSIVE, PRICE_LEVEL_MODERATE, PRICE_LEVEL_EXPENSIVE,
   * PRICE_LEVEL_VERY_EXPENSIVE
   *
   * @param self::PRICE_LEVEL_* $priceLevel
   */
  public function setPriceLevel($priceLevel)
  {
    $this->priceLevel = $priceLevel;
  }
  /**
   * @return self::PRICE_LEVEL_*
   */
  public function getPriceLevel()
  {
    return $this->priceLevel;
  }
  /**
   * The price range associated with a Place.
   *
   * @param GoogleMapsPlacesV1PriceRange $priceRange
   */
  public function setPriceRange(GoogleMapsPlacesV1PriceRange $priceRange)
  {
    $this->priceRange = $priceRange;
  }
  /**
   * @return GoogleMapsPlacesV1PriceRange
   */
  public function getPriceRange()
  {
    return $this->priceRange;
  }
  /**
   * The primary type of the given result. This type must be one of the Places
   * API supported types. For example, "restaurant", "cafe", "airport", etc. A
   * place can only have a single primary type. For the complete list of
   * possible values, see Table A and Table B at
   * https://developers.google.com/maps/documentation/places/web-service/place-
   * types. The primary type may be missing if the place's primary type is not a
   * supported type. When a primary type is present, it is always one of the
   * types in the `types` field.
   *
   * @param string $primaryType
   */
  public function setPrimaryType($primaryType)
  {
    $this->primaryType = $primaryType;
  }
  /**
   * @return string
   */
  public function getPrimaryType()
  {
    return $this->primaryType;
  }
  /**
   * The display name of the primary type, localized to the request language if
   * applicable. For the complete list of possible values, see Table A and Table
   * B at https://developers.google.com/maps/documentation/places/web-
   * service/place-types. The primary type may be missing if the place's primary
   * type is not a supported type.
   *
   * @param GoogleTypeLocalizedText $primaryTypeDisplayName
   */
  public function setPrimaryTypeDisplayName(GoogleTypeLocalizedText $primaryTypeDisplayName)
  {
    $this->primaryTypeDisplayName = $primaryTypeDisplayName;
  }
  /**
   * @return GoogleTypeLocalizedText
   */
  public function getPrimaryTypeDisplayName()
  {
    return $this->primaryTypeDisplayName;
  }
  /**
   * Indicates whether the place is a pure service area business. Pure service
   * area business is a business that visits or delivers to customers directly
   * but does not serve customers at their business address. For example,
   * businesses like cleaning services or plumbers. Those businesses may not
   * have a physical address or location on Google Maps.
   *
   * @param bool $pureServiceAreaBusiness
   */
  public function setPureServiceAreaBusiness($pureServiceAreaBusiness)
  {
    $this->pureServiceAreaBusiness = $pureServiceAreaBusiness;
  }
  /**
   * @return bool
   */
  public function getPureServiceAreaBusiness()
  {
    return $this->pureServiceAreaBusiness;
  }
  public function setRating($rating)
  {
    $this->rating = $rating;
  }
  public function getRating()
  {
    return $this->rating;
  }
  /**
   * The regular hours of operation. Note that if a place is always open (24
   * hours), the `close` field will not be set. Clients can rely on always open
   * (24 hours) being represented as an
   * [`open`](https://developers.google.com/maps/documentation/places/web-
   * service/reference/rest/v1/places#Period) period containing
   * [`day`](https://developers.google.com/maps/documentation/places/web-
   * service/reference/rest/v1/places#Point) with value `0`,
   * [`hour`](https://developers.google.com/maps/documentation/places/web-
   * service/reference/rest/v1/places#Point) with value `0`, and
   * [`minute`](https://developers.google.com/maps/documentation/places/web-
   * service/reference/rest/v1/places#Point) with value `0`.
   *
   * @param GoogleMapsPlacesV1PlaceOpeningHours $regularOpeningHours
   */
  public function setRegularOpeningHours(GoogleMapsPlacesV1PlaceOpeningHours $regularOpeningHours)
  {
    $this->regularOpeningHours = $regularOpeningHours;
  }
  /**
   * @return GoogleMapsPlacesV1PlaceOpeningHours
   */
  public function getRegularOpeningHours()
  {
    return $this->regularOpeningHours;
  }
  /**
   * Contains an array of entries for information about regular secondary hours
   * of a business. Secondary hours are different from a business's main hours.
   * For example, a restaurant can specify drive through hours or delivery hours
   * as its secondary hours. This field populates the type subfield, which draws
   * from a predefined list of opening hours types (such as DRIVE_THROUGH,
   * PICKUP, or TAKEOUT) based on the types of the place.
   *
   * @param GoogleMapsPlacesV1PlaceOpeningHours[] $regularSecondaryOpeningHours
   */
  public function setRegularSecondaryOpeningHours($regularSecondaryOpeningHours)
  {
    $this->regularSecondaryOpeningHours = $regularSecondaryOpeningHours;
  }
  /**
   * @return GoogleMapsPlacesV1PlaceOpeningHours[]
   */
  public function getRegularSecondaryOpeningHours()
  {
    return $this->regularSecondaryOpeningHours;
  }
  /**
   * Specifies if the place supports reservations.
   *
   * @param bool $reservable
   */
  public function setReservable($reservable)
  {
    $this->reservable = $reservable;
  }
  /**
   * @return bool
   */
  public function getReservable()
  {
    return $this->reservable;
  }
  /**
   * Place has restroom.
   *
   * @param bool $restroom
   */
  public function setRestroom($restroom)
  {
    $this->restroom = $restroom;
  }
  /**
   * @return bool
   */
  public function getRestroom()
  {
    return $this->restroom;
  }
  /**
   * AI-generated summary of the place using user reviews.
   *
   * @param GoogleMapsPlacesV1PlaceReviewSummary $reviewSummary
   */
  public function setReviewSummary(GoogleMapsPlacesV1PlaceReviewSummary $reviewSummary)
  {
    $this->reviewSummary = $reviewSummary;
  }
  /**
   * @return GoogleMapsPlacesV1PlaceReviewSummary
   */
  public function getReviewSummary()
  {
    return $this->reviewSummary;
  }
  /**
   * List of reviews about this place, sorted by relevance. A maximum of 5
   * reviews can be returned.
   *
   * @param GoogleMapsPlacesV1Review[] $reviews
   */
  public function setReviews($reviews)
  {
    $this->reviews = $reviews;
  }
  /**
   * @return GoogleMapsPlacesV1Review[]
   */
  public function getReviews()
  {
    return $this->reviews;
  }
  /**
   * Specifies if the place serves beer.
   *
   * @param bool $servesBeer
   */
  public function setServesBeer($servesBeer)
  {
    $this->servesBeer = $servesBeer;
  }
  /**
   * @return bool
   */
  public function getServesBeer()
  {
    return $this->servesBeer;
  }
  /**
   * Specifies if the place serves breakfast.
   *
   * @param bool $servesBreakfast
   */
  public function setServesBreakfast($servesBreakfast)
  {
    $this->servesBreakfast = $servesBreakfast;
  }
  /**
   * @return bool
   */
  public function getServesBreakfast()
  {
    return $this->servesBreakfast;
  }
  /**
   * Specifies if the place serves brunch.
   *
   * @param bool $servesBrunch
   */
  public function setServesBrunch($servesBrunch)
  {
    $this->servesBrunch = $servesBrunch;
  }
  /**
   * @return bool
   */
  public function getServesBrunch()
  {
    return $this->servesBrunch;
  }
  /**
   * Place serves cocktails.
   *
   * @param bool $servesCocktails
   */
  public function setServesCocktails($servesCocktails)
  {
    $this->servesCocktails = $servesCocktails;
  }
  /**
   * @return bool
   */
  public function getServesCocktails()
  {
    return $this->servesCocktails;
  }
  /**
   * Place serves coffee.
   *
   * @param bool $servesCoffee
   */
  public function setServesCoffee($servesCoffee)
  {
    $this->servesCoffee = $servesCoffee;
  }
  /**
   * @return bool
   */
  public function getServesCoffee()
  {
    return $this->servesCoffee;
  }
  /**
   * Place serves dessert.
   *
   * @param bool $servesDessert
   */
  public function setServesDessert($servesDessert)
  {
    $this->servesDessert = $servesDessert;
  }
  /**
   * @return bool
   */
  public function getServesDessert()
  {
    return $this->servesDessert;
  }
  /**
   * Specifies if the place serves dinner.
   *
   * @param bool $servesDinner
   */
  public function setServesDinner($servesDinner)
  {
    $this->servesDinner = $servesDinner;
  }
  /**
   * @return bool
   */
  public function getServesDinner()
  {
    return $this->servesDinner;
  }
  /**
   * Specifies if the place serves lunch.
   *
   * @param bool $servesLunch
   */
  public function setServesLunch($servesLunch)
  {
    $this->servesLunch = $servesLunch;
  }
  /**
   * @return bool
   */
  public function getServesLunch()
  {
    return $this->servesLunch;
  }
  /**
   * Specifies if the place serves vegetarian food.
   *
   * @param bool $servesVegetarianFood
   */
  public function setServesVegetarianFood($servesVegetarianFood)
  {
    $this->servesVegetarianFood = $servesVegetarianFood;
  }
  /**
   * @return bool
   */
  public function getServesVegetarianFood()
  {
    return $this->servesVegetarianFood;
  }
  /**
   * Specifies if the place serves wine.
   *
   * @param bool $servesWine
   */
  public function setServesWine($servesWine)
  {
    $this->servesWine = $servesWine;
  }
  /**
   * @return bool
   */
  public function getServesWine()
  {
    return $this->servesWine;
  }
  /**
   * A short, human-readable address for this place.
   *
   * @param string $shortFormattedAddress
   */
  public function setShortFormattedAddress($shortFormattedAddress)
  {
    $this->shortFormattedAddress = $shortFormattedAddress;
  }
  /**
   * @return string
   */
  public function getShortFormattedAddress()
  {
    return $this->shortFormattedAddress;
  }
  /**
   * A list of sub-destinations related to the place.
   *
   * @param GoogleMapsPlacesV1PlaceSubDestination[] $subDestinations
   */
  public function setSubDestinations($subDestinations)
  {
    $this->subDestinations = $subDestinations;
  }
  /**
   * @return GoogleMapsPlacesV1PlaceSubDestination[]
   */
  public function getSubDestinations()
  {
    return $this->subDestinations;
  }
  /**
   * Specifies if the business supports takeout.
   *
   * @param bool $takeout
   */
  public function setTakeout($takeout)
  {
    $this->takeout = $takeout;
  }
  /**
   * @return bool
   */
  public function getTakeout()
  {
    return $this->takeout;
  }
  /**
   * IANA Time Zone Database time zone. For example "America/New_York".
   *
   * @param GoogleTypeTimeZone $timeZone
   */
  public function setTimeZone(GoogleTypeTimeZone $timeZone)
  {
    $this->timeZone = $timeZone;
  }
  /**
   * @return GoogleTypeTimeZone
   */
  public function getTimeZone()
  {
    return $this->timeZone;
  }
  /**
   * A set of type tags for this result. For example, "political" and
   * "locality". For the complete list of possible values, see Table A and Table
   * B at https://developers.google.com/maps/documentation/places/web-
   * service/place-types
   *
   * @param string[] $types
   */
  public function setTypes($types)
  {
    $this->types = $types;
  }
  /**
   * @return string[]
   */
  public function getTypes()
  {
    return $this->types;
  }
  /**
   * The total number of reviews (with or without text) for this place.
   *
   * @param int $userRatingCount
   */
  public function setUserRatingCount($userRatingCount)
  {
    $this->userRatingCount = $userRatingCount;
  }
  /**
   * @return int
   */
  public function getUserRatingCount()
  {
    return $this->userRatingCount;
  }
  /**
   * Number of minutes this place's timezone is currently offset from UTC. This
   * is expressed in minutes to support timezones that are offset by fractions
   * of an hour, e.g. X hours and 15 minutes.
   *
   * @param int $utcOffsetMinutes
   */
  public function setUtcOffsetMinutes($utcOffsetMinutes)
  {
    $this->utcOffsetMinutes = $utcOffsetMinutes;
  }
  /**
   * @return int
   */
  public function getUtcOffsetMinutes()
  {
    return $this->utcOffsetMinutes;
  }
  /**
   * A viewport suitable for displaying the place on an average-sized map. This
   * viewport should not be used as the physical boundary or the service area of
   * the business.
   *
   * @param GoogleGeoTypeViewport $viewport
   */
  public function setViewport(GoogleGeoTypeViewport $viewport)
  {
    $this->viewport = $viewport;
  }
  /**
   * @return GoogleGeoTypeViewport
   */
  public function getViewport()
  {
    return $this->viewport;
  }
  /**
   * The authoritative website for this place, e.g. a business' homepage. Note
   * that for places that are part of a chain (e.g. an IKEA store), this will
   * usually be the website for the individual store, not the overall chain.
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
class_alias(GoogleMapsPlacesV1Place::class, 'Google_Service_MapsPlaces_GoogleMapsPlacesV1Place');
