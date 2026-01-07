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

namespace Google\Service\Analytics;

class Webproperty extends \Google\Model
{
  /**
   * Account ID to which this web property belongs.
   *
   * @var string
   */
  public $accountId;
  protected $childLinkType = WebpropertyChildLink::class;
  protected $childLinkDataType = '';
  /**
   * Time this web property was created.
   *
   * @var string
   */
  public $created;
  /**
   * Set to true to reset the retention period of the user identifier with each
   * new event from that user (thus setting the expiration date to current time
   * plus retention period). Set to false to delete data associated with the
   * user identifier automatically after the rentention period. This property
   * cannot be set on insert.
   *
   * @var bool
   */
  public $dataRetentionResetOnNewActivity;
  /**
   * The length of time for which user and event data is retained. This property
   * cannot be set on insert.
   *
   * @var string
   */
  public $dataRetentionTtl;
  /**
   * Default view (profile) ID.
   *
   * @var string
   */
  public $defaultProfileId;
  /**
   * Web property ID of the form UA-XXXXX-YY.
   *
   * @var string
   */
  public $id;
  /**
   * The industry vertical/category selected for this web property.
   *
   * @var string
   */
  public $industryVertical;
  /**
   * Internal ID for this web property.
   *
   * @var string
   */
  public $internalWebPropertyId;
  /**
   * Resource type for Analytics WebProperty.
   *
   * @var string
   */
  public $kind;
  /**
   * Level for this web property. Possible values are STANDARD or PREMIUM.
   *
   * @var string
   */
  public $level;
  /**
   * Name of this web property.
   *
   * @var string
   */
  public $name;
  protected $parentLinkType = WebpropertyParentLink::class;
  protected $parentLinkDataType = '';
  protected $permissionsType = WebpropertyPermissions::class;
  protected $permissionsDataType = '';
  /**
   * View (Profile) count for this web property.
   *
   * @var int
   */
  public $profileCount;
  /**
   * Link for this web property.
   *
   * @var string
   */
  public $selfLink;
  /**
   * Indicates whether this web property is starred or not.
   *
   * @var bool
   */
  public $starred;
  /**
   * Time this web property was last modified.
   *
   * @var string
   */
  public $updated;
  /**
   * Website url for this web property.
   *
   * @var string
   */
  public $websiteUrl;

  /**
   * Account ID to which this web property belongs.
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
   * Child link for this web property. Points to the list of views (profiles)
   * for this web property.
   *
   * @param WebpropertyChildLink $childLink
   */
  public function setChildLink(WebpropertyChildLink $childLink)
  {
    $this->childLink = $childLink;
  }
  /**
   * @return WebpropertyChildLink
   */
  public function getChildLink()
  {
    return $this->childLink;
  }
  /**
   * Time this web property was created.
   *
   * @param string $created
   */
  public function setCreated($created)
  {
    $this->created = $created;
  }
  /**
   * @return string
   */
  public function getCreated()
  {
    return $this->created;
  }
  /**
   * Set to true to reset the retention period of the user identifier with each
   * new event from that user (thus setting the expiration date to current time
   * plus retention period). Set to false to delete data associated with the
   * user identifier automatically after the rentention period. This property
   * cannot be set on insert.
   *
   * @param bool $dataRetentionResetOnNewActivity
   */
  public function setDataRetentionResetOnNewActivity($dataRetentionResetOnNewActivity)
  {
    $this->dataRetentionResetOnNewActivity = $dataRetentionResetOnNewActivity;
  }
  /**
   * @return bool
   */
  public function getDataRetentionResetOnNewActivity()
  {
    return $this->dataRetentionResetOnNewActivity;
  }
  /**
   * The length of time for which user and event data is retained. This property
   * cannot be set on insert.
   *
   * @param string $dataRetentionTtl
   */
  public function setDataRetentionTtl($dataRetentionTtl)
  {
    $this->dataRetentionTtl = $dataRetentionTtl;
  }
  /**
   * @return string
   */
  public function getDataRetentionTtl()
  {
    return $this->dataRetentionTtl;
  }
  /**
   * Default view (profile) ID.
   *
   * @param string $defaultProfileId
   */
  public function setDefaultProfileId($defaultProfileId)
  {
    $this->defaultProfileId = $defaultProfileId;
  }
  /**
   * @return string
   */
  public function getDefaultProfileId()
  {
    return $this->defaultProfileId;
  }
  /**
   * Web property ID of the form UA-XXXXX-YY.
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
   * The industry vertical/category selected for this web property.
   *
   * @param string $industryVertical
   */
  public function setIndustryVertical($industryVertical)
  {
    $this->industryVertical = $industryVertical;
  }
  /**
   * @return string
   */
  public function getIndustryVertical()
  {
    return $this->industryVertical;
  }
  /**
   * Internal ID for this web property.
   *
   * @param string $internalWebPropertyId
   */
  public function setInternalWebPropertyId($internalWebPropertyId)
  {
    $this->internalWebPropertyId = $internalWebPropertyId;
  }
  /**
   * @return string
   */
  public function getInternalWebPropertyId()
  {
    return $this->internalWebPropertyId;
  }
  /**
   * Resource type for Analytics WebProperty.
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
   * Level for this web property. Possible values are STANDARD or PREMIUM.
   *
   * @param string $level
   */
  public function setLevel($level)
  {
    $this->level = $level;
  }
  /**
   * @return string
   */
  public function getLevel()
  {
    return $this->level;
  }
  /**
   * Name of this web property.
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
   * Parent link for this web property. Points to the account to which this web
   * property belongs.
   *
   * @param WebpropertyParentLink $parentLink
   */
  public function setParentLink(WebpropertyParentLink $parentLink)
  {
    $this->parentLink = $parentLink;
  }
  /**
   * @return WebpropertyParentLink
   */
  public function getParentLink()
  {
    return $this->parentLink;
  }
  /**
   * Permissions the user has for this web property.
   *
   * @param WebpropertyPermissions $permissions
   */
  public function setPermissions(WebpropertyPermissions $permissions)
  {
    $this->permissions = $permissions;
  }
  /**
   * @return WebpropertyPermissions
   */
  public function getPermissions()
  {
    return $this->permissions;
  }
  /**
   * View (Profile) count for this web property.
   *
   * @param int $profileCount
   */
  public function setProfileCount($profileCount)
  {
    $this->profileCount = $profileCount;
  }
  /**
   * @return int
   */
  public function getProfileCount()
  {
    return $this->profileCount;
  }
  /**
   * Link for this web property.
   *
   * @param string $selfLink
   */
  public function setSelfLink($selfLink)
  {
    $this->selfLink = $selfLink;
  }
  /**
   * @return string
   */
  public function getSelfLink()
  {
    return $this->selfLink;
  }
  /**
   * Indicates whether this web property is starred or not.
   *
   * @param bool $starred
   */
  public function setStarred($starred)
  {
    $this->starred = $starred;
  }
  /**
   * @return bool
   */
  public function getStarred()
  {
    return $this->starred;
  }
  /**
   * Time this web property was last modified.
   *
   * @param string $updated
   */
  public function setUpdated($updated)
  {
    $this->updated = $updated;
  }
  /**
   * @return string
   */
  public function getUpdated()
  {
    return $this->updated;
  }
  /**
   * Website url for this web property.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Webproperty::class, 'Google_Service_Analytics_Webproperty');
