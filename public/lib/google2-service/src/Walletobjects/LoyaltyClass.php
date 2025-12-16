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

class LoyaltyClass extends \Google\Collection
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
   * The account ID label, such as "Member ID." Recommended maximum length is 15
   * characters to ensure full string is displayed on smaller screens.
   *
   * @var string
   */
  public $accountIdLabel;
  /**
   * The account name label, such as "Member Name." Recommended maximum length
   * is 15 characters to ensure full string is displayed on smaller screens.
   *
   * @var string
   */
  public $accountNameLabel;
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
  protected $discoverableProgramType = DiscoverableProgram::class;
  protected $discoverableProgramDataType = '';
  /**
   * Identifies whether this class supports Smart Tap. The `redemptionIssuers`
   * and one of object level `smartTapRedemptionLevel`, barcode.value`, or
   * `accountId` fields must also be set up correctly in order for a pass to
   * support Smart Tap.
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
   * Identifies what kind of resource this is. Value: the fixed string
   * `"walletobjects#loyaltyClass"`.
   *
   * @deprecated
   * @var string
   */
  public $kind;
  protected $linksModuleDataType = LinksModuleData::class;
  protected $linksModuleDataDataType = '';
  protected $localizedAccountIdLabelType = LocalizedString::class;
  protected $localizedAccountIdLabelDataType = '';
  protected $localizedAccountNameLabelType = LocalizedString::class;
  protected $localizedAccountNameLabelDataType = '';
  protected $localizedIssuerNameType = LocalizedString::class;
  protected $localizedIssuerNameDataType = '';
  protected $localizedProgramNameType = LocalizedString::class;
  protected $localizedProgramNameDataType = '';
  protected $localizedRewardsTierType = LocalizedString::class;
  protected $localizedRewardsTierDataType = '';
  protected $localizedRewardsTierLabelType = LocalizedString::class;
  protected $localizedRewardsTierLabelDataType = '';
  protected $localizedSecondaryRewardsTierType = LocalizedString::class;
  protected $localizedSecondaryRewardsTierDataType = '';
  protected $localizedSecondaryRewardsTierLabelType = LocalizedString::class;
  protected $localizedSecondaryRewardsTierLabelDataType = '';
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
  protected $programLogoType = Image::class;
  protected $programLogoDataType = '';
  /**
   * Required. The program name, such as "Adam's Apparel". The app may display
   * an ellipsis after the first 20 characters to ensure full string is
   * displayed on smaller screens.
   *
   * @var string
   */
  public $programName;
  /**
   * Identifies which redemption issuers can redeem the pass over Smart Tap.
   * Redemption issuers are identified by their issuer ID. Redemption issuers
   * must have at least one Smart Tap key configured. The `enableSmartTap` and
   * one of object level `smartTapRedemptionValue`, barcode.value`, or
   * `accountId` fields must also be set up correctly in order for a pass to
   * support Smart Tap.
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
   * The rewards tier, such as "Gold" or "Platinum." Recommended maximum length
   * is 7 characters to ensure full string is displayed on smaller screens.
   *
   * @var string
   */
  public $rewardsTier;
  /**
   * The rewards tier label, such as "Rewards Tier." Recommended maximum length
   * is 9 characters to ensure full string is displayed on smaller screens.
   *
   * @var string
   */
  public $rewardsTierLabel;
  /**
   * The secondary rewards tier, such as "Gold" or "Platinum."
   *
   * @var string
   */
  public $secondaryRewardsTier;
  /**
   * The secondary rewards tier label, such as "Rewards Tier."
   *
   * @var string
   */
  public $secondaryRewardsTierLabel;
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
   * View Unlock Requirement options for the loyalty card.
   *
   * @var string
   */
  public $viewUnlockRequirement;
  protected $wideProgramLogoType = Image::class;
  protected $wideProgramLogoDataType = '';
  protected $wordMarkType = Image::class;
  protected $wordMarkDataType = '';

  /**
   * The account ID label, such as "Member ID." Recommended maximum length is 15
   * characters to ensure full string is displayed on smaller screens.
   *
   * @param string $accountIdLabel
   */
  public function setAccountIdLabel($accountIdLabel)
  {
    $this->accountIdLabel = $accountIdLabel;
  }
  /**
   * @return string
   */
  public function getAccountIdLabel()
  {
    return $this->accountIdLabel;
  }
  /**
   * The account name label, such as "Member Name." Recommended maximum length
   * is 15 characters to ensure full string is displayed on smaller screens.
   *
   * @param string $accountNameLabel
   */
  public function setAccountNameLabel($accountNameLabel)
  {
    $this->accountNameLabel = $accountNameLabel;
  }
  /**
   * @return string
   */
  public function getAccountNameLabel()
  {
    return $this->accountNameLabel;
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
   * Information about how the class may be discovered and instantiated from
   * within the Google Pay app.
   *
   * @param DiscoverableProgram $discoverableProgram
   */
  public function setDiscoverableProgram(DiscoverableProgram $discoverableProgram)
  {
    $this->discoverableProgram = $discoverableProgram;
  }
  /**
   * @return DiscoverableProgram
   */
  public function getDiscoverableProgram()
  {
    return $this->discoverableProgram;
  }
  /**
   * Identifies whether this class supports Smart Tap. The `redemptionIssuers`
   * and one of object level `smartTapRedemptionLevel`, barcode.value`, or
   * `accountId` fields must also be set up correctly in order for a pass to
   * support Smart Tap.
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
   * Identifies what kind of resource this is. Value: the fixed string
   * `"walletobjects#loyaltyClass"`.
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
   * Translated strings for the account_id_label. Recommended maximum length is
   * 15 characters to ensure full string is displayed on smaller screens.
   *
   * @param LocalizedString $localizedAccountIdLabel
   */
  public function setLocalizedAccountIdLabel(LocalizedString $localizedAccountIdLabel)
  {
    $this->localizedAccountIdLabel = $localizedAccountIdLabel;
  }
  /**
   * @return LocalizedString
   */
  public function getLocalizedAccountIdLabel()
  {
    return $this->localizedAccountIdLabel;
  }
  /**
   * Translated strings for the account_name_label. Recommended maximum length
   * is 15 characters to ensure full string is displayed on smaller screens.
   *
   * @param LocalizedString $localizedAccountNameLabel
   */
  public function setLocalizedAccountNameLabel(LocalizedString $localizedAccountNameLabel)
  {
    $this->localizedAccountNameLabel = $localizedAccountNameLabel;
  }
  /**
   * @return LocalizedString
   */
  public function getLocalizedAccountNameLabel()
  {
    return $this->localizedAccountNameLabel;
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
   * Translated strings for the program_name. The app may display an ellipsis
   * after the first 20 characters to ensure full string is displayed on smaller
   * screens.
   *
   * @param LocalizedString $localizedProgramName
   */
  public function setLocalizedProgramName(LocalizedString $localizedProgramName)
  {
    $this->localizedProgramName = $localizedProgramName;
  }
  /**
   * @return LocalizedString
   */
  public function getLocalizedProgramName()
  {
    return $this->localizedProgramName;
  }
  /**
   * Translated strings for the rewards_tier. Recommended maximum length is 7
   * characters to ensure full string is displayed on smaller screens.
   *
   * @param LocalizedString $localizedRewardsTier
   */
  public function setLocalizedRewardsTier(LocalizedString $localizedRewardsTier)
  {
    $this->localizedRewardsTier = $localizedRewardsTier;
  }
  /**
   * @return LocalizedString
   */
  public function getLocalizedRewardsTier()
  {
    return $this->localizedRewardsTier;
  }
  /**
   * Translated strings for the rewards_tier_label. Recommended maximum length
   * is 9 characters to ensure full string is displayed on smaller screens.
   *
   * @param LocalizedString $localizedRewardsTierLabel
   */
  public function setLocalizedRewardsTierLabel(LocalizedString $localizedRewardsTierLabel)
  {
    $this->localizedRewardsTierLabel = $localizedRewardsTierLabel;
  }
  /**
   * @return LocalizedString
   */
  public function getLocalizedRewardsTierLabel()
  {
    return $this->localizedRewardsTierLabel;
  }
  /**
   * Translated strings for the secondary_rewards_tier.
   *
   * @param LocalizedString $localizedSecondaryRewardsTier
   */
  public function setLocalizedSecondaryRewardsTier(LocalizedString $localizedSecondaryRewardsTier)
  {
    $this->localizedSecondaryRewardsTier = $localizedSecondaryRewardsTier;
  }
  /**
   * @return LocalizedString
   */
  public function getLocalizedSecondaryRewardsTier()
  {
    return $this->localizedSecondaryRewardsTier;
  }
  /**
   * Translated strings for the secondary_rewards_tier_label.
   *
   * @param LocalizedString $localizedSecondaryRewardsTierLabel
   */
  public function setLocalizedSecondaryRewardsTierLabel(LocalizedString $localizedSecondaryRewardsTierLabel)
  {
    $this->localizedSecondaryRewardsTierLabel = $localizedSecondaryRewardsTierLabel;
  }
  /**
   * @return LocalizedString
   */
  public function getLocalizedSecondaryRewardsTierLabel()
  {
    return $this->localizedSecondaryRewardsTierLabel;
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
   * Required. The logo of the loyalty program or company. This logo is
   * displayed in both the details and list views of the app.
   *
   * @param Image $programLogo
   */
  public function setProgramLogo(Image $programLogo)
  {
    $this->programLogo = $programLogo;
  }
  /**
   * @return Image
   */
  public function getProgramLogo()
  {
    return $this->programLogo;
  }
  /**
   * Required. The program name, such as "Adam's Apparel". The app may display
   * an ellipsis after the first 20 characters to ensure full string is
   * displayed on smaller screens.
   *
   * @param string $programName
   */
  public function setProgramName($programName)
  {
    $this->programName = $programName;
  }
  /**
   * @return string
   */
  public function getProgramName()
  {
    return $this->programName;
  }
  /**
   * Identifies which redemption issuers can redeem the pass over Smart Tap.
   * Redemption issuers are identified by their issuer ID. Redemption issuers
   * must have at least one Smart Tap key configured. The `enableSmartTap` and
   * one of object level `smartTapRedemptionValue`, barcode.value`, or
   * `accountId` fields must also be set up correctly in order for a pass to
   * support Smart Tap.
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
   * The rewards tier, such as "Gold" or "Platinum." Recommended maximum length
   * is 7 characters to ensure full string is displayed on smaller screens.
   *
   * @param string $rewardsTier
   */
  public function setRewardsTier($rewardsTier)
  {
    $this->rewardsTier = $rewardsTier;
  }
  /**
   * @return string
   */
  public function getRewardsTier()
  {
    return $this->rewardsTier;
  }
  /**
   * The rewards tier label, such as "Rewards Tier." Recommended maximum length
   * is 9 characters to ensure full string is displayed on smaller screens.
   *
   * @param string $rewardsTierLabel
   */
  public function setRewardsTierLabel($rewardsTierLabel)
  {
    $this->rewardsTierLabel = $rewardsTierLabel;
  }
  /**
   * @return string
   */
  public function getRewardsTierLabel()
  {
    return $this->rewardsTierLabel;
  }
  /**
   * The secondary rewards tier, such as "Gold" or "Platinum."
   *
   * @param string $secondaryRewardsTier
   */
  public function setSecondaryRewardsTier($secondaryRewardsTier)
  {
    $this->secondaryRewardsTier = $secondaryRewardsTier;
  }
  /**
   * @return string
   */
  public function getSecondaryRewardsTier()
  {
    return $this->secondaryRewardsTier;
  }
  /**
   * The secondary rewards tier label, such as "Rewards Tier."
   *
   * @param string $secondaryRewardsTierLabel
   */
  public function setSecondaryRewardsTierLabel($secondaryRewardsTierLabel)
  {
    $this->secondaryRewardsTierLabel = $secondaryRewardsTierLabel;
  }
  /**
   * @return string
   */
  public function getSecondaryRewardsTierLabel()
  {
    return $this->secondaryRewardsTierLabel;
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
   * View Unlock Requirement options for the loyalty card.
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
   * The wide logo of the loyalty program or company. When provided, this will
   * be used in place of the program logo in the top left of the card view.
   *
   * @param Image $wideProgramLogo
   */
  public function setWideProgramLogo(Image $wideProgramLogo)
  {
    $this->wideProgramLogo = $wideProgramLogo;
  }
  /**
   * @return Image
   */
  public function getWideProgramLogo()
  {
    return $this->wideProgramLogo;
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
class_alias(LoyaltyClass::class, 'Google_Service_Walletobjects_LoyaltyClass');
