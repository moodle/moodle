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

namespace Google\Service\AndroidPublisher;

class OutOfAppPurchaseContext extends \Google\Model
{
  protected $expiredExternalAccountIdentifiersType = ExternalAccountIdentifiers::class;
  protected $expiredExternalAccountIdentifiersDataType = '';
  /**
   * The purchase token of the last expired subscription. This purchase token
   * must only be used to help identify the user if the link between the
   * purchaseToken and user is stored in your database. This cannot be used to
   * call the Google Developer API if it has been more than 60 days since
   * expiry.
   *
   * @var string
   */
  public $expiredPurchaseToken;

  /**
   * User account identifier from the last expired subscription for this SKU.
   *
   * @param ExternalAccountIdentifiers $expiredExternalAccountIdentifiers
   */
  public function setExpiredExternalAccountIdentifiers(ExternalAccountIdentifiers $expiredExternalAccountIdentifiers)
  {
    $this->expiredExternalAccountIdentifiers = $expiredExternalAccountIdentifiers;
  }
  /**
   * @return ExternalAccountIdentifiers
   */
  public function getExpiredExternalAccountIdentifiers()
  {
    return $this->expiredExternalAccountIdentifiers;
  }
  /**
   * The purchase token of the last expired subscription. This purchase token
   * must only be used to help identify the user if the link between the
   * purchaseToken and user is stored in your database. This cannot be used to
   * call the Google Developer API if it has been more than 60 days since
   * expiry.
   *
   * @param string $expiredPurchaseToken
   */
  public function setExpiredPurchaseToken($expiredPurchaseToken)
  {
    $this->expiredPurchaseToken = $expiredPurchaseToken;
  }
  /**
   * @return string
   */
  public function getExpiredPurchaseToken()
  {
    return $this->expiredPurchaseToken;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OutOfAppPurchaseContext::class, 'Google_Service_AndroidPublisher_OutOfAppPurchaseContext');
