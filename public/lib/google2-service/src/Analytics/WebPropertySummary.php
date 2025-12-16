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

class WebPropertySummary extends \Google\Collection
{
  protected $collection_key = 'profiles';
  /**
   * Web property ID of the form UA-XXXXX-YY.
   *
   * @var string
   */
  public $id;
  /**
   * Internal ID for this web property.
   *
   * @var string
   */
  public $internalWebPropertyId;
  /**
   * Resource type for Analytics WebPropertySummary.
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
   * Web property name.
   *
   * @var string
   */
  public $name;
  protected $profilesType = ProfileSummary::class;
  protected $profilesDataType = 'array';
  /**
   * Indicates whether this web property is starred or not.
   *
   * @var bool
   */
  public $starred;
  /**
   * Website url for this web property.
   *
   * @var string
   */
  public $websiteUrl;

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
   * Resource type for Analytics WebPropertySummary.
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
   * Web property name.
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
   * List of profiles under this web property.
   *
   * @param ProfileSummary[] $profiles
   */
  public function setProfiles($profiles)
  {
    $this->profiles = $profiles;
  }
  /**
   * @return ProfileSummary[]
   */
  public function getProfiles()
  {
    return $this->profiles;
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
class_alias(WebPropertySummary::class, 'Google_Service_Analytics_WebPropertySummary');
