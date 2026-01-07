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

class PurchaseOptionTaxAndComplianceSettings extends \Google\Model
{
  public const WITHDRAWAL_RIGHT_TYPE_WITHDRAWAL_RIGHT_TYPE_UNSPECIFIED = 'WITHDRAWAL_RIGHT_TYPE_UNSPECIFIED';
  public const WITHDRAWAL_RIGHT_TYPE_WITHDRAWAL_RIGHT_DIGITAL_CONTENT = 'WITHDRAWAL_RIGHT_DIGITAL_CONTENT';
  public const WITHDRAWAL_RIGHT_TYPE_WITHDRAWAL_RIGHT_SERVICE = 'WITHDRAWAL_RIGHT_SERVICE';
  /**
   * Optional. Digital content or service classification for products
   * distributed to users in eligible regions. If unset, it defaults to
   * `WITHDRAWAL_RIGHT_DIGITAL_CONTENT`. Refer to the [Help Center
   * article](https://support.google.com/googleplay/android-
   * developer/answer/10463498) for more information.
   *
   * @var string
   */
  public $withdrawalRightType;

  /**
   * Optional. Digital content or service classification for products
   * distributed to users in eligible regions. If unset, it defaults to
   * `WITHDRAWAL_RIGHT_DIGITAL_CONTENT`. Refer to the [Help Center
   * article](https://support.google.com/googleplay/android-
   * developer/answer/10463498) for more information.
   *
   * Accepted values: WITHDRAWAL_RIGHT_TYPE_UNSPECIFIED,
   * WITHDRAWAL_RIGHT_DIGITAL_CONTENT, WITHDRAWAL_RIGHT_SERVICE
   *
   * @param self::WITHDRAWAL_RIGHT_TYPE_* $withdrawalRightType
   */
  public function setWithdrawalRightType($withdrawalRightType)
  {
    $this->withdrawalRightType = $withdrawalRightType;
  }
  /**
   * @return self::WITHDRAWAL_RIGHT_TYPE_*
   */
  public function getWithdrawalRightType()
  {
    return $this->withdrawalRightType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PurchaseOptionTaxAndComplianceSettings::class, 'Google_Service_AndroidPublisher_PurchaseOptionTaxAndComplianceSettings');
