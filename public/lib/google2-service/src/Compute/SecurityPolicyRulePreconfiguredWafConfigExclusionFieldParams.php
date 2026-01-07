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

namespace Google\Service\Compute;

class SecurityPolicyRulePreconfiguredWafConfigExclusionFieldParams extends \Google\Model
{
  /**
   * The operator matches if the field value contains the specified value.
   */
  public const OP_CONTAINS = 'CONTAINS';
  /**
   * The operator matches if the field value ends with the specified value.
   */
  public const OP_ENDS_WITH = 'ENDS_WITH';
  /**
   * The operator matches if the field value equals the specified value.
   */
  public const OP_EQUALS = 'EQUALS';
  /**
   * The operator matches if the field value is any value.
   */
  public const OP_EQUALS_ANY = 'EQUALS_ANY';
  /**
   * The operator matches if the field value starts with the specified value.
   */
  public const OP_STARTS_WITH = 'STARTS_WITH';
  /**
   * The match operator for the field.
   *
   * @var string
   */
  public $op;
  /**
   * The value of the field.
   *
   * @var string
   */
  public $val;

  /**
   * The match operator for the field.
   *
   * Accepted values: CONTAINS, ENDS_WITH, EQUALS, EQUALS_ANY, STARTS_WITH
   *
   * @param self::OP_* $op
   */
  public function setOp($op)
  {
    $this->op = $op;
  }
  /**
   * @return self::OP_*
   */
  public function getOp()
  {
    return $this->op;
  }
  /**
   * The value of the field.
   *
   * @param string $val
   */
  public function setVal($val)
  {
    $this->val = $val;
  }
  /**
   * @return string
   */
  public function getVal()
  {
    return $this->val;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SecurityPolicyRulePreconfiguredWafConfigExclusionFieldParams::class, 'Google_Service_Compute_SecurityPolicyRulePreconfiguredWafConfigExclusionFieldParams');
