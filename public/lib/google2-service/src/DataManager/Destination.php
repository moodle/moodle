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

namespace Google\Service\DataManager;

class Destination extends \Google\Model
{
  protected $linkedAccountType = ProductAccount::class;
  protected $linkedAccountDataType = '';
  protected $loginAccountType = ProductAccount::class;
  protected $loginAccountDataType = '';
  protected $operatingAccountType = ProductAccount::class;
  protected $operatingAccountDataType = '';
  /**
   * Required. The object within the product account to ingest into. For
   * example, a Google Ads audience ID, a Display & Video 360 audience ID or a
   * Google Ads conversion action ID.
   *
   * @var string
   */
  public $productDestinationId;
  /**
   * Optional. ID for this `Destination` resource, unique within the request.
   * Use to reference this `Destination` in the IngestEventsRequest and
   * IngestAudienceMembersRequest.
   *
   * @var string
   */
  public $reference;

  /**
   * Optional. An account that the calling user's `login_account` has access to,
   * through an established account link. For example, a data partner's
   * `login_account` might have access to a client's `linked_account`. The
   * partner might use this field to send data from the `linked_account` to
   * another `operating_account`.
   *
   * @param ProductAccount $linkedAccount
   */
  public function setLinkedAccount(ProductAccount $linkedAccount)
  {
    $this->linkedAccount = $linkedAccount;
  }
  /**
   * @return ProductAccount
   */
  public function getLinkedAccount()
  {
    return $this->linkedAccount;
  }
  /**
   * Optional. The account used to make this API call. To add or remove data
   * from the `operating_account`, this `login_account` must have write access
   * to the `operating_account`. For example, a manager account of the
   * `operating_account`, or an account with an established link to the
   * `operating_account`.
   *
   * @param ProductAccount $loginAccount
   */
  public function setLoginAccount(ProductAccount $loginAccount)
  {
    $this->loginAccount = $loginAccount;
  }
  /**
   * @return ProductAccount
   */
  public function getLoginAccount()
  {
    return $this->loginAccount;
  }
  /**
   * Required. The account to send the data to or remove the data from.
   *
   * @param ProductAccount $operatingAccount
   */
  public function setOperatingAccount(ProductAccount $operatingAccount)
  {
    $this->operatingAccount = $operatingAccount;
  }
  /**
   * @return ProductAccount
   */
  public function getOperatingAccount()
  {
    return $this->operatingAccount;
  }
  /**
   * Required. The object within the product account to ingest into. For
   * example, a Google Ads audience ID, a Display & Video 360 audience ID or a
   * Google Ads conversion action ID.
   *
   * @param string $productDestinationId
   */
  public function setProductDestinationId($productDestinationId)
  {
    $this->productDestinationId = $productDestinationId;
  }
  /**
   * @return string
   */
  public function getProductDestinationId()
  {
    return $this->productDestinationId;
  }
  /**
   * Optional. ID for this `Destination` resource, unique within the request.
   * Use to reference this `Destination` in the IngestEventsRequest and
   * IngestAudienceMembersRequest.
   *
   * @param string $reference
   */
  public function setReference($reference)
  {
    $this->reference = $reference;
  }
  /**
   * @return string
   */
  public function getReference()
  {
    return $this->reference;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Destination::class, 'Google_Service_DataManager_Destination');
