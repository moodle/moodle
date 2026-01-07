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

class ExternalAccountIds extends \Google\Model
{
  /**
   * Optional. Specifies an optional obfuscated string that is uniquely
   * associated with the purchaser's user account in your app. If you pass this
   * value, Google Play can use it to detect irregular activity. Do not use this
   * field to store any Personally Identifiable Information (PII) such as emails
   * in cleartext. Attempting to store PII in this field will result in
   * purchases being blocked. Google Play recommends that you use either
   * encryption or a one-way hash to generate an obfuscated identifier to send
   * to Google Play. This identifier is limited to 64 characters. This field can
   * only be set for resubscription purchases. See https://developer.android.com
   * /reference/com/android/billingclient/api/BillingFlowParams.Builder#setobfus
   * catedaccountid to set this field for purchases made using the standard in-
   * app billing flow.
   *
   * @var string
   */
  public $obfuscatedAccountId;
  /**
   * Optional. Specifies an optional obfuscated string that is uniquely
   * associated with the purchaser's user profile in your app. If you pass this
   * value, Google Play can use it to detect irregular activity. Do not use this
   * field to store any Personally Identifiable Information (PII) such as emails
   * in cleartext. Attempting to store PII in this field will result in
   * purchases being blocked. Google Play recommends that you use either
   * encryption or a one-way hash to generate an obfuscated identifier to send
   * to Google Play. This identifier is limited to 64 characters. This field can
   * only be set for resubscription purchases. See https://developer.android.com
   * /reference/com/android/billingclient/api/BillingFlowParams.Builder#setobfus
   * catedprofileid to set this field for purchases made using the standard in-
   * app billing flow.
   *
   * @var string
   */
  public $obfuscatedProfileId;

  /**
   * Optional. Specifies an optional obfuscated string that is uniquely
   * associated with the purchaser's user account in your app. If you pass this
   * value, Google Play can use it to detect irregular activity. Do not use this
   * field to store any Personally Identifiable Information (PII) such as emails
   * in cleartext. Attempting to store PII in this field will result in
   * purchases being blocked. Google Play recommends that you use either
   * encryption or a one-way hash to generate an obfuscated identifier to send
   * to Google Play. This identifier is limited to 64 characters. This field can
   * only be set for resubscription purchases. See https://developer.android.com
   * /reference/com/android/billingclient/api/BillingFlowParams.Builder#setobfus
   * catedaccountid to set this field for purchases made using the standard in-
   * app billing flow.
   *
   * @param string $obfuscatedAccountId
   */
  public function setObfuscatedAccountId($obfuscatedAccountId)
  {
    $this->obfuscatedAccountId = $obfuscatedAccountId;
  }
  /**
   * @return string
   */
  public function getObfuscatedAccountId()
  {
    return $this->obfuscatedAccountId;
  }
  /**
   * Optional. Specifies an optional obfuscated string that is uniquely
   * associated with the purchaser's user profile in your app. If you pass this
   * value, Google Play can use it to detect irregular activity. Do not use this
   * field to store any Personally Identifiable Information (PII) such as emails
   * in cleartext. Attempting to store PII in this field will result in
   * purchases being blocked. Google Play recommends that you use either
   * encryption or a one-way hash to generate an obfuscated identifier to send
   * to Google Play. This identifier is limited to 64 characters. This field can
   * only be set for resubscription purchases. See https://developer.android.com
   * /reference/com/android/billingclient/api/BillingFlowParams.Builder#setobfus
   * catedprofileid to set this field for purchases made using the standard in-
   * app billing flow.
   *
   * @param string $obfuscatedProfileId
   */
  public function setObfuscatedProfileId($obfuscatedProfileId)
  {
    $this->obfuscatedProfileId = $obfuscatedProfileId;
  }
  /**
   * @return string
   */
  public function getObfuscatedProfileId()
  {
    return $this->obfuscatedProfileId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ExternalAccountIds::class, 'Google_Service_AndroidPublisher_ExternalAccountIds');
