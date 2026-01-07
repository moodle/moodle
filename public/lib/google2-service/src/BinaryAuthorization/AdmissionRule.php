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

namespace Google\Service\BinaryAuthorization;

class AdmissionRule extends \Google\Collection
{
  /**
   * Do not use.
   */
  public const ENFORCEMENT_MODE_ENFORCEMENT_MODE_UNSPECIFIED = 'ENFORCEMENT_MODE_UNSPECIFIED';
  /**
   * Enforce the admission rule by blocking the pod creation.
   */
  public const ENFORCEMENT_MODE_ENFORCED_BLOCK_AND_AUDIT_LOG = 'ENFORCED_BLOCK_AND_AUDIT_LOG';
  /**
   * Dryrun mode: Audit logging only. This will allow the pod creation as if the
   * admission request had specified break-glass.
   */
  public const ENFORCEMENT_MODE_DRYRUN_AUDIT_LOG_ONLY = 'DRYRUN_AUDIT_LOG_ONLY';
  /**
   * Do not use.
   */
  public const EVALUATION_MODE_EVALUATION_MODE_UNSPECIFIED = 'EVALUATION_MODE_UNSPECIFIED';
  /**
   * This rule allows all pod creations.
   */
  public const EVALUATION_MODE_ALWAYS_ALLOW = 'ALWAYS_ALLOW';
  /**
   * This rule allows a pod creation if all the attestors listed in
   * `require_attestations_by` have valid attestations for all of the images in
   * the pod spec.
   */
  public const EVALUATION_MODE_REQUIRE_ATTESTATION = 'REQUIRE_ATTESTATION';
  /**
   * This rule denies all pod creations.
   */
  public const EVALUATION_MODE_ALWAYS_DENY = 'ALWAYS_DENY';
  protected $collection_key = 'requireAttestationsBy';
  /**
   * Required. The action when a pod creation is denied by the admission rule.
   *
   * @var string
   */
  public $enforcementMode;
  /**
   * Required. How this admission rule will be evaluated.
   *
   * @var string
   */
  public $evaluationMode;
  /**
   * Optional. The resource names of the attestors that must attest to a
   * container image, in the format `projects/attestors`. Each attestor must
   * exist before a policy can reference it. To add an attestor to a policy the
   * principal issuing the policy change request must be able to read the
   * attestor resource. Note: this field must be non-empty when the
   * `evaluation_mode` field specifies `REQUIRE_ATTESTATION`, otherwise it must
   * be empty.
   *
   * @var string[]
   */
  public $requireAttestationsBy;

  /**
   * Required. The action when a pod creation is denied by the admission rule.
   *
   * Accepted values: ENFORCEMENT_MODE_UNSPECIFIED,
   * ENFORCED_BLOCK_AND_AUDIT_LOG, DRYRUN_AUDIT_LOG_ONLY
   *
   * @param self::ENFORCEMENT_MODE_* $enforcementMode
   */
  public function setEnforcementMode($enforcementMode)
  {
    $this->enforcementMode = $enforcementMode;
  }
  /**
   * @return self::ENFORCEMENT_MODE_*
   */
  public function getEnforcementMode()
  {
    return $this->enforcementMode;
  }
  /**
   * Required. How this admission rule will be evaluated.
   *
   * Accepted values: EVALUATION_MODE_UNSPECIFIED, ALWAYS_ALLOW,
   * REQUIRE_ATTESTATION, ALWAYS_DENY
   *
   * @param self::EVALUATION_MODE_* $evaluationMode
   */
  public function setEvaluationMode($evaluationMode)
  {
    $this->evaluationMode = $evaluationMode;
  }
  /**
   * @return self::EVALUATION_MODE_*
   */
  public function getEvaluationMode()
  {
    return $this->evaluationMode;
  }
  /**
   * Optional. The resource names of the attestors that must attest to a
   * container image, in the format `projects/attestors`. Each attestor must
   * exist before a policy can reference it. To add an attestor to a policy the
   * principal issuing the policy change request must be able to read the
   * attestor resource. Note: this field must be non-empty when the
   * `evaluation_mode` field specifies `REQUIRE_ATTESTATION`, otherwise it must
   * be empty.
   *
   * @param string[] $requireAttestationsBy
   */
  public function setRequireAttestationsBy($requireAttestationsBy)
  {
    $this->requireAttestationsBy = $requireAttestationsBy;
  }
  /**
   * @return string[]
   */
  public function getRequireAttestationsBy()
  {
    return $this->requireAttestationsBy;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AdmissionRule::class, 'Google_Service_BinaryAuthorization_AdmissionRule');
