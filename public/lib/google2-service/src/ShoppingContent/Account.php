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

namespace Google\Service\ShoppingContent;

class Account extends \Google\Collection
{
  protected $collection_key = 'youtubeChannelLinks';
  /**
   * Output only. How the account is managed. Acceptable values are: -
   * "`manual`" - "`automatic`"
   *
   * @var string
   */
  public $accountManagement;
  protected $adsLinksType = AccountAdsLink::class;
  protected $adsLinksDataType = 'array';
  /**
   * Indicates whether the merchant sells adult content.
   *
   * @var bool
   */
  public $adultContent;
  protected $automaticImprovementsType = AccountAutomaticImprovements::class;
  protected $automaticImprovementsDataType = '';
  /**
   * Automatically created label IDs that are assigned to the account by CSS
   * Center.
   *
   * @var string[]
   */
  public $automaticLabelIds;
  protected $businessIdentityType = AccountBusinessIdentity::class;
  protected $businessIdentityDataType = '';
  protected $businessInformationType = AccountBusinessInformation::class;
  protected $businessInformationDataType = '';
  protected $conversionSettingsType = AccountConversionSettings::class;
  protected $conversionSettingsDataType = '';
  /**
   * ID of CSS the account belongs to.
   *
   * @var string
   */
  public $cssId;
  protected $googleMyBusinessLinkType = AccountGoogleMyBusinessLink::class;
  protected $googleMyBusinessLinkDataType = '';
  /**
   * Required. 64-bit Merchant Center account ID.
   *
   * @var string
   */
  public $id;
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "`content#account`".
   *
   * @var string
   */
  public $kind;
  /**
   * Manually created label IDs that are assigned to the account by CSS.
   *
   * @var string[]
   */
  public $labelIds;
  /**
   * Required. Display name for the account.
   *
   * @var string
   */
  public $name;
  /**
   * Client-specific, locally-unique, internal ID for the child account.
   *
   * @var string
   */
  public $sellerId;
  protected $usersType = AccountUser::class;
  protected $usersDataType = 'array';
  /**
   * The merchant's website.
   *
   * @var string
   */
  public $websiteUrl;
  protected $youtubeChannelLinksType = AccountYouTubeChannelLink::class;
  protected $youtubeChannelLinksDataType = 'array';

  /**
   * Output only. How the account is managed. Acceptable values are: -
   * "`manual`" - "`automatic`"
   *
   * @param string $accountManagement
   */
  public function setAccountManagement($accountManagement)
  {
    $this->accountManagement = $accountManagement;
  }
  /**
   * @return string
   */
  public function getAccountManagement()
  {
    return $this->accountManagement;
  }
  /**
   * Linked Ads accounts that are active or pending approval. To create a new
   * link request, add a new link with status `active` to the list. It will
   * remain in a `pending` state until approved or rejected either in the Ads
   * interface or through the Google Ads API. To delete an active link, or to
   * cancel a link request, remove it from the list.
   *
   * @param AccountAdsLink[] $adsLinks
   */
  public function setAdsLinks($adsLinks)
  {
    $this->adsLinks = $adsLinks;
  }
  /**
   * @return AccountAdsLink[]
   */
  public function getAdsLinks()
  {
    return $this->adsLinks;
  }
  /**
   * Indicates whether the merchant sells adult content.
   *
   * @param bool $adultContent
   */
  public function setAdultContent($adultContent)
  {
    $this->adultContent = $adultContent;
  }
  /**
   * @return bool
   */
  public function getAdultContent()
  {
    return $this->adultContent;
  }
  /**
   * The automatic improvements of the account can be used to automatically
   * update items, improve images and shipping. Each section inside
   * AutomaticImprovements is updated separately.
   *
   * @param AccountAutomaticImprovements $automaticImprovements
   */
  public function setAutomaticImprovements(AccountAutomaticImprovements $automaticImprovements)
  {
    $this->automaticImprovements = $automaticImprovements;
  }
  /**
   * @return AccountAutomaticImprovements
   */
  public function getAutomaticImprovements()
  {
    return $this->automaticImprovements;
  }
  /**
   * Automatically created label IDs that are assigned to the account by CSS
   * Center.
   *
   * @param string[] $automaticLabelIds
   */
  public function setAutomaticLabelIds($automaticLabelIds)
  {
    $this->automaticLabelIds = $automaticLabelIds;
  }
  /**
   * @return string[]
   */
  public function getAutomaticLabelIds()
  {
    return $this->automaticLabelIds;
  }
  /**
   * The business identity attributes can be used to self-declare attributes
   * that let customers know more about your business.
   *
   * @param AccountBusinessIdentity $businessIdentity
   */
  public function setBusinessIdentity(AccountBusinessIdentity $businessIdentity)
  {
    $this->businessIdentity = $businessIdentity;
  }
  /**
   * @return AccountBusinessIdentity
   */
  public function getBusinessIdentity()
  {
    return $this->businessIdentity;
  }
  /**
   * The business information of the account.
   *
   * @param AccountBusinessInformation $businessInformation
   */
  public function setBusinessInformation(AccountBusinessInformation $businessInformation)
  {
    $this->businessInformation = $businessInformation;
  }
  /**
   * @return AccountBusinessInformation
   */
  public function getBusinessInformation()
  {
    return $this->businessInformation;
  }
  /**
   * Settings for conversion tracking.
   *
   * @param AccountConversionSettings $conversionSettings
   */
  public function setConversionSettings(AccountConversionSettings $conversionSettings)
  {
    $this->conversionSettings = $conversionSettings;
  }
  /**
   * @return AccountConversionSettings
   */
  public function getConversionSettings()
  {
    return $this->conversionSettings;
  }
  /**
   * ID of CSS the account belongs to.
   *
   * @param string $cssId
   */
  public function setCssId($cssId)
  {
    $this->cssId = $cssId;
  }
  /**
   * @return string
   */
  public function getCssId()
  {
    return $this->cssId;
  }
  /**
   * The Business Profile which is linked or in the process of being linked with
   * the Merchant Center account.
   *
   * @param AccountGoogleMyBusinessLink $googleMyBusinessLink
   */
  public function setGoogleMyBusinessLink(AccountGoogleMyBusinessLink $googleMyBusinessLink)
  {
    $this->googleMyBusinessLink = $googleMyBusinessLink;
  }
  /**
   * @return AccountGoogleMyBusinessLink
   */
  public function getGoogleMyBusinessLink()
  {
    return $this->googleMyBusinessLink;
  }
  /**
   * Required. 64-bit Merchant Center account ID.
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
   * Identifies what kind of resource this is. Value: the fixed string
   * "`content#account`".
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * Manually created label IDs that are assigned to the account by CSS.
   *
   * @param string[] $labelIds
   */
  public function setLabelIds($labelIds)
  {
    $this->labelIds = $labelIds;
  }
  /**
   * @return string[]
   */
  public function getLabelIds()
  {
    return $this->labelIds;
  }
  /**
   * Required. Display name for the account.
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
   * Client-specific, locally-unique, internal ID for the child account.
   *
   * @param string $sellerId
   */
  public function setSellerId($sellerId)
  {
    $this->sellerId = $sellerId;
  }
  /**
   * @return string
   */
  public function getSellerId()
  {
    return $this->sellerId;
  }
  /**
   * Users with access to the account. Every account (except for subaccounts)
   * must have at least one admin user.
   *
   * @param AccountUser[] $users
   */
  public function setUsers($users)
  {
    $this->users = $users;
  }
  /**
   * @return AccountUser[]
   */
  public function getUsers()
  {
    return $this->users;
  }
  /**
   * The merchant's website.
   *
   * @param string $websiteUrl
   */
  public function setWebsiteUrl($websiteUrl)
  {
    $this->websiteUrl = $websiteUrl;
  }
  /**
   * @return string
   */
  public function getWebsiteUrl()
  {
    return $this->websiteUrl;
  }
  /**
   * Linked YouTube channels that are active or pending approval. To create a
   * new link request, add a new link with status `active` to the list. It will
   * remain in a `pending` state until approved or rejected in the YT Creator
   * Studio interface. To delete an active link, or to cancel a link request,
   * remove it from the list.
   *
   * @param AccountYouTubeChannelLink[] $youtubeChannelLinks
   */
  public function setYoutubeChannelLinks($youtubeChannelLinks)
  {
    $this->youtubeChannelLinks = $youtubeChannelLinks;
  }
  /**
   * @return AccountYouTubeChannelLink[]
   */
  public function getYoutubeChannelLinks()
  {
    return $this->youtubeChannelLinks;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Account::class, 'Google_Service_ShoppingContent_Account');
