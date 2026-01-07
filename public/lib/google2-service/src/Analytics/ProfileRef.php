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

class ProfileRef extends \Google\Model
{
  /**
   * Account ID to which this view (profile) belongs.
   *
   * @var string
   */
  public $accountId;
  /**
   * Link for this view (profile).
   *
   * @var string
   */
  public $href;
  /**
   * View (Profile) ID.
   *
   * @var string
   */
  public $id;
  /**
   * Internal ID for the web property to which this view (profile) belongs.
   *
   * @var string
   */
  public $internalWebPropertyId;
  /**
   * Analytics view (profile) reference.
   *
   * @var string
   */
  public $kind;
  /**
   * Name of this view (profile).
   *
   * @var string
   */
  public $name;
  /**
   * Web property ID of the form UA-XXXXX-YY to which this view (profile)
   * belongs.
   *
   * @var string
   */
  public $webPropertyId;

  /**
   * Account ID to which this view (profile) belongs.
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
   * Link for this view (profile).
   *
   * @param string $href
   */
  public function setHref($href)
  {
    $this->href = $href;
  }
  /**
   * @return string
   */
  public function getHref()
  {
    return $this->href;
  }
  /**
   * View (Profile) ID.
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
   * Internal ID for the web property to which this view (profile) belongs.
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
   * Analytics view (profile) reference.
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
   * Name of this view (profile).
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
   * Web property ID of the form UA-XXXXX-YY to which this view (profile)
   * belongs.
   *
   * @param string $webPropertyId
   */
  public function setWebPropertyId($webPropertyId)
  {
    $this->webPropertyId = $webPropertyId;
  }
  /**
   * @return string
   */
  public function getWebPropertyId()
  {
    return $this->webPropertyId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProfileRef::class, 'Google_Service_Analytics_ProfileRef');
