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

class TransitObject extends \Google\Collection
{
  public const CONCESSION_CATEGORY_CONCESSION_CATEGORY_UNSPECIFIED = 'CONCESSION_CATEGORY_UNSPECIFIED';
  public const CONCESSION_CATEGORY_ADULT = 'ADULT';
  /**
   * Legacy alias for `ADULT`. Deprecated.
   *
   * @deprecated
   */
  public const CONCESSION_CATEGORY_adult = 'adult';
  public const CONCESSION_CATEGORY_CHILD = 'CHILD';
  /**
   * Legacy alias for `CHILD`. Deprecated.
   *
   * @deprecated
   */
  public const CONCESSION_CATEGORY_child = 'child';
  public const CONCESSION_CATEGORY_SENIOR = 'SENIOR';
  /**
   * Legacy alias for `SENIOR`. Deprecated.
   *
   * @deprecated
   */
  public const CONCESSION_CATEGORY_senior = 'senior';
  /**
   * Default behavior is no notifications sent.
   */
  public const NOTIFY_PREFERENCE_NOTIFICATION_SETTINGS_FOR_UPDATES_UNSPECIFIED = 'NOTIFICATION_SETTINGS_FOR_UPDATES_UNSPECIFIED';
  /**
   * This value will result in a notification being sent, if the updated fields
   * are part of an allowlist.
   */
  public const NOTIFY_PREFERENCE_NOTIFY_ON_UPDATE = 'NOTIFY_ON_UPDATE';
  public const PASSENGER_TYPE_PASSENGER_TYPE_UNSPECIFIED = 'PASSENGER_TYPE_UNSPECIFIED';
  public const PASSENGER_TYPE_SINGLE_PASSENGER = 'SINGLE_PASSENGER';
  /**
   * Legacy alias for `SINGLE_PASSENGER`. Deprecated.
   *
   * @deprecated
   */
  public const PASSENGER_TYPE_singlePassenger = 'singlePassenger';
  public const PASSENGER_TYPE_MULTIPLE_PASSENGERS = 'MULTIPLE_PASSENGERS';
  /**
   * Legacy alias for `MULTIPLE_PASSENGERS`. Deprecated.
   *
   * @deprecated
   */
  public const PASSENGER_TYPE_multiplePassengers = 'multiplePassengers';
  /**
   * Default value.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Object is active and displayed to with other active objects.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * Legacy alias for `ACTIVE`. Deprecated.
   *
   * @deprecated
   */
  public const STATE_active = 'active';
  /**
   * Object has completed it's lifecycle.
   */
  public const STATE_COMPLETED = 'COMPLETED';
  /**
   * Legacy alias for `COMPLETED`. Deprecated.
   *
   * @deprecated
   */
  public const STATE_completed = 'completed';
  /**
   * Object is no longer valid (`validTimeInterval` passed).
   */
  public const STATE_EXPIRED = 'EXPIRED';
  /**
   * Legacy alias for `EXPIRED`. Deprecated.
   *
   * @deprecated
   */
  public const STATE_expired = 'expired';
  /**
   * Object is no longer valid
   */
  public const STATE_INACTIVE = 'INACTIVE';
  /**
   * Legacy alias for `INACTIVE`. Deprecated.
   *
   * @deprecated
   */
  public const STATE_inactive = 'inactive';
  public const TICKET_STATUS_TICKET_STATUS_UNSPECIFIED = 'TICKET_STATUS_UNSPECIFIED';
  public const TICKET_STATUS_USED = 'USED';
  /**
   * Legacy alias for `USED`. Deprecated.
   *
   * @deprecated
   */
  public const TICKET_STATUS_used = 'used';
  public const TICKET_STATUS_REFUNDED = 'REFUNDED';
  /**
   * Legacy alias for `REFUNDED`. Deprecated.
   *
   * @deprecated
   */
  public const TICKET_STATUS_refunded = 'refunded';
  public const TICKET_STATUS_EXCHANGED = 'EXCHANGED';
  /**
   * Legacy alias for `EXCHANGED`. Deprecated.
   *
   * @deprecated
   */
  public const TICKET_STATUS_exchanged = 'exchanged';
  public const TRIP_TYPE_TRIP_TYPE_UNSPECIFIED = 'TRIP_TYPE_UNSPECIFIED';
  public const TRIP_TYPE_ROUND_TRIP = 'ROUND_TRIP';
  /**
   * Legacy alias for `ROUND_TRIP`. Deprecated.
   *
   * @deprecated
   */
  public const TRIP_TYPE_roundTrip = 'roundTrip';
  public const TRIP_TYPE_ONE_WAY = 'ONE_WAY';
  /**
   * Legacy alias for `ONE_WAY`. Deprecated.
   *
   * @deprecated
   */
  public const TRIP_TYPE_oneWay = 'oneWay';
  protected $collection_key = 'valueAddedModuleData';
  protected $activationStatusType = ActivationStatus::class;
  protected $activationStatusDataType = '';
  protected $appLinkDataType = AppLinkData::class;
  protected $appLinkDataDataType = '';
  protected $barcodeType = Barcode::class;
  protected $barcodeDataType = '';
  /**
   * Required. The class associated with this object. The class must be of the
   * same type as this object, must already exist, and must be approved. Class
   * IDs should follow the format issuer ID.identifier where the former is
   * issued by Google and latter is chosen by you.
   *
   * @var string
   */
  public $classId;
  protected $classReferenceType = TransitClass::class;
  protected $classReferenceDataType = '';
  /**
   * The concession category for the ticket.
   *
   * @var string
   */
  public $concessionCategory;
  protected $customConcessionCategoryType = LocalizedString::class;
  protected $customConcessionCategoryDataType = '';
  protected $customTicketStatusType = LocalizedString::class;
  protected $customTicketStatusDataType = '';
  protected $deviceContextType = DeviceContext::class;
  protected $deviceContextDataType = '';
  /**
   * Indicates if notifications should explicitly be suppressed. If this field
   * is set to true, regardless of the `messages` field, expiration
   * notifications to the user will be suppressed. By default, this field is set
   * to false. Currently, this can only be set for offers.
   *
   * @var bool
   */
  public $disableExpirationNotification;
  protected $groupingInfoType = GroupingInfo::class;
  protected $groupingInfoDataType = '';
  /**
   * Whether this object is currently linked to a single device. This field is
   * set by the platform when a user saves the object, linking it to their
   * device. Intended for use by select partners. Contact support for additional
   * information.
   *
   * @var bool
   */
  public $hasLinkedDevice;
  /**
   * Indicates if the object has users. This field is set by the platform.
   *
   * @var bool
   */
  public $hasUsers;
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
  /**
   * Required. The unique identifier for an object. This ID must be unique
   * across all objects from an issuer. This value should follow the format
   * issuer ID.identifier where the former is issued by Google and latter is
   * chosen by you. The unique identifier should only include alphanumeric
   * characters, '.', '_', or '-'.
   *
   * @var string
   */
  public $id;
  protected $imageModulesDataType = ImageModuleData::class;
  protected $imageModulesDataDataType = 'array';
  protected $infoModuleDataType = InfoModuleData::class;
  protected $infoModuleDataDataType = '';
  /**
   * linked_object_ids are a list of other objects such as event ticket,
   * loyalty, offer, generic, giftcard, transit and boarding pass that should be
   * automatically attached to this transit object. If a user had saved this
   * transit card, then these linked_object_ids would be automatically pushed to
   * the user's wallet (unless they turned off the setting to receive such
   * linked passes). Make sure that objects present in linked_object_ids are
   * already inserted - if not, calls would fail. Once linked, the linked
   * objects cannot be unlinked. You cannot link objects belonging to another
   * issuer. There is a limit to the number of objects that can be linked to a
   * single object. After the limit is reached, new linked objects in the call
   * will be ignored silently. Object IDs should follow the format issuer ID.
   * identifier where the former is issued by Google and the latter is chosen by
   * you.
   *
   * @var string[]
   */
  public $linkedObjectIds;
  protected $linksModuleDataType = LinksModuleData::class;
  protected $linksModuleDataDataType = '';
  protected $locationsType = LatLongPoint::class;
  protected $locationsDataType = 'array';
  protected $merchantLocationsType = MerchantLocation::class;
  protected $merchantLocationsDataType = 'array';
  protected $messagesType = Message::class;
  protected $messagesDataType = 'array';
  /**
   * Whether or not field updates to this object should trigger notifications.
   * When set to NOTIFY, we will attempt to trigger a field update notification
   * to users. These notifications will only be sent to users if the field is
   * part of an allowlist. If set to DO_NOT_NOTIFY or
   * NOTIFICATION_SETTINGS_UNSPECIFIED, no notification will be triggered. This
   * setting is ephemeral and needs to be set with each PATCH or UPDATE request,
   * otherwise a notification will not be triggered.
   *
   * @var string
   */
  public $notifyPreference;
  protected $passConstraintsType = PassConstraints::class;
  protected $passConstraintsDataType = '';
  /**
   * The name(s) of the passengers the ticket is assigned to. The above
   * `passengerType` field is meant to give Google context on this field.
   *
   * @var string
   */
  public $passengerNames;
  /**
   * The number of passengers.
   *
   * @var string
   */
  public $passengerType;
  protected $purchaseDetailsType = PurchaseDetails::class;
  protected $purchaseDetailsDataType = '';
  protected $rotatingBarcodeType = RotatingBarcode::class;
  protected $rotatingBarcodeDataType = '';
  protected $saveRestrictionsType = SaveRestrictions::class;
  protected $saveRestrictionsDataType = '';
  /**
   * The value that will be transmitted to a Smart Tap certified terminal over
   * NFC for this object. The class level fields `enableSmartTap` and
   * `redemptionIssuers` must also be set up correctly in order for the pass to
   * support Smart Tap. Only ASCII characters are supported.
   *
   * @var string
   */
  public $smartTapRedemptionValue;
  /**
   * Required. The state of the object. This field is used to determine how an
   * object is displayed in the app. For example, an `inactive` object is moved
   * to the "Expired passes" section.
   *
   * @var string
   */
  public $state;
  protected $textModulesDataType = TextModuleData::class;
  protected $textModulesDataDataType = 'array';
  protected $ticketLegType = TicketLeg::class;
  protected $ticketLegDataType = '';
  protected $ticketLegsType = TicketLeg::class;
  protected $ticketLegsDataType = 'array';
  /**
   * The number of the ticket. This is a unique identifier for the ticket in the
   * transit operator's system.
   *
   * @var string
   */
  public $ticketNumber;
  protected $ticketRestrictionsType = TicketRestrictions::class;
  protected $ticketRestrictionsDataType = '';
  /**
   * The status of the ticket. For states which affect display, use the `state`
   * field instead.
   *
   * @var string
   */
  public $ticketStatus;
  /**
   * This id is used to group tickets together if the user has saved multiple
   * tickets for the same trip.
   *
   * @var string
   */
  public $tripId;
  /**
   * Required. The type of trip this transit object represents. Used to
   * determine the pass title and/or which symbol to use between the origin and
   * destination.
   *
   * @var string
   */
  public $tripType;
  protected $validTimeIntervalType = TimeInterval::class;
  protected $validTimeIntervalDataType = '';
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
   * The activation status for the object. Required if the class has
   * `activationOptions` set.
   *
   * @param ActivationStatus $activationStatus
   */
  public function setActivationStatus(ActivationStatus $activationStatus)
  {
    $this->activationStatus = $activationStatus;
  }
  /**
   * @return ActivationStatus
   */
  public function getActivationStatus()
  {
    return $this->activationStatus;
  }
  /**
   * Optional app or website link that will be displayed as a button on the
   * front of the pass. If AppLinkData is provided for the corresponding class
   * only object AppLinkData will be displayed.
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
   * The barcode type and value.
   *
   * @param Barcode $barcode
   */
  public function setBarcode(Barcode $barcode)
  {
    $this->barcode = $barcode;
  }
  /**
   * @return Barcode
   */
  public function getBarcode()
  {
    return $this->barcode;
  }
  /**
   * Required. The class associated with this object. The class must be of the
   * same type as this object, must already exist, and must be approved. Class
   * IDs should follow the format issuer ID.identifier where the former is
   * issued by Google and latter is chosen by you.
   *
   * @param string $classId
   */
  public function setClassId($classId)
  {
    $this->classId = $classId;
  }
  /**
   * @return string
   */
  public function getClassId()
  {
    return $this->classId;
  }
  /**
   * A copy of the inherited fields of the parent class. These fields are
   * retrieved during a GET.
   *
   * @param TransitClass $classReference
   */
  public function setClassReference(TransitClass $classReference)
  {
    $this->classReference = $classReference;
  }
  /**
   * @return TransitClass
   */
  public function getClassReference()
  {
    return $this->classReference;
  }
  /**
   * The concession category for the ticket.
   *
   * Accepted values: CONCESSION_CATEGORY_UNSPECIFIED, ADULT, adult, CHILD,
   * child, SENIOR, senior
   *
   * @param self::CONCESSION_CATEGORY_* $concessionCategory
   */
  public function setConcessionCategory($concessionCategory)
  {
    $this->concessionCategory = $concessionCategory;
  }
  /**
   * @return self::CONCESSION_CATEGORY_*
   */
  public function getConcessionCategory()
  {
    return $this->concessionCategory;
  }
  /**
   * A custom concession category to use when `concessionCategory` does not
   * provide the right option. Both `concessionCategory` and
   * `customConcessionCategory` may not be set.
   *
   * @param LocalizedString $customConcessionCategory
   */
  public function setCustomConcessionCategory(LocalizedString $customConcessionCategory)
  {
    $this->customConcessionCategory = $customConcessionCategory;
  }
  /**
   * @return LocalizedString
   */
  public function getCustomConcessionCategory()
  {
    return $this->customConcessionCategory;
  }
  /**
   * A custom status to use for the ticket status value when `ticketStatus` does
   * not provide the right option. Both `ticketStatus` and `customTicketStatus`
   * may not be set.
   *
   * @param LocalizedString $customTicketStatus
   */
  public function setCustomTicketStatus(LocalizedString $customTicketStatus)
  {
    $this->customTicketStatus = $customTicketStatus;
  }
  /**
   * @return LocalizedString
   */
  public function getCustomTicketStatus()
  {
    return $this->customTicketStatus;
  }
  /**
   * Device context associated with the object.
   *
   * @param DeviceContext $deviceContext
   */
  public function setDeviceContext(DeviceContext $deviceContext)
  {
    $this->deviceContext = $deviceContext;
  }
  /**
   * @return DeviceContext
   */
  public function getDeviceContext()
  {
    return $this->deviceContext;
  }
  /**
   * Indicates if notifications should explicitly be suppressed. If this field
   * is set to true, regardless of the `messages` field, expiration
   * notifications to the user will be suppressed. By default, this field is set
   * to false. Currently, this can only be set for offers.
   *
   * @param bool $disableExpirationNotification
   */
  public function setDisableExpirationNotification($disableExpirationNotification)
  {
    $this->disableExpirationNotification = $disableExpirationNotification;
  }
  /**
   * @return bool
   */
  public function getDisableExpirationNotification()
  {
    return $this->disableExpirationNotification;
  }
  /**
   * Information that controls how passes are grouped together.
   *
   * @param GroupingInfo $groupingInfo
   */
  public function setGroupingInfo(GroupingInfo $groupingInfo)
  {
    $this->groupingInfo = $groupingInfo;
  }
  /**
   * @return GroupingInfo
   */
  public function getGroupingInfo()
  {
    return $this->groupingInfo;
  }
  /**
   * Whether this object is currently linked to a single device. This field is
   * set by the platform when a user saves the object, linking it to their
   * device. Intended for use by select partners. Contact support for additional
   * information.
   *
   * @param bool $hasLinkedDevice
   */
  public function setHasLinkedDevice($hasLinkedDevice)
  {
    $this->hasLinkedDevice = $hasLinkedDevice;
  }
  /**
   * @return bool
   */
  public function getHasLinkedDevice()
  {
    return $this->hasLinkedDevice;
  }
  /**
   * Indicates if the object has users. This field is set by the platform.
   *
   * @param bool $hasUsers
   */
  public function setHasUsers($hasUsers)
  {
    $this->hasUsers = $hasUsers;
  }
  /**
   * @return bool
   */
  public function getHasUsers()
  {
    return $this->hasUsers;
  }
  /**
   * Optional banner image displayed on the front of the card. If none is
   * present, hero image of the class, if present, will be displayed. If hero
   * image of the class is also not present, nothing will be displayed.
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
   * Required. The unique identifier for an object. This ID must be unique
   * across all objects from an issuer. This value should follow the format
   * issuer ID.identifier where the former is issued by Google and latter is
   * chosen by you. The unique identifier should only include alphanumeric
   * characters, '.', '_', or '-'.
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
   * @param InfoModuleData $infoModuleData
   */
  public function setInfoModuleData(InfoModuleData $infoModuleData)
  {
    $this->infoModuleData = $infoModuleData;
  }
  /**
   * @return InfoModuleData
   */
  public function getInfoModuleData()
  {
    return $this->infoModuleData;
  }
  /**
   * linked_object_ids are a list of other objects such as event ticket,
   * loyalty, offer, generic, giftcard, transit and boarding pass that should be
   * automatically attached to this transit object. If a user had saved this
   * transit card, then these linked_object_ids would be automatically pushed to
   * the user's wallet (unless they turned off the setting to receive such
   * linked passes). Make sure that objects present in linked_object_ids are
   * already inserted - if not, calls would fail. Once linked, the linked
   * objects cannot be unlinked. You cannot link objects belonging to another
   * issuer. There is a limit to the number of objects that can be linked to a
   * single object. After the limit is reached, new linked objects in the call
   * will be ignored silently. Object IDs should follow the format issuer ID.
   * identifier where the former is issued by Google and the latter is chosen by
   * you.
   *
   * @param string[] $linkedObjectIds
   */
  public function setLinkedObjectIds($linkedObjectIds)
  {
    $this->linkedObjectIds = $linkedObjectIds;
  }
  /**
   * @return string[]
   */
  public function getLinkedObjectIds()
  {
    return $this->linkedObjectIds;
  }
  /**
   * Links module data. If links module data is also defined on the class, both
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
   * Merchant locations. There is a maximum of ten on the object. Any additional
   * MerchantLocations added beyond the 10 will be rejected. These locations
   * will trigger a notification when a user enters within a Google-set radius
   * of the point. This field replaces the deprecated LatLongPoints.
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
   * Whether or not field updates to this object should trigger notifications.
   * When set to NOTIFY, we will attempt to trigger a field update notification
   * to users. These notifications will only be sent to users if the field is
   * part of an allowlist. If set to DO_NOT_NOTIFY or
   * NOTIFICATION_SETTINGS_UNSPECIFIED, no notification will be triggered. This
   * setting is ephemeral and needs to be set with each PATCH or UPDATE request,
   * otherwise a notification will not be triggered.
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
   * Pass constraints for the object. Includes limiting NFC and screenshot
   * behaviors.
   *
   * @param PassConstraints $passConstraints
   */
  public function setPassConstraints(PassConstraints $passConstraints)
  {
    $this->passConstraints = $passConstraints;
  }
  /**
   * @return PassConstraints
   */
  public function getPassConstraints()
  {
    return $this->passConstraints;
  }
  /**
   * The name(s) of the passengers the ticket is assigned to. The above
   * `passengerType` field is meant to give Google context on this field.
   *
   * @param string $passengerNames
   */
  public function setPassengerNames($passengerNames)
  {
    $this->passengerNames = $passengerNames;
  }
  /**
   * @return string
   */
  public function getPassengerNames()
  {
    return $this->passengerNames;
  }
  /**
   * The number of passengers.
   *
   * Accepted values: PASSENGER_TYPE_UNSPECIFIED, SINGLE_PASSENGER,
   * singlePassenger, MULTIPLE_PASSENGERS, multiplePassengers
   *
   * @param self::PASSENGER_TYPE_* $passengerType
   */
  public function setPassengerType($passengerType)
  {
    $this->passengerType = $passengerType;
  }
  /**
   * @return self::PASSENGER_TYPE_*
   */
  public function getPassengerType()
  {
    return $this->passengerType;
  }
  /**
   * Purchase details for this ticket.
   *
   * @param PurchaseDetails $purchaseDetails
   */
  public function setPurchaseDetails(PurchaseDetails $purchaseDetails)
  {
    $this->purchaseDetails = $purchaseDetails;
  }
  /**
   * @return PurchaseDetails
   */
  public function getPurchaseDetails()
  {
    return $this->purchaseDetails;
  }
  /**
   * The rotating barcode type and value.
   *
   * @param RotatingBarcode $rotatingBarcode
   */
  public function setRotatingBarcode(RotatingBarcode $rotatingBarcode)
  {
    $this->rotatingBarcode = $rotatingBarcode;
  }
  /**
   * @return RotatingBarcode
   */
  public function getRotatingBarcode()
  {
    return $this->rotatingBarcode;
  }
  /**
   * Restrictions on the object that needs to be verified before the user tries
   * to save the pass. Note that this restrictions will only be applied during
   * save time. If the restrictions changed after a user saves the pass, the new
   * restrictions will not be applied to an already saved pass.
   *
   * @param SaveRestrictions $saveRestrictions
   */
  public function setSaveRestrictions(SaveRestrictions $saveRestrictions)
  {
    $this->saveRestrictions = $saveRestrictions;
  }
  /**
   * @return SaveRestrictions
   */
  public function getSaveRestrictions()
  {
    return $this->saveRestrictions;
  }
  /**
   * The value that will be transmitted to a Smart Tap certified terminal over
   * NFC for this object. The class level fields `enableSmartTap` and
   * `redemptionIssuers` must also be set up correctly in order for the pass to
   * support Smart Tap. Only ASCII characters are supported.
   *
   * @param string $smartTapRedemptionValue
   */
  public function setSmartTapRedemptionValue($smartTapRedemptionValue)
  {
    $this->smartTapRedemptionValue = $smartTapRedemptionValue;
  }
  /**
   * @return string
   */
  public function getSmartTapRedemptionValue()
  {
    return $this->smartTapRedemptionValue;
  }
  /**
   * Required. The state of the object. This field is used to determine how an
   * object is displayed in the app. For example, an `inactive` object is moved
   * to the "Expired passes" section.
   *
   * Accepted values: STATE_UNSPECIFIED, ACTIVE, active, COMPLETED, completed,
   * EXPIRED, expired, INACTIVE, inactive
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
   * A single ticket leg contains departure and arrival information along with
   * boarding and seating information. If more than one leg is to be specified
   * then use the `ticketLegs` field instead. Both `ticketLeg` and `ticketLegs`
   * may not be set.
   *
   * @param TicketLeg $ticketLeg
   */
  public function setTicketLeg(TicketLeg $ticketLeg)
  {
    $this->ticketLeg = $ticketLeg;
  }
  /**
   * @return TicketLeg
   */
  public function getTicketLeg()
  {
    return $this->ticketLeg;
  }
  /**
   * Each ticket may contain one or more legs. Each leg contains departure and
   * arrival information along with boarding and seating information. If only
   * one leg is to be specified then use the `ticketLeg` field instead. Both
   * `ticketLeg` and `ticketLegs` may not be set.
   *
   * @param TicketLeg[] $ticketLegs
   */
  public function setTicketLegs($ticketLegs)
  {
    $this->ticketLegs = $ticketLegs;
  }
  /**
   * @return TicketLeg[]
   */
  public function getTicketLegs()
  {
    return $this->ticketLegs;
  }
  /**
   * The number of the ticket. This is a unique identifier for the ticket in the
   * transit operator's system.
   *
   * @param string $ticketNumber
   */
  public function setTicketNumber($ticketNumber)
  {
    $this->ticketNumber = $ticketNumber;
  }
  /**
   * @return string
   */
  public function getTicketNumber()
  {
    return $this->ticketNumber;
  }
  /**
   * Information about what kind of restrictions there are on using this ticket.
   * For example, which days of the week it must be used, or which routes are
   * allowed to be taken.
   *
   * @param TicketRestrictions $ticketRestrictions
   */
  public function setTicketRestrictions(TicketRestrictions $ticketRestrictions)
  {
    $this->ticketRestrictions = $ticketRestrictions;
  }
  /**
   * @return TicketRestrictions
   */
  public function getTicketRestrictions()
  {
    return $this->ticketRestrictions;
  }
  /**
   * The status of the ticket. For states which affect display, use the `state`
   * field instead.
   *
   * Accepted values: TICKET_STATUS_UNSPECIFIED, USED, used, REFUNDED, refunded,
   * EXCHANGED, exchanged
   *
   * @param self::TICKET_STATUS_* $ticketStatus
   */
  public function setTicketStatus($ticketStatus)
  {
    $this->ticketStatus = $ticketStatus;
  }
  /**
   * @return self::TICKET_STATUS_*
   */
  public function getTicketStatus()
  {
    return $this->ticketStatus;
  }
  /**
   * This id is used to group tickets together if the user has saved multiple
   * tickets for the same trip.
   *
   * @param string $tripId
   */
  public function setTripId($tripId)
  {
    $this->tripId = $tripId;
  }
  /**
   * @return string
   */
  public function getTripId()
  {
    return $this->tripId;
  }
  /**
   * Required. The type of trip this transit object represents. Used to
   * determine the pass title and/or which symbol to use between the origin and
   * destination.
   *
   * Accepted values: TRIP_TYPE_UNSPECIFIED, ROUND_TRIP, roundTrip, ONE_WAY,
   * oneWay
   *
   * @param self::TRIP_TYPE_* $tripType
   */
  public function setTripType($tripType)
  {
    $this->tripType = $tripType;
  }
  /**
   * @return self::TRIP_TYPE_*
   */
  public function getTripType()
  {
    return $this->tripType;
  }
  /**
   * The time period this object will be `active` and object can be used. An
   * object's state will be changed to `expired` when this time period has
   * passed.
   *
   * @param TimeInterval $validTimeInterval
   */
  public function setValidTimeInterval(TimeInterval $validTimeInterval)
  {
    $this->validTimeInterval = $validTimeInterval;
  }
  /**
   * @return TimeInterval
   */
  public function getValidTimeInterval()
  {
    return $this->validTimeInterval;
  }
  /**
   * Optional value added module data. Maximum of ten on the object.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TransitObject::class, 'Google_Service_Walletobjects_TransitObject');
