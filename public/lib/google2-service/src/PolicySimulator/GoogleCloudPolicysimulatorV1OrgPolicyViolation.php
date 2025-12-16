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

namespace Google\Service\PolicySimulator;

class GoogleCloudPolicysimulatorV1OrgPolicyViolation extends \Google\Model
{
  protected $customConstraintType = GoogleCloudOrgpolicyV2CustomConstraint::class;
  protected $customConstraintDataType = '';
  protected $errorType = GoogleRpcStatus::class;
  protected $errorDataType = '';
  /**
   * The name of the `OrgPolicyViolation`. Example: organizations/my-example-org
   * /locations/global/orgPolicyViolationsPreviews/506a5f7f/orgPolicyViolations/
   * 38ce`
   *
   * @var string
   */
  public $name;
  protected $resourceType = GoogleCloudPolicysimulatorV1ResourceContext::class;
  protected $resourceDataType = '';

  /**
   * The custom constraint being violated.
   *
   * @param GoogleCloudOrgpolicyV2CustomConstraint $customConstraint
   */
  public function setCustomConstraint(GoogleCloudOrgpolicyV2CustomConstraint $customConstraint)
  {
    $this->customConstraint = $customConstraint;
  }
  /**
   * @return GoogleCloudOrgpolicyV2CustomConstraint
   */
  public function getCustomConstraint()
  {
    return $this->customConstraint;
  }
  /**
   * Any error encountered during the evaluation.
   *
   * @param GoogleRpcStatus $error
   */
  public function setError(GoogleRpcStatus $error)
  {
    $this->error = $error;
  }
  /**
   * @return GoogleRpcStatus
   */
  public function getError()
  {
    return $this->error;
  }
  /**
   * The name of the `OrgPolicyViolation`. Example: organizations/my-example-org
   * /locations/global/orgPolicyViolationsPreviews/506a5f7f/orgPolicyViolations/
   * 38ce`
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
   * The resource violating the constraint.
   *
   * @param GoogleCloudPolicysimulatorV1ResourceContext $resource
   */
  public function setResource(GoogleCloudPolicysimulatorV1ResourceContext $resource)
  {
    $this->resource = $resource;
  }
  /**
   * @return GoogleCloudPolicysimulatorV1ResourceContext
   */
  public function getResource()
  {
    return $this->resource;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudPolicysimulatorV1OrgPolicyViolation::class, 'Google_Service_PolicySimulator_GoogleCloudPolicysimulatorV1OrgPolicyViolation');
