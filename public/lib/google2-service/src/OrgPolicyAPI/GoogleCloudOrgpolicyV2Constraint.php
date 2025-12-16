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

namespace Google\Service\OrgPolicyAPI;

class GoogleCloudOrgpolicyV2Constraint extends \Google\Model
{
  /**
   * This is only used for distinguishing unset values and should never be used.
   * Results in an error.
   */
  public const CONSTRAINT_DEFAULT_CONSTRAINT_DEFAULT_UNSPECIFIED = 'CONSTRAINT_DEFAULT_UNSPECIFIED';
  /**
   * Indicate that all values are allowed for list constraints. Indicate that
   * enforcement is off for boolean constraints.
   */
  public const CONSTRAINT_DEFAULT_ALLOW = 'ALLOW';
  /**
   * Indicate that all values are denied for list constraints. Indicate that
   * enforcement is on for boolean constraints.
   */
  public const CONSTRAINT_DEFAULT_DENY = 'DENY';
  protected $booleanConstraintType = GoogleCloudOrgpolicyV2ConstraintBooleanConstraint::class;
  protected $booleanConstraintDataType = '';
  /**
   * The evaluation behavior of this constraint in the absence of a policy.
   *
   * @var string
   */
  public $constraintDefault;
  /**
   * Detailed description of what this constraint controls as well as how and
   * where it is enforced. Mutable.
   *
   * @var string
   */
  public $description;
  /**
   * The human readable name. Mutable.
   *
   * @var string
   */
  public $displayName;
  /**
   * Managed constraint and canned constraint sometimes can have equivalents.
   * This field is used to store the equivalent constraint name.
   *
   * @var string
   */
  public $equivalentConstraint;
  protected $listConstraintType = GoogleCloudOrgpolicyV2ConstraintListConstraint::class;
  protected $listConstraintDataType = '';
  /**
   * Immutable. The resource name of the constraint. Must be in one of the
   * following forms: *
   * `projects/{project_number}/constraints/{constraint_name}` *
   * `folders/{folder_id}/constraints/{constraint_name}` *
   * `organizations/{organization_id}/constraints/{constraint_name}` For
   * example, "/projects/123/constraints/compute.disableSerialPortAccess".
   *
   * @var string
   */
  public $name;
  /**
   * Shows if dry run is supported for this constraint or not.
   *
   * @var bool
   */
  public $supportsDryRun;
  /**
   * Shows if simulation is supported for this constraint or not.
   *
   * @var bool
   */
  public $supportsSimulation;

  /**
   * Defines this constraint as being a boolean constraint.
   *
   * @param GoogleCloudOrgpolicyV2ConstraintBooleanConstraint $booleanConstraint
   */
  public function setBooleanConstraint(GoogleCloudOrgpolicyV2ConstraintBooleanConstraint $booleanConstraint)
  {
    $this->booleanConstraint = $booleanConstraint;
  }
  /**
   * @return GoogleCloudOrgpolicyV2ConstraintBooleanConstraint
   */
  public function getBooleanConstraint()
  {
    return $this->booleanConstraint;
  }
  /**
   * The evaluation behavior of this constraint in the absence of a policy.
   *
   * Accepted values: CONSTRAINT_DEFAULT_UNSPECIFIED, ALLOW, DENY
   *
   * @param self::CONSTRAINT_DEFAULT_* $constraintDefault
   */
  public function setConstraintDefault($constraintDefault)
  {
    $this->constraintDefault = $constraintDefault;
  }
  /**
   * @return self::CONSTRAINT_DEFAULT_*
   */
  public function getConstraintDefault()
  {
    return $this->constraintDefault;
  }
  /**
   * Detailed description of what this constraint controls as well as how and
   * where it is enforced. Mutable.
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
   * The human readable name. Mutable.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Managed constraint and canned constraint sometimes can have equivalents.
   * This field is used to store the equivalent constraint name.
   *
   * @param string $equivalentConstraint
   */
  public function setEquivalentConstraint($equivalentConstraint)
  {
    $this->equivalentConstraint = $equivalentConstraint;
  }
  /**
   * @return string
   */
  public function getEquivalentConstraint()
  {
    return $this->equivalentConstraint;
  }
  /**
   * Defines this constraint as being a list constraint.
   *
   * @param GoogleCloudOrgpolicyV2ConstraintListConstraint $listConstraint
   */
  public function setListConstraint(GoogleCloudOrgpolicyV2ConstraintListConstraint $listConstraint)
  {
    $this->listConstraint = $listConstraint;
  }
  /**
   * @return GoogleCloudOrgpolicyV2ConstraintListConstraint
   */
  public function getListConstraint()
  {
    return $this->listConstraint;
  }
  /**
   * Immutable. The resource name of the constraint. Must be in one of the
   * following forms: *
   * `projects/{project_number}/constraints/{constraint_name}` *
   * `folders/{folder_id}/constraints/{constraint_name}` *
   * `organizations/{organization_id}/constraints/{constraint_name}` For
   * example, "/projects/123/constraints/compute.disableSerialPortAccess".
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Shows if dry run is supported for this constraint or not.
   *
   * @param bool $supportsDryRun
   */
  public function setSupportsDryRun($supportsDryRun)
  {
    $this->supportsDryRun = $supportsDryRun;
  }
  /**
   * @return bool
   */
  public function getSupportsDryRun()
  {
    return $this->supportsDryRun;
  }
  /**
   * Shows if simulation is supported for this constraint or not.
   *
   * @param bool $supportsSimulation
   */
  public function setSupportsSimulation($supportsSimulation)
  {
    $this->supportsSimulation = $supportsSimulation;
  }
  /**
   * @return bool
   */
  public function getSupportsSimulation()
  {
    return $this->supportsSimulation;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudOrgpolicyV2Constraint::class, 'Google_Service_OrgPolicyAPI_GoogleCloudOrgpolicyV2Constraint');
