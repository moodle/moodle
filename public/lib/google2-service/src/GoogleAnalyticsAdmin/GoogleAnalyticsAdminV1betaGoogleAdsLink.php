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

namespace Google\Service\GoogleAnalyticsAdmin;

class GoogleAnalyticsAdminV1betaGoogleAdsLink extends \Google\Model
{
  /**
   * Enable personalized advertising features with this integration.
   * Automatically publish my Google Analytics audience lists and Google
   * Analytics remarketing events/parameters to the linked Google Ads account.
   * If this field is not set on create/update, it will be defaulted to true.
   *
   * @var bool
   */
  public $adsPersonalizationEnabled;
  /**
   * Output only. If true, this link is for a Google Ads manager account.
   *
   * @var bool
   */
  public $canManageClients;
  /**
   * Output only. Time when this link was originally created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. Email address of the user that created the link. An empty
   * string will be returned if the email address can't be retrieved.
   *
   * @var string
   */
  public $creatorEmailAddress;
  /**
   * Immutable. Google Ads customer ID.
   *
   * @var string
   */
  public $customerId;
  /**
   * Output only. Format:
   * properties/{propertyId}/googleAdsLinks/{googleAdsLinkId} Note:
   * googleAdsLinkId is not the Google Ads customer ID.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Time when this link was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Enable personalized advertising features with this integration.
   * Automatically publish my Google Analytics audience lists and Google
   * Analytics remarketing events/parameters to the linked Google Ads account.
   * If this field is not set on create/update, it will be defaulted to true.
   *
   * @param bool $adsPersonalizationEnabled
   */
  public function setAdsPersonalizationEnabled($adsPersonalizationEnabled)
  {
    $this->adsPersonalizationEnabled = $adsPersonalizationEnabled;
  }
  /**
   * @return bool
   */
  public function getAdsPersonalizationEnabled()
  {
    return $this->adsPersonalizationEnabled;
  }
  /**
   * Output only. If true, this link is for a Google Ads manager account.
   *
   * @param bool $canManageClients
   */
  public function setCanManageClients($canManageClients)
  {
    $this->canManageClients = $canManageClients;
  }
  /**
   * @return bool
   */
  public function getCanManageClients()
  {
    return $this->canManageClients;
  }
  /**
   * Output only. Time when this link was originally created.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Output only. Email address of the user that created the link. An empty
   * string will be returned if the email address can't be retrieved.
   *
   * @param string $creatorEmailAddress
   */
  public function setCreatorEmailAddress($creatorEmailAddress)
  {
    $this->creatorEmailAddress = $creatorEmailAddress;
  }
  /**
   * @return string
   */
  public function getCreatorEmailAddress()
  {
    return $this->creatorEmailAddress;
  }
  /**
   * Immutable. Google Ads customer ID.
   *
   * @param string $customerId
   */
  public function setCustomerId($customerId)
  {
    $this->customerId = $customerId;
  }
  /**
   * @return string
   */
  public function getCustomerId()
  {
    return $this->customerId;
  }
  /**
   * Output only. Format:
   * properties/{propertyId}/googleAdsLinks/{googleAdsLinkId} Note:
   * googleAdsLinkId is not the Google Ads customer ID.
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
   * Output only. Time when this link was last updated.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAnalyticsAdminV1betaGoogleAdsLink::class, 'Google_Service_GoogleAnalyticsAdmin_GoogleAnalyticsAdminV1betaGoogleAdsLink');
