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

namespace Google\Service\Dfareporting;

class AccountUserProfile extends \Google\Model
{
  /**
   * Internal profile, but is not a trafficker.
   */
  public const TRAFFICKER_TYPE_INTERNAL_NON_TRAFFICKER = 'INTERNAL_NON_TRAFFICKER';
  /**
   * Internal profile who is a trafficker.
   */
  public const TRAFFICKER_TYPE_INTERNAL_TRAFFICKER = 'INTERNAL_TRAFFICKER';
  /**
   * External profile who is a trafficker.
   */
  public const TRAFFICKER_TYPE_EXTERNAL_TRAFFICKER = 'EXTERNAL_TRAFFICKER';
  /**
   * Normal user managed by the customer.
   */
  public const USER_ACCESS_TYPE_NORMAL_USER = 'NORMAL_USER';
  /**
   * Super user managed by internal support teams.
   */
  public const USER_ACCESS_TYPE_SUPER_USER = 'SUPER_USER';
  /**
   * Internal administrator having super user access to only a specific set of
   * networks.
   */
  public const USER_ACCESS_TYPE_INTERNAL_ADMINISTRATOR = 'INTERNAL_ADMINISTRATOR';
  /**
   * A super-user without permission to mutate any data.
   */
  public const USER_ACCESS_TYPE_READ_ONLY_SUPER_USER = 'READ_ONLY_SUPER_USER';
  /**
   * Account ID of the user profile. This is a read-only field that can be left
   * blank.
   *
   * @var string
   */
  public $accountId;
  /**
   * Whether this user profile is active. This defaults to false, and must be
   * set true on insert for the user profile to be usable.
   *
   * @var bool
   */
  public $active;
  protected $advertiserFilterType = ObjectFilter::class;
  protected $advertiserFilterDataType = '';
  protected $campaignFilterType = ObjectFilter::class;
  protected $campaignFilterDataType = '';
  /**
   * Comments for this user profile.
   *
   * @var string
   */
  public $comments;
  /**
   * Email of the user profile. The email address must be linked to a Google
   * Account. This field is required on insertion and is read-only after
   * insertion.
   *
   * @var string
   */
  public $email;
  /**
   * ID of the user profile. This is a read-only, auto-generated field.
   *
   * @var string
   */
  public $id;
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "dfareporting#accountUserProfile".
   *
   * @var string
   */
  public $kind;
  /**
   * Locale of the user profile. This is a required field. Acceptable values
   * are: - "cs" (Czech) - "de" (German) - "en" (English) - "en-GB" (English
   * United Kingdom) - "es" (Spanish) - "fr" (French) - "it" (Italian) - "ja"
   * (Japanese) - "ko" (Korean) - "pl" (Polish) - "pt-BR" (Portuguese Brazil) -
   * "ru" (Russian) - "sv" (Swedish) - "tr" (Turkish) - "zh-CN" (Chinese
   * Simplified) - "zh-TW" (Chinese Traditional)
   *
   * @var string
   */
  public $locale;
  /**
   * Name of the user profile. This is a required field. Must be less than 64
   * characters long, must be globally unique, and cannot contain whitespace or
   * any of the following characters: "&;<>"#%,".
   *
   * @var string
   */
  public $name;
  protected $siteFilterType = ObjectFilter::class;
  protected $siteFilterDataType = '';
  /**
   * Subaccount ID of the user profile. This is a read-only field that can be
   * left blank.
   *
   * @var string
   */
  public $subaccountId;
  /**
   * Trafficker type of this user profile. This is a read-only field.
   *
   * @var string
   */
  public $traffickerType;
  /**
   * User type of the user profile. This is a read-only field that can be left
   * blank.
   *
   * @var string
   */
  public $userAccessType;
  protected $userRoleFilterType = ObjectFilter::class;
  protected $userRoleFilterDataType = '';
  /**
   * User role ID of the user profile. This is a required field.
   *
   * @var string
   */
  public $userRoleId;

  /**
   * Account ID of the user profile. This is a read-only field that can be left
   * blank.
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
   * Whether this user profile is active. This defaults to false, and must be
   * set true on insert for the user profile to be usable.
   *
   * @param bool $active
   */
  public function setActive($active)
  {
    $this->active = $active;
  }
  /**
   * @return bool
   */
  public function getActive()
  {
    return $this->active;
  }
  /**
   * Filter that describes which advertisers are visible to the user profile.
   *
   * @param ObjectFilter $advertiserFilter
   */
  public function setAdvertiserFilter(ObjectFilter $advertiserFilter)
  {
    $this->advertiserFilter = $advertiserFilter;
  }
  /**
   * @return ObjectFilter
   */
  public function getAdvertiserFilter()
  {
    return $this->advertiserFilter;
  }
  /**
   * Filter that describes which campaigns are visible to the user profile.
   *
   * @param ObjectFilter $campaignFilter
   */
  public function setCampaignFilter(ObjectFilter $campaignFilter)
  {
    $this->campaignFilter = $campaignFilter;
  }
  /**
   * @return ObjectFilter
   */
  public function getCampaignFilter()
  {
    return $this->campaignFilter;
  }
  /**
   * Comments for this user profile.
   *
   * @param string $comments
   */
  public function setComments($comments)
  {
    $this->comments = $comments;
  }
  /**
   * @return string
   */
  public function getComments()
  {
    return $this->comments;
  }
  /**
   * Email of the user profile. The email address must be linked to a Google
   * Account. This field is required on insertion and is read-only after
   * insertion.
   *
   * @param string $email
   */
  public function setEmail($email)
  {
    $this->email = $email;
  }
  /**
   * @return string
   */
  public function getEmail()
  {
    return $this->email;
  }
  /**
   * ID of the user profile. This is a read-only, auto-generated field.
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
   * "dfareporting#accountUserProfile".
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
   * Locale of the user profile. This is a required field. Acceptable values
   * are: - "cs" (Czech) - "de" (German) - "en" (English) - "en-GB" (English
   * United Kingdom) - "es" (Spanish) - "fr" (French) - "it" (Italian) - "ja"
   * (Japanese) - "ko" (Korean) - "pl" (Polish) - "pt-BR" (Portuguese Brazil) -
   * "ru" (Russian) - "sv" (Swedish) - "tr" (Turkish) - "zh-CN" (Chinese
   * Simplified) - "zh-TW" (Chinese Traditional)
   *
   * @param string $locale
   */
  public function setLocale($locale)
  {
    $this->locale = $locale;
  }
  /**
   * @return string
   */
  public function getLocale()
  {
    return $this->locale;
  }
  /**
   * Name of the user profile. This is a required field. Must be less than 64
   * characters long, must be globally unique, and cannot contain whitespace or
   * any of the following characters: "&;<>"#%,".
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
   * Filter that describes which sites are visible to the user profile.
   *
   * @param ObjectFilter $siteFilter
   */
  public function setSiteFilter(ObjectFilter $siteFilter)
  {
    $this->siteFilter = $siteFilter;
  }
  /**
   * @return ObjectFilter
   */
  public function getSiteFilter()
  {
    return $this->siteFilter;
  }
  /**
   * Subaccount ID of the user profile. This is a read-only field that can be
   * left blank.
   *
   * @param string $subaccountId
   */
  public function setSubaccountId($subaccountId)
  {
    $this->subaccountId = $subaccountId;
  }
  /**
   * @return string
   */
  public function getSubaccountId()
  {
    return $this->subaccountId;
  }
  /**
   * Trafficker type of this user profile. This is a read-only field.
   *
   * Accepted values: INTERNAL_NON_TRAFFICKER, INTERNAL_TRAFFICKER,
   * EXTERNAL_TRAFFICKER
   *
   * @param self::TRAFFICKER_TYPE_* $traffickerType
   */
  public function setTraffickerType($traffickerType)
  {
    $this->traffickerType = $traffickerType;
  }
  /**
   * @return self::TRAFFICKER_TYPE_*
   */
  public function getTraffickerType()
  {
    return $this->traffickerType;
  }
  /**
   * User type of the user profile. This is a read-only field that can be left
   * blank.
   *
   * Accepted values: NORMAL_USER, SUPER_USER, INTERNAL_ADMINISTRATOR,
   * READ_ONLY_SUPER_USER
   *
   * @param self::USER_ACCESS_TYPE_* $userAccessType
   */
  public function setUserAccessType($userAccessType)
  {
    $this->userAccessType = $userAccessType;
  }
  /**
   * @return self::USER_ACCESS_TYPE_*
   */
  public function getUserAccessType()
  {
    return $this->userAccessType;
  }
  /**
   * Filter that describes which user roles are visible to the user profile.
   *
   * @param ObjectFilter $userRoleFilter
   */
  public function setUserRoleFilter(ObjectFilter $userRoleFilter)
  {
    $this->userRoleFilter = $userRoleFilter;
  }
  /**
   * @return ObjectFilter
   */
  public function getUserRoleFilter()
  {
    return $this->userRoleFilter;
  }
  /**
   * User role ID of the user profile. This is a required field.
   *
   * @param string $userRoleId
   */
  public function setUserRoleId($userRoleId)
  {
    $this->userRoleId = $userRoleId;
  }
  /**
   * @return string
   */
  public function getUserRoleId()
  {
    return $this->userRoleId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AccountUserProfile::class, 'Google_Service_Dfareporting_AccountUserProfile');
