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

namespace Google\Service\ServiceControl;

class ViolationInfo extends \Google\Model
{
  /**
   * Default value. This value should not be used.
   */
  public const POLICY_TYPE_POLICY_TYPE_UNSPECIFIED = 'POLICY_TYPE_UNSPECIFIED';
  /**
   * Indicates boolean policy constraint
   */
  public const POLICY_TYPE_BOOLEAN_CONSTRAINT = 'BOOLEAN_CONSTRAINT';
  /**
   * Indicates list policy constraint
   */
  public const POLICY_TYPE_LIST_CONSTRAINT = 'LIST_CONSTRAINT';
  /**
   * Indicates custom policy constraint
   */
  public const POLICY_TYPE_CUSTOM_CONSTRAINT = 'CUSTOM_CONSTRAINT';
  /**
   * Optional. Value that is being checked for the policy. This could be in
   * encrypted form (if pii sensitive). This field will only be emitted in
   * LIST_POLICY types
   *
   * @var string
   */
  public $checkedValue;
  /**
   * Optional. Constraint name
   *
   * @var string
   */
  public $constraint;
  /**
   * Optional. Provides extra information for the specific violated constraint.
   * See the constraint's documentation to determine if this field is populated
   * and what the structure of the message should be.
   *
   * @var array[]
   */
  public $constraintViolationInfo;
  /**
   * Optional. Error message that policy is indicating.
   *
   * @var string
   */
  public $errorMessage;
  /**
   * Optional. Indicates the type of the policy.
   *
   * @var string
   */
  public $policyType;

  /**
   * Optional. Value that is being checked for the policy. This could be in
   * encrypted form (if pii sensitive). This field will only be emitted in
   * LIST_POLICY types
   *
   * @param string $checkedValue
   */
  public function setCheckedValue($checkedValue)
  {
    $this->checkedValue = $checkedValue;
  }
  /**
   * @return string
   */
  public function getCheckedValue()
  {
    return $this->checkedValue;
  }
  /**
   * Optional. Constraint name
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
   * Optional. Provides extra information for the specific violated constraint.
   * See the constraint's documentation to determine if this field is populated
   * and what the structure of the message should be.
   *
   * @param array[] $constraintViolationInfo
   */
  public function setConstraintViolationInfo($constraintViolationInfo)
  {
    $this->constraintViolationInfo = $constraintViolationInfo;
  }
  /**
   * @return array[]
   */
  public function getConstraintViolationInfo()
  {
    return $this->constraintViolationInfo;
  }
  /**
   * Optional. Error message that policy is indicating.
   *
   * @param string $errorMessage
   */
  public function setErrorMessage($errorMessage)
  {
    $this->errorMessage = $errorMessage;
  }
  /**
   * @return string
   */
  public function getErrorMessage()
  {
    return $this->errorMessage;
  }
  /**
   * Optional. Indicates the type of the policy.
   *
   * Accepted values: POLICY_TYPE_UNSPECIFIED, BOOLEAN_CONSTRAINT,
   * LIST_CONSTRAINT, CUSTOM_CONSTRAINT
   *
   * @param self::POLICY_TYPE_* $policyType
   */
  public function setPolicyType($policyType)
  {
    $this->policyType = $policyType;
  }
  /**
   * @return self::POLICY_TYPE_*
   */
  public function getPolicyType()
  {
    return $this->policyType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ViolationInfo::class, 'Google_Service_ServiceControl_ViolationInfo');
