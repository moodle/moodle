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

class RoutePolicyPolicyTerm extends \Google\Collection
{
  protected $collection_key = 'actions';
  protected $actionsType = Expr::class;
  protected $actionsDataType = 'array';
  protected $matchType = Expr::class;
  protected $matchDataType = '';
  /**
   * The evaluation priority for this term, which must be between 0 (inclusive)
   * and 2^31 (exclusive), and unique within the list.
   *
   * @var int
   */
  public $priority;

  /**
   * CEL expressions to evaluate to modify a route when this term matches.
   *
   * @param Expr[] $actions
   */
  public function setActions($actions)
  {
    $this->actions = $actions;
  }
  /**
   * @return Expr[]
   */
  public function getActions()
  {
    return $this->actions;
  }
  /**
   * CEL expression evaluated against a route to determine if this term applies.
   * When not set, the term applies to all routes.
   *
   * @param Expr $match
   */
  public function setMatch(Expr $match)
  {
    $this->match = $match;
  }
  /**
   * @return Expr
   */
  public function getMatch()
  {
    return $this->match;
  }
  /**
   * The evaluation priority for this term, which must be between 0 (inclusive)
   * and 2^31 (exclusive), and unique within the list.
   *
   * @param int $priority
   */
  public function setPriority($priority)
  {
    $this->priority = $priority;
  }
  /**
   * @return int
   */
  public function getPriority()
  {
    return $this->priority;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RoutePolicyPolicyTerm::class, 'Google_Service_Compute_RoutePolicyPolicyTerm');
