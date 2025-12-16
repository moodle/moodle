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

class LinkedForeignAccount extends \Google\Model
{
  /**
   * Account ID to which this linked foreign account belongs.
   *
   * @var string
   */
  public $accountId;
  /**
   * Boolean indicating whether this is eligible for search.
   *
   * @var bool
   */
  public $eligibleForSearch;
  /**
   * Entity ad account link ID.
   *
   * @var string
   */
  public $id;
  /**
   * Internal ID for the web property to which this linked foreign account
   * belongs.
   *
   * @var string
   */
  public $internalWebPropertyId;
  /**
   * Resource type for linked foreign account.
   *
   * @var string
   */
  public $kind;
  /**
   * The foreign account ID. For example the an Google Ads `linkedAccountId` has
   * the following format XXX-XXX-XXXX.
   *
   * @var string
   */
  public $linkedAccountId;
  /**
   * Remarketing audience ID to which this linked foreign account belongs.
   *
   * @var string
   */
  public $remarketingAudienceId;
  /**
   * The status of this foreign account link.
   *
   * @var string
   */
  public $status;
  /**
   * The type of the foreign account. For example, `ADWORDS_LINKS`, `DBM_LINKS`,
   * `MCC_LINKS` or `OPTIMIZE`.
   *
   * @var string
   */
  public $type;
  /**
   * Web property ID of the form UA-XXXXX-YY to which this linked foreign
   * account belongs.
   *
   * @var string
   */
  public $webPropertyId;

  /**
   * Account ID to which this linked foreign account belongs.
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
   * Boolean indicating whether this is eligible for search.
   *
   * @param bool $eligibleForSearch
   */
  public function setEligibleForSearch($eligibleForSearch)
  {
    $this->eligibleForSearch = $eligibleForSearch;
  }
  /**
   * @return bool
   */
  public function getEligibleForSearch()
  {
    return $this->eligibleForSearch;
  }
  /**
   * Entity ad account link ID.
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
   * Internal ID for the web property to which this linked foreign account
   * belongs.
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
   * Resource type for linked foreign account.
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
   * The foreign account ID. For example the an Google Ads `linkedAccountId` has
   * the following format XXX-XXX-XXXX.
   *
   * @param string $linkedAccountId
   */
  public function setLinkedAccountId($linkedAccountId)
  {
    $this->linkedAccountId = $linkedAccountId;
  }
  /**
   * @return string
   */
  public function getLinkedAccountId()
  {
    return $this->linkedAccountId;
  }
  /**
   * Remarketing audience ID to which this linked foreign account belongs.
   *
   * @param string $remarketingAudienceId
   */
  public function setRemarketingAudienceId($remarketingAudienceId)
  {
    $this->remarketingAudienceId = $remarketingAudienceId;
  }
  /**
   * @return string
   */
  public function getRemarketingAudienceId()
  {
    return $this->remarketingAudienceId;
  }
  /**
   * The status of this foreign account link.
   *
   * @param string $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return string
   */
  public function getStatus()
  {
    return $this->status;
  }
  /**
   * The type of the foreign account. For example, `ADWORDS_LINKS`, `DBM_LINKS`,
   * `MCC_LINKS` or `OPTIMIZE`.
   *
   * @param string $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }
  /**
   * Web property ID of the form UA-XXXXX-YY to which this linked foreign
   * account belongs.
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
class_alias(LinkedForeignAccount::class, 'Google_Service_Analytics_LinkedForeignAccount');
