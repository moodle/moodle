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

class GenericObject extends \Google\Collection
{
  /**
   * Unspecified generic type.
   */
  public const GENERIC_TYPE_GENERIC_TYPE_UNSPECIFIED = 'GENERIC_TYPE_UNSPECIFIED';
  /**
   * Season pass
   */
  public const GENERIC_TYPE_GENERIC_SEASON_PASS = 'GENERIC_SEASON_PASS';
  /**
   * Utility bills
   */
  public const GENERIC_TYPE_GENERIC_UTILITY_BILLS = 'GENERIC_UTILITY_BILLS';
  /**
   * Parking pass
   */
  public const GENERIC_TYPE_GENERIC_PARKING_PASS = 'GENERIC_PARKING_PASS';
  /**
   * Voucher
   */
  public const GENERIC_TYPE_GENERIC_VOUCHER = 'GENERIC_VOUCHER';
  /**
   * Gym membership cards
   */
  public const GENERIC_TYPE_GENERIC_GYM_MEMBERSHIP = 'GENERIC_GYM_MEMBERSHIP';
  /**
   * Library membership cards
   */
  public const GENERIC_TYPE_GENERIC_LIBRARY_MEMBERSHIP = 'GENERIC_LIBRARY_MEMBERSHIP';
  /**
   * Reservations
   */
  public const GENERIC_TYPE_GENERIC_RESERVATIONS = 'GENERIC_RESERVATIONS';
  /**
   * Auto-insurance cards
   */
  public const GENERIC_TYPE_GENERIC_AUTO_INSURANCE = 'GENERIC_AUTO_INSURANCE';
  /**
   * Home-insurance cards
   */
  public const GENERIC_TYPE_GENERIC_HOME_INSURANCE = 'GENERIC_HOME_INSURANCE';
  /**
   * Entry tickets
   */
  public const GENERIC_TYPE_GENERIC_ENTRY_TICKET = 'GENERIC_ENTRY_TICKET';
  /**
   * Receipts
   */
  public const GENERIC_TYPE_GENERIC_RECEIPT = 'GENERIC_RECEIPT';
  /**
   * Loyalty cards. Please note that it is advisable to use a dedicated Loyalty
   * card pass type instead of this generic type. A dedicated loyalty card pass
   * type offers more features and functionality than a generic pass type.
   */
  public const GENERIC_TYPE_GENERIC_LOYALTY_CARD = 'GENERIC_LOYALTY_CARD';
  /**
   * Other type
   */
  public const GENERIC_TYPE_GENERIC_OTHER = 'GENERIC_OTHER';
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
  protected $appLinkDataType = AppLinkData::class;
  protected $appLinkDataDataType = '';
  protected $barcodeType = Barcode::class;
  protected $barcodeDataType = '';
  protected $cardTitleType = LocalizedString::class;
  protected $cardTitleDataType = '';
  /**
   * Required. The class associated with this object. The class must be of the
   * same type as this object, must already exist, and must be approved. Class
   * IDs should follow the format `issuerID.identifier` where `issuerID` is
   * issued by Google and `identifier` is chosen by you.
   *
   * @var string
   */
  public $classId;
  /**
   * Specify which `GenericType` the card belongs to.
   *
   * @var string
   */
  public $genericType;
  protected $groupingInfoType = GroupingInfo::class;
  protected $groupingInfoDataType = '';
  /**
   * Indicates if the object has users. This field is set by the platform.
   *
   * @var bool
   */
  public $hasUsers;
  protected $headerType = LocalizedString::class;
  protected $headerDataType = '';
  protected $heroImageType = Image::class;
  protected $heroImageDataType = '';
  /**
   * The background color for the card. If not set, the dominant color of the
   * hero image is used, and if no hero image is set, the dominant color of the
   * logo is used and if logo is not set, a color would be chosen by Google.
   *
   * @var string
   */
  public $hexBackgroundColor;
  /**
   * Required. The unique identifier for an object. This ID must be unique
   * across all objects from an issuer. This value needs to follow the format
   * `issuerID.identifier` where `issuerID` is issued by Google and `identifier`
   * is chosen by you. The unique identifier can only include alphanumeric
   * characters, `.`, `_`, or `-`.
   *
   * @var string
   */
  public $id;
  protected $imageModulesDataType = ImageModuleData::class;
  protected $imageModulesDataDataType = 'array';
  /**
   * linked_object_ids are a list of other objects such as event ticket,
   * loyalty, offer, generic, giftcard, transit and boarding pass that should be
   * automatically attached to this generic object. If a user had saved this
   * generic card, then these linked_object_ids would be automatically pushed to
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
  protected $logoType = Image::class;
  protected $logoDataType = '';
  protected $merchantLocationsType = MerchantLocation::class;
  protected $merchantLocationsDataType = 'array';
  protected $messagesType = Message::class;
  protected $messagesDataType = 'array';
  protected $notificationsType = Notifications::class;
  protected $notificationsDataType = '';
  protected $passConstraintsType = PassConstraints::class;
  protected $passConstraintsDataType = '';
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
   * The state of the object. This field is used to determine how an object is
   * displayed in the app. For example, an `inactive` object is moved to the
   * "Expired passes" section. If this is not provided, the object would be
   * considered `ACTIVE`.
   *
   * @var string
   */
  public $state;
  protected $subheaderType = LocalizedString::class;
  protected $subheaderDataType = '';
  protected $textModulesDataType = TextModuleData::class;
  protected $textModulesDataDataType = 'array';
  protected $validTimeIntervalType = TimeInterval::class;
  protected $validTimeIntervalDataType = '';
  protected $valueAddedModuleDataType = ValueAddedModuleData::class;
  protected $valueAddedModuleDataDataType = 'array';
  protected $wideLogoType = Image::class;
  protected $wideLogoDataType = '';

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
   * The barcode type and value. If pass does not have a barcode, we can allow
   * the issuer to set Barcode.alternate_text and display just that.
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
   * Required. The header of the pass. This is usually the Business name such as
   * "XXX Gym", "AAA Insurance". This field is required and appears in the
   * header row at the very top of the pass.
   *
   * @param LocalizedString $cardTitle
   */
  public function setCardTitle(LocalizedString $cardTitle)
  {
    $this->cardTitle = $cardTitle;
  }
  /**
   * @return LocalizedString
   */
  public function getCardTitle()
  {
    return $this->cardTitle;
  }
  /**
   * Required. The class associated with this object. The class must be of the
   * same type as this object, must already exist, and must be approved. Class
   * IDs should follow the format `issuerID.identifier` where `issuerID` is
   * issued by Google and `identifier` is chosen by you.
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
   * Specify which `GenericType` the card belongs to.
   *
   * Accepted values: GENERIC_TYPE_UNSPECIFIED, GENERIC_SEASON_PASS,
   * GENERIC_UTILITY_BILLS, GENERIC_PARKING_PASS, GENERIC_VOUCHER,
   * GENERIC_GYM_MEMBERSHIP, GENERIC_LIBRARY_MEMBERSHIP, GENERIC_RESERVATIONS,
   * GENERIC_AUTO_INSURANCE, GENERIC_HOME_INSURANCE, GENERIC_ENTRY_TICKET,
   * GENERIC_RECEIPT, GENERIC_LOYALTY_CARD, GENERIC_OTHER
   *
   * @param self::GENERIC_TYPE_* $genericType
   */
  public function setGenericType($genericType)
  {
    $this->genericType = $genericType;
  }
  /**
   * @return self::GENERIC_TYPE_*
   */
  public function getGenericType()
  {
    return $this->genericType;
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
   * Required. The title of the pass, such as "50% off coupon" or "Library card"
   * or "Voucher". This field is required and appears in the title row of the
   * pass detail view.
   *
   * @param LocalizedString $header
   */
  public function setHeader(LocalizedString $header)
  {
    $this->header = $header;
  }
  /**
   * @return LocalizedString
   */
  public function getHeader()
  {
    return $this->header;
  }
  /**
   * Banner image displayed on the front of the card if present. The image will
   * be displayed at 100% width.
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
   * The background color for the card. If not set, the dominant color of the
   * hero image is used, and if no hero image is set, the dominant color of the
   * logo is used and if logo is not set, a color would be chosen by Google.
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
   * across all objects from an issuer. This value needs to follow the format
   * `issuerID.identifier` where `issuerID` is issued by Google and `identifier`
   * is chosen by you. The unique identifier can only include alphanumeric
   * characters, `.`, `_`, or `-`.
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
   * Image module data. Only one of the image from class and one from object
   * level will be rendered when both set.
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
   * linked_object_ids are a list of other objects such as event ticket,
   * loyalty, offer, generic, giftcard, transit and boarding pass that should be
   * automatically attached to this generic object. If a user had saved this
   * generic card, then these linked_object_ids would be automatically pushed to
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
   * Links module data. If `linksModuleData` is also defined on the class, both
   * will be displayed. The maximum number of these fields displayed is 10 from
   * class and 10 from object.
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
   * The logo image of the pass. This image is displayed in the card detail view
   * in upper left, and also on the list/thumbnail view. If the logo is not
   * present, the first letter of `cardTitle` would be shown as logo.
   *
   * @param Image $logo
   */
  public function setLogo(Image $logo)
  {
    $this->logo = $logo;
  }
  /**
   * @return Image
   */
  public function getLogo()
  {
    return $this->logo;
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
   * The notification settings that are enabled for this object.
   *
   * @param Notifications $notifications
   */
  public function setNotifications(Notifications $notifications)
  {
    $this->notifications = $notifications;
  }
  /**
   * @return Notifications
   */
  public function getNotifications()
  {
    return $this->notifications;
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
   * The rotating barcode settings/details.
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
   * The state of the object. This field is used to determine how an object is
   * displayed in the app. For example, an `inactive` object is moved to the
   * "Expired passes" section. If this is not provided, the object would be
   * considered `ACTIVE`.
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
   * The title label of the pass, such as location where this pass can be used.
   * Appears right above the title in the title row in the pass detail view.
   *
   * @param LocalizedString $subheader
   */
  public function setSubheader(LocalizedString $subheader)
  {
    $this->subheader = $subheader;
  }
  /**
   * @return LocalizedString
   */
  public function getSubheader()
  {
    return $this->subheader;
  }
  /**
   * Text module data. If `textModulesData` is also defined on the class, both
   * will be displayed. The maximum number of these fields displayed is 10 from
   * class and 10 from object.
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
   * The time period this object will be considered valid or usable. When the
   * time period is passed, the object will be considered expired, which will
   * affect the rendering on user's devices.
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
   * The wide logo of the pass. When provided, this will be used in place of the
   * logo in the top left of the card view.
   *
   * @param Image $wideLogo
   */
  public function setWideLogo(Image $wideLogo)
  {
    $this->wideLogo = $wideLogo;
  }
  /**
   * @return Image
   */
  public function getWideLogo()
  {
    return $this->wideLogo;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GenericObject::class, 'Google_Service_Walletobjects_GenericObject');
