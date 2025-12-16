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

namespace Google\Service\AccessApproval;

class AccessReason extends \Google\Model
{
  /**
   * This value is not used.
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * Customer made a request or raised an issue that required the principal to
   * access customer data. `detail` is of the form ("#####" is the issue ID): *
   * "Feedback Report: #####" * "Case Number: #####" * "Case ID: #####" * "E-PIN
   * Reference: #####" * "Google-#####" * "T-#####"
   */
  public const TYPE_CUSTOMER_INITIATED_SUPPORT = 'CUSTOMER_INITIATED_SUPPORT';
  /**
   * The principal accessed customer data in order to diagnose or resolve a
   * suspected issue in services. Often this access is used to confirm that
   * customers are not affected by a suspected service issue or to remediate a
   * reversible system issue.
   */
  public const TYPE_GOOGLE_INITIATED_SERVICE = 'GOOGLE_INITIATED_SERVICE';
  /**
   * Google initiated service for security, fraud, abuse, or compliance
   * purposes.
   */
  public const TYPE_GOOGLE_INITIATED_REVIEW = 'GOOGLE_INITIATED_REVIEW';
  /**
   * The principal was compelled to access customer data in order to respond to
   * a legal third party data request or process, including legal processes from
   * customers themselves.
   */
  public const TYPE_THIRD_PARTY_DATA_REQUEST = 'THIRD_PARTY_DATA_REQUEST';
  /**
   * The principal accessed customer data in order to diagnose or resolve a
   * suspected issue in services or a known outage.
   */
  public const TYPE_GOOGLE_RESPONSE_TO_PRODUCTION_ALERT = 'GOOGLE_RESPONSE_TO_PRODUCTION_ALERT';
  /**
   * Similar to 'GOOGLE_INITIATED_SERVICE' or 'GOOGLE_INITIATED_REVIEW', but
   * with universe agnostic naming. The principal accessed customer data in
   * order to diagnose or resolve a suspected issue in services or a known
   * outage, or for security, fraud, abuse, or compliance review purposes.
   */
  public const TYPE_CLOUD_INITIATED_ACCESS = 'CLOUD_INITIATED_ACCESS';
  /**
   * More detail about certain reason types. See comments for each type above.
   *
   * @var string
   */
  public $detail;
  /**
   * Type of access reason.
   *
   * @var string
   */
  public $type;

  /**
   * More detail about certain reason types. See comments for each type above.
   *
   * @param string $detail
   */
  public function setDetail($detail)
  {
    $this->detail = $detail;
  }
  /**
   * @return string
   */
  public function getDetail()
  {
    return $this->detail;
  }
  /**
   * Type of access reason.
   *
   * Accepted values: TYPE_UNSPECIFIED, CUSTOMER_INITIATED_SUPPORT,
   * GOOGLE_INITIATED_SERVICE, GOOGLE_INITIATED_REVIEW,
   * THIRD_PARTY_DATA_REQUEST, GOOGLE_RESPONSE_TO_PRODUCTION_ALERT,
   * CLOUD_INITIATED_ACCESS
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AccessReason::class, 'Google_Service_AccessApproval_AccessReason');
