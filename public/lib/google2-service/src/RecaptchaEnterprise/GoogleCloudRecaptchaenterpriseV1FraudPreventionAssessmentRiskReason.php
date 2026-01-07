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

namespace Google\Service\RecaptchaEnterprise;

class GoogleCloudRecaptchaenterpriseV1FraudPreventionAssessmentRiskReason extends \Google\Model
{
  /**
   * Default unspecified type.
   */
  public const REASON_REASON_UNSPECIFIED = 'REASON_UNSPECIFIED';
  /**
   * A suspiciously high number of recent transactions have used identifiers
   * present in this transaction.
   */
  public const REASON_HIGH_TRANSACTION_VELOCITY = 'HIGH_TRANSACTION_VELOCITY';
  /**
   * User is cycling through a suspiciously large number of identifiers,
   * suggesting enumeration or validation attacks within a potential fraud
   * network.
   */
  public const REASON_EXCESSIVE_ENUMERATION_PATTERN = 'EXCESSIVE_ENUMERATION_PATTERN';
  /**
   * User has a short history or no history in the reCAPTCHA network, suggesting
   * the possibility of synthetic identity generation.
   */
  public const REASON_SHORT_IDENTITY_HISTORY = 'SHORT_IDENTITY_HISTORY';
  /**
   * Identifiers used in this transaction originate from an unusual or
   * conflicting set of geolocations.
   */
  public const REASON_GEOLOCATION_DISCREPANCY = 'GEOLOCATION_DISCREPANCY';
  /**
   * This transaction is linked to a cluster of known fraudulent activity.
   */
  public const REASON_ASSOCIATED_WITH_FRAUD_CLUSTER = 'ASSOCIATED_WITH_FRAUD_CLUSTER';
  /**
   * Output only. Risk reasons applicable to the Fraud Prevention assessment.
   *
   * @var string
   */
  public $reason;

  /**
   * Output only. Risk reasons applicable to the Fraud Prevention assessment.
   *
   * Accepted values: REASON_UNSPECIFIED, HIGH_TRANSACTION_VELOCITY,
   * EXCESSIVE_ENUMERATION_PATTERN, SHORT_IDENTITY_HISTORY,
   * GEOLOCATION_DISCREPANCY, ASSOCIATED_WITH_FRAUD_CLUSTER
   *
   * @param self::REASON_* $reason
   */
  public function setReason($reason)
  {
    $this->reason = $reason;
  }
  /**
   * @return self::REASON_*
   */
  public function getReason()
  {
    return $this->reason;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRecaptchaenterpriseV1FraudPreventionAssessmentRiskReason::class, 'Google_Service_RecaptchaEnterprise_GoogleCloudRecaptchaenterpriseV1FraudPreventionAssessmentRiskReason');
