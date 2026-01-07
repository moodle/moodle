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

namespace Google\Service\CloudIdentity;

class MemberRestriction extends \Google\Model
{
  protected $evaluationType = RestrictionEvaluation::class;
  protected $evaluationDataType = '';
  /**
   * Member Restriction as defined by CEL expression. Supported restrictions
   * are: `member.customer_id` and `member.type`. Valid values for `member.type`
   * are `1`, `2` and `3`. They correspond to USER, SERVICE_ACCOUNT, and GROUP
   * respectively. The value for `member.customer_id` only supports
   * `groupCustomerId()` currently which means the customer id of the group will
   * be used for restriction. Supported operators are `&&`, `||` and `==`,
   * corresponding to AND, OR, and EQUAL. Examples: Allow only service accounts
   * of given customer to be members. `member.type == 2 && member.customer_id ==
   * groupCustomerId()` Allow only users or groups to be members. `member.type
   * == 1 || member.type == 3`
   *
   * @var string
   */
  public $query;

  /**
   * The evaluated state of this restriction on a group.
   *
   * @param RestrictionEvaluation $evaluation
   */
  public function setEvaluation(RestrictionEvaluation $evaluation)
  {
    $this->evaluation = $evaluation;
  }
  /**
   * @return RestrictionEvaluation
   */
  public function getEvaluation()
  {
    return $this->evaluation;
  }
  /**
   * Member Restriction as defined by CEL expression. Supported restrictions
   * are: `member.customer_id` and `member.type`. Valid values for `member.type`
   * are `1`, `2` and `3`. They correspond to USER, SERVICE_ACCOUNT, and GROUP
   * respectively. The value for `member.customer_id` only supports
   * `groupCustomerId()` currently which means the customer id of the group will
   * be used for restriction. Supported operators are `&&`, `||` and `==`,
   * corresponding to AND, OR, and EQUAL. Examples: Allow only service accounts
   * of given customer to be members. `member.type == 2 && member.customer_id ==
   * groupCustomerId()` Allow only users or groups to be members. `member.type
   * == 1 || member.type == 3`
   *
   * @param string $query
   */
  public function setQuery($query)
  {
    $this->query = $query;
  }
  /**
   * @return string
   */
  public function getQuery()
  {
    return $this->query;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MemberRestriction::class, 'Google_Service_CloudIdentity_MemberRestriction');
