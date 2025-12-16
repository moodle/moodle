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

class CustomerApprovalApprovalPolicy extends \Google\Model
{
  /**
   * Default value, defaults to JUSTIFICATION_BASED_APPROVAL_NOT_ENABLED if not
   * set. This value is not able to be configured by the user, do not use.
   */
  public const JUSTIFICATION_BASED_APPROVAL_POLICY_JUSTIFICATION_BASED_APPROVAL_POLICY_UNSPECIFIED = 'JUSTIFICATION_BASED_APPROVAL_POLICY_UNSPECIFIED';
  /**
   * Audit-only mode. All accesses are pre-approved instantly.
   */
  public const JUSTIFICATION_BASED_APPROVAL_POLICY_JUSTIFICATION_BASED_APPROVAL_ENABLED_ALL = 'JUSTIFICATION_BASED_APPROVAL_ENABLED_ALL';
  /**
   * Customer initiated support access reasons are pre-approved instantly. All
   * other accesses require customer approval.
   */
  public const JUSTIFICATION_BASED_APPROVAL_POLICY_JUSTIFICATION_BASED_APPROVAL_ENABLED_EXTERNAL_JUSTIFICATIONS = 'JUSTIFICATION_BASED_APPROVAL_ENABLED_EXTERNAL_JUSTIFICATIONS';
  /**
   * All access approval requests require customer approval. This is the default
   * value if the policy is not set.
   */
  public const JUSTIFICATION_BASED_APPROVAL_POLICY_JUSTIFICATION_BASED_APPROVAL_NOT_ENABLED = 'JUSTIFICATION_BASED_APPROVAL_NOT_ENABLED';
  /**
   * Defer configuration to parent settings. This is the default value if the
   * policy is not set and the parent has a value set.
   */
  public const JUSTIFICATION_BASED_APPROVAL_POLICY_JUSTIFICATION_BASED_APPROVAL_INHERITED = 'JUSTIFICATION_BASED_APPROVAL_INHERITED';
  /**
   * Optional. Policy for approval based on the justification given.
   *
   * @var string
   */
  public $justificationBasedApprovalPolicy;

  /**
   * Optional. Policy for approval based on the justification given.
   *
   * Accepted values: JUSTIFICATION_BASED_APPROVAL_POLICY_UNSPECIFIED,
   * JUSTIFICATION_BASED_APPROVAL_ENABLED_ALL,
   * JUSTIFICATION_BASED_APPROVAL_ENABLED_EXTERNAL_JUSTIFICATIONS,
   * JUSTIFICATION_BASED_APPROVAL_NOT_ENABLED,
   * JUSTIFICATION_BASED_APPROVAL_INHERITED
   *
   * @param self::JUSTIFICATION_BASED_APPROVAL_POLICY_* $justificationBasedApprovalPolicy
   */
  public function setJustificationBasedApprovalPolicy($justificationBasedApprovalPolicy)
  {
    $this->justificationBasedApprovalPolicy = $justificationBasedApprovalPolicy;
  }
  /**
   * @return self::JUSTIFICATION_BASED_APPROVAL_POLICY_*
   */
  public function getJustificationBasedApprovalPolicy()
  {
    return $this->justificationBasedApprovalPolicy;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CustomerApprovalApprovalPolicy::class, 'Google_Service_AccessApproval_CustomerApprovalApprovalPolicy');
