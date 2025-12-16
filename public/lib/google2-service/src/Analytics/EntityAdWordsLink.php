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

class EntityAdWordsLink extends \Google\Collection
{
  protected $collection_key = 'profileIds';
  protected $adWordsAccountsType = AdWordsAccount::class;
  protected $adWordsAccountsDataType = 'array';
  protected $entityType = EntityAdWordsLinkEntity::class;
  protected $entityDataType = '';
  /**
   * Entity Google Ads link ID
   *
   * @var string
   */
  public $id;
  /**
   * Resource type for entity Google Ads link.
   *
   * @var string
   */
  public $kind;
  /**
   * Name of the link. This field is required when creating a Google Ads link.
   *
   * @var string
   */
  public $name;
  /**
   * IDs of linked Views (Profiles) represented as strings.
   *
   * @var string[]
   */
  public $profileIds;
  /**
   * URL link for this Google Analytics - Google Ads link.
   *
   * @var string
   */
  public $selfLink;

  /**
   * A list of Google Ads client accounts. These cannot be MCC accounts. This
   * field is required when creating a Google Ads link. It cannot be empty.
   *
   * @param AdWordsAccount[] $adWordsAccounts
   */
  public function setAdWordsAccounts($adWordsAccounts)
  {
    $this->adWordsAccounts = $adWordsAccounts;
  }
  /**
   * @return AdWordsAccount[]
   */
  public function getAdWordsAccounts()
  {
    return $this->adWordsAccounts;
  }
  /**
   * Web property being linked.
   *
   * @param EntityAdWordsLinkEntity $entity
   */
  public function setEntity(EntityAdWordsLinkEntity $entity)
  {
    $this->entity = $entity;
  }
  /**
   * @return EntityAdWordsLinkEntity
   */
  public function getEntity()
  {
    return $this->entity;
  }
  /**
   * Entity Google Ads link ID
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
   * Resource type for entity Google Ads link.
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
   * Name of the link. This field is required when creating a Google Ads link.
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
   * IDs of linked Views (Profiles) represented as strings.
   *
   * @param string[] $profileIds
   */
  public function setProfileIds($profileIds)
  {
    $this->profileIds = $profileIds;
  }
  /**
   * @return string[]
   */
  public function getProfileIds()
  {
    return $this->profileIds;
  }
  /**
   * URL link for this Google Analytics - Google Ads link.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EntityAdWordsLink::class, 'Google_Service_Analytics_EntityAdWordsLink');
