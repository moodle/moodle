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

class Policy extends \Google\Collection
{
  protected $collection_key = 'complianceStandards';
  protected $complianceStandardsType = ComplianceStandard::class;
  protected $complianceStandardsDataType = 'array';
  protected $constraintType = Constraint::class;
  protected $constraintDataType = '';
  /**
   * Optional. A description of the policy.
   *
   * @var string
   */
  public $description;
  /**
   * Required. A user-specified identifier for the policy. In a PolicySet, each
   * policy must have a unique identifier.
   *
   * @var string
   */
  public $policyId;

  /**
   * Optional. The compliance standards that the policy helps enforce.
   *
   * @param ComplianceStandard[] $complianceStandards
   */
  public function setComplianceStandards($complianceStandards)
  {
    $this->complianceStandards = $complianceStandards;
  }
  /**
   * @return ComplianceStandard[]
   */
  public function getComplianceStandards()
  {
    return $this->complianceStandards;
  }
  /**
   * Required. The constraints that the policy includes.
   *
   * @param Constraint $constraint
   */
  public function setConstraint(Constraint $constraint)
  {
    $this->constraint = $constraint;
  }
  /**
   * @return Constraint
   */
  public function getConstraint()
  {
    return $this->constraint;
  }
  /**
   * Optional. A description of the policy.
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
  /**
   * Required. A user-specified identifier for the policy. In a PolicySet, each
   * policy must have a unique identifier.
   *
   * @param string $policyId
   */
  public function setPolicyId($policyId)
  {
    $this->policyId = $policyId;
  }
  /**
   * @return string
   */
  public function getPolicyId()
  {
    return $this->policyId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Policy::class, 'Google_Service_SecurityPosture_Policy');
