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

namespace Google\Service\CloudAsset;

class GoogleCloudAssetV1Constraint extends \Google\Model
{
  /**
   * This is only used for distinguishing unset values and should never be used.
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
  protected $booleanConstraintType = GoogleCloudAssetV1BooleanConstraint::class;
  protected $booleanConstraintDataType = '';
  /**
   * The evaluation behavior of this constraint in the absence of 'Policy'.
   *
   * @var string
   */
  public $constraintDefault;
  /**
   * Detailed description of what this `Constraint` controls as well as how and
   * where it is enforced.
   *
   * @var string
   */
  public $description;
  /**
   * The human readable name of the constraint.
   *
   * @var string
   */
  public $displayName;
  protected $listConstraintType = GoogleCloudAssetV1ListConstraint::class;
  protected $listConstraintDataType = '';
  /**
   * The unique name of the constraint. Format of the name should be *
   * `constraints/{constraint_name}` For example,
   * `constraints/compute.disableSerialPortAccess`.
   *
   * @var string
   */
  public $name;

  /**
   * Defines this constraint as being a BooleanConstraint.
   *
   * @param GoogleCloudAssetV1BooleanConstraint $booleanConstraint
   */
  public function setBooleanConstraint(GoogleCloudAssetV1BooleanConstraint $booleanConstraint)
  {
    $this->booleanConstraint = $booleanConstraint;
  }
  /**
   * @return GoogleCloudAssetV1BooleanConstraint
   */
  public function getBooleanConstraint()
  {
    return $this->booleanConstraint;
  }
  /**
   * The evaluation behavior of this constraint in the absence of 'Policy'.
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
   * Detailed description of what this `Constraint` controls as well as how and
   * where it is enforced.
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
   * The human readable name of the constraint.
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
   * Defines this constraint as being a ListConstraint.
   *
   * @param GoogleCloudAssetV1ListConstraint $listConstraint
   */
  public function setListConstraint(GoogleCloudAssetV1ListConstraint $listConstraint)
  {
    $this->listConstraint = $listConstraint;
  }
  /**
   * @return GoogleCloudAssetV1ListConstraint
   */
  public function getListConstraint()
  {
    return $this->listConstraint;
  }
  /**
   * The unique name of the constraint. Format of the name should be *
   * `constraints/{constraint_name}` For example,
   * `constraints/compute.disableSerialPortAccess`.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAssetV1Constraint::class, 'Google_Service_CloudAsset_GoogleCloudAssetV1Constraint');
