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

class EventTicketClass extends \Google\Collection
{
  public const CONFIRMATION_CODE_LABEL_CONFIRMATION_CODE_LABEL_UNSPECIFIED = 'CONFIRMATION_CODE_LABEL_UNSPECIFIED';
  public const CONFIRMATION_CODE_LABEL_CONFIRMATION_CODE = 'CONFIRMATION_CODE';
  /**
   * Legacy alias for `CONFIRMATION_CODE`. Deprecated.
   *
   * @deprecated
   */
  public const CONFIRMATION_CODE_LABEL_confirmationCode = 'confirmationCode';
  public const CONFIRMATION_CODE_LABEL_CONFIRMATION_NUMBER = 'CONFIRMATION_NUMBER';
  /**
   * Legacy alias for `CONFIRMATION_NUMBER`. Deprecated.
   *
   * @deprecated
   */
  public const CONFIRMATION_CODE_LABEL_confirmationNumber = 'confirmationNumber';
  public const CONFIRMATION_CODE_LABEL_ORDER_NUMBER = 'ORDER_NUMBER';
  /**
   * Legacy alias for `ORDER_NUMBER`. Deprecated.
   *
   * @deprecated
   */
  public const CONFIRMATION_CODE_LABEL_orderNumber = 'orderNumber';
  public const CONFIRMATION_CODE_LABEL_RESERVATION_NUMBER = 'RESERVATION_NUMBER';
  /**
   * Legacy alias for `RESERVATION_NUMBER`. Deprecated.
   *
   * @deprecated
   */
  public const CONFIRMATION_CODE_LABEL_reservationNumber = 'reservationNumber';
  public const GATE_LABEL_GATE_LABEL_UNSPECIFIED = 'GATE_LABEL_UNSPECIFIED';
  public const GATE_LABEL_GATE = 'GATE';
  /**
   * Legacy alias for `GATE`. Deprecated.
   *
   * @deprecated
   */
  public const GATE_LABEL_gate = 'gate';
  public const GATE_LABEL_DOOR = 'DOOR';
  /**
   * Legacy alias for `DOOR`. Deprecated.
   *
   * @deprecated
   */
  public const GATE_LABEL_door = 'door';
  public const GATE_LABEL_ENTRANCE = 'ENTRANCE';
  /**
   * Legacy alias for `ENTRANCE`. Deprecated.
   *
   * @deprecated
   */
  public const GATE_LABEL_entrance = 'entrance';
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
  public const ROW_LABEL_ROW_LABEL_UNSPECIFIED = 'ROW_LABEL_UNSPECIFIED';
  public const ROW_LABEL_ROW = 'ROW';
  /**
   * Legacy alias for `ROW`. Deprecated.
   *
   * @deprecated
   */
  public const ROW_LABEL_row = 'row';
  public const SEAT_LABEL_SEAT_LABEL_UNSPECIFIED = 'SEAT_LABEL_UNSPECIFIED';
  public const SEAT_LABEL_SEAT = 'SEAT';
  /**
   * Legacy alias for `SEAT`. Deprecated.
   *
   * @deprecated
   */
  public const SEAT_LABEL_seat = 'seat';
  public const SECTION_LABEL_SECTION_LABEL_UNSPECIFIED = 'SECTION_LABEL_UNSPECIFIED';
  public const SECTION_LABEL_SECTION = 'SECTION';
  /**
   * Legacy alias for `SECTION`. Deprecated.
   *
   * @deprecated
   */
  public const SECTION_LABEL_section = 'section';
  public const SECTION_LABEL_THEATER = 'THEATER';
  /**
   * Legacy alias for `THEATER`. Deprecated.
   *
   * @deprecated
   */
  public const SECTION_LABEL_theater = 'theater';
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
  protected $callbackOptionsType = CallbackOptions::class;
  protected $callbackOptionsDataType = '';
  protected $classTemplateInfoType = ClassTemplateInfo::class;
  protected $classTemplateInfoDataType = '';
  /**
   * The label to use for the confirmation code value
   * (`eventTicketObject.reservationInfo.confirmationCode`) on the card detail
   * view. Each available option maps to a set of localized strings, so that
   * translations are shown to the user based on their locale. Both
   * `confirmationCodeLabel` and `customConfirmationCodeLabel` may not be set.
   * If neither is set, the label will default to "Confirmation Code",
   * localized. If the confirmation code field is unset, this label will not be
   * used.
   *
   * @var string
   */
  public $confirmationCodeLabel;
  /**
   * Country code used to display the card's country (when the user is not in
   * that country), as well as to display localized content when content is not
   * available in the user's locale.
   *
   * @var string
   */
  public $countryCode;
  protected $customConfirmationCodeLabelType = LocalizedString::class;
  protected $customConfirmationCodeLabelDataType = '';
  protected $customGateLabelType = LocalizedString::class;
  protected $customGateLabelDataType = '';
  protected $customRowLabelType = LocalizedString::class;
  protected $customRowLabelDataType = '';
  protected $customSeatLabelType = LocalizedString::class;
  protected $customSeatLabelDataType = '';
  protected $customSectionLabelType = LocalizedString::class;
  protected $customSectionLabelDataType = '';
  protected $dateTimeType = EventDateTime::class;
  protected $dateTimeDataType = '';
  /**
   * Identifies whether this class supports Smart Tap. The `redemptionIssuers`
   * and object level `smartTapRedemptionLevel` fields must also be set up
   * correctly in order for a pass to support Smart Tap.
   *
   * @var bool
   */
  public $enableSmartTap;
  /**
   * The ID of the event. This ID should be unique for every event in an
   * account. It is used to group tickets together if the user has saved
   * multiple tickets for the same event. It can be at most 64 characters. If
   * provided, the grouping will be stable. Be wary of unintentional collision
   * to avoid grouping tickets that should not be grouped. If you use only one
   * class per event, you can simply set this to the `classId` (with or without
   * the issuer ID portion). If not provided, the platform will attempt to use
   * other data to group tickets (potentially unstable).
   *
   * @var string
   */
  public $eventId;
  protected $eventNameType = LocalizedString::class;
  protected $eventNameDataType = '';
  protected $finePrintType = LocalizedString::class;
  protected $finePrintDataType = '';
  /**
   * The label to use for the gate value (`eventTicketObject.seatInfo.gate`) on
   * the card detail view. Each available option maps to a set of localized
   * strings, so that translations are shown to the user based on their locale.
   * Both `gateLabel` and `customGateLabel` may not be set. If neither is set,
   * the label will default to "Gate", localized. If the gate field is unset,
   * this label will not be used.
   *
   * @var string
   */
  public $gateLabel;
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
   * `"walletobjects#eventTicketClass"`.
   *
   * @deprecated
   * @var string
   */
  public $kind;
  protected $linksModuleDataType = LinksModuleData::class;
  protected $linksModuleDataDataType = '';
  protected $localizedIssuerNameType = LocalizedString::class;
  protected $localizedIssuerNameDataType = '';
  protected $locationsType = LatLongPoint::class;
  protected $locationsDataType = 'array';
  protected $logoType = Image::class;
  protected $logoDataType = '';
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
  /**
   * The label to use for the row value (`eventTicketObject.seatInfo.row`) on
   * the card detail view. Each available option maps to a set of localized
   * strings, so that translations are shown to the user based on their locale.
   * Both `rowLabel` and `customRowLabel` may not be set. If neither is set, the
   * label will default to "Row", localized. If the row field is unset, this
   * label will not be used.
   *
   * @var string
   */
  public $rowLabel;
  /**
   * The label to use for the seat value (`eventTicketObject.seatInfo.seat`) on
   * the card detail view. Each available option maps to a set of localized
   * strings, so that translations are shown to the user based on their locale.
   * Both `seatLabel` and `customSeatLabel` may not be set. If neither is set,
   * the label will default to "Seat", localized. If the seat field is unset,
   * this label will not be used.
   *
   * @var string
   */
  public $seatLabel;
  /**
   * The label to use for the section value
   * (`eventTicketObject.seatInfo.section`) on the card detail view. Each
   * available option maps to a set of localized strings, so that translations
   * are shown to the user based on their locale. Both `sectionLabel` and
   * `customSectionLabel` may not be set. If neither is set, the label will
   * default to "Section", localized. If the section field is unset, this label
   * will not be used.
   *
   * @var string
   */
  public $sectionLabel;
  protected $securityAnimationType = SecurityAnimation::class;
  protected $securityAnimationDataType = '';
  protected $textModulesDataType = TextModuleData::class;
  protected $textModulesDataDataType = 'array';
  protected $valueAddedModuleDataType = ValueAddedModuleData::class;
  protected $valueAddedModuleDataDataType = 'array';
  protected $venueType = EventVenue::class;
  protected $venueDataType = '';
  /**
   * Deprecated
   *
   * @deprecated
   * @var string
   */
  public $version;
  /**
   * View Unlock Requirement options for the event ticket.
   *
   * @var string
   */
  public $viewUnlockRequirement;
  protected $wideLogoType = Image::class;
  protected $wideLogoDataType = '';
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
   * The label to use for the confirmation code value
   * (`eventTicketObject.reservationInfo.confirmationCode`) on the card detail
   * view. Each available option maps to a set of localized strings, so that
   * translations are shown to the user based on their locale. Both
   * `confirmationCodeLabel` and `customConfirmationCodeLabel` may not be set.
   * If neither is set, the label will default to "Confirmation Code",
   * localized. If the confirmation code field is unset, this label will not be
   * used.
   *
   * Accepted values: CONFIRMATION_CODE_LABEL_UNSPECIFIED, CONFIRMATION_CODE,
   * confirmationCode, CONFIRMATION_NUMBER, confirmationNumber, ORDER_NUMBER,
   * orderNumber, RESERVATION_NUMBER, reservationNumber
   *
   * @param self::CONFIRMATION_CODE_LABEL_* $confirmationCodeLabel
   */
  public function setConfirmationCodeLabel($confirmationCodeLabel)
  {
    $this->confirmationCodeLabel = $confirmationCodeLabel;
  }
  /**
   * @return self::CONFIRMATION_CODE_LABEL_*
   */
  public function getConfirmationCodeLabel()
  {
    return $this->confirmationCodeLabel;
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
   * A custom label to use for the confirmation code value
   * (`eventTicketObject.reservationInfo.confirmationCode`) on the card detail
   * view. This should only be used if the default "Confirmation Code" label or
   * one of the `confirmationCodeLabel` options is not sufficient. Both
   * `confirmationCodeLabel` and `customConfirmationCodeLabel` may not be set.
   * If neither is set, the label will default to "Confirmation Code",
   * localized. If the confirmation code field is unset, this label will not be
   * used.
   *
   * @param LocalizedString $customConfirmationCodeLabel
   */
  public function setCustomConfirmationCodeLabel(LocalizedString $customConfirmationCodeLabel)
  {
    $this->customConfirmationCodeLabel = $customConfirmationCodeLabel;
  }
  /**
   * @return LocalizedString
   */
  public function getCustomConfirmationCodeLabel()
  {
    return $this->customConfirmationCodeLabel;
  }
  /**
   * A custom label to use for the gate value
   * (`eventTicketObject.seatInfo.gate`) on the card detail view. This should
   * only be used if the default "Gate" label or one of the `gateLabel` options
   * is not sufficient. Both `gateLabel` and `customGateLabel` may not be set.
   * If neither is set, the label will default to "Gate", localized. If the gate
   * field is unset, this label will not be used.
   *
   * @param LocalizedString $customGateLabel
   */
  public function setCustomGateLabel(LocalizedString $customGateLabel)
  {
    $this->customGateLabel = $customGateLabel;
  }
  /**
   * @return LocalizedString
   */
  public function getCustomGateLabel()
  {
    return $this->customGateLabel;
  }
  /**
   * A custom label to use for the row value (`eventTicketObject.seatInfo.row`)
   * on the card detail view. This should only be used if the default "Row"
   * label or one of the `rowLabel` options is not sufficient. Both `rowLabel`
   * and `customRowLabel` may not be set. If neither is set, the label will
   * default to "Row", localized. If the row field is unset, this label will not
   * be used.
   *
   * @param LocalizedString $customRowLabel
   */
  public function setCustomRowLabel(LocalizedString $customRowLabel)
  {
    $this->customRowLabel = $customRowLabel;
  }
  /**
   * @return LocalizedString
   */
  public function getCustomRowLabel()
  {
    return $this->customRowLabel;
  }
  /**
   * A custom label to use for the seat value
   * (`eventTicketObject.seatInfo.seat`) on the card detail view. This should
   * only be used if the default "Seat" label or one of the `seatLabel` options
   * is not sufficient. Both `seatLabel` and `customSeatLabel` may not be set.
   * If neither is set, the label will default to "Seat", localized. If the seat
   * field is unset, this label will not be used.
   *
   * @param LocalizedString $customSeatLabel
   */
  public function setCustomSeatLabel(LocalizedString $customSeatLabel)
  {
    $this->customSeatLabel = $customSeatLabel;
  }
  /**
   * @return LocalizedString
   */
  public function getCustomSeatLabel()
  {
    return $this->customSeatLabel;
  }
  /**
   * A custom label to use for the section value
   * (`eventTicketObject.seatInfo.section`) on the card detail view. This should
   * only be used if the default "Section" label or one of the `sectionLabel`
   * options is not sufficient. Both `sectionLabel` and `customSectionLabel` may
   * not be set. If neither is set, the label will default to "Section",
   * localized. If the section field is unset, this label will not be used.
   *
   * @param LocalizedString $customSectionLabel
   */
  public function setCustomSectionLabel(LocalizedString $customSectionLabel)
  {
    $this->customSectionLabel = $customSectionLabel;
  }
  /**
   * @return LocalizedString
   */
  public function getCustomSectionLabel()
  {
    return $this->customSectionLabel;
  }
  /**
   * The date & time information of the event.
   *
   * @param EventDateTime $dateTime
   */
  public function setDateTime(EventDateTime $dateTime)
  {
    $this->dateTime = $dateTime;
  }
  /**
   * @return EventDateTime
   */
  public function getDateTime()
  {
    return $this->dateTime;
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
   * The ID of the event. This ID should be unique for every event in an
   * account. It is used to group tickets together if the user has saved
   * multiple tickets for the same event. It can be at most 64 characters. If
   * provided, the grouping will be stable. Be wary of unintentional collision
   * to avoid grouping tickets that should not be grouped. If you use only one
   * class per event, you can simply set this to the `classId` (with or without
   * the issuer ID portion). If not provided, the platform will attempt to use
   * other data to group tickets (potentially unstable).
   *
   * @param string $eventId
   */
  public function setEventId($eventId)
  {
    $this->eventId = $eventId;
  }
  /**
   * @return string
   */
  public function getEventId()
  {
    return $this->eventId;
  }
  /**
   * Required. The name of the event, such as "LA Dodgers at SF Giants".
   *
   * @param LocalizedString $eventName
   */
  public function setEventName(LocalizedString $eventName)
  {
    $this->eventName = $eventName;
  }
  /**
   * @return LocalizedString
   */
  public function getEventName()
  {
    return $this->eventName;
  }
  /**
   * The fine print, terms, or conditions of the ticket.
   *
   * @param LocalizedString $finePrint
   */
  public function setFinePrint(LocalizedString $finePrint)
  {
    $this->finePrint = $finePrint;
  }
  /**
   * @return LocalizedString
   */
  public function getFinePrint()
  {
    return $this->finePrint;
  }
  /**
   * The label to use for the gate value (`eventTicketObject.seatInfo.gate`) on
   * the card detail view. Each available option maps to a set of localized
   * strings, so that translations are shown to the user based on their locale.
   * Both `gateLabel` and `customGateLabel` may not be set. If neither is set,
   * the label will default to "Gate", localized. If the gate field is unset,
   * this label will not be used.
   *
   * Accepted values: GATE_LABEL_UNSPECIFIED, GATE, gate, DOOR, door, ENTRANCE,
   * entrance
   *
   * @param self::GATE_LABEL_* $gateLabel
   */
  public function setGateLabel($gateLabel)
  {
    $this->gateLabel = $gateLabel;
  }
  /**
   * @return self::GATE_LABEL_*
   */
  public function getGateLabel()
  {
    return $this->gateLabel;
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
   * `"walletobjects#eventTicketClass"`.
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
   * The logo image of the ticket. This image is displayed in the card detail
   * view of the app.
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
   * Merchant locations. There is a maximum of ten on the class. Any additional
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
   * The label to use for the row value (`eventTicketObject.seatInfo.row`) on
   * the card detail view. Each available option maps to a set of localized
   * strings, so that translations are shown to the user based on their locale.
   * Both `rowLabel` and `customRowLabel` may not be set. If neither is set, the
   * label will default to "Row", localized. If the row field is unset, this
   * label will not be used.
   *
   * Accepted values: ROW_LABEL_UNSPECIFIED, ROW, row
   *
   * @param self::ROW_LABEL_* $rowLabel
   */
  public function setRowLabel($rowLabel)
  {
    $this->rowLabel = $rowLabel;
  }
  /**
   * @return self::ROW_LABEL_*
   */
  public function getRowLabel()
  {
    return $this->rowLabel;
  }
  /**
   * The label to use for the seat value (`eventTicketObject.seatInfo.seat`) on
   * the card detail view. Each available option maps to a set of localized
   * strings, so that translations are shown to the user based on their locale.
   * Both `seatLabel` and `customSeatLabel` may not be set. If neither is set,
   * the label will default to "Seat", localized. If the seat field is unset,
   * this label will not be used.
   *
   * Accepted values: SEAT_LABEL_UNSPECIFIED, SEAT, seat
   *
   * @param self::SEAT_LABEL_* $seatLabel
   */
  public function setSeatLabel($seatLabel)
  {
    $this->seatLabel = $seatLabel;
  }
  /**
   * @return self::SEAT_LABEL_*
   */
  public function getSeatLabel()
  {
    return $this->seatLabel;
  }
  /**
   * The label to use for the section value
   * (`eventTicketObject.seatInfo.section`) on the card detail view. Each
   * available option maps to a set of localized strings, so that translations
   * are shown to the user based on their locale. Both `sectionLabel` and
   * `customSectionLabel` may not be set. If neither is set, the label will
   * default to "Section", localized. If the section field is unset, this label
   * will not be used.
   *
   * Accepted values: SECTION_LABEL_UNSPECIFIED, SECTION, section, THEATER,
   * theater
   *
   * @param self::SECTION_LABEL_* $sectionLabel
   */
  public function setSectionLabel($sectionLabel)
  {
    $this->sectionLabel = $sectionLabel;
  }
  /**
   * @return self::SECTION_LABEL_*
   */
  public function getSectionLabel()
  {
    return $this->sectionLabel;
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
   * Event venue details.
   *
   * @param EventVenue $venue
   */
  public function setVenue(EventVenue $venue)
  {
    $this->venue = $venue;
  }
  /**
   * @return EventVenue
   */
  public function getVenue()
  {
    return $this->venue;
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
   * View Unlock Requirement options for the event ticket.
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
   * The wide logo of the ticket. When provided, this will be used in place of
   * the logo in the top left of the card view.
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
class_alias(EventTicketClass::class, 'Google_Service_Walletobjects_EventTicketClass');
