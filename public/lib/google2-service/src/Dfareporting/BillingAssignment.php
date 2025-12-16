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

class BillingAssignment extends \Google\Model
{
  /**
   * ID of the account associated with the billing assignment.This is a read-
   * only, auto-generated field.
   *
   * @var string
   */
  public $accountId;
  /**
   * ID of the advertiser associated with the billing assignment.Wildcard (*)
   * means this assignment is not limited to a single advertiser
   *
   * @var string
   */
  public $advertiserId;
  /**
   * ID of the campaign associated with the billing assignment. Wildcard (*)
   * means this assignment is not limited to a single campaign
   *
   * @var string
   */
  public $campaignId;
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "dfareporting#billingAssignment".
   *
   * @var string
   */
  public $kind;
  /**
   * ID of the subaccount associated with the billing assignment.Wildcard (*)
   * means this assignment is not limited to a single subaccountThis is a read-
   * only, auto-generated field.
   *
   * @var string
   */
  public $subaccountId;

  /**
   * ID of the account associated with the billing assignment.This is a read-
   * only, auto-generated field.
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
   * ID of the advertiser associated with the billing assignment.Wildcard (*)
   * means this assignment is not limited to a single advertiser
   *
   * @param string $advertiserId
   */
  public function setAdvertiserId($advertiserId)
  {
    $this->advertiserId = $advertiserId;
  }
  /**
   * @return string
   */
  public function getAdvertiserId()
  {
    return $this->advertiserId;
  }
  /**
   * ID of the campaign associated with the billing assignment. Wildcard (*)
   * means this assignment is not limited to a single campaign
   *
   * @param string $campaignId
   */
  public function setCampaignId($campaignId)
  {
    $this->campaignId = $campaignId;
  }
  /**
   * @return string
   */
  public function getCampaignId()
  {
    return $this->campaignId;
  }
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "dfareporting#billingAssignment".
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
   * ID of the subaccount associated with the billing assignment.Wildcard (*)
   * means this assignment is not limited to a single subaccountThis is a read-
   * only, auto-generated field.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BillingAssignment::class, 'Google_Service_Dfareporting_BillingAssignment');
