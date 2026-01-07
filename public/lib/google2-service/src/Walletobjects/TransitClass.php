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

class TransitClass extends \Google\Collection
{
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
  public const TRANSIT_TYPE_TRANSIT_TYPE_UNSPECIFIED = 'TRANSIT_TYPE_UNSPECIFIED';
  public const TRANSIT_TYPE_BUS = 'BUS';
  /**
   * Legacy alias for `BUS`. Deprecated.
   *
   * @deprecated
   */
  public const TRANSIT_TYPE_bus = 'bus';
  public const TRANSIT_TYPE_RAIL = 'RAIL';
  /**
   * Legacy alias for `RAIL`. Deprecated.
   *
   * @deprecated
   */
  public const TRANSIT_TYPE_rail = 'rail';
  public const TRANSIT_TYPE_TRAM = 'TRAM';
  /**
   * Legacy alias for `TRAM`. Deprecated.
   *
   * @deprecated
   */
  public const TRANSIT_TYPE_tram = 'tram';
  public const TRANSIT_TYPE_FERRY = 'FERRY';
  /**
   * Legacy alias for `FERRY`. Deprecated.
   *
   * @deprecated
   */
  public const TRANSIT_TYPE_ferry = 'ferry';
  public const TRANSIT_TYPE_OTHER = 'OTHER';
  /**
   * Legacy alias for `OTHER`. Deprecated.
   *
   * @deprecated
   */
  public const TRANSIT_TYPE_other = 'other';
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
  protected $activationOptionsType = ActivationOptions::class;
  protected $activationOptionsDataType = '';
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
   * Country code used to display the card's country (when the user is not in
   * that country), as well as to display localized content when content is not
   * available in the user's locale.
   *
   * @var string
   */
  public $countryCode;
  protected $customCarriageLabelType = LocalizedString::class;
  protected $customCarriageLabelDataType = '';
  protected $customCoachLabelType = LocalizedString::class;
  protected $customCoachLabelDataType = '';
  protected $customConcessionCategoryLabelType = LocalizedString::class;
  protected $customConcessionCategoryLabelDataType = '';
  protected $customConfirmationCodeLabelType = LocalizedString::class;
  protected $customConfirmationCodeLabelDataType = '';
  protected $customDiscountMessageLabelType = LocalizedString::class;
  protected $customDiscountMessageLabelDataType = '';
  protected $customFareClassLabelType = LocalizedString::class;
  protected $customFareClassLabelDataType = '';
  protected $customFareNameLabelType = LocalizedString::class;
  protected $customFareNameLabelDataType = '';
  protected $customOtherRestrictionsLabelType = LocalizedString::class;
  protected $customOtherRestrictionsLabelDataType = '';
  protected $customPlatformLabelType = LocalizedString::class;
  protected $customPlatformLabelDataType = '';
  protected $customPurchaseFaceValueLabelType = LocalizedString::class;
  protected $customPurchaseFaceValueLabelDataType = '';
  protected $customPurchasePriceLabelType = LocalizedString::class;
  protected $customPurchasePriceLabelDataType = '';
  protected $customPurchaseReceiptNumberLabelType = LocalizedString::class;
  protected $customPurchaseReceiptNumberLabelDataType = '';
  protected $customRouteRestrictionsDetailsLabelType = LocalizedString::class;
  protected $customRouteRestrictionsDetailsLabelDataType = '';
  protected $customRouteRestrictionsLabelType = LocalizedString::class;
  protected $customRouteRestrictionsLabelDataType = '';
  protected $customSeatLabelType = LocalizedString::class;
  protected $customSeatLabelDataType = '';
  protected $customTicketNumberLabelType = LocalizedString::class;
  protected $customTicketNumberLabelDataType = '';
  protected $customTimeRestrictionsLabelType = LocalizedString::class;
  protected $customTimeRestrictionsLabelDataType = '';
  protected $customTransitTerminusNameLabelType = LocalizedString::class;
  protected $customTransitTerminusNameLabelDataType = '';
  protected $customZoneLabelType = LocalizedString::class;
  protected $customZoneLabelDataType = '';
  /**
   * Controls the display of the single-leg itinerary for this class. By
   * default, an itinerary will only display for multi-leg trips.
   *
   * @var bool
   */
  public $enableSingleLegItinerary;
  /**
   * Identifies whether this class supports Smart Tap. The `redemptionIssuers`
   * and object level `smartTapRedemptionLevel` fields must also be set up
   * correctly in order for a pass to support Smart Tap.
   *
   * @var bool
   */
  public $enableSmartTap;
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
   * If this field is present, transit tickets served to a user's device will
   * always be in this language. Represents the BCP 47 language tag. Example
   * values are "en-US", "en-GB", "de", or "de-AT".
   *
   * @var string
   */
  public $languageOverride;
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
   * part of an allowlist. If set to DO_NOT_NOTIFY or
   * NOTIFICATION_SETTINGS_UNSPECIFIED, no notification will be triggered. This
   * setting is ephemeral and needs to be set with each PATCH or UPDATE request,
   * otherwise a notification will not be triggered.
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
  protected $securityAnimationType = SecurityAnimation::class;
  protected $securityAnimationDataType = '';
  protected $textModulesDataType = TextModuleData::class;
  protected $textModulesDataDataType = 'array';
  protected $transitOperatorNameType = LocalizedString::class;
  protected $transitOperatorNameDataType = '';
  /**
   * Required. The type of transit this class represents, such as "bus".
   *
   * @var string
   */
  public $transitType;
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
   * View Unlock Requirement options for the transit ticket.
   *
   * @var string
   */
  public $viewUnlockRequirement;
  protected $watermarkType = Image::class;
  protected $watermarkDataType = '';
  protected $wideLogoType = Image::class;
  protected $wideLogoDataType = '';
  protected $wordMarkType = Image::class;
  protected $wordMarkDataType = '';

  /**
   * Activation options for an activatable ticket.
   *
   * @param ActivationOptions $activationOptions
   */
  public function setActivationOptions(ActivationOptions $activationOptions)
  {
    $this->activationOptions = $activationOptions;
  }
  /**
   * @return ActivationOptions
   */
  public function getActivationOptions()
  {
    return $this->activationOptions;
  }
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
   * A custom label to use for the carriage value
   * (`transitObject.ticketLeg.carriage`).
   *
   * @param LocalizedString $customCarriageLabel
   */
  public function setCustomCarriageLabel(LocalizedString $customCarriageLabel)
  {
    $this->customCarriageLabel = $customCarriageLabel;
  }
  /**
   * @return LocalizedString
   */
  public function getCustomCarriageLabel()
  {
    return $this->customCarriageLabel;
  }
  /**
   * A custom label to use for the coach value
   * (`transitObject.ticketLeg.ticketSeat.coach`).
   *
   * @param LocalizedString $customCoachLabel
   */
  public function setCustomCoachLabel(LocalizedString $customCoachLabel)
  {
    $this->customCoachLabel = $customCoachLabel;
  }
  /**
   * @return LocalizedString
   */
  public function getCustomCoachLabel()
  {
    return $this->customCoachLabel;
  }
  /**
   * A custom label to use for the transit concession category value
   * (`transitObject.concessionCategory`).
   *
   * @param LocalizedString $customConcessionCategoryLabel
   */
  public function setCustomConcessionCategoryLabel(LocalizedString $customConcessionCategoryLabel)
  {
    $this->customConcessionCategoryLabel = $customConcessionCategoryLabel;
  }
  /**
   * @return LocalizedString
   */
  public function getCustomConcessionCategoryLabel()
  {
    return $this->customConcessionCategoryLabel;
  }
  /**
   * A custom label to use for the confirmation code value
   * (`transitObject.purchaseDetails.confirmationCode`).
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
   * A custom label to use for the transit discount message value
   * (`transitObject.purchaseDetails.ticketCost.discountMessage`).
   *
   * @param LocalizedString $customDiscountMessageLabel
   */
  public function setCustomDiscountMessageLabel(LocalizedString $customDiscountMessageLabel)
  {
    $this->customDiscountMessageLabel = $customDiscountMessageLabel;
  }
  /**
   * @return LocalizedString
   */
  public function getCustomDiscountMessageLabel()
  {
    return $this->customDiscountMessageLabel;
  }
  /**
   * A custom label to use for the fare class value
   * (`transitObject.ticketLeg.ticketSeat.fareClass`).
   *
   * @param LocalizedString $customFareClassLabel
   */
  public function setCustomFareClassLabel(LocalizedString $customFareClassLabel)
  {
    $this->customFareClassLabel = $customFareClassLabel;
  }
  /**
   * @return LocalizedString
   */
  public function getCustomFareClassLabel()
  {
    return $this->customFareClassLabel;
  }
  /**
   * A custom label to use for the transit fare name value
   * (`transitObject.ticketLeg.fareName`).
   *
   * @param LocalizedString $customFareNameLabel
   */
  public function setCustomFareNameLabel(LocalizedString $customFareNameLabel)
  {
    $this->customFareNameLabel = $customFareNameLabel;
  }
  /**
   * @return LocalizedString
   */
  public function getCustomFareNameLabel()
  {
    return $this->customFareNameLabel;
  }
  /**
   * A custom label to use for the other restrictions value
   * (`transitObject.ticketRestrictions.otherRestrictions`).
   *
   * @param LocalizedString $customOtherRestrictionsLabel
   */
  public function setCustomOtherRestrictionsLabel(LocalizedString $customOtherRestrictionsLabel)
  {
    $this->customOtherRestrictionsLabel = $customOtherRestrictionsLabel;
  }
  /**
   * @return LocalizedString
   */
  public function getCustomOtherRestrictionsLabel()
  {
    return $this->customOtherRestrictionsLabel;
  }
  /**
   * A custom label to use for the boarding platform value
   * (`transitObject.ticketLeg.platform`).
   *
   * @param LocalizedString $customPlatformLabel
   */
  public function setCustomPlatformLabel(LocalizedString $customPlatformLabel)
  {
    $this->customPlatformLabel = $customPlatformLabel;
  }
  /**
   * @return LocalizedString
   */
  public function getCustomPlatformLabel()
  {
    return $this->customPlatformLabel;
  }
  /**
   * A custom label to use for the purchase face value
   * (`transitObject.purchaseDetails.ticketCost.faceValue`).
   *
   * @param LocalizedString $customPurchaseFaceValueLabel
   */
  public function setCustomPurchaseFaceValueLabel(LocalizedString $customPurchaseFaceValueLabel)
  {
    $this->customPurchaseFaceValueLabel = $customPurchaseFaceValueLabel;
  }
  /**
   * @return LocalizedString
   */
  public function getCustomPurchaseFaceValueLabel()
  {
    return $this->customPurchaseFaceValueLabel;
  }
  /**
   * A custom label to use for the purchase price value
   * (`transitObject.purchaseDetails.ticketCost.purchasePrice`).
   *
   * @param LocalizedString $customPurchasePriceLabel
   */
  public function setCustomPurchasePriceLabel(LocalizedString $customPurchasePriceLabel)
  {
    $this->customPurchasePriceLabel = $customPurchasePriceLabel;
  }
  /**
   * @return LocalizedString
   */
  public function getCustomPurchasePriceLabel()
  {
    return $this->customPurchasePriceLabel;
  }
  /**
   * A custom label to use for the purchase receipt number value
   * (`transitObject.purchaseDetails.purchaseReceiptNumber`).
   *
   * @param LocalizedString $customPurchaseReceiptNumberLabel
   */
  public function setCustomPurchaseReceiptNumberLabel(LocalizedString $customPurchaseReceiptNumberLabel)
  {
    $this->customPurchaseReceiptNumberLabel = $customPurchaseReceiptNumberLabel;
  }
  /**
   * @return LocalizedString
   */
  public function getCustomPurchaseReceiptNumberLabel()
  {
    return $this->customPurchaseReceiptNumberLabel;
  }
  /**
   * A custom label to use for the route restrictions details value
   * (`transitObject.ticketRestrictions.routeRestrictionsDetails`).
   *
   * @param LocalizedString $customRouteRestrictionsDetailsLabel
   */
  public function setCustomRouteRestrictionsDetailsLabel(LocalizedString $customRouteRestrictionsDetailsLabel)
  {
    $this->customRouteRestrictionsDetailsLabel = $customRouteRestrictionsDetailsLabel;
  }
  /**
   * @return LocalizedString
   */
  public function getCustomRouteRestrictionsDetailsLabel()
  {
    return $this->customRouteRestrictionsDetailsLabel;
  }
  /**
   * A custom label to use for the route restrictions value
   * (`transitObject.ticketRestrictions.routeRestrictions`).
   *
   * @param LocalizedString $customRouteRestrictionsLabel
   */
  public function setCustomRouteRestrictionsLabel(LocalizedString $customRouteRestrictionsLabel)
  {
    $this->customRouteRestrictionsLabel = $customRouteRestrictionsLabel;
  }
  /**
   * @return LocalizedString
   */
  public function getCustomRouteRestrictionsLabel()
  {
    return $this->customRouteRestrictionsLabel;
  }
  /**
   * A custom label to use for the seat location value
   * (`transitObject.ticketLeg.ticketSeat.seat`).
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
   * A custom label to use for the ticket number value
   * (`transitObject.ticketNumber`).
   *
   * @param LocalizedString $customTicketNumberLabel
   */
  public function setCustomTicketNumberLabel(LocalizedString $customTicketNumberLabel)
  {
    $this->customTicketNumberLabel = $customTicketNumberLabel;
  }
  /**
   * @return LocalizedString
   */
  public function getCustomTicketNumberLabel()
  {
    return $this->customTicketNumberLabel;
  }
  /**
   * A custom label to use for the time restrictions details value
   * (`transitObject.ticketRestrictions.timeRestrictions`).
   *
   * @param LocalizedString $customTimeRestrictionsLabel
   */
  public function setCustomTimeRestrictionsLabel(LocalizedString $customTimeRestrictionsLabel)
  {
    $this->customTimeRestrictionsLabel = $customTimeRestrictionsLabel;
  }
  /**
   * @return LocalizedString
   */
  public function getCustomTimeRestrictionsLabel()
  {
    return $this->customTimeRestrictionsLabel;
  }
  /**
   * A custom label to use for the transit terminus name value
   * (`transitObject.ticketLeg.transitTerminusName`).
   *
   * @param LocalizedString $customTransitTerminusNameLabel
   */
  public function setCustomTransitTerminusNameLabel(LocalizedString $customTransitTerminusNameLabel)
  {
    $this->customTransitTerminusNameLabel = $customTransitTerminusNameLabel;
  }
  /**
   * @return LocalizedString
   */
  public function getCustomTransitTerminusNameLabel()
  {
    return $this->customTransitTerminusNameLabel;
  }
  /**
   * A custom label to use for the boarding zone value
   * (`transitObject.ticketLeg.zone`).
   *
   * @param LocalizedString $customZoneLabel
   */
  public function setCustomZoneLabel(LocalizedString $customZoneLabel)
  {
    $this->customZoneLabel = $customZoneLabel;
  }
  /**
   * @return LocalizedString
   */
  public function getCustomZoneLabel()
  {
    return $this->customZoneLabel;
  }
  /**
   * Controls the display of the single-leg itinerary for this class. By
   * default, an itinerary will only display for multi-leg trips.
   *
   * @param bool $enableSingleLegItinerary
   */
  public function setEnableSingleLegItinerary($enableSingleLegItinerary)
  {
    $this->enableSingleLegItinerary = $enableSingleLegItinerary;
  }
  /**
   * @return bool
   */
  public function getEnableSingleLegItinerary()
  {
    return $this->enableSingleLegItinerary;
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
   * If this field is present, transit tickets served to a user's device will
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
   * Required. The logo image of the ticket. This image is displayed in the card
   * detail view of the app.
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
   * The name of the transit operator.
   *
   * @param LocalizedString $transitOperatorName
   */
  public function setTransitOperatorName(LocalizedString $transitOperatorName)
  {
    $this->transitOperatorName = $transitOperatorName;
  }
  /**
   * @return LocalizedString
   */
  public function getTransitOperatorName()
  {
    return $this->transitOperatorName;
  }
  /**
   * Required. The type of transit this class represents, such as "bus".
   *
   * Accepted values: TRANSIT_TYPE_UNSPECIFIED, BUS, bus, RAIL, rail, TRAM,
   * tram, FERRY, ferry, OTHER, other
   *
   * @param self::TRANSIT_TYPE_* $transitType
   */
  public function setTransitType($transitType)
  {
    $this->transitType = $transitType;
  }
  /**
   * @return self::TRANSIT_TYPE_*
   */
  public function getTransitType()
  {
    return $this->transitType;
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
   * View Unlock Requirement options for the transit ticket.
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
   * Watermark image to display on the user's device.
   *
   * @param Image $watermark
   */
  public function setWatermark(Image $watermark)
  {
    $this->watermark = $watermark;
  }
  /**
   * @return Image
   */
  public function getWatermark()
  {
    return $this->watermark;
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
class_alias(TransitClass::class, 'Google_Service_Walletobjects_TransitClass');
