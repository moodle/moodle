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

class LoyaltyObject extends \Google\Collection
{
  /**
   * Default behavior is no notifications sent.
   */
  public const NOTIFY_PREFERENCE_NOTIFICATION_SETTINGS_FOR_UPDATES_UNSPECIFIED = 'NOTIFICATION_SETTINGS_FOR_UPDATES_UNSPECIFIED';
  /**
   * This value will result in a notification being sent, if the updated fields
   * are part of an allowlist.
   */
  public const NOTIFY_PREFERENCE_NOTIFY_ON_UPDATE = 'NOTIFY_ON_UPDATE';
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
  protected $collection_key = 'valueAddedModuleData';
  /**
   * The loyalty account identifier. Recommended maximum length is 20
   * characters.
   *
   * @var string
   */
  public $accountId;
  /**
   * The loyalty account holder name, such as "John Smith." Recommended maximum
   * length is 20 characters to ensure full string is displayed on smaller
   * screens.
   *
   * @var string
   */
  public $accountName;
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
  protected $classReferenceType = LoyaltyClass::class;
  protected $classReferenceDataType = '';
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
   * Identifies what kind of resource this is. Value: the fixed string
   * `"walletobjects#loyaltyObject"`.
   *
   * @deprecated
   * @var string
   */
  public $kind;
  /**
   * linked_object_ids are a list of other objects such as event ticket,
   * loyalty, offer, generic, giftcard, transit and boarding pass that should be
   * automatically attached to this loyalty object. If a user had saved this
   * loyalty card, then these linked_object_ids would be automatically pushed to
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
  /**
   * A list of offer objects linked to this loyalty card. The offer objects must
   * already exist. Offer object IDs should follow the format issuer ID.
   * identifier where the former is issued by Google and latter is chosen by
   * you.
   *
   * @var string[]
   */
  public $linkedOfferIds;
  protected $linksModuleDataType = LinksModuleData::class;
  protected $linksModuleDataDataType = '';
  protected $locationsType = LatLongPoint::class;
  protected $locationsDataType = 'array';
  protected $loyaltyPointsType = LoyaltyPoints::class;
  protected $loyaltyPointsDataType = '';
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
  protected $rotatingBarcodeType = RotatingBarcode::class;
  protected $rotatingBarcodeDataType = '';
  protected $saveRestrictionsType = SaveRestrictions::class;
  protected $saveRestrictionsDataType = '';
  protected $secondaryLoyaltyPointsType = LoyaltyPoints::class;
  protected $secondaryLoyaltyPointsDataType = '';
  /**
   * The value that will be transmitted to a Smart Tap certified terminal over
   * NFC for this object. The class level fields `enableSmartTap` and
   * `redemptionIssuers` must also be set up correctly in order for the pass to
   * support Smart Tap. Only ASCII characters are supported. If this value is
   * not set but the class level fields `enableSmartTap` and `redemptionIssuers`
   * are set up correctly, the `barcode.value` or the `accountId` fields are
   * used as fallback if present.
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
   * The loyalty account identifier. Recommended maximum length is 20
   * characters.
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
   * The loyalty account holder name, such as "John Smith." Recommended maximum
   * length is 20 characters to ensure full string is displayed on smaller
   * screens.
   *
   * @param string $accountName
   */
  public function setAccountName($accountName)
  {
    $this->accountName = $accountName;
  }
  /**
   * @return string
   */
  public function getAccountName()
  {
    return $this->accountName;
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
   * @param LoyaltyClass $classReference
   */
  public function setClassReference(LoyaltyClass $classReference)
  {
    $this->classReference = $classReference;
  }
  /**
   * @return LoyaltyClass
   */
  public function getClassReference()
  {
    return $this->classReference;
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
   * Identifies what kind of resource this is. Value: the fixed string
   * `"walletobjects#loyaltyObject"`.
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
   * linked_object_ids are a list of other objects such as event ticket,
   * loyalty, offer, generic, giftcard, transit and boarding pass that should be
   * automatically attached to this loyalty object. If a user had saved this
   * loyalty card, then these linked_object_ids would be automatically pushed to
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
   * A list of offer objects linked to this loyalty card. The offer objects must
   * already exist. Offer object IDs should follow the format issuer ID.
   * identifier where the former is issued by Google and latter is chosen by
   * you.
   *
   * @param string[] $linkedOfferIds
   */
  public function setLinkedOfferIds($linkedOfferIds)
  {
    $this->linkedOfferIds = $linkedOfferIds;
  }
  /**
   * @return string[]
   */
  public function getLinkedOfferIds()
  {
    return $this->linkedOfferIds;
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
   * The loyalty reward points label, balance, and type.
   *
   * @param LoyaltyPoints $loyaltyPoints
   */
  public function setLoyaltyPoints(LoyaltyPoints $loyaltyPoints)
  {
    $this->loyaltyPoints = $loyaltyPoints;
  }
  /**
   * @return LoyaltyPoints
   */
  public function getLoyaltyPoints()
  {
    return $this->loyaltyPoints;
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
   * The secondary loyalty reward points label, balance, and type. Shown in
   * addition to the primary loyalty points.
   *
   * @param LoyaltyPoints $secondaryLoyaltyPoints
   */
  public function setSecondaryLoyaltyPoints(LoyaltyPoints $secondaryLoyaltyPoints)
  {
    $this->secondaryLoyaltyPoints = $secondaryLoyaltyPoints;
  }
  /**
   * @return LoyaltyPoints
   */
  public function getSecondaryLoyaltyPoints()
  {
    return $this->secondaryLoyaltyPoints;
  }
  /**
   * The value that will be transmitted to a Smart Tap certified terminal over
   * NFC for this object. The class level fields `enableSmartTap` and
   * `redemptionIssuers` must also be set up correctly in order for the pass to
   * support Smart Tap. Only ASCII characters are supported. If this value is
   * not set but the class level fields `enableSmartTap` and `redemptionIssuers`
   * are set up correctly, the `barcode.value` or the `accountId` fields are
   * used as fallback if present.
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
class_alias(LoyaltyObject::class, 'Google_Service_Walletobjects_LoyaltyObject');
