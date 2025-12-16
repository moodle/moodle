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

class Account extends \Google\Model
{
  protected $childLinkType = AccountChildLink::class;
  protected $childLinkDataType = '';
  /**
   * Time the account was created.
   *
   * @var string
   */
  public $created;
  /**
   * Account ID.
   *
   * @var string
   */
  public $id;
  /**
   * Resource type for Analytics account.
   *
   * @var string
   */
  public $kind;
  /**
   * Account name.
   *
   * @var string
   */
  public $name;
  protected $permissionsType = AccountPermissions::class;
  protected $permissionsDataType = '';
  /**
   * Link for this account.
   *
   * @var string
   */
  public $selfLink;
  /**
   * Indicates whether this account is starred or not.
   *
   * @var bool
   */
  public $starred;
  /**
   * Time the account was last modified.
   *
   * @var string
   */
  public $updated;

  /**
   * Child link for an account entry. Points to the list of web properties for
   * this account.
   *
   * @param AccountChildLink $childLink
   */
  public function setChildLink(AccountChildLink $childLink)
  {
    $this->childLink = $childLink;
  }
  /**
   * @return AccountChildLink
   */
  public function getChildLink()
  {
    return $this->childLink;
  }
  /**
   * Time the account was created.
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
   * Account ID.
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
   * Resource type for Analytics account.
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
   * Account name.
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
   * Permissions the user has for this account.
   *
   * @param AccountPermissions $permissions
   */
  public function setPermissions(AccountPermissions $permissions)
  {
    $this->permissions = $permissions;
  }
  /**
   * @return AccountPermissions
   */
  public function getPermissions()
  {
    return $this->permissions;
  }
  /**
   * Link for this account.
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
   * Indicates whether this account is starred or not.
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
   * Time the account was last modified.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Account::class, 'Google_Service_Analytics_Account');
