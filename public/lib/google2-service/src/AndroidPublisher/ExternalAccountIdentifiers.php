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

class ExternalAccountIdentifiers extends \Google\Model
{
  /**
   * User account identifier in the third-party service. Only present if account
   * linking happened as part of the subscription purchase flow.
   *
   * @var string
   */
  public $externalAccountId;
  /**
   * An obfuscated version of the id that is uniquely associated with the user's
   * account in your app. Present for the following purchases: * If account
   * linking happened as part of the subscription purchase flow. * It was
   * specified using https://developer.android.com/reference/com/android/billing
   * client/api/BillingFlowParams.Builder#setobfuscatedaccountid when the
   * purchase was made.
   *
   * @var string
   */
  public $obfuscatedExternalAccountId;
  /**
   * An obfuscated version of the id that is uniquely associated with the user's
   * profile in your app. Only present if specified using https://developer.andr
   * oid.com/reference/com/android/billingclient/api/BillingFlowParams.Builder#s
   * etobfuscatedprofileid when the purchase was made.
   *
   * @var string
   */
  public $obfuscatedExternalProfileId;

  /**
   * User account identifier in the third-party service. Only present if account
   * linking happened as part of the subscription purchase flow.
   *
   * @param string $externalAccountId
   */
  public function setExternalAccountId($externalAccountId)
  {
    $this->externalAccountId = $externalAccountId;
  }
  /**
   * @return string
   */
  public function getExternalAccountId()
  {
    return $this->externalAccountId;
  }
  /**
   * An obfuscated version of the id that is uniquely associated with the user's
   * account in your app. Present for the following purchases: * If account
   * linking happened as part of the subscription purchase flow. * It was
   * specified using https://developer.android.com/reference/com/android/billing
   * client/api/BillingFlowParams.Builder#setobfuscatedaccountid when the
   * purchase was made.
   *
   * @param string $obfuscatedExternalAccountId
   */
  public function setObfuscatedExternalAccountId($obfuscatedExternalAccountId)
  {
    $this->obfuscatedExternalAccountId = $obfuscatedExternalAccountId;
  }
  /**
   * @return string
   */
  public function getObfuscatedExternalAccountId()
  {
    return $this->obfuscatedExternalAccountId;
  }
  /**
   * An obfuscated version of the id that is uniquely associated with the user's
   * profile in your app. Only present if specified using https://developer.andr
   * oid.com/reference/com/android/billingclient/api/BillingFlowParams.Builder#s
   * etobfuscatedprofileid when the purchase was made.
   *
   * @param string $obfuscatedExternalProfileId
   */
  public function setObfuscatedExternalProfileId($obfuscatedExternalProfileId)
  {
    $this->obfuscatedExternalProfileId = $obfuscatedExternalProfileId;
  }
  /**
   * @return string
   */
  public function getObfuscatedExternalProfileId()
  {
    return $this->obfuscatedExternalProfileId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ExternalAccountIdentifiers::class, 'Google_Service_AndroidPublisher_ExternalAccountIdentifiers');
