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

namespace Google\Service\SecurityPosture;

class PolicyDetails extends \Google\Collection
{
  /**
   * Default value. This value is unused.
   */
  public const CONSTRAINT_TYPE_CONSTRAINT_TYPE_UNSPECIFIED = 'CONSTRAINT_TYPE_UNSPECIFIED';
  /**
   * A custom module for Security Health Analytics.
   */
  public const CONSTRAINT_TYPE_SECURITY_HEALTH_ANALYTICS_CUSTOM_MODULE = 'SECURITY_HEALTH_ANALYTICS_CUSTOM_MODULE';
  /**
   * A custom organization policy constraint.
   */
  public const CONSTRAINT_TYPE_ORG_POLICY_CUSTOM = 'ORG_POLICY_CUSTOM';
  /**
   * A built-in detector for Security Health Analytics.
   */
  public const CONSTRAINT_TYPE_SECURITY_HEALTH_ANALYTICS_MODULE = 'SECURITY_HEALTH_ANALYTICS_MODULE';
  /**
   * A predefined organization policy constraint.
   */
  public const CONSTRAINT_TYPE_ORG_POLICY = 'ORG_POLICY';
  /**
   * A custom rego policy constraint.
   */
  public const CONSTRAINT_TYPE_REGO_POLICY = 'REGO_POLICY';
  protected $collection_key = 'complianceStandards';
  /**
   * The compliance standards that the policy maps to. For example, `CIS-2.0
   * 1.15`.
   *
   * @var string[]
   */
  public $complianceStandards;
  /**
   * Information about the constraint that was violated. The format of this
   * information can change at any time without prior notice. Your application
   * must not depend on this information in any way.
   *
   * @var string
   */
  public $constraint;
  /**
   * The type of constraint that was violated.
   *
   * @var string
   */
  public $constraintType;
  /**
   * A description of the policy.
   *
   * @var string
   */
  public $description;

  /**
   * The compliance standards that the policy maps to. For example, `CIS-2.0
   * 1.15`.
   *
   * @param string[] $complianceStandards
   */
  public function setComplianceStandards($complianceStandards)
  {
    $this->complianceStandards = $complianceStandards;
  }
  /**
   * @return string[]
   */
  public function getComplianceStandards()
  {
    return $this->complianceStandards;
  }
  /**
   * Information about the constraint that was violated. The format of this
   * information can change at any time without prior notice. Your application
   * must not depend on this information in any way.
   *
   * @param string $constraint
   */
  public function setConstraint($constraint)
  {
    $this->constraint = $constraint;
  }
  /**
   * @return string
   */
  public function getConstraint()
  {
    return $this->constraint;
  }
  /**
   * The type of constraint that was violated.
   *
   * Accepted values: CONSTRAINT_TYPE_UNSPECIFIED,
   * SECURITY_HEALTH_ANALYTICS_CUSTOM_MODULE, ORG_POLICY_CUSTOM,
   * SECURITY_HEALTH_ANALYTICS_MODULE, ORG_POLICY, REGO_POLICY
   *
   * @param self::CONSTRAINT_TYPE_* $constraintType
   */
  public function setConstraintType($constraintType)
  {
    $this->constraintType = $constraintType;
  }
  /**
   * @return self::CONSTRAINT_TYPE_*
   */
  public function getConstraintType()
  {
    return $this->constraintType;
  }
  /**
   * A description of the policy.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PolicyDetails::class, 'Google_Service_SecurityPosture_PolicyDetails');
