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

namespace Google\Service\DisplayVideo;

class FirstPartyAndPartnerAudience extends \Google\Model
{
  /**
   * Default value when audience source is not specified or is unknown.
   */
  public const AUDIENCE_SOURCE_AUDIENCE_SOURCE_UNSPECIFIED = 'AUDIENCE_SOURCE_UNSPECIFIED';
  /**
   * Originated from Display & Video 360.
   */
  public const AUDIENCE_SOURCE_DISPLAY_VIDEO_360 = 'DISPLAY_VIDEO_360';
  /**
   * Originated from Campaign Manager 360.
   */
  public const AUDIENCE_SOURCE_CAMPAIGN_MANAGER = 'CAMPAIGN_MANAGER';
  /**
   * Originated from Google Ad Manager.
   */
  public const AUDIENCE_SOURCE_AD_MANAGER = 'AD_MANAGER';
  /**
   * Originated from Search Ads 360.
   */
  public const AUDIENCE_SOURCE_SEARCH_ADS_360 = 'SEARCH_ADS_360';
  /**
   * Originated from Youtube.
   */
  public const AUDIENCE_SOURCE_YOUTUBE = 'YOUTUBE';
  /**
   * Originated from Ads Data Hub.
   */
  public const AUDIENCE_SOURCE_ADS_DATA_HUB = 'ADS_DATA_HUB';
  /**
   * Default value when type is not specified or is unknown.
   */
  public const AUDIENCE_TYPE_AUDIENCE_TYPE_UNSPECIFIED = 'AUDIENCE_TYPE_UNSPECIFIED';
  /**
   * Audience was generated through matching customers to known contact
   * information.
   */
  public const AUDIENCE_TYPE_CUSTOMER_MATCH_CONTACT_INFO = 'CUSTOMER_MATCH_CONTACT_INFO';
  /**
   * Audience was generated through matching customers to known Mobile device
   * IDs.
   */
  public const AUDIENCE_TYPE_CUSTOMER_MATCH_DEVICE_ID = 'CUSTOMER_MATCH_DEVICE_ID';
  /**
   * Audience was generated through matching customers to known User IDs.
   */
  public const AUDIENCE_TYPE_CUSTOMER_MATCH_USER_ID = 'CUSTOMER_MATCH_USER_ID';
  /**
   * Audience was created based on campaign activity.
   *
   * @deprecated
   */
  public const AUDIENCE_TYPE_ACTIVITY_BASED = 'ACTIVITY_BASED';
  /**
   * Audience was created based on excluding the number of impressions they were
   * served.
   *
   * @deprecated
   */
  public const AUDIENCE_TYPE_FREQUENCY_CAP = 'FREQUENCY_CAP';
  /**
   * Audience was created based on custom variables attached to pixel.
   */
  public const AUDIENCE_TYPE_TAG_BASED = 'TAG_BASED';
  /**
   * Audience was created based on past interactions with videos, YouTube ads,
   * or YouTube channel.
   */
  public const AUDIENCE_TYPE_YOUTUBE_USERS = 'YOUTUBE_USERS';
  /**
   * Audience has been licensed for use from a third party.
   */
  public const AUDIENCE_TYPE_THIRD_PARTY = 'THIRD_PARTY';
  /**
   * Audience provided by commerce partners for a fee.
   */
  public const AUDIENCE_TYPE_COMMERCE = 'COMMERCE';
  /**
   * Audience for Linear TV content.
   */
  public const AUDIENCE_TYPE_LINEAR = 'LINEAR';
  /**
   * Audience provided by an agency.
   */
  public const AUDIENCE_TYPE_AGENCY = 'AGENCY';
  /**
   * Default value when type is not specified or is unknown.
   */
  public const FIRST_PARTY_AND_PARTNER_AUDIENCE_TYPE_FIRST_PARTY_AND_PARTNER_AUDIENCE_TYPE_UNSPECIFIED = 'FIRST_PARTY_AND_PARTNER_AUDIENCE_TYPE_UNSPECIFIED';
  /**
   * Audience that is created via usage of client data.
   */
  public const FIRST_PARTY_AND_PARTNER_AUDIENCE_TYPE_TYPE_FIRST_PARTY = 'TYPE_FIRST_PARTY';
  /**
   * Audience that is provided by Third Party data providers.
   */
  public const FIRST_PARTY_AND_PARTNER_AUDIENCE_TYPE_TYPE_PARTNER = 'TYPE_PARTNER';
  /**
   * Output only. The estimated audience size for the Display network in the
   * past month. If the size is less than 1000, the number will be hidden and 0
   * will be returned due to privacy reasons. Otherwise, the number will be
   * rounded off to two significant digits. Only returned in GET request.
   *
   * @var string
   */
  public $activeDisplayAudienceSize;
  /**
   * Optional. The app_id matches with the type of the mobile_device_ids being
   * uploaded. Only applicable to audience_type `CUSTOMER_MATCH_DEVICE_ID`
   *
   * @var string
   */
  public $appId;
  /**
   * Output only. The source of the audience.
   *
   * @var string
   */
  public $audienceSource;
  /**
   * Immutable. The type of the audience.
   *
   * @var string
   */
  public $audienceType;
  protected $contactInfoListType = ContactInfoList::class;
  protected $contactInfoListDataType = '';
  /**
   * Optional. The user-provided description of the audience. Only applicable to
   * first party audiences.
   *
   * @var string
   */
  public $description;
  /**
   * Output only. The estimated audience size for the Display network. If the
   * size is less than 1000, the number will be hidden and 0 will be returned
   * due to privacy reasons. Otherwise, the number will be rounded off to two
   * significant digits. Only returned in GET request.
   *
   * @var string
   */
  public $displayAudienceSize;
  /**
   * Output only. The estimated desktop audience size in Display network. If the
   * size is less than 1000, the number will be hidden and 0 will be returned
   * due to privacy reasons. Otherwise, the number will be rounded off to two
   * significant digits. Only applicable to first party audiences. Only returned
   * in GET request.
   *
   * @var string
   */
  public $displayDesktopAudienceSize;
  /**
   * Output only. The estimated mobile app audience size in Display network. If
   * the size is less than 1000, the number will be hidden and 0 will be
   * returned due to privacy reasons. Otherwise, the number will be rounded off
   * to two significant digits. Only applicable to first party audiences. Only
   * returned in GET request.
   *
   * @var string
   */
  public $displayMobileAppAudienceSize;
  /**
   * Output only. The estimated mobile web audience size in Display network. If
   * the size is less than 1000, the number will be hidden and 0 will be
   * returned due to privacy reasons. Otherwise, the number will be rounded off
   * to two significant digits. Only applicable to first party audiences. Only
   * returned in GET request.
   *
   * @var string
   */
  public $displayMobileWebAudienceSize;
  /**
   * Optional. The display name of the first party and partner audience.
   *
   * @var string
   */
  public $displayName;
  /**
   * Identifier. The unique ID of the first party and partner audience. Assigned
   * by the system.
   *
   * @var string
   */
  public $firstPartyAndPartnerAudienceId;
  /**
   * Output only. Whether the audience is a first party and partner audience.
   *
   * @var string
   */
  public $firstPartyAndPartnerAudienceType;
  /**
   * Output only. The estimated audience size for Gmail network. If the size is
   * less than 1000, the number will be hidden and 0 will be returned due to
   * privacy reasons. Otherwise, the number will be rounded off to two
   * significant digits. Only applicable to first party audiences. Only returned
   * in GET request.
   *
   * @var string
   */
  public $gmailAudienceSize;
  /**
   * Optional. The duration in days that an entry remains in the audience after
   * the qualifying event. The set value must be greater than 0 and less than or
   * equal to 540. Only applicable to first party audiences. This field is
   * required if one of the following audience_type is used: *
   * `CUSTOMER_MATCH_CONTACT_INFO` * `CUSTOMER_MATCH_DEVICE_ID`
   *
   * @var string
   */
  public $membershipDurationDays;
  protected $mobileDeviceIdListType = MobileDeviceIdList::class;
  protected $mobileDeviceIdListDataType = '';
  /**
   * Output only. The resource name of the first party and partner audience.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The estimated audience size for YouTube network. If the size
   * is less than 1000, the number will be hidden and 0 will be returned due to
   * privacy reasons. Otherwise, the number will be rounded off to two
   * significant digits. Only applicable to first party audiences. Only returned
   * in GET request.
   *
   * @var string
   */
  public $youtubeAudienceSize;

  /**
   * Output only. The estimated audience size for the Display network in the
   * past month. If the size is less than 1000, the number will be hidden and 0
   * will be returned due to privacy reasons. Otherwise, the number will be
   * rounded off to two significant digits. Only returned in GET request.
   *
   * @param string $activeDisplayAudienceSize
   */
  public function setActiveDisplayAudienceSize($activeDisplayAudienceSize)
  {
    $this->activeDisplayAudienceSize = $activeDisplayAudienceSize;
  }
  /**
   * @return string
   */
  public function getActiveDisplayAudienceSize()
  {
    return $this->activeDisplayAudienceSize;
  }
  /**
   * Optional. The app_id matches with the type of the mobile_device_ids being
   * uploaded. Only applicable to audience_type `CUSTOMER_MATCH_DEVICE_ID`
   *
   * @param string $appId
   */
  public function setAppId($appId)
  {
    $this->appId = $appId;
  }
  /**
   * @return string
   */
  public function getAppId()
  {
    return $this->appId;
  }
  /**
   * Output only. The source of the audience.
   *
   * Accepted values: AUDIENCE_SOURCE_UNSPECIFIED, DISPLAY_VIDEO_360,
   * CAMPAIGN_MANAGER, AD_MANAGER, SEARCH_ADS_360, YOUTUBE, ADS_DATA_HUB
   *
   * @param self::AUDIENCE_SOURCE_* $audienceSource
   */
  public function setAudienceSource($audienceSource)
  {
    $this->audienceSource = $audienceSource;
  }
  /**
   * @return self::AUDIENCE_SOURCE_*
   */
  public function getAudienceSource()
  {
    return $this->audienceSource;
  }
  /**
   * Immutable. The type of the audience.
   *
   * Accepted values: AUDIENCE_TYPE_UNSPECIFIED, CUSTOMER_MATCH_CONTACT_INFO,
   * CUSTOMER_MATCH_DEVICE_ID, CUSTOMER_MATCH_USER_ID, ACTIVITY_BASED,
   * FREQUENCY_CAP, TAG_BASED, YOUTUBE_USERS, THIRD_PARTY, COMMERCE, LINEAR,
   * AGENCY
   *
   * @param self::AUDIENCE_TYPE_* $audienceType
   */
  public function setAudienceType($audienceType)
  {
    $this->audienceType = $audienceType;
  }
  /**
   * @return self::AUDIENCE_TYPE_*
   */
  public function getAudienceType()
  {
    return $this->audienceType;
  }
  /**
   * Input only. A list of contact information to define the initial audience
   * members. Only applicable to audience_type `CUSTOMER_MATCH_CONTACT_INFO`
   *
   * @param ContactInfoList $contactInfoList
   */
  public function setContactInfoList(ContactInfoList $contactInfoList)
  {
    $this->contactInfoList = $contactInfoList;
  }
  /**
   * @return ContactInfoList
   */
  public function getContactInfoList()
  {
    return $this->contactInfoList;
  }
  /**
   * Optional. The user-provided description of the audience. Only applicable to
   * first party audiences.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Output only. The estimated audience size for the Display network. If the
   * size is less than 1000, the number will be hidden and 0 will be returned
   * due to privacy reasons. Otherwise, the number will be rounded off to two
   * significant digits. Only returned in GET request.
   *
   * @param string $displayAudienceSize
   */
  public function setDisplayAudienceSize($displayAudienceSize)
  {
    $this->displayAudienceSize = $displayAudienceSize;
  }
  /**
   * @return string
   */
  public function getDisplayAudienceSize()
  {
    return $this->displayAudienceSize;
  }
  /**
   * Output only. The estimated desktop audience size in Display network. If the
   * size is less than 1000, the number will be hidden and 0 will be returned
   * due to privacy reasons. Otherwise, the number will be rounded off to two
   * significant digits. Only applicable to first party audiences. Only returned
   * in GET request.
   *
   * @param string $displayDesktopAudienceSize
   */
  public function setDisplayDesktopAudienceSize($displayDesktopAudienceSize)
  {
    $this->displayDesktopAudienceSize = $displayDesktopAudienceSize;
  }
  /**
   * @return string
   */
  public function getDisplayDesktopAudienceSize()
  {
    return $this->displayDesktopAudienceSize;
  }
  /**
   * Output only. The estimated mobile app audience size in Display network. If
   * the size is less than 1000, the number will be hidden and 0 will be
   * returned due to privacy reasons. Otherwise, the number will be rounded off
   * to two significant digits. Only applicable to first party audiences. Only
   * returned in GET request.
   *
   * @param string $displayMobileAppAudienceSize
   */
  public function setDisplayMobileAppAudienceSize($displayMobileAppAudienceSize)
  {
    $this->displayMobileAppAudienceSize = $displayMobileAppAudienceSize;
  }
  /**
   * @return string
   */
  public function getDisplayMobileAppAudienceSize()
  {
    return $this->displayMobileAppAudienceSize;
  }
  /**
   * Output only. The estimated mobile web audience size in Display network. If
   * the size is less than 1000, the number will be hidden and 0 will be
   * returned due to privacy reasons. Otherwise, the number will be rounded off
   * to two significant digits. Only applicable to first party audiences. Only
   * returned in GET request.
   *
   * @param string $displayMobileWebAudienceSize
   */
  public function setDisplayMobileWebAudienceSize($displayMobileWebAudienceSize)
  {
    $this->displayMobileWebAudienceSize = $displayMobileWebAudienceSize;
  }
  /**
   * @return string
   */
  public function getDisplayMobileWebAudienceSize()
  {
    return $this->displayMobileWebAudienceSize;
  }
  /**
   * Optional. The display name of the first party and partner audience.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Identifier. The unique ID of the first party and partner audience. Assigned
   * by the system.
   *
   * @param string $firstPartyAndPartnerAudienceId
   */
  public function setFirstPartyAndPartnerAudienceId($firstPartyAndPartnerAudienceId)
  {
    $this->firstPartyAndPartnerAudienceId = $firstPartyAndPartnerAudienceId;
  }
  /**
   * @return string
   */
  public function getFirstPartyAndPartnerAudienceId()
  {
    return $this->firstPartyAndPartnerAudienceId;
  }
  /**
   * Output only. Whether the audience is a first party and partner audience.
   *
   * Accepted values: FIRST_PARTY_AND_PARTNER_AUDIENCE_TYPE_UNSPECIFIED,
   * TYPE_FIRST_PARTY, TYPE_PARTNER
   *
   * @param self::FIRST_PARTY_AND_PARTNER_AUDIENCE_TYPE_* $firstPartyAndPartnerAudienceType
   */
  public function setFirstPartyAndPartnerAudienceType($firstPartyAndPartnerAudienceType)
  {
    $this->firstPartyAndPartnerAudienceType = $firstPartyAndPartnerAudienceType;
  }
  /**
   * @return self::FIRST_PARTY_AND_PARTNER_AUDIENCE_TYPE_*
   */
  public function getFirstPartyAndPartnerAudienceType()
  {
    return $this->firstPartyAndPartnerAudienceType;
  }
  /**
   * Output only. The estimated audience size for Gmail network. If the size is
   * less than 1000, the number will be hidden and 0 will be returned due to
   * privacy reasons. Otherwise, the number will be rounded off to two
   * significant digits. Only applicable to first party audiences. Only returned
   * in GET request.
   *
   * @param string $gmailAudienceSize
   */
  public function setGmailAudienceSize($gmailAudienceSize)
  {
    $this->gmailAudienceSize = $gmailAudienceSize;
  }
  /**
   * @return string
   */
  public function getGmailAudienceSize()
  {
    return $this->gmailAudienceSize;
  }
  /**
   * Optional. The duration in days that an entry remains in the audience after
   * the qualifying event. The set value must be greater than 0 and less than or
   * equal to 540. Only applicable to first party audiences. This field is
   * required if one of the following audience_type is used: *
   * `CUSTOMER_MATCH_CONTACT_INFO` * `CUSTOMER_MATCH_DEVICE_ID`
   *
   * @param string $membershipDurationDays
   */
  public function setMembershipDurationDays($membershipDurationDays)
  {
    $this->membershipDurationDays = $membershipDurationDays;
  }
  /**
   * @return string
   */
  public function getMembershipDurationDays()
  {
    return $this->membershipDurationDays;
  }
  /**
   * Input only. A list of mobile device IDs to define the initial audience
   * members. Only applicable to audience_type `CUSTOMER_MATCH_DEVICE_ID`
   *
   * @param MobileDeviceIdList $mobileDeviceIdList
   */
  public function setMobileDeviceIdList(MobileDeviceIdList $mobileDeviceIdList)
  {
    $this->mobileDeviceIdList = $mobileDeviceIdList;
  }
  /**
   * @return MobileDeviceIdList
   */
  public function getMobileDeviceIdList()
  {
    return $this->mobileDeviceIdList;
  }
  /**
   * Output only. The resource name of the first party and partner audience.
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
   * Output only. The estimated audience size for YouTube network. If the size
   * is less than 1000, the number will be hidden and 0 will be returned due to
   * privacy reasons. Otherwise, the number will be rounded off to two
   * significant digits. Only applicable to first party audiences. Only returned
   * in GET request.
   *
   * @param string $youtubeAudienceSize
   */
  public function setYoutubeAudienceSize($youtubeAudienceSize)
  {
    $this->youtubeAudienceSize = $youtubeAudienceSize;
  }
  /**
   * @return string
   */
  public function getYoutubeAudienceSize()
  {
    return $this->youtubeAudienceSize;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FirstPartyAndPartnerAudience::class, 'Google_Service_DisplayVideo_FirstPartyAndPartnerAudience');
