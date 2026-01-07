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

class TermsOfService extends \Google\Model
{
  /**
   * Not specified.
   */
  public const CUSTOMER_MATCH_TERMS_OF_SERVICE_STATUS_TERMS_OF_SERVICE_STATUS_UNSPECIFIED = 'TERMS_OF_SERVICE_STATUS_UNSPECIFIED';
  /**
   * Status indicating the caller has chosen to accept the terms of service.
   */
  public const CUSTOMER_MATCH_TERMS_OF_SERVICE_STATUS_ACCEPTED = 'ACCEPTED';
  /**
   * Status indicating the caller has chosen to reject the terms of service.
   */
  public const CUSTOMER_MATCH_TERMS_OF_SERVICE_STATUS_REJECTED = 'REJECTED';
  /**
   * Optional. The Customer Match terms of service:
   * https://support.google.com/adspolicy/answer/6299717. This must be accepted
   * when ingesting UserData or MobileData. This field is not required for
   * Partner Match User list.
   *
   * @var string
   */
  public $customerMatchTermsOfServiceStatus;

  /**
   * Optional. The Customer Match terms of service:
   * https://support.google.com/adspolicy/answer/6299717. This must be accepted
   * when ingesting UserData or MobileData. This field is not required for
   * Partner Match User list.
   *
   * Accepted values: TERMS_OF_SERVICE_STATUS_UNSPECIFIED, ACCEPTED, REJECTED
   *
   * @param self::CUSTOMER_MATCH_TERMS_OF_SERVICE_STATUS_* $customerMatchTermsOfServiceStatus
   */
  public function setCustomerMatchTermsOfServiceStatus($customerMatchTermsOfServiceStatus)
  {
    $this->customerMatchTermsOfServiceStatus = $customerMatchTermsOfServiceStatus;
  }
  /**
   * @return self::CUSTOMER_MATCH_TERMS_OF_SERVICE_STATUS_*
   */
  public function getCustomerMatchTermsOfServiceStatus()
  {
    return $this->customerMatchTermsOfServiceStatus;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TermsOfService::class, 'Google_Service_DataManager_TermsOfService');
