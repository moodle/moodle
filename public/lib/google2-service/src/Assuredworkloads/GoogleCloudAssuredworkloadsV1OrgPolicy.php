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

namespace Google\Service\Assuredworkloads;

class GoogleCloudAssuredworkloadsV1OrgPolicy extends \Google\Model
{
  /**
   * The constraint name of the OrgPolicy. e.g.
   * "constraints/gcp.resourceLocations".
   *
   * @var string
   */
  public $constraint;
  /**
   * If `inherit` is true, policy rules of the lowest ancestor in the resource
   * hierarchy chain are inherited. If it is false, policy rules are not
   * inherited.
   *
   * @var bool
   */
  public $inherit;
  /**
   * Ignores policies set above this resource and restores to the
   * `constraint_default` value. `reset` can only be true when `rules` is empty
   * and `inherit` is false.
   *
   * @var bool
   */
  public $reset;
  /**
   * Resource that the OrgPolicy attaches to. Format: folders/123"
   * projects/123".
   *
   * @var string
   */
  public $resource;
  protected $ruleType = GoogleCloudAssuredworkloadsV1OrgPolicyPolicyRule::class;
  protected $ruleDataType = '';

  /**
   * The constraint name of the OrgPolicy. e.g.
   * "constraints/gcp.resourceLocations".
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
   * If `inherit` is true, policy rules of the lowest ancestor in the resource
   * hierarchy chain are inherited. If it is false, policy rules are not
   * inherited.
   *
   * @param bool $inherit
   */
  public function setInherit($inherit)
  {
    $this->inherit = $inherit;
  }
  /**
   * @return bool
   */
  public function getInherit()
  {
    return $this->inherit;
  }
  /**
   * Ignores policies set above this resource and restores to the
   * `constraint_default` value. `reset` can only be true when `rules` is empty
   * and `inherit` is false.
   *
   * @param bool $reset
   */
  public function setReset($reset)
  {
    $this->reset = $reset;
  }
  /**
   * @return bool
   */
  public function getReset()
  {
    return $this->reset;
  }
  /**
   * Resource that the OrgPolicy attaches to. Format: folders/123"
   * projects/123".
   *
   * @param string $resource
   */
  public function setResource($resource)
  {
    $this->resource = $resource;
  }
  /**
   * @return string
   */
  public function getResource()
  {
    return $this->resource;
  }
  /**
   * The rule of the OrgPolicy.
   *
   * @param GoogleCloudAssuredworkloadsV1OrgPolicyPolicyRule $rule
   */
  public function setRule(GoogleCloudAssuredworkloadsV1OrgPolicyPolicyRule $rule)
  {
    $this->rule = $rule;
  }
  /**
   * @return GoogleCloudAssuredworkloadsV1OrgPolicyPolicyRule
   */
  public function getRule()
  {
    return $this->rule;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAssuredworkloadsV1OrgPolicy::class, 'Google_Service_Assuredworkloads_GoogleCloudAssuredworkloadsV1OrgPolicy');
