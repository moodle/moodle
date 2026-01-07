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

namespace Google\Service\Walletobjects;

class FlightClass extends \Google\Collection
{
  public const FLIGHT_STATUS_FLIGHT_STATUS_UNSPECIFIED = 'FLIGHT_STATUS_UNSPECIFIED';
  /**
   * Flight is on time, early, or delayed.
   */
  public const FLIGHT_STATUS_SCHEDULED = 'SCHEDULED';
  /**
   * Legacy alias for `SCHEDULED`. Deprecated.
   *
   * @deprecated
   */
  public const FLIGHT_STATUS_scheduled = 'scheduled';
  /**
   * Flight is in progress (taxiing, taking off, landing, airborne).
   */
  public const FLIGHT_STATUS_ACTIVE = 'ACTIVE';
  /**
   * Legacy alias for `ACTIVE`. Deprecated.
   *
   * @deprecated
   */
  public const FLIGHT_STATUS_active = 'active';
  /**
   * Flight landed at the original destination.
   */
  public const FLIGHT_STATUS_LANDED = 'LANDED';
  /**
   * Legacy alias for `LANDED`. Deprecated.
   *
   * @deprecated
   */
  public const FLIGHT_STATUS_landed = 'landed';
  /**
   * Flight is cancelled.
   */
  public const FLIGHT_STATUS_CANCELLED = 'CANCELLED';
  /**
   * Legacy alias for `CANCELLED`. Deprecated.
   *
   * @deprecated
   */
  public const FLIGHT_STATUS_cancelled = 'cancelled';
  /**
   * Flight is airborne but heading to a different airport than the original
   * destination.
   */
  public const FLIGHT_STATUS_REDIRECTED = 'REDIRECTED';
  /**
   * Legacy alias for `REDIRECTED`. Deprecated.
   *
   * @deprecated
   */
  public const FLIGHT_STATUS_redirected = 'redirected';
  /**
   * Flight has already landed at a different airport than the original
   * destination.
   */
  public const FLIGHT_STATUS_DIVERTED = 'DIVERTED';
  /**
   * Legacy alias for `DIVERTED`. Deprecated.
   *
   * @deprecated
   */
  public const FLIGHT_STATUS_diverted = 'diverted';
  /**
   * Unspecified preference.
   */
  public const MULTIPLE_DEVICES_AND_HOLDERS_ALLOWED_STATUS_STATUS_UNSPECIFIED = 'STATUS_UNSPECIFIED';
  /**
   * The Pass object is shareable by a user and can be saved by any number of
   * different users, and on any number of devices. Partners typically use this
   * setup for passes that do not need to be restricted to a single user or
   * pinned to a single device.
   */
  public const MULTIPLE_DEVICES_AND_HOLDERS_ALLOWED_STATUS_MULTIPLE_HOLDERS = 'MULTIPLE_HOLDERS';
  /**
   * An object can only be saved by one user, but this user can view and use it
   * on multiple of their devices. Once the first user saves the object, no
   * other user will be allowed to view or save it.
   */
  public const MULTIPLE_DEVICES_AND_HOLDERS_ALLOWED_STATUS_ONE_USER_ALL_DEVICES = 'ONE_USER_ALL_DEVICES';
  /**
   * An object can only be saved by one user on a single device. Intended for
   * use by select partners in limited circumstances. An example use case is a
   * transit ticket that should be "device pinned", meaning it can be saved,
   * viewed and used only by a single user on a single device. Contact support
   * for additional information.
   */
  public const MULTIPLE_DEVICES_AND_HOLDERS_ALLOWED_STATUS_ONE_USER_ONE_DEVICE = 'ONE_USER_ONE_DEVICE';
  /**
   * Legacy alias for `MULTIPLE_HOLDERS`. Deprecated.
   *
   * @deprecated
   */
  public const MULTIPLE_DEVICES_AND_HOLDERS_ALLOWED_STATUS_multipleHolders = 'multipleHolders';
  /**
   * Legacy alias for `ONE_USER_ALL_DEVICES`. Deprecated.
   *
   * @deprecated
   */
  public const MULTIPLE_DEVICES_AND_HOLDERS_ALLOWED_STATUS_oneUserAllDevices = 'oneUserAllDevices';
  /**
   * Legacy alias for `ONE_USER_ONE_DEVICE`. Deprecated.
   *
   * @deprecated
   */
  public const MULTIPLE_DEVICES_AND_HOLDERS_ALLOWED_STATUS_oneUserOneDevice = 'oneUserOneDevice';
  /**
   * Default behavior is no notifications sent.
   */
  public const NOTIFY_PREFERENCE_NOTIFICATION_SETTINGS_FOR_UPDATES_UNSPECIFIED = 'NOTIFICATION_SETTINGS_FOR_UPDATES_UNSPECIFIED';
  /**
   * This value will result in a notification being sent, if the updated fields
   * are part of an allowlist.
   */
  public const NOTIFY_PREFERENCE_NOTIFY_ON_UPDATE = 'NOTIFY_ON_UPDATE';
  public const REVIEW_STATUS_REVIEW_STATUS_UNSPECIFIED = 'REVIEW_STATUS_UNSPECIFIED';
  public const REVIEW_STATUS_UNDER_REVIEW = 'UNDER_REVIEW';
  /**
   * Legacy alias for `UNDER_REVIEW`. Deprecated.
   *
   * @deprecated
   */
  public const REVIEW_STATUS_underReview = 'underReview';
  public const REVIEW_STATUS_APPROVED = 'APPROVED';
  /**
   * Legacy alias for `APPROVED`. Deprecated.
   *
   * @deprecated
   */
  public const REVIEW_STATUS_approved = 'approved';
  public const REVIEW_STATUS_REJECTED = 'REJECTED';
  /**
   * Legacy alias for `REJECTED`. Deprecated.
   *
   * @deprecated
   */
  public const REVIEW_STATUS_rejected = 'rejected';
  public const REVIEW_STATUS_DRAFT = 'DRAFT';
  /**
   * Legacy alias for `DRAFT`. Deprecated.
   *
   * @deprecated
   */
  public const REVIEW_STATUS_draft = 'draft';
  /**
   * Default value, same as UNLOCK_NOT_REQUIRED.
   */
  public const VIEW_UNLOCK_REQUIREMENT_VIEW_UNLOCK_REQUIREMENT_UNSPECIFIED = 'VIEW_UNLOCK_REQUIREMENT_UNSPECIFIED';
  /**
   * Default behavior for all the existing Passes if ViewUnlockRequirement is
   * not set.
   */
  public const VIEW_UNLOCK_REQUIREMENT_UNLOCK_NOT_REQUIRED = 'UNLOCK_NOT_REQUIRED';
  /**
   * Requires the user to unlock their device each time the pass is viewed. If
   * the user removes their device lock after saving the pass, then they will be
   * prompted to create a device lock before the pass can be viewed.
   */
  public const VIEW_UNLOCK_REQUIREMENT_UNLOCK_REQUIRED_TO_VIEW = 'UNLOCK_REQUIRED_TO_VIEW';
  protected $collection_key = 'valueAddedModuleData';
  /**
   * Deprecated. Use `multipleDevicesAndHoldersAllowedStatus` instead.
   *
   * @deprecated
   * @var bool
   */
  public $allowMultipleUsersPerObject;
  protected $appLinkDataType = AppLinkData::class;
  protected $appLinkDataDataType = '';
  protected $boardingAndSeatingPolicyType = BoardingAndSeatingPolicy::class;
  protected $boardingAndSeatingPolicyDataType = '';
  protected $callbackOptionsType = CallbackOptions::class;
  protected $callbackOptionsDataType = '';
  protected $classTemplateInfoType = ClassTemplateInfo::class;
  protected $classTemplateInfoDataType = '';
  /**
   * Country code used to display the card's country (when the user is not in
   * that country), as well as to display localized content when content is not
   * available in the user's locale.
   *
   * @var string
   */
  public $countryCode;
  protected $destinationType = AirportInfo::class;
  protected $destinationDataType = '';
  /**
   * Identifies whether this class supports Smart Tap. The `redemptionIssuers`
   * and object level `smartTapRedemptionLevel` fields must also be set up
   * correctly in order for a pass to support Smart Tap.
   *
   * @var bool
   */
  public $enableSmartTap;
  protected $flightHeaderType = FlightHeader::class;
  protected $flightHeaderDataType = '';
  /**
   * Status of this flight. If unset, Google will compute status based on data
   * from other sources, such as FlightStats, etc. Note: Google-computed status
   * will not be returned in API responses.
   *
   * @var string
   */
  public $flightStatus;
  protected $heroImageType = Image::class;
  protected $heroImageDataType = '';
  /**
   * The background color for the card. If not set the dominant color of the
   * hero image is used, and if no hero image is set, the dominant color of the
   * logo is used. The format is #rrggbb where rrggbb is a hex RGB triplet, such
   * as `#ffcc00`. You can also use the shorthand version of the RGB triplet
   * which is #rgb, such as `#fc0`.
   *
   * @var string
   */
  public $hexBackgroundColor;
  protected $homepageUriType = Uri::class;
  protected $homepageUriDataType = '';
  /**
   * Required. The unique identifier for a class. This ID must be unique across
   * all classes from an issuer. This value should follow the format issuer ID.
   * identifier where the former is issued by Google and latter is chosen by
   * you. Your unique identifier should only include alphanumeric characters,
   * '.', '_', or '-'.
   *
   * @var string
   */
  public $id;
  protected $imageModulesDataType = ImageModuleData::class;
  protected $imageModulesDataDataType = 'array';
  protected $infoModuleDataType = InfoModuleData::class;
  protected $infoModuleDataDataType = '';
  /**
   * Required. The issuer name. Recommended maximum length is 20 characters to
   * ensure full string is displayed on smaller screens.
   *
   * @var string
   */
  public $issuerName;
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * `"walletobjects#flightClass"`.
   *
   * @deprecated
   * @var string
   */
  public $kind;
  /**
   * If this field is present, boarding passes served to a user's device will
   * always be in this language. Represents the BCP 47 language tag. Example
   * values are "en-US", "en-GB", "de", or "de-AT".
   *
   * @var string
   */
  public $languageOverride;
  protected $linksModuleDataType = LinksModuleData::class;
  protected $linksModuleDataDataType = '';
  /**
   * The boarding time as it would be printed on the boarding pass. This is an
   * ISO 8601 extended format date/time without an offset. Time may be specified
   * up to millisecond precision. eg: `2027-03-05T06:30:00` This should be the
   * local date/time at the airport (not a UTC time). Google will reject the
   * request if UTC offset is provided. Time zones will be calculated by Google
   * based on departure airport.
   *
   * @var string
   */
  public $localBoardingDateTime;
  /**
   * The estimated time the aircraft plans to reach the destination gate (not
   * the runway) or the actual time it reached the gate. This field should be
   * set if at least one of the below is true: - It differs from the scheduled
   * time. Google will use it to calculate the delay. - The aircraft already
   * arrived at the gate. Google will use it to inform the user that the flight
   * has arrived at the gate. This is an ISO 8601 extended format date/time
   * without an offset. Time may be specified up to millisecond precision. eg:
   * `2027-03-05T06:30:00` This should be the local date/time at the airport
   * (not a UTC time). Google will reject the request if UTC offset is provided.
   * Time zones will be calculated by Google based on arrival airport.
   *
   * @var string
   */
  public $localEstimatedOrActualArrivalDateTime;
  /**
   * The estimated time the aircraft plans to pull from the gate or the actual
   * time the aircraft already pulled from the gate. Note: This is not the
   * runway time. This field should be set if at least one of the below is true:
   * - It differs from the scheduled time. Google will use it to calculate the
   * delay. - The aircraft already pulled from the gate. Google will use it to
   * inform the user when the flight actually departed. This is an ISO 8601
   * extended format date/time without an offset. Time may be specified up to
   * millisecond precision. eg: `2027-03-05T06:30:00` This should be the local
   * date/time at the airport (not a UTC time). Google will reject the request
   * if UTC offset is provided. Time zones will be calculated by Google based on
   * departure airport.
   *
   * @var string
   */
  public $localEstimatedOrActualDepartureDateTime;
  /**
   * The gate closing time as it would be printed on the boarding pass. Do not
   * set this field if you do not want to print it in the boarding pass. This is
   * an ISO 8601 extended format date/time without an offset. Time may be
   * specified up to millisecond precision. eg: `2027-03-05T06:30:00` This
   * should be the local date/time at the airport (not a UTC time). Google will
   * reject the request if UTC offset is provided. Time zones will be calculated
   * by Google based on departure airport.
   *
   * @var string
   */
  public $localGateClosingDateTime;
  /**
   * The scheduled time the aircraft plans to reach the destination gate (not
   * the runway). Note: This field should not change too close to the flight
   * time. For updates to departure times (delays, etc), please set
   * `localEstimatedOrActualArrivalDateTime`. This is an ISO 8601 extended
   * format date/time without an offset. Time may be specified up to millisecond
   * precision. eg: `2027-03-05T06:30:00` This should be the local date/time at
   * the airport (not a UTC time). Google will reject the request if UTC offset
   * is provided. Time zones will be calculated by Google based on arrival
   * airport.
   *
   * @var string
   */
  public $localScheduledArrivalDateTime;
  /**
   * Required. The scheduled date and time when the aircraft is expected to
   * depart the gate (not the runway) Note: This field should not change too
   * close to the departure time. For updates to departure times (delays, etc),
   * please set `localEstimatedOrActualDepartureDateTime`. This is an ISO 8601
   * extended format date/time without an offset. Time may be specified up to
   * millisecond precision. eg: `2027-03-05T06:30:00` This should be the local
   * date/time at the airport (not a UTC time). Google will reject the request
   * if UTC offset is provided. Time zones will be calculated by Google based on
   * departure airport.
   *
   * @var string
   */
  public $localScheduledDepartureDateTime;
  protected $localizedIssuerNameType = LocalizedString::class;
  protected $localizedIssuerNameDataType = '';
  protected $locationsType = LatLongPoint::class;
  protected $locationsDataType = 'array';
  protected $merchantLocationsType = MerchantLocation::class;
  protected $merchantLocationsDataType = 'array';
  protected $messagesType = Message::class;
  protected $messagesDataType = 'array';
  /**
   * Identifies whether multiple users and devices will save the same object
   * referencing this class.
   *
   * @var string
   */
  public $multipleDevicesAndHoldersAllowedStatus;
  /**
   * Whether or not field updates to this class should trigger notifications.
   * When set to NOTIFY, we will attempt to trigger a field update notification
   * to users. These notifications will only be sent to users if the field is
   * part of an allowlist. If not specified, no notification will be triggered.
   * This setting is ephemeral and needs to be set with each PATCH or UPDATE
   * request, otherwise a notification will not be triggered.
   *
   * @var string
   */
  public $notifyPreference;
  protected $originType = AirportInfo::class;
  protected $originDataType = '';
  /**
   * Identifies which redemption issuers can redeem the pass over Smart Tap.
   * Redemption issuers are identified by their issuer ID. Redemption issuers
   * must have at least one Smart Tap key configured. The `enableSmartTap` and
   * object level `smartTapRedemptionLevel` fields must also be set up correctly
   * in order for a pass to support Smart Tap.
   *
   * @var string[]
   */
  public $redemptionIssuers;
  protected $reviewType = Review::class;
  protected $reviewDataType = '';
  /**
   * Required. The status of the class. This field can be set to `draft` or
   * `underReview` using the insert, patch, or update API calls. Once the review
   * state is changed from `draft` it may not be changed back to `draft`. You
   * should keep this field to `draft` when the class is under development. A
   * `draft` class cannot be used to create any object. You should set this
   * field to `underReview` when you believe the class is ready for use. The
   * platform will automatically set this field to `approved` and it can be
   * immediately used to create or migrate objects. When updating an already
   * `approved` class you should keep setting this field to `underReview`.
   *
   * @var string
   */
  public $reviewStatus;
  protected $securityAnimationType = SecurityAnimation::class;
  protected $securityAnimationDataType = '';
  protected $textModulesDataType = TextModuleData::class;
  protected $textModulesDataDataType = 'array';
  protected $valueAddedModuleDataType = ValueAddedModuleData::class;
  protected $valueAddedModuleDataDataType = 'array';
  /**
   * Deprecated
   *
   * @deprecated
   * @var string
   */
  public $version;
  /**
   * View Unlock Requirement options for the boarding pass.
   *
   * @var string
   */
  public $viewUnlockRequirement;
  protected $wordMarkType = Image::class;
  protected $wordMarkDataType = '';

  /**
   * Deprecated. Use `multipleDevicesAndHoldersAllowedStatus` instead.
   *
   * @deprecated
   * @param bool $allowMultipleUsersPerObject
   */
  public function setAllowMultipleUsersPerObject($allowMultipleUsersPerObject)
  {
    $this->allowMultipleUsersPerObject = $allowMultipleUsersPerObject;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getAllowMultipleUsersPerObject()
  {
    return $this->allowMultipleUsersPerObject;
  }
  /**
   * Optional app or website link that will be displayed as a button on the
   * front of the pass. If AppLinkData is provided for the corresponding object
   * that will be used instead.
   *
   * @param AppLinkData $appLinkData
   */
  public function setAppLinkData(AppLinkData $appLinkData)
  {
    $this->appLinkData = $appLinkData;
  }
  /**
   * @return AppLinkData
   */
  public function getAppLinkData()
  {
    return $this->appLinkData;
  }
  /**
   * Policies for boarding and seating. These will inform which labels will be
   * shown to users.
   *
   * @param BoardingAndSeatingPolicy $boardingAndSeatingPolicy
   */
  public function setBoardingAndSeatingPolicy(BoardingAndSeatingPolicy $boardingAndSeatingPolicy)
  {
    $this->boardingAndSeatingPolicy = $boardingAndSeatingPolicy;
  }
  /**
   * @return BoardingAndSeatingPolicy
   */
  public function getBoardingAndSeatingPolicy()
  {
    return $this->boardingAndSeatingPolicy;
  }
  /**
   * Callback options to be used to call the issuer back for every save/delete
   * of an object for this class by the end-user. All objects of this class are
   * eligible for the callback.
   *
   * @param CallbackOptions $callbackOptions
   */
  public function setCallbackOptions(CallbackOptions $callbackOptions)
  {
    $this->callbackOptions = $callbackOptions;
  }
  /**
   * @return CallbackOptions
   */
  public function getCallbackOptions()
  {
    return $this->callbackOptions;
  }
  /**
   * Template information about how the class should be displayed. If unset,
   * Google will fallback to a default set of fields to display.
   *
   * @param ClassTemplateInfo $classTemplateInfo
   */
  public function setClassTemplateInfo(ClassTemplateInfo $classTemplateInfo)
  {
    $this->classTemplateInfo = $classTemplateInfo;
  }
  /**
   * @return ClassTemplateInfo
   */
  public function getClassTemplateInfo()
  {
    return $this->classTemplateInfo;
  }
  /**
   * Country code used to display the card's country (when the user is not in
   * that country), as well as to display localized content when content is not
   * available in the user's locale.
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
   * Required. Destination airport.
   *
   * @param AirportInfo $destination
   */
  public function setDestination(AirportInfo $destination)
  {
    $this->destination = $destination;
  }
  /**
   * @return AirportInfo
   */
  public function getDestination()
  {
    return $this->destination;
  }
  /**
   * Identifies whether this class supports Smart Tap. The `redemptionIssuers`
   * and object level `smartTapRedemptionLevel` fields must also be set up
   * correctly in order for a pass to support Smart Tap.
   *
   * @param bool $enableSmartTap
   */
  public function setEnableSmartTap($enableSmartTap)
  {
    $this->enableSmartTap = $enableSmartTap;
  }
  /**
   * @return bool
   */
  public function getEnableSmartTap()
  {
    return $this->enableSmartTap;
  }
  /**
   * Required. Information about the flight carrier and number.
   *
   * @param FlightHeader $flightHeader
   */
  public function setFlightHeader(FlightHeader $flightHeader)
  {
    $this->flightHeader = $flightHeader;
  }
  /**
   * @return FlightHeader
   */
  public function getFlightHeader()
  {
    return $this->flightHeader;
  }
  /**
   * Status of this flight. If unset, Google will compute status based on data
   * from other sources, such as FlightStats, etc. Note: Google-computed status
   * will not be returned in API responses.
   *
   * Accepted values: FLIGHT_STATUS_UNSPECIFIED, SCHEDULED, scheduled, ACTIVE,
   * active, LANDED, landed, CANCELLED, cancelled, REDIRECTED, redirected,
   * DIVERTED, diverted
   *
   * @param self::FLIGHT_STATUS_* $flightStatus
   */
  public function setFlightStatus($flightStatus)
  {
    $this->flightStatus = $flightStatus;
  }
  /**
   * @return self::FLIGHT_STATUS_*
   */
  public function getFlightStatus()
  {
    return $this->flightStatus;
  }
  /**
   * Optional banner image displayed on the front of the card. If none is
   * present, nothing will be displayed. The image will display at 100% width.
   *
   * @param Image $heroImage
   */
  public function setHeroImage(Image $heroImage)
  {
    $this->heroImage = $heroImage;
  }
  /**
   * @return Image
   */
  public function getHeroImage()
  {
    return $this->heroImage;
  }
  /**
   * The background color for the card. If not set the dominant color of the
   * hero image is used, and if no hero image is set, the dominant color of the
   * logo is used. The format is #rrggbb where rrggbb is a hex RGB triplet, such
   * as `#ffcc00`. You can also use the shorthand version of the RGB triplet
   * which is #rgb, such as `#fc0`.
   *
   * @param string $hexBackgroundColor
   */
  public function setHexBackgroundColor($hexBackgroundColor)
  {
    $this->hexBackgroundColor = $hexBackgroundColor;
  }
  /**
   * @return string
   */
  public function getHexBackgroundColor()
  {
    return $this->hexBackgroundColor;
  }
  /**
   * The URI of your application's home page. Populating the URI in this field
   * results in the exact same behavior as populating an URI in linksModuleData
   * (when an object is rendered, a link to the homepage is shown in what would
   * usually be thought of as the linksModuleData section of the object).
   *
   * @param Uri $homepageUri
   */
  public function setHomepageUri(Uri $homepageUri)
  {
    $this->homepageUri = $homepageUri;
  }
  /**
   * @return Uri
   */
  public function getHomepageUri()
  {
    return $this->homepageUri;
  }
  /**
   * Required. The unique identifier for a class. This ID must be unique across
   * all classes from an issuer. This value should follow the format issuer ID.
   * identifier where the former is issued by Google and latter is chosen by
   * you. Your unique identifier should only include alphanumeric characters,
   * '.', '_', or '-'.
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
   * Image module data. The maximum number of these fields displayed is 1 from
   * object level and 1 for class object level.
   *
   * @param ImageModuleData[] $imageModulesData
   */
  public function setImageModulesData($imageModulesData)
  {
    $this->imageModulesData = $imageModulesData;
  }
  /**
   * @return ImageModuleData[]
   */
  public function getImageModulesData()
  {
    return $this->imageModulesData;
  }
  /**
   * Deprecated. Use textModulesData instead.
   *
   * @deprecated
   * @param InfoModuleData $infoModuleData
   */
  public function setInfoModuleData(InfoModuleData $infoModuleData)
  {
    $this->infoModuleData = $infoModuleData;
  }
  /**
   * @deprecated
   * @return InfoModuleData
   */
  public function getInfoModuleData()
  {
    return $this->infoModuleData;
  }
  /**
   * Required. The issuer name. Recommended maximum length is 20 characters to
   * ensure full string is displayed on smaller screens.
   *
   * @param string $issuerName
   */
  public function setIssuerName($issuerName)
  {
    $this->issuerName = $issuerName;
  }
  /**
   * @return string
   */
  public function getIssuerName()
  {
    return $this->issuerName;
  }
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * `"walletobjects#flightClass"`.
   *
   * @deprecated
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * If this field is present, boarding passes served to a user's device will
   * always be in this language. Represents the BCP 47 language tag. Example
   * values are "en-US", "en-GB", "de", or "de-AT".
   *
   * @param string $languageOverride
   */
  public function setLanguageOverride($languageOverride)
  {
    $this->languageOverride = $languageOverride;
  }
  /**
   * @return string
   */
  public function getLanguageOverride()
  {
    return $this->languageOverride;
  }
  /**
   * Links module data. If links module data is also defined on the object, both
   * will be displayed.
   *
   * @param LinksModuleData $linksModuleData
   */
  public function setLinksModuleData(LinksModuleData $linksModuleData)
  {
    $this->linksModuleData = $linksModuleData;
  }
  /**
   * @return LinksModuleData
   */
  public function getLinksModuleData()
  {
    return $this->linksModuleData;
  }
  /**
   * The boarding time as it would be printed on the boarding pass. This is an
   * ISO 8601 extended format date/time without an offset. Time may be specified
   * up to millisecond precision. eg: `2027-03-05T06:30:00` This should be the
   * local date/time at the airport (not a UTC time). Google will reject the
   * request if UTC offset is provided. Time zones will be calculated by Google
   * based on departure airport.
   *
   * @param string $localBoardingDateTime
   */
  public function setLocalBoardingDateTime($localBoardingDateTime)
  {
    $this->localBoardingDateTime = $localBoardingDateTime;
  }
  /**
   * @return string
   */
  public function getLocalBoardingDateTime()
  {
    return $this->localBoardingDateTime;
  }
  /**
   * The estimated time the aircraft plans to reach the destination gate (not
   * the runway) or the actual time it reached the gate. This field should be
   * set if at least one of the below is true: - It differs from the scheduled
   * time. Google will use it to calculate the delay. - The aircraft already
   * arrived at the gate. Google will use it to inform the user that the flight
   * has arrived at the gate. This is an ISO 8601 extended format date/time
   * without an offset. Time may be specified up to millisecond precision. eg:
   * `2027-03-05T06:30:00` This should be the local date/time at the airport
   * (not a UTC time). Google will reject the request if UTC offset is provided.
   * Time zones will be calculated by Google based on arrival airport.
   *
   * @param string $localEstimatedOrActualArrivalDateTime
   */
  public function setLocalEstimatedOrActualArrivalDateTime($localEstimatedOrActualArrivalDateTime)
  {
    $this->localEstimatedOrActualArrivalDateTime = $localEstimatedOrActualArrivalDateTime;
  }
  /**
   * @return string
   */
  public function getLocalEstimatedOrActualArrivalDateTime()
  {
    return $this->localEstimatedOrActualArrivalDateTime;
  }
  /**
   * The estimated time the aircraft plans to pull from the gate or the actual
   * time the aircraft already pulled from the gate. Note: This is not the
   * runway time. This field should be set if at least one of the below is true:
   * - It differs from the scheduled time. Google will use it to calculate the
   * delay. - The aircraft already pulled from the gate. Google will use it to
   * inform the user when the flight actually departed. This is an ISO 8601
   * extended format date/time without an offset. Time may be specified up to
   * millisecond precision. eg: `2027-03-05T06:30:00` This should be the local
   * date/time at the airport (not a UTC time). Google will reject the request
   * if UTC offset is provided. Time zones will be calculated by Google based on
   * departure airport.
   *
   * @param string $localEstimatedOrActualDepartureDateTime
   */
  public function setLocalEstimatedOrActualDepartureDateTime($localEstimatedOrActualDepartureDateTime)
  {
    $this->localEstimatedOrActualDepartureDateTime = $localEstimatedOrActualDepartureDateTime;
  }
  /**
   * @return string
   */
  public function getLocalEstimatedOrActualDepartureDateTime()
  {
    return $this->localEstimatedOrActualDepartureDateTime;
  }
  /**
   * The gate closing time as it would be printed on the boarding pass. Do not
   * set this field if you do not want to print it in the boarding pass. This is
   * an ISO 8601 extended format date/time without an offset. Time may be
   * specified up to millisecond precision. eg: `2027-03-05T06:30:00` This
   * should be the local date/time at the airport (not a UTC time). Google will
   * reject the request if UTC offset is provided. Time zones will be calculated
   * by Google based on departure airport.
   *
   * @param string $localGateClosingDateTime
   */
  public function setLocalGateClosingDateTime($localGateClosingDateTime)
  {
    $this->localGateClosingDateTime = $localGateClosingDateTime;
  }
  /**
   * @return string
   */
  public function getLocalGateClosingDateTime()
  {
    return $this->localGateClosingDateTime;
  }
  /**
   * The scheduled time the aircraft plans to reach the destination gate (not
   * the runway). Note: This field should not change too close to the flight
   * time. For updates to departure times (delays, etc), please set
   * `localEstimatedOrActualArrivalDateTime`. This is an ISO 8601 extended
   * format date/time without an offset. Time may be specified up to millisecond
   * precision. eg: `2027-03-05T06:30:00` This should be the local date/time at
   * the airport (not a UTC time). Google will reject the request if UTC offset
   * is provided. Time zones will be calculated by Google based on arrival
   * airport.
   *
   * @param string $localScheduledArrivalDateTime
   */
  public function setLocalScheduledArrivalDateTime($localScheduledArrivalDateTime)
  {
    $this->localScheduledArrivalDateTime = $localScheduledArrivalDateTime;
  }
  /**
   * @return string
   */
  public function getLocalScheduledArrivalDateTime()
  {
    return $this->localScheduledArrivalDateTime;
  }
  /**
   * Required. The scheduled date and time when the aircraft is expected to
   * depart the gate (not the runway) Note: This field should not change too
   * close to the departure time. For updates to departure times (delays, etc),
   * please set `localEstimatedOrActualDepartureDateTime`. This is an ISO 8601
   * extended format date/time without an offset. Time may be specified up to
   * millisecond precision. eg: `2027-03-05T06:30:00` This should be the local
   * date/time at the airport (not a UTC time). Google will reject the request
   * if UTC offset is provided. Time zones will be calculated by Google based on
   * departure airport.
   *
   * @param string $localScheduledDepartureDateTime
   */
  public function setLocalScheduledDepartureDateTime($localScheduledDepartureDateTime)
  {
    $this->localScheduledDepartureDateTime = $localScheduledDepartureDateTime;
  }
  /**
   * @return string
   */
  public function getLocalScheduledDepartureDateTime()
  {
    return $this->localScheduledDepartureDateTime;
  }
  /**
   * Translated strings for the issuer_name. Recommended maximum length is 20
   * characters to ensure full string is displayed on smaller screens.
   *
   * @param LocalizedString $localizedIssuerName
   */
  public function setLocalizedIssuerName(LocalizedString $localizedIssuerName)
  {
    $this->localizedIssuerName = $localizedIssuerName;
  }
  /**
   * @return LocalizedString
   */
  public function getLocalizedIssuerName()
  {
    return $this->localizedIssuerName;
  }
  /**
   * Note: This field is currently not supported to trigger geo notifications.
   *
   * @deprecated
   * @param LatLongPoint[] $locations
   */
  public function setLocations($locations)
  {
    $this->locations = $locations;
  }
  /**
   * @deprecated
   * @return LatLongPoint[]
   */
  public function getLocations()
  {
    return $this->locations;
  }
  /**
   * Merchant locations. There is a maximum of ten on the class. Any additional
   * MerchantLocations added beyond the 10 will be rejected by the validator.
   * These locations will trigger a notification when a user enters within a
   * Google-set radius of the point. This field replaces the deprecated
   * LatLongPoints.
   *
   * @param MerchantLocation[] $merchantLocations
   */
  public function setMerchantLocations($merchantLocations)
  {
    $this->merchantLocations = $merchantLocations;
  }
  /**
   * @return MerchantLocation[]
   */
  public function getMerchantLocations()
  {
    return $this->merchantLocations;
  }
  /**
   * An array of messages displayed in the app. All users of this object will
   * receive its associated messages. The maximum number of these fields is 10.
   *
   * @param Message[] $messages
   */
  public function setMessages($messages)
  {
    $this->messages = $messages;
  }
  /**
   * @return Message[]
   */
  public function getMessages()
  {
    return $this->messages;
  }
  /**
   * Identifies whether multiple users and devices will save the same object
   * referencing this class.
   *
   * Accepted values: STATUS_UNSPECIFIED, MULTIPLE_HOLDERS,
   * ONE_USER_ALL_DEVICES, ONE_USER_ONE_DEVICE, multipleHolders,
   * oneUserAllDevices, oneUserOneDevice
   *
   * @param self::MULTIPLE_DEVICES_AND_HOLDERS_ALLOWED_STATUS_* $multipleDevicesAndHoldersAllowedStatus
   */
  public function setMultipleDevicesAndHoldersAllowedStatus($multipleDevicesAndHoldersAllowedStatus)
  {
    $this->multipleDevicesAndHoldersAllowedStatus = $multipleDevicesAndHoldersAllowedStatus;
  }
  /**
   * @return self::MULTIPLE_DEVICES_AND_HOLDERS_ALLOWED_STATUS_*
   */
  public function getMultipleDevicesAndHoldersAllowedStatus()
  {
    return $this->multipleDevicesAndHoldersAllowedStatus;
  }
  /**
   * Whether or not field updates to this class should trigger notifications.
   * When set to NOTIFY, we will attempt to trigger a field update notification
   * to users. These notifications will only be sent to users if the field is
   * part of an allowlist. If not specified, no notification will be triggered.
   * This setting is ephemeral and needs to be set with each PATCH or UPDATE
   * request, otherwise a notification will not be triggered.
   *
   * Accepted values: NOTIFICATION_SETTINGS_FOR_UPDATES_UNSPECIFIED,
   * NOTIFY_ON_UPDATE
   *
   * @param self::NOTIFY_PREFERENCE_* $notifyPreference
   */
  public function setNotifyPreference($notifyPreference)
  {
    $this->notifyPreference = $notifyPreference;
  }
  /**
   * @return self::NOTIFY_PREFERENCE_*
   */
  public function getNotifyPreference()
  {
    return $this->notifyPreference;
  }
  /**
   * Required. Origin airport.
   *
   * @param AirportInfo $origin
   */
  public function setOrigin(AirportInfo $origin)
  {
    $this->origin = $origin;
  }
  /**
   * @return AirportInfo
   */
  public function getOrigin()
  {
    return $this->origin;
  }
  /**
   * Identifies which redemption issuers can redeem the pass over Smart Tap.
   * Redemption issuers are identified by their issuer ID. Redemption issuers
   * must have at least one Smart Tap key configured. The `enableSmartTap` and
   * object level `smartTapRedemptionLevel` fields must also be set up correctly
   * in order for a pass to support Smart Tap.
   *
   * @param string[] $redemptionIssuers
   */
  public function setRedemptionIssuers($redemptionIssuers)
  {
    $this->redemptionIssuers = $redemptionIssuers;
  }
  /**
   * @return string[]
   */
  public function getRedemptionIssuers()
  {
    return $this->redemptionIssuers;
  }
  /**
   * The review comments set by the platform when a class is marked `approved`
   * or `rejected`.
   *
   * @param Review $review
   */
  public function setReview(Review $review)
  {
    $this->review = $review;
  }
  /**
   * @return Review
   */
  public function getReview()
  {
    return $this->review;
  }
  /**
   * Required. The status of the class. This field can be set to `draft` or
   * `underReview` using the insert, patch, or update API calls. Once the review
   * state is changed from `draft` it may not be changed back to `draft`. You
   * should keep this field to `draft` when the class is under development. A
   * `draft` class cannot be used to create any object. You should set this
   * field to `underReview` when you believe the class is ready for use. The
   * platform will automatically set this field to `approved` and it can be
   * immediately used to create or migrate objects. When updating an already
   * `approved` class you should keep setting this field to `underReview`.
   *
   * Accepted values: REVIEW_STATUS_UNSPECIFIED, UNDER_REVIEW, underReview,
   * APPROVED, approved, REJECTED, rejected, DRAFT, draft
   *
   * @param self::REVIEW_STATUS_* $reviewStatus
   */
  public function setReviewStatus($reviewStatus)
  {
    $this->reviewStatus = $reviewStatus;
  }
  /**
   * @return self::REVIEW_STATUS_*
   */
  public function getReviewStatus()
  {
    return $this->reviewStatus;
  }
  /**
   * Optional information about the security animation. If this is set a
   * security animation will be rendered on pass details.
   *
   * @param SecurityAnimation $securityAnimation
   */
  public function setSecurityAnimation(SecurityAnimation $securityAnimation)
  {
    $this->securityAnimation = $securityAnimation;
  }
  /**
   * @return SecurityAnimation
   */
  public function getSecurityAnimation()
  {
    return $this->securityAnimation;
  }
  /**
   * Text module data. If text module data is also defined on the class, both
   * will be displayed. The maximum number of these fields displayed is 10 from
   * the object and 10 from the class.
   *
   * @param TextModuleData[] $textModulesData
   */
  public function setTextModulesData($textModulesData)
  {
    $this->textModulesData = $textModulesData;
  }
  /**
   * @return TextModuleData[]
   */
  public function getTextModulesData()
  {
    return $this->textModulesData;
  }
  /**
   * Optional value added module data. Maximum of ten on the class. For a pass
   * only ten will be displayed, prioritizing those from the object.
   *
   * @param ValueAddedModuleData[] $valueAddedModuleData
   */
  public function setValueAddedModuleData($valueAddedModuleData)
  {
    $this->valueAddedModuleData = $valueAddedModuleData;
  }
  /**
   * @return ValueAddedModuleData[]
   */
  public function getValueAddedModuleData()
  {
    return $this->valueAddedModuleData;
  }
  /**
   * Deprecated
   *
   * @deprecated
   * @param string $version
   */
  public function setVersion($version)
  {
    $this->version = $version;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getVersion()
  {
    return $this->version;
  }
  /**
   * View Unlock Requirement options for the boarding pass.
   *
   * Accepted values: VIEW_UNLOCK_REQUIREMENT_UNSPECIFIED, UNLOCK_NOT_REQUIRED,
   * UNLOCK_REQUIRED_TO_VIEW
   *
   * @param self::VIEW_UNLOCK_REQUIREMENT_* $viewUnlockRequirement
   */
  public function setViewUnlockRequirement($viewUnlockRequirement)
  {
    $this->viewUnlockRequirement = $viewUnlockRequirement;
  }
  /**
   * @return self::VIEW_UNLOCK_REQUIREMENT_*
   */
  public function getViewUnlockRequirement()
  {
    return $this->viewUnlockRequirement;
  }
  /**
   * Deprecated.
   *
   * @deprecated
   * @param Image $wordMark
   */
  public function setWordMark(Image $wordMark)
  {
    $this->wordMark = $wordMark;
  }
  /**
   * @deprecated
   * @return Image
   */
  public function getWordMark()
  {
    return $this->wordMark;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FlightClass::class, 'Google_Service_Walletobjects_FlightClass');
