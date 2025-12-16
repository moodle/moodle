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

namespace Google\Service\NetworkSecurity;

class AuthzPolicyAuthzRuleTo extends \Google\Collection
{
  protected $collection_key = 'operations';
  protected $notOperationsType = AuthzPolicyAuthzRuleToRequestOperation::class;
  protected $notOperationsDataType = 'array';
  protected $operationsType = AuthzPolicyAuthzRuleToRequestOperation::class;
  protected $operationsDataType = 'array';

  /**
   * Optional. Describes the negated properties of the targets of a request.
   * Matches requests for operations that do not match the criteria specified in
   * this field. At least one of operations or notOperations must be specified.
   *
   * @param AuthzPolicyAuthzRuleToRequestOperation[] $notOperations
   */
  public function setNotOperations($notOperations)
  {
    $this->notOperations = $notOperations;
  }
  /**
   * @return AuthzPolicyAuthzRuleToRequestOperation[]
   */
  public function getNotOperations()
  {
    return $this->notOperations;
  }
  /**
   * Optional. Describes properties of one or more targets of a request. At
   * least one of operations or notOperations must be specified. Limited to 1
   * operation. A match occurs when ANY operation (in operations or
   * notOperations) matches. Within an operation, the match follows AND
   * semantics across fields and OR semantics within a field, i.e. a match
   * occurs when ANY path matches AND ANY header matches and ANY method matches.
   *
   * @param AuthzPolicyAuthzRuleToRequestOperation[] $operations
   */
  public function setOperations($operations)
  {
    $this->operations = $operations;
  }
  /**
   * @return AuthzPolicyAuthzRuleToRequestOperation[]
   */
  public function getOperations()
  {
    return $this->operations;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AuthzPolicyAuthzRuleTo::class, 'Google_Service_NetworkSecurity_AuthzPolicyAuthzRuleTo');
